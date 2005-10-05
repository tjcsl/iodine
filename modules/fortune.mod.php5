<?php

	class fortune implements Module {

		private $fortune;

		public function init_box() {
			if (!isSet($_REQUEST['fortune_regen']) && isSet($_SESSION['fortune'])) {
				d('Old fortune retrieved.');
				$this->fortune = $_SESSION['fortune'];
			} else {
				$this->regen();
			}
			return "Fortune";
		}

		public function regen() {
			d('Generating new fortune...');
			$handle = popen('/usr/games/fortune','r');
			$this->fortune = '';
			while (!feof($handle)) {
				$this->fortune .= fgets($handle,1024);
			}
			pclose($handle);
			d('New fortune `'.$this->fortune."' generated.");
			$_SESSION['fortune'] = $this->fortune;

		}

		public function display_box($display) {
			$display->disp('fortune_box.tpl',array('fortune'=>$this->fortune));
		}

		public function init_pane() {
			return false;
		}

		public function display_pane($display) {
		}

		public function get_name() {
			return "Fortune";
		}
	
	}

?>
