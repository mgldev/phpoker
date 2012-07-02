<?php

require_once __DIR__ .'/../vendor/autoload.php';

$loader = new \Composer\Autoload\ClassLoader();
$loader->add('Magma', __DIR__ . '/../src');
$loader->register();

use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Magma\PlanningPoker\WebSocket\Poker;

$server = IoServer::factory(
        new WsServer(
            new Poker()
        )
      , 8000
    );
$server->run();