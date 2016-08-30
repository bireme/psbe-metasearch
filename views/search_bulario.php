<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->match('search_bulario/', function (Request $request) use ($app, $config) {

    $params = array_merge(
            $app['request']->request->all(),
            $app['request']->query->all()
    );

    $db_config = $config['BULARIO'];

    $query = $params['q'];

    $result_url =  $db_config['result_url'];
    $params = $db_config['resul_params'] . '&txtMedicamento=' . $query;

    parse_str($params, $result_params);

    $output['result_url'] = $result_url;
    $output['result_params'] = $result_params;

    return $app['twig']->render('bulario.html', $output);

});

?>
