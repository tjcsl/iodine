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
class Bugzilla extends Module {

	private $tpl_args = [];
	private $tpl = NULL;

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

		$bug_server = i2config_get('bugzilla_server', 'mysql.tjhsst.edu', 'bugzilla');
		$bug_db = i2config_get('bugzilla_db', 'bugs', 'bugzilla');
		$bug_user = i2config_get('bugzilla_user', 'bugs', 'bugzilla');
		$bug_pass = i2config_get('bugzilla_pass', 'sgub', 'bugzilla');
		$bug_url = i2config_get('bugzilla_url', 'https://bugs.tjhsst.edu/', 'bugzilla');
		$bug_expire = i2config_get('bugzilla_expire', 'Fri, 01-Jan-2038 00:00:00 GMT', 'bugzilla');
		$cryptpassword = i2config_get('cryptpassword', 'aJ7qujwxlkuac', 'bugzilla');
		$otherSQL = new MySQL($bug_server, $bug_db, $bug_user, $bug_pass);

		$res = $otherSQL->query('SELECT userid FROM profiles WHERE login_name=%s', $I2_USER->iodineUid . '@tjhsst.edu');
		if ($res->num_rows() == 0) {
			$otherSQL->query(
				'INSERT INTO profiles SET login_name=%s, cryptpassword=%s, realname=%s', 
				$I2_USER->iodineUid . '@tjhsst.edu', 
				$cryptpassword, 
				$I2_USER->fullname_comma);
			$res = $otherSQL->query(
				'SELECT userid FROM profiles WHERE login_name=%s', 
				$I2_USER->iodineUid . '@tjhsst.edu');
		}
		$userid = $res->fetch_single_value();

		$res = $otherSQL->query('SELECT COUNT(*) FROM email_setting WHERE user_id=%s', $userid);

		if ($res->fetch_single_value() == 0) {
			foreach(array(0,1,2,3,4,5) as $brel) {
				foreach(array(0,1,2,3,4,5,6,7,8,9,50) as $bev) {
					if($bev==8 && $brel!=2)
						continue;
					$otherSQL->query(
						'INSERT INTO email_setting (user_id, relationship, event) VALUES (%s, %s, %s)',
						$userid,
						$brel,
						$bev);
				}
			}
			foreach(array(100,101) as $bev) {
				$otherSQL->query(
					'INSERT INTO email_setting (user_id, relationship, event) VALUES (%s, 100, %s)',
					$userid,
					$bev);
			}
		}

		$ipaddy = $_SERVER['REMOTE_ADDR'];

		do {
			$logincookie = substr(tempname(),0,10);
		} while($otherSQL->query(
			'SELECT userid FROM logincookies WHERE cookie=%s', 
				$logincookie)->num_rows() > 0);

		$otherSQL->query(
			'INSERT INTO logincookies (cookie, userid, ipaddr, lastused) VALUES (%s, %s, %s, NOW())', 
			$logincookie, 
			$userid, 
			$ipaddy);
		setcookie("Bugzilla_login", $userid , strtotime($bug_expire), '/', '.tjhsst.edu');
		setcookie("Bugzilla_logincookie", $logincookie , strtotime($bug_expire), '/', '.tjhsst.edu');
		header("Location: $bug_url");
	}
}
?>
