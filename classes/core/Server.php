<?php
class Server {
 	static function isSecure() {
 		if(!empty( $_SERVER['https'] ) ) {
 			return true;
 		}
 		if(!empty( $_SERVER['HTTPS'] ) ) {
 			return true;
 		}		
 		if(!empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
 			return true;
 		}
 		return false;
 	}	
 	static function goHome() {
 		header("Location: ".getenv("SERFVER_NAME").'/');exit;	
 	}
 	static function isDevelopment() {
 		if (strstr(dirname(__FILE__),"sandbox")) {
 			return true;
 		} else {
 			return false;
 		}
 	}	
 	static function go($path,$perm = false) {
 		if ($perm) {
 			header("HTTP/1.1 301 Moved Permanently"); 	
 		}
 		$protocol = 'http';
 		if (Server::isSecure()) {
 			$protocol .= 's';
 		}
 		header('Location: '.$protocol.'://'.getenv("SERVER_NAME").$path);
 		exit;	
 	}
}