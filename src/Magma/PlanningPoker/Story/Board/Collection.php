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
    public function __construct(array $storyBoards) {
        
        parent::__construct($storyBoards);
    }
    
    /**
     * Add a new Story Board to the collection
     * 
     * @param StoryBoard $storyBoard
     * @return \Magma\PlanningPoker\Story\Board\Collection 
     */
    public function addStoryBoard(StoryBoard $storyBoard) {
        
        $this->offsetSet(null, $storyBoard);
        return $this;
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
    
    public function offsetSet($index, StoryBoard $board) {
        
        parent::offsetSet($index, $board);
    }
    
    /**
     * Retrieve all Story Boards
     * 
     * @return \Magma\PlanningPoker\Story\Board\Collection 
     */
    public function getAll() {
        
        return $this;
    }
    
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
}