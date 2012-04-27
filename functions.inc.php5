<?php
/**
* Global utility functions for Iodine. Only general utility functions
* which do not belong in any other specific module belong here.

* @author The Intranet2 Development Team <intranet2@lists.tjhsst.edu>
* @copyright 2004-2005 The Intranet2 Development Team
* @package core
* @filesource
*/

/**
* A quick function call for debugging.
*
* This function is basically just a wrapper for $I2_LOG->log_debug().
* This function is far more convenient, however, since you don't need
* to declare $I2_LOG as a global, and the function name is only one
* keystroke. What a timesaver!
*
* @param string $text The text to display.
* @param int $level The debugging level at which to log this message.
*                   (The higher the level, the less important, default
*                   is 9, the highest standard level.)
*/
function d($text, $level = 9) {
	global $I2_LOG;
	if (isSet($I2_LOG)) {
		$I2_LOG->log_debug($text, $level);
	}
}

function d_r($obj, $level = 9) {
	d(print_r($obj, 1), $level);
}

/**
* A quick function call for issuing a warning.
*
* This function is basically a wrapper for $I2_ERR->nonfatal_error(), but
* is much easier to type. This creates a message in the little error pane, but
* does not stop or alter execution in any way.
*
* @param string @msg The warning message.
*/
function warn($msg) {
	global $I2_ERR;
	$I2_ERR->nonfatal_error($msg);
}

function warn_r($obj) {
	warn(print_r($obj, 1));
}

/**
* A quick function call for displaying a fatal error
*
* This function executes $I2_ERR->fatal_error() if $I2_ERR exists, otherwise
* it displays the message and dies.
*
* @param string @msg The error message.
* @param bool @critical The error message.
*/
function error($msg, $critical=FALSE) {
	global $I2_ERR;
	if ($I2_ERR) {
		$I2_ERR->fatal_error($msg, $critical);
	} else {
		echo("$msg\n");
		die(1);
	}
}

/**
* The __autoload function, used for autoloading modules.
*
* This is the function used by PHP as a last resort for loading 
* noninstantiated classes before it throws an error. It checks for
* readability of the module (if one exists) in the module path.
* Triggers an error if it does not exist.
* 
* @param string $class_name Name of noninstantiated class.
*/
function __autoload($class_name) {
	global $I2_ERR;
	d("Loading $class_name");
	$class_file = '';

	if (!($class_file=get_i2module($class_name))) {
		error('Cannot load module/class '.$class_name.': the file '.$class_file.' is not readable.');
	}
	else {
		require_once($class_file);
	}
}

/**
* Loads the module map.
*
*/
function load_module_map() {
	global $I2_MODULE_MAP;
	
	$filename = i2config_get('cache_dir', NULL, 'core') . 'module.map';
	
	if (!file_exists($filename)) {
		d('Generating module map', 4);
		require_once('modules/admin/modulesmapper.class.php5');
		ModulesMapper::generate();
	}
	
	$contents = file_get_contents($filename);
	if ($contents === FALSE) {
		error('Could not load module map: could not read file ' . $filename);
	}
	
	$I2_MODULE_MAP = unserialize($contents);
	if ($I2_MODULE_MAP === FALSE) {
		error('Could not load module map: could unserialize contents of file ' . $filename);
	}
}

/**
* Determines whether a certain class name is an instantiatable I2 module.
*
* This checks whether the class name is already instantiated, and if
* not, then checks to see if an I2 module file is available and
* readable. Unlike __autoload, this only returns FALSE if a module does
* not exist, as opposed to raising an exception.
*
* @param String $module_name The name of the module.
* @return string The file name of the module to load, if it exists; false if it doesn't.
*/
function get_i2module($module_name) {
	global $I2_MODULE_MAP;
	
	/* Do not run autoload, since it will throw an exception if the
	class does not exist */

	$key = strtolower($module_name);
	
	if (!isset($I2_MODULE_MAP[$key])) {
		return FALSE;
	}
	
	$file = $I2_MODULE_MAP[$key];
	if (is_readable($file)) {
		return $file;
	}

	return FALSE;
}

/**
* Gets a configuration value from the global Iodine configuration
*
* This parses the config file specified by the constant CONFIG_FILENAME.
* The file is parsed only once, and kept in a static variable for the
* rest of the duration of the script. The section argument is optional;
* if you do not supply it, i2config_get will try to guess it for you,
* based on the class you are calling it from.
*
* @param String $field The name of the config value you want to get.
* @param mixed $default The default value to be returned if the specified key is not found.
* @param String $section The name of the config section you want to retrieve the value from.
* @return mixed The config value for the specified field, the default value if one was passed and if the specified key was not found, NULL otherwise.
*/
function i2config_get($field, $default = NULL, $section = NULL) {
	global $I2_ERR;
	static $config = NULL;
	
	if ($config === NULL) { /*Parse the INI file*/
	
		if (!is_readable(CONFIG_FILENAME)) {
			/* This is a critical error, but do not set the
			critical error flag, because the email address
			to send critical errors to is in the config
			file! */
			/* hence, put a hard-coded mail() call here */
			error('The master Iodine configuration file '.CONFIG_FILENAME.' cannot be read.');
		}
		
		$config = parse_ini_file(CONFIG_FILENAME, TRUE);
	}

	if ($section != NULL) {
		if (isset($config[$section][$field])) {
			return $config[$section][$field];
		}
		if ($default === NULL) {
			/* Return error, should probably also make a logging call here */
			d("Attempted to read bad config value $field in section $section", 1);
			return NULL;
		}
		d("Using default value $default for $field in section $section", 2);
		return $default;
		
	}
	
	/* If a section was not specified, try to guess it from getting
	the class name of what called us. */
	
	$trace = debug_backtrace();

	/* If we were called by a class, use the config section
	for that class. */
	if (isSet($trace[1]['class'])) {
		return i2config_get($field, $default, strtolower($trace[1]['class']));
	}
	
	/* If we were not called by a class, default to core, since
	if we were called by core, it would not report a class. */

	return i2config_get($field, $default, 'core');
}

