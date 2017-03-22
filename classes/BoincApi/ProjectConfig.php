<?php
class BoincApi_ProjectConfig {
	
	private $name = 'grcpool.com';
	private $min_passwd_length = 8;

	public function getName() {
		return $this->name;
	}
	
	public function toXml() {
		return '<project_config><name>'.$this->name.'</name><min_passwd_length>'.$this->min_passwd_length.'</min_passwd_length><account_manager/><uses_username/><client_account_creation_disabled/></project_config>';
	}
}