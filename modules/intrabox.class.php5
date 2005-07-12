<?php
/**
* Just contains the definition for the class {@link IntraBox}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2004-2005 The Intranet 2 Development Team
* @version $Id: intrabox.class.php5,v 1.6 2005/07/11 20:49:22 adeason Exp $
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
	* The display object to display the intrabox templates on.
	*/
	private static $display = NULL;

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
		if( self::$display === NULL ) {
			self::$display = new Display('Intrabox');
		}

		if( $module_name == self::$main_module->get_name() ) {
			$this->module = self::$main_module;
		}
		else {
			if( get_i2module($module_name) ) {
				eval('$mod = new '.$module_name.'();');
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
	}

	/**
	* Displays this intrabox.
	*/
	public function display_box() {
		global $I2_ERR;
		if( is_string($this->module) ) {
			$tpl = 'intrabox_'.$this->module.'.tpl';
			
			if( Display::template_exists($tpl) ) {
				self::$display->disp($tpl);
			}
			else {
				throw new I2Exception('Invalid intrabox `'.$this->module.'` was attempted to be displayed.');
			}
		}
		else {
			$name = $this->module->get_name();
			try {
				if( ($title = $this->module->init_box(true)) ) {
					self::$display->disp('intrabox_openbox.tpl', array('title' => $title));
					try {
						$this->module->display_box(new Display($name));
					}
					catch( Exception $e ) {
						self::$display->disp('intrabox_closebox.tpl');
						throw $e;
					}
					self::$display->disp('intrabox_closebox.tpl');
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
		
		if( self::$main_module === NULL ) {
			self::$main_module = $main_module;
		}

		foreach(explode(',', $I2_USER->boxes) as $mod) {
			$box = new Intrabox($mod);
			$box->display_box();
		}
	}

	/**
	* Gets the current list of intraboxes that a user has set to display.
	*
	* @param int $uid The user ID of the user from which to get the list of
	*                 intraboxes.
	* @return array The list of the names of the intraboxes.
	*/
	public static function get_user_boxes($uid) {
		$user = new User($uid);
		return explode(',', $user->startpage);
	}

	/**
	* Sets a user's preferences so the specified intraboxes are displayed
	* for them.
	*
	* @todo Use calls in User instead of sql
	* @param int $uid The user ID of the user to set the boxes for.
	* @param array $boxes The array of the names of the boxes.
	*/
	public static function set_user_boxes($uid, $boxes) {
		global $I2_SQL;
		$box_str = implode(',', $boxes);
		$I2_SQL->query('UPDATE userinfo SET boxes=%s WHERE uid=%d;', $box_str, $uid);
	}

	/**
	* @return array The names of all of the intraboxes that a user can
	*               choose to have.
	* @todo Write this method.
	*/
	public static function get_all_boxes() {
		
	}
}

?>
