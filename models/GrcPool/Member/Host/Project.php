<?php
abstract class GrcPool_Member_Host_Project_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_memberId = 0;
	private $_poolId = 1;
	private $_hostId = 0;
	private $_hostCpid = '';
	private $_hostDbid = 0;
	private $_projectUrl = '';
	private $_noCpu = 0;
	private $_noNvidiaGpu = 0;
	private $_noAtiGpu = 0;
	private $_noIntelGpu = 0;
	private $_resourceShare = 100;
	private $_attached = 0;
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setMemberId(int $int) {$this->_memberId=$int;}
	public function getMemberId():int {return $this->_memberId;}
	public function setPoolId(int $int) {$this->_poolId=$int;}
	public function getPoolId():int {return $this->_poolId;}
	public function setHostId(int $int) {$this->_hostId=$int;}
	public function getHostId():int {return $this->_hostId;}
	public function setHostCpid(string $string) {$this->_hostCpid=$string;}
	public function getHostCpid():string {return $this->_hostCpid;}
	public function setHostDbid(int $int) {$this->_hostDbid=$int;}
	public function getHostDbid():int {return $this->_hostDbid;}
	public function setProjectUrl(string $string) {$this->_projectUrl=$string;}
	public function getProjectUrl():string {return $this->_projectUrl;}
	public function setNoCpu(int $int) {$this->_noCpu=$int;}
	public function getNoCpu():int {return $this->_noCpu;}
	public function setNoNvidiaGpu(int $int) {$this->_noNvidiaGpu=$int;}
	public function getNoNvidiaGpu():int {return $this->_noNvidiaGpu;}
	public function setNoAtiGpu(int $int) {$this->_noAtiGpu=$int;}
	public function getNoAtiGpu():int {return $this->_noAtiGpu;}
	public function setNoIntelGpu(int $int) {$this->_noIntelGpu=$int;}
	public function getNoIntelGpu():int {return $this->_noIntelGpu;}
	public function setResourceShare(int $int) {$this->_resourceShare=$int;}
	public function getResourceShare():int {return $this->_resourceShare;}
	public function setAttached(int $int) {$this->_attached=$int;}
	public function getAttached():int {return $this->_attached;}
}

abstract class GrcPool_Member_Host_Project_MODELDAO extends TableDAO {
	protected $_database = 'grcpool';
	protected $_table = 'member_host_project';
	protected $_model = 'GrcPool_Member_Host_Project_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'memberId' => array('type'=>'INT','dbType'=>'int(11)'),
		'poolId' => array('type'=>'INT','dbType'=>'smallint(3)'),
		'hostId' => array('type'=>'INT','dbType'=>'int(11)'),
		'hostCpid' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'hostDbid' => array('type'=>'INT','dbType'=>'int(11)'),
		'projectUrl' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'noCpu' => array('type'=>'INT','dbType'=>'int(1)'),
		'noNvidiaGpu' => array('type'=>'INT','dbType'=>'int(1)'),
		'noAtiGpu' => array('type'=>'INT','dbType'=>'int(1)'),
		'noIntelGpu' => array('type'=>'INT','dbType'=>'int(1)'),
		'resourceShare' => array('type'=>'INT','dbType'=>'int(6)'),
		'attached' => array('type'=>'INT','dbType'=>'int(1)'),
	);
}