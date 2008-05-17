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

	private $closed = 0;

	const USED = 1;
	const UNUSED = 2;

	/**
	* Constructor, makes the intrabox with info $info.
	*
	* This creates an IntraBox object for the specified module.
	* If the intrabox name is not an Intranet2 module, then the intrabox created
	* is a 'static' intrabox, which is not represented by any class, but
	* rather by the IntraBox class itself. (One example is the 'links'
	* intrabox, which does not need an entire class for it.)
	*
	* @param Array $info An array containing the information about the
	*                    Intrabox, presumably from a Result object received
	*                    from the Intrabox table in MySQL.
	*/
	public function __construct($info) {
		global $I2_SQL,$I2_SELF,$I2_USER;
		
		$this->boxid = $info['boxid'];
		$module_name = $info['name'];
		$this->closed = $info['closed'];

		// Do not re-instantiate if the main pane module is the same as this module
		if( self::$main_module && strcasecmp($module_name, self::$main_module->get_name()) == 0 ) {
			$this->module = self::$main_module;
		}
		else {
			if( get_i2module($module_name) ) {
				$mod = new $module_name();
				if( ! $mod instanceof Module ) {
					throw new I2Exception('The class '.$module_name.' was passed as an Intrabox, but it does not implement the Module interface.');
				}
				$this->module = $mod;
			}
			// for static iboxen
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
		global $I2_ERR,$I2_SQL;

		// for static iboxen
		if( is_string($this->module) ) {
			$tpl = 'intrabox_'.$this->module.'.tpl';
			$display_title = flatten($I2_SQL->query('SELECT display_name FROM intrabox WHERE boxid=%d', $this->boxid)->fetch_array(Result::NUM));

			try {
				self::$display->disp('intrabox_openbox.tpl', array('name' => $this->module, 'title' => ucwords($display_title[0]), 'boxid' => $this->boxid, 'closed' => $this->closed));
				self::$display->disp($tpl);
				self::$display->disp('intrabox_closebox.tpl');
				self::$display->flush_buffer();
			}
			catch(I2Exception $e) {
				warn('Invalid intrabox `'.$this->module.'` was attempted to be displayed.');
				self::$display->clear_buffer();

			}
		}
		// for Module iboxen
		else {
			$name = $this->module->get_name();
			try {
				if( ($title = $this->module->init_box()) ) {
					try {
						self::$display->disp('intrabox_openbox.tpl', array('name' => $this->module->get_name(), 'title' => $title, 'boxid' => $this->boxid, 'closed' => $this->closed));
						$this->module->display_box($this->mydisplay);
						self::$display->disp('intrabox_closebox.tpl');
						self::$display->flush_buffer();
					}
					catch( Exception $e ) {
						warn($e->__toString());
						self::$display->clear_buffer();
					}

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
	* @param boolean $nags Whether 'nagging mode' - minimal display - is on.
	*/
	public static function display_boxes($main_module,$nags=FALSE) {
		global $I2_USER;

		if( self::$main_module === NULL && is_object($main_module) ) {
			self::$main_module = $main_module;
		}
		
		if( self::$display === NULL ) {
			self::$display = new Display('Intrabox');
		}
		
		$openclass = null;

		if ($I2_USER->header=='TRUE' && !$nags) {
			$openclass = 'boxes';
		} else {
			$openclass = 'boxes_noheader';
		}

      // Set the style if it is no already set - required for nags
      Display::style_set();

		self::$display->disp('intrabox_open.tpl',array('intrabox_open_class'=>$openclass));

		if (!$nags) {
			$b = self::get_user_boxes($I2_USER->uid);
			foreach($b as $box) {
				$box->display_box();
			}
		}

		self::$display->disp('intrabox_close.tpl');
	}

	/**
	* Gets the current list of intraboxes that a user has set to display.
	*
	* @param int $uid The user ID of the user from which to get the list of
	*                 intraboxes.
	* @return array The list of the boxids of the intraboxes.
	*/
	public static function get_user_boxes($uid) {
		global $I2_SQL;
		$boxen = $I2_SQL->query( 'SELECT * FROM intrabox 
					 JOIN intrabox_map USING (boxid) 
					 WHERE intrabox_map.uid=%d 
					 ORDER BY intrabox_map.box_order ASC;'
			,$uid);
		$ret = array();
	
		while ($boxen->more_rows()) {
			$ret[] = new Intrabox($boxen->fetch_array(Result::ASSOC));
		}

		return $ret;
	}

	/**
	* Retrieves all available intraboxes from MySQL.
	*	
	* @return array The names of all of the intraboxes that a user can
	*               choose to have.
	*/
	public static function get_all_boxes() {
		global $I2_SQL;
		return flatten($I2_SQL->query('SELECT name,gid FROM intrabox
			LEFT JOIN intrabox_group_map USING (boxid)
			WHERE gid IN (NULL,%D);',self::get_all_groups())->fetch_all_arrays(Result::NUM));
	}

	/**
	* Adds a box to a user's intrabox list.
	*
	* @param int $boxid The ID of the box to add.
	*/
	public static function add_box($boxid) {
		global $I2_SQL,$I2_USER;
		
		//This is possible to do in one query with subqueries in SQL, I believe, but not prior to MySQL 4.1 afaik. If anyone knows of a way to do this in one query, by all means do it
		list($max) = $I2_SQL->query('SELECT MAX(box_order) FROM intrabox_map WHERE uid=%d', $I2_USER->uid)->fetch_array(Result::NUM);
		
		$boxinfo = self::get_boxes_info(self::USED)->fetch_all_arrays(Result::ASSOC);
		
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
		
		if( ! ($res = $I2_SQL->query('SELECT box_order FROM intrabox_map WHERE uid=%d AND boxid=%d;', $I2_USER->uid, $boxid)->fetch_array(Result::NUM)) ) {
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
				FROM intrabox LEFT JOIN intrabox_group_map USING (boxid)
				WHERE gid IN (%D) OR gid IS NULL ORDER BY intrabox.display_name;', self::get_all_groups());
			}
			return $I2_SQL->query('
			SELECT DISTINCT intrabox.boxid AS boxid, intrabox.display_name AS display_name
			FROM intrabox LEFT JOIN intrabox_map USING (boxid) LEFT JOIN intrabox_group_map USING (boxid)
			WHERE intrabox.boxid NOT IN (%D) AND (gid IN (%D) OR gid IS NULL)
			ORDER BY intrabox.display_name;', $ids, self::get_all_groups());
		}
		else {
			throw new I2Exception('Invalid parameter passed to Intrabox::get_boxes_info()');
		}
	}

	public static function renormalize_order() {
		global $I2_USER,$I2_SQL;

		$count = 1;
		foreach($I2_SQL->query('SELECT boxid FROM intrabox_map WHERE uid=%d ORDER BY box_order;',$I2_USER->uid) as $row) {
			$boxid = $row[0];
			$I2_SQL->query('UPDATE intrabox_map SET box_order=%d WHERE uid=%d AND boxid=%d;', $count, $I2_USER->uid, $boxid);
			$count++;
		}
	}

	private static $groups = NULL;
	private static function get_all_groups() {
		global $I2_USER;
		if (self::$groups === NULL) {
			self::$groups = array();
			foreach (Group::get_user_groups($I2_USER) as $g)
				self::$groups[] = $g->gid;
			d(print_r(self::$groups,TRUE),1);
		}
		return self::$groups;
	}	
}

?>
