<?php
/**
* Just contains the definition for the {@link Module} {@link ManageIbox}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package core
* @subpackage Display
* @filesource
*/

/**
* The {@link Module} to manage Intraboxen.
* @package core
* @subpackage Display
*/
class ManageIbox implements Module {

	private $tpl = NULL;

	/**
	* Unused; required to implement {@link Module}
	*
	* @param Display $disp The Display object to use for output.
	*/
	public function display_box($disp) {
	}
	
	/**
	* Displays all of a module's main content.
	*
	* @param Display $disp The Display object to use for output.
	*/
	public function display_pane($disp) {
	}
	
	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	*/
	public function get_name() {
		return 'ManageIbox';
	}

	/**
	* Unused; required to implement {@link Module}
	*
	* @returns string The title of the box if it is to be displayed,
	*                 otherwise FALSE if this module doesn't have an
	*                 intrabox.
	*/
	public function init_box() {
		return FALSE;
	}

	/**
	* Performs all initialization necessary for this module to be
	* displayed as the main page.
	*
	* I2_ARGS accepted:
	*	I2_ARGS[1] = subcommand, like 'move' or 'delete'
	*	I2_ARGS[n&gt;1] = subcommand-specific arguments
	*
	* @returns mixed Either a string, which will be the title for both the
	*                main pane and for part of the page title, or an array
	*                of two strings: the first is part of the page title,
	*                and the second is the title of the content pane. To
	*                specify no titles, return an empty array. To specify
	*                that this module has no main content pane (and will
	*                show an error if someone tries to access it as such),
	*                return FALSE.
	*/
	public function init_pane() {
		global $I2_ARGS,$I2_ROOT;

		switch($I2_ARGS[1]) {
			case 'move':
				$this->move_box();
				break;
			case 'delete':
				$this->del_box();
				break;
			case 'minimize':
				$this->min_box();
				break;
			case 'renormalize':
				$this->renormalize_boxen();
				break;
			default:
				$this->tpl = array('error.tpl', array());
				return 'Error';
		}

		// Redirect to the page the user was just viewing.
		redirect(str_replace($I2_ROOT,'',$_SERVER['HTTP_REFERER']));
	}

	/**
	* Move an intrabox.
	*
	* $I2_ARGS parameters:
	*	$I2_ARGS[2] = boxid of the intrabox to move
	*	$I2_ARGS[3] = amount to add to the ordering index. (1 to move the box one place down, -1 to move it one place up, etc.)
	*/
	private function move_box() {
		global $I2_ARGS,$I2_SQL,$I2_USER;

		$boxid = $I2_ARGS[2];
		$delta_index = $I2_ARGS[3];

		if(!is_numeric($boxid)) {
			throw new I2Exception('Non-numeric boxid `$boxid` given to move_box!');
		}
		if(!is_numeric($delta_index)) {
			throw new I2Exception('Non-numeric order index delta `$delta_index` given to move_box!');
		}

		$max = $I2_SQL->query('SELECT max(box_order) FROM intrabox_map WHERE uid=%d;',$I2_USER->uid)->fetch_single_value();
		$from_index = $I2_SQL->query('SELECT box_order FROM intrabox_map WHERE uid=%d AND boxid=%d;',$I2_USER->uid,$boxid)->fetch_single_value();
		if($from_index === FALSE) {
			throw new I2Exception('Tried to move box `$boxid`, which is not currently enabled for this user');
		}
		$to_index = $from_index + $delta_index;
		
		if($to_index < 1) {
			$to_index = 1;
			warn('Specified index out of range in move_box, forcing to_index to 1');
		}
		if($to_index > $max) {
			$to_index = $max;
			warn('Specified index out of range in move_box, forcing to_index to $max: '.$max);
		}

		$delta_index = $to_index - $from_index;

		/*
		** You CANNOT display content in the init_ routines!!!
		** There's a good reason that no Display object is passed to them.
		** Displaying anything makes a redirect impossible.
		** This must be eliminated.
		*/

		
		//echo('<div style=\"position: absolute; left: 300px; top: 10px; z-index: 100\">delta_index: $delta_index, to_index: $to_index, from_index: $from_index</div>');
		if($delta_index != 0) {
			$I2_SQL->query('UPDATE intrabox_map SET box_order = box_order+(%d) WHERE uid=%d AND (box_order BETWEEN %d AND %d OR box_order = %d);', ($delta_index>0?-1:1), $I2_USER->uid, min($from_index, $to_index), max($from_index, $to_index), $to_index);
			$I2_SQL->query('UPDATE intrabox_map SET box_order = %d WHERE uid=%d AND boxid=%d;', $to_index, $I2_USER->uid, $boxid);
		}
	}

	/**
	* Delete an intrabox
	*/
	private function del_box() {
		global $I2_SQL,$I2_USER,$I2_ARGS;

		$boxid = $I2_ARGS[2];
		if(!is_numeric($boxid)) {
			throw new I2Exception('Tried to delete non-numeric intrabox `$boxid`');
		}

		$box_order = $I2_SQL->query('SELECT box_order FROM intrabox_map WHERE uid=%d AND boxid=%d;', $I2_USER->uid, $boxid)->fetch_single_value();
		if($box_order) {
			$I2_SQL->query('DELETE FROM intrabox_map WHERE uid=%d AND boxid=%d;', $I2_USER->uid, $boxid);
			$I2_SQL->query('UPDATE intrabox_map SET box_order=box_order-1 WHERE uid=%d AND box_order > %d;', $I2_USER->uid, $box_order);
		}
	}

	private function min_box() {
		global $I2_SQL,$I2_USER,$I2_ARGS;

		$boxid = $I2_ARGS[2];
		$from_min = $I2_SQL->query('SELECT closed FROM intrabox_map WHERE uid=%d AND boxid=%d;', $I2_USER->uid, $boxid)->fetch_single_value();
		$to_min = ($from_min?0:1);

		$I2_SQL->query('UPDATE intrabox_map SET closed=%d WHERE uid=%d AND boxid=%d;',$to_min,$I2_USER->uid,$boxid);
	}

	private function renormalize_boxen() {
		Intrabox::renormalize_order();
	}
	
}
?>
