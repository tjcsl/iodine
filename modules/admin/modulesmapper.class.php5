<?php
/**
* Generates module map
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Admin
* @filesource
*/

/**
* @package modules
* @subpackage Admin
*/

class ModulesMapper {
	
	static $map = array();

	private static function process_dir($dir) {
		if ($handle = opendir($dir)) {
			while (FALSE !== ($file = readdir($handle))) {
				if ($file == '.' || $file == '..') {
					continue;
				}
	
				if (is_dir($dir . $file)) {
					if (!self::process_dir("$dir$file/")) {
						return FALSE;
					}
				} else if (ends_with($file, '.php5')) {
					$arr = explode('.', $file);
					self::$map[strtolower($arr[0])] = "$dir$file";
				} else {
					d("Ignoring $file");
				}
			}
		} else {
			return FALSE;
		}
		return TRUE;
	}

	public static function generate() {
		global $I2_FS_ROOT;
		$module_path = $I2_FS_ROOT . 'modules/';
		$cache_dir = i2config_get('cache_dir', NULL, 'core');
		$map_file = $cache_dir . 'module.map';
		
		if (!self::process_dir($module_path)) {
			error("Error! Could not process modules directory $module_path");
		}

		if (file_exists($map_file) && !unlink($map_file)) {
			error("Error! Could not delete $map_file");
		}

		if (!file_put_contents($map_file, serialize(self::$map))) {
			error("Error! Could not write contents to $map_file");
		}
	}

}
?>
