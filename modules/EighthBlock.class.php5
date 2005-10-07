<?php
/**
* Just contains the definition for the class {@link EighthBlock}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Eighth
* @filesource
*/

/**
* The module that holds the definition for an eighth period block.
* @package modules
* @subpackage Eighth
*/

class EighthBlock {

	private $data = array();

	/**
	* The constructor for the {@link EighthBlock} class.
	*
	* @access public
	* @param int $activityid The activity ID.
	*/
	public function __construct($blockid) {
		global $I2_SQL;
		$this->data = $I2_SQL->query("SELECT * FROM eighth_blocks WHERE bid=%d", $blockid)->fetch_array(MYSQL_ASSOC);
	}

	/**
	* Adds a block to the list.
	*
	* @access public
	* @param string $date The date of the block.
	* @param string $block The block letter (A or B).
	*/
	public static function add_block($date, $block) {
		global $I2_SQL;
		$result = $I2_SQL->query("INSERT INTO eighth_blocks (date,block) VALUES (%t,%s)", $date, $block);
		$uids = flatten($I2_SQL->query("SELECT uid FROM user")->fetch_all_arrays(MYSQL_NUM));
		// Figure out what the default should be, 999?
		$block = new Block($result->get_insert_id());
		EighthSchedule::schedule_activity($block->bid, 1);
		$activity = new EighthActivity(1, $block->bid);
		$activity->add_members($users);
		return $result->get_insert_id();
	}

	/**
	* Removes a block from the list.
	*
	* @access public
	* @param int $blockid The block ID.
	*/
	public static function remove_block($blockid) {
		global $I2_SQL;
		$result = $I2_SQL->query("DELETE FROM eighth_blocks WHERE bid=%d", $blockid);
		// TODO: Deal with removing a block
	}

	/**
	* Removes this block from the list.
	*
	* @access public
	*/
	public function remove() {
		$this->remove_block($this->data['bid']);
	}

	/**
	* Gets a list of all the blocks.
	*
	* @access public
	* @param string $starting_date The starting date for the list, usually NULL.
	* @param int $number_of_days The number of days to return.
	*/
	public static function get_all_blocks($starting_date = NULL, $number_of_days = 14) {
		global $I2_SQL;
		if($starting_date == NULL) {
			$starting_date = i2config_get('start_date', date("Y-m-d"), 'eighth');
		}
		return $I2_SQL->query("SELECT * FROM eighth_blocks WHERE date >= %t AND date <= ADDDATE(%t, INTERVAL %d DAY) ORDER BY date,block", $starting_date, $starting_date, $number_of_days)->fetch_all_arrays(MYSQL_ASSOC);
	}

	/**
	* Gets the schedule for a particular activity.
	*
	* @access public
	* @param int $activityid The activity ID.
	* @param string $starting_date The starting date for the list, usually NULL.
	* @param int $number_of_days The number of days to return.
	*/
	public static function get_activity_schedule($activityid, $starting_date = NULL, $number_of_days = 14) {
		global $I2_SQL;
		$blocks = self::get_all_blocks($starting_date, $number_of_days);
		$activities = array();
		foreach($blocks as $block) {
			$result = $I2_SQL->query("SELECT rooms,sponsors from eighth_block_map WHERE bid=%d AND activityid=%d", $block['bid'], $activityid);
			if($result->num_rows() == 0) {
				$result = $I2_SQL->query("SELECT rooms,sponsors FROM eighth_activities WHERE aid=%d", $activityid);
			}
			$activities[] = array("block" => $block) + $result->fetch_array(MYSQL_ASSOC);
		}
		return $activities;
	}

	/**
	* The magic __get function.
	*
	* @access public
	* @param string $name The name of the field to get.
	*/
	public function __get($name) {
		if(array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}
	}

	/**
	* The magic __set function.
	*
	* @access public
	* @param string $name The name of the field to set.
	* @param mixed $value The value to assign to the field.
	*/
	public function __set($name, $value) {
		global $I2_SQL;
		if($name == "date") {
			$result = $I2_SQL->query("UPDATE eighth_blocks SET date=%t WHERE bid=%d", $value, $this->data['bid']);
			$this->data['date'] = $value;
		}
		else if($name == "block") {
			$result = $I2_SQL->query("UPDATE eighth_blocks SET block=%s WHERE bid=%d", $value, $this->data['bid']);
			$this->data['block'] = $value;
		}
		else if($name == "locked") {
			$result = $I2_SQL->query("UPDATE eighth_blocks SET locked=%d WHERE bid=%d", (int)$value, $this->data['bid']);
			$this->data['locked'] = (bool)$value;
		}
	}
}

?>
