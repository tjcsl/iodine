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
	* @access public
	*/
	public $buffer;

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
	* Whether to display anything.
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
		global $I2_FS_ROOT;
		require_once('smarty/Smarty.class.php');
		$this->smarty = new Smarty;
		$this->smarty->left_delimiter = '[<';
		$this->smarty->right_delimiter = '>]';
		$this->smarty->setCompileDir(i2config_get('cache_dir', NULL, 'core') . 'smarty/');
		$this->smarty->addPluginsDir($I2_FS_ROOT . 'smarty');
		$this->smarty->setCacheDir($this->smarty->getCompileDir().'cache');

		// Caching off by default
		$this->smarty->caching = false;

		//TODO: turn this off for production code!
		$this->smarty->compile_check = true;

		$this->my_module_name = $module_name;

		if(!file_exists($this->smarty->getCompileDir()) && !mkdir($this->smarty->getCompileDir())) {
			error("Error! Could not create $this->smarty->getCompileDir()");
		}

		if ($module_name == 'core') {
			self::$core_display = $this;
		}
		self::$tpl_root = $I2_FS_ROOT . 'templates/';
		$this->buffer = '';
		$this->buffering = TRUE;

		self::style_set();
	}

	/**
	* Sets the name of the module associated with this Display object.
	*
	* Useful for handing off Display objects to other classes.
	*/
	public function set_module_name($name) {
		$this->my_module_name = $name;
	}

	/**
	* The main non-core executing loop of Iodine.
	*
	* This function basically displays everything and performs
	* pretty much all processing done outside of core.
	*
	* @param string $module The name of the module to display in the main
	*					 panel and give processing control to.
	*/
	public function display_loop($module) {
		global $I2_ERR, $I2_USER, $I2_QUERY;
		$IBOX_FIRST = (isset($_COOKIE,$_COOKIE['gc'])&&$_COOKIE['gc']==true)||(isset($I2_QUERY,$I2_QUERY['gc']));
		if (self::$display_stopped) {
			return;
		}

		if(isset($I2_USER)){ //Not set when using some modules, like Feeds-based stuff.
			// Limit users to certain modules. Used for TJStar users.
			$allowed_modules=$I2_USER->allowed_modules;
			if(!count($allowed_modules)==0 && !in_array(strtolower($module),$allowed_modules)) {
				$I2_ERR->nonfatal_error("User tried to access the unauthorized module $module");
				redirect();
				return;
			}
		}

		// Allow nags to catch users after they log in
		$nagging = (strcasecmp($module,'nags') == 0);
		// But let CSS escape the horror
		if (!$nagging && strcasecmp($module,'css') != 0) {
			$nagging = Nags::login_hook();
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
						$timestart=explode(" ",microtime());
						d("entering $module pane initialization at ".($timestart[1]+$timestart[0]),'P');
						$title = $mod->init_pane();
						$timeend=explode(" ",microtime());
						d((($timeend[1]-$timestart[1])+($timeend[0]-$timestart[0]))." seconds to initialize $module pane",'P');
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
					/**
					  * If title is an array, $title[0] is the page title
					  * in <title>, and the second is the header <h1>. If
					  * $title[2] is set and it equals false, the <h1>
					  * title ($title[1]) will not be URL encoded so links
					  * can be placed there for absences in eighth, for example.
					  **/
					if( !is_array($title) ) {
						$title = array( $title, $title );
					}
					elseif( count($title) == 1 ) {
						$title = array( $title[0], $title[0] );
					}
					elseif( count($title) == 0 ) {
						$title = array( NULL, '&nbsp;' );
					}
					if(!isset($title[2]) || $title[2]) $title[1] = htmlspecialchars($title[1]);
					$display_chrome = (isset($I2_USER)?($I2_USER->chrome=='TRUE'?TRUE:FALSE):FALSE);
					

					$this->global_header($title[0],$display_chrome,$nagging);
					if($IBOX_FIRST) {
                                        	Intrabox::display_boxes($mod,$nagging); global $I2_ROOT;
						$title[1] = str_replace("Eighth Period Office Online:", '<img src="'.$I2_ROOT.'www/pics/eighth/eighth.png" class="eighth_online" />', $title[1]);
                                	}
					if (!self::$display_stopped && $title) {
						if ($display_chrome) {
							$this->open_content_pane(array('title' => $this->addtitlesuffix($title[1])),$nagging);
						}
						try {
							$timestart=explode(" ",microtime());
							d("entering $module pane display call at ".($timestart[1]+$timestart[0]),'P');
							$mod->display_pane($disp);
							$timeend=explode(" ",microtime());
							d((($timeend[1]-$timestart[1])+($timeend[0]-$timestart[0]))." seconds to display $module pane",'P');
						} catch (Exception $e) {
							/* Make sure to close the content pane*/
							if ($display_chrome || $nagging) {
								$this->close_content_pane();
							}
							throw $e;
						}
						if ($display_chrome || $nagging) {
							$this->close_content_pane();
						}
					}
				}
			}

		} catch (Exception $e) {
			$I2_ERR->nonfatal_error('Exception raised in module '.$module.', while processing main pane. Exception: '.$e->__toString());
		}

		if(!$IBOX_FIRST) Intrabox::display_boxes($mod,$nagging);

		$this->global_footer();
	}

	/**
	 * Append text (like absence information) to the
	 * .boxheader title on every page
	 */
	public function addtitlesuffix($title) {
		global $I2_USER, $I2_ROOT;
		if($I2_USER->objectClass == "tjhsstStudent") {
                        $numabs = count(EighthSchedule::get_absences($I2_USER->uid));
                        if($numabs > 0) {
                                $suffix = " - <a".($numabs>2?" style='color: red'":"")." href='{$I2_ROOT}eighth/vcp_schedule/absences/uid/{$I2_USER->uid}'>{$numabs} absence".($numabs>1?'s':'')."</a>";
                        } else $suffix = "";
                } else $suffix = "";
		return $title . $suffix;
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
		if (isset($I2_USER)) {
			$I2_USER->recache('style');
			self::$style = ($I2_USER->style);
			CSS::flush_cache($I2_USER);
			JS::flush_cache($I2_USER);
		}
		else {
			self::$style = 'default';
		}
		d('Style changed, is now: '.self::$style,7);
	}

	 /**
	 * Initially set the style, if required.
	 */
	 public static function style_set() {
		  global $I2_USER;
		  if (self::$style == NULL) {
			  if (isset($I2_USER)) {
					  self::$style = ($I2_USER->style);
			  }
			  else {
					  self::$style = 'default';
			  }
			  d('Style set to '.self::$style,7);
		  }
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

	public function smarty_register_function($name, $call = NULL) {
		$this->smarty->register_function($name, ($call == NULL ? $name : $call));
	}

	/**
	* Assigns all I2 variables which we want available in all templates.
	*/
	private function assign_i2vals() {
		global $I2_USER,$I2_ROOT,$I2_SELF,$I2_ARGS,$module;
		$this->smarty->assign('I2_ROOT', $I2_ROOT);
		$this->smarty->assign('I2_SELF', $I2_SELF);
		$this->smarty->assign('I2_ARGSTRING', implode('/',$I2_ARGS));
		$this->smarty->assign('I2_MODNAME', $module);
		if( isset($I2_USER) ) {
			$this->smarty->assign('I2_UID', $I2_USER->uid);
			$this->smarty->assign('I2_USER', $I2_USER);
			$this->smarty->assign('I2_CSS', "{$I2_ROOT}css/".self::$style.'.css/'.$I2_USER->uid);
			$this->smarty->assign('I2_JS', "{$I2_ROOT}js/".self::$style.'.js');
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
	public function disp($template, $args=[], $validate=TRUE) {
		global $I2_QUERY;
		if(self::$display_stopped) {
			return;
		}

		$this->assign_i2vals();
		$this->smarty_assign($args);

		if(isset($_COOKIE, $_COOKIE['gc']) && $_COOKIE['gc'] == true || isset($I2_QUERY['gc'])) {
			$ntemplate = str_replace('.tpl','-gc.tpl',$template);
			if(file_exists('./templates/'.strtolower($this->my_module_name).'/'.$ntemplate)) {
				d("Using GC template $ntemplate over $template");
				$template = $ntemplate;
			}
		}
		// Validate template given
		if( $validate && (($tpl = self::get_template(strtolower($this->my_module_name).'/'.$template)) === NULL) ) {
			throw new I2Exception('Invalid template `'.$this->my_module_name.'/'.$template.'` passed to Display');
		}

		if ($this->buffering_on()) {
			self::$core_display->buffer .= $this->smarty->fetch($tpl);
			#die("Got here");
		} else {
			$this->smarty->display($tpl);
		}
	}

	/**
	* Fetches a template's output without displaying it.
	*
	* @param string $temple File name of the template.
	* @param array $args Associative array of Smarty arguments.
	*/
	public function fetch($template, $args=[], $validate = TRUE) {
		$this->assign_i2vals();
		$this->smarty_assign($args);
		if(self::get_template(strtolower($this->my_module_name).'/'.$template) === NULL) {
			if($validate)
				throw new I2Exception('Invalid template `'.$this->my_module_name.'/'.$template.'` passed to Display');
		}
		return $this->smarty->fetch($template);
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
		echo(self::$core_display->buffer);
		self::$core_display->buffer = '';
	}

	/**
	* Gets the contents of the display buffer
	*/
	public function get_buffer() {
		return self::$core_display->buffer;
	}

	/**
	* Clears any output buffers not currently pushed through to the browser.
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
		self::$core_display->buffering = $on;
		if (!$this->buffering_on()) {
			$this->flush_buffer();
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
	public function global_header($title = NULL,$chrome = TRUE, $nagging=FALSE) {
		global $I2_USER, $I2_ROOT;
		$this->smarty_assign(
			array(
					'title' => htmlspecialchars($title),
					'first_name' => (isset($I2_USER)?$I2_USER->fname:"Noman"),
					'chrome' => $chrome
			)
		);
		TopBar::display($this, $chrome, $nagging);
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
	public function open_content_pane($args, $minimal=FALSE) {
		global $I2_USER;
		$numboxes = count(Intrabox::get_user_boxes($I2_USER->uid));
		if ($I2_USER->header=='TRUE' && !$minimal) {
			if ($numboxes > 0) {
				$args['mainbox_class'] = 'mainbox';
			} else {
				$args['mainbox_class'] = 'mainbox_nointraboxes';
			}
		} else if ($numboxes > 0 && !$minimal) {
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
	* Gets the absolute filename for a template file.
	*
	* Given a template file, this method will determine if such a template
	* file exists, and if so, will return the absolute path name of the
	* template file. Otherwise will return NULL if the template is not
	* found.
	*
	* @returns mixed The absolute filename as a string if the template was
	*		 found, NULL otherwise.
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
