<?php

namespace Magma\PlanningPoker\Story;
use Magma\PlanningPoker\Story;

/**
 * Holds a collection of stories to estimate
 *
 * @author michael
 */
class Board {
    
    protected $_id = null;
    protected $_name = null;
    protected $_active = false;
    protected $_stories = null;
    
    public function __construct($name, $id = null, array $stories = array()) {
        
        if (is_null($id)) {
            $id = uniqid();
        }
        
        $this->setId($id)
                ->setName($name)
                ->setStories($stories);
    }
    
    public function setId($id) {
        
        $this->_id = $id;
        return $this;
    }
    
    public function getId() {
        
        return $this->_id;
    }
    
    public function setName($name) {
        
        $this->_name = $name;
        return $this;
    }
    
    public function getName() {
        
        return $this->_name;
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
    
    public function setActive($flag) {
        
        $this->_active = (bool) $flag;
        return $this;
    }
    
    public function toArray() {
        
        $retval = array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'active' => $this->isActive()
        );
        
        return $retval;
    }
}