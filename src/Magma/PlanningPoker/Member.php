<?php

namespace Magma\PlanningPoker;

/**
 * A member (participant) of Planning Poker
 *
 * @author michael
 */
class Member {

	protected $_username = null;
	protected $_displayName = null;

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

	public function toArray() {

		$retval = array(
			'username' => $this->_username,
			'displayName' => $this->_displayName
		);

		return $retval;
	}
}