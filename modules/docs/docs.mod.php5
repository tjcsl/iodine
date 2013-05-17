<?php
/**
* Allows the uploading of files to the local Iodine server.
* These files can be accessed by specific groups the same way news posts and polls can.
* @package modules
* @subpackage Docs
*/
class Docs implements Module {

	/**
	* The display object to use
	*/
	private $display;

	/**
	* Template for the specified action
	*/
	private $template;

	/**
	* Arguments for the template
	*/
	private $template_args = [];

	/**
	* Declaring some global variables
	*/
	private $message;

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function init_mobile() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_mobile($disp) {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*/
	function init_cli() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_cli($disp) {
		return FALSE;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function api($disp) {
		return false;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*/
	function api_build_dtd() {
		return false;
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_USER, $I2_ARGS;

		if (count($I2_ARGS) <= 1) {
			$I2_ARGS[1] = 'home';
		}

		$method = $I2_ARGS[1];
		if (method_exists($this, $method)) {
			$this->$method();
			$this->template_args['method'] = $method;
			return 'Documents: ' . ucwords(strtr($method,'-',' '));
		}
		else {
			redirect('docs');
		}
	}

	function display_pane($display) {
		$display->disp($this->template, $this->template_args);
	}

	function get_name() {
		return 'I2 Documents';
	}

	function init_box() {
		return FALSE;
	}

	function display_box($display) {
	}

	/**
	* View the files you are authorized to access
	*/
	function home() {
		global $I2_USER;

		$validdocs = Doc::accessible_docs();
		$this->template_args['is_admin'] = $I2_USER->is_group_member('admin_all');
		$this->template_args['docs'] = $validdocs;
		$this->template = 'docs_pane.tpl';
	}

	function view() {
		global $I2_USER, $I2_ARGS;
		$doc = new Doc($I2_ARGS[2]);
		if($doc->can_see()) {
			$size=filesize("$doc->path");
			$begin=0;
			$cur=0;
			$end=$size;
			$filename = substr(strrchr($doc->path,'/'),1);
			if(isset($_SERVER['HTTP_RANGE'])) {
				if(preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
					$begin=intval($matches[0]);
					if(!empty($matches[1]))
						$end=intval($matches[1]);
				}
			}
			 
			if($begin>0||$end<$size)
				header('HTTP/1.0 206 Partial Content');
			else
				header('HTTP/1.0 200 OK');  
			header('Content-Description: File Transfer');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: '); // IE won't download over SSL with default "Pragma: no-cache"
			header('Content-Length: '.($end-$begin));	
			header('Accept-Ranges: bytes');
			header("Content-Range: bytes $begin-$end/$size");
			header('Content-Transfer-Encoding: binary\n');
			header('Content-Type: '.$doc->type);
			header('Content-Disposition: attachment; filename="'.$filename.'"');
	
			// For _really_ big files, like Mathematica ISOs, set the time limit to 2 hours.
			set_time_limit(10800);
			$f=fopen($doc->path,'rb');
			while(!feof($f)&&$cur<$end&&(connection_status()==0)) {
				print fread($f,min(1024*1024*2,$end-$cur));
				$cur+=1024*1024*2;
				sleep(1);
			}
			exit;
		}
		else {
			$this->home();
		}
	}

	/**
	* Upload a document
	*/
	function add() {
		global $I2_USER, $I2_ARGS;
		$this->template_args['groups'] = Group::get_all_groups();
		$allowed_extensions = array('.txt','.rtf','.doc','.docx','.pdf','.jpg');
		$allowed_types = array('text/plain','application/rtf','application/msword','application/vnd.ms-excel','application/pdf','application/vnd.ms-office','application/octet-stream','image/jpeg');
		$this->template_args['exts'] = implode(', ',$allowed_extensions);
		$max_size = 10485760; // 10 MB
		if(count($_POST) > 0) {
			$fname = $_FILES['upfile']['name'];
			$typer = new finfo(FILEINFO_MIME_TYPE);
			$filetype = $typer->file($_FILES['upfile']['tmp_name']);
			if(in_array($filetype,$allowed_types) && filesize($_FILES['upfile']['tmp_name']) <= $max_size) {
				$upload_dir = i2config_get('upload_dir', NULL, 'core');
				if(is_writable($upload_dir)) {
					if(move_uploaded_file($_FILES['upfile']['tmp_name'],$upload_dir.$fname)) {
						$name = $_POST['name'];
						$path = $upload_dir.$fname;
						$visible = isset($_POST['visible']) && $_POST['visible'] == 'on' ? 1 : 0;
						$d = Doc::add_doc($name, $path, $visible, $filetype);
						$_POST['groups'] = array_unique($_POST['groups']);
						foreach($_POST['groups'] as $key => $id) {
							$g = $_POST['group_gids'][$id];
							$d->add_group_id($g, array(isset($_POST['view'][$id])?1:0, isset($_POST['edit'][$id])?1:0));
						}
						$_POST = [];
						$I2_ARGS[2] = $d->docid;
						$this->edit();
					} else {
						$this->template_args['error'] = "There was an error placing the file in ".$upload_dir;
					}
				} else {
					$this->template_args['error'] = "The file upload directory is not writable. It may need to have `chmod 777` run against it.";
				}
			} else {
				$this->template_args['error'] = "Either your file was too big, or it was an improper file type. ($filetype)";
			}
			if(isset($this->template_args['error'])) {
				$this->template = 'docs_add.tpl';
			}
			else {
				redirect('docs');
			}
		} else {
			$this->template_args['error'] = NULL;
			$this->template = 'docs_add.tpl';
		}
	}

	/**
	* Edit a document
	*/
	function edit() {
		global $I2_USER, $I2_ARGS;
		$doc = new Doc($I2_ARGS[2]);
		
		$this->template = 'docs_edit.tpl';
		$upload_dir = i2config_get('upload_dir', NULL, 'core');
		$this->template_args['upload_dir'] = $upload_dir;
		$this->template_args['doc'] = $doc;
		$this->template_args['groups'] = Group::get_all_groups();
		//FIXME: implement support for multiple groups
		$gid = array_keys($doc->groups)[0];
		$this->template_args['gid'] = $gid;
		$this->template_args['view'] = $doc->groups[$gid][0];
		$this->template_args['edit'] = $doc->groups[$gid][1];
		$this->template_args['error'] = NULL;
		$typer = new finfo(FILEINFO_MIME_TYPE);
		$this->template_args['type'] = $typer->file($doc->path);
		if(count($_POST)>0) {
			if(empty($_POST['name']) || empty($_POST['path']) || empty($_POST['type'])) {
				$this->template_args['error'] = "Blank fields are not allowed.";
			}
			else {
				$gid = $_POST['group_gids'][$_POST['groups'][0]];
				$doc->edit_doc($_POST['name'], $upload_dir.$_POST['path'], isset($_POST['visible'])?1:0, $_POST['type']);
				$doc->edit_group_id($gid, array(isset($_POST['view'])?1:0, isset($_POST['edit'])?1:0));
				redirect('docs');
			}
		}
	}

	/**
	* Delete a document
	*/
	function delete() {
		global $I2_USER, $I2_ARGS, $I2_SQL;
		if(!isset($I2_ARGS[2]))
			$this->home();
		$docid = $I2_ARGS[2];
		$name = $I2_SQL->query('SELECT name FROM docs WHERE docid=%d',$docid)->fetch_single_value();
		$this->template_args['docname'] = $name;
		Doc::delete_doc($docid);
		$this->template = 'docs_delete.tpl';
	}
}
?>
