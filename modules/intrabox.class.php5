<?php
/**
* Just contains the definition for the class {@link IntraBox}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2004-2005 The Intranet 2 Development Team
* @package core
* @subpackage Display
* @filesource
*/

/**
* Class for obtaining/displaying/processing IntraBoxes.
*
* @package core
* @subpackage Display
*/
class IntraBox {

	/**
	* The current {@link Module} object, or, if the current intrabox is a
	* 'static' intrabox, a string representing which 'static' intrabox this
	* is.
	*/
	private $module;

	/**
	* The module which is being displayed in the main pane. This is stored
	* so that module isn't unneccessarily instantiated twice, and the same
	* object is used to display the ibox and the main pane.
	*/
	private static $main_module = NULL;

	/**
	* The Display object to display the global intrabox templates on.
	*/
	private static $display = NULL;

	/**
	* The Display object for this particular Intrabox
	*/
	private $mydisplay = NULL;

	const USED = 1;
	const UNUSED = 2;

	/**
	* Constructor, makes the intrabox with name $module_name.
	*
	* This creates an IntraBox object for the module named by $module_name.
	* If $module_name is not an Intranet2 module, then the intrabox created
	* is a 'static' intrabox, which is not represented by any class, but
	* rather by the IntraBox class itself. (One example is the 'links'
	* intrabox, which does not need an entire class for it.)
	*
	* @param string $module_name The name of the module to create an intrabox
	*                            for.
	*/
	public function __construct($module_name) {

		if( self::$main_module && strcasecmp($module_name, self::$main_module->get_name()) == 0 ) {
			$this->module = self::$main_module;
		}
		else {
			if( get_i2module($module_name) ) {
				$mod = new $module_name();
				if( ! in_array( 'Module', class_implements($mod)) ) {
					throw new I2Exception('The class '.$module_name.' was passed as an Intrabox, but it does not implement the Module interface.');
				}
				$this->module = $mod;
			}
			/* for static iboxen */
			else {
				$this->module = $module_name;
			}
		}

		$this->mydisplay = new Display($module_name);
	}

	/**
	* Displays this intrabox.
	*/
	public function display_box() {
		global $I2_ERR;
		if( is_string($this->module) ) {
			$tpl = 'intrabox_'.$this->module.'.tpl';
			
			if( Display::get_template($tpl) ) {
				$this->mydisplay->disp('intrabox_openbox.tpl', array('title' => ucwords($this->module)));
				$this->mydisplay->disp($tpl);
				$this->mydisplay->disp('intrabox_closebox.tpl');
			}
			else {
				throw new I2Exception('Invalid intrabox `'.$this->module.'` was attempted to be displayed.');
			}
		}
		else {
			$name = $this->module->get_name();
			try {
				if( ($title = $this->module->init_box(true)) ) {
					$this->mydisplay->disp('intrabox_openbox.tpl', array('title' => $title));
					try {
						$this->module->display_box($this->mydisplay);
					}
					catch( Exception $e ) {
						$this->mydisplay->disp('intrabox_closebox.tpl');
						throw $e;
					}
					$this->mydisplay->disp('intrabox_closebox.tpl');
				}
			}
			catch( Exception $e ) {
				$I2_ERR->nonfatal_error('There was an error in module `'.$name.'` when trying to create its intrabox: '.$e->__toString());
			}
		}
	}

	/**
	* Displays all intraboxes for the current logged-in user.
	*
	* @param Module $main_module The module that will be used to display
	*                            the main content pane. This is passed so
	*                            that particular module is not instantiated
	*                            twice.
	*/
	public static function display_boxes($main_module) {
		global $I2_USER;
		
		if( self::$main_module === NULL && is_object($main_module) ) {
			self::$main_module = $main_module;
		}
		
		if( self::$display === NULL ) {
			self::$display = new Display('Intrabox');
		}

		self::$display->disp('intrabox_open.tpl');
		
		$b = self::get_user_boxes($I2_USER->uid);
		foreach($b as $mod) {
			//d("Box: $mod");
			$box = new Intrabox($mod);
			$box->display_box();
		}

		self::$display->disp('intrabox_close.tpl');
	}

	/**
	* Gets the current list of intraboxes that a user has set to display.
	*
	* @param int $uid The user ID of the user from which to get the list of
	*                 intraboxes.
	* @return array The list of the names of the intraboxes.
	*/
	public static function get_user_boxes($uid) {
		global $I2_SQL;
		return flatten($I2_SQL->query(	'SELECT intrabox.name FROM intrabox 
					 JOIN intrabox_map USING (boxid) 
					 WHERE intrabox_map.uid=%d 
					 ORDER BY intrabox_map.box_order;'
			,$uid)->fetch_all_arrays(MYSQL_NUM));
	}

