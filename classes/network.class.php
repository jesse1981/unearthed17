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
  public function enableCOR() {
    header('Access-Control-Allow-Origin: '.ORIGIN);
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, PATCH, DELETE');
    header('Access-Control-Allow-Headers: X-Requested-With,content-type');
    header('Access-Control-Allow-Credentials: true');
  }
}
?>
