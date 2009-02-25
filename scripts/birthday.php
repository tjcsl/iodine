#!/usr/bin/php
<?php
/**
 *	Script to send today's birthday emails
 */
$do_mail = TRUE;

//Assumes it is located in intranet2/scripts directory, change as necessary
include "../functions.inc.php5";
define("CONFIG_FILENAME","../config.ini");

$birthday = strftime("%m%d");
$filter = "(&(objectClass=tjhsstStudent)(birthday=*$birthday))";

$who = i2config_get('authuser_dn',NULL,'ldap');
$cred = i2config_get('authuser_passwd',NULL,'ldap');

if ($who === NULL || $cred === NULL) {
	print "Could not access the configuration.";
	exit(1);
}

$results = array();
exec("ldapsearch -x -LLL -D $who -w $cred \"$filter\" iodineUid cn birthday givenName mail",$results);
$results = implode("\n",$results);
$results = explode("\n\n",$results);

foreach ($results as $k) {
	email(explode("\n",$k));
}

function email($array) {
	global $do_mail;
	$iodineuid = NULL;
	$cn = NULL;
	$age = NULL;
	$givenname = NULL;
	$mail = NULL;
	foreach ($array as $k) {
		if (substr($k,0,9) == "iodineUid") {
			$iodineuid = substr($k,11);
		}
		elseif (substr($k,0,2) == "cn") {
			$cn = substr($k,4);
		}
		elseif (substr($k,0,8) == "birthday") {
			$age = substr($k,10,14);
			$age = strftime("%Y") - substr($age,0,4);
			$age .= "th";
		}
		elseif (substr($k,0,9) == "givenName") {
			$givenname = substr($k,11);
		}
		elseif ($mail == NULL && substr($k,0,4) == "mail") {
			$mail = substr($k,6);
		}
	}
	if ($mail == NULL) {
		$mail = "$iodineuid@tjhsst.edu";	
	}
	$name = $givenname;
	if ($name == NULL) {
		$name = $cn;
	}
	
	$from = "intranet@tjhsst.edu";
	$subj = "Happy Birthday!";
	$headers = "From: $from\r\n";
	$headers .= "Reply-To: $from\r\n";
	$headers .= "Return-Path: $from\r\n";
	$mesg = "Hey $name! This is the TJ Intranet wishing you a happy $age birthday! Have a good one!\r\n";
	$mesg .= "\r\nP.S. Make Intranet devs happy -- bake them cookies!\r\n\r\n-$from";

	if (!$do_mail) {
		echo(print_r(array($iodineuid,$givenname,$mail),1));
		return;
	}
	if (!mail($mail,$subj,$mesg,$headers)) {
		print "Mail could not be sent to $mail.\n";
	} else {
		print "Mail sent sucessfully to $mail.\n";
	}
}
?>
