<?php

	//We need Smarty to work with templates, of course.
	require_once('Smarty.class.php');


	/**
	* The display module for Iodine.
	* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
	* @copyright 2004 The Intranet 2 Development Team
	* @version 1.0
	* @since 1.0
	* @package error
	*/

	class Display {

		/**
		* The Smarty object used to convert templates to HTML.
		*
		* @access private
		*/
		private var $smarty;

		/**
		* The name of the module associated with this Display object.
		*
		* @access private
		*/
		private var $my_module_name;

		/**
		* The output buffer.
		*
		* @access private
		*/
		private var $buffer;
		
		/**
		* Whether to buffer output.
		*
		* @access private
		*/
		private var $buffering = true;

		/**
		* The core display object to get buffering data from.
		*
		* @access private
		*/
		private static var $core_display;
		
		/**
		* The Display class constructor.
		* 
		* @access public
		* @param string $module_name The name of the module this Display object applies to.
		*/
		function Display($module_name) {
			$this->smarty = new Smarty;
			$this->smarty->register_prefilter('prefilter');
			$this->smarty->register_postfilter('postfilter');
			$this->smarty->register_outputfilter('outputfilter');
			$this->my_module_name = $module_name;
			if ($module_name == 'core') {
				$Display::core_display = $this;
			}
			$buffer = "";
		}

		/**
		* Get the current buffering state.
		*
		* @return bool Whether buffering is enabled.
		*/
		function bufferingOn() {
			return $Display::core_display->$buffering;
		}

		/**
		* Assign a Smarty variable a value.
		*
		* @param string $var The name of the variable to assign.
		* @param string $value The value to assign the variable.
		*/
		function assign($var,$value) {
			$this->smarty->assign($var,$value);
		}

		/**
		* Assign a list of Smarty variables values.
		*
		* @param array $array An associative array matching variables to values.
		*/
		function assign($array) {
			foreach ($array as $key=>$val) {
				assign($key,$val);
			}
		}

		/**
		* The display function.
		* 
		* @param string $template File name of the template.
		* @param array $args Associative array of Smarty arguments.
		*/
		function disp($template, $args=array()) {
			assign($args);
			//TODO: validate passed template name.
			if (bufferingOn()) {
				$Display::core_display->buffer .= $this->smarty->fetch($template); 
			} else {
				$this->smarty->display($template);
			}
		}
		
		/**
		* Output raw HTML to the browser.  Not advisable.
		*
		* @param string $text The text to display.
		*/
		function rawDisplay($text) {
			if (bufferingOn()) {
				$Display::core_display->buffer .= "$text";
			} else {
				echo($text);
			}
		}
		
		/**
		* Clear any output buffers, ensuring that all data is written to the browser.
		*/
		function flush() {
			if ($this == $Display::core_display) {
				echo($Display::core_display->buffer);
				$Display::core_display->buffer = "";
			}
		}
		
		/**
		* Set whether or not to buffer output.
		*
		* @param bool $on Whether to buffer output.
		*/
		function setBuffering($on) {
			if ($this == $Display::core_display) {
				$Display::core_display->buffering = $on;
				if (!bufferingOn()) {
					flush();
				}
			}
		}
		
		/**
		* Outputs everything that should go to the user before iboxes, regardless
		* of whether it will appear at the top or bottom of the finished layout.
		* Also sends all necessary header information, links CSS, etc.  Please note
		* that this is not global:  it is called only on the core's Display instance.
		*/
		function globalHeader() {
			//TODO: implement this for real.
			disp('header.tpl',array());
			flush();
		}

		/**
		* Closes everything that remains open, and prints anything else that goes
		* after the modules.
		*/
		function globalFooter() {
			disp('footer.tpl',array());
			flush();
		}

		/**
		* Opens the ibox section of the page.
		*/
		function startBoxes() {
			disp('startboxes.tpl',array());
			flush();
		}

		/**
		* Closes the ibox section of the page.
		*/
		function endBoxes() {
			disp('endboxes.tpl',array());
			flush();
		}

		/**
		* Open an ibox.
		*
		* @param object $module The module that the ibox will contain.
		*/
		function openBox(&$module) {
			//TODO: implement for real
			setBuffering(true);
			$name = $module->getName();
			disp('openbox.tpl',array('module_name'=>$name));
		}

		/**
		* Close an ibox.
		*
		* @param object $module The module that was contained in the ibox.
		*/
		function closeBox(&$module) {
			//TODO: implement for real
			$name = $module->getName();
			disp('closebox.tpl',array('module_name'=>$name));
			setBuffering(false);
		}

		/**
		* Open the main display box.
		*
		* @param object $module The module that will be displayed in the main box.
		*/
		function openMainBox(&$module) {
			setBuffering(false);
			$name = $module->getName();
			disp('openmainbox.tpl',array('module_name'=>$name));
		}

		/**
		* Close the main display box.
		*
		* @param object $module The module that was displayed in the main box.
		*/
		function closeMainBox(&$module) {
			$name = $module->getName();
			disp('closemainbox.tpl',array('module_name'=>$name));
		}

		/**
		* Wraps templates in {strip} tags before compilation if debugging is on.
		*
		* @param string $source The uncompiled template file.
		* @param object $smarty The Smarty object.
		* @return string The source, wrapped in {strip} tags if appropriate.
		*/
		function prefilter($source,&$smarty) {
			//TODO: put actual debug-mode-detection here
			if ($debug) {
				return "{strip}$source{/strip}";
			}
			return $source;
		}

		function postfilter($source,&$smarty) {
			return $source;
		}

		function outputfilter($output,&$smarty) {
			return $output;
		}
	}
?>
