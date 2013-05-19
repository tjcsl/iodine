<?php
/**
 * Shows useful links for testing.
 */
class Testing extends Module {

	private $template_args = [];

	function init_pane() {
		global $I2_USER,$I2_SQL;
		if(!$I2_USER->is_group_member('admin_testing'))
			redirect();
		$this->template_args['message']="";
		if(isset($_POST['time']) && isset($_POST['type']) && isset($_POST['cost'])){
			if(isset($_POST['update'])) {
				$I2_SQL->query("UPDATE tests SET time=%s, type=%s, cost=%s WHERE id=%i",$_POST['time'],$_POST['type'],$_POST['cost'],$_POST['update']);
				$this->template_args['message']="Test Updated";
			} else {
				$I2_SQL->query("INSERT INTO tests (time,type,cost) VALUES(%s,%s,%s)",$_POST['time'],$_POST['type'],$_POST['cost']);
				$this->template_args['message']="Test Added";
			}
		}
		$this->template_args['tests']=$this->get_tests(TRUE);
		return "Testing Administration Page";
	}

	function display_pane($disp) {
		$disp->disp("testing_pane.tpl",$this->template_args);
	}

	function init_box() {
		$this->template_args['tests']=$this->get_tests();
		return "Testing";
	}

	function display_box($disp) {
		$disp->disp("testing_box.tpl", $this->template_args);
	}

	function get_name() {
		return "Testing";
	}
	function get_tests($old=FALSE) {
		global $I2_SQL;
		$res=$I2_SQL->query("SELECT * FROM tests")->fetch_all_arrays(Result::ASSOC);
		return $res;
	}
}
?>
