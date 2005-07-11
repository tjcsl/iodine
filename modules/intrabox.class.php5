<?php

/**
* Class for intraboxen.
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

	public static function display_boxes($main_module) {
		global $I2_SQL,$I2_ARGS;
		
		if( self::$main_module === NULL ) {
			self::$main_module = $main_module;
		}

		$res = $I2_SQL->query('SELECT boxes FROM userinfo WHERE uid=%d', $_SESSION['i2_uid'])->fetch_all_arrays(MYSQL_NUM);
		foreach(explode(',', $res[0][0]) as $mod) {
			$box = new Intrabox($mod);
			$box->display_box();
		}

	}
}

?>
