<?php

exec('./mysqlpass', $pass);
$link = mysql_connect(':/var/run/mysqld/mysqld.sock', 'iodine', $pass[0]);
mysql_select_db('iodine', $link);
mysql_query('DELETE FROM intrabox', $link);
mysql_query('ALTER TABLE intrabox AUTO_INCREMENT = 1');

require_once("../modules/Module.class.php5");

exec('cd ../modules/ && ls --color=never --width=1 *.mod.php5', $files);
foreach($files as $fname) {
	print("$fname\n");
	require_once("../modules/".$fname);
	exec("echo $fname | sed s/\.mod\.php5// -", $fname);
	$fname = $fname[0];
	print("$fname\n");
	if (call_user_func(array($fname, 'is_intrabox'))) {
		print("Inserting $fname\n");
		mysql_query("INSERT INTO intrabox SET name='$fname', display_name='$fname'", $link);
	}
}

$files = null;
exec('cd ../templates/intrabox/ && ls --color=never --width=1', $files);
foreach($files as $fname) {
	exec("echo $fname | sed s/intrabox_// -", $fname);
	$fname = $fname[0];
	exec("echo $fname | sed s/\.tpl// -", $fname);
	$fname = $fname[0];
	print("$fname\n");
	if($fname != 'close' && $fname != 'closebox' && $fname != 'open' && $fname !='openbox') {
		print("Inserting $fname\n");
		mysql_query("INSERT INTO intrabox SET name='$fname', display_name='$fname'", $link);
	}
}

?>
