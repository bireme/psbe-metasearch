<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->match('search_proqualis/', function (Request $request) use ($app, $config) {

    $params = array_merge(
            $app['request']->request->all(),
            $app['request']->query->all()
    );

    $db_config = $config['PROQUALIS'];

    $query = $params['q'];

    $request_url =  $db_config['search_url'] . urlencode($query);

    $html = file_get_contents($request_url); //get the html returned from the following url

    $service_doc = new DOMDocument();

    libxml_use_internal_errors(TRUE); //disable libxml errors

    if(!empty($html)){ //if any html is actually returned

      $service_doc->loadHTML($html);
      libxml_clear_errors(); //remove errors for yucky html

      $service_xpath = new DOMXPath($service_doc);

      //get total result
      $result_row = $service_xpath->query("//div[@class = 'resulatdo_busca']//p");
      $total_html = (string) $result_row->item(0)->nodeValue;

      $total_hits = substr($total_html, strpos($total_html,"total de ")+9);
      $total_hits = substr($total_hits, 0, strpos($total_hits,"resultados."));
    }

    $output['total_hits'] = intval($total_hits);
    $output['result_url'] = $request_url;

    return $app['twig']->render('proqualis.html', $output);

});

?>
