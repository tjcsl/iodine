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
class News implements Module {

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
	private $template_args = array();

	/**
	* A 1-dimensional array of all the stories
	*/
	private $stories;

	/**
	* A 1-dimensional array containing all of the titles for all news posts.
	*/
	private $summaries = array();

	/**
	* Whether the current user is a news administrator.
	*/
	private $newsadmin;

	/**
	 * Whether the current user can post at all.
	 */
	private $maypost;
	
	private static $translate = TRUE;
	private function set_news_admin() {	
		global $I2_USER;
		$this->newsadmin = $I2_USER->is_group_member('admin_news');
		if ($this->newsadmin) {
			d('This user is a news administrator - news alteration privileges have been granted.',7);
		}	

		d('setting maypost');
		$this->maypost = count(Group::get_user_groups($I2_USER, new Permission(News::PERM_POST))) > 0;
		d('done setting maypost');
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
					$title = $_REQUEST['add_title'];
					$text = $_REQUEST['add_text'];
					$expire = $_REQUEST['add_expire'];
					$groups = Group::generate($_REQUEST['add_groups']);

					if(Newsitem::post_item($I2_USER, $title, $text, $groups, $expire)) {
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
					$this->template_args['groups'] = Group::get_user_groups($I2_USER, new Permission(News::PERM_POST));
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
					$title = $_REQUEST['edit_title'];
					$text = $_REQUEST['edit_text'];
					$expire = $_REQUEST['edit_expire'];
					$groups = Group::generate($_REQUEST['add_groups']);
					$item->edit($title, $text, $groups,$expire);
					$this->template_args['edited'] = 1;
				}

				if($this->newsadmin) {
					// If they are a news admin, they can post to anything.
					$this->template_args['groups'] = Group::get_all_groups();
				}
				else {
					$this->template_args['groups'] = Group::get_user_groups($I2_USER, new Permission(News::PERM_POST));
				}

				$item->title = stripslashes($item->title);
				//$item->text = stripslashes($item->text);
				$item->text = htmlspecialchars_decode($item->text);
				$item->text = preg_replace('/<br\\s*?\/??>/i', "\n", $item->text);
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
				return "News: $item->title";


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

			case 'shade':
				$nid = $I2_ARGS[2];
				$closed = $I2_SQL->query('SELECT COUNT(*) FROM news_shaded_map WHERE uid=%d AND nid=%d;', $I2_USER->uid, $nid)->fetch_single_value();
				if($closed)
					$I2_SQL->query('DELETE FROM news_shaded_map WHERE uid=%d AND nid=%d;', $I2_USER->uid, $nid);
				else
					$I2_SQL->query('INSERT INTO news_shaded_map SET uid=%d, nid=%d;', $I2_USER->uid, $nid);
				// Redirect to the page the user was just viewing.
				redirect(str_replace($I2_ROOT,'',$_SERVER['HTTP_REFERER']));

	   		case 'translate':
				self::$translate = FALSE;
				return self::display_news(false);
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
	public static function igpayatinlay($text) {
		$string = stripslashes(trim($text));
		$string = ereg_replace("\r\n|\n|\r", " ", $string);
		$pig_latin_string = "";
		$disallowed_punctuation = '& @ # $ % ^ * ( ) _ - + = { } [ ] | < > / ~ ` " \ \'';
		$disallowed_string = split(" ", $disallowed_punctuation);
		foreach ($disallowed_string as $disallowed_value) {
			if (strchr($string, $disallowed_value)) {
				$string = (str_replace($disallowed_value, "", $string));
			}
		}
		$string = split(" ", $string);
		foreach ($string as $value) {
			$word1 = substr_replace($value, "", 0, 1);
			$word2 = substr_replace($value, "", 1);
			if (is_numeric($value)) {
				$translation = $value;
			} else {
				$translation = (strtolower($word1.$word2."ay"));
			}
			if (eregi("[aeiou]", $word2)) {
				$translation = (strtolower($value."yay"));
			}
			if (ereg("[[:upper:]]", $word2)) {
				$translation = ucfirst($translation);
			}
			$allowed_punctuation = ". ? ! , ; :";
			$string2 = split(" ", $allowed_punctuation);
			foreach ($string2 as $value2) {
				if (strchr($translation, $value2)) {
					$translation = (str_replace($value2, "", $translation)).$value2;
				}
			}
			$pig_latin_string .= $translation." ";
		}
		return (trim($pig_latin_string));		
	}
	public static function flip($string) {
		$flip_table = array(
			'a' => '&#0592',
			'b' => 'q',
			'c' => '&#0596', //open o -- from pne
			'd' => 'p',
			'e' => '&#0477',
			'f' => '&#0607', //from pne
			'g' => '&#0387',
			'h' => '&#0613',
			'i' => '&#0305', //from pne
			'j' => '&#0638',
			'k' => '&#0670',
			'm' => '&#0623',
			'n' => 'u',
			'p' => 'd',
			'q' => 'b',
			'r' => '&#0633',
			't' => '&#0647',
			'u' => 'n',
			'v' => '&#0652',
			'w' => '&#0653',
			'y' => '&#0654',
			'.' => '&#0729',
			'[' => ']',
			'(' => ')',
			')' => '(',
			'{' => '}',
			'}' => '{',
			'?' => '&#0191', //from pne
			'!' => '&#0161',
			"\'" => ',',
			"/" => "\\",
			"\\" => "/",
			'<' => '>',
			'>' => '<',
			'_' => '&#8254',
			';' => '&#1563',
			'\r' => '\n');
		$string = strrev(strtolower($string));
		$newstring = "";
		for($k=0; $k<strlen($string); $k++) {
			if(in_array($string[$k], array_keys($flip_table))) {
				$newstring .= $flip_table[$string[$k]];
			} else {
				$newstring .= $string[$k];
			}
		}	
		return $newstring;
	}
	function display_news($archive = false,$title='Recent News Posts',$expired = false) {	
		$this->template = 'news_pane.tpl';
		$I2_ARGS[1] = '';

		$this->template_args['stories'] = array();

		if( $this->stories === NULL) {
			$this->stories = Newsitem::get_all_items($expired);
		}

		foreach($this->stories as $story) {
			if (($archive || !$story->has_been_read()) && $story->readable()) {
				if(self::$translate) {
					$story->text = self::igpayatinlay($story->text);
					$story->title = self::flip($story->title);
				} else {
					//$story->text = stripslashes($story->text);	  
					$story->title = stripslashes($story->title);
				}
				$this->template_args['stories'][] = $story;
			}
		}
		return array('News',$title);
	}
}

?>
