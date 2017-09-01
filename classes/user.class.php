<?php
class user {
	var $session;

	public function __construct() {
    $this->session = new session();

    // Only authenticate if the request comes from an address other than itself.
    if (($_SERVER["REMOTE_ADDR"]!=$_SERVER["SERVER_ADDR"]) && (strtolower(MODULE)!="users")) {
      if ($this->session->getKey('username')=='') {
          // Proceed with HTTP authentication method
        if (BEARER) {
          $token = BEARER;
          // check length
          if (strlen($token)==32) {
            $db  = new database();
            $sql = "SELECT * "
                  . "FROM customer_session "
                  . "WHERE token = ?";
            $res = $db->query($sql,array($token));
            $tab = $db->getTable($res);
            if (isset($tab[0])) {
              $user_id = (int)$tab[0]["user_id"];
              $tab = $database->get("users",$user_id);
              if (isset($tab[0])) {
                $stored = $this->storeSession($tab);
                return $stored;
              }
              else return false;
            }
          }
        }
        else {
          if (isset($_POST["username"])) {
            $user = $_POST["username"];
            $pass = $_POST["password"];
            $result = $this->login($user, $pass);
            if ($result) {
              if (!isset($_GET["redirect"])) header('Location: '.ROOTWEB);
              else if ($_GET["redirect"]=="index/index") header('Location: '.ROOTWEB);
              else header('Location: '.ROOTWEB.$_GET["redirect"]);
            }
            else header('Location: '.ROOTWEB.'login/invalid');
          }
          else if (MODULE != "login") {
            $dash->redirect('login?redirect='.MODULE.'/'.ACTION);
          }
        }
      }
    }
  }
	public function index() {
		$this->setView('users','_master.php');
	}
	public function add() {
		$this->setView('users/add','_master.php');
	}
	public function password() {
			$this->setView('users/password','_master.php');
	}

	private function storeSession($user) {
        $GLOBALS["USER_ID"] = (int)$user[0]["id"];

        $keys = array(
          "username"    => "username",
          "first_name"  => "firstname",
          "lastname"    => "lastname",
          "email"       => "email",
          "department"  => "department",
          "avatar"      => "avatar",
          "user_id"     => "id",
          "access"      => "access",
          "group_id"    => "group_id"
        );

        foreach($keys as $k=>$v)
          if (isset($user[0][$v]))
            $this->session->addKey($k,$user[0][$v]);

        $this->session->addKey("dudkey","dudkey");
        return $user;
    }
	private function getUserReturn($user_id,$token) {
		$db = new database();
		$ret = array();
		$ret["token"] = $token;
		$ret["fields"] = array();

		$sql = "SELECT u.*,g.description as group_name "
				. "FROM users u "
				. "INNER JOIN groups g ON g.id = u.group_id "
				. "WHERE u.id = ?";
		$res = $db->query($sql,array($user_id));
		$result = $db->getTable($res);

		$ret["group"] = $result[0]["group_name"];

		$fields = array("name_first","name_last","dob","address1","address2","suburb","postcode","country","phone","fax","email");
		foreach ($fields as $f) $ret["fields"][$f] = $result[0][$f];

		return $ret;
	}
	private function encrypt($id,$pass) {
		$salt = "Row row, row your boat, $id down the stream";
		$hash = md5($pass . $salt);
		return $hash;
	}

