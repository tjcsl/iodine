<?php
//Fast handling of stuff - at least faster than core.php5 and loading everything. Helps with AJAX.

//Get these, because we'll need them.
$namelen=strlen(end(explode('/',$_SERVER["SCRIPT_NAME"])));
$I2_ROOT = (isSet($_SERVER['HTTPS'])?'https://':'http://') . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'],0,-($namelen+9));
$I2_FS_ROOT = substr($_SERVER['SCRIPT_FILENAME'],0,-($namelen+9));

//Get the session variables from normal iodine, so that we can check authentication.
//include $I2_FS_ROOT."modules/auth/sessiongc.mod.php5";
define('CONFIG_FILENAME', $I2_FS_ROOT.'config.ini');
include $I2_FS_ROOT."functions.inc.php5";
load_module_map();
session_set_save_handler(array('SessionGC','open'),array('SessionGC','close'),array('SessionGC','read'),array('SessionGC','write'),array('SessionGC','destroy'),array('SessionGC','gc'));
session_start();

//Check if we're logged in. It'd be nice if we didn't hard-code the 10-minute limit though.
//include $I2_FS_ROOT."modules/auth/auth.class.php5";
if(!isset($_SESSION['i2_username']) || (time()>$_SESSION['i2_login_time']+600 && $_SESSION['i2_username'] != 'eighthoffice'))
{
	session_destroy();
	unset($_SESSION);
	//header("Location: $I2_ROOT");
	echo "ERROR_NOSESS";
}

//Update the login time so that we don't autologout all of the time.
$_SESSION['i2_login_time']=time();
?>
