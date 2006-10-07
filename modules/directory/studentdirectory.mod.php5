<?php
/**
* Just contains the definition for the class {@link StudentDirectory}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage StudentDirectory
* @filesource
*/

/**
* This module helps you find info on your fellow classmates, addresses, classes,
* etc.
* @package modules
* @subpackage StudentDirectory
*/
class StudentDirectory implements Module {
	
	private $information;
	private $user = NULL;
	private $classes;

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_SQL,$I2_ARGS,$I2_USER;

		$this->user = NULL;
		if( ! isset($I2_ARGS[1]) ) {
			$this->information = 'help';
			return array('Directory Help', 'Searching Help');
		}
		
		switch($I2_ARGS[1]) {
			//Get info about someone or something
			case 'info':
				try {
					$this->user = isset($I2_ARGS[2]) ? new User($I2_ARGS[2]) : $I2_USER;
				} catch(I2Exception $e) {
					return array('Error', 'Error: User does not exist');
				}
				return array('Directory: '.$this->user->fname.' '.$this->user->lname, $this->user->fname.' '.$this->user->lname);

			case 'search':
				if( !isSet($_REQUEST['studentdirectory_query']) || $_REQUEST['studentdirectory_query'] == '') {
					$this->information = 'help';
					return array('Directory Help', 'Searching Help');
				} else {
					$this->information = $I2_USER->search_info($_REQUEST['studentdirectory_query']);
					
					if( count($this->information) == 1 ) {
						redirect('studentdirectory/info/'.$this->information[0]->uid);
					}
					return array('Directory search results for "'.$_REQUEST['studentdirectory_query'].'"', 'Search results for "'.$_REQUEST['studentdirectory_query'].'"');
				}
				break;
			case 'class':
				if(!isSet($I2_ARGS[2])) {
					redirect();
				}
				$sec = Schedule::section($I2_ARGS[2]);
				$this->information = array('class'=>$sec,'students'=>$sec->get_students());
				return "Students in {$sec->name}, Period {$sec->period}";
				break;
			case 'section':
				if (isSet($I2_ARGS[2])) {
					$classid = $I2_ARGS[2];
				} else {
					$classid = NULL;
				}
				$sectionids = Schedule::sections($classid);
				$this->classes = array();
				foreach ($sectionids as $sectionid) {
					$sec = Schedule::section($sectionid);
					$this->classes[] = array('class'=>$sec);
				}
				usort($this->classes,array($this,'teacherperiodsort'));
				$classname = $this->classes[0]['class']->name;
				$this->information = 'classes';
				return "Sections of $classname";
			case 'allphotos':
				if (isSet($I2_ARGS[2])) {
					$this->user = new User($I2_ARGS[2]);
					$this->information = 'photos';
					return "All photos for {$this->user->fullname}";
				}
			default:
				$this->information = FALSE;
				return array('Error', 'Error: User does not exist');
				
		}
	}
	
	private function teacherperiodsort($one, $two) {
			  $diff = strcasecmp($one['class']->teacher->name_comma,$two['class']->teacher->name_comma);
			  if ($diff != 0) {
			  	return $diff;
			  }
			  return $one['class']->period-$two['class']->period;
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		$eighth = NULL;
		$im_status = NULL;
		switch($this->information) {
			case 'help':
				$display->disp('studentdirectory_help.tpl');
				break;
			case 'classes':
				$display->smarty_assign('classes',$this->classes);
				$display->disp('classes.tpl');
				break;
			case 'photos':
				$display->disp('photos.tpl',array('user' => $this->user));
				break;
			default:
				if($this->user !== NULL) {
					try {
						$sched = $this->user->schedule();
						if (!$sched->current()) {
							$sched = NULL;
						}
					} catch( I2Exception $e) {
						$sched = NULL;
					}
				} else {
					$sched = NULL;
				}
				if ($this->user !== NULL) {
					$im_status = array();
					$aim_accts = $this->user->aim;
					$icq_accts = $this->user->icq;
					$jabber_accts = $this->user->jabber;
					$yahoo_accts = $this->user->yahoo;
					if(count($aim_accts)) {
						if(!is_array($aim_accts) && !is_object($aim_accts)) {
							settype($aim_accts, 'array');
						}
						foreach($aim_accts as $aim) {
							switch($this->im_status('aim', $aim)) {
							case IM_ONLINE:
								$im_status['aim'][$aim] = 'online';
								break;
							case IM_OFFLINE:
								$im_status['aim'][$aim] = 'offline';
								break;
							case IM_UNKNOWN:
								$im_status['aim'][$aim] = 'unknown';
								break;
							}
						}
					}
					if(count($icq_accts)) {
						if(!is_array($icq_accts) && !is_object($icq_accts)) {
							settype($icq_accts, 'array');
						}
						foreach($icq_accts as $icq) {
							switch($this->im_status('icq', $icq)) {
							case IM_ONLINE:
								$im_status['icq'][$icq] = 'online';
								break;
							case IM_OFFLINE:
								$im_status['icq'][$icq] = 'offline';
								break;
							case IM_UNKNOWN:
								$im_status['icq'][$icq] = 'unknown';
								break;
							}
						}
					}
					if(count($jabber_accts)) {
						if(!is_array($jabber_accts) && !is_object($jabber_accts)) {
							settype($jabber_accts, 'array');
						}
						foreach($jabber_accts as $jabber) {
							switch($this->im_status('jabber', $jabber)) {
							case IM_ONLINE:
								$im_status['jabber'][$jabber] = 'online';
								break;
							case IM_OFFLINE:
								$im_status['jabber'][$jabber] = 'offline';
								break;
							case IM_UNKNOWN:
								$im_status['jabber'][$jabber] = 'unknown';
								break;
							}
						}
					}
					if(count($yahoo_accts)) {
						if(!is_array($yahoo_accts) && !is_object($yahoo_accts)) {
							settype($yahoo_accts, 'array');
						}
						foreach($yahoo_accts as $yahoo) {
							switch($this->im_status('yahoo', $yahoo)) {
							case IM_ONLINE:
								$im_status['yahoo'][$yahoo] = 'online';
								break;
							case IM_OFFLINE:
								$im_status['yahoo'][$yahoo] = 'offline';
								break;
							case IM_UNKNOWN:
								$im_status['yahoo'][$yahoo] = 'unknown';
								break;
							}
						}
					}
					$eighth = EighthActivity::id_to_activity(EighthSchedule::get_activities($this->user->uid));
				}
				$display->disp('studentdirectory_pane.tpl',array('info' => $this->information, 'schedule' => $sched, 'user' => $this->user, 'eighth' => $eighth, 'im_status' => $im_status, 'homecoming_may_vote' => Homecoming::user_may_vote($this->user)));
		}
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		return 'Search the Directory'; // right now we don't need to get any initial values, the box will just contain a form like the old intranet for queries
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
		$display->disp('studentdirectory_box.tpl');
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return 'StudentDirectory';
	}
	function im_status($type, $id) {
		if (!defined('IM_ONLINE')) define('IM_ONLINE', 1);
		if (!defined('IM_OFFLINE')) define('IM_OFFLINE', 0);
		if (!defined('IM_UNKNOWN')) define('IM_UNKNOWN', 3);

		$response = '';
		static $im_status;
		//print_r($im_status);
		if (isset($im_status[$type][$id])) { return $im_status[$type][$id]; }
		switch($type) {
			case 'yahoo':
				$fp = fopen('http://mail.opi.yahoo.com/online?u=' . $id . '&m=t&t=1', 'r');
				do {
				$response .= fread($fp, 128);
				} while (!feof($fp));
				fclose($fp);
				if ($response == '01') { $im_status[$type][$id] = IM_ONLINE; return IM_ONLINE; }
				else { $im_status[$type][$id] = IM_OFFLINE; return IM_OFFLINE; }
				break;
			case 'icq':
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
			case 'aim':
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
}

?>
