<?php
	/**
	* The auth module for Iodine.
	* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
	* @copyright 2004-2005 The Intranet 2 Development Team
	* @version 1.0
	* @since 1.0
	* @package core
	* @subpackage auth
	*/
	
	class Auth {
		/**
		* The Auth class constructor.
		* 
		* @access public
		*/
		function __construct() {	
		}

		/**
		* Checks the user's authentication status.
		*
		* @return boolean True if user is authenticated, false if not.
		*/
		function check_authenticated() {
			global $_SESSION, $I2_ARGS;
			//FIXME: these need to be stored SERVER-SIDE, not in $_SESSION!!!!  Important!!!
			if (isSet($_SESSION['i2_uid']) 
				&& isSet($_SESSION['i2_login_time']) 
				&& $_SESSION['i2_login_time'] <= time()+i2config_get('timeout',0,'login')) {
				set_i2var('i2_login_time',time());
				return TRUE;
			}
			return FALSE;
		}
		
		/**
		* Checks a user with the specified password.
		*
		* @param string $user The username of the user you want to check
		* @param string $password The user's password
		* @return boolean	True if correct user/pass pair, false
		*			otherwise.
		TODO: Auth WILL WORK, but we need (in krb5.conf) -Victor:
		LOCAL.TJHSST.EDU = {
		kdc = tj01.local.tjhsst.edu
	        admin_server = tj01.local.tjhsst.edu
		}
		 */
		 
		function check_user($user, $password) {
			$descriptors = array(0 => array('pipe', 'r'), 1 => array('file', '/dev/null', 'w'), 2 => array('file', '/dev/null', 'w'));

			$process = proc_open("kinit $user@LOCAL.TJHSST.EDU", $descriptors, $pipes);
			if(is_resource($process)) {
				fwrite($pipes[0], $password);
				fclose($pipes[0]);
				$status = proc_close($process);
			
				if($status == 0) {
					exec('kdestroy');
					return true;
				}
			}
			return false;
		}
		
}

?>
