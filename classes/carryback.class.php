<?php
class carryback {
  public function index() {
    $db = new database();
    $sql = "SELECT * FROM test";
    $res = $db->query($sql);
    var_dump($res);
  }
}
?>
