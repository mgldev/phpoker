<?php

namespace Magma\PlanningPoker\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Magma\PlanningPoker\Member;
use Magma\PlanningPoker\Story\Board as StoryBoard;
use Magma\PlanningPoker\Story\Board\Collection as StoryBoardCollection;

class Poker implements MessageComponentInterface {

    protected $_members = null;
    
    /**
     *
     * @var type 
     */
    protected $_storyBoards = null;

    public function __construct(array $storyBoards = array()) {

        $this->_members = new \SplObjectStorage();
        $this->setStoryBoards($storyBoards);
    }
    
    public function onOpen(ConnectionInterface $conn) {

        $this->_members->attach($conn);
    }

    public function onMessage(ConnectionInterface $conn, $msg) {

        $request = json_decode($msg);

        switch ($request->action) {

            case 'join':
                $this->joinMember($conn, $request->params->username);
                break;
        }
    }

    public function action(ConnectionInterface $conn, $action, $params) {

        $response = array('action' => $action, 'params' => $params);
        $conn->send(json_encode($response));
        return $this;
    }

    public function onClose(ConnectionInterface $conn) {

        $member = $this->_members[$conn];

        if ($member) {
            echo 'LEFT: ' . $member->getDisplayName();
        } else {
            echo 'LEFT: <unknown>';
        }

        $this->_members->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {

        echo 'ERROR: ' . $e->getMessage();
    }

    public function joinMember(ConnectionInterface $conn, $username) {

        /**
         * @ToDo: Use username to retrieve member from database, for now
         * simply creating a new member on the fly
         */
        $member = new Member;
        $member->setUsername($username)
                ->setDisplayName(ucwords(str_replace('_', ' ', $username)));

        $this->_members->attach($conn, $member);

        echo 'JOINED: ' . $member->getDisplayName() . "\n";

        // send member the current list of connected members
        $this->sendMemberList($conn);
        $this->notifyOfNewMember($conn);
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

}