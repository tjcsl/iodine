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
		global $I2_USER, $I2_ARGS, $I2_SQL;
		$curtime  =time();
		if(!isset($_GET['startdate'])) {
			$starttime=$curtime;
			$starttime=$starttime-date('w',$starttime)*60*60*24;
			$startdate=date("Y-m-d",$starttime);
		} else {
			$starttime=strtotime($_GET['startdate'],$curtime);
			$starttime=$starttime-date('w',$starttime)*60*60*24;
			$startdate=date("Y-m-d",$starttime);
		}
		$endtime =$starttime+24*60*60*7*5;
		$enddate =date("Y-m-d",$endtime);
		$this->template_args['startdate']=$startdate;
		$this->template_args['enddate']=$enddate;

		$data = $I2_SQL->query('SELECT * FROM calendar')->fetch_all_arrays_keyed_list('day',MYSQL_ASSOC);
		$weeks=array();
		$thisdate=$starttime;
		for($i=0;$i<5;$i++) {
			$weeks[]=array();
			for($j=0;$j<7;$j++) {
				$weeks[$i][]=array();
				$weeks[$i][$j]['day']=date("j",$thisdate);
				$weeks[$i][$j]['monthodd']=(date("n",$thisdate)%2)*35+220;
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
	* Add an event
	*/
	static function add_event($eventid, $datestamp, $title, $text) {
		global $I2_SQL;
		if(Calendar::event_exists($eventid)) {
			d("Event already exists, skipping...",5);
			return false;
		}
		$I2_SQL->query("INSERT INTO calendar (id,day,text,title) VALUES (%s,%s,%s,%s)",$eventid,date("Y-m-d",$datestamp),$text,$title);
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
	static function modify_event($eventid,$datestamp,$title,$text) {
		global $I2_SQL;
		if(!Calendar::event_exists($eventid)) {
			d("Event doesn't exist, skipping...",5);
			return false;
		}
		$I2_SQL->query("UPDATE calendar SET day=%s,text=%s,title=%s WHERE id=%s",date("Y-m-d",$datestamp),$text,$title,$eventid);
		return true;
	}
}
?>
