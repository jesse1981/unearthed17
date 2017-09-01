<?php
class template {
  private $template = "_master.php";
  private $view = "";

  public function loadPartialView($filename) {
    if (!file_exists($filename)) return "<h1>Error: $filename does not exist.</h1>";
    ob_start();
    include $filename;
    return ob_get_clean();
  }

  public function setTemplate($name) {
    $this->template = $name;
    return $this;
  }
  public function setView($name) {
    $this->view = $name;
    return $this;
  }

  public function enableCOR() {
    header('Access-Control-Allow-Origin: https://acresta.eps.blinkm.co');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, PATCH, DELETE');
    header('Access-Control-Allow-Headers: X-Requested-With,content-type');
    header('Access-Control-Allow-Credentials: true');
  }
  public function output($cor=false) {
    if ($cor) $this->enableCOR();
    if ($this->view) $view = $this->loadPartialView("./views/".$this->view."/index.php");
    if (($this->template) && (file_exists("./templates/".$this->template))) include "./templates/".$this->template;
    else if ($view) echo $view;
  }
}
?>
