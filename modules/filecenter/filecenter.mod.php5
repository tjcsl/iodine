<?php
/**
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Filecenter
* @filesource
* Handles the various filesystems
*/

/**
* @package modules
* @subpackage Filecenter
* Handles the various filesystems
*/
class Filecenter extends Module {

	/**
	* Template for the specified action
	*/
	private $template = 'filecenter_pane.tpl';

	/**
	* Template arguments for intrabox
	*/
	private $box_args = [];

	/**
	* Template arguments for pane
	*/
	private $template_args = [];


	private $filesystem;

	private $directory;

	private $show_hidden_files;
	
	/**
	* Allows you to reference a student by class standing.
	*
	* Warning: This may not work for all shares.
	*/
	private static $standing = [
		'graduate' => 'invalid',
		'12' => 'senior',
		'11' => 'junior',
		'10' => 'sophomore',
		'9' => 'freshman'
	];

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

	private function common_init() {
		global $I2_USER, $I2_ARGS, $I2_QUERY, $I2_SQL, $I2_LOG, $I2_AUTH;

		//Make sure the address ends in a trailing slash.
		//...but only if first arg isn't cslauth.  Yes this is hackish. --wyang
		if(isset($I2_ARGS[1]) && $I2_ARGS[1] != "cslauth")
		{
			$index = strpos($_SERVER['REDIRECT_QUERY_STRING'], '?');
			if(substr($_SERVER['REDIRECT_QUERY_STRING'], $index-1, 1) != "/")
				redirect(substr($_SERVER['REDIRECT_QUERY_STRING'], 0, $index) . "/");
		}

		$system_type = isset($I2_ARGS[1]) ? $I2_ARGS[1] : 'undefined';
		
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
		else if (!isset($_SESSION['csl_username'])) {
			$_SESSION['csl_username'] = $_SESSION['i2_username'];
			$_SESSION['csl_password'] = $I2_AUTH->get_user_password();
		}
		else {
			$this->template_args['csl_failed_login'] = TRUE;
		}

		$return=false;

		//HACK: find a better way to do this
		$this->commonserver = i2config_get('cifs_common_server','','filecenter');

		eval($I2_SQL->query('SELECT code FROM filecenter_filesystems WHERE name=%s',$system_type)->fetch_single_value());
		if($return) return $return;//We have to do this, because if you return inside the eval, it just exits the eval.

		if(!isset($this->filesystem)) {
			throw new I2Exception("No filesystem found. Is this filesystem in the db?");
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
		} else if (isset($I2_QUERY['rename'])) {
			$from = $this->directory . $I2_QUERY['rename'];
			$to = $this->directory . $I2_QUERY['to'];
			if ($from != $to) {
				$this->filesystem->move_file($from, $to);
			}
			redirect("filecenter/$system_type"."{$this->directory}");
		} else if (isset($_REQUEST['mkdir'])) {
			$this->filesystem->make_dir($this->directory . $_REQUEST['mkdir']);
			redirect("filecenter/$system_type"."{$this->directory}");
		} else if (isset($I2_QUERY['rmf'])) {
			$this->filesystem->remove_file($this->directory . $I2_QUERY['rmf']);
			redirect("filecenter/$system_type"."{$this->directory}");
		} else if (isset($I2_QUERY['rml'])) {
			$this->filesystem->remove_link($this->directory . $I2_QUERY['rml']);
			redirect("filecenter/$system_type"."{$this->directory}");
		} else if (isset($I2_QUERY['rmd'])) {
			$this->filesystem->remove_dir($this->directory . $I2_QUERY['rmd']);
			redirect("filecenter/$system_type"."{$this->directory}");
		} else if (isset($I2_QUERY['rmld'])) {
			$this->filesystem->remove_link($this->directory . $I2_QUERY['rmld']);
			redirect("filecenter/$system_type"."{$this->directory}");
		} else if (isset($I2_QUERY['rmd_recursive'])) {
			$this->filesystem->remove_dir_recursive($this->directory . $I2_QUERY['rmd_recursive']);
			redirect("filecenter/$system_type"."{$this->directory}");
		}
	}

