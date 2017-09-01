<?php
class index {
  public function index() {
    $template = new template;
    $template->setView('three')->output();
  }
}
?>
