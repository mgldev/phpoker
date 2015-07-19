<?php

namespace Magma\PlanningPoker;

use Magma\PlanningPoker\Story\Board as StoryBoard;
use Magma\PlanningPoker\Story\Board;

/**
 * A member (participant) of Planning Poker
 *
 * @author michael
 */
class Member {

	protected $_username = null;
	protected $_displayName = null;

    /**
     * @var Board
     */
    protected $_board = null;

	public function setUsername($username) {

		$this->_username = $username;
		return $this;
	}

	public function getUsername() {

		return $this->_username;
	}

	public function setDisplayName($displayName) {

		$this->_displayName = $displayName;
		return $this;
	}

	public function getDisplayName() {

		return $this->_displayName;
	}

    /**
     * @param StoryBoard $board
     * @return $this
     */
    public function joinBoard(Board $board) {

        $this->_board = $board;
        $this->_board->addMember($this);
        return $this;
    }

    /**
     * @return Board
     */
    public function getCurrentStoryBoard() {
        
        return $this->_board;
    }
    
    /**
     * Provide an estimate for the current story
     * 
     * @param float $estimate
     * @return \Magma\PlanningPoker\Member
     * @throws \DomainException 
     */
    public function estimateCurrentStory($estimate) {
        
        $board = $this->getCurrentStoryBoard();
        
        if (is_null($board)) {
            throw new \DomainException('Member has not joined a board');
        }
        
        $story = $board->getCurrentStory();
        
        if (is_null($story)) {
            throw new \DomainException('Board does not have an active current story');
        }
        
        $story->addEstimate($this, $estimate);
        return $this;
    }

	public function toArray() {

		$retval = array(
			'username' => $this->_username,
			'displayName' => $this->_displayName
		);

		return $retval;
	}
}