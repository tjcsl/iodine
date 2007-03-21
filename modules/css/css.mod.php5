<?php
/**
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage CSS
* @filesource
*/

/**
* @package modules
* @subpackage CSS
*/
class CSS implements Module {

	private $style_sheet;

	private $css_text;

	private $warnings = array();

	private $style_cache;

	private $date;

	private $gmdate;

	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		return FALSE;
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($disp) {
		return FALSE;
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($disp) {
		
		header('Content-type: text/css');
		header("Last-Modified: {$this->gmdate}");
		header('Cache-Control: public');
		header('Pragma:'); //Unset pragma header
		header('Expires:'); //Unset expires header
		//echo $this->css_text;
		echo "/* Server-Cache: {$this->style_cache} */\n";
		echo "/* Client-Cached: {$this->date} */\n";
		$disp->clear_buffer();
		$text = $disp->fetch($this->style_cache,array(),FALSE);
		//TODO: cache to stop extra Smarty runs?
		echo $text;
		
		Display::stop_display();
		
		exit;
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return 'css';
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_ARGS, $I2_USER;
		
		$current_style = $I2_ARGS[1];

		if (ends_with($current_style, '.css')) {
			$current_style = substr($current_style, 0, strlen($current_style) - 4);
		}
		
		$this->style_path = i2config_get('style_path', NULL, 'core');
		$cache_dir = i2config_get('cache_dir', NULL, 'core') . 'styles/';
		if (!is_dir($cache_dir)) {
			mkdir($cache_dir, 0700, TRUE);
		}
		$style_cache = $cache_dir . $I2_USER->uid;

		//Recompile the cache if it's stale
		if (!file_exists($style_cache) || filemtime($style_cache) < dirmtime($this->style_path)) {
			$this->style_sheet = new StyleSheet();
			$this->load_style('default');
			if ($current_style != 'default') {
				$this->load_style($current_style);
			}

			$date = date("D M j G:i:s T Y");
			$contents = "/* Server cache '$current_style' created on $date */\n";

			foreach ($this->warnings as $message) {
				$contents .= "/* WARNING: $message */\n";
			}
			
			$contents .= $this->style_sheet->__toString();
			file_put_contents($style_cache, $contents);
		}
		
		//Modification date of cache file
		$this->gmdate = gmdate('D, d M Y H:i:s', filemtime($style_cache)) . ' GMT';

		//Checks to see if the client's cache is stale
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			$if_modified_since = preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
			if ($if_modified_since == $this->gmdate) {
				Display::stop_display();
				header('HTTP/1.0 304 Not Modified');
				exit;
			}
		}
		
		
		$date = date('D M j G:i:s T Y');
		$this->date = $date;

		$this->style_cache = $style_cache;

		//$this->css_text = file_get_contents($style_cache);
		
		return 'css';
	}

	public static function get_available_styles() {
		$styles = array();

		$style_path = i2config_get('style_path', NULL, 'core');
		$handle = opendir($style_path);
		while (($name = readdir($handle)) !== FALSE) {
			if ($name != '.' && $name != '..' && is_dir("$style_path/$name")) {
				$styles[] = $name;
			}
		}
		closedir($handle);

		sort($styles);
		return $styles;
	}

	private function load_style($style) {
		$dir =  $this->style_path . $style;
	
		$ini_path = $dir . '/style.ini';
		if (file_exists($ini_path)) {
			$config = parse_ini_file($ini_path);
			if (isset($config['base_style'])) {
				$this->load_style($config['base_style']);
			}
		}
		
		$this->load_dir($dir);
	}

	private function load_dir($dir) {
		$handle = opendir($dir);
		while (($name = readdir($handle)) !== FALSE) {
			if ($name == '.' || $name == '..') {
				continue;
			}
			
			$file = "$dir/$name";
			
			if (is_dir($file)) {
				$this->load_dir($file);
			} else if (ends_with($name, '.css')) {
				$this->load_css($file);
			}
		}
		closedir($handle);
	}

	private function load_css($path) {
		$contents = file_get_contents($path);

		if ($contents === FALSE) {
			throw new I2Exception("Could not read contents of $path");
		}
		
		$filename = basename($path);

		/* remove comments */
		$contents = preg_replace("/\/\*.*?\*\//s", '', $contents);

		$rules = array_map('trim', explode('}', $contents));
		foreach ($rules as $rule) {
			if ($rule == '') {
				continue;
			}

			$replace = TRUE;
			
			list($keys, $values) = explode('{', $rule);
			
			if (preg_match_all('/\@(.*?)\s/s', $keys, $modifiers) > 0) {
				foreach ($modifiers[1] as $modifier) {
					if ($modifier == 'extend') {
						$replace = FALSE;
					} else if ($modifier == 'replace') {
						$replace  = TRUE;
					} else {
						$this->warnings[] = "Unknown modifier $modifier in $path";
					}
				}
				$keys = preg_replace('/\@.*?\s/s', '', $keys);
			}

			$selectors = array_map('trim', explode(',', $keys));
			$properties = array_map('trim', explode(';', $values));

			$rule = new CSSRule($filename);
			
			foreach ($properties as $property) {
				if ($property == '') {
					continue;
				}

				$colonpos = strpos($property,':');
				$key = trim(substr($property,0,$colonpos));
				$value = trim(substr($property,$colonpos+1));
				$rule->set_property($key, $value);
			}

			foreach ($selectors as $selector) {
				if ($selector == '') {
					continue;
				}
				$rule->add_selector($selector);
			}

			if ($replace) {
				$this->style_sheet->replace_rule($rule);
			} else {
				$this->style_sheet->extend_rule($rule);
			}
		}
	}

	public static function flush_cache(User $user) {
		$cache_dir = i2config_get('cache_dir', NULL, 'core') . 'styles/';
		$style_cache = $cache_dir . $user->uid;
		exec("rm -f $style_cache");
	}
}

?>
