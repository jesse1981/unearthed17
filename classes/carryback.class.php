<?php
class carryback {
  public function index() {
    $db = new database();
    $sql = "SELECT * FROM test WHERE field = :valuekey";
    $res = $db->query($sql,array("valuekey"=>"value"));
    var_dump($res);
  }
}
?>
