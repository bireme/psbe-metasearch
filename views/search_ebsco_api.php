<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->match('search_ebsco/', function (Request $request) use ($app, $config) {

    $params = array_merge(
            $app['request']->request->all(),
            $app['request']->query->all()
    );

    $ebsco_config = $config['EBSCO'];
    $service_url = $ebsco_config['api_url'] . 'Search?prof=' . $ebsco_config['profile'] . '&pwd=' . $ebsco_config['password'] . '&authType=&ipprof=';
    $query = $params['q'];
    $db = $params['db'];

    $request_url = $service_url . '&query=' . $query . '&db=' . $db;

    $service_xml = @simplexml_load_file($request_url);

    if ($service_xml){
        $item_list = $service_xml->SearchResults->records->rec;
    }

    $output['item_list'] = $item_list;

    return $app['twig']->render('ebsco.html', $output);

});

?>
