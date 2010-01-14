<?php
/**
* Just contains the definition for the class {@link RSS}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage RSS
* @filesource
*/

/**
* The module that handles unauthenticated RSS feeds.
* @package modules
* @subpackage RSS
*/
class RSS implements Module {
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
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		global $I2_ROOT;
		header("Content-Type: application/xml");

		$cachefile = i2config_get('cache_dir','/var/cache/iodine/','core') . 'rss.cache';

		if(!($contents = RSS::get_cache($cachefile))) {
			$contents = RSS::update($cachefile);
		}
		echo $contents;
		Display::stop_display();
	}

	private static function get_cache($cachefile) {
		if(!file_exists($cachefile) || !($contents = file_get_contents($cachefile))) {
			return FALSE;
		}
		
		return unserialize($contents);
	}

	private static function store_cache($content,$cachefile) {
		$fh = fopen($cachefile,'w');
		$serial = serialize($content);
		fwrite($fh,$serial);
		fclose($fh);
	}

	private static function create_contents() {
		global $I2_ROOT;
		$p = "";
		$p.="<?xml version=\"1.0\"?>\n";
		$p.="<rss version=\"2.0\">\n";
		$p.="	<channel>\n";
		$p.="		<title>TJHSST Intranet News</title>\n";
		$p.="		<link>".$I2_ROOT."</link>\n";
		$p.="		<description>TJHSST Intranet News</description>\n";
		$p.="		<language>en-us</language>\n";
		$p.="		<pubDate>".date("r")."</pubDate>\n"; //We should make a variable to store this later.
		$p.="		<generator>TJHSST Intranet</generator>\n";
		$p.="		<managingEditor>iodine@tjhsst.edu</managingEditor>\n";
		$p.="		<webMaster>iodine@tjhsst.edu</webMaster>\n";
		$news = NewsItem::get_all_items_nouser();
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
			$p.="		<item>\n";
			$p.="			<title>".strip_tags($item->title)."</title>\n";
			$p.="			<link>".$I2_ROOT."news/show/$item->nid</link>\n";
			$p.="			<description>".htmlspecialchars($item->text)."</description>\n";
			$p.="			<pubDate>".date("r",strtotime($item->posted))."</pubDate>\n";
			$p.="			<guid>".$I2_ROOT."news/show/$item->nid</guid>\n";
			$p.="		</item>\n";
		}
		$p.="	</channel>\n";
		$p.="</rss>\n";
		return $p;
	}
	public static function update($cachefile=FALSE) {
		$cachefile = i2config_get('cache_dir','/var/cache/iodine/','core') . 'rss.cache';
		$contents = RSS::create_contents();// If the contents of the file havn't been made yet.
		RSS::store_cache($contents,$cachefile);
		return $contents;
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
		return "RSS";
	}
}

?>
