<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->match('/', function (Request $request) use ($app, $config) {

    $params = array_merge(
        $app['request']->request->all(),
        $app['request']->query->all()
    );

    $q = $params['q'];
    $access_token = $params['access_token'];

    // validate user token
    $valid_token = validate_token($access_token);

    $output = array();
    if ($valid_token == true){
        $output['q'] = $q;
        $output['layout'] = ($params['layout'] ? $params['layout'] : '1');
        $output['access_token'] = $access_token;

        return $app['twig']->render('home.html', $output);
    }else{
        $output['redirect_url'] = $config['login_redirect_url'];
        return $app['twig']->render('invalid_access.html', $output);
    }

});

?>
