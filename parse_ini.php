<?php
	// File to read
	$file = "config.ini";

	$lines = file($file);

	$config = array();

	$cur = "";

	foreach($lines as $line) {
		// Ignore comments and blank lines
		if(strlen(trim($line)) == 0 || $line{0} == ";") {
			continue;
		}
		// Make new sub-arrays for sections
		elseif($line{0} == "[") {
			$config[($cur = substr(trim($line), 1, -1))] = array();
		}
		// Add key = value to section arrays
		else {
			$parts = explode("=", $line);
			$config[$cur][trim($parts[0])] = trim($parts[1]);
		}
	}
	// Change to output config however you want
	print_r($config);
?>
