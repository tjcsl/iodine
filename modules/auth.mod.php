<?php
	/**
	* The authentication module for Iodine.
	* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
	* @copyright 2004-2005 The Intranet 2 Development Team
	* @version 1.0
	* @since 1.0
	* @package auth
	*/
	
	class Authentication {
		/**
		* The Authentication class constructor.
		* 
		* @access public
		*/
		function Authentication() {
		
		}

		/**
		* Checks the user's authentication status.
		*
		* @return boolean True if user is authenticated, false if not.
		*/
		function check_authenticated() {
			if (check_sessid()) { // If the user has a session ID
				return true;
			}
			// Here, they should have POST data (user+pass)
			return check_user($POST['username'], $_POST['password']);
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
		*/
		function show_login() {
			$I2_DISP->show_login();
		}
		
		/**
		* Checks a user with the specified password.
		*
		* @param string $user The username of the user you want to check
		* @param string $password The user's password
		* @return boolean	True if correct user/pass pair, false
		*			otherwise.
		FIXME: is that what it really returns? deason is just guessing
		TODO: Really messy Windows/Novell auth. Have fun lburton etc.
		*/
		function check_user($user, $password) {
			return true;
		}
		
		/**
		* Gets the starting page for a user.
		Doesn't this call another module or something to get the data?
		*
		* @param string $user The user to get the page for.
		* @return string The start page for the user.
		*/
		function get_startpage($user) {

		}

	}

?>
