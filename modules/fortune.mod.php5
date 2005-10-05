<?php

	class fortune implements Module {

		private $fortune;

		public function init_box() {
			if (isSet($_SESSION['fortune'])) {
				$this->fortune = $_SESSION['fortune'];
			} else {
				$handle = popen('fortune','r');
				$this->fortune = '';
				while ($read = fread($handle,2096)) {
					$this->fortune .= $read;
				}
				pclose($handle);
				$_SESSION['fortune'] = $this->fortune;
			}
			return "Fortune";
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
