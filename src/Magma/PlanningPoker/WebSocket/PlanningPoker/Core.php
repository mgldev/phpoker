<?php

namespace Magma\PlanningPoker\WebSocket\PlanningPoker;

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
     * @var StoryBoardCollection
     */
    protected $_boards = null;

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
    protected function getConnectionByMember(\Magma\PlanningPoker\Member $member) {

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
     * @param StoryBoardCollection $storyBoards
     * @return \Magma\PlanningPoker\WebSocket\Poker
     */
    public function setStoryBoards(\Magma\PlanningPoker\Story\Board\Collection $storyBoards) {

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
}