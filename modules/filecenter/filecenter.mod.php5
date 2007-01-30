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
	* Allows you to reference a student by grade name (is that the term?)
	*
	* This is specific for tj01.
	*/
	private static $tj01_grade = array(
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

		$system_type = $I2_ARGS[1];
		
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
		} else if (!isSet($_SESSION['csl_username'])) {
			$_SESSION['csl_username'] = $_SESSION['i2_username'];
			$_SESSION['csl_password'] = $I2_AUTH->get_user_password();
		} else {
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
		case 'default':
			throw new I2Exception("Unknown filesystem type $system_type");
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
			$this->filesystem->delete_file($this->directory . $I2_QUERY['rmf']);
			redirect("filecenter/$system_type"."{$this->directory}");
		} else if (isSet($I2_QUERY['rmd'])) {
			$this->filesystem->remove_dir($this->directory . $I2_QUERY['rmd']);
			redirect("filecenter/$system_type"."{$this->directory}");
		}
		
		$this->template = 'filecenter_pane.tpl';

		return array('Filecenter', 'Current directory: ' . $this->directory);
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		global $I2_SQL, $I2_USER;

		if ($this->filesystem->is_valid()) {
			$dirs = array();
			$files = array();
			
			//Add the .. directory
			if (!$this->filesystem->is_root($this->directory)) {
				$file = $this->filesystem->get_file($this->directory . '/..');
				$dirs[] = array(
					'name' => '..',
					'last_modified' => date('n/j/y g:i A', $file->last_modified()) 
				);
			}

			foreach($this->filesystem->list_files($this->directory) as $file) {
				$properties = array(
					"name" => $file->get_name(),
					"size" => self::human_readable_size($file->get_size()),
					"last_modified" => date("n/j/y g:i A", $file->last_modified())
				);
			
				if (!$this->show_hidden_files && $file->is_hidden()) {
					continue;
				}
			
				if ($file->is_directory()) {
					$dirs[] = $properties;
				} else {
					$files[] = $properties;
				}
			}
		
			sort($dirs);
			sort($files);
		
			$this->template_args['dirs'] = $dirs;
			$this->template_args['files'] = $files;
			$this->template_args['curdir'] = $this->directory;
		}
		
		$display->disp($this->template, $this->template_args);
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
			$this->box_args['tj01path'] = 'students/' . self::$tj01_grade[$I2_USER->grade] . '/' . $_SESSION['i2_username'];
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
