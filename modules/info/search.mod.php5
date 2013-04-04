<?php
/**
* Provides a modular search-info plug-in which may be used in other components.
* @author The Intranet 2 Development Team <intranet2@thsst.edu>
* @copyright 2006 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage info
* @filesource
*/

/**
* Provides a modular search-info plug-in which may be used in other components. 
* @package modules
* @subpackage info
*/
class Search implements Module {

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

	public function init_box() {
		return FALSE;
	}

	public function init_pane() {
			  global $I2_ARGS;
			  if (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'results') {
						 
						 if (!isSet($_REQUEST['name']) || $_REQUEST['name'] == '') {
									$name = '*';
						 } else {
									$name = $_REQUEST['name'];
						 }

						 if (!isSet($_REQUEST['graduationYear'])) {
									$gradyear = NULL;
						 } else {
									$gradyear = $_REQUEST['graduationYear'];
						 }
						
						 $res = User::search_info($name,$gradyear);
						 $_SESSION['search_results'] = $res;
			  } elseif (isSet($I2_ARGS[1]) && $I2_ARGS[1] == 'clear') {
						 self::clear_results();
			  }
			  if (isSet($I2_ARGS[2])) {
				  redirect(implode('/',array_slice($I2_ARGS,2)));
			  }
			  return 'Advanced Search';
	}

	public static function get_results() {
		if (!isSet($_SESSION['search_results'])) {
			return FALSE;
		}
		return $_SESSION['search_results'];
	}

	public static function clear_results() {
				unset($_SESSION['search_results']);
	}

	public function display_box($disp) {
	}

	public function display_pane($disp) {
		if (isSet($_SESSION['search_results'])) {
			$disp->smarty_assign('info',$_SESSION['search_results']);
			$disp->smarty_assign('numresults',count($_SESSION['search_results']));
			$disp->smarty_assign('results_destination','studentdirectory/info/');
			$disp->smarty_assign('return_destination','search');
			$disp->disp('search_results_pane.tpl');
		} else {
			$disp->smarty_assign('action_name','Search');
			$disp->smarty_assign('search_destination','search/results/');
			$disp->smarty_assign('first_year',User::get_gradyear(12));
			$disp->disp('search_pane.tpl');
		}
	}

	public function get_name() {
			  return 'Search';
	}

}
?>
