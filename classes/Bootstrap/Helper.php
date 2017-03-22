<?php
class Bootstrap_Helper {
	/**
	 * 
	 * @param int $lg
	 * @param int $md
	 * @param int $sm
	 * @param int $xs
	 * @return string
	 */
	public static function autoClear($lg,$md,$sm,$xs) {
		return '
			<style>
				@media (min-width:1200px){.auto-clear .col-lg-'.$lg.':nth-child('.floor(12/$lg).'n+1){clear:left;}}
				@media (min-width:992px) and (max-width:1199px){.auto-clear .col-md-'.$md.':nth-child('.floor(12/$md).'n+1){clear:left;}}
				@media (min-width:768px) and (max-width:991px){.auto-clear .col-sm-'.$sm.':nth-child('.floor(12/$sm).'n+1){clear:left;}}
				@media (max-width:767px){.auto-clear .col-xs-'.$xs.':nth-child(odd){clear:left;}}
			</style>
		';		
	}
}