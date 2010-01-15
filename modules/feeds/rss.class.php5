<?php
/**
* Just contains the definition for the class {@link RSS}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package RSS
* @filesource
*/

/**
* The class that handles unauthenticated RSS feeds.
* @package RSS
*/
class RSS {
	public static function get_cache($cachefile) {
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
		$news = Feeds::getItems();
		foreach($news as $item) {
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
}

?>
