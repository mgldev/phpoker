<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Magma\PlanningPoker\WebSocket\PlanningPoker;
use Magma\PlanningPoker\Story;
use Magma\PlanningPoker\Story\Board as StoryBoard;
use Magma\PlanningPoker\Story\Board\Collection as StoryBoardCollection;

$board1 = new StoryBoard('Bluebook API');
$board1->addStory(new Story('Setup application foundation (harness, framework)', 'Get the project installed in to the harness and get initial settings prepared'));
$board1->addStory(new Story('Access Control Layer', 'Implement the access control layer and configure roles and permissions'));
$board1->addStory(new Story('Implement OAuth2', 'Implement and configure OAuth2 for authentication'));
$board1->addStory(new Story('User Management - Create user', 'Get the basics of stories / story boards running'));
$board1->addStory(new Story('User Management - List users', 'Get the basics of stories / story boards running'));
$board1->setActive(true);

$boards = new StoryBoardCollection;
$boards->addStoryBoard($board1);

$planningPoker = new PlanningPoker($boards);
$server = IoServer::factory(new HttpServer(new WsServer($planningPoker)), 8000);
$server->run();