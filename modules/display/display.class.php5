<?php
/**
* Just contains the definition for the class {@link Display}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package core
* @subpackage Display
* @filesource
*/

/**
* The display module for Iodine.
* @package core
* @subpackage Display
* @todo Somehow catch errors that happen when executing a Smarty template. Right now it screws up the page if you try to call an undefined function, or something.
*/
class Display {

	/**
	* The Smarty object used to convert templates to HTML.
	*
	* @access private
	*/
	private $smarty;

	/**
	* The name of the module associated with this Display object.
	*
	* @access private
	*/
	private $my_module_name;

	/**
	* The output buffer.
	*
	* @access private
	*/
	private $buffer;
	
	/**
	* Whether to buffer output.
	*
	* @access private
	*/
	private $buffering = TRUE;

	/**
	* The core display object to get buffering data from.
	* @access private
	*/
	private static $core_display = NULL;
	
	/**
	* @access private
	*/
	private static $display_stopped = FALSE;

	/**
	* The template root directory, as specified in the configuration file.
	* This variable is just here to cache the info in the config file,
	* since it's referenced so much in Display.
	*/
	private static $tpl_root = NULL;
	
	private static $style = NULL;
	
	/**
	* The Display class constructor.
	* 
	* @access public
	* @param string $module_name The name of the module this Display object applies to.
	*/
	public function __construct($module_name='core') {
		require_once('Smarty.class.php');
		$this->smarty = new Smarty;
		//$this->smarty->register_prefilter(array(&$this,'prefilter'));
		//$this->smarty->load_filter('pre','strip');
		//$this->smarty->register_postfilter(array(&$this,'postfilter'));
		//$this->smarty->register_outputfilter(array(&$this,'outputfilter'));
		$this->smarty->left_delimiter = '[<';
		$this->smarty->right_delimiter = '>]';
		$this->smarty->compile_dir = i2config_get('smarty_path','./','core');
		$this->smarty->plugins_dir = array('plugins',i2config_get('root_path',NULL,'core').'smarty');
		$this->smarty->cache_dir = $this->smarty->compile_dir.'cache';

		// Caching off by default
		$this->smarty->caching = false;
		
		//TODO: turn this off for production code!
		$this->smarty->compile_check = true;

		$this->my_module_name = $module_name;

		
		if ($module_name == 'core') {
			self::$core_display = $this;
		}
		self::$tpl_root = i2config_get('template_path','./','core');
		$this->buffer = '';
		$this->buffering = TRUE;

		if (self::$style == NULL) {
			self::style_changed();
		}
	}

	/**
	* The main non-core executing loop of Iodine.
	*
	* This function basically displays everything and performs
	* pretty much all processing done outside of core.
	*
	* @param string $module The name of the module to display in the main
	*                panel and give processing control to.
	*/
	public function display_loop($module) {
		global $I2_ERR,$I2_USER;

		if (self::$display_stopped) {
			return;
		}
		
		$mod = '';

		try {	
			if( !get_i2module($module) ) {
				$this->global_header('Error');
				$this->open_content_pane(array('no_module' => $module));
				$this->close_content_pane();
			}
			else {
		
				/*
				** Display the main pane.
				*/
				$disp = new Display($module);
				
				$mod = NULL;

				try {
					$mod = new $module();
					if(! $mod instanceof Module) {
						// essentially, 'no such module'
						$title = FALSE;
					}
					else {
						$title = $mod->init_pane();
					}
				} catch( Exception $e ) {
					$this->global_header('Error');
					$this->open_content_pane(array('error' => 1));
					$this->close_content_pane();
					throw $e;
				}
				
				if ( $title === FALSE) {
					$this->global_header('Error');
					$this->open_content_pane(array('no_module' => $module));
					$this->close_content_pane();
				}
				else {
	
					if( !is_array($title) ) {
						$title = array( $title, $title );
					}
					elseif( count($title) == 1 ) {
						$title = array( $title[0], $title[0] );
					}
					elseif( count($title) == 0 ) {
						$title = array( NULL, '&nbsp;' );
					}
				
					$display_chrome = $I2_USER->chrome;
					
					$this->global_header($title[0],$display_chrome);
					
					if (!self::$display_stopped && $title) {
						if ($display_chrome) {
							$this->open_content_pane(array('title' => htmlspecialchars($title[1])));
						}
						try {
							$mod->display_pane($disp);
						} catch (Exception $e) {
							/* Make sure to close the content pane*/
							if ($display_chrome) {
								$this->close_content_pane();
							}
							throw $e;
						}
						if ($display_chrome) {
							$this->close_content_pane();
						}
					}
				}
			}
						
		} catch (Exception $e) {
			$I2_ERR->nonfatal_error('Exception raised in module '.$module.', while processing main pane. Exception: '.$e->__toString());
		}
			
		// Let the Intrabox class handle intraboxes
		Intrabox::display_boxes($mod);
		
		$this->global_footer();
	}

