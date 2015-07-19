<?php

$app = require_once('bootstrap.php');

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->get('/', function() use ($app) {
	return $app['twig']->render('poker.twig');
});

$app->run();