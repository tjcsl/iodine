<?php
/**
* Just contains the definition for the interface {@link Module}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005-2006 The Intranet 2 Development Team
* @package core
* @subpackage Module
* @filesource
*/

/**
* The API for all Intranet2 modules to extend.
* @package core
* @subpackage Module
*/
class Prom implements Module {

		  private $template = 'prom_pane.tpl';
		  private $template_args = array();

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
	* Displays all of a module's ibox content.
	*
	* @param Display $disp The Display object to use for output.
	* @abstract
	*/
	function display_box($disp) {
	}
	
	/**
	* Displays all of a module's main content.
	*
	* @param Display $disp The Display object to use for output.
	* @abstract
	*/
	function display_pane($disp) {
		$disp->disp($this->template,$this->template_args);
	}
	
	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	* @abstract
	*/
	function get_name() {
		return 'Prom Registration';
	}

	/**
	* Performs all initialization necessary for this module to be 
	* displayed in an ibox.
	*
	* @returns string The title of the box if it is to be displayed,
	*                 otherwise FALSE if this module doesn't have an
	*                 intrabox.
	* @abstract
	*/
	function init_box() {
		return FALSE;
	}

	/**
	* Performs all initialization necessary for this module to be
	* displayed as the main page.
	*
	* @returns mixed Either a string, which will be the title for both the
	*                main pane and for part of the page title, or an array
	*                of two strings: the first is part of the page title,
	*                and the second is the title of the content pane. To
	*                specify no titles, return an empty array. To specify
	*                that this module has no main content pane (and will
	*                show an error if someone tries to access it as such),
	*                return FALSE.
	* @abstract
	*/
	function init_pane() {
		global $I2_USER,$I2_SQL, $I2_ARGS;
		if (!isset($I2_ARGS[1]))
			$I2_ARGS[1] = 'home';
		if (count($_POST) > 0) {
			$ticket = 0;
			if ($_POST['attending'] == 0) {
				$I2_SQL->query('REPLACE INTO prom SET uid=%d,going=0',$I2_USER->uid);
				$ticket = 0;
			} else if ($_POST['nosenior'] == 1) {
				$I2_SQL->query('REPLACE INTO prom SET uid=%d,going=1,datefrom=\'TJ\','.
					'datename=%s,dategrade=%d',$I2_USER->uid,$_POST['datename'],$_POST['dategrade']);
				$ticket = 2;
			} else if ($_POST['notj'] == 1) {
				$I2_SQL->query('REPLACE INTO prom SET uid=%d,going=1,datefrom=\'FCPS\',datename=%s,'.
					'dategrade=%d,dateother=%s',$I2_USER->uid,$_POST['date2name'],
					$_POST['date2grade'],$_POST['date2school']);
				$ticket = 3;
			} else if ($_POST['nosch'] == 1) {
				$I2_SQL->query('REPLACE INTO prom SET uid=%d,going=1,datefrom=\'other\',datename=%s,'.
					'dateother=%s',$I2_USER->uid,$_POST['date3name'],$_POST['date3desc']);
				$ticket = 3;
			} else {
				$I2_SQL->query('REPLACE INTO prom SET uid=%d,going=1,datefrom=NULL',$I2_USER->uid);
				$ticket = 1;
			}
			$this->template_args['ticket'] = $ticket;
			$this->template = 'prom_thanx.tpl';
		} else if ($I2_ARGS[1] == 'admin') {
			if (!$I2_USER->is_group_member('admin_prom')) {
				throw new I2Exception('You are not authorized to view this page!');
			}
			$this->template = 'prom_admin.tpl';
			$tout = $I2_SQL->query('SELECT * FROM prom ORDER BY going, datefrom')->fetch_all_arrays();
			$notgoing = array();
			$nodate = array();
			$tjdate = array();
			$fcpsdate = array();
			$fardate = array();
			foreach ($tout as $someone) {
				$u = new User($someone['uid']);
				$name = $u->name;
				if (!$someone['going']) {
					$notgoing[] = $name;
				} else if ($someone['datefrom'] == 'TJ') {
					$tjdate[] = array('name' => $name, 'date' => $someone['datename'], 'grade' => $someone['dategrade']); 
				} else if ($someone['datefrom'] == 'FCPS') {
					$fcpsdate[] = array('name' => $name, 'date' => $someone['datename'], 'grade' => $someone['dategrade'], 'school' => $someone['dateother']); 
				} else if ($someone['datefrom'] == 'other') {
					$fardate[] = array('name' => $name, 'date' => $someone['datename'], 'desc' => $someone['dateother']); 
				} else {
					$nodate[] = $name;
				}
			}
			$this->template_args['notgoing'] = $notgoing;
			$this->template_args['nodate'] = $nodate;
			$this->template_args['tjdate'] = $tjdate;
			$this->template_args['fcpsdate'] = $fcpsdate;
			$this->template_args['fardate'] = $fardate;
			return 'Prom Admin';
		}
		if ($I2_USER->grade != 12 && !$I2_USER->is_group_member('admin_prom')) {
			throw new I2Exception('Only seniors may register for the SENIOR prom!');
		}
		return 'Prom Registration';
	}

}
?>
