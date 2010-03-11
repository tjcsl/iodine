<?php
/**
* Just contains the definition for the class {@link Suggest}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Suggest
* @filesource
*/

/**
* The module that handles search suggestions.
* @package modules
* @subpackage Suggest
*/
class Suggest implements Module {
	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function init_mobile() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_mobile($disp) {
		return FALSE;
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		return 'Intranet Suggest';
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		global $I2_ARGS,$I2_ROOT;
		if(!isset($I2_ARGS[1]) || !isset($I2_ARGS[2])) {
			redirect();
		} else if($I2_ARGS[1] == 'searchsuggest') {
			/*
			header("Content-Type: application/xml");
			$cachefile = i2config_get('cache_dir','/var/cache/iodine/','core') . 'rss.cache';
			if(!($contents = RSS::get_cache($cachefile))) {
				$contents = RSS::update($cachefile);
			}
			unserialize($contents);*/
			if(strlen($I2_ARGS[2])>=3) {
				$arr = User::search_info($I2_ARGS[2]);
				if(count($arr)>10) {
					$arr=array_slice($arr,0,10);
				}
				foreach($arr as $ar) {
					echo "<a href=\"$I2_ROOT/studentdirectory/info/$ar->uid\">" . $ar->fullname."</a><br />";
				}
			} else {
				echo "";
			}
			Display::stop_display();
		} else {
			redirect();
		}
	}

	public static function update() {
		RSS::update();
		ATOM::update();
	}
	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		return FALSE;
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
		return FALSE;
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return "Feeds";
	}
	public static function getItems() {
		$news = NewsItem::get_all_items_nouser();
		$returner = array();
		foreach($news as $item) {
			if($item->public==0) //Only display stuff that's public.
				continue;
			$test=FALSE;	//Stuff only goes on the feed if "all" can see it.
			foreach ($item->groups as $group) {
				if($group->gid == 1) {
					$test=TRUE;
					break;
				}
			}
			if(!$test) {
				continue;
			}
			$returner[] = $item;
		}
		return $returner;
	}
}

?>
