<?php
class Bootstrap_Callout {
	public static function error($text,$small = false) {
		return '<div class="bs-callout bs-callout-danger '.($small?'bs-callout-small':'').' rowpad">'.$text.'</div>';
	}
	public static function info($text,$small = false) {
		return '<div class="bs-callout bs-callout-info '.($small?'bs-callout-small':'').' rowpad">'.$text.'</div>';
	}
}