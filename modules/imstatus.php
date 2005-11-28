<?php
	function im_status($type, $id) {
		if (!defined('IM_ONLINE')) define('IM_ONLINE', 1);
		if (!defined('IM_OFFLINE')) define('IM_OFFLINE', 0);
		if (!defined('IM_UNKNOWN')) define('IM_UNKNOWN', 0); // Might want to distinguish, 2?

		$response = '';
		static $im_status;
		//print_r($im_status);
		if (isset($im_status[$type][$id])) { return $im_status[$type][$id]; }
		switch($type) {
			case "yahoo":
				$fp = fopen('http://mail.opi.yahoo.com/online?u=' . $id . '&m=t&t=1', 'r');
				do {
				$response .= fread($fp, 128);
				} while (!feof($fp));
				fclose($fp);
				if ($response == '01') { $im_status[$type][$id] = IM_ONLINE; return IM_ONLINE; }
				else { $im_status[$type][$id] = IM_OFFLINE; return IM_OFFLINE; }
				break;
			case "icq":
				$icq2im = array(0 => IM_OFFLINE, 1 => IM_ONLINE, 2 => IM_UNKNOWN);
				$server = 'status.icq.com';
				$url = '/online.gif?icq=' . $id . '&img=1';
				$fp = fsockopen($server, 80, $errno, $errstr, 90);
				socket_set_blocking($fp, 1);

				$data = '';
				fputs($fp,
				  'HEAD ' . $url . ' HTTP/1.1' . "\r\n" .
				'Host: ' . $server . "\r\n\r\n");
				do {
				$data = fgets($fp, 1024);
				if (strstr($data, '404 Not Found')) return IM_UNKNOWN;
				} while(strstr($data, 'Location: /') === false && !feof($fp));
				fclose($fp);
				$status = substr($data, -7, 1);
				$im_status[$type][$id] = $icq2im[$status];
				return($icq2im[$status]);
				break;
			case "aim":
				/* This works by opening an url in the form of
				 * http://big.oscar.aol.com/AIM_ID?on_url=ON_URL&off_url=OFF_URL
				 * Which then redirects with a Location: headerto either ON_URL or
				 * OFF_URL and as such, a GET request is required for some reason.
				*/

				$server = 'big.oscar.aol.com';
				$url = '/'.$id.'?on_url=http://' . IM_ONLINE . '.com/&off_url=http://' . IM_OFFLINE . '.com/';
				$fp = fsockopen($server, 80, $errno, $errstr, 90);
				socket_set_blocking($fp, 1);

				$data = '';

				$request  = 'GET ' . $url . ' HTTP/1.0' . "\r\n";
				$request .= 'Host: ' . $server . "\r\n";
				$request .= 'Connection: Close' . "\r\n";
				$request .= "\r\n";

				fputs($fp, $request);
				while (!feof($fp)) {
				$data = fgets($fp, 1024);
				if (strpos($data, 'Location: ') === 0) { return (int) substr($data, 17, 1); }
				}
				return IM_UNKNOWN;
				break;
			case 'jabber':
				/* This requires you to allow edgar@jabber.netflint.net to see your online status
				 * see http://edgar.netflint.net/ for more info
				 * If you've set up your own edgar bot just change the $server and $url variable.
				 */
				 $server = 'edgar.netflint.net';
				 $url = '/status.php';
				 $status = join(file('http://' . $server . $url . '?jid=' . $id . '&type=text'),'');
				 $status = substr($status, 0, strpos($status, ':'));
				 switch($status) {
					 case 'Online':
					 case 'Away':
					 case 'Not Available':
					 case 'Do not disturb':
					 case 'Free for chat':
					 	$im_status[$type][$id] = IM_ONLINE;
					 	return IM_ONLINE;
					 	break;
					 case 'Offline':
					 	$im_status[$type][$id] = IM_OFFLINE;
					 	return IM_OFFLINE;
					 	break;
					 default:
					 	$im_status[$type][$id] = IM_UNKNOWN;
					 	return IM_UNKNOWN;
					 	break;
				 }
				 break;
			default:
				return false;
				break;
		}
	}
?> 
