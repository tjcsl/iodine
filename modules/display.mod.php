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
		private static var $smarty = new Smarty;

		/**
		* The name of the module associated with this Display object.
		*
		* @access private
		*/
		private var $module;

		/**
		* The Display class constructor.
		* 
		* @access public
		* @staticvar object $Display is instantiated if it hadn't been before.
		*/
		function Display($module_name) {
			//Create Smarty object if necessary
			if (!$Display->smarty) {
				$Display->smarty = new Smarty;
				$Display->smarty->register_prefilter('prefilter');
				$Display->smarty->register_postfilter('postfilter');
				$Display->smarty->register_outputfilter('outputfilter');
			}

			$this->module = $module_name;
			
		}

		/**
		* The static (internal?) display function.
		* 
		* @param string $module_name The name of the module calling the function.
		* @param string $template File name of the template.
		* @param array $args Associative array of Smarty arguments.
		*/
		static function disp($module_name, $template, $args) {
			foreach ($args as $key=>$value) {
				$smarty->assign($key,$value);
			}
			$smarty->display($template);
		}

		/**
		* The display function.
		*
		* @param string $template File name of the template to be displayed.
		* @param array $args Associative array of Smarty arguments.
		*/
		function disp($template, $args) {
			disp($this->module,$template,$args);
		}

		static function box_header($module_name) {
			//TODO: implement
		}

		function header() {
			header($this->module);
		}

		static function footer($module_name) {
			//TODO: implement
		}

		function footer() {
			footer($this->module);
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
