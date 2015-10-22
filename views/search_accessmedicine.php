<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->match('search_accessmedicine/', function (Request $request) use ($app, $config) {

    $params = array_merge(
        $app['request']->request->all(),
        $app['request']->query->all()
    );

    $db_config = $config['ACCESSMEDICINE'];
    $service_url = $db_config['search_url'];

    $query = $params['q'];

    $request_url = $service_url . '?q=' . $query;

    $html = file_get_contents($request_url); //get the html returned from the following url

    $service_doc = new DOMDocument();

    libxml_use_internal_errors(TRUE); //disable libxml errors

    if(!empty($html)){ //if any html is actually returned

      $service_doc->loadHTML($html);
      libxml_clear_errors(); //remove errors for yucky html

      $service_xpath = new DOMXPath($service_doc);

      //get total result
      $result_links = $service_xpath->query('//div[@role="navigation"]/ul[@data-level="1"]/li');

      $result_set = array();
      foreach($result_links as $item){
          $node = $item->nodeValue;
          $pattern_total_hits = "/\((.*)\)/";

          $item_label = trim(substr($node, 0, strpos($node,"(")));

          preg_match($pattern_total_hits, $node, $matches);
          $item_total = $matches[1];

          $result_set[$item_label] = $item_total;
      }

    }

    $output['result_set'] = $result_set;
    return $app['twig']->render('accessmedicine.html', $output);

});

?>