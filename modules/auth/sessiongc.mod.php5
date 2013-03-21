<?php
/**
* Just contains the definition for the {@link SessionGC} class.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005-2006 The Intranet 2 Development Team
* @since 1.0
* @package core
* @subpackage Auth
* @filesource
*/

/**
* A class to clean up expired sessions.
* @package core
* @subpackage Auth
*/
class SessionGC {
	/**
	* The directory where all PHP session files reside.
	*/
	const SESS_DIR = '/var/lib/php5/iodine';

	/**
	* Emulates default session handling behavior; required by session_set_save_handler().
	*/
	public static function open($save_path, $sess_name) {
		return TRUE;
	}
	/**
	* Emulates default session handling behavior; required by session_set_save_handler().
	*/
	public static function close() {
		return TRUE;
	}
	/**
	* Emulates default session handling behavior; required by session_set_save_handler().
	*/
	public static function read($sessid) {
		return @file_get_contents(self::SESS_DIR . 'sess_' . $sessid);
	}
	/**
	* Emulates default session handling behavior; required by session_set_save_handler().
	*/
	public static function write($sessid, $value) {
		return file_put_contents(self::SESS_DIR . 'sess_' . $sessid, $value) > 0;
	}
	/**
	* Emulates default session handling behavior; required by session_set_save_handler().
	*/
	public static function destroy($sessid) {
		if(file_exists(self::SESS_DIR . 'sess_' . $sessid)) {
			return @unlink(self::SESS_DIR . 'sess_' . $sessid);
		}
		return FALSE;
	}

	/**
	* Cleans up expired sessions, and executes their $_SESSION['logout_funcs'] functions.
	*
	* @param int $max_lifetime Passed by session_set_save_handler(), unused here.
	* @return bool TRUE on success, FALSE on error.
	*/
	public static function gc($max_lifetime) {
		clearstatcache();
		exec("date >>/tmp/gclog"); // This is temporary, until I can see that it works correctly.
		foreach (glob(self::SESS_DIR."sess_*") as $filename) {
			if(is_file($filename) && is_readable($filename)) {
				$contents = file_get_contents($filename);
				$sess = self::unserializesession($contents);
				if(!$sess) {
					// Invalid cache file, should delete
					unlink($filename);
					continue;
				}

				// If we have no login time, use the session last-modified time
				if(!isset($sess['i2_login_time'])) {
					$sess['i2_login_time'] = filemtime($filename);
				}
				if(Auth::should_autologout($sess['i2_login_time'],(isset($sess['i2_username'])?$sess['i2_username']:NULL))) {
					self::logoutfuncs($sess);
					unlink($filename);
				}
			}
		}
		return TRUE;
	}

	/**
	* Run logout functions and so on.
	*
	* @param filename Session variables from session.
	*/
	public static function logoutfuncs($sess) {
		if(isset($sess['logout_funcs'])) {
			foreach($sess['logout_funcs'] as $callback) {
				exec("echo ".$callback[0][0]."->"$callback[0][1].":".implode($callback[1])." >> /tmp/gcrunlog");
				if(is_callable($callback[0])) {
					call_user_func_array($callback[0], $callback[1]);
				}
			}
		}
	}

	/**
	* Unserializes session data.
	*
	* @param string $data The session data to unserialize.
	* @return Array An array of session data, akin to $_SESSION.
	*/
	private static function unserializesession($data) {
		$vars=preg_split(
//			'/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\|/',
			'/([a-zA-Z_][a-zA-Z0-9_]*)\|/',
			$data,-1,PREG_SPLIT_NO_EMPTY |
			PREG_SPLIT_DELIM_CAPTURE
		);
		for($i=0; isset($vars[$i]) && $vars[$i]; $i++) {
			$result[$vars[$i++]] = @unserialize($vars[$i]);
		}
		if(!isset($result)) {
			$result = FALSE;
		}
		return $result;
	}
}
?>
