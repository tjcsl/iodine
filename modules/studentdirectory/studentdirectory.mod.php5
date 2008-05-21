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
		
	const IM_AVAILABLE = 'available.png';
	const IM_AVAILABLE_IDLE = 'available.png';
	const IM_AWAY = 'away.png';
	const IM_AWAY_IDLE = 'away.png';
	const IM_OFFLINE = 'offline.png';
	const IM_UNKNOWN = FALSE;

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_ROOT,$I2_SQL,$I2_ARGS,$I2_USER;

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
			
				$im_networks = Array(
					'aim' => "AIM/AOL Screenname",
					'yahoo' => 'Yahoo! ID',
					'msn' => 'MSN Screenname',
					'jabber' => 'Jabber Username',
					'icq' => 'ICQ Number',
					'googletalk' => 'Google Talk Username',
					'xfire' => 'XFire handle',
					'skype' => 'Skype handle');
				$im_sns = Array();
				foreach(array_keys($im_networks) as $network) {
					$sns = $user->$network;
					if(!empty($sns)) {
						$method = $network . '_statuses';
						if(!method_exists($this, $method)) {
							$method = 'unknown_statuses';
						}
						$im_sns[$network] = $this->$method(array_flip((Array)$sns));
					}

				}
				
				$eighth = EighthActivity::id_to_activity(EighthSchedule::get_activities($user->uid));

				$this->template = 'studentdirectory_pane.tpl';
				$this->template_args['schedule'] = $sched;
				$this->template_args['user'] = $user;
				$this->template_args['im_sns'] = $im_sns;
				$this->template_args['im_networks'] = $im_networks;
				$this->template_args['im_icons'] = $I2_ROOT . 'www/status/';
				$this->template_args['eighth'] = $eighth;
				
				$this->template_args['homecoming_may_vote'] = Homecoming::user_may_vote($user);
				$this->template_args['is_admin'] = Group::admin_all()->has_member($user);
				$this->template_args['mode'] = i2config_get('mode','full','roster');
				return Array('Directory: '.$user->fname.' '.$user->lname, $user->fname.' '.$user->lname);
			default:
				return Array('Error', 'Error: User does not exist');
				
		}
	}
	
	function unknown_statuses($sns) {
		foreach(array_keys($sns) as $sn) {
			$sns[$sn] = self::IM_UNKNOWN;
		}
		return $sns;
	}
	
	function aim_statuses($sns) {
		$key = i2config_get("key", NULL, "aim");
		if ($key === NULL) {
			d("No AIM Presence key in config file.",4);
			return $this->unknown_statuses($sns);
		}
		$url = "https://api.oscar.aol.com/presence/get?f=xml&k=" . $key;
		foreach(array_keys($sns) as $aim) {
			$url .= "&t=" . urlencode($aim);
		}
		$fp = @fopen($url, 'r');
		if (isSet($fp) && $fp) {
			$response = '';
			do {
				$response .= fread($fp, 128);
			} while (!feof($fp));
			fclose($fp);
		} else {
			return $this->unknown_statuses($sns);
		}

		$statuses = Array();
		while(substr_count($response, '<user>') > 0) {
			$user = substr($response, strpos($response, '<user>') + 6);
			$user = substr($user, 0, strpos($user,'</user>'));

			$aimid = substr($user, strpos($user, '<aimId>') + 7);
			$aimid = substr($aimid, 0, strpos($aimid, '</aimId>'));

			$status = substr($user, strpos($user, '<state>') + 7);
			$status = substr($status, 0, strpos($status, '</state>'));
			
			$idle = substr($user, strpos($user, '<idleTime>') + 10);
			$idle = substr($idle, 0, strpos($idle, '</idleTime>'));
			$idle = !empty($idle) && $idle > 0;
			
			$response = substr($response, strpos($response, '</user>') + 6);
		
			switch($status) {
				case 'online':
					$statuses[$aimid] = $idle ? self::IM_AVAILABLE_IDLE : self::IM_AVAILABLE;
					break;
				case 'away':
					$statuses[$aimid] = $idle ? self::IM_AWAY_IDLE : self::IM_AWAY;
					break;
				case 'offline':
					$statuses[$aimid] = self::IM_OFFLINE;
			}
		}
		foreach(array_keys($sns) as $aim) {
			$aimid = str_replace(' ','',strtolower($aim));
			$sns[$aim] = isSet($statuses[$aimid]) ? $statuses[$aimid] : self::IM_UNKNOWN;
		}
		return $sns;
	}

	function jabber_statuses($sns) {
		/*
		 * The server has been down for a long time, and we can't get to it through the proxy anyway.
		 */
		return $this->unknown_statuses($sns);
		
		foreach(array_keys($sns) as $jabber) {
			/* 
			 * This requires you to allow edgar@jabber.netflint.net to see your online status
			 * see http://edgar.netflint.net/ for more info
			 * If you've set up your own edgar bot just change the $server and $url variable.
			 */
			$server = 'edgar.netflint.net';
			$url = '/status.php';
			$file = file('http://' . $server . $url . '?jid=' . $jabber . '&type=text');
			$status = '';
			if (isSet($file) && $file) {
				$status = join($file,'');
				$status = substr($status, 0, strpos($status, ':'));
			}
			switch($status) {
				case 'Online':
				case 'Away':
				case 'Not Available':
				case 'Do not disturb':
				case 'Free for chat':
					$sns[$jabber] = self::IM_AVAILABLE;
				 	break;
				case 'Offline':
				 	$sns[$jabber] = self::IM_OFFLINE;
				 	break;
				default:
					$sns[$jabber] = self::IM_UNKNOWN;
			}
		}
		return $sns;
	}

	function icq_statuses($sns) {
		foreach(array_keys($sns) as $icq) {
			$server = 'status.icq.com';
			$url = '/online.gif?icq=' . $icq . '&img=1';
			$fp = @fsockopen($server, 80, $errno, $errstr, 90);
			if ($fp) {
				socket_set_blocking($fp, 1);

				$data = '';
				fputs($fp,
				  'HEAD ' . $url . ' HTTP/1.1' . "\r\n" .
				  'Host: ' . $server . "\r\n\r\n");
				do {
					$data = fgets($fp, 1024);
					if (strstr($data, '404 Not Found')) {
						break;
					}
				} while(strstr($data, 'Location: /') === false && !feof($fp));
				fclose($fp);
			}
			switch(substr($data, -7, 1)) {
				case 0:
					$sns[$icq] = self::IM_OFFLINE;
					break;
				case 1:
					$sns[$icq] = self::IM_AVAILABLE;
					break;
				default: //404
					$sns[$icq] = self::IM_UNKNOWN;
			}
		}	
		return $sns;
	}
	function yahoo_statuses($sns) {
		foreach(array_keys($sns) as $yahoo) {
			$fp = @fopen('http://mail.opi.yahoo.com/online?m=t&t=1&u=' . $yahoo, 'r');
			$response = '';
			if ($fp) {
				do {
					$response .= fread($fp, 128);
				} while (!feof($fp));
				fclose($fp);
			}
			switch($response) {
				case '00':
					$sns[$yahoo] = self::IM_OFFLINE;
					break;
				case '01':
					$sns[$yahoo] = self::IM_AVAILABLE;
					break;
				default:
					$sns[$yahoo] = self::IM_UNKNOWN;
			}
		}
		return $sns;
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

	public static function sort_name($a, $b) {
		return strcmp($a->name, $b->name);
	}

	public static function sort_teacher($a, $b) {
		return strcmp($a->teacher->name_comma, $b->teacher->name_comma);
	}

	public static function sort_period($a, $b) {
		$tem = strcmp($a->period, $b->period);
		if($tem == 0)
			//sub-sort by term
			return sort_term($a, $b);
		return $tem;
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
