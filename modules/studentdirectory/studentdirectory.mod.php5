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
	
	private $template;
	private $template_args = array();

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_SQL,$I2_ARGS,$I2_USER;

		$this->user = NULL;

		if( ! isset($I2_ARGS[1]) ) {
			$this->template = 'studentdirectory_help.tpl';
			return array('Directory Help', 'Searching Help');
		}

		$mode = i2config_get('mode','full','roster');
		if ($mode != 'full') {
			if (in_array($I2_ARGS[1],array('roster','class','section')))
				redirect();
		}
		
		switch($I2_ARGS[1]) {
			case 'pictures':
				try {
					$user = isset($I2_ARGS[2]) ? new User($I2_ARGS[2]) : $I2_USER;
				} catch (I2Exception $e) {
					return array('Error', 'Error: User does not exist');
				}
				$this->template = 'pictures.tpl';
				$this->template_args['user'] = $user;
				return array('Pictures: '.$user->fname.' '.$user->lname, $user->fname.' '.$user->lname);
			case 'search':
				if( !isSet($_REQUEST['studentdirectory_query']) || $_REQUEST['studentdirectory_query'] == '') {
					$this->template = 'studentdirectory_help.tpl';
					return array('Directory Help', 'Searching Help');
				} else {
					$info = $I2_USER->search_info($_REQUEST['studentdirectory_query']);
					
					if( count($info) == 1 ) {
						redirect('studentdirectory/info/'.$info[0]->uid);
					} 
					$this->template_args['info'] = $info;
					$this->template = 'search.tpl';
					return array('Directory search results for "'.$_REQUEST['studentdirectory_query'].'"', 'Search results for "'.$_REQUEST['studentdirectory_query'].'"');
				}
				break;
			case 'class':
				if(!isSet($I2_ARGS[2])) {
					redirect();
				}
				$sec = Schedule::section($I2_ARGS[2]);
				$this->template = 'class.tpl';
				$this->template_args = array('class'=>$sec,'students'=>$sec->get_students(),'aimkey'=>i2config_get("key", NULL, "aim"));
				return "Students in {$sec->name}, Period {$sec->period}";
			case 'section':
				if (isSet($I2_ARGS[2])) {
					$classid = $I2_ARGS[2];
				} else {
					$classid = NULL;
				}
				$sectionids = Schedule::sections($classid);
				$classes = array();
				foreach ($sectionids as $sectionid) {
					$sec = Schedule::section($sectionid);
					$classes[] = array('class'=>$sec);
				}
				usort($classes,array($this,'teacherperiodsort'));
				$classname = $classes[0]['class']->name;
				$this->template = 'classes.tpl';
				$this->template_args['classes'] = $classes;
				return "Sections of $classname";
			case 'roster':
				$sectionids = Schedule::roster();
				$classes = array();
				foreach ($sectionids as $sectionid) {
					$sec = Schedule::section($sectionid);
					$classes[] = $sec;
				}
				$I2_ARGS[2] = (isset($I2_ARGS[2]) ? $I2_ARGS[2] : '');
				switch (strtolower($I2_ARGS[2])) {
				case 'teacher':
					@usort($classes, array('StudentDirectory', 'sort_teacher'));
					break;
				case 'period':
					@usort($classes, array('StudentDirectory', 'sort_period'));
					break;
				case 'room':
					@usort($classes, array('StudentDirectory', 'sort_room'));
					break;
				case 'term':
					@usort($classes, array('StudentDirectory', 'sort_term'));
					break;
				case 'name':
				default:
					@usort($classes, array('StudentDirectory', 'sort_name'));
					break;
				}
				$this->template = 'roster.tpl';
				$this->template_args['courses'] = $classes;
				return "School Roster: All Classes";
			//Get info about someone or something
			case 'info':
				try {
					$user = isset($I2_ARGS[2]) ? new User($I2_ARGS[2]) : $I2_USER;
				} catch(I2Exception $e) {
					$this->template = 'nouser.tpl';
					return array('Error', 'Error: User does not exist');
				}

				try {
					$sched = $user->schedule();
					if (!$sched->current()) {
						$sched = NULL;
					}
				} catch( I2Exception $e) {
					$sched = NULL;
				}

				$im_status = array();
				$aim_accts = $user->aim;
				$icq_accts = $user->icq;
				$jabber_accts = $user->jabber;
				$yahoo_accts = $user->yahoo;
				if(count($aim_accts)) {
					if(!is_array($aim_accts) && !is_object($aim_accts)) {
						settype($aim_accts, 'array');
					}
					$aim_icon = array();
					$key = i2config_get("key", NULL, "aim");
					if ($key === NULL)
						d("No AIM Presence key in config file.",4);
					foreach($aim_accts as $aim) {
						if ($key === NULL) {
							global $I2_ROOT;
							$url="{$I2_ROOT}www/pics/osi/";
							switch($this->im_status('aim', $aim)) {
							case IM_ONLINE:
								$url .= 'online.png';
								break;
							case IM_OFFLINE:
								$url .= 'offline.png';
								break;
							case IM_UNKNOWN:
								$url .= 'unknown.png';
								break;
							}
						} else {
							$url="http://api.oscar.aol.com/presence/icon?k=$key&t=$aim";
						}
						$aim_icon[] = $url;	
					}
					$this->template_args['aim_icon'] = $aim_icon;
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

				$eighth = EighthActivity::id_to_activity(EighthSchedule::get_activities($user->uid));

				$this->template = 'studentdirectory_pane.tpl';
				$this->template_args['schedule'] = $sched;
				$this->template_args['user'] = $user;
				$this->template_args['eighth'] = $eighth;
				$this->template_args['im_status'] = $im_status;
				$this->template_args['homecoming_may_vote'] = Homecoming::user_may_vote($user);
				$this->template_args['is_admin'] = Group::admin_all()->has_member($user);
				$this->template_args['mode'] = i2config_get('mode','full','roster');
				return array('Directory: '.$user->fname.' '.$user->lname, $user->fname.' '.$user->lname);
			default:
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
		$display->disp($this->template, $this->template_args);
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
				$fp = @fopen('http://mail.opi.yahoo.com/online?u=' . $id . '&m=t&t=1', 'r');
				if ($fp) {
					do {
					$response .= fread($fp, 128);
					} while (!feof($fp));
					fclose($fp);
				}
				if ($response == '01') { $im_status[$type][$id] = IM_ONLINE; return IM_ONLINE; }
				else { $im_status[$type][$id] = IM_OFFLINE; return IM_OFFLINE; }
				break;
			case 'icq':
				$icq2im = array(0 => IM_OFFLINE, 1 => IM_ONLINE, 2 => IM_UNKNOWN);
				$server = 'status.icq.com';
				$url = '/online.gif?icq=' . $id . '&img=1';
				$fp = @fsockopen($server, 80, $errno, $errstr, 90);
				if ($fp) {
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
				}
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
				if ($fp) {
					socket_set_blocking($fp, 1);

					$data = '';

					$request  = 'GET ' . $url . ' HTTP/1.0' . "\r\n";
					$request .= 'Host: ' . $server . "\r\n";
					$request .= 'Connection: Close' . "\r\n";
					$request .= "\r\n";

					fputs($fp, $request);
					while (!feof($fp)) {
						$data = fgets($fp, 1024);
						if (strpos($data, 'Location: ') === 0) { 
							fclose($fp);
							return (int) substr($data, 17, 1);
						}
					}
					@fclose($fp);
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
				 $status = join(@file('http://' . $server . $url . '?jid=' . $id . '&type=text'),'');
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

	public static function sort_name($a, $b) {
		return strcmp($a->name, $b->name);
	}

	public static function sort_teacher($a, $b) {
		return strcmp($a->teacher->name_comma, $b->teacher->name_comma);
	}

	public static function sort_period($a, $b) {
		return strcmp($a->period, $b->period);
	}

	public static function sort_room($a, $b) {
		return strcmp($a->room, $b->room);
	}

	public static function sort_term($a, $b) {
		if (count($a->quarters) < count($b->quarters)) {
			return -1;
		}
		if (count($a->quarters) > count($b->quarters)) {
			return 1;
		}
		for ($x = 0; $x < count($a->quarters); $x++) {
			if ($a->quarters[$x] < $b->quarters[$x]) {
				return -1;
			}
			if ($a->quarters[$x] > $b->quarters[$x]) {
				return 1;
			}
		}
		return 0;
	}

}

?>