	/**
	* Retrieves all available intraboxes from MySQL.
	*	
	* @return array The names of all of the intraboxes that a user can
	*               choose to have.
	*/
	public static function get_all_boxes() {
		global $I2_SQL;
		return flatten($I2_SQL->query('SELECT name FROM intrabox;')->fetch_all_arrays(MYSQL_NUM));
	}

	/**
	* Adds a box to a user's intrabox list.
	*
	* @param int $boxid The ID of the box to add.
	*/
	public static function add_box($boxid) {
		global $I2_SQL,$I2_USER;
		
		//This is possible to do in one query with subqueries in SQL, I believe, but not prior to MySQL 4.1 afaik. If anyone knows of a way to do this in one query, by all means do it
		list($max) = $I2_SQL->query('SELECT MAX(box_order) FROM intrabox_map WHERE uid=%d', $I2_USER->uid)->fetch_array(MYSQL_NUM);
		
		$boxinfo = self::get_boxes_info(self::USED)->fetch_all_arrays(MYSQL_ASSOC);
		
		foreach ($boxinfo as $box) {
			if ($box['boxid'] == $boxid) {
				d("User attempted to re-add box $boxid - ignored.");
				return;	
			}
		}
		
		$I2_SQL->query('INSERT INTO intrabox_map ( uid, boxid, box_order ) VALUES ( %d, %d, %d );', $I2_USER->uid, $boxid, $max+1);
	}

	/**
	* Deletes a box from a user's intrabox list.
	*
	* @param int $boxid The ID of the box to delete.
	*/
	public static function delete_box($boxid) {
		global $I2_SQL,$I2_USER;
		
		if( ! ($res = $I2_SQL->query('SELECT box_order FROM intrabox_map WHERE uid=%d AND boxid=%d;', $I2_USER->uid, $boxid)->fetch_array(MYSQL_NUM)) ) {
			d('The specified intrabox '.$boxid.' was not already selected by the current user, but something asked to delete it. Ignoring this request', 5);
			return;
		}
		$order = $res[0];

		$I2_SQL->query('DELETE FROM intrabox_map WHERE uid=%d AND boxid=%d;', $I2_USER->uid, $boxid);
		$I2_SQL->query('UPDATE intrabox_map SET box_order=box_order-1 WHERE uid=%d AND box_order>%d;', $I2_USER->uid, $order);
	}

	/**
	* Gets information about certain boxes.
	*
	* Gets the boxid and the display name of a certain set of intraboxes. If
	* the Intrabox::USED constant is passed as a parameter, then information
	* about the logged-in user's intraboxes is returned. If Intrabox::UNUSED
	* is passed, then information about intraboxes that the logged-in user
	* has not selected will be returned.
	*
	* The information is returned as a {@link Result} object, with the two
	* columns 'boxid' and 'display_name' containing the information in its
	* rows.
	*
	* @param int $boxes One of the constants in Intrabox, either
	* {@link Intrabox::USED} or {@link Intrabox::UNUSED}.
	* @return Result The information about the requested boxes.
	*/
	public static function get_boxes_info($boxes) {
		global $I2_SQL, $I2_USER;
		if( $boxes == self::USED ) {
			return $I2_SQL->query('
			SELECT intrabox.boxid AS boxid, intrabox.display_name AS display_name
			FROM intrabox JOIN intrabox_map USING (boxid)
			WHERE intrabox_map.uid = %d
			ORDER BY intrabox_map.box_order;', $I2_USER->uid);
		}
		elseif( $boxes == self::UNUSED ) {
			//this is possible with one query with subqueries, but mysql < 4.1 doesn't support them
			//I can't think of a way to do it right now with joins, but if it's possible, then someone do it here
			$ids = array();
			foreach($I2_SQL->query('SELECT boxid FROM intrabox_map WHERE uid=%d;', $I2_USER->uid) as $row) {
				$ids[] = $row[0];
			}
			if (count($ids) == 0) {
				return $I2_SQL->query('
				SELECT DISTINCT intrabox.boxid AS boxid, intrabox.display_name AS display_name
				FROM intrabox LEFT JOIN intrabox_map USING (boxid)
				ORDER BY intrabox.display_name;');
			}
			return $I2_SQL->query('
			SELECT DISTINCT intrabox.boxid AS boxid, intrabox.display_name AS display_name
			FROM intrabox LEFT JOIN intrabox_map USING (boxid)
			WHERE intrabox.boxid NOT IN (%D)
			ORDER BY intrabox.display_name;', $ids);
		}
		else {
			throw new I2Exception('Invalid parameter passed to Intrabox::get_boxes_info()');
		}
	}
	
}

?>
