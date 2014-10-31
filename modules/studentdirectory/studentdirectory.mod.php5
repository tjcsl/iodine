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
class StudentDirectory extends Module {
	
	private $template;
	private $template_args = [];
		
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
		global $I2_ROOT,$I2_ARGS,$I2_USER,$I2_LDAP,$I2_QUERY;

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
				if( !isset($I2_QUERY['q']) || $I2_QUERY['q'] == '') {
					$this->template = 'studentdirectory_help.tpl';
					return array('Directory Help', 'Searching Help');
				} else {
					$info = $I2_USER->search_info($I2_QUERY['q']);
					
					if( count($info) == 1 ) {
						redirect('studentdirectory/info/'.$info[0]->uid);
					} 
					$this->template_args['info'] = $info;
					$fp=fopen('/tmp/i2srclog',"a");
					fwrite($fp,$I2_USER->iodineUID.' -> '.$I2_QUERY['q']);
					fclose($fp);
					$this->template_args['numresults'] = count($info);
					$this->template_args['query']=$I2_QUERY['q'];
					$this->template_args['math_eval']=$this->math_eval($I2_QUERY['q']);
					$this->template = 'search.tpl';
					return array('Directory search results for "'.$I2_QUERY['q'].'"', 'Search results for "'.$I2_QUERY['q'].'"');
				}
				break;
			case 'class':
				if(!isset($I2_ARGS[2])) {
					redirect();
				}
				$sec = Schedule::section($I2_ARGS[2]);
				$students = $sec->get_students();
				
				$aim_sns = Array();
				foreach ($students as $student) {
					$aim = $student->aim;
					if (! empty($aim)) {
						if (gettype($aim) == "array") {
							$aim_sns = array_merge($aim_sns, array_values($aim));
						} else {
							$aim_sns[] = $aim;
						}
					}
				}
				if(!empty($aim_sns)) {
					$aim_sns = $this->aim_statuses(array_flip($aim_sns));
				}
				
				$this->template = 'class.tpl';
				$this->template_args['class'] = $sec;
				$this->template_args['students'] = $students;
				$this->template_args['im_icons'] = $I2_ROOT . 'www/status/';
				$this->template_args['aim'] = $aim_sns;
				return "Students in {$sec->name}, Period {$sec->periods}";
			case 'section':
				if (isset($I2_ARGS[2])) {
					$classid = $I2_ARGS[2];
				} else {
					$classid = NULL;
				}
				$sectionids = Schedule::sections($classid);
				$classes = [];
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
				$classes = [];
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
			case 'preview':
				try {
					$I2_LDAP = LDAP::get_generic_bind();
				} catch( I2Exception $e) {
					d("Generic bind failed, trying anonymous...",1);
					$I2_LDAP = LDAP::get_anonymous_bind();
				}
				//fall through
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


				$this->template_args['maillist'] = [];
				if($I2_USER->grade == "staff" && $user->grade != "staff") {
					$this->template_args['maillist'][] = $user->get_tjmail();
				}
				$tempmaillist = $user->mail;
				if(is_array($tempmaillist)) {
					foreach($tempmaillist as $email) {
						$this->template_args['maillist'][] = str_replace("?","",$email);
					}
				} else {
					$this->template_args['maillist'][]=str_replace("?","",$tempmaillist);
				}
				
				$eighth = EighthActivity::id_to_activity(EighthSchedule::get_activities($user->uid));
				$eighthhosting = EighthActivity::id_to_activity(EighthSchedule::get_activities_sponsored($user->uid));

				$this->template = 'studentdirectory_pane.tpl';
				$this->template_args['schedule'] = $sched;
				$this->template_args['user'] = $user;
				$this->template_args['im_sns'] = $im_sns;
				$this->template_args['im_networks'] = $im_networks;
				$this->template_args['im_icons'] = $I2_ROOT . 'www/status/';
				$this->template_args['eighth'] = $eighth;
				$this->template_args['eighthhosting'] = $eighthhosting;
				
