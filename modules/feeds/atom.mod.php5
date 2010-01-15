<?php
/**
* Just contains the definition for the class {@link ATOM}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage ATOM
* @filesource
*/

/**
* The module that handles unauthenticated ATOM feeds.
* @package modules
* @subpackage ATOM
*/
class ATOM implements Module {
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

		$cachefile = i2config_get('cache_dir','/var/cache/iodine/','core') . 'atom.cache';

		if(!($contents = ATOM::get_cache($cachefile))) {
			$contents = ATOM::update($cachefile);
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
		$p.="<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$p.="<feed xmlns=\"http://www.w3.org/2005/Atom\">\n";
		$p.="\n";
		$p.="	<title>TJHSST Intranet News</title>\n";
		$p.="	<subtitle>A news feed provided through the TJHSST Intranet.</subtitle>\n";
		$p.="	<link href=\"".$I2_ROOT."atom\" rel=\"self\" />\n";
		$p.="	<link href=\"".$I2_ROOT."\" />\n";
		$p.="	<generator>TJHSST Intranet</generator>\n";
		$p.="	<author>\n";
		$p.="		<name>TJHSST Sysadmins</name>\n";
		$p.="		<email>iodine@tjhsst.edu</email>\n";
		$p.="	</author>\n";
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
			$p.="	<entry>\n";
			$p.="		<title>".strip_tags($item->title)."</title>\n";
			$p.="		<link rel=\"alternate\" href=\"".$I2_ROOT."news/show/$item->nid\" />\n";
			$p.="		<content>".htmlspecialchars($item->text)."</content>\n";
			$p.="		<updated>".date("'Y-m-d\TH:i:s'",strtotime($item->posted))."</updated>\n";
			$p.="		<published>".date("'Y-m-d\TH:i:s'",strtotime($item->posted))."</published>\n";
			$p.="		<id>".$I2_ROOT."news/show/$item->nid</id>\n";
			$p.="	</entry>\n";
		}
		$p.="</feed>\n";
		return $p;
	}
	public static function update($cachefile=FALSE) {
		$cachefile = i2config_get('cache_dir','/var/cache/iodine/','core') . 'atom.cache';
		$contents = ATOM::create_contents();// If the contents of the file havn't been made yet.
		ATOM::store_cache($contents,$cachefile);
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
		return "ATOM";
	}
}

?>
