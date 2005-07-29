<?php
/**
* Just contains the definition for the class {@link News}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @version $Id: news.mod.php5,v 1.18 2005/07/29 01:41:58 adeason Exp $
* @package modules
* @subpackage News
* @filesource
*/

/**
* The module that keeps you hip and cool with the latest happenings all
* around TJ.
* @package modules
* @subpackage News
* @todo Mechanism for posting news
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
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_SQL;
		$res = $I2_SQL->query('SELECT title,text,authorID,posted FROM news ORDER BY posted DESC;');
		$this->newsdetails = $res->fetch_all_arrays(MYSQL_BOTH);

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
		}
		return array('News', 'Recent News Posts');
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		//$display->raw_display("This is today's news, in a pane.");
		$display->disp('news_pane.tpl',array('news_stories'=>$this->newsdetails));
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		if( $this->newsdetails === NULL ) {
			global $I2_SQL;
			$res = $I2_SQL->query('SELECT title FROM news ORDER BY posted DESC;')->fetch_all_arrays(MYSQL_ASSOC);
			$titles = array();
			foreach ($res as $row) {
				$titles[] = $row['title'];
			}
		}
		else {
			$titles=array();
			foreach($this->newsdetails as $news) {
				$titles[] = $news['title'];
			}
		}
		$this->summaries = $titles;
		$num = count($this->summaries);
		return 'News: '.$num.' post'.($num==1?'':'s').' to read';
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
		$display->disp('news_box.tpl',array('summaries'=>$this->summaries));
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return "News";
	}
}

?>
