<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->match('search_epistemonikos/', function (Request $request) use ($app, $config) {

    $params = array_merge(
            $app['request']->request->all(),
            $app['request']->query->all()
    );

    $db_config = $config['EPISTEMONIKOS'];

    $query = $params['q'];

    $request_url =  $db_config['search_url'] . '?q=' . urlencode($query);

    $html = file_get_contents($request_url); //get the html returned from the following url

    $service_doc = new DOMDocument();

    libxml_use_internal_errors(TRUE); //disable libxml errors

    if(!empty($html)){ //if any html is actually returned

      $service_doc->loadHTML($html);
      libxml_clear_errors(); //remove errors for yucky html

      $service_xpath = new DOMXPath($service_doc);

      //get total result
      $result_row = $service_xpath->query("//div[@id = 'selected_documents_link']//strong");
      $total_html = (string) $result_row->item(0)->nodeValue;
      $total_hits = substr($total_html, strpos($total_html," of ")+4);
    }

    $output['total_hits'] = intval($total_hits);
    $output['result_url'] = $request_url;

    return $app['twig']->render('epistemonikos.html', $output);

});

?>
