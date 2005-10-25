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
	
	/**
	* The display object to use
	*/
	private $display;

	/**
	* A 2-dimensional array containing all of the information for news posts.
	*/
	private $newsdetails = NULL; 

	/**
	* A 1-dimensional array containing all of the titles for all news posts.
	*/
	private $summaries;

	/**
	* Whether the current user is a news administrator.
	*/
	private $newsadmin;
	
	private function set_news_admin() {	
		global $I2_USER;
		$this->newsadmin = $I2_USER->is_group_member('admin_news');
		if ($this->newsadmin) {
			d('This user is a news administrator - news alteration privileges have been granted.');
		}else if($I2_USER->is_group_member('admin_all')){
			d('This user is an uber-administrator - news alteration privileges have been granted.');
			$this->newsadmin = true;
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
		
		switch($I2_ARGS[1]) {

			case 'add':
				
				if( isset($_REQUEST['add_form']) && $this->newsadmin) {
					$I2_SQL->query('INSERT INTO news ( authorID, title, text, posted ) VALUES ( %d, %s, %s, CURRENT_TIMESTAMP );', $I2_USER->uid, $_REQUEST['add_title'], $_REQUEST['add_text']);
					$this->newsdetails = 1;
					return array('Post News', 'News article posted');
				}
				return array('Post News', 'Add a news article');

			case 'edit':
				if( !isset($I2_ARGS[2]) ) {
					throw new I2Exception('ID of article to delete not specified.');
				}
				
				$res = $I2_SQL->query('SELECT title,text,authorID FROM news WHERE id=%d;', $I2_ARGS[2])->fetch_array();
				
				if( $res === FALSE ) {
					throw new I2Exception('Specified article ID does not exist.');
				}
				if( !$this->newsadmin ) {
					throw new I2Exception('You do not have permission to edit this article.');
				}

				if( isset($_REQUEST['edit_form']) ) {
					$I2_SQL->query('UPDATE news SET title=%s, text=%s WHERE id=%d;', $_REQUEST['edit_title'], $_REQUEST['edit_text'], $I2_ARGS[2]);
					$res = $I2_SQL->query('SELECT title,text,authorID FROM news WHERE id=%d;', $I2_ARGS[2])->fetch_array();
					$res['edited'] = 1;
				}

				$this->newsdetails = $res;
				return 'Edit News Post';
				
			case 'delete':
				if( !isset($I2_ARGS[2]) ) {
					throw new I2Exception('ID of article to delete not specified.');
				}
				if (!$this->newsadmin) {
					throw new I2Exception('You do not have permission to delete this article!');
				}
				$res = $I2_SQL->query('SELECT title,text,authorID FROM news WHERE id=%d;', $I2_ARGS[2])->fetch_array();
				
				if( $res === FALSE ) {
					throw new I2Exception('Specified article ID does not exist.');
				}

				if( isset($_REQUEST['delete_confirm']) ) {
					$I2_SQL->query('DELETE FROM news WHERE id=%d;', $I2_ARGS[2]);
					return 'News Post Deleted';
				}
				
				$this->newsdetails = $res;
				return array('Delete News Post', 'Confirm News Post Delete');
				
			default:
				$I2_ARGS[1] = '';
				
				$this->newsdetails = $I2_SQL->query('SELECT id,title,text,authorID,posted FROM news ORDER BY posted DESC;')->fetch_all_arrays(MYSQL_ASSOC);
				$this->summaries = &$this->newsdetails;
		
				$authors = array();
				foreach( $this->newsdetails as $i=>$story ) {
					if( $story['authorID'] ) {
						if( isset($authors[$story['authorID']]) ) {
							$this->newsdetails[$i]['author'] = $authors[$story['authorID']];
						}
						else {
							$tmpuser = new User($story['authorID']);
							$this->newsdetails[$i]['author'] = $tmpuser->fname .' '.$tmpuser->lname;
							$authors[$story['authorID']] = $this->newsdetails[$i]['author'];
						}
					}	
					//A story is editable if this person wrote it or if they're a newsadmin
					$this->newsdetails[$i]['editable'] = ($story['authorID'] == $I2_USER->uid || $this->newsadmin );
					//FIXME: eliminate this broken hack - make the SQL query above more extensive.
					$this->newsdetails[$i]['read'] = FALSE;
				}
				return array('News', 'Recent News Posts');
		}
		//should not happen
		throw new I2Exception('Internal error: sanity check, reached end of init_pane in news.');
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		global $I2_ARGS;
		
		$display->disp('news_'.($I2_ARGS[1]?$I2_ARGS[1]:'pane').'.tpl',array('news_stories'=>$this->newsdetails,'newsadmin'=>$this->newsadmin));
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		if( $this->summaries === NULL ) {
			global $I2_SQL;
			$this->summaries = $I2_SQL->query('SELECT title FROM news ORDER BY posted DESC;')->fetch_all_arrays(MYSQL_ASSOC);
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
		return "News";
	}
}

?>
