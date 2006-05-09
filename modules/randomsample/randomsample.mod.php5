<?php
class Randomsample implements Module {

	private $sample;

	private function take_sample($filter,$size,$attrs) {
		global $I2_LDAP,$I2_LOG;
		$samp = array();
		$res = $I2_LDAP->search('ou=people',$filter,$attrs);
		$pop = $res->fetch_all_arrays();
		$popsize = count($pop);
		$numselected = 0;
		if ($size > $popsize) {
			return -1;
		}
		while ($numselected < $size) {
			$choice = rand(0,$popsize-$numselected);
			$samp[] = $pop[$choice];
			array_splice($pop,$choice,1);
			$numselected++;
		}
		return $samp;
	}

	public function init_pane() {
		global $I2_ARGS;
		if (!isSet($I2_ARGS[1]) || $I2_ARGS[1] != 'results') {
			return 'Take a Random Sample';
		} else {
			$this->sample = $this->take_sample($_REQUEST['filter'],
							   $_REQUEST['size'],explode(',',$_REQUEST['attrs']));
			return 'Sample Results';
		}
	}

	public function display_pane($disp) {
		$args = array();
		if (isSet($this->sample)) {
			$args['sample'] = $this->sample;
		}
		$disp->disp('randomsample_pane.tpl',$args);
	}

	public function init_box() {
		return FALSE;
	}

	public function display_box($disp) {
	}

	public function get_name() {
		return 'Random Sample';
	}

}
?>
