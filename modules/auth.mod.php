<?php
	/**
	* The auth module for Iodine.
	* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
	* @copyright 2004-2005 The Intranet 2 Development Team
	* @version 1.0
	* @since 1.0
	* @package auth
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
			if ($this->check_sessid()) { // If the user has a session ID
				return true;
			}
			// Here, they should have POST data (user+pass)
			return $this->check_user($POST['username'], $_POST['password']);
		}
		
		/**
		* Checks the user's session ID
		* False for now until something happens here.
		*/
		function check_sessid() {
			// $_SESSION['blah'] is equal to blah
			return false;
		}

		/**
		* Shows the login box so users can actually log in to use the
		* system.
		*
		*/
		function show_login() {
			/* FIXME: make this code be in auth, not display*/
			$I2_DISP->show_login();
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
			$status=shell_exec("echo $password | kinit $user 1> /dev/null 2> /dev/null;echo $?;kdestroy 1> /dev/null 2> /dev/null");
			if ($status==0)
				return false;
			return true;
		}

		/**
		 * Gets the starting page for a user.
		 * Doesn't this call another module or something to get the data? Yes, but
		 * until it gets discussed, don't worry about it. -Deason
		 *
		 * @param string $user The user to get the page for.
		 * @return string The start page for the user.
		 */
		function get_startpage($user) {

		}

	}

?>
