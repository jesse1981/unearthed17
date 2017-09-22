<?php

$origin   = "";
$referer  = "";
if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $origin = $_SERVER['HTTP_ORIGIN'];
}
if (array_key_exists('HTTP_REFERER', $_SERVER)) {
    $origin = ($origin) ? $origin:$_SERVER['HTTP_REFERER'];
    $referer = $origin;
}
else {
    $origin = $_SERVER['REMOTE_ADDR'];
}
define('ORIGIN',$origin,true);
define('REFERER',$_SERVER["HTTP_REFERER"],true);
define('IP',$_SERVER["SERVER_ADDR"],true);

class network {
  private function stringifyHeaders() {
    $headers = getallheaders();
    $result = array();
    foreach ($headers as $k=>$v)
      $result[] = $k;

    return implode(",",$result);
  }

  public function enableCOR() {
    header('Access-Control-Allow-Origin: '.ORIGIN);
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, PATCH, DELETE');
    header('Access-Control-Allow-Headers: '.$this->stringifyHeaders());
    header('Access-Control-Allow-Credentials: true');
  }
  public function request($url,$username="",$password="",$postbody="",$postdata=array()) {
    $field_string = "";
    if ($postdata) {
      foreach ($postdata as $k=>$v) {
        if ($field_string) $field_string .= "&";
        $v2 = urlencode($v);
        $field_string .= "$k=$v2";
      }
    }

    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
    if ($username) {
      curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    if ($postbody) {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postbody);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($postbody))
      );
    }
    else if ($postdata) {
      curl_setopt($ch,CURLOPT_POST, count($postdata));
      curl_setopt($ch,CURLOPT_POSTFIELDS, $field_string);
    }

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    //execute post
    $result = curl_exec($ch);

  	if ($result === FALSE) {
  		printf("cUrl error (#%d): %s<br>\n", curl_errno($handle),
  		htmlspecialchars(curl_error($handle)));
  	}

    $info = curl_getinfo($ch);

    //close connection
    curl_close($ch);

    return $result;
  }
}
?>
