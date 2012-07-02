<?php

namespace Magma\PlanningPoker\Story\Board;
use Magma\PlanningPoker\Story\Board as StoryBoard;

/**
 * Stores a collection of Story Boards
 *
 * @author michael
 */
class Collection extends \ArrayObject {

    /**
     * Instantiate a new Story Board collection
     * @param array $storyBoards    Initial Story Boards
     */
    public function __construct(array $storyBoards = array()) {
        
        parent::__construct($storyBoards);
    }
    
    /**
     * Add a new Story Board to the collection
     * 
     * @param StoryBoard $storyBoard
     * @return \Magma\PlanningPoker\Story\Board\Collection 
     */
    public function addStoryBoard(StoryBoard $storyBoard) {
        
        $this->offsetSet($storyBoard->getId(), $storyBoard);
        return $this;
    }
    
    /**
     * Retrieve a Story Board by ID
     * 
     * @param mixed $id
     * @return \Magma\PlanningPoker\Story\Board
     */
    public function getStoryBoard($id) {
        
        $retval = null;
        
        if ($this->offsetExists($id)) {
            $retval = $this[$id];
        }
        
        return $retval;
    }
    
    /**
     * Set Story Board collection en masse
     * 
     * @param array $storyBoards
     * @return \Magma\PlanningPoker\Story\Board\Collection 
     */
    public function setStoryBoards(array $storyBoards) {
        
        $this->exchangeArray(array()); // reset
        
        foreach ($storyBoards as $board) {
            $this->addStoryBoard($board);
        }
        
        return $this;
    }
    
    public function offsetSet($offset, $value) {
        
        if (!($value instanceof StoryBoard)) {
            throw new \InvalidArgumentException('$value must be an instance of \Magma\PlanningPoker\Story\Board');
        }
        
        parent::offsetSet($offset, $value);
    }
    
    /**
     * Retrieve all Story Boards
     * 
     * @return \Magma\PlanningPoker\Story\Board\Collection 
     */
    public function getAll() {
        
        return $this;
    }
    
    /**
     * Retrieve only active boards
     * @return \Magma\PlanningPoker\Story\Board\self 
     */
    public function getActive() {
        
        $retval = array();
        
        $boards = $this->getAll();
        
        foreach ($boards as $board) {
            if ($board->isActive()) {
                $retval[] = $board;
            }
        }
        
        return new self($boards);
    }
    
    public function toArray() {
        
        $retval = array();
        
        foreach ($this->getAll() as $board) {
            $retval[] = $board->toArray();
        }
        
        return $retval;
    }
}