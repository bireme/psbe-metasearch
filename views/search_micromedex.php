<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->match('search_micromedex/', function (Request $request) use ($app, $config) {

    $params = array_merge(
            $app['request']->request->all(),
            $app['request']->query->all()
    );

    $db_config = $config['MICROMEDEX'];
    $service_url = $db_config['deeplink_url'] . '&institution=' . $db_config['institution'];

    $query = $params['q'];

    $request_url = $service_url . '&SearchTerm=' . $query;

    if ($params['debug']){
        print $request_url;
    }

    $html = file_get_contents($request_url); //get the html returned from the following url

    $service_doc = new DOMDocument();

    libxml_use_internal_errors(TRUE); //disable libxml errors

    if(!empty($html)){ //if any html is actually returned

      $service_doc->loadHTML($html);
      libxml_clear_errors(); //remove errors for yucky html

      $service_xpath = new DOMXPath($service_doc);

      //get total result
      $result_row = $service_xpath->query("//h1");
      $total_html = (string) $result_row->item(0)->nodeValue;
      if ($total_html){
          // case 1: multiples results found
        if (preg_match('/results/', $total_html)){
            $total_hits = substr($total_html, 0, strpos($total_html,"results"));
            $total_hits = intval($total_hits);
        }else{
            // case 2: single result found
            $total_hits = 1;
        }
      }else{
         // case 3: no results found
        $total_hits = 0;
      }
    }
    $output['total_hits'] = $total_hits;
    $output['result_url'] = $request_url;

    return $app['twig']->render('micromedex.html', $output);

});

?>
