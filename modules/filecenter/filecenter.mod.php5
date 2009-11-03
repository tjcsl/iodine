<?php
/**
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Filecenter
* @filesource
*/

/**
* @package modules
* @subpackage Filecenter
*/
class Filecenter implements Module {

	/**
	* The display object to use
	*/
	private $display;

	/**
	* Template for the specified action
	*/
	private $template = 'filecenter_pane.tpl';

	/**
	* Template arguments for intrabox
	*/
	private $box_args = array();

	/**
	* Template arguments for pane
	*/
	private $template_args = array();


	private $filesystem;

	private $directory;

	private $show_hidden_files;
	
	/**
	* Allows you to reference a student by class standing.
	*
	* Warning: This may not work for all shares.
	*/
	private static $standing = array(
		'12' => 'senior',
		'11' => 'junior',
		'10' => 'sophomore',
		'9' => 'freshman'
	);

	/**
	* Returns $size in a human-readable format.
	*
	* Takes the size of a file ($size) in bytes, and returns a string,
	* representing the same size, but in terms of KB, MB, etc.
	*
	* @param int $size The size in bytes.
	* @returns string The file size in a human-readable format.
	*/
	public static function human_readable_size($size) {
		if($size == 0) {
			return("0 Bytes");
		}
		$filesizename = array(' Bytes', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB');
		return round($size/pow(1024, ($i = floor(log($size, 1024)))), 1) . $filesizename[$i];
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_USER, $I2_ARGS, $I2_QUERY, $I2_SQL, $I2_LOG, $I2_AUTH;

		//Make sure the address ends in a trailing slash.
		//...but only if first arg isn't cslauth.  Yes this is hackish. --wyang
		if(isSet($I2_ARGS[1]) && $I2_ARGS[1] != "cslauth")
		{
			$index = strpos($_SERVER['REDIRECT_QUERY_STRING'], '?');
			if(substr($_SERVER['REDIRECT_QUERY_STRING'], $index-1, 1) != "/")
				redirect(substr($_SERVER['REDIRECT_QUERY_STRING'], 0, $index) . "/");
		}

		$system_type = isSet($I2_ARGS[1]) ? $I2_ARGS[1] : 'undefined';
		
		if (!isset($_SESSION['csl_show_hidden_files'])) {
			$_SESSION['csl_show_hidden_files'] = FALSE;
		}
		if (isset($_REQUEST['toggle_hide'])) {
			$_SESSION['csl_show_hidden_files'] = !$_SESSION['csl_show_hidden_files'];
		}

		$this->show_hidden_files = $_SESSION['csl_show_hidden_files'];

		if ($system_type == 'cslauth' && isset($_REQUEST['user']) && isset($_REQUEST['password'])) {
			//$I2_SQL->query("INSERT INTO cslfiles (uid,user,pass) VALUES(%d,%s,%s)",);
			/* 
			 * We shouldn't store pass in a mysql table, but we could store a cslusername
			 * and a setting that says if the pass is the same as intranet password.  If it is
			 * we log in user, if not we prompt for password.
			 * -Sam
			 */
			$_SESSION['csl_username'] = $_REQUEST['user'];
			$_SESSION['csl_password'] = $_REQUEST['password'];
			redirect('filecenter/csl/user/'.$_SESSION['csl_username'].'/');
		}
		else if (!isSet($_SESSION['csl_username'])) {
			$_SESSION['csl_username'] = $_SESSION['i2_username'];
			$_SESSION['csl_password'] = $I2_AUTH->get_user_password();
		}
		else {
			$this->template_args['csl_failed_login'] = TRUE;
		}

		switch($system_type) {
		case 'lan':
			$this->filesystem = new LANFilesystem($_SESSION['i2_username'], $I2_AUTH->get_user_password());
			$this->template_args['max_file_size'] = 10485760; //10 mb
			break;
		case 'portfolio':
			$this->filesystem = new PortfolioFilesystem($_SESSION['i2_username'], $I2_AUTH->get_user_password());
			$this->template_args['max_file_size'] = 10485760; //FIXME: is 10 mb correct?
			break;
		case 'csl':
			$this->filesystem = new CSLProxy($_SESSION['csl_username'], $_SESSION['csl_password']);
			if (!$this->filesystem->is_valid()) {
				$this->template = 'csl_login.tpl';
				return array('Filecenter','CSL Authentication');
			}
			$this->template_args['max_file_size'] = 20971520; //20 mb
			break;
		case 'main':
			$this->filesystem = new CSLProxy($_SESSION['i2_username'], $I2_AUTH->get_user_password(),'LOCAL.TJHSST.EDU');
			$this->template_args['max_file_size'] = 20971520;
			break;
		case 'undefined':
		default:
			$this->filesystem = 'listing';
			return array('Filecenter', 'Filecenter options');
			break;
		}
		
		
		$this->directory = '/';
		
		if (count($I2_ARGS) > 2) {
			$this->directory .= implode('/', array_slice($I2_ARGS, 2)) . '/';
		}

		if (isset($_FILES['file'])) {
			d('Received uploaded file');
			$this->handle_upload($_FILES['file']);
		}

		$file = $this->filesystem->get_file($this->directory);
		if(!$file) {
			throw new I2Exception('Filesystem returned invalid file object.');
		}
		if (isset($I2_QUERY['download']) || $file->is_file()) {
			if ($file->is_directory()) {
				$this->send_zipped_dir($this->directory);
			} else {
				if ($I2_QUERY['download'] == 'zip') {
					$this->send_zipped_file($this->directory);
				} else {
					$this->file_header($file->get_name(), $file->get_size());
					$this->filesystem->echo_contents($this->directory);
					die;
				}
			}
		} else if (isSet($I2_QUERY['rename'])) {
			$from = $this->directory . $I2_QUERY['rename'];
			$to = $this->directory . $I2_QUERY['to'];
			if ($from != $to) {
				$this->filesystem->move_file($from, $to);
			}
			redirect("filecenter/$system_type"."{$this->directory}");
		} else if (isSet($_REQUEST['mkdir'])) {
			$this->filesystem->make_dir($this->directory . $_REQUEST['mkdir']);
			redirect("filecenter/$system_type"."{$this->directory}");
		} else if (isSet($I2_QUERY['rmf'])) {
			$this->filesystem->remove_file($this->directory . $I2_QUERY['rmf']);
			redirect("filecenter/$system_type"."{$this->directory}");
		} else if (isSet($I2_QUERY['rml'])) {
			$this->filesystem->remove_link($this->directory . $I2_QUERY['rml']);
			redirect("filecenter/$system_type"."{$this->directory}");
		} else if (isSet($I2_QUERY['rmd'])) {
			$this->filesystem->remove_dir($this->directory . $I2_QUERY['rmd']);
			redirect("filecenter/$system_type"."{$this->directory}");
		} else if (isSet($I2_QUERY['rmld'])) {
			$this->filesystem->remove_link($this->directory . $I2_QUERY['rmld']);
			redirect("filecenter/$system_type"."{$this->directory}");
		} else if (isSet($I2_QUERY['rmd_recursive'])) {
			$this->filesystem->remove_dir_recursive($this->directory . $I2_QUERY['rmd_recursive']);
			redirect("filecenter/$system_type"."{$this->directory}");
		}
		
		$this->template = 'filecenter_pane.tpl';

		return array('Filecenter', 'Current directory: ' . $this->directory);
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		global $I2_SQL, $I2_USER, $I2_QUERY;

		// if the user didn't choose a filesystem
		if($this->filesystem == 'listing') {
			$this->template_args['i2_username'] = $_SESSION['i2_username'];
			$this->template_args['grad_year'] = $I2_USER->grad_year;
			if (isSet($_SESSION['csl_username'])) {
				$this->template_args['csl_username'] = $_SESSION['csl_username'];
			}
			else {
				$this->template_args['csl_username'] = $_SESSION['i2_username'];
			}
			if ($I2_USER->grade != "staff") {
				$this->template_args['tj01path'] = 'students/' . self::$standing[$I2_USER->grade] . '/' . $_SESSION['i2_username'];
			}
			$this->template = 'filecenter_box.tpl';
		}
		elseif ($this->filesystem->is_valid()) {
			$dirs = array();
			$files = array();
			
			//Add the .. directory
			if (!$this->filesystem->is_root($this->directory)) {
				$file = $this->filesystem->get_file($this->directory . '/..');
				$raw_mtime = $file->last_modified();
				$dirs[] = array(
					'name' => '..',
					'last_modified' => date('n/j/y g:i A', $raw_mtime),
					'raw_mtime' => $raw_mtime,
					'link' => FALSE,
					'empty' => FALSE
				);
			}
			//We cache some directories just to reduce the OMG9000 request time
			if ($this->filesystem->exists_file($this->directory . '.filecache')) {
				$cache = $this->filesystem->get_file($this->directory . '.filecache');
				$cachearray = $cache->read_cache_arrays();
				foreach ($cachearray as $carr) {
					$dirs[] = array(
						'name' => $carr[0],
						'last_modified' => '',
						'raw_mtime' => 0,
						'link' => $carr[1],
						'empty' => FALSE
					);
				}
			} else {
				foreach($this->filesystem->list_files($this->directory) as $file) {
					$raw_size = $file->get_size();
					$raw_mtime = $file->last_modified();
					$properties = array(
						"name" => $file->get_name(),
						"size" => self::human_readable_size($raw_size),
						"raw_size" => $raw_size,
						"last_modified" => date("n/j/y g:i A", $raw_mtime),
						"raw_mtime" => $raw_mtime
					);
					
					if (!$this->show_hidden_files && $file->is_hidden()) {
						continue;
					}
				
					$properties["link"]  = $file->is_symlink();
	
					if ($file->is_directory()) {
						// Commenting this out because it's not used EVER and it causes errors -dmorris
						$temp = 0;//count($this->filesystem->list_files($this->directory . $file->get_name()));
	
						$properties["empty"] = $temp > 0 ? FALSE : TRUE;
	
						$dirs[] = $properties;
					} else {
						$files[] = $properties;
					}
				}
			}
			if (isset($I2_QUERY['sort'])) { // Ooh, the user wants us to sort it a special way.
				switch( $I2_QUERY['sort'] ) {
					case 'name':
						usort($dirs,'Filecenter::equals_name');
						usort($files,'Filecenter::equals_name');
						$this->template_args['sort']='name';
						break;
					case 'size':
						usort($dirs,'Filecenter::equals_name'); // Files have no size
						usort($files,'Filecenter::equals_size');
						$this->template_args['sort']='size';
						break;
					case 'mtime':
						usort($dirs,'Filecenter::equals_mtime');
						usort($files,'Filecenter::equals_mtime');
						$this->template_args['sort']='mtime';
						break;
					default:
						usort($dirs,'Filecenter::equals_name');
						usort($files,'Filecenter::equals_name');
						$this->template_args['sort']='name';
						break;
				}
			}
			else {
				usort($dirs,'Filecenter::equals_name');
				usort($files,'Filecenter::equals_name');
				$this->template_args['sort']='name';
			}
			if (isset($I2_QUERY['reverse'])) {
				$dirs=array_reverse($dirs);
				$files=array_reverse($files);
				$this->template_args['reverse'] = 'true';
			}
			else $this->template_args['reverse'] = 'false';
			$this->template_args['dirs'] = $dirs;
			$this->template_args['files'] = $files;
			$this->template_args['curdir'] = $this->directory;
		}
		
		$display->disp($this->template, $this->template_args);
	}

	/**
	* Sort by name, used for field sorting in the usort()s above.
	*/
	static function equals_name($f1,$f2) {
		return strnatcmp($f1['name'],$f2['name']);
	}

	/**
	* Sort by size, used for field sorting in the usort()s above.
	*/
	static function equals_size($f1,$f2) {
		if ($f1['raw_size']==$f2['raw_size'])
			return strnatcmp($f1['name'],$f2['name']);
		return $f1['raw_size']>$f2['raw_size'];
	}

	/**
	* Sort by mtime, used for field sorting in the usort()s above.
	*/
	static function equals_mtime($f1,$f2) {
		// Here we do /60 to remove the difference of seconds, because
		// the users don't see them. It looks better.
		if ((int)($f1['raw_mtime']/60)==(int)($f2['raw_mtime']/60))
			return strnatcmp($f1['name'],$f2['name']);
		return $f1['raw_mtime']>$f2['raw_mtime'];
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		global $I2_USER;

		$this->box_args['i2_username'] = $_SESSION['i2_username'];
		$this->box_args['grad_year'] = $I2_USER->grad_year;
		if (isSet($_SESSION['csl_username'])) {
			$this->box_args['csl_username'] = $_SESSION['csl_username'];
		} else {
			$this->box_args['csl_username'] = $_SESSION['i2_username'];
		}
		d('grade: ' . $I2_USER->grade);
		if ($I2_USER->grade != "staff") {
			$this->box_args['tj01path'] = 'students/' . self::$standing[$I2_USER->grade] . '/' . $_SESSION['i2_username'];
		}

		return 'Filecenter';
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
		$display->disp('filecenter_box.tpl', $this->box_args);
	}

	public function handle_upload($file) {
		if ($file['error'] != UPLOAD_ERR_OK) {
			throw new I2Exception('Error with uploaded file: ' . $file['error']);
		}

		$this->filesystem->copy_file_into_system($file['tmp_name'], $this->directory . $file['name']);
	}

	public function file_header($name, $size) {
		Display::stop_display();
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $name . '"');
		header('Content-length: ' . $size);
		header('Pragma: ');
	}

	public function send_zipped_dir($path) {
		$zipfile = tempname('/tmp/iodine-filesystem-', '.zip');
		$this->filesystem->zip_dir($path, $zipfile);
		$this->file_header(basename($path) . '.zip', filesize($zipfile));
		readfile($zipfile);
		unlink($zipfile);
		die;
	}

	public function send_zipped_file($path) {
		$zipfile = tempname('/tmp/iodine-filesystem-', '.zip');
		$this->filesystem->zip_file($path, $zipfile);
		$this->file_header(basename($path) . '.zip', filesize($zipfile));
		readfile($zipfile);
		unlink($zipfile);
		die;
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return "Filecenter";
	}
}

?>
