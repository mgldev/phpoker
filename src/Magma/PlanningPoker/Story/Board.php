<?php

namespace Magma\PlanningPoker\Story;

/**
 * Holds a collection of stories to estimate
 *
 * @author michael
 */
class Board {
    
    protected $_name = null;
    protected $_active = false;
    protected $_stories = null;
    
    public function __construct($name, array $stories = array()) {
        
        $this->setName($name)->setStories($stories);
    }
    
    public function addStory(Story $story) {
        
        $this->_stories = $story;
        return $this;
    }
    
    public function getStories() {
        
        $retval = $this->_stories;
        return $retval;
    }
    
    public function setStories(array $stories) {
        
        $this->_stories = $stories;
        return $this;
    }
    
    public function isActive() {
        
        return $this->_active;
    }
}