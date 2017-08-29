<?php
class Email {

	private $to = array();
	private $from = array();
	private $replyTo = array();
	private $subject = '';
	private $message = '';
	private $server = '';
	private $secure = true;
	public function getSecure() {return $this->secure;}
	public function setSecure($b) {$this->secure = $b;}
	public function getServer() {return $this->server;}
	public function setServer($s) {$this->server = $s;}
	public function getTo() {return $this->to;}
	public function addTo($s) {array_push($this->to,$s);}
	public function getFrom() {return $this->from;}
	public function addFrom($s) {array_push($this->from,$s);}
	public function getReplyTo() {return $this->replyTo;}
	public function addReplyTo($s) {array_push($this->replyTo,$s);}
	public function getSubject() {return $this->subject;}
	public function setSubject($s) {$this->subject = $s;}
	public function getMessage() {return $this->message;}
	public function setMessage($s) {$this->message = $s;}

	public static function getPasswordResetEmail($member,$link) {
		$fullLink = 'https://'.getenv("SERVER_NAME").$link;
		return '
			You or someone pretending to be you has requested a password reset.<br/><br/>
			Here is the link needed to reset your password:
			<a href="'.$fullLink.'">'.$fullLink.'</a>
		';
	}
	
	public static function getVerificationMessage($key,$userid) {
		return '
			Hi and welcome to '.Constants::BOINC_POOL_NAME.'.<br/><br/>
			Thank you for taking the time to verify your email address. This will help to keep grcpool running smoothly and keep you informed of very important updates. 
			Your email address will not be used outside of '.Constants::BOINC_POOL_NAME.' and only for things you should find very important to your involvement with the pool.<br/><br/>
			Your verification link is:<br/>
			<a href="https://'.getenv("SERVER_NAME").'/emailVerification/index/'.$userid.'/'.$key.'">https://'.getenv("SERVER_NAME").'/emailVerification/index/'.$userid.'/'.$key.'</a>
			<br/><br/>
			Thank You
		';
	}
	
	function send() {
		$property = new Property(Constants::PROPERTY_FILE);
		if ($this->secure) {
			$transport = Swift_SmtpTransport::newInstance($property->get('emailServer'),$property->get('emailSslPort'),'ssl')
				->setUsername($property->get('emailUsername'))
				->setPassword($property->get('emailPassword'));
			;
		} else {
			$transport = Swift_SmtpTransport::newInstance($property->get('emailServer'),$property->get('emailPort'))
				->setUsername($property->get('emailUsername'))
				->setPassword($property->get('emailPassword'));
			;
		}
		$mailer = Swift_Mailer::newInstance($transport);
		$message = Swift_Message::newInstance($this->subject)
			->setFrom($this->from)
			->setTo($this->to)
			->setBody($this->message,'text/html')
			->setReplyTo($this->replyTo);
		;
		if ($mailer->send($message)) {
			return true;
		} else {
			return false;
		}
	}

}
