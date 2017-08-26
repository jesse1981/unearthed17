<?php
include_once 'config.php';
$controller = new $module;
$controller->$action();
?>
