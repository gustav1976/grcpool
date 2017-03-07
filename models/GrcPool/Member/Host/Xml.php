<?php
abstract class GrcPool_Member_Host_Xml_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_memberId = 0;
	private $_hostId = 0;
	private $_theTime = 0;
	private $_xml = '';
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setMemberId(int $int) {$this->_memberId=$int;}
	public function getMemberId():int {return $this->_memberId;}
	public function setHostId(int $int) {$this->_hostId=$int;}
	public function getHostId():int {return $this->_hostId;}
	public function setTheTime(int $int) {$this->_theTime=$int;}
	public function getTheTime():int {return $this->_theTime;}
	public function setXml(string $string) {$this->_xml=$string;}
	public function getXml():string {return $this->_xml;}
}

abstract class GrcPool_Member_Host_Xml_MODELDAO extends TableDAO {
	protected $_database = 'grcpool';
	protected $_table = 'member_host_xml';
	protected $_model = 'GrcPool_Member_Host_Xml_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'memberId' => array('type'=>'INT','dbType'=>'int(11)'),
		'hostId' => array('type'=>'INT','dbType'=>'int(11)'),
		'theTime' => array('type'=>'INT','dbType'=>'int(11)'),
		'xml' => array('type'=>'STRING','dbType'=>'longblob'),
	);
}