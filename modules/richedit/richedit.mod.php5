<?php
/**
* The discussion class file.
* @author Derek Morris + Dyllan Ladwig
* @copyright 2007 The Intranet 2 Development Team
* @package modules
* @subpackage Discussions
* @filesource
*/

/**
* The class that represents a Discussion.
* @package modules
* @subpackage Discussions
*/
class RichEdit implements Module {

	// Variable removed after budget cuts
	//private $OMGPONIES

	private static $bb_otags, $bb_ctags, $bb_nostrip, $errs;
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
	* Unused; Not supported for this module.
	*/
	function init_cli() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_cli($disp) {
		return FALSE;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function api($disp) {
		return false;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*/
	function api_build_dtd() {
		return false;
	}

	function init_pane() {
		return 'RichEdit';
	}

	function display_pane($display) {
		global $I2_FS_ROOT;
		// This just makes basically a blank page with just the css set
		// That way the editor already has the css loaded for you
		echo $display->fetch("richeditwindow.tpl", array(), FALSE);
		Display::stop_display();
		exit;
	}

	function init_box() {
		return FALSE;
	}

	function display_box($display) {
		return FALSE;
	}

	function get_name() {
		return 'Rich Text Editor';
	}
	/**
	 * Vars this can get:
	 * nothing, but it returns chicken soup anyway because it's good for you.
	 */
	public function __get($var) {
		return "Chicken Soup";
	}

	/**
	 * Creates a Richedit object with the given id.
	 * This does not modify the database in any way; it's just used for
	 * objects.
	 */
	public function __construct() {
	}

	/**
	 * Process the string given through bbcode, and returns
	 * the string in an html form. Note that currently this
	 * escapes all html, and you'll want to save the bbcode
	 * verion if you want to have edit functionality later.
	 *
	 * @param string $text The input to process
	 */
	// Dyllan Ladwig's awesome BBCode handler - currently unused, but still cool
	public static function doBCode($post) {
		global $bb_otags, $bb_ctags, $bb_nostrip, $errs;
		$bb_otags=array('lb'=>'[','rb'=>']', 'hr'=>"<div class='bb_hr'></div>", 'hairline'=>"<div class='bb_hr'></div>", 'b'=>"<span class='bb_bold'>", 'bold'=>"<span class='bb_bold'>", 'i'=>"<span class='bb_italic'>", 'ital'=>"<span class='bb_italic'>", 'italic'=>"<span class='bb_italic", 'u'=>"<span class='bb_underline'>", 'under'=>"<span class='bb_underline'>", 'underline'=>"span class='bb_underline'", 'hl'=>"<span class='bb_highlight'>", 'high'=>"<span class='bb_highlight'", 'highlight'=>"<span class='bb_highlight'>", 's'=>"<span class='bb_strike'>", 'strike'=>"<span class='bb_strike'>", 'strikethrough'=>"<span class='bb_strikethrough'>", 'a='=>array("<a href='", "'>", 'safe'=>'strtr'), 'anchor='=>array("a href='","'>", 'safe'=>'strtr'), 'img='=>array("<img src='", "' alt='User Picture' />", 'safe'=>'strtr'), 'image='=>array("<img src='","' alt='User Picture' />", 'safe'=>'strtr'), 'color='=>array("<span style='color:","'>",'safe'=>'strtr'), 'url='=>array("<a href='","'>", 'safe'=>'strtr'));
		$bb_ctags=array('b'=>'</span>', 'bold'=>'</span>', 'i'=>'</span>', 'ital'=>'</span>', 'italic'=>'</span>', 'u'=>'</span>', 'under'=>'</span>', 'underline'=>'</span>', 'hl'=>'</span>', 'high'=>'</span>', 'highlight'=>'</span>', 's'=>'</span>', 'strike'=>'</span>', 'strikethrough'=>'</span', 'a'=>'</a>', 'color'=>'</span>', 'url'=>'</a>', 'anchor'=>'</a>');
		$bb_nostrip=array('lb'=>1, 'rb'=>1);
		$errs = array();

		return Discussion::bbCodeConvert($post);
		if(count($errs)>0)
			foreach($errs as $i)
				throw new I2Exception($i);

	}
	private static function bbCodeConvert($in) {
		global $bb_otags, $bb_ctags;

		$table=get_html_translation_table();
		$table["\n"]="<br />\n";

		$otags=$bb_otags;
		$ctags=$bb_ctags;

		$c=Discussion::recursiveConverter($in, $table, $otags, $ctags, 50);

		return $c[0];
	}
	private static function recursiveConverter($in, $table, $otags, $ctags, $depth, $tag=false, $tags=false) {
		global $errs;

		if($tags===false) {
			$tags=array();
		}
		if($depth<0) {
			$errs[] = "One of the posters in this thread has tried to overnest BBCode elements. There can be no more than 50 nested BBCode elements.";
			return array('', $in, true);
		}
		$fail=false;
		$out="";
		while(1) {
			$pos=strpos($in, '[');

			if($pos===false) {
				$out.=strtr($in, $table);
				$in="";
				$fail=true;
				break;
			}
			$out.=strtr(substr($in, 0, $pos), $table);
			$in=substr($in, $pos);

			$epos=strpos($in, ']');

			if($epos===false) {
				$out.=strtr($in, $table);
				$in="";
				$fail=true;
				break;
			}

			$nxcstag=substr($in, 1, $epos-1);
			$nxtag=strtolower($nxcstag);

			$qpos=strpos($nxtag, "=");

			if($qpos !== false) {
				$nxetag=substr($nxtag, 0, $qpos);
				$nxqtag=substr($nxtag, 0, $qpos+1);
				$eparam=substr($nxcstag, $qpos+1);

				//print ';'.$nxetag.';'."\n";

				if(isset($otags[$nxqtag])&&isset($ctags[$nxetag])) {
					$tags[]=$nxetag;
					$rco = Discussion::recursiveConverter(substr($in, strlen($nxtag)+2), $table, $otags, $ctags, $depth-1, $nxetag, $tags);
					//print ":".$nxetag.":".$rco[0].":".$rco[1].":".($rco[2]?"F":"T").":\n\n";
					unset($tags[count($tags)-1]);
					if($rco[2]) {
						$out.=strtr(substr($in, 0, $epos+1), $table);
					} else {
						$out.=Discussion::bb_param_otag($otags, $eparam, $nxqtag, $table);
					}
					$out.=$rco[0];
					$in=$rco[1];
				} else if(isset($otags[$nxqtag])) {
					$out.=Discussion::bb_param_otag($otags, $eparam, $nxqtag, $table);
					$in=substr($in, strlen($nxtag)+2);
				} else {
					$out.=strtr(substr($in, 0, strlen($nxtag)+2), $table);
					$in=substr($in, strlen($nxtag)+2);
				}
			} else if(isset($otags[$nxtag])&&isset($ctags[$nxtag])) {
				$tags[]=$nxtag;
				$rco = Discussion::recursiveConverter(substr($in, strlen($nxtag)+2), $table, $otags, $ctags, $depth-1, $nxtag, $tags);
				unset($tags[count($tags)-1]);
				if($rco[2]) {
					$out.=strtr(substr($in, 0, $epos+1), $table);
				} else {
					$out.=$otags[$nxtag];
				}
				$out.=$rco[0];
				$in=$rco[1];
			} else if(isset($otags[$nxtag])) {
				$out.=$otags[$nxtag];
				$in=substr($in, strlen($nxtag)+2);
			} else if($nxtag[0]=='/') {
				$cxtag=substr($nxtag, 1);
				//print $cxtag;
				if($cxtag==$tag) {
					//print "G ";
					$out.=$ctags[$cxtag];
					$in=substr($in, strlen($nxtag)+2);
					break;
				} else if(array_search($cxtag, $tags)!==false) {
					//print "F ";
					$fail=true;
					break;
				} else {
					//print "0 ";
					$out.=strtr(substr($in, 0, strlen($nxtag)+2), $table);
					$in=substr($in, strlen($nxtag)+2);
				}
			} else  {
				$out.=strtr(substr($in, 0, strlen($nxtag)+2), $table);
				$in=substr($in, strlen($nxtag)+2);
			}
		}
		return array($out, $in, $fail);
	}
	private static function bb_param_otag($otags, $eparam, $nxqtag, $table) {
		$out="";

		if($otags[$nxqtag]['safe']=='strtr') {
			$out.=$otags[$nxqtag][0].strtr($eparam, $table).$otags[$nxqtag][1];
		} else if($otags[$nxqtag]['safe']=='slashes') {
			$out.=$otags[$nxqtag][0].addslashes($eparam).$otags[$nxqtag][1];
		} else if($otags[$nxqtag]['safe']=='url') {
			$out.=$otags[$nxqtag][0].rawurlencode($eparam).$otags[$nxqtag][1];
		} else if($otags[$nxqtag]['safe']=='drop') {
			$out.=$otags[$nxqtag][0].$otags[$nxqtag][1];
		} else if($otags[$nxqtag]['safe']=='none') {
			$out.=$otags[$nxqtag][0].$eparam.$otags[$nxqtag][1];
		} else {
			$out.=$otags[$nxqtag][0].strtr($eparam, $table).$otags[$nxqtag][1];
		}
		return $out;
	}
}
?>