	/**
	* Turns on or off caching.
	*/
	public function cache($yes=TRUE) {
		$this->smarty->caching = $yes;
	}

	/**
	* Clears any cached Smarty templates.
	*/
	public function clear_cache($template=FALSE) {
		if (!$template) {
			$this->smarty->clear_all_cache();
		} else {
			$this->smarty->clear_cache($template);
		}
	}
	
	/**
	* Inform Dislay that the user's style has changed.
	*/
	public static function style_changed() {
		global $I2_USER;
		if (isSet($I2_USER)) {
			self::$style = ($I2_USER->style);
		}
		else {
			self::$style = 'default';
		}
		d('Style changed, is now: '.self::$style,7);
	}

	/**
	* Get the current buffering state.
	*
	* @return bool Whether buffering is enabled.
	*/
	public function buffering_on() {
		if( self::$core_display !== NULL )
			return self::$core_display->buffering;
		return FALSE;
	}

	/**
	* Assign a Smarty variable a value.
	*
	* @param mixed $var either the name of the variable to assign or an array of key,value pairs to assign.
	* @param mixed $value The value to assign the variable.
	*/
	public function smarty_assign($var,$value=null) {
		if ($value === null) {
			//Assign key,value pairs in the array
			$this->smarty->assign($var);
		}
		else {
			$this->smarty->assign($var,$value);
		}
	}

	/**
	* Assigns all I2 variables which we want available in all templates.
	*/
	private function assign_i2vals() {
		global $I2_USER,$I2_ROOT,$I2_SELF,$I2_ARGS;
		$this->smarty->assign('I2_ROOT', $I2_ROOT);
		$this->smarty->assign('I2_SELF', $I2_SELF);
		$this->smarty->assign('I2_ARGSTRING', implode('/',$I2_ARGS));
		if( isSet($I2_USER) ) {
			$this->smarty->assign('I2_UID', $I2_USER->uid);
			$this->smarty->assign('I2_CSS', "{$I2_ROOT}css/".self::$style.'.css');
		}
		else {
			$this->smarty->assign('I2_CSS', "{$I2_ROOT}css/default.css");
		}
	}

	/**
	* The display function.
	* 
	* @param string $template File name of the template.
	* @param array $args Associative array of Smarty arguments.
	*/
	public function disp($template, $args=array()) {
		if(self::$display_stopped) {
			return;
		}
	
		$this->assign_i2vals();
		$this->smarty_assign($args);
		
		// Validate template given
		if( ($tpl = self::get_template(strtolower($this->my_module_name).'/'.$template)) === NULL ) {
			throw new I2Exception('Invalid template `'.$this->my_module_name.'/'.$template.'` passed to Display');
		}
		
		if ($this->buffering_on()) {
			self::$core_display->buffer .= $this->smarty->fetch($tpl); 
		} else {
			$this->smarty->display($tpl);
		}
	}
	
	/**
	* Output raw HTML to the browser.  Not advisable.
	*
	* @param string $text The text to display.
	*/
	public function raw_display($text) {
		if(self::$display_stopped) {
			return;
		}
	
		$text = 'Raw display from module '.$this->my_module_name.': '.$text;
		if ($this->buffering_on()) {
			self::$core_display->buffer .= "$text";
		} else {
			echo($text);
		}
	}
	
	/**
	* Clear any output buffers, ensuring that all data is written to the browser.
	*/
	public function flush_buffer() {
		if (1) {
			echo(self::$core_display->buffer);
			self::$core_display->buffer = '';
		}
	}

	/**
	* Clears any output buffers not current pushed through to the browser.
	*/
	public function clear_buffer() {
		self::$core_display->buffer = '';
	}
	
	/**
	* Set whether or not to buffer output.
	*
	* @param bool $on Whether to buffer output.
	*/
	public function set_buffering($on) {
		if ($this == self::$core_display) {
			self::$core_display->buffering = $on;
			if (!$this->buffering_on()) {
				$this->flush_buffer();
			}
		}
	}
	
