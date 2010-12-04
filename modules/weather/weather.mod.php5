<?php
/**
* A module that allows persistent editable text across pages.
* @package modules
* @subpackage weather
*/

class Weather implements Module {

	private $template_args = array();
	private $data;

	private function makeData($server = 'weather.tjhsst.edu', $port=8889) {
		global $I2_CACHE;
		$args=unserialize($I2_CACHE->read('Weather','template_args'));
		if($args) {
			$this->template_args = $args;
			return;
		}
		$this->template_args=unserialize($I2_CACHE->read($this,'template_args'));
		if($this->template_args===false) {
			$connection = @fsockopen($server, $port, $errno, $errstr);
			// We don't assume that weather is always up.
			if (!$connection) {
				$this->template_args['data'] = 0;
				$this->template_args['temperature'] = 0;
				$this->template_args['windchill'] = 0;
				$this->template_args['humidity'] = 0;
				$this->template_args['barometer'] = 0;
				$this->template_args['bar_fall'] = 'steady';
				$this->template_args['wind'] = 0;
				$this->template_args['wind_dir'] = 0;
				$this->template_args['rain'] = 0;
				$this->template_args['rain_int'] = 0;
				$this->template_args['error'] = "It appears that the weather station is currently unavailable. We apologize for the inconvenience.";
				return;
			}
			$datum = '';
			while (!feof($connection))
				$datum .= fgets($connection,100);
			fclose($connection);
			$this->data = explode(',',$datum);
			$this->template_args['data'] = $this->data;
			$this->template_args['temperature'] = $this->data[7];
			$this->template_args['windchill'] = $this->data[53];
			$this->template_args['humidity'] = $this->data[19];
			$this->template_args['barometer'] = $this->data[25];
			if ($this->data[26] < 0)
				$this->template_args['bar_fall'] = 'falling';
			else if ($this->data[26] > 0)
				$this->template_args['bar_fall'] = 'rising';
			else
				$this->template_args['bar_fall'] = 'steady';
			$this->template_args['wind'] = $this->data[37];
			$this->template_args['wind_dir'] = $this->data[38];
			$this->template_args['rain'] = $this->data[47];
			$this->template_args['rain_int'] = $this->data[50];
			$I2_CACHE->store('Weather','template_args',serialize($this->template_args),60*5);
		}
	}

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
	* Build the dataset for the cli
	*/
	function init_cli() {
		$this->makeData();
		return "weather";
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_cli($disp) {
		if( isset($this->template_args['error']) ) {
			return "<div>".$this->template_args['error']."</div>";
		}
		$str ="<div>\n";
		$str.="Current temperature is {$this->template_args['temperature']}<br />\n";
		$str.="Rain accumulation is {$this->template_args['rain']} inch(es)";
		return $str;
	}

	public function init_box() {
		$this->makeData();
		return 'Current Weather at TJ';
	}

	public function display_box($disp) {
		$disp->disp('weather_box.tpl', $this->template_args);
	}
	
	/**
	* I2_ARGS accepted:
	*/
	public function init_pane() {
		return FALSE;
	}
	
	function display_pane($disp) {
		global $I2_ARGS;
	}

	function get_name() {
		return 'weather';
	}

	function is_intrabox() {
		return true;
	}
}
?>
