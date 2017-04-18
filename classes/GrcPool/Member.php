<?php
class GrcPool_Member_OBJ extends GrcPool_Member_MODEL {
	
	public function __construct() {
		parent::__construct();
	}
	
	public function hasAlerts() {
		$count = 0;
		if ($this->getVerified() == 0) {$count++;}
		if ($this->getGrcAddress() == '') {$count++;}
		$projectDao = new GrcPool_Member_Host_Project_DAO();
		$projs = $projectDao->getWithMemberIdAndDbid($this->getId(),0);
		if ($projs) {$count++;}
		return $count;
	}
}

class GrcPool_Member_DAO extends GrcPool_Member_MODELDAO {
	/**
	 *
	 * @param string $username
	 * @return NULL|GrcPool_Member_OBJ
	 */
	public function initWithUsername($username) {
		return $this->fetch(array($this->where('username',$username)));
	}
	
	/**
	 *
	 * @param string $email
	 * @return NULL|GrcPool_Member_OBJ
	 */
	public function initWithEmail($email) {
		return $this->fetch(array($this->where('email',$email)));
	}
	
	/**
	 *
	 * @param string $hash
	 * @return NULL|GrcPool_Member_OBJ
	 */
	public function initWithHash($hash) {
		return $this->fetch(array($this->where('passwordHash',$hash)));
	}
	
	
	/**
	 *
	 * @return GrcPool_Member_OBJ
	 */
	public function initWithSession() {
		if (isset($_COOKIE[SESSION_COOKIE_NAME])) {
			$sessionDao = new GrcPool_Session_DAO();
			$session = $sessionDao->fetch(array(
					$sessionDao->where('session',$_COOKIE[SESSION_COOKIE_NAME])
			));
			if ($session) {
				$session->setLastUsed(time());
				$userDao = new GrcPool_Member_DAO();
				return $userDao->initWithKey($session->getUserid());
			}
		}
		return new GrcPool_Member_OBJ();
	}
}