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
		if($dh = opendir(self::SESS_DIR)) {
			while(FALSE !== ($file = readdir($dh))) {
				$file = self::SESS_DIR . $file;
				if(is_file($file) && is_readable($file)) {
					$contents = file_get_contents($file);
					$sess = self::unserializesession($contents);

					// If we have no login time, use the session last-modified time
					if($sess && !isset($sess['i2_login_time'])) {
						$sess['i2_login_time'] = filemtime($file);
					}
					if($sess && Auth::should_autologout($sess['i2_login_time'])) {
						if(isset($sess['logout_funcs'])) {
							foreach($sess['logout_funcs'] as $callback) {
								if(is_callable($callback[0])) {
									call_user_func_array($callback[0], $callback[1]);
								}
							}
						}
						unlink($file);
					}
				}
			}
			return TRUE;
		}
		warn('Cannot open PHP session directory: ' . SESS_DIR . ', please contact the Intranetmaster about this issue!');
		return FALSE;
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
