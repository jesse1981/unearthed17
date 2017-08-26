<?php
// Session Cache Limiter
session_cache_limiter("nocache");
// Set UTC Date/Time Zone
date_default_timezone_set('Australia/Sydney');
// SPL Autoload Register
spl_autoload_register(function ($class) {
  include 'classes/' . $class . '.class.php';
});

$module = (isset($_GET["module"]) && !empty($_GET["module"]))  ? $_GET["module"]:"index";
$action = (isset($_GET["action"]))  ? $_GET["action"]:"index";
$id     = (isset($_GET["id"]))      ? $_GET["id"]:0;
$format = (isset($_GET["format"]))  ? $_GET["format"]:"";


$settings = parse_ini_file('.env',true);
foreach ($settings as $group=>$arr) {
    foreach ($arr as $k=>$v) {
        define($k,$v,true);
    }
}
?>
