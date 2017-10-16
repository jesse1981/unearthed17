<?php
class finder {
  public function index() {
    $paths = array(
      "Harvey Norman"  => array(
        "url"             => "https://www.harveynorman.com.au/catalogsearch/result/?q=",
        "searchDelimiter" => "+",
        "products"        => '//*[@id="category-grid"]/div/div/div',
        "meta"            => array(
          "img"       => "./div/div[1]/a/img",
          "price"     => "./div/div[2]/span/span",
          "desc"      => "./div/div[3]/a"
        )
      ),
      "JB Hi-Fi"       => array(
        "url"             => "https://www.jbhifi.com.au/?q=",
        "searchDelimiter" => "%20",
        "products"        => '//*[@id="search-overlay"]/div/div/div/ul[@class="grid"]/li/div[@class="product-tile"]',
        "meta"            => array(
          "img"       => "./a[1]/div[2]/img",
          "price"     => "./a[1]/div[4]/div/p/span",
          "desc"      => "./a[1]/div[3]/div/h4"
        )
      )
      ,
      "Betta Electrical"=> array(
        "url"             => "http://electrical.betta.com.au/search?w=",
        "searchDelimiter" => "%20",
        "products"        => '//*[@id="root-wrapper"]/div/div/div/div/div/div/ul/li/div',
        "meta"            => array(
          "img"       => "./a[1]/div[2]/img",
          "price"     => "./a[1]/div[4]/div/p/span",
          "desc"      => "./a[1]/div[3]/div/h4"
        )
      )
    );

    $net = new network();

    $searchTerm = array("speakers","yamaha");

    $results = array();

    // find product divs
    foreach ($paths as $site=>$props) {
      $debug = ($site=="JB Hi-Fi" || $site=="Betta Electrical") ? true:false;
      echo "Now processing: $site<br/>" ;
      $url = $props["url"] . implode($props["searchDelimiter"],$searchTerm);
      $res = $net->request($url);
      $xml = new xml(null,$res);

      $products = $xml->getXpathArray($props["products"]);

      if ($debug) {
        echo "The RES: <br/>";
        var_dump($res);
        echo "Product Count: ".count($products)."<br/>";
        var_dump($products);

        $index = strpos($res,"products-grid");
        echo "B/E Index: $index<br/>";
      }

      foreach ($products as $p) {
        $img    = $xml->getXpathArray($props["meta"]["img"],$p);
        $price  = $xml->getXpathArray($props["meta"]["price"],$p);
        $desc   = $xml->getXpathArray($props["meta"]["desc"],$p);

        $t = array(
          "desc"  =>  (isset($desc[0])) ? (string)$desc[0]:"",
          "price" =>  (isset($price[0])) ? (string)$price[0]:"",
          "img"   =>  $xml->getAttribute($img[0],"data-src")
        );
        $results[] = $t;
      }
    }
    echo "Results: ";
    var_dump($results);
  }
}
?>
