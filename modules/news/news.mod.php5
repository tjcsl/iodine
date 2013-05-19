<?php
/**
* Just contains the definition for the class {@link News}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage News
* @filesource
*/

/**
* The module that keeps you hip and cool with the latest happenings all
* around TJ.
* @package modules
* @subpackage News
*/
class News extends Module {

	const PERM_POST = 'NEWS_POST';
	
	/**
	* The display object to use
	*/
	private $display;

	/**
	* Template for the specified action
	*/
	private $template;

	/**
	* Template arguments for the specified action
	*/
	private $template_args = [];

	/**
	* A 1-dimensional array of all the stories
	*/
	private $stories;

	/**
	* A 1-dimensional array containing all of the titles for all news posts.
	*/
	private $summaries = [];

	/**
	* Whether the current user is a news administrator.
	*/
	private $newsadmin;

	/**
	 * Whether the current user can post at all.
	 */
	private $maypost;
	
	private function set_news_admin() {	
		global $I2_USER;
		$this->newsadmin = $I2_USER->is_group_member('admin_news');
		if ($this->newsadmin) {
			d('This user is a news administrator - news alteration privileges have been granted.',7);
		}	

		d('setting maypost');
		$this->maypost = count(Group::get_user_groups($I2_USER, Permission::getPermission(News::PERM_POST))) > 0;
		d('done setting maypost');
	}
	
	/**
	* Turns out that the normal news view also works for mobile.
	*/
	function init_mobile() {
		return $this->init_pane();
	}

	/**
	* Turns out that the normal news view also works for mobile.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_mobile($disp) {
		return $this->display_pane($disp);
	}

	/**
	* Returning the command title
	*/
	function init_cli() {
		return "news";
	}

	/**
	* Handle the news command.
	*
	*/
	function display_cli() {
		global $I2_ARGS;
		$valid_commands = array("list","show","old","archived");
		if(!isset($I2_ARGS[2]) || !in_array(strtolower($I2_ARGS[2]),$valid_commands) ) {
			return "<div>Usage: news list<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;news show [news id]<br /><br />news is a command for reading Iodine news.<br /><br />Commands:<br />&nbsp;&nbsp;&nbsp;list - list all currently active news articles<br />&nbsp;&nbsp;&nbsp;show - print out the content of a news article<br />&nbsp;&nbsp;&nbsp;old - printa list of out old news<br />&nbsp;&nbsp;&nbsp;archived - print out a list of archived news<br /></div>\n";
		}
		switch (strtolower($I2_ARGS[2])) {
			case "list":
				$string= "<div>\n";
				if( $this->stories === NULL) {
					$this->stories = Newsitem::get_all_items(false);
				}
				foreach($this->stories as $story) {
					if ((!$story->has_been_read()) && $story->readable()) {
						$string.= "&nbsp;&nbsp;&nbsp;$story->nid - $story->title<br />\n";
					}
				}
				$string.="</div>\n";
				return $string;
			case "show":
				if( !isset($I2_ARGS[3]) ) {
					return "<div>ID of article to read not specified.</div>\n";
				}
				$item = new Newsitem($I2_ARGS[3]);
				return "<div>\n$item->nid - $item->title<br /><br />\n<div style='width:640px'>$item->text</div><br />";
			case "old":
				$string= "<div>\n";
				if( $this->stories === NULL) {
					$this->stories = Newsitem::get_all_items(true);
				}
				foreach($this->stories as $story) {
					if (($archive || !$story->has_been_read()) && $story->readable()) {
						$string.= "&nbsp;&nbsp;&nbsp;$story->nid - $story->title<br />\n";
					}
				}
				$string.="</div>\n";
				return $string;
			case "archived":
				$string= "<div>\n";
				if( $this->stories === NULL) {
					$this->stories = Newsitem::get_all_items(true);
				}
				foreach($this->stories as $story) {
					$string.= "&nbsp;&nbsp;&nbsp;$story->nid - $story->title<br />\n";
				}
				$string.="</div>\n";
				return $string;
			default:
				return "<div>Error: unrecognizable input</div>\n";
		}
	}

