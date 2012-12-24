<?php
/**
* A semi-public-access calendar.
* @package modules
* @subpackage Docs
*/
class Calendar implements Module {

	/**
	* The display object to use
	*/
	private $display;

	/**
	* Template for the specified action
	*/
	private $template;

	/**
	* Arguments for the template
	*/
	private $template_args = array();

	/**
	* Declaring some global variables
	*/
	private $message;

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function init_mobile() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_mobile($disp) {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*/
	function init_cli() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_cli($disp) {
		return FALSE;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function api($disp) {
		return false;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*/
	function api_build_dtd() {
		return false;
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_USER, $I2_ARGS;

		if (count($I2_ARGS) <= 1) {
			$I2_ARGS[1] = 'view';
		}

		$method = $I2_ARGS[1];
		if (method_exists($this, $method)) {
			$this->$method();
			$this->template_args['method'] = $method;
			return 'Calendar: ' . ucwords(strtr($method,'-',' '));
		}
		else {
			redirect('calendar');
		}
	}

	function display_pane($display) {
		$display->disp($this->template, $this->template_args);
	}

	function get_name() {
		return 'Calendar';
	}

	function init_box() {
		return FALSE;
	}

	function display_box($display) {
	}

	/**
	* View the files you are authorized to access
	*/


	function view() {
		global $I2_USER, $I2_ARGS, $I2_SQL, $I2_ROOT, $I2_QUERY;
		$curtime  =time();
		if(!isset($I2_QUERY['startdate'])) {
			$starttime=$curtime;
			$starttime=$starttime-date('w',$starttime)*60*60*24;
			$startdate=date("Y-m-d",$starttime);
		} else {
			$starttime=strtotime($I2_QUERY['startdate'],$curtime);
			$starttime=$starttime-date('w',$starttime)*60*60*24;
			$startdate=date("Y-m-d",$starttime);
		}
		$endtime =$starttime+24*60*60*7*5;
		$enddate =date("Y-m-d",$endtime);
		$this->template_args['startdate']=$startdate;
		$this->template_args['enddate']=$enddate;

		$data_time = $I2_SQL->query('SELECT * FROM calendar WHERE blocknottime=0')->fetch_all_arrays(MYSQL_ASSOC);
		$data_block = $I2_SQL->query('SELECT * FROM calendar WHERE blocknottime=1')->fetch_all_arrays_keyed_list('blockdate',MYSQL_ASSOC);

		$blocksraw = $I2_SQL->query('SELECT * FROM calendar_schedule')->fetch_all_arrays(MYSQL_ASSOC);
		$blocks = array();
		foreach($blocksraw as $block) {
			$blocklist = explode('|',$block['blocksarray']);
			//print_r($blocklist);
			$addarray=array();
			foreach($blocklist as $shard) {
				$blockparts = explode(',',$shard);
				$ret=array();
				$ret['name']=$blockparts[0];
				//echo "(".$block['day'].' '.$blockparts[1].")\r\n";
				$ret['starttime']=strtotime($block['day'].' '.$blockparts[1]);
				$ret['endtime']=strtotime($block['day'].' '.$blockparts[2]);
				$addarray[]=$ret;
			}
			$blocks[$block['day']]=$addarray;
		}
		$data = array();
		foreach($data_time as $row) {
			$data[date("Y-m-d",strtotime($row['starttime']))][]=array('title'=>$row['title'],'id'=>$row['id'],'text'=>$row['text']);
		}
		//print_r($data);
		//print_r($blocks);
		// TODO: Get working
		/*if(isset($I2_USER) && $I2_USER->iodineUIDNumber !=9999) {
			$userdata = $I2_SQL->query('SELECT * FROM calendar_user WHERE uid=%s',$I2_USER->iodineUIDNumber)->fetch_all_arrays_keyed_list('day',MYSQL_ASSOC);
			$data = array_merge($data,$userdata);
			$this->template_args['extraline']='';
		} else {
			$this->template_args['extraline']='<link type="text/css" rel="stylesheet" href="'.$I2_ROOT.'www/extra-css/defaultnoauth.css" />';
		}*/
		
		

		$weeks=array();
		$thisdate=$starttime;
		for($i=0;$i<5;$i++) {
			$weeks[]=array();
			for($j=0;$j<7;$j++) {
				$weeks[$i][]=array();
				$weeks[$i][$j]['day']=date("j",$thisdate);
				$weeks[$i][$j]['monthodd']=date("n",$thisdate)%2;
				/*$text="";
				if(isset($data[date("Y-m-d",$thisdate)])) {
					foreach($data[date("Y-m-d",$thisdate)] as $row) {
						$text.=$row['title']."<br />";
					}
				}*/
				if(isset($data[date("Y-m-d",$thisdate)])) {
					$weeks[$i][$j]['info']=$data[date("Y-m-d",$thisdate)];
				} else {
					$weeks[$i][$j]['info']=array(array('text'=>'hi!','title'=>'','id'=>0));
				}
				$thisdate+=24*60*60;
			}
		}
		$this->template_args['weeks']=$weeks;
		$this->template = 'view.tpl';
	}

	/**
	* Add an event, interface
	*/

	function add() {
		global $I2_USER, $I2_ARGS, $I2_SQL;
		$this->template_args['error']='';
		if(isset($_POST['action'])) {
			//$allowed=self::get_allowed_targets();
			$obid=time();
			if($_POST['blocknottime'] && $_POST['blocknottime']=='true') { // Locked to a block for start and end
				self::add_event('manualevent_'.$obid,True, $_POST['justthedate'], array($_POST['startblock'],$_POST['endblock']), $_POST['title'], $_POST['text'],'');
			} else { // Locked to a particular time
				self::add_event('manualevent_'.$obid,False, $_POST['starttime'], $_POST['endtime'], $_POST['title'], $_POST['text'],'');
			}
			/*foreach($_POST['add_groups'] as $group) {
				if(!in_array($group,$allowed)) {
					$this->template_args['error'].="Can't post to ".$group."<br />";
					return;
				}
				if($group=='self') {
					self::add_user_event('userevent_'.$I2_USER->iodineUIDNumber.$obid,strtotime($_POST['time']),$_POST['title'],$_POST['text'],$I2_USER->iodineUIDNumber);
				} else {
					//self::add_event('manualevent_'.$obid,strtotime($_POST['time']),$_POST['title'],$_POST['text']);
					if($_POST['isablock'] && $_POST['isablock']==True) { // Locked to a block for start and end
						self::add_event('manualevent_'.$obid,True, $_POST['date'], array($_POST['startblock'],$_POST['endblock']), $_POST['title'], $_POST['text'],$_POST['tags']);
					} else { // Locked to a particular time
						self::add_event('manualevent_'.$obid,False, $_POST['starttime'], $_POST['endtime'], $_POST['title'], $_POST['text'],$_POST['tags']);
					}
				}
			}*/
			redirect('calendar');
			return;
		} else {
			$this->template_args['groups']=self::get_allowed_targets();
		}
		$this->template='add.tpl';
	}

	/**
	* Get the groups a user can post to.
	*/
	static function get_allowed_targets() {
		global $I2_USER, $I2_SQL;
		$ret=array('self');
		if($I2_USER->is_group_member('admin_calendar'))
			$ret[]='1';
		//$I2_SQL->query('SELECT * FROM calendar_permissions_groups')->fetch_all_rows();
		return $ret;
	}
	/**
	* Add a user event
	*/
	static function add_user_event($eventid, $datestamp, $title, $text, $uid) {
		global $I2_SQL;
		if(Calendar::user_event_exists($eventid)) {
			d("Event already exists, skipping...",5);
			return false;
		}
		$I2_SQL->query("INSERT INTO calendar_user (id,day,text,title,uid) VALUES (%s,%s,%s,%s,%s)",$eventid,date("Y-m-d",$datestamp),$text,$title,$uid);
		return true;
	}
	/**
	* Check if a user event exists
	*/
	static function user_event_exists($eventid,$uid) {
		global $I2_SQL;
		$data=$I2_SQL->query("SELECT * FROM calendar_user WHERE id=%s and uid=%s",$eventid,$uid)->fetch_all_arrays();
		return count($data)>0;
	}
	/**
	* Remove a user event
	*/
	static function remove_user_event($eventid, $uid) {
		global $I2_SQL;
		if(!is_string($eventid)) {
			throw new I2Exception("Non-string event id passed to Calendar's remove_event!");
			return false;
		}
		$I2_SQL->query("DELETE FROM calendar_user WHERE id=%s and uid=%s",$eventid,$uid);
		return true;
	}
	/**
	* Modify a user event
	*/
	static function modify_user_event($eventid,$datestamp,$title,$text,$uid) {
		global $I2_SQL;
		if(!Calendar::user_event_exists($eventid,$uid)) {
			d("Event doesn't exist, skipping...",5);
			return false;
		}
		$I2_SQL->query("UPDATE calendar_user SET day=%s,text=%s,title=%s,uid=%s WHERE id=%s",date("Y-m-d",$datestamp),$text,$title,$uid,$eventid);
		return true;
	}
	/**
	* Add an event
	*/
	static function add_event($eventid,$isablock, $firsttimearg, $secondtimearg, $title, $text,$tags) {
		global $I2_SQL;
		if(Calendar::event_exists($eventid)) {
			d("Event already exists, skipping...",5);
			return false;
		}
		if(is_string($firsttimearg)) {
			$firsttimearg=strtotime($firsttimearg,time());
			echo $firsttimearg;
		}
		if(is_string($secondtimearg)) {
			$secondtimearg=strtotime($secondtimearg,time());
			echo $secondtimearg;
		}
		if(!is_bool($isablock)) {
			throw new I2Exception("Invalid argument type! (isabool)");
			return false;
		}
		if(is_array($tags)) {
			$tags=implode(" ",$tags);
		}
		if($isablock) { // Timeslot is locked to a block, as opposed to an actual time.
			$I2_SQL->query("INSERT INTO calendar (id,blocknottime,blockdate,startblock,endblock,title,text,tags) VALUES (%s,%i,%s,%s,%s,%s,%s,%s)",$eventid,1,date("Y-m-d",$firsttimearg),$secondtimearg[0],$secondtimearg[1],$title,$text,$tags);
		} else { // Timeslot is locked to an actual start and end time
			$I2_SQL->query("INSERT INTO calendar (id,blocknottime,starttime,endtime,title,text,tags) VALUES (%s,%i,%s,%s,%s,%s,%s)",$eventid,0,date("Y-m-d H:i:s",$firsttimearg),date("Y-m-d H:i:s",$secondtimearg),$title,$text,$tags);
		}
		return true;
	}
	/**
	* Check if an event exists
	*/
	static function event_exists($eventid) {
		global $I2_SQL;
		$data=$I2_SQL->query("SELECT * FROM calendar WHERE id=%s",$eventid)->fetch_all_arrays();
		return count($data)>0;
	}
	/**
	* Remove an event
	*/
	static function remove_event($eventid) {
		global $I2_SQL;
		if(!is_string($eventid)) {
			throw new I2Exception("Non-string event id passed to Calendar's remove_event!");
			return false;
		}
		$I2_SQL->query("DELETE FROM calendar WHERE id=%s",$eventid);
		return true;
	}
	/**
	* Modify an event
	*/
	static function modify_event($eventid, bool $isablock, $firsttimearg, $secondtimearg, $title, $text) {
		global $I2_SQL;
		if(!Calendar::event_exists($eventid)) {
			d("Event doesn't exist, skipping...",5);
			return false;
		}
		if(!is_bool($isablock)) {
			throw new I2Exception("Invalid argument type! (isablock)");
			return false;
		}
		if($isablock) { // Timeslot is locked to a block, as opposed to an actual time.
			$I2_SQL->query("UPDATE calendar SET blocknottime=1, blockdate=%s, startblock=%s, endblock=%s, title=%s, text=%s WHERE id=%s",date("Y-m-d",$firsttimearg),$secondtimearg[0],$secondtimearg[1],$title,$text,$tags,$eventid);
		} else { // Timeslot is locked to an actual start and end time
			$I2_SQL->query("UPDATE calendar SET blocknottime=0, starttime=%s, endtime=%s, title=%s, text=%s WHERE id=%s",date("Y-m-d H:i:s",$firsttimearg),date("Y-m-d H:i:s",$secondtimearg),$title,$text,$eventid);
		}
		return true;
	}
	/**
	* Add tag to an event
	*/
	static function set_tags($eventid,$tags) {
		global $I2_SQL;
		if(!Calendar::event_exists($eventid)) {
			d("Event doesn't exist, skipping...",5);
			return false;
		}
		if(!is_array($tags)) {
			d("Tags have to be strings, skipping...",5);
			return false;
		}
		//init_kittens();
		$I2_SQL->query("UPDATE calendar SET tags=%s WHERE id=%s",implode(" ",$tags));
		return true;
	}
	/**
	* Add tag to an event
	*/
	static function add_tag($eventid,$tag) {
		global $I2_SQL;
		if(!Calendar::event_exists($eventid)) {
			d("Event doesn't exist, skipping...",5);
			return false;
		}
		if(!is_string($tag)) {
			d("Tags have to be strings, skipping...",5);
			return false;
		}
		$oldtags = $I2_SQL->query("SELECT tags FROM calendar WHERE id=%s",$eventid)->fetch_single_value();
		$I2_SQL->query("UPDATE calendar SET tags=%s WHERE id=%s",$oldtags." ".$tag);
		//Commented out for speed.
		//$I2_SQL->raw_query("CREATE TABLE kittens (varchar(30) name, PRIMARY KEY('name'),INTEGER fluffiness)");
		return true;
	}
	/**
	* Remove a tag from an event
	*/
	static function remove_tag($eventid,$tag) {
		global $I2_SQL;
		if(!Calendar::event_exists($eventid)) {
			d("Event doesn't exist, skipping...",5);
			return false;
		}
		if(!is_string($tag)) {
			d("Tags have to be strings, skipping...",5);
			return false;
		}
		$oldtags = $I2_SQL->query("SELECT tags FROM calendar WHERE id=%s",$eventid)->fetch_single_value();
		$I2_SQL->query("UPDATE calendar SET tags=%s WHERE id=%s",preg_replace(array("/".$tag." /","/ ".$tag."/","/^$tag$/"),"",$oldtags)); // KITTENS!!!!!
		return true;
	}
}
?>
