<?php
/**
* Just contains the definition for the class {@link EventBlock}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Events
* @filesource
*/

/**
* @package modules
* @subpackage Events
*/
class EventBlock {

	/**
	 * The id number for this event.
	 */
	private $mybid;

	private $info = array();

	/**
	 * The php magical __get method.
	 *
	 * @access public
	 * @param mixed $var The field for which to get data.
	 * @return mixed The requested data.
	 */
	public function __get($var) {
		global $I2_SQL;

		if(isset($this->info[$var])) {
			return $this->info[$var];
		}

		switch($var) {
			case 'id':
			case 'bid':
				return $this->mybid;
			case 'startdt':
			case 'enddt':
				$this->info[$var] = $I2_SQL->query('SELECT %c FROM event_blocks WHERE id=%d', $var, $this->mybid)->fetch_single_value();
				break;
		}

		if(!isset($this->info[$var])) {
			throw new I2Exception('Invalid attribute passed to EventBlock::__get(): '.$var);
		}

		return $this->info[$var];
	}

	/**
	 * The constructor
	 */
	public function __construct($id) {
		$this->mybid = $id;
	}

	/**
	 * Determine if this block and another block overlap
	 *
	 * @param EventBlock $block The second block for comparison
	 * @return boolean
	 */
	public function overlaps(EventBlock $block) {
		$start1 = strtotime($this->startdt);
		$end1 = strtotime($this->enddt);
		$start2 = strtotime($block->startdt);
		$end2 = strtotime($block->enddt);
		return ($start1 <= $start2 && $end1 > $start2) || ($start1 < $end2 && $end1 >= $end2);
	}

	/**
	 * Create a new block
	 * 
	 * @param int $startdt The beginning datetime of the block
	 * @param int $enddt The ending datetime of the block
	 * @return integer The ID number of the new block
	 */
	public static function create_block($startdt, $enddt) {
		global $I2_SQL, $I2_USER;
		if (!$I2_USER->is_group_member('admin_events')) {
			throw new I2Exception('You are not allowed to create new event blocks!');
		}

		$res = $I2_SQL->query('INSERT INTO event_blocks SET startdt=%s, enddt=%s', $startdt, $enddt);
		return new EventBlock($res->get_insert_id());
	}

	/**
	 * Get all event blocks
	 *
	 * @return array An array of EighthBlock objects representing all existant blocks
	 */
	public static function all_blocks() {
		global $I2_SQL;
		$ret = array();
		$res = $I2_SQL->query('SELECT id FROM event_blocks WHERE startdt>%s', date('Y-m-d H:i:s'));
		foreach ($res->fetch_col('id') as $id) {
			$ret[] = new EventBlock($id);
		}
		return $ret;
	}

	/**
	 * Test if a block exists
	 *
	 * @return boolean
	 */
	public static function block_exists($bid) {
		global $I2_SQL;
		return $I2_SQL->query('SELECT COUNT(*) FROM event_blocks WHERE id=%d', $bid)->fetch_single_value() == 1;
	}
}

?>