	private function print_story($story) {
		global $I2_API;
		$I2_API->startElement('post');
		$I2_API->writeElement('id',$story->nid);
		$I2_API->writeElement('title',$story->title);
		$I2_API->startElement('text');
		$I2_API->writeCData($story->text);
		$I2_API->endElement();
		$I2_API->startElement('text_strip');
		$I2_API->writeCData(strip_tags($story->text));
		$I2_API->endElement();
	}

	/**
	* Handle the api.
	*
	*/
	function api() {
		global $I2_ARGS;
		if(!isset($I2_ARGS[1])) {
			throw new I2Exception('Arguments not specified. Possible arguments are list, show.');
		}
		switch (strtolower($I2_ARGS[1])) {
			case "list":
				if( $this->stories === NULL) {
					$this->stories = Newsitem::get_all_items(false);
				}
				foreach($this->stories as $story) {
					if ((!$story->has_been_read()) && $story->readable()) {
						self::print_story($story);
					}
				}
				break;
			case "show":
				if(!isset($I2_ARGS[2])) {
					throw new I2Exception('ID of article to read not specified.');
				}
				$story = new Newsitem($I2_ARGS[2]);
				self::print_story($story);
				break;
			default:
				throw new I2Exception('Error: unrecognizable input');
		}
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_SQL,$I2_ARGS,$I2_USER,$I2_ROOT;

		if( ! isset($I2_ARGS[1]) ) {
			$I2_ARGS[1] = '';
		}
		
		if (!isSet($this->newsadmin)) {
			$this->set_news_admin();
		}

		$archive = false;

		switch($I2_ARGS[1]) {

			case 'add':
				$this->template = 'news_add.tpl';
				
				if( isset($_REQUEST['add_form'])) {
					$title = stripslashes($_REQUEST['add_title']);
					$text = stripslashes($_REQUEST['add_text']);
					$expire = $_REQUEST['add_expire'];
					$visible = isSet($_REQUEST['add_visible']) ? 1 : 0;
					$groups = Group::generate($_REQUEST['add_groups']);
					$public = isSet($_REQUEST['add_public']) ? 1 : 0;

					if(Newsitem::post_item($I2_USER, $title, $text, $groups, $expire, $visible, $public)) {
						$this->template_args['added'] = 1;
					}
					else {
						$this->template_args['added'] = 0;
					}

					return array('Post News', 'News article posted');
				}
				if($this->newsadmin) {
					// If they are a news admin, they can post to anything.
					$this->template_args['groups'] = Group::get_all_groups();
				}
				else {
					d('getting user groups with PERM_POST');
					$this->template_args['groups'] = Group::get_user_groups($I2_USER, Permission::getPermission(News::PERM_POST));
					d('done getting user groups with PERM_POST');
				}
				return array('Post News', 'Add a news article');

			case 'edit':
				$this->template = 'news_edit.tpl';
				if( !isset($I2_ARGS[2]) ) {
					throw new I2Exception('ID of article to edit not specified.');
				}

				try {
					$item = new Newsitem($I2_ARGS[2]);
				} catch(I2Exception $e) {
					throw new I2Exception("Specified article ID {$I2_ARGS[2]} is invalid.");
				}

				if( !$item->editable() ) {
					throw new I2Exception('You do not have permission to edit this article.');
				}

				if( isset($_REQUEST['edit_form']) ) {
					$title = stripslashes($_REQUEST['edit_title']);
					$text = stripslashes($_REQUEST['edit_text']);
					$expire = $_REQUEST['edit_expire'];
					$visible = isSet($_REQUEST['edit_visible']) ? 1 : 0;
					$groups = Group::generate($_REQUEST['add_groups']);
					$public = isSet($_REQUEST['edit_public']) ? 1 : 0;
					$item->edit($title, $text, $groups,$expire,$visible,$public);
					$item = new Newsitem($I2_ARGS[2], TRUE);
					$this->template_args['edited'] = 1;
				}

				if($this->newsadmin) {
					// If they are a news admin, they can post to anything.
					$this->template_args['groups'] = Group::get_all_groups();
				}
				else {
					$this->template_args['groups'] = Group::get_user_groups($I2_USER, Permission::getPermission(News::PERM_POST));
				}

				//$item->title = stripslashes($item->title);
				//$item->text = stripslashes($item->text);
				$item->text = htmlspecialchars_decode($item->text);
				$item->text = preg_replace('/<br\\s*?\/??>/i', "\n", $item->text);
				// To fix highlighting in vim, since it thinks we just closed the tag: <?php
				$this->template_args['newsitem'] = $item;
				return 'Edit News Post';
				
			case 'delete':
				$this->template = 'news_delete.tpl';
				if( !isset($I2_ARGS[2]) ) {
					throw new I2Exception('ID of article to delete not specified.');
				}
				
				try {
					$item = new Newsitem($I2_ARGS[2]);
				} catch(I2Exception $e) {
					throw new I2Exception("Specified article ID {$I2_ARGS[2]} is invalid.");
				}

				if( !$item->editable() ) {
					throw new I2Exception('You do not have permission to delete this article.');
				}

				if( isset($_REQUEST['delete_confirm']) ) {
					$item->delete();
					return 'News Post Deleted';
				}
				else {
					$this->template_args['newsitem'] = new Newsitem($I2_ARGS[2]);
					return array('Delete News Post', 'Confirm News Post Delete');
				}
				
			case 'archive':
				return self::display_news(true,'Old News Posts');
			case 'all':
				return self::display_news(true,'Archived News Posts', true);

			case 'show':
				$this->template = 'news_show.tpl';
				if( !isset($I2_ARGS[2]) ) {
					throw new I2Exception('ID of article to read not specified.');
				}
				$item = new Newsitem($I2_ARGS[2]);
				$this->template_args['story'] = $item;
				$notag_title = preg_replace("/<font(.*?)>/",'',$item->title);
				$notag_title = preg_replace("/<\/font>/",'',$notag_title);
				return "News: $notag_title";


			case 'read':
				$this->template = 'news_read.tpl';
				if( !isset($I2_ARGS[2]) ) {
					throw new I2Exception('ID of article to mark as read not specified.');
				}

				try {
					$item = new Newsitem($I2_ARGS[2]);
				} catch(I2Exception $e) {
					throw new I2Exception("Specified article ID {$I2_ARGS[2]} invalid.");
				}

				$item->mark_as_read();
		 		return self::display_news(false);

		 	case 'unread':
				$this->template = 'news_unread.tpl';
				if( !isset($I2_ARGS[2]) ) {
					throw new I2Exception('ID of article to mark as unread not specified.');
				}

				try {
					$item = new Newsitem($I2_ARGS[2]);
				} catch(I2Exception $e) {
					throw new I2Exception("Specified article ID {$I2_ARGS[2]} invalid.");
				}

				$item->mark_as_unread();
		 		return self::display_news(true,'Old News Posts');

			case 'request':
				$this->template = 'news_request.tpl';
				$usermail = $I2_USER->mail;
				if (is_array($usermail)) {
					$usermail = $usermail[0];
				}
				$this->template_args['usermail'] = $usermail;
				$this->template_args['iodinemail'] = i2config_get('sendto', 'intranet@tjhsst.edu', 'suggestion');
		
				if (!((isset($_REQUEST['submit_form']) && isset($_REQUEST['submit_title'])) && isset($_REQUEST['submit_box']))) {
					return 'News Request';
				}
				
				$mesg = 'Title: ' . $_REQUEST['submit_title'] . "\r\nRequested expiration date: " . $_REQUEST['submit_expdate'] . "\r\n\r\nText:\r\n" . $_REQUEST['submit_box'];
				if(isset($_REQUEST['notes_box'])&&!empty($_REQUEST['notes_box']))
					$mesg .= "\r\n\r\nNotes:\r\n".$_REQUEST['notes_box'];
				if ($mesg == "" || $mesg == " ") { //may need a whitespace regex
					return 'News Request';
				}
		
				$to = i2config_get('sendto', 'intranet@tjhsst.edu', 'suggestion');
				$subj = "News request from {$I2_USER->fullname}";
				$headers = "From: $usermail\r\n";
				$headers .= "Reply-To: $usermail\r\n";
				$headers .= "Return-Path: $to\r\n";
		
				$this->template_args['mailed'] = mail($to,$subj,$mesg,$headers);
				return 'News Request';
			case 'shade':
				$nid = $I2_ARGS[2];
				$closed = $I2_SQL->query('SELECT COUNT(*) FROM news_shaded_map WHERE uid=%d AND nid=%d;', $I2_USER->uid, $nid)->fetch_single_value();
				if($closed)
					$I2_SQL->query('DELETE FROM news_shaded_map WHERE uid=%d AND nid=%d;', $I2_USER->uid, $nid);
				else
					$I2_SQL->query('INSERT INTO news_shaded_map SET uid=%d, nid=%d;', $I2_USER->uid, $nid);
				// Redirect to the page the user was just viewing.
				redirect(str_replace($I2_ROOT,'',$_SERVER['HTTP_REFERER']));
			case 'like':
				$nid = $I2_ARGS[2];
				
				$liked = $I2_SQL->query("SELECT COUNT(*) FROM news_likes WHERE uid=%d AND nid=%d;", $I2_USER->uid, $nid)->fetch_single_value();
				if ($liked) {
					$I2_SQL->query("DELETE FROM news_likes WHERE uid=%d AND nid=%d;", $I2_USER->uid, $nid);
				} else {
					$I2_SQL->query("INSERT INTO news_likes SET uid=%d, nid=%d;", $I2_USER->uid, $nid);
				}

	   		default:
				return self::display_news(false);
		}
		//should not happen
		throw new I2Exception('Internal error: sanity check, reached end of init_pane in news.');
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		global $I2_ARGS;
		
		$this->template_args['newsadmin'] = $this->newsadmin;
		$this->template_args['maypost'] = $this->maypost;
		$display->disp($this->template, $this->template_args);
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		if( $this->stories === NULL ) {
			global $I2_SQL;
			$this->stories = Newsitem::get_all_items(false);
		}
		foreach($this->stories as $story) {
			if (!$story->has_been_read() && $story->readable()) {
				$this->summaries[] = array('title' => $story->title, 'id' => $story->id);
			}
		}
		$num = count($this->summaries);
		if (!isSet($this->newsadmin)) {
			$this->set_news_admin();
		}
		return 'News: '.$num.' post'.($num==1?'':'s').' to read';
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
		$display->disp('news_box.tpl',array('summaries'=>$this->summaries,'newsadmin'=>$this->newsadmin));
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return 'News';
	}

