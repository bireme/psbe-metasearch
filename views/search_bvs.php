<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->match('search_bvs/', function (Request $request) use ($app, $config) {

    $params = array_merge(
        $app['request']->request->all(),
        $app['request']->query->all()
    );

    $db_config = $config['BVS'];
    $service_url = $db_config['api_url'];
    $query = $params['q'];

    $request_url = $service_url . '&q=' . $query . '&count=' . $config['items_per_page'];

    $result_url = $db_config['result_url'] . '&q=' . $query . '&count=' . $config['items_per_page'];

    $result_json = @file_get_contents($request_url);

    if ($result_json){
        $result = json_decode($result_json, true);
    }

    $output['item_list'] = $result['diaServerResponse'][0]['response']['docs'];
    $output['result_url'] = $result_url;

    return $app['twig']->render('bvs.html', $output);

});

?>
