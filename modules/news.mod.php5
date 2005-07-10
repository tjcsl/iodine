<?php
/**
* Just contains the definition for the class {@link News}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @version 1.0
* @since 1.0
* @package modules
* @subpackage News
* @filesource
*/

/**
* The module that keeps you hip and cool with the latest happenings all
* around TJ.
* @package modules
* @subpackage News
* @todo Still needs a bit of touchup, with design and stuff, I think
*/
class News implements Module {
	
	private $display;
	private $newsdetails; 
	private $summaries;
	
	/**
	* The News class constructor.
	*/
	function __construct() {
	}
	
	function init_pane(Token $token) {
		global $I2_SQL;
		$res = $I2_SQL->query($token, 'SELECT title,text,author,authorID,authortype,posted FROM news;');
//		$res = $I2_SQL->select($token,'news_stories',array('title','text','author','authorID','authortype','posted'));
		$this->newsdetails = $res->fetch_all_arrays(MYSQL_BOTH);
		return TRUE;
	}
	
	function display_pane($display) {
		//$display->raw_display("This is today's news, in a pane.");
		$display->disp('newspane.tpl',array('news_stories'=>$this->newsdetails));
	}
	
	function init_box(Token $token) {
		global $I2_SQL;
		$res = $I2_SQL->query($token, 'SELECT title FROM news;');
//		$res = $I2_SQL->select($token,'news_stories',array('title'));
		$this->summaries = $res->fetch_col('title');
		return TRUE;
	}

	function display_box($display) {
		$display->raw_display("This is today's news, in a box.");
		$display->disp('newsbox.tpl',array('summaries'=>$this->summaries));
	}

	function get_name() {
		return "News";
	}
}

?>
