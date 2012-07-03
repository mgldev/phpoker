<?php

require_once __DIR__ .'/../vendor/autoload.php';

$loader = new \Composer\Autoload\ClassLoader();
$loader->add('Magma', __DIR__ . '/../src');
$loader->register();

use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Magma\PlanningPoker\WebSocket\PlanningPoker;
use Magma\PlanningPoker\Story;
use Magma\PlanningPoker\Story\Board as StoryBoard;
use Magma\PlanningPoker\Story\Board\Collection as StoryBoardCollection;

/**
 * simply building story boards / stories by hand until
 * mechanism built for retrieving / injecting
 */
$board1 = new StoryBoard('Planning Poker');
$board1->addStory(new Story('Perform initial testing of Web Sockets', 'Get a working example of web sockets up and running'));
$board1->addStory(new Story('Produce a basic working example of jQuery Mobile', 'Get jQuery mobile installed / themed'));
$board1->addStory(new Story('Establish Story / Story Board foundation classes', 'Get the basics of stories / story boards running'));
$board1->setActive(true);

$board2 = new StoryBoard('Scrumz Dashboard');
$board3 = new StoryBoard('Secret Project');
$board3->setActive(true);

$boards = new StoryBoardCollection;
$boards->addStoryBoard($board1)
        ->addStoryBoard($board2)
        ->addStoryBoard($board3);

$planningPoker = new PlanningPoker($boards);
$server = IoServer::factory(new WsServer($planningPoker), 8000);
$server->run();