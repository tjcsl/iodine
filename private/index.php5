<?php
/**
* A page to just test WebAuth-related things.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005-2006 The Intranet 2 Development Team
* @package core
* @subpackage Auth
* @filesource
*/
	$user = getenv('REMOTE_USER');
	$creds = getenv('KRB5CCNAME');
?>
<html><head><title>Auth Successful</title></head><body>
You are authenticated as <?php echo $user; ?><br />
Your kerberos credentials are located in <?php echo $creds; ?><br />
Your credentials:
<?php
	$desc = array(
		0 => array('pipe','r'),
		1 => array('pipe','w'),
		2 => array('pipe','w')
	);
	
	$env = array('KRB5CCNAME' => $creds);
	
	$klist = proc_open('klist',$desc,$pipes,NULL,$env);

	if (is_resource($klist)) {
		fclose($pipes[0]);
		$str = stream_get_contents($pipes[1]);
		$str = str_replace('\n','<br />',$str);
		echo '<pre>';
		echo $str;
		echo '</pre>';
		fclose($pipes[1]);
	}
	//echo shell_exec('echo $KRB5CCNAME');
?><br />
WebAuth-fetched LDAP attributes:<br />
<?php
	foreach ($_SERVER as $key=>$value) {
		if (substr($key,0,12) == 'WEBAUTH_LDAP') {
			echo "$key => $value<br />";
		}
	}
?>
</body></html>
