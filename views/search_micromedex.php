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

    $request_url = $service_url . '&SearchTerm=' . urlencode($query);

    if ($params['debug']){
        print $request_url;
    }

    $html = file_get_contents($request_url); //get the html returned from the following url

    $service_doc = new DOMDocument();

    libxml_use_internal_errors(TRUE); //disable libxml errors

    if(!empty($html)){ //if any html is actually returned

      if (preg_match("/There were no results found for/", $html)) {
          $total_hits = 0;
      }else{
          $service_doc->loadHTML($html);
          libxml_clear_errors(); //remove errors for bad html

          $service_xpath = new DOMXPath($service_doc);

          //check for single result or list result page
          $result_row = $service_xpath->query("//h1");
          if ($result_row->length > 0){
              $total_html = (string) $result_row->item(0)->nodeValue;
              // case 1: multiples results found. ex. health
            if (preg_match('/results/', $total_html)){
                $total_hits = substr($total_html, 0, strpos($total_html,"results"));
                $total_hits = intval($total_hits);
            }else{
                // case 2: single result found. ex. exoparin
                $total_hits = 1;
            }
          }else{
              // check for brand name (ex. clexane) or drug class (ex. benzodiazepines)
              $result_row = $service_xpath->query("//div[@id='displayCountBarPrinted']");

              if ($result_row->length > 0){
                  $total_html = (string) $result_row->item(0)->nodeValue;
                  // extract numbers from html text
                  preg_match_all('!\d+!', $total_html, $matches);
                  $total_hits = $matches[0][0];
              }else{
                  // other types of result page
                  $total_hits = 1;
              }
          }
      }
    }
    $output['total_hits'] = $total_hits;
    $output['result_url'] = $request_url;

    return $app['twig']->render('micromedex.html', $output);

});

?>
