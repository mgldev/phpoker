<?php

namespace Magma\PlanningPoker\WebSocket;

use Magma\PlanningPoker\Story\Board;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Magma\PlanningPoker\Member;
use Magma\PlanningPoker\Story\Board as StoryBoard;
use Magma\PlanningPoker\Story\Board\Collection as StoryBoardCollection;
use Magma\PlanningPoker\WebSocket\PlanningPoker\Core;
use Ratchet\WebSocket\Version\Hixie76\Connection;

/**
 * Web Sockets Planning Poker
 * 
 * @author  Michael Leach
 */
class PlanningPoker extends Core {

    /**
     * Instantiate a new Planning Poker instance
     *
     * @param StoryBoardCollection $storyBoards
     */
    public function __construct(\Magma\PlanningPoker\Story\Board\Collection $storyBoards) {

        parent::__construct();
        $this->setStoryBoards($storyBoards);
    }

    /**
     * Lookup Member object for specified username and associate with connection
     *
     * @param ConnectionInterface $conn
     * @param string $username
     */
    public function login(ConnectionInterface $conn, $username) {

        $member = new Member;
        $member->setUsername($username)
                ->setDisplayName(ucwords(str_replace('_', ' ', $username)));

        $this->getMembers()->attach($conn, $member);
        $this->sendBoardList($conn);
        
        return $this;
    }

    /**
     * Join a story board
     * 
     * @param ConnectionInterface $conn
     * @param string $boardId
     */
    public function joinBoard(ConnectionInterface $conn, $boardId) {

        // retrieve story board and add new member
        $board = $this->getStoryBoards()->getStoryBoard($boardId);
        $newMember = $this->getMemberByConnection($conn);
        $newMember->joinBoard($board);

        // notify board members of new member
        foreach ($board->getMembers() as $member) {
            $notifyConn = $this->getConnectionByMember($member);
            $params = array('members' => $board->getMemberArray());
            $this->action($notifyConn, 'refresh-board-members', $params);
        }

        $this->showCurrentStoryForBoard($conn, $board);
        return $this;
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function leave(ConnectionInterface $conn) {

        $member = $this->getMemberByConnection($conn);

        $board = $member->getCurrentStoryBoard();
        $board->removeMember($member);

        foreach ($board->getMembers() as $member) {
            // notify board members of member leaving
            $notifyConn = $this->getConnectionByMember($member);
            $params = array('members' => $board->getMemberArray());
            $this->action($notifyConn, 'refresh-board-members', $params);
        }

        $this->getMembers()->detach($conn);
    }

    protected function showCurrentStoryForBoard(ConnectionInterface $conn, Board $board) {

        // show the current story being estimated on this board
        $story = $board->getCurrentStory();
        $params = array(
            'story' => $story->toArray(),
            'board' => $board->toArray()
        );

        $this->action($conn, 'show-current-story', $params);
        return $this;
    }

    /**
     * Send list of available boards to client
     *
     * @param ConnectionInterface $conn
     */
    protected function sendBoardList(ConnectionInterface $conn) {

        $boards = $this->getStoryBoards()->toArray();
        $params = array('boards' => $boards);
        $this->action($conn, 'board-list', $params);
        
        return $this;
    }

    /**
     * Set the estimate for the current story
     *
     * @param ConnectionInterface $conn
     * @param float $estimate
     */
    public function estimateCurrentStory(ConnectionInterface $conn, $estimate) {

        $member = $this->getMemberByConnection($conn);
        $member->estimateCurrentStory($estimate);

        foreach ($member->getCurrentStoryBoard()->getMembers() as $member) {

            $connection = $this->getConnectionByMember($member);
            if ($connection === $conn)
                continue;
            $params = array('member' => $member->toArray());
            $this->action($connection, 'member-voted', $params);
        }
        
        return $this;
    }
    
    /**
     * Show each member of the board the estimates, triggered by a moderator
     * 
     * @param ConnectionInterface $conn 
     */
    public function revealCurrentStoryEstimates(ConnectionInterface $conn) {
        
        $member = $this->getMemberByConnection($conn);
        $board = $member->getCurrentStoryBoard();
        $story = $board->getCurrentStory();
        $estimates = $story->getEstimatesArray();
        $params = array('estimates' => $estimates);
        
        foreach ($board->getMembers() as $member) {
            $connection = $this->getConnectionByMember($member);
            $this->action($connection, 'show-estimates', $params);
        }
        
        return $this;
    }
    
    /**
     * Progress the board and each member to the next story
     * 
     * @param ConnectionInterface $connection 
     */
    public function gotoNextStory(ConnectionInterface $conn) {
        
        $member = $this->getMemberByConnection($conn);
        $board = $member->getCurrentStoryBoard();
        $story = $board->nextStory();
        $params = array('story' => $story->toArray());
        
        foreach ($board->getMembers() as $member) {
            $connection = $this->getConnectionByMember($member);
            $this->action($connection, 'show-current-story', $params);
        }
        
        return $this;
    }

    /**
     * Handle incoming request and delegate to relevant handler
     *
     * @param ConnectionInterface   $conn     Client connection
     * @param string                $msg      JSON string
     */
    public function onMessage(ConnectionInterface $conn, $msg) {

        $request = json_decode($msg);

        switch ($request->action) {

            case 'login':
                $this->login($conn, $request->params->username);
                break;

            case 'join-board':
                $this->joinBoard($conn, $request->params->board);
                break;

            case 'estimate':
                $this->estimateCurrentStory($conn, $request->params->estimate);
                break;
            
            case 'reveal':
                $this->revealCurrentStoryEstimates($conn);
                break;
            
            case 'next-story':
                $this->gotoNextStory($conn);
                break;
        }
    }

    public function onOpen(ConnectionInterface $conn) {

        $this->debug($conn, 'NEW_CONNECTION');
        $this->getMembers()->attach($conn);
    }

    /**
     * Disconnect member
     *
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn) {

        $this->leave($conn);
    }

    /**
     * Error occurred, log to error_log (for now)
     * 
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e) {

        error_log((string) $e);
    }
}