/**
* Issues an HTTP redirect to the user.
*
* Redirects a user to the specified URL via an HTTP Location: header.
* Do _not_ use this function if output has already been sent to the
* user. If you attempt to do so, an exception will be thrown.
*
* @param string $url The url to redirect to (relative to Iodine root).
*                    If this is NULL or not passed, then the user will
*                    be redirected to Iodine root.
*/
function redirect($url = NULL,$postrelay=false) {
	global $I2_ROOT;

	if( headers_sent($file, $line) ) {
		throw new I2Exception('A redirect was attempted, but headers have already been sent in file '.$file.' on line '.$line);
	}
	
	$url = $I2_ROOT . $url;
	d('Redirecting to '.$url);

	if($postrelay) {
		header('HTTP/1.0 307 Temporary Redirect',true,307);
	}
	header('Location: '.$url);
	die();

}

/**
* Flatten an array. (Non-recursive, one level deep.)
*
* @param Array $arr The array to flatten.
* @return Array The flattened array.
*/
function flatten($arr) {
	$ret = array();
	foreach($arr as $item) {
		if( is_array($item) ) {
			$ret = array_merge($ret,$item);
		}
		else {
			$ret[] = $item;
		}
	}
	return $ret;
}

/**
* Flatten an array irrespective of keys. (Non-recursive, one level deep.)
*
* @param Array $arr The array to flatten.
* @return Array The flattened array.
*/
function flatten_values($arr) {
	$ret = array();
	foreach($arr as $item) {
		if( is_array($item) ) {
			$ret = array_merge($ret, array_values($item));
		}
		else {
			$ret[] = $item;
		}
	}
	return $ret;
}

/**
* Tests if the string starts with the specified prefix.
*
* @param string $str The string to test
* @param string $suffix The suffix
* @return bool True if the given string ends with the given suffix, false otherwise.
*/
function ends_with($str, $suffix) {
	return substr($str, strlen($str) - strlen($suffix)) == $suffix;
}

/**
* Determines the most recent modification time of all files and directories in a given directory.
*
* @param string $dir The directory
* @return int The most recent modification time.
*/
function dirmtime($dir) {
	$time = filemtime($dir);

	$handle = opendir($dir);
	while (($name = readdir($handle)) !== FALSE) {
		$file = "$dir/$name";
		if ($name != '.' && $name != '..' && is_dir($file)) {
			$time = max($time, dirmtime($file));
		}
	}
	closedir($handle);

	return $time;
}

/**
* Creates a unique filename which begins with prefix followed by 16 random characters
* and ends with suffix. This function is different than the built in php function tempnam
* by allowing a suffix and not actually creating the file.
*
* @param $prefix The prefix including the path to the temporary file
* @param $suffix The optional suffix
* @return string A unique filename beginning with prefix and ending with suffix
*/
function tempname($prefix, $suffix='') {
	do {
		$mtime = microtime();
		srand((float)(substr($mtime, 1+strpos($mtime, ' '))));
		$file = $prefix . substr(md5(''.rand()),0,16) . $suffix;
	} while(file_exists($file));
	return $file;
}


/**
* Sends a multipart (HTML/txt) eMail with an I2 From: address and various headers set to prevent
* message bounceback. Note that sending a message to multiple To: addresses will result in multiple
* emails rather than one to multiple recipients (ie. not To: all of them)
*
* @param $to The address to send the mail to; can be an array, in which case it will send to all
* @param $subject The message subject
* @param $message_content The message - can be html
*/
function i2_mail($to, $subject, $message_content, $news=false) {
	$date = date("r",time());
	$separator = "--MAIL-" . md5($message_content."_".$to."_".$date);	

	$from = "TJHSST Intranet <".i2config_get('sendto', 'intranet@tjhsst.edu', 'suggestion').">";
	if($news)
		$from =  "TJHSST Intranet News <".i2config_get('news', 'intranet-news@tjhsst.edu', 'suggestion').">";

	// required headers
	$headers =  "From: " . $from . "\r\n";
	$headers .= "Date: " . $date . "\r\n";

	// bounceback prevention headers
	$headers .= "Precedence: list\r\n";			// de facto standard (could also be 'bulk')
	$headers .= "Auto-Submitted: auto-generated\r\n";	// RFC3834
	$headers .= "X-Auto-Response-Suppress: OOF, AutoReply\r\n"; // Microsoft Exchange

	// multipart content headers
	$headers .= "Content-Type: multipart/alternative; boundary=\"" . $separator . "\"\r\n";
	
	$message = "--" . $separator . "\r\nContent-Type: text/plain; charset=\"UTF-8\"\r\n\r\n";
	$message .= strip_tags($message_content);
	$message .= "\r\n--" . $separator . "\r\nContent-Type: text/html; charset=\"UTF-8\"\r\n\r\n";
	$message .= $message_content;
	$message .= "\r\n--".$separator."--"; // end with separator, make amavis happy

	if(gettype($to)=="array") {
		foreach($to as $mail) {
			mail($mail,$subject,$message,$headers);
		}
	} else if($to) {
		mail($to,$subject,$message,$headers);
	}	// if there's no $to... well, derp?
}

?>
