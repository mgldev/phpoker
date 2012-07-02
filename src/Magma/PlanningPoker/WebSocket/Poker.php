<?php

namespace Magma\PlanningPoker\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Magma\PlanningPoker\Member;
use Magma\PlanningPoker\Story\Board as StoryBoard;
use Magma\PlanningPoker\Story\Board\Collection as StoryBoardCollection;

class Poker implements MessageComponentInterface {

    /**
     * Connection to Member hashmap
     * 
     * @var \SplObjectStorage
     */
    protected $_members = null;
    
    /**
     * Collection of Story Boards
     * 
     * @var StoryBoardCollection
     */
    protected $_storyBoards = null;
    
    /**
     * Instantiate a new Planning Poker instance
     * 
     * @param StoryBoardCollection $storyBoards 
     */
    public function __construct(StoryBoardCollection $storyBoards) {
        
        $this->_members = new \SplObjectStorage();
        $this->setStoryBoards($storyBoards);
    }
    
    /**
     * Build an action to be sent to the client
     * 
     * @param ConnectionInterface $conn
     * @param string $action    Name of action to be performed
     * @param type $params      Action parameters
     * @return \Magma\PlanningPoker\WebSocket\Poker 
     */
    public function action(ConnectionInterface $conn, $action, $params) {

        $response = array('action' => $action, 'params' => $params);
        $conn->send(json_encode($response));
        return $this;
    }

    public function attachMember(ConnectionInterface $conn, $username) {

        /**
         * @ToDo: Use username to retrieve member from database, for now
         * simply creating a new member on the fly
         */
        $member = new Member;
        $member->setUsername($username)
                ->setDisplayName(ucwords(str_replace('_', ' ', $username)));

        $this->_members->attach($conn, $member);
        $this->sendBoardList($conn);
    }
    
    public function joinBoard(ConnectionInterface $conn, $board_id) {

        echo 'joinBoard called';
        $board = $this->getStoryBoards()->getStoryBoard($board_id);
        $this->action($conn, 'show-board', array());
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
     * Send current member list to new member
     *
     * @param ConnectionInterface $conn
     */
    protected function sendMemberList(ConnectionInterface $conn) {

        $members = array();

        foreach ($this->_members as $connection) {
            $member = $this->_members[$connection];
            $members[] = $member->toArray();
        }

        $params = array('members' => $members);
        $this->action($conn, 'member-list', $params);
    }

    /**
     * Notify existing members of new arrival
     *
     * @param ConnectionInterface $conn		Connection of newly joined member
     * @return \Magma\PlanningPoker\WebSocket\Poker
     */
    protected function notifyOfNewMember(ConnectionInterface $conn) {

        $member = $this->_members[$conn];
        $params = array('member' => $member->toArray());

        foreach ($this->_members as $connection) {
            if ($connection === $conn)
                continue;
            $this->action($connection, 'new-member', $params);
        }

        return $this;
    }

    /**
     * Set story board collection
     * 
     * @param StoryBoardCollection $storyBoards
     * @return \Magma\PlanningPoker\WebSocket\Poker 
     */
    public function setStoryBoards(StoryBoardCollection $storyBoards) {
        
        $this->_storyBoards = $storyBoards;
        return $this;
    }
    
    /**
     * Retrieve story board collection
     * 
     * @return StoryBoardCollection
     */
    public function getStoryBoards() {
        
        return $this->_storyBoards;
    }
    
    /**
     * @inheritDoc
     */
    public function onOpen(ConnectionInterface $conn) {

        $this->_members->attach($conn);
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

            case 'join':
                $this->attachMember($conn, $request->params->username);
                break;
            
            case 'join-board':
                $this->joinBoard($conn, $request->params->board);
                break;
        }
    }
    
    /**
     * @inheritDoc
     */
    public function onClose(ConnectionInterface $conn) {

        $member = $this->_members[$conn];

        if ($member) {
            echo 'LEFT: ' . $member->getDisplayName();
        } else {
            echo 'LEFT: <unknown>';
        }

        $this->_members->detach($conn);
    }
    
    /**
     * @inheritDoc
     */
    public function onError(ConnectionInterface $conn, \Exception $e) {

        echo 'ERROR: ' . $e->getMessage();
    }
}