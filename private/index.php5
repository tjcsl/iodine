<?php
	$user = getenv('WEBAUTH_USER');
	$creds = getenv('KRB5CCNAME');
?>
<html><head><title>Auth Successful</title></head><body>
You are authenticated as <?php echo $user; ?><br />
Your kerberos credentials are located in <?php echo $creds; ?><br />
Your credentials:
<?php
	$out = array();
	exec('klist',$out);
	foreach ($out as $line) {
		echo $line;
		echo "<br />';
	}
?>
</body></html>
