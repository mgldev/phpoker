<?php

namespace Magma\PlanningPoker\Story;
use Magma\PlanningPoker\Member;
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

    /**
     * @var Member[]
     */
    protected $_members = null;
    protected $_position = 0;
    
    public function __construct($name, $id = null, array $stories = array()) {
        
        if (is_null($id)) {
            $id = uniqid();
        }
        
        $this->setId($id)
                ->setName($name)
                ->setStories($stories);
        
        $this->setMembers(new \SplObjectStorage);
    }

    /**
     * @return Story
     */
    public function getCurrentStory() {

        return $this->_stories[$this->getStoryPosition()];
    }

    public function getStoryPosition() {

        return $this->_position;
    }

    public function getStoryCount() {

        return count($this->_stories);
    }

    public function nextStory() {

        if (!(($this->_position + 1) > $this->getStoryCount())) {
            $this->_position++;
        }

        return $this->getCurrentStory();
    }

    public function previousStory() {

        if (!(($this->_position - 1) < 0)) {
            $this->_position--;
        }

        return $this->getCurrentStory();
    }

    /**
     * @param \SplObjectStorage $members
     * @return $this
     */
    public function setMembers(\SplObjectStorage $members) {
        
        $this->_members = $members;
        return $this;
    }

    /**
     * @return Member[]
     */
    public function getMembers() {
        
        return $this->_members;
    }

    /**
     * @param Member $member
     * @return $this
     */
    public function addMember(Member $member) {
        
        $this->getMembers()->attach($member);
        return $this;
    }

    public function removeMember(Member $member) {

        $this->getMembers()->detach($member);
        return $this;
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
        
        $this->_stories[] = $story;
        return $this;
    }
    
    public function getStories() {
        
        $retval = $this->_stories;
        return $retval;
    }
    
    public function setStories(array $stories) {

        foreach ($stories as $story) {
            $this->addStory($story);
        }

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
            'active' => $this->isActive(),
            'story_count' => $this->getStoryCount(),
            'story_position' => $this->getStoryPosition() + 1,
            'members' => $this->getMemberArray()
        );
        
        return $retval;
    }

    public function getMemberArray() {

        $retval = array();

        foreach (clone $this->getMembers() as $member) {
            $retval[] = $member->toArray();
        }

        return $retval;
    }
}