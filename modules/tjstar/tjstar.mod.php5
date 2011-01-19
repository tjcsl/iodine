<?php
/**
 * Shows useful links for the TJStar Research Symposium
 */
class TJStar implements Module {

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
	* We don't really support this yet, but make it look like we do.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function api($disp) {
		return false;
	}

	function init_pane() {
		return "TJStar Information Page";
	}

	function display_pane($disp) {
		$this->template_args['message']="O hai thar";
		$disp->disp("tjstar_pane.tpl",$this->template_args);
	}

	function init_box() {
		global $I2_USER;
		$this->template_args["uid"] = $I2_USER->uid;
		return "TJStar";
	}

	function display_box($disp) {
		$disp->disp("tjstar_box.tpl", $this->template_args);
	}

	function get_name() {
		return "TJStar";
	}

	/* 
	 * Removes the tjstar intrabox
	*/

	public static function remove_self() {
		global $I2_SQL;
		$tjstarid = $I2_SQL->query('SELECT boxid FROM intrabox WHERE name="tjstar";')->fetch_single_value();
		$users = $I2_SQL->query('SELECT uid FROM intrabox_map WHERE boxid=%d;', $tjstarid)->fetch_all_arrays(Result::ASSOC);
		foreach ($users as $user) {
			$order = $I2_SQL->query('SELECT box_order FROM intrabox_map WHERE boxid=%d AND uid=%d;', $tjstarid, $user['uid'])->fetch_single_value();
			$I2_SQL->query('DELETE FROM intrabox_map WHERE uid=%d AND boxid=%d;', $user['uid'], $tjstarid);
			$I2_SQL->query('UPDATE intrabox_map SET box_order=box_order-1 WHERE uid=%d AND box_order>%d;', $user['uid'], $order);
		}
		$I2_SQL->query('DELETE FROM intrabox WHERE name="tjstar";');
		
	}
}
?>
