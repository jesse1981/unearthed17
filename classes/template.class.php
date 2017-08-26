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
  }
  public function setView($name) {
    $this->view = $name;
  }

  public function output() {
    if ($this->view) $content = $this->loadPartialView("./views/".$this->view."/index.php");
    if (($this->template) && (file_exists("./templates/".$this->template))) {
      include "./templates/".$this->template;
    }
  }
}
?>
