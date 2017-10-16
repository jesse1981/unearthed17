<?php
class xml {
  private $xml;

  public function __construct($filename=null,$html=null) {
    if ($filename) $this->xml = @simplexml_load_file($filename);
    else if ($html) $this->xml = @simplexml_import_dom(DOMDocument::loadHTML($html));
  }

  public function getAttribute($e,$att) {
    $result = $e->attributes()->$att->__toString();
    return $result;
  }
  public function getXpathArray($xpath,$xml=null) {
    $xml = ($xml) ? $xml:$this->xml;
    $result = (array)$xml->xpath($xpath);
    return $result;
  }

}
?>