				if($I2_ARGS[1] == "preview")
					$this->template_args['homecoming_may_vote'] = false;// Don't show this in the preview
				else
					$this->template_args['homecoming_may_vote'] = Homecoming::user_may_vote($user);
				$this->template_args['im_an_admin'] = Group::admin_all()->has_member($I2_USER);
				$this->template_args['is_admin'] = Group::admin_all()->has_member($user);
				$this->template_args['sex'] = $user->sex;
				$this->template_args['mode'] = $mode;

				return Array('Directory: '.$user->fname.' '.$user->lname, $user->fname.' '.$user->lname);
			default:
				return Array('Error', 'Error: User does not exist');
				
		}
	}

    function api_entry($k, $v) {
        global $I2_API;
        if(is_object($v) || is_array($v)) {
            try {
                $v = (array)$v;
                @$I2_API->startElement("".$k);
                foreach($v as $w=>$x) {
                    @$I2_API->writeElement($w, $x);
                }
                $I2_API->endElement();
            } catch(Exception $e) {}
        } else {
            @$I2_API->writeElement($k, $v);
        }
    }

    function api() {
        global $I2_API, $I2_ARGS, $I2_QUERY, $I2_USER;
        if(!isset($I2_ARGS[1])) {
            throw new I2Exception("Argument needed");
        }
        switch($I2_ARGS[1]) {
            case 'info':
                if(!isset($I2_ARGS[2])) $user = $I2_USER;
                else $user = new User($I2_ARGS[2]);
                $uid = $user->uid;
                $eighth = EighthActivity::id_to_activity(EighthSchedule::get_activities($uid));
                $eighthhosting = EighthActivity::id_to_activity(EighthSchedule::get_activities_sponsored($uid));

                $I2_API->startElement("info");
               // $I2_API->writeElement("d", print_r($user,1));
                $user = (array)$user;
                foreach($user as $k=>$v) {
                    self::api_entry($k, $v);
                }
                break;
            
            case 'search':
                if(!isset($I2_ARGS[2])) {
                    if(!isset($I2_QUERY['q'])) {
                        throw new I2Exception("No search query given");
                    } else $q = $I2_QUERY['q'];
                } else $q = $I2_ARGS[2];

                $I2_API->startElement('search');
                $I2_API->writeAttribute("q", $q);

                $info = $I2_USER->search_info($q);
                //$I2_API->writeElement('d', print_r($info,1));
                foreach($info as $entry) {
                    $I2_API->startElement("result");
                    $I2_API->writeElement("entry", print_r($entry,1));
                    $entry = (array)$entry;
                    foreach($entry as $k => $v) {
                        self::api_entry($k, $v);
                    }
                    $I2_API->endElement();
                }
            break;

            default:
                throw new I2Exception("Invalid submodule");
        }
    }
	
	function math_eval($input) { //TODO: Parse mathematical stuff, return output (or false if not valid)
		return false;
	}

	function logic_eval($input) {
		// http://en.wikipedia.org/wiki/Logical_connective
		$vartable = []; //TODO: finish parse_logicelement, make display nicer
		if(has_unlogical($input))
			return false;
		$rootnode = parse_logicelement($input,$vartable);
		if($rootnode==false)
			return false;
		$numvars=count($vartable);
		if($numvars==0)
			return $rootnode->evaluate();
		if($numvars>4) //Sanity cap
			return false;
		for($i=2^$numvars;$i<2^($numvars+1);$i++) {
			$varstr=decbin($i);
			for($j=1;$j<=$numvars;$j++) {
				$vartable[$j-1]=$varstr[$j];
			}
			$output[]=$rootnode->evaluate();
		}
		$numtopvars=floor($numvars/2);
		$numleftvars=ceil($numvars/2);
		$retstring="<table><tr><td colspan=".($numleftvars*2)." rowspan=".($numtopvars*2)."> </td>";
		$vars=array_keys($vartable);
		for($i=0;$i<$numtopvars;$i++) {
			for($j=0;$j<2^$i;$j++)
				$retstring.="<th colspan=".(($numtopvars-$i)*2).">".($vars[$i])."</th>";
			$retstring.="</tr><tr>";
			for($j=0;$j<2^$i;$j++)
				$retstring.="<th colspan=".(($numtopvars-$i-1)*2).">0</th><th colspan=".(($numtopvars-$i-1)*2).">1</th>";
		}
		for($i=0;$i<$numleftvars;$i++)
			for($j=0;$j<$numtopvars;$j++) {
				$retstring.="<td>".$output[$i*$numtopvars+$j]."</td>";
			}
		return $retstring;
	}

	function has_unlogical($input) {
		foreach(explode("",$input) as $char) {
			if($char!='1'&&$char!='0'&&!in_array($char,logicelement::onearg)&&!in_array($char,logicelement::twoarg))
				return true;
		}
		return false;
	}

	function parse_logicelement($input,&$vartable) {
		$temparg1=null;
		$temparg2=null;
		$i=0;
		//TODO: MAKE THIS WORK
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
		if (isset($fp) && $fp) {
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
			$sns[$aim] = isset($statuses[$aimid]) ? $statuses[$aimid] : self::IM_UNKNOWN;
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
			if (isset($file) && $file) {
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
	function display_pane($disp) {
		$disp->disp($this->template, $this->template_args);
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
	function display_box($disp) {
		$template_args['suggestenabled']=false;
		$disp->disp('studentdirectory_box.tpl',$template_args);
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
class logicelement {
	private $type;
	private $arg1;
	private $arg2;
	private $vartable;
	public static $onearg = array('N');
	public static $twoarg = array('I','F','G','K','D','A','X','L','C','M','B','J','E');
	function __construct($type,$arg1,$arg2,&$vartable) {
		$this->type=$type;
		if(ctype_lower($type)) {
			if(!array_key_exists($type,$vartable))
				$vartable[$type]=false;
		} elseif(in_array($type,$onearg)) {
			$this->arg1=$arg1;
		} elseif(in_array($type,$twoarg)) {
			$this->arg1=$arg1;
			$this->arg2=$arg2;
		}
		$this->vartable=$vartable;
	}
	function evaluate() {
		if(ctype_lower($this->type))
			return $this->vartable[$this->type];
		if($type=='1')
			return true;
		if($type=='0')
			return false;
		switch($this->type) {
			case 'N':
				return !$this->arg1->evaluate();
			case 'O':
				return false;
			case 'V':
				return true;
			case 'I':
				return $this->arg1->evaluate();
			case 'F':
				return !$this->arg1->evaluate();
			case 'H':
				return $this->arg2->evaluate();
			case 'G':
				return !$this->arg2->evaluate();
			case 'K':
				return $this->arg1->evaluate() && $this->arg2->evaluate();
			case 'D':
				return !($this->arg1->evaluate() && $this->arg2->evaluate());
			case 'A':
				return $this->arg1->evaluate() || $this->arg2->evaluate();
			case 'X':
				return !($this->arg1->evaluate() || $this->arg2->evaluate());
			case 'L':
				return $this->arg1->evaluate() && (!$this->arg2->evaluate());
			case 'C':
				return !($this->arg1->evaluate() && (!$this->arg2->evaluate()));
			case 'M':
				return !($this->arg1->evaluate() || (!$this->arg2->evaluate()));
			case 'B':
				return $this->arg1->evaluate() || (!$this->arg2->evaluate());
			case 'J':
				return $this->arg1->evaluate() xor $this->arg2->evaluate();
			case 'E':
				return !($this->arg1->evaluate() xor $this->arg2->evaluate());
		}
	}
}

?>
