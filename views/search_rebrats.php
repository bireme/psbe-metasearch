<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->match('search_rebrats/', function (Request $request) use ($app, $config) {

    $params = array_merge(
        $app['request']->request->all(),
        $app['request']->query->all()
    );
    $query = $params['q'];

    $db_config = $config['REBRATS'];
    $service_url = $db_config['search_url'];
    $service_url .= '?' . $db_config['search_params'];

    $request_url = $service_url . '&no_palavra_chave=' . urlencode($query);

    $url = $db_config['search_url'];
    $params = $db_config['search_params'] . '&no_palavra_chave=' . urlencode($query);

    parse_str($params, $result_params);

    $output['result_url'] = $db_config['search_url'];
    $output['result_params'] = $result_params;

    return $app['twig']->render('rebrats.html', $output);

});

?>