	/**
	* Incomplete: will tie in more directly to cliodine
	*/
	function init_cli() {
		self::common_init();
		return 'Filecenter';
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		self::common_init();
		
		$this->template = 'filecenter_pane.tpl';

		return array('Filecenter', 'Current directory: ' . $this->directory);
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($disp) {
		global $I2_SQL, $I2_USER, $I2_QUERY;

		// if the user didn't choose a filesystem
		if($this->filesystem == 'listing') {
			$this->template_args['i2_username'] = $_SESSION['i2_username'];
			$this->template_args['grad_year'] = $I2_USER->grad_year;
			if (isset($_SESSION['csl_username'])) {
				$this->template_args['csl_username'] = $_SESSION['csl_username'];
			}
			else {
				$this->template_args['csl_username'] = $_SESSION['i2_username'];
			}
			if ($I2_USER->grade != "staff") {
				$this->template_args['tj01path'] = 'students/' . self::$standing[$I2_USER->grade] . '/' . $_SESSION['i2_username'];
			}
			$this->template_args['dirs'] = Filecenter::get_additional_dirs();
			$this->template = 'filecenter_box.tpl';
		}
		elseif ($this->filesystem == 'bookmarks') {
			if (isset($I2_QUERY['action'])) {
				d($I2_QUERY['action']);
				switch ($I2_QUERY['action']) {
					case 'add':
						if(isset($I2_QUERY['name']) && isset($I2_QUERY['path']))
							$I2_SQL->query("INSERT INTO filecenter_folders VALUES (%d,%s,%s)",$I2_USER->uid,$I2_QUERY['path'],$I2_QUERY['name']);
						break;
					case 'remove':
						if(isset($I2_QUERY['name']) && isset($I2_QUERY['path']))
							$I2_SQL->query("DELETE FROM filecenter_folders WHERE uid=%d AND name=%s AND path=%s",$I2_USER->uid,$I2_QUERY['name'],$I2_QUERY['path']);
						break;
				}
				redirect("{$I2_ROOT}filecenter/bookmarks");
			}
			$this->template_args['dirs'] = Filecenter::get_additional_dirs_onlymine();
			$this->template = 'filecenter_bookmarks.tpl';
		}
		elseif ($this->filesystem->is_valid()) {
			$dirs = [];
			$files = [];
			
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
			// Check if the user actually CAN view the directory due to permissions.
			if (! $this->filesystem->can_do($this->directory,'list')) {
				$this->template_args['error'] = "You do not have permission to view this directory.";
				$this->template_args['sort'] = 'name';
				$this->template_args['reverse'] = false;
				$this->template_args['files'] = $files;
				$this->template_args['dirs'] = $dirs;
			} else {
				// We cache some directories just to reduce the OMG9000 request time
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
			$this->template_args['readperm'] = $this->filesystem->can_do($this->directory,'read')?'true':'false';
			$this->template_args['insertperm'] = $this->filesystem->can_do($this->directory,'insert')?'true':'false';
			$this->template_args['deleteperm'] = $this->filesystem->can_do($this->directory,'delete')?'true':'false';
			$this->template_args['writeperm'] = $this->filesystem->can_do($this->directory,'write')?'true':'false';
			$this->template_args['lockperm'] = $this->filesystem->can_do($this->directory,'lock')?'true':'false';
			$this->template_args['adminperm'] = $this->filesystem->can_do($this->directory,'administer')?'true':'false';
		}
		
		$disp->disp($this->template, $this->template_args);
	}

	/**
	* Sort by name, used for field sorting in the usort()s above.
	*/
	static function equals_name($f1,$f2) {
		//HACK: special case ..
		if($f1['name']=='..') {
			return -1;
		}
		if($f2['name']=='..') {
			return 1;
		}
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

		// Most of this stuff isn't used, but keep it just in case.
		$this->box_args['i2_username'] = $_SESSION['i2_username'];
		$this->box_args['grad_year'] = $I2_USER->grad_year;
		if (isset($_SESSION['csl_username'])) {
			$this->box_args['csl_username'] = $_SESSION['csl_username'];
		} else {
			$this->box_args['csl_username'] = $_SESSION['i2_username'];
		}
		d('grade: ' . $I2_USER->grade);
		if ($I2_USER->grade != "staff" && $I2_USER->grade != "graduate") {
			$this->box_args['tj01path'] = 'students/' . self::$standing[$I2_USER->grade] . '/' . $_SESSION['i2_username'];
		}
		// This is where most of the directories are loaded from mysql/
		$this->box_args['dirs'] = Filecenter::get_additional_dirs();

		return 'Filecenter';
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($disp) {
		$disp->disp('filecenter_box.tpl', $this->box_args);
	}

	public function handle_upload($file) {
		for($i=0;$i<count($file['name']);$i++) {
			if ($file['error'][$i] != UPLOAD_ERR_OK) {
				switch($file['error'][$i]) {
					case UPLOAD_ERR_INI_SIZE: //Max size in php
					case UPLOAD_ERR_FORM_SIZE: //Max size in html
						if(count($file['name'])>1)
							$this->template_args['error'] = "One of the files exceeded the maximum file size.";
						else
							$this->template_args['error'] = "The file exceeded the maximum file size.";
						return;
					case UPLOAD_ERR_PARTIAL:
						if(count($file['name'])>1)
							$this->template_args['error'] = "Not all of the uploaded files were fully received.";
						else
							$this->template_args['error'] = "The uploaded file was not fully received.";
						return;
					case UPLOAD_ERR_NO_FILE:
						$this->template_args['error'] = "You must select a file to upload.";
						return;
					case UPLOAD_ERR_NO_TMP_DIR:
						$this->template_args['error'] = "Internal error in Intranet. Temporary folder missing. Please report this to the Intranet staff.";
						return;
					case UPLOAD_ERR_CANT_WRITE:
						$this->template_args['error'] = "Internal error in Intranet. Disk write failed. Please report this to the Intranet staff.";
						return;
					case UPLOAD_ERR_EXTENSION:
						$this->template_args['error'] = "Internal error in Intranet. A PHP extension blocked the upload. Please report this to the Intranet staff.";
						return;
					default:
						throw new I2Exception('Error with uploaded file: ' . $file['error'][$i]);
				}
			}
	
			$this->filesystem->copy_file_into_system($file['tmp_name'][$i], $this->directory . $file['name'][$i]);
		}
	}

	public function file_header($name, $size) {
		Display::stop_display();
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $name . '"');
		header('Content-length: ' . $size);
		header('Pragma: ');
	}

	public function send_zipped_dir($path) {
		global $I2_USER;
		$zipfile = tempname('/tmp/iodine-filesystem-'.$I2_USER->username.'--', '.zip');
		$this->filesystem->zip_dir($path, $zipfile);
		$this->file_header(basename($path) . '.zip', filesize($zipfile));
		readfile($zipfile);
		unlink($zipfile);
		die;
	}

	public function send_zipped_file($path) {
		global $I2_USER;
		$zipfile = tempname('/tmp/iodine-filesystem-'.$I2_USER->username.'-', '.zip');
		$this->filesystem->zip_file($path, $zipfile);
		$this->file_header(basename($path) . '.zip', filesize($zipfile));
		readfile($zipfile);
		unlink($zipfile);
		die;
	}
	
	static function get_additional_dirs() {
		global $I2_USER, $I2_SQL, $I2_ROOT;
		$dirs = [];
		$dirs = $I2_SQL->query('SELECT path,name FROM filecenter_folders WHERE uid=%d',$I2_USER->uid)->fetch_all_arrays(Result::ASSOC);
		// Find out all of a user's groups, then dynamic groups
		$groups = $I2_SQL->query('SELECT gid FROM groups_static WHERE uid=%d',$I2_USER->uid)->fetch_all_single_values();
		$dynagroups = [];
		$dynagroupsarray = $I2_SQL->query('SELECT * FROM groups_dynamic')->fetch_all_arrays(Result::ASSOC);
		$user = $I2_USER;
		foreach ($dynagroupsarray as $dynagroup) {
			if($dynagroup['dbtype'] == 'PHP') {
				if(eval($dynagroup['query']))
					$dynagroups[] = $dynagroup['gid'];
			} // TODO: handle other types of dynamic groups. No other kinds are used right now, so it's relatively safe to have this open.
		}
		$groupdirs = $I2_SQL->query('SELECT path,name FROM filecenter_folders_groups WHERE gid IN (%D)',array_merge($groups,$dynagroups))->fetch_all_arrays(Result::ASSOC);
		$outarray = array_merge($dirs,$groupdirs);
		// Magic words. Lets the entries be customized for each student.
		$grad_year = $I2_USER->grad_year;
		$i2_username = $_SESSION['i2_username'];
		$tj01path = ($I2_USER->grade!='staff') ? 'students/' . self::$standing[$I2_USER->grade] . '/' . $i2_username : "staff";
		$studentorstaff = ($I2_USER->grade!='staff')?"students/".$grad_year:"staff"; //Used for the unix files entry
		if (isset($_SESSION['csl_username'])) {
			$csl_username = $_SESSION['csl_username'];
		} else {
			$csl_username = $i2_username;
		}
		for ($i=0;$i<sizeof($outarray);$i++) {
			$outarray[$i] = str_replace("{{grad_year}}",$grad_year,$outarray[$i]);
			$outarray[$i] = str_replace("{{i2_username}}",$i2_username,$outarray[$i]);
			$outarray[$i] = str_replace("{{tj01path}}",$tj01path,$outarray[$i]);
			$outarray[$i] = str_replace("{{studentorstaff}}",$studentorstaff,$outarray[$i]);
			$outarray[$i] = str_replace("{{csl_username}}",$csl_username,$outarray[$i]);
			$outarray[$i] = str_replace("{{I2_ROOT}}",$I2_ROOT,$outarray[$i]);
		}
		return $outarray;
	}

	static function get_additional_dirs_onlymine() {
		global $I2_USER, $I2_SQL;
		$dirs = [];
		$dirs = $I2_SQL->query('SELECT path,name FROM filecenter_folders WHERE uid=%d',$I2_USER->uid)->fetch_all_arrays(Result::ASSOC);
		return $dirs;
	}

	static function get_additional_dirs_onlygroup($gid) {
		global $I2_SQL;
		return $I2_SQL->query('SELECT path,name FROM filecenter_folders_groups WHERE gid=%d',$gid)->fetch_all_arrays(Result::ASSOC);
	}
	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return "Filecenter";
	}
}

?>
