<?php
class template {
  private $template = "master.php";
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
  public function output($cor=false) {
    if ($this->view) $view = $this->loadPartialView("./views/".$this->view."/index.php");
    if (($this->template) && (file_exists("./templates/".$this->template))) include "./templates/".$this->template;
    else if ($view) echo $view;
  }
}
?>
