<?php
/**
* Just contains the definition for the class {@link Suggest}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Suggest
* @filesource
*/

/**
* The module that handles search suggestions.
* @package modules
* @subpackage Suggest
*/
class Suggest extends Module {

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		return 'Intranet Suggest';
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		global $I2_ARGS,$I2_ROOT;
		if(!isset($I2_ARGS[1]) || !isset($I2_ARGS[2])) {
			redirect();
		} else if($I2_ARGS[1] == 'searchsuggest') {
			if(strlen($I2_ARGS[2])>=3) {
				$arr = User::search_info($I2_ARGS[2]);
				if(count($arr)>10) {
					$arr=array_slice($arr,0,10);
				}
				foreach($arr as $ar) {
					echo "<a href=\"$I2_ROOT/studentdirectory/info/$ar->uid\">" . $ar->fullname."</a><br />";
				}
			} else {
				echo "";
			}
			Display::stop_display();
		} else {
			redirect();
		}
	}

	
	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return "Suggest";
	}
}

?>
