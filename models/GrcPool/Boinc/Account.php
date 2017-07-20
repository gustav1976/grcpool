<?php
abstract class GrcPool_Boinc_Account_MODEL {

	public function __construct() { }

	private $_id = 0;
	private $_name = '';
	private $_url = '';
	private $_signature = '';
	private $_weakKey = '';
	private $_strongKey = '';
	private $_weakKey2 = '';
	private $_strongKey2 = '';
	private $_whiteList = 0;
	private $_rac = 0;
	private $_baseUrl = '';
	private $_teamId = 0;
	private $_attachable = 0;
	private $_message = '';
	private $_grcname = '';
	private $_lastSeen = 0;
	private $_secure = 0;
	private $_whiteListCount = 0;
	private $_minRac = 0.00;
	private $_android = 0;
	private $_raspberryPi = 0;
	private $_linux = 0;
	private $_windows = 0;
	private $_virtualBox = 0;
	private $_intel = 0;
	private $_amd = 0;
	private $_nvidia = 0;
	private $_mac = 0;
	public function setId(int $int) {$this->_id=$int;}
	public function getId():int {return $this->_id;}
	public function setName(string $string) {$this->_name=$string;}
	public function getName():string {return $this->_name;}
	public function setUrl(string $string) {$this->_url=$string;}
	public function getUrl():string {return $this->_url;}
	public function setSignature(string $string) {$this->_signature=$string;}
	public function getSignature():string {return $this->_signature;}
	public function setWeakKey(string $string) {$this->_weakKey=$string;}
	public function getWeakKey():string {return $this->_weakKey;}
	public function setStrongKey(string $string) {$this->_strongKey=$string;}
	public function getStrongKey():string {return $this->_strongKey;}
	public function setWeakKey2(string $string) {$this->_weakKey2=$string;}
	public function getWeakKey2():string {return $this->_weakKey2;}
	public function setStrongKey2(string $string) {$this->_strongKey2=$string;}
	public function getStrongKey2():string {return $this->_strongKey2;}
	public function setWhiteList(int $int) {$this->_whiteList=$int;}
	public function getWhiteList():int {return $this->_whiteList;}
	public function setRac(float $float) {$this->_rac=$float;}
	public function getRac():float {return $this->_rac;}
	public function setBaseUrl(string $string) {$this->_baseUrl=$string;}
	public function getBaseUrl():string {return $this->_baseUrl;}
	public function setTeamId(int $int) {$this->_teamId=$int;}
	public function getTeamId():int {return $this->_teamId;}
	public function setAttachable(int $int) {$this->_attachable=$int;}
	public function getAttachable():int {return $this->_attachable;}
	public function setMessage(string $string) {$this->_message=$string;}
	public function getMessage():string {return $this->_message;}
	public function setGrcname(string $string) {$this->_grcname=$string;}
	public function getGrcname():string {return $this->_grcname;}
	public function setLastSeen(int $int) {$this->_lastSeen=$int;}
	public function getLastSeen():int {return $this->_lastSeen;}
	public function setSecure(int $int) {$this->_secure=$int;}
	public function getSecure():int {return $this->_secure;}
	public function setWhiteListCount(int $int) {$this->_whiteListCount=$int;}
	public function getWhiteListCount():int {return $this->_whiteListCount;}
	public function setMinRac(float $float) {$this->_minRac=$float;}
	public function getMinRac():float {return $this->_minRac;}
	public function setAndroid(int $int) {$this->_android=$int;}
	public function getAndroid():int {return $this->_android;}
	public function setRaspberryPi(int $int) {$this->_raspberryPi=$int;}
	public function getRaspberryPi():int {return $this->_raspberryPi;}
	public function setLinux(int $int) {$this->_linux=$int;}
	public function getLinux():int {return $this->_linux;}
	public function setWindows(int $int) {$this->_windows=$int;}
	public function getWindows():int {return $this->_windows;}
	public function setVirtualBox(int $int) {$this->_virtualBox=$int;}
	public function getVirtualBox():int {return $this->_virtualBox;}
	public function setIntel(int $int) {$this->_intel=$int;}
	public function getIntel():int {return $this->_intel;}
	public function setAmd(int $int) {$this->_amd=$int;}
	public function getAmd():int {return $this->_amd;}
	public function setNvidia(int $int) {$this->_nvidia=$int;}
	public function getNvidia():int {return $this->_nvidia;}
	public function setMac(int $int) {$this->_mac=$int;}
	public function getMac():int {return $this->_mac;}
}

abstract class GrcPool_Boinc_Account_MODELDAO extends TableDAO {
	protected $_database = 'grcpool';
	protected $_table = 'boinc_account';
	protected $_model = 'GrcPool_Boinc_Account_OBJ';
	protected $_primaryKey = 'id';
	protected $_fields = array(
		'id' => array('type'=>'INT','dbType'=>'int(3)'),
		'name' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'url' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'signature' => array('type'=>'STRING','dbType'=>'varchar(500)'),
		'weakKey' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'strongKey' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'weakKey2' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'strongKey2' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'whiteList' => array('type'=>'INT','dbType'=>'int(1)'),
		'rac' => array('type'=>'FLOAT','dbType'=>'decimal(22,8)'),
		'baseUrl' => array('type'=>'STRING','dbType'=>'varchar(100)'),
		'teamId' => array('type'=>'INT','dbType'=>'int(8)'),
		'attachable' => array('type'=>'INT','dbType'=>'tinyint(1)'),
		'message' => array('type'=>'STRING','dbType'=>'varchar(500)'),
		'grcname' => array('type'=>'STRING','dbType'=>'varchar(50)'),
		'lastSeen' => array('type'=>'INT','dbType'=>'int(11)'),
		'secure' => array('type'=>'INT','dbType'=>'tinyint(1)'),
		'whiteListCount' => array('type'=>'INT','dbType'=>'smallint(3)'),
		'minRac' => array('type'=>'FLOAT','dbType'=>'decimal(9,2)'),
		'android' => array('type'=>'INT','dbType'=>'tinyint(1)'),
		'raspberryPi' => array('type'=>'INT','dbType'=>'tinyint(1)'),
		'linux' => array('type'=>'INT','dbType'=>'tinyint(1)'),
		'windows' => array('type'=>'INT','dbType'=>'tinyint(1)'),
		'virtualBox' => array('type'=>'INT','dbType'=>'tinyint(1)'),
		'intel' => array('type'=>'INT','dbType'=>'tinyint(1)'),
		'amd' => array('type'=>'INT','dbType'=>'tinyint(1)'),
		'nvidia' => array('type'=>'INT','dbType'=>'tinyint(1)'),
		'mac' => array('type'=>'INT','dbType'=>'tinyint(1)'),
	);
}