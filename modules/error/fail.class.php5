<?php
class Fail implements Module {
		  
		  public function init_pane() {
					 return 'Failure';
		  }

		  public function init_box() {
					 return FALSE;
		  }

		  public function display_pane($disp) {
					 throw new I2Exception('Failed!');
		  }

		  public function display_box($disp) {
		  }

		  public function get_name() {
					 return 'Error';
		  }
}
?>
