<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->match('search_ebsco/', function (Request $request) use ($app, $config) {

    $params = array_merge(
            $app['request']->request->all(),
            $app['request']->query->all()
    );

    $request_uri = preg_replace('/&page=(\d+)/','', $_SERVER['REQUEST_URI']);

    $db_config = $config['EBSCO'];

    if ($db_config['items_per_page'] != ''){
        $count = $db_config['items_per_page'];
    }else{
        $count = $config['items_per_page'];
    }

    $service_url = $db_config['api_url'] . 'Search?prof=' . $db_config['profile'] . '&pwd=' . $db_config['password'] . '&authType=' . $db_config['auth_type'];
    $service_url.= '&format=brief&num_rec=' . $count;

    if ($params['page'] > 1){
        $from = ($params['page'] * $count) + 1;
        $service_url.= '&startrec=' . $from;
    }

    $query = urlencode($params['q']);
    $db = $params['db'];

    $request_url = $service_url . '&query=' . $query . '&db=' . $db;

    $service_xml = @simplexml_load_file($request_url);

    if ($service_xml){
        $item_list = $service_xml->SearchResults->records->rec;
        $total_hits = (int) $service_xml->Hits;
    }

    if ($total_hits > 0){
        $pagination['page'] = ($params['page'] > 0 ? $params['page'] : 1);
        $pagination['num_pages'] = (int)($total_hits/$count);
        $pagination['has_next'] = ($pagination['page'] >= $pagination['num_pages']? false : true);
        $pagination['has_previous'] = ($pagination['page'] == 1? false : true);
    }

    $output['total_hits'] = $total_hits;
    $output['item_list'] = $item_list;
    $output['pagination'] = $pagination;
    $output['request_uri'] = $request_uri;
    $output['result_url'] = $db_config['result_url'] . '&bquery=' . $query . '&db=' . $db;
    $output['box'] = $params['box'];

    return $app['twig']->render('ebsco.html', $output);

});

?>
