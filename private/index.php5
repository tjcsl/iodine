<?php
	$user = getenv('WEBAUTH_USER');
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
?>
</body></html>
