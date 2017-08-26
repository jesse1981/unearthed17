<?php
class template {
  private $template = "_master.php";
  private $view = "";

  private function loadPartialView() {

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
