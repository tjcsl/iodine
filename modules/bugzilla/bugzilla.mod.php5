<?php
/**
 * Just contains the definition for the {@link Zimbramail}.
 * @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
 * @copyright 2005-2006 The Intranet 2 Development Team
 * @package modules
 * @subpackage mail
 * @filesource
 */

/**
 * The module that keeps the angry people happy. Oh, wait...
 * @package modules
 * @subpackage mail
 */
class Bugzilla implements Module {

	private $tpl_args = array();
	private $tpl = NULL;

	/**
	 * Displays all of a module's ibox content.
	 *
	 * @param Display $disp The Display object to use for output.
	 */
	function display_box($disp) {
	}

	/**
	 * Displays all of a module's main content.
	 *
	 * @param Display $disp The Display object to use for output.
	 */
	function display_pane($disp) {
		$disp->disp($this->tpl, $this->tpl_args);
	}

	/**
	 * Gets the module's name.
	 *
	 * @returns string The name of the module.
	 */
	function get_name() {
		return 'Bugzilla';
	}

	/**
	 * Performs all initialization necessary for this module to be 
	 * displayed in an ibox.
	 *
	 * @returns string The title of the box if it is to be displayed,
	 *                 otherwise FALSE if this module doesn't have an
	 *                 intrabox.
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
		global $I2_USER, $I2_AUTH, $I2_ARGS;

		$bug_server = i2config_get('bugzilla_server', 'bugs.tjhsst.edu', 'bugzilla');
		$bug_db = i2config_get('bugzilla_db', 'bugs', 'bugzilla');
		$bug_user = i2config_get('bugzilla_user', 'bugs', 'bugzilla');
		$bug_pass = i2config_get('bugzilla_pass', 'sgub', 'bugzilla');
		$bug_url = i2config_get('bugzilla_url', 'https://bugs.tjhsst.edu/', 'bugzilla');
		$bug_expire = i2config_get('bugzilla_expire', 'Fri, 01-Jan-2038 00:00:00 GMT', 'bugzilla');
		$otherSQL = new MySQL($bug_server, $bug_db, $bug_user, $bug_pass);

		$res = $otherSQL->query('SELECT userid FROM profiles WHERE login_name=%s', $I2_USER->iodineUid . '@tjhsst.edu');
		if ($res->num_rows() == 0) {
			$otherSQL->query('INSERT INTO profiles SET login_name=%s, cryptpassword=%s, realname=%s, mybugslink=1, refreshed_when=NOW()', $I2_USER->iodineUid . '@tjhsst.edu', 'aJ7qujwxlkuac', $I2_USER->fullname_comma);
			$res = $otherSQL->query('SELECT userid FROM profiles WHERE login_name=%s', $I2_USER->iodineUid . '@tjhsst.edu');
		}
		$userid = $res->fetch_single_value();

		$ipaddy = $_SERVER['REMOTE_ADDR'];

		do {
			$logincookie = substr(tempname(),0,10);
		} while($otherSQL->query('SELECT userid FROM logincookies WHERE cookie=%s', $logincookie)->num_rows() > 0);

		$otherSQL->query('INSERT INTO logincookies (cookie, userid, ipaddr, lastused) VALUES (%s, %s, %s, NOW())', $logincookie, $userid, $ipaddy);
		setcookie("Bugzilla_login", $userid , strtotime($bug_expire), '/', '.tjhsst.edu');
		setcookie("Bugzilla_logincookie", $logincookie , strtotime($bug_expire), '/', '.tjhsst.edu');
		header("Location: $bug_url");
	}
}
?>