	function display_news($archive = false,$title='Recent News Posts',$expired = false) {	
		$this->template = 'news_pane.tpl';
		$I2_ARGS[1] = '';

		$this->template_args['stories'] = [];

		if( $this->stories === NULL) {
			$this->stories = Newsitem::get_all_items($expired);
		}

		foreach($this->stories as $story) {
			if (($archive || !$story->has_been_read()) && $story->readable()) {
				//$story->text = stripslashes($story->text);	  
				$story->title = stripslashes($story->title);
				$this->template_args['stories'][] = $story;
			}
		}
		$this->template_args['weatherstatus']=$this->get_emerg_message();
		return array('News',$title);
	}

	/**
	* Get the emergency messages from FCPS.
	* This is stuff like weather-related cancellations.
	*/
	function get_emerg_message() {
		global $I2_ROOT,$I2_QUERY;
		$cachefile = i2config_get('cache_dir','/var/cache/iodine/','core') . 'emerg.cache';
		if(!file_exists($cachefile) || !($contents = file_get_contents($cachefile)) || (time() - filemtime($cachefile)>600) || isset($I2_QUERY['get_new_message'])) { //Don't let the cache get older than an hour.
			$contents = $this->get_new_message();
			$this->store_emerg_message($cachefile,$contents);
		}
		return $contents;
	}