	/**
	* Outputs everything that should go to the user before iboxes, regardless
	* of whether it will appear at the top or bottom of the finished layout.
	* Also sends all necessary header information, links CSS, etc.  Please note
	* that this is not global:  it is called only on the core's Display instance.
	*
	* @param string $title The title for the page.
	* @param boolean $chrome Whether to embellish the top of the page.
	*/
	public function global_header($title = NULL,$chrome = TRUE) {
		global $I2_USER;
		$this->smarty_assign(
			array(
					'title' => htmlspecialchars($title), 
					'first_name' => $I2_USER->fname, 
					'admin_mysql' => Group::admin_mysql()->has_member($I2_USER),
					'admin_ldap' => Group::admin_ldap()->has_member($I2_USER),
					'admin_groups' => Group::admin_groups()->has_member($I2_USER),
					'chrome' => $chrome
			)
		);
		if ($I2_USER->header && $chrome) {
			$this->disp('header.tpl');
		} else {
			d('The user has minimized their header',6);
			$this->disp('header-small.tpl');
		}
		//XXX: The following line needs to be commented out for raw data output to work. I don't know how necessary it is. -adeason
//		$this->flush_buffer();
	}

	/**
	* Closes everything that remains open, and prints anything else that goes
	* after the modules.
	*/
	public function global_footer() {
		$this->disp('footer.tpl');
		$this->flush_buffer();
	}

	/**
	* Open the content pane.
	*
	* @param array $args The arguments passed to the Smarty template.
	*/
	public function open_content_pane($args) {
		global $I2_USER;
		$numboxes = count(Intrabox::get_user_boxes($I2_USER->uid));
		if ($I2_USER->header) {
			if ($numboxes > 0) {
				$args['mainbox_class'] = 'mainbox';
			} else {
				$args['mainbox_class'] = 'mainbox_nointraboxes';
			}
		} else if ($numboxes > 0) {
			$args['mainbox_class'] = 'mainbox_noheader';
		} else {
			$args['mainbox_class'] = 'mainbox_noheader_nointraboxes';
		}
		$this->disp('openmainbox.tpl',$args);
	}

	/**
	* Close the content pane.
	*
	*/
	public function close_content_pane() {
		$this->disp('closemainbox.tpl');
	}

	/**
	* Wraps templates in {strip} tags before compilation if debugging is on.
	*
	* @param string $source The uncompiled template file.
	* @param object $smarty The Smarty object.
	* @return string The source, wrapped in {strip} tags if appropriate.
	*/
	public function prefilter($source,&$smarty) {
		if (!$debug) {
			return '[<strip>]'.$source.'[</strip>]';
		}
		return $source;
	}

	/**
	* The postfilter smarty function. Not currently in use.
	*/
	public function postfilter($source,&$smarty) {
		return $source;
	}

	/**
	* The outputfilter smarty function. Not currently in use.
	*/
	public function outputfilter($output,&$smarty) {
		return $output;
	}

	/**
	* Gets the absolute filename for a template file.
	*
	* Given a template file, this method will determine if such a template
	* file exists, and if so, will return the absolute path name of the
	* template file. Otherwise will return NULL if the template is not
	* found.
	*
	* @returns mixed The absolute filename as a string if the template was
	*                found, NULL otherwise.
	*/
	public static function get_template($tpl) {
		$path = self::$tpl_root . $tpl;
		
		if (is_readable($path)) {
			return $path;
		}
		return NULL;
	}

	/**
	* Determines if a module is valid.
	*
	* @param $tpl string The file name of the template, in <module>/<file.tpl> format.
	*
	* @return bool TRUE if the specified template exists, FALSE otherwise.
	*/
	public static function is_template($tpl) {
		return (self::get_template($tpl)!==NULL);
	}

	/**
	* Automagical destructor for Display objects.  This flushes all output.
	*/
	public function __finalize() {
		$this->flush_buffer();
	}

	/**
	* Stop Display from displaying anything.
	*
	* Use this method in the case of outputting raw file data, or any other case where you need to ensure that the normal display does not get sent.
	*/
	public static function stop_display() {
		self::$display_stopped = TRUE;
		self::$core_display->clear_buffer();
		self::$core_display->set_buffering(FALSE);
	}
}
?>
