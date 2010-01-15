<?php
/**
* Just contains the definition for the class {@link Feeds}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage RSS
* @filesource
*/

/**
* The module that handles unauthenticated feeds.
* @package modules
* @subpackage Feeds
*/
class Feeds implements Module {
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
		return 'Intranet Feeds';
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		global $I2_ARGS;
		if(!isset($I2_ARGS[1])) {
			redirect();
		} else if($I2_ARGS[1] == 'rss') {
			header("Content-Type: application/xml");
			$cachefile = i2config_get('cache_dir','/var/cache/iodine/','core') . 'rss.cache';
			if(!($contents = RSS::get_cache($cachefile))) {
				$contents = RSS::update($cachefile);
			}
			echo $contents;
			Display::stop_display();
		} else if($I2_ARGS[1] == 'atom') {
			header("Content-Type: application/xml");
			$cachefile = i2config_get('cache_dir','/var/cache/iodine/','core') . 'atom.cache';
			if(!($contents = ATOM::get_cache($cachefile))) {
				$contents = ATOM::update($cachefile);
			}
			echo $contents;
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
