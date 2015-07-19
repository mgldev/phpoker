<?php

namespace Magma\PlanningPoker;

/**
 * Represents a story which contains a name, description
 * and a collection of estimates from members
 *
 * @author michael
 */
class Story {
    
    protected $_name = null;
    protected $_description = null;
    protected $_estimates = null;
    
    public function __construct($name = null, $description = null) {
        
        $this->setName($name)->setDescription($description);
        $this->_estimates = new \SplObjectStorage();
    }
    
    public function setName($name) {
        
        $this->_name = $name;
        return $this;
    }
    
    public function getName() {
        
        return $this->_name;
    }
    
    public function setDescription($description) {
        
        $this->_description = $description;
        return $this;
    }
    
    public function getDescription() {
        
        return $this->_description;
    }
    
    public function addEstimate(Member $member, $estimate) {
        
        if (!is_float($estimate)) {
            throw new \InvalidArgumentException('$estimate must be a float');
        }
        
        $this->getEstimates()->attach($member, $estimate);
        return $this;
    }
    
    public function getEstimates() {
        
        return $this->_estimates;
    }

    public function toArray() {

        $retval = array(
            'name' => $this->getName(),
            'description' => $this->getDescription()
        );

        return $retval;
    }
}