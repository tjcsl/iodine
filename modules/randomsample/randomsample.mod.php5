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
class Randomsample extends Module {

		  private $sample;
		  private $attrs;

	private function take_sample($filter,$size,$attrs) {
		global $I2_LDAP,$I2_LOG;
		$samp = [];
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
		$pop = [];
		$ct = 0;
		$rows = $res->num_rows();
		while ($ct < $rows) {
				  /*
				  ** Mail gets special treatment
				  */
				  $row = $res->fetch_array(Result::ASSOC);
				  if ($mail && !isset($row['mail'])) {
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
		$selected = [];
		while ($numselected < $size) {
				  $choice = rand(0,$popsize-$numselected-1);
				  if (isset($selected[$choice])) {
							 continue;
				  }
				 
				  $samp[] = $pop[$choice];
				  $selected[$choice] = 1;
				  $numselected++;
		}
		return $samp;
	}

	public function init_pane() {
		global $I2_ARGS;
		if (!isset($I2_ARGS[1]) || $I2_ARGS[1] != 'results') {
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
		$args = [];
		if (isset($this->sample)) {
				  $args['sample'] = $this->sample;
				  $args['cols'] = $this->attrs;
		}
		$disp->disp('randomsample_pane.tpl',$args);
	}

	public function get_name() {
		return 'Random Sample';
	}

}
?>
