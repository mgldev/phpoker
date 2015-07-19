<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Magma\PlanningPoker\WebSocket\PlanningPoker;
use Magma\PlanningPoker\Story;
use Magma\PlanningPoker\Story\Board as StoryBoard;
use Magma\PlanningPoker\Story\Board\Collection as StoryBoardCollection;

$board1 = new StoryBoard('Planning Poker');
$board1->addStory(new Story('Perform initial testing of Web Sockets', 'Get a working example of web sockets up and running'));
$board1->addStory(new Story('Produce a basic working example of jQuery Mobile', 'Get jQuery mobile installed / themed'));
$board1->addStory(new Story('Establish Story / Story Board foundation classes', 'Get the basics of stories / story boards running'));
$board1->setActive(true);

$boards = new StoryBoardCollection;
$boards->addStoryBoard($board1);

$planningPoker = new PlanningPoker($boards);
$server = IoServer::factory(new HttpServer(new WsServer($planningPoker)), 8000);
$server->run();