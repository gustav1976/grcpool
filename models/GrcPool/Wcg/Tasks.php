<?php
/* ***********************************************************************
THIS FILE WAS CREATED AUTOMATICALLY BY PHP MODEL/OBJECT CREATOR
MANUAL MODIFICATIONS WILL BE AUTOMATICALLY OVERWRITTEN
************************************************************************ */
abstract class GrcPool_Wcg_Tasks_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_poolId = 1;
	private $_appName = '';
	private $_claimedCredit = 0.00000000000000000000;
	private $_cpuTime = 0.00000000000000000000;
	private $_elapsedTime = 0.00000000000000000000;
	private $_exitStatus = 0;
	private $_grantedCredit = 0.00000000000000000000;
	private $_deviceId = 0;
	private $_modTime = 0;
	private $_workUnitId = 0;
	private $_resultId = 0;
	private $_name = '';
	private $_outcome = 0;
	private $_receivedTime = 0;
	private $_reportDeadline = 0;
	private $_sentTime = 0;
	private $_serverState = 0;
	private $_validateState = 0;
	private $_fileDeleteState = 0;
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setPoolId(int $int) {$this->_poolId=$int;}
	public function getPoolId():int {return $this->_poolId;}
	public function setAppName(string $string) {$this->_appName=$string;}
	public function getAppName():string {return $this->_appName;}
	public function setClaimedCredit(float $float) {$this->_claimedCredit=$float;}
	public function getClaimedCredit():float {return $this->_claimedCredit;}
	public function setCpuTime(float $float) {$this->_cpuTime=$float;}
	public function getCpuTime():float {return $this->_cpuTime;}
	public function setElapsedTime(float $float) {$this->_elapsedTime=$float;}
	public function getElapsedTime():float {return $this->_elapsedTime;}
	public function setExitStatus(int $int) {$this->_exitStatus=$int;}
	public function getExitStatus():int {return $this->_exitStatus;}
	public function setGrantedCredit(float $float) {$this->_grantedCredit=$float;}
	public function getGrantedCredit():float {return $this->_grantedCredit;}
	public function setDeviceId(int $int) {$this->_deviceId=$int;}
	public function getDeviceId():int {return $this->_deviceId;}
	public function setModTime(int $int) {$this->_modTime=$int;}
	public function getModTime():int {return $this->_modTime;}
	public function setWorkUnitId(int $int) {$this->_workUnitId=$int;}
	public function getWorkUnitId():int {return $this->_workUnitId;}
	public function setResultId(int $int) {$this->_resultId=$int;}
	public function getResultId():int {return $this->_resultId;}
	public function setName(string $string) {$this->_name=$string;}
	public function getName():string {return $this->_name;}
	public function setOutcome(int $int) {$this->_outcome=$int;}
	public function getOutcome():int {return $this->_outcome;}
	public function setReceivedTime(int $int) {$this->_receivedTime=$int;}
	public function getReceivedTime():int {return $this->_receivedTime;}
	public function setReportDeadline(int $int) {$this->_reportDeadline=$int;}
	public function getReportDeadline():int {return $this->_reportDeadline;}
	public function setSentTime(int $int) {$this->_sentTime=$int;}
	public function getSentTime():int {return $this->_sentTime;}
	public function setServerState(int $int) {$this->_serverState=$int;}
	public function getServerState():int {return $this->_serverState;}
	public function setValidateState(int $int) {$this->_validateState=$int;}
	public function getValidateState():int {return $this->_validateState;}
	public function setFileDeleteState(int $int) {$this->_fileDeleteState=$int;}
	public function getFileDeleteState():int {return $this->_fileDeleteState;}
}

abstract class GrcPool_Wcg_Tasks_MODELDAO extends TableDAO {
	protected $_database = Constants::DATABASE_NAME;
	protected $_table = 'wcg_tasks';
	protected $_model = 'GrcPool_Wcg_Tasks_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(11)'),
		'poolId' => array('type'=>'INT','dbType'=>'smallint(3)'),
		'appName' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'claimedCredit' => array('type'=>'FLOAT','dbType'=>'decimal(30,20)'),
		'cpuTime' => array('type'=>'FLOAT','dbType'=>'decimal(30,20)'),
		'elapsedTime' => array('type'=>'FLOAT','dbType'=>'decimal(30,20)'),
		'exitStatus' => array('type'=>'INT','dbType'=>'smallint(2)'),
		'grantedCredit' => array('type'=>'FLOAT','dbType'=>'decimal(30,20)'),
		'deviceId' => array('type'=>'INT','dbType'=>'int(11)'),
		'modTime' => array('type'=>'INT','dbType'=>'int(11)'),
		'workUnitId' => array('type'=>'INT','dbType'=>'int(11)'),
		'resultId' => array('type'=>'INT','dbType'=>'int(11)'),
		'name' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'outcome' => array('type'=>'INT','dbType'=>'smallint(2)'),
		'receivedTime' => array('type'=>'INT','dbType'=>'int(11)'),
		'reportDeadline' => array('type'=>'INT','dbType'=>'int(11)'),
		'sentTime' => array('type'=>'INT','dbType'=>'int(11)'),
		'serverState' => array('type'=>'INT','dbType'=>'smallint(2)'),
		'validateState' => array('type'=>'INT','dbType'=>'smallint(2)'),
		'fileDeleteState' => array('type'=>'INT','dbType'=>'smallint(2)'),
	);
}