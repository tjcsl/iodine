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
	
	private function set_news_admin() {	
		global $I2_USER;
		$this->newsadmin = $I2_USER->is_group_member('admin_news');
		if ($this->newsadmin) {
			d('This user is a news administrator - news alteration privileges have been granted.',7);
		}	
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_SQL,$I2_ARGS,$I2_USER;

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
					$groups = Group::generate($_REQUEST['add_groups']);

					if(Newsitem::post_item($I2_USER, $title, $text, $groups)) {
						$this->template_args['added'] = 1;
					}
					else {
						$this->template_args['added'] = 0;
					}

					return array('Post News', 'News article posted');
				}
				if($this->newsadmin) {
					// If they are a news admin, they can post to anything.
					$this->template_args['groups'] = array_merge(Group::get_all_groups(), Group::get_special_groups());
				}
				else {
					$this->template_args['groups'] = Group::get_user_groups($I2_USER,FALSE,News::PERM_POST);
				}
				return array('Post News', 'Add a news article');

			case 'edit':
				$this->template = 'news_edit.tpl';
				if( !isset($I2_ARGS[2]) ) {
					throw new I2Exception('ID of article to edit not specified.');
				}
				
				$item = new Newsitem($I2_ARGS[2]);
				if( !$item->editable() ) {
					throw new I2Exception('You do not have permission to edit this article.');
				}

				if( isset($_REQUEST['edit_form']) ) {
					$title = $_REQUEST['edit_title'];
					$text = $_REQUEST['edit_text'];
					$groups = Group::generate($_REQUEST['add_groups']);
					$item->edit($title, $text, $groups);
					$this->template_args['edited'] = 1;
				}

				if($this->newsadmin) {
					// If they are a news admin, they can post to anything.
					$this->template_args['groups'] = array_merge(Group::get_all_groups(), Group::get_special_groups());
				}
				else {
					$this->template_args['groups'] = Group::get_user_groups($I2_USER,FALSE,News::PERM_POST);
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
				if (!$this->newsadmin) {
					throw new I2Exception('You do not have permission to delete this article!');
				}
				
				try {
					$item = new Newsitem($I2_ARGS[2]);
				} catch(I2Exception $e) {
					throw new I2Exception("Specified article ID {$I2_ARGS[2]} is invalid.");
				}

				if( isset($_REQUEST['delete_confirm']) ) {
					$item->delete();
					return 'News Post Deleted';
				}
				else {
					$this->template_args['newsitem'] = new Newsitem($I2_ARGS[2]);
					return array('Delete News Post', 'Confirm News Post Delete');
				}
				
			case 'archive';
				return self::display_news(true,'Old News Posts');

			case 'show':
					$this->template = 'news_show.tpl';
					if( !isset($I2_ARGS[2]) ) {
						throw new I2Exception('ID of article to read not specified.');
					}
					$this->template_args['story'] = new Newsitem($I2_ARGS[2]);
					return array(TRUE,$this->template_args['story']->title);


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
		$display->disp($this->template, $this->template_args);
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		if( $this->stories === NULL ) {
			global $I2_SQL;
			$this->stories = Newsitem::get_all_items();
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
	function display_news($archive = false,$title='Recent News Posts') {	
		$this->template = 'news_pane.tpl';
		$I2_ARGS[1] = '';

		$this->template_args['stories'] = array();

		if( $this->stories === NULL) {
			$this->stories = Newsitem::get_all_items();
		}

		foreach($this->stories as $story) {
			if (($archive || !$story->has_been_read()) && $story->readable()) {
				//$story->text = stripslashes($story->text);	  
				$story->title = stripslashes($story->title);
				$this->template_args['stories'][] = $story;
			}
		}
		return array('News',$title);
	}
}

?>
