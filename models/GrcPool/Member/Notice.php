<?php
abstract class GrcPool_Member_Notice_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_memberId = 0;
	private $_noticeId = 0;
	private $_thetime = 0;
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setMemberId(int $int) {$this->_memberId=$int;}
	public function getMemberId():int {return $this->_memberId;}
	public function setNoticeId(int $int) {$this->_noticeId=$int;}
	public function getNoticeId():int {return $this->_noticeId;}
	public function setThetime(int $int) {$this->_thetime=$int;}
	public function getThetime():int {return $this->_thetime;}
}

abstract class GrcPool_Member_Notice_MODELDAO extends TableDAO {
	protected $_database = 'grcpool';
	protected $_table = 'member_notice';
	protected $_model = 'GrcPool_Member_Notice_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'memberId' => array('type'=>'INT','dbType'=>'int(11)'),
		'noticeId' => array('type'=>'INT','dbType'=>'int(11)'),
		'thetime' => array('type'=>'INT','dbType'=>'int(11)'),
	);
}