	public function remove($id) {
		$session = new session();
		$groupid = (int)$session->getKey("group_id");
		if ($groupid==1) {
			$db = new database();
			$sql = "DELETE FROM users WHERE id = ?";
			$res = $db->query($sql,array($id));
		}
	}
  public function login($user,$pass) {
		$db = new database();

		// Confirm if user row
		$sql = "SELECT * FROM users WHERE username = ?";
		$res = $db->query($sql,array($user));
		$tab = $db->getTable($res);
		if ($tab) {
			// confirm password from salted hash
			$id = $tab[0]["id"];
			$encrypt = $this->encrypt($id,$pass);
			if ($tab[0]["password"] == $encrypt) return $tab[0];
			else return false;
		}
		else return false;
	}
	public function userList() {
		$session    = new session();
		$db         = new database();

		$userid     = (int)$session->getKey("user_id");
		$groupid    = (int)$session->getKey("group_id");

		$module_filter  = $session->getKey("module_filter");
		$module_id      = $session->getKey("module_id");

		$sql = "SELECT company_id "
				. "FROM users "
				. "WHERE id = $userid";
		$res = $db->query($sql);
		$tab = $db->getTable($res);
		$company_id = (isset($tab[0])) ? (int)$tab[0]["company_id"]:0;

		$sql = "SELECT	u.id,
						u.identifier,
						u.username,
						u.name_first,
						u.name_last,
						c.company_name,
						u.email,
						u.date_start,
						u.active,
						g.description as designation
				FROM users u
				INNER JOIN groups g ON u.group_id = g.id
				INNER JOIN companys c ON u.company_id = c.id
				WHERE u.group_id >= $groupid ";
		if ($groupid===2)                                                                               $sql .= "AND u.company_id = $company_id ";
		else if (($groupid===1) && (isset($_POST["company_id"])) && (is_numeric($_POST["company_id"]))) $sql .= "AND u.company_id = ".$_POST["company_id"];
		else if (($groupid===1) && ($module_filter==="companies") && ((int)$module_id))                 $sql .= "AND u.company_id = $module_id ";

		$res = $db->query($sql);
		$tab = $db->getTable($res);
		$jsn = json_encode($tab);
		echo $jsn;
	}
	public function access_token() {
		$user   = new user();
		$db	= new database();
		$dash   = new dashboard();
		$post   = $dash->getPost();

		// attempt login
		$result = $user->login($post["username"], $post["password"]);
		$id     = (isset($result[0]["id"])) ? (int)$result[0]["id"]:0;
		if ($id) {
			// login successful, write the token session
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
			$token = '';
			for ($i = 0; $i < 32; $i++) {
				$token .= $characters[rand(0, $charactersLength - 1)];
			}

                        $ret = $this->getUserReturn($id, $token);

			// write session login
			$sql = "INSERT INTO customer_session (user_id,token,login_date,login_time) VALUES (";
			$sql .= "$id,";
			$sql .= "'$token',";
			$sql .= "'".date('Y-m-d')."',";
			$sql .= "'".date('H:i:s')."')";
			$db->query($sql);

			// write the output
                        echo json_encode($ret);
		}
		else echo "{}";
	}
  public function register() {
		$db = new database();
		$dash = new dashboard();
		$post = $dash->getPost();
		$validated = true;

		// validate entries
		$keys = array("username","password","email","firstname","lastname");
		foreach ($keys as $k) {
			if ((!isset($post[$k])) || (isset($post[$k]) && (empty($post[$k])))) {
				$validated = false;
			}
		}
		if ($validated) {
			if ((isset($post["user-type"])) && ($post["user-type"]=="provider")) {
				$validated = ((isset($post["company_name"])) && (!empty($post["company_name"]))) ? true:false;
			}
		}
		if ($validated) {
			$sql = "INSERT INTO users (username,password,name_first,name_last,email) VALUES (";
			$sql .= "'".$post["username"]."',";
			$sql .= "'',";
			$sql .= "'".$post["firstname"]."',";
			$sql .= "'".$post["lastname"]."',";
			$sql .= "'".$post["email"]."')";
			$db->query($sql);
			$user_id = $db->getLastId();

			$salt = "This is CookieJar! $user_id";
			$password = trim($post["password"],"'");
			$hash = md5($salt . $password);

			$sql = "UPDATE users SET password = '$password' WHERE id = $user_id";
			$db->query($sql);

			if ((isset($post["user-type"])) && ($post["user-type"]=="provider")) {
				$company_keys = array(
					"company_name",
					"abn",
					"address1",
					"suburb",
					"state",
					"postcode",
					"phone",
					"business_email",
					"facilime",
					"contact_email",
					"contact_name",
					"contact_mobile"
				);
				$sql = "INSERT INTO companys (".implode(",", $company_keys).") VALUES (";
				$sql = str_replace("business_", "", $sql);
				$count = 0;
				foreach ($company_keys as $k) {
					if ($count) $sql .= ",";
					$sql .= ((isset($_POST[$k])) && (!empty($_POST[$k]))) ? "'".$_POST[$k]."'":"''";
					$count++;
				}
				$sql .= ")";
				$db->query($sql);
				$company_id = $db->getLastId();

				$sql = "UPDATE users SET company_id = $company_id, group_id = 2 WHERE id = $user_id";
			}
			else $sql = "UPDATE users SET group_id = 4 WHERE user_id = $user_id";
			$db->query($sql);

			echo 1;
			return true;
		}
		else {
			echo 0;
			return false;
		}
	}
  public function save() {
        $db     = new database();
        $dash   = new dashboard();
        $post   = $dash->getPost();
        $token  = BEARER;
        $keys   =  array(
            "address1",
            "address2",
            "suburb",
            "postcode",
            "country",
            "phone",
            "fascilime",
            "email"
        );

        // find user
        $sql = "SELECT * "
                . "FROM customer_session "
                . "WHERE token = '$token'";
        $res = $db->query($sql);
        $tab = $db->getTable($res);
        $user_id = (isset($tab[0])) ? (int)$tab[0]["user_id"]:0;

        if ($user_id) {
            $sql = "UPDATE users SET ";
            $cnt = 0;
            foreach ($keys as $k) {
                if ((isset($post[$k])) && ($post[$k]!="")) {
                    if ($cnt) $sql .= ", ";
                    $sql .= "$k = '".$post[$k]."'";
                    $cnt++;
                }
            }
            $sql .= "WHERE id = $user_id";
            $db->query($sql);

            $ret = $this->getUserReturn($user_id, $token);

            echo json_encode($ret);
        }
        else echo "{}";
    }
}
?>
