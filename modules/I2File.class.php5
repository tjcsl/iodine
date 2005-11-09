<?php


define("FILES_ONLY",1);
define("DIRS_ONLY",2);
define("FILES_AND_DIRS",3);
				
class I2File {
				
	private $path;
	private $file;
	private $filename;
	private $buffer;
	private $isdir;
	private $readout = FALSE;
	private $rmed = FALSE;
				
	function __autoconstruct($file) {
		//TODO: strip path into $this->path and last bit into $this->file
		//TODO: assign $this->isdir if the file ends with a /
	}

	private function checkrm() {
		if ($this->rmed) {
			throw new I2Exception("A removed file was accessed!");
		}
	}

	private function checkdir($dirisokay=TRUE) {
		$this->checkrm();
		if ($this->is_directory() != $dirisokay) {
			throw new I2Exception("An attempt was made to read a directory as a file!");
		}
	}

	public function get_full_name() {
		$this->checkrm();
		if ($this->is_directory()) {
			return $this->path.'/';
		}
		return $this->path.'/'.$this->filename;
	}
	
	public function get_name() {
		$this->checkrm();
		return $this->filename;
	}

	public function read_line() {
		$this->checkdir();
		//TODO: this
	}

	public function get_contents() {
		$this->checkdir();
		if (!$this->buffer) {
			$this->buffer = fgets($this->file);
		}
		return $this->buffer;
	}

	public function is_directory() {
		$this->checkrm();
		return $this->isdir;
	}

	public function rm() {
		$this->checkdir();
		exec('i2_rm "'.$this->get_full_name().'"');
		$this->rmed = TRUE;
	}

	public function rm_recursive() {
		$this->checkrm();
		exec('i2_rmdir "'.$this->get_full_name().'"');
		$this->rmed = TRUE;
	}

	public function mv($dest) {
		$this->checkrm();
		exec('i2_mv "'.$this->get_full_name().'" "'.$dest.'"');
	}

	public function ls($type=FILES_AND_DIRS) {
		$this->checkdir(TRUE);
		$ls = exec('i2_ls "'.$this->get_full_name().'"');
		//TODO: this
	}
				
}

				
?>
