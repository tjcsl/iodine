<?php
/**
*
*/

/**
*
*/

class HGRepository implements Repository {
	private static $hg = NULL;

	private $oldcwd;

	private $manifest;

	private $root;
	
	public function __construct($root) {
		$this->oldcwd = getcwd();
		chdir($root);
		$this->root = $root;

		if(self::$hg === NULL) {
			self::$hg = i2config_get('hg_binary','/usr/bin/hg','hgrepository');
		}
		$this->manifest = self::hg("manifest | cut -d ' ' -f3-");
	}

	public function __destruct() {
		chdir($this->oldcwd);
	}
	
	public function list_files($path) {
		$ret = array();
		$numslashes = substr_count($path,'/');
		foreach(explode("\n",$this->manifest) as $file) {
			if(substr_count($file,'/') == $numslashes && $file) {
				$ret[] = $file;
			}
		}
		return $ret;
	}

	public function summary($file) {
		$ret = array( 'name' => $file );
		
		$log = array_slice(explode("\n", self::hg("log $file")),0,9);
		foreach($log as $i=>$line) {
			d('Line: '.$line);
			if(strpos($line,'user:') === 0) {
				$ret['lastmod_user'] = str_replace('user:        ','',$line);
			}
			elseif(strpos($line,'date:') === 0) {
				$ret['lastmod_date'] = str_replace('date:        ','',$line);
			}
			elseif(strpos($line,'description:') === 0) {
				$ret['lastmod_log'] = $log[$i+1];
			}
		}
		
		return $ret;
	}

	public function is_dir($file) {
		return is_dir($this->root.$file);
	}

	private static function hg($str) {
		$cmd = self::$hg. " $str";
		return `$cmd`;
	}
}
?>
