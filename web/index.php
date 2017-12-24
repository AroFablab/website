<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
  $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
      return sprintf('%s/%s', trim($app['request']->getBasePath()), ltrim($asset, '/'));
  }));
  return $twig;
}));

$app->before(function ($request) use ($app) {
    $app['twig']->addGlobal('active', $request->get("_route"));
});

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Our web handlers

$app->get('/', function() use ($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});

$app->get('/membership', function() use ($app) {
  $app['monolog']->addDebug('logging members page.');
  return $app['twig']->render('members/members.twig');
})->bind('membership');
// https://swiftmailer.symfony.com/docs/introduction.html

$app->run();