	private function store_emerg_message($cachefile,$string) {
		$fh = fopen($cachefile,'w');
		fwrite($fh,$string);
		fclose($fh);
	}

	private function get_new_message() {
		global $I2_FS_ROOT;
		// HTTPS because otheriwse it gets cached by the proxy, which is bad.
		// It endangers kittens because they don't get information quickly enough.
		/*$url = i2config_get('emerg_url','https://www.fcps.edu/content/emergencyContent.html','emergency'); // FCPS Emergency announcement _really_ short summary page.
		if( $str = $this->curl_file_get_contents($url) ) { // Returns false if can't get anything.
			$starter= i2config_get('emerg_starter','<h3 >','emergency');
			$ender  = i2config_get('emerg_ender','</h3>','emergency');
			if(!stristr($str,$starter) || !stristr($str,$ender))
				return "<!-- ERROR: FCPS's page doesn't parse correctly, because they changed the format. -->";
			$startpos = strpos($str,$starter)+strlen($starter);
			$endpos = strpos($str,$ender);
			$length = $endpos-$startpos;
			$str=substr($str,$startpos,$length);
			if(trim($str)==i2config_get('emerg_default','There are no emergency announcements at this time.','emergency'))
				return "<!-- No emergency announements -->";
			$str=preg_replace("/<!--.*-->/s","",$str); // Remove all commented stuff.
			$str=str_replace("<p","<p style='color: red' ",$str); // They use <p> tags for their formatting. We hijack that to do this!
			$str=str_replace("href=\"","href=\"http://www.fcps.edu/",$str); // Their links are relative, so we have to do this. A better way to reliably do this would be good.
			if(stristr($str,"proxy.tjhsst.edu")) {
				return "<!-- ERROR: FCPS can't keep their info page up. -->";
			} else {
				return $str;
			}
		} else {
			return "<!-- ERROR: We can't reach FCPS' page. -->"; // If fcps isn't up, don't bother showing anything.
		}*/
		d('Checking FCPS emerg msgs and including simple_html_dom', 9);
		require_once $I2_FS_ROOT.'lib/simple_html_dom.php';
		$url = "http://www.fcps.edu/news/emerg.shtml";
		try {
			if($fgetc = $this->curl_file_get_contents($url)) {
				$html = str_get_html($fgetc);
				//$false_str = "There are no emergency announcements at this time";
				// The string used for there being no emergency messages
				$false_str = "There are no emergency messages at this time"; 
				$con = $html->find('div[id=mainContent]');
				$snowdayd = $con[0]->innertext;
				$snowday = (strpos($snowdayd, $false_str)!==false);
				// This is the message that ends the emergency text;
				// it is currently the text of the header below
				$end_str = "Go to The Source";
				$d = explode($end_str, $con[0]->plaintext);
				$dn = explode($false_str, $d[0]);
				if(!$snowday) {
					d("Emergency info: no snow day");
					return "<!-- !snowday, ".$false_str." -->";
				}
				if(!empty($d[0]) && !empty($dn[0])) {
					$ddate = explode("--", $dn[0]);
					// FCPS doesn't really like to delete old emerg messages
					// This was the last emerg message of 2013
					if(trim($ddate[0]) == "Monday, March 25") {
						$einfo= "<!-- Got old emergency message, not showing -->";
					} else if(stristr($dn[0], $false_str)) {
						$einfo= "<!-- snowday,".$false_str." -->";
					} else {
						$einfo= "<!-- ".print_r($d,1).print_r($dn,1)." -->".
								"<p style='color: red'>".trim($dn[0])."</p>";
					}
					//echo "<a href='{$url}'>Click here for more information</a>";
				} else {
					//echo "Unable to fetch information on emergency announcements. <a href='{$url}'>Click here to check manually.</a>";
					$einfo = "<!-- Unable to fetch emerg announcements -->";
				}
				d("Emergency info:".$einfo, 5);
				return $einfo;
			} else {
				d("Emergency info: Could not fetch FCPS' site",5);
				return "Emergency info: Could not fetch FCPS' site";
			}
		} catch(Exception $e) {
			d("Emergency info: Error parsing FCPS' emergency site",5);
			return "<!-- Error parsing FCPS' emergency site -->";
		}

	}
	private function curl_file_get_contents($URL)
	{
		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_URL, $URL);
		$contents = curl_exec($c);
		curl_close($c);
		
		if ($contents) return $contents;
		else return FALSE;
	}
}

?>
