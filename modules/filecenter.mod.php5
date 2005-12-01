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
	private $template = "filecenter_pane.tpl";

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

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_USER, $I2_ARGS, $I2_SQL, $I2_LOG;

		$system_type = $I2_ARGS[1];
		
		if ($system_type == 'cslauth' && isSet($_REQUEST['user']) && isSet($_REQUEST['password'])) {
			//$I2_SQL->query("INSERT INTO cslfiles (uid,user,pass) VALUES(%d,%s,%s)",);
			$_SESSION['csl_username'] = $_REQUEST['user'];
			$_SESSION['csl_password'] = $_REQUEST['password'];
			redirect('filecenter/csl/user/'.$_SESSION['csl_username']);
		} else if (!isSet($_SESSION['csl_username'])) {
			$_SESSION['csl_username'] = $_SESSION['i2_username'];
			$_SESSION['csl_password'] = $_SESSION['i2_password'];
		} else {
			$this->template_args['csl_failed_login'] = TRUE;
		}
		
		if ($system_type == 'lan') {
			$this->filesystem = new LANFilesystem($_SESSION['i2_username'], $_SESSION['i2_password']);
		} else if ($system_type == 'csl') {
			$this->filesystem = new CSLProxy($_SESSION['csl_username'], $_SESSION['csl_password']);
			if (!$this->filesystem->is_valid()) {
				$this->template = 'csl_login.tpl';
				return array('Filecenter','CSL Authentication');
			}
		} else {
			throw new I2Exception("Unknown filesystem type $system_type");
		}

		$this->directory = '/';
		
		if (count($I2_ARGS) > 2) {
			$this->directory .= implode("/", array_slice($I2_ARGS, 2));
		}

		$file = $this->filesystem->get_file($this->directory);
		if ($file->is_file()) {
			Display::stop_display();
			
			header('Content-type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . $file->get_name() . '"');
			header('Content-length: ' . $file->get_size());
			header('Pragma: public');
			echo $this->filesystem->get_file_contents($this->directory);

			die;
		}
		
		$this->template = "filecenter_pane.tpl";

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
					"size" => round($file->get_size() / 1024),
					"last_modified" => date("n/j/y g:i A", $file->last_modified())
				);
			
				if ($file->is_directory()) {
					$dirs[] = $properties;
				} else {
					$files[] = $properties;
				}

			}
		
			$this->template_args['dirs'] = $dirs;
			$this->template_args['files'] = $files;
		}
		
		$display->disp($this->template, $this->template_args);
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		if (isSet($_SESSION['csl_username'])) {
			$this->box_args['csl_username'] = $_SESSION['csl_username'];
		} else {
			$this->box_args['csl_username'] = $_SESSION['i2_username'];
		}
		return 'Filecenter';
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
		$display->disp('filecenter_box.tpl', $this->box_args);
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return "Filecenter";
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function is_intrabox() {
		return true;
	}
}

?>
