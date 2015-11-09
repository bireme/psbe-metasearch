<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->match('search_bvs/', function (Request $request) use ($app, $config) {

    $params = array_merge(
        $app['request']->request->all(),
        $app['request']->query->all()
    );

    $request_uri = preg_replace('/&page=(\d+)/','', $_SERVER['REQUEST_URI']);

    $db_config = $config['BVS'];
    $service_url = $db_config['api_url'];
    $query = urlencode($params['q']);

    if ($db_config['items_per_page'] != ''){
        $count = $db_config['items_per_page'];
    }else{
        $count = $config['items_per_page'];
    }

    $request_url = $service_url . '&q=' . $query . '&count=' . $config['items_per_page'];
    $result_url = $db_config['result_url'] . '&q=' . $query . '&count=' . $count;

    if ($params['page'] > 1){
        $from = ($params['page'] * $count) + 1;
        $request_url.= '&from=' . $from;
    }

    $result_json = @file_get_contents($request_url);

    if ($result_json){
        $result = json_decode($result_json, true);
        $total_hits = $result['diaServerResponse'][0]['response']['numFound'];
    }

    if ($total_hits > 0){
        $pagination['page'] = ($params['page'] > 0 ? $params['page'] : 1);
        $pagination['num_pages'] = (int)($total_hits/$count);
        $pagination['has_next'] = ($pagination['page'] >= $pagination['num_pages']? false : true);
        $pagination['has_previous'] = ($pagination['page'] == 1? false : true);
    }

    $output['total_hits'] = $total_hits;
    $output['item_list'] = $result['diaServerResponse'][0]['response']['docs'];
    $output['result_url'] = $result_url;
    $output['pagination'] = $pagination;
    $output['request_uri'] = $request_uri;
    $output['box'] = $params['box'];


    return $app['twig']->render('bvs.html', $output);

});

?>
