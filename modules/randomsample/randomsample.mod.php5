<?php
/**
* Just contains the definition for the {@link Module} {@link Randomsample}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005-2006 The Intranet 2 Development Team
* @package modules
* @subpackage Info
* @filesource
*/

/**
* A module to obtain a random sampling of students.
* @package modules
* @subpackage Info
*/
class Randomsample implements Module {

		  private $sample;
		  private $attrs;

	private function take_sample($filter,$size,$attrs) {
		global $I2_LDAP,$I2_LOG;
		$samp = array();
		$mail = FALSE;
		$username = FALSE;
		if (in_array('iodineUid',$attrs)) {
				  $username = TRUE;
		}
		if (in_array('mail',$attrs)) {
				  $mail = TRUE;
				  if (!$username) {
							 $attrs[] = 'iodineUid';
				  }
		}
		$res = $I2_LDAP->search('ou=people',$filter,$attrs);
		$pop = array();
		$ct = 0;
		$rows = $res->num_rows();
		while ($ct < $rows) {
				  /*
				  ** Mail gets special treatment
				  */
				  $row = $res->fetch_array(Result::ASSOC);
				  if ($mail && !isSet($row['mail'])) {
						 $row['mail'] = $row['iodineUid'].'@tjhsst.edu';
				  }
				  if ($mail && !$username) {
							 unSet($row['iodineUid']);
				  }
				  $pop[] = $row;
				  $ct++;
		}
		$popsize = count($pop);
		$numselected = 0;
		if ($size > $popsize) {
			return -1;
		}
		$selected = array();
		while ($numselected < $size) {
				  $choice = rand(0,$popsize-$numselected-1);
				  if (isSet($selected[$choice])) {
							 continue;
				  }
				 
				  $samp[] = $pop[$choice];
				  $selected[$choice] = 1;
				  $numselected++;
		}
		return $samp;
	}

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

	public function init_pane() {
		global $I2_ARGS;
		if (!isSet($I2_ARGS[1]) || $I2_ARGS[1] != 'results') {
			return 'Take a Random Sample';
		} else {
			$this->attrs = explode(',',$_REQUEST['attrs']);
			$this->sample = $this->take_sample($_REQUEST['filter'],
					  $_REQUEST['size'],$this->attrs);
			$_SESSION['random_sample'] = $this->sample;
			return 'Sample Results';
		}
	}

	public function display_pane($disp) {
		$args = array();
		if (isSet($this->sample)) {
				  $args['sample'] = $this->sample;
				  $args['cols'] = $this->attrs;
		}
		$disp->disp('randomsample_pane.tpl',$args);
	}

	public function init_box() {
		return FALSE;
	}

	public function display_box($disp) {
	}

	public function get_name() {
		return 'Random Sample';
	}

}
?>
