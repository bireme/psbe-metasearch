<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->match('/', function (Request $request) use ($app, $config) {

    $params = array_merge(
        $app['request']->request->all(),
        $app['request']->query->all()
    );

    $q = $params['q'];

    $output = array();
    $output['q'] = $q;

    return $app['twig']->render('home.html', $output);

});

?>
