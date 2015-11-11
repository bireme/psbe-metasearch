<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->match('search_bvs/', function (Request $request) use ($app, $config) {
    global $texts;
    $params = array_merge(
        $app['request']->request->all(),
        $app['request']->query->all()
    );

    $request_uri = preg_replace('/&page=(\d+)/','', $_SERVER['REQUEST_URI']);

    $db_config = $config['BVS'];
    $service_url = $db_config['api_url'];
    $query = urlencode($params['q']);
    $lang = (isset($param['lang']) ? $param['lang'] : $config['default_lang']);
    $filter_args = $params['filter'];

    $filter_param = '';
    $selected_filters = array();
    if (isset($filter_args)){
        foreach ($filter_args as $filter){
            $filter_parts = explode(':', $filter);
            $filter_name = $filter_parts[0];
            $filter_value = $filter_parts[1];
            $filter_param .= '&filter[' . $filter_name . '][]=' . str_replace(' ', '%20', $filter_value);
            $selected_filters[$filter_name] = $filter_value;
        }
    }

    if ($db_config['items_per_page'] != ''){
        $count = $db_config['items_per_page'];
    }else{
        $count = $config['items_per_page'];
    }

    $request_params = '&q=' . $query . '&count=' . $count . $filter_param;

    $request_url = $service_url . $request_params;
    $result_url = $db_config['result_url'] . $request_params;

    //print $request_url;

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

    $filter_list = explode(', ', $db_config['filter_list']);
    // remove empty elements
    $filter_list = array_filter($filter_list);

    if (count($filter_list) > 0){
        $translation_file_url = $db_config['result_url'] . "/locale/" . $lang . "/texts.ini";

        $translation_file_content = file_get_contents($translation_file_url);
        $texts = parse_ini_string($translation_file_content, true);
    }

    $output['total_hits'] = $total_hits;
    $output['item_list'] = $result['diaServerResponse'][0]['response']['docs'];
    $output['clusters'] = $result['diaServerResponse'][0]['facet_counts']['facet_fields'];
    $output['result_url'] = $result_url;
    $output['pagination'] = $pagination;
    $output['request_uri'] = $request_uri;
    $output['filter_list'] = $filter_list;
    $output['selected_filters'] = $selected_filters;
    $output['texts'] = $texts;
    $output['lang'] = $lang;
    $output['box'] = $params['box'];

    return $app['twig']->render('bvs.html', $output);

});

?>
