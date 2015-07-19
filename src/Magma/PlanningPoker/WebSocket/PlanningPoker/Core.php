<?php

namespace Magma\PlanningPoker\WebSocket\PlanningPoker;

use Magma\PlanningPoker\Member;
use Magma\PlanningPoker\Story\Board\Collection;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

/**
 * Provides core Planning Poker functionality
 * 
 * @author  Michael Leach 
 */
abstract class Core implements MessageComponentInterface {

    /**
     * Connection to Member hashmap
     * @var \SplObjectStorage
     */
    protected $_members = null;

    /**
     * Collection of Story Boards
     * @var Collection
     */
    protected $_storyBoards = null;

    public function __construct() {

        $this->setMembers(new \SplObjectStorage());
    }

    public function setMembers(\SplObjectStorage $members) {

        $this->_members = $members;
        return $this;
    }

    public function getMembers() {

        return $this->_members;
    }

    /**
     * Retrieve Member instance for a specified ConnectionInterface instance
     *
     * @param ConnectionInterface $conn
     * @return Member
     */
    protected function getMemberByConnection(ConnectionInterface $conn) {

        $retval = null;

        if ($this->_members->contains($conn)) {
            $retval = $this->_members[$conn];
        }

        return $retval;
    }

    /**
     * Retrieve ConnectionInterface instance for a specified Member instance
     *
     * @param Member $member
     * @return ConnectionInterface
     */
    protected function getConnectionByMember(Member $member) {

        $retval = null;

        foreach ($this->_members as $connection) {
            if ($this->_members->getInfo() === $member) {
                $retval = $connection;
                break;
            }
        }

        return $retval;
    }

    /**
     * Set story board collection
     *
     * @param Collection $storyBoards
     * @return \Magma\PlanningPoker\WebSocket\Poker
     */
    public function setStoryBoards(Collection $storyBoards) {

        $this->_storyBoards = $storyBoards;
        return $this;
    }

    /**
     * Retrieve story board collection
     *
     * @return Collection
     */
    public function getStoryBoards() {

        return $this->_storyBoards;
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
        $response = json_encode($response);
        $conn->send($response);
        return $this;
    }

    /**
     * @param ConnectionInterface $conn
     * @param $action
     * @param string $message
     */
    public function debug(ConnectionInterface $conn, $action, $message = '') {

        $member = $this->getMemberByConnection($conn);

        $name = '';
        if ($member) {
            $name = '[' . $member->getDisplayName() . ']';
        }

        print('[' . date('d/m/Y H:i:s', time()) . '] ' . $name . ' [' . $action . '] ' . $message . "\n");
    }
}