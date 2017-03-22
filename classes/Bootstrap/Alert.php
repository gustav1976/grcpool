<?php
class Bootstrap_Alert {
	public static function error($text,$heading='',$close = true) {
		return '
			<div class="alert alert-danger" style="margin-bottom:20px;margin-top:20px;">
				'.($close?'<a class="close" data-dismiss="alert">&times;</a>':'').'
				'.($heading!=''?'<h4 class="alert-heading">'.$heading.'</h4>':'').'
				'.$text.'
			</div>
		';
	}
	public static function success($text,$heading='',$close = true) {
		return '
			<div class="alert alert-success" style="margin-bottom:20px;margin-top:20px;">
				'.($close?'<a class="close" data-dismiss="alert">&times;</a>':'').'
				'.($heading!=''?'<h4 class="alert-heading">'.$heading.'</h4>':'').'
				'.$text.'
			</div>
		';
	}	
	public static function warning($text,$heading='',$close = true) {
		return '
			<div class="alert alert-warning" style="margin-bottom:20px;margin-top:20px;">
				'.($close?'<a class="close" data-dismiss="alert">&times;</a>':'').'
				'.($heading!=''?'<h4 class="alert-heading">'.$heading.'</h4>':'').'
				'.$text.'
			</div>
		';
	}
	public static function info($text,$heading = '',$close = true) {
		return '
			<div class="alert alert-info" style="margin-bottom:20px;margin-top:20px;">
				'.($close?'<a class="close" data-dismiss="alert">&times;</a>':'').'
				'.($heading!=''?'<h4 class="alert-heading">'.$heading.'</h4>':'').'
				'.$text.'
			</div>
		';		
	}
}
