<?php
/**
* Just contains the definition for the {@link Module} {@link Privacy}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005-2006 The Intranet 2 Development Team
* @package core
* @subpackage Module
* @filesource
*/

/**
* A {@link Module} to set the parental privacy controls on a student.
* @package core
* @subpackage Module
*/
class Privacy extends Module {

	private $template = 'view.tpl';
	private $template_args = [];

	public function init_pane() {
		global $I2_USER,$I2_AUTH,$I2_ARGS;
		if (isset($_REQUEST['update'])) {
			$user = new User($_REQUEST['uid']);
			$prefs = array(
				'showaddressself','showphoneself','showbdayself','showscheduleself','showpictureself','showfreshmanpictureself','showsophomorepictureself','showjuniorpictureself','showseniorpictureself','showlockerself','showeighthself','showaddress','showphone','showbdate','showschedule','showpictures','showlocker','showeighth');
			foreach ($prefs as $pref) {
				if (isset($_REQUEST['perm_'.$pref])) {
					$user->$pref = 'TRUE';
				} else {
					$user->$pref = 'FALSE';
				}
			}
			Search::clear_results();
			//redirect('privacy/'.$user->uid);
		}
		if ($I2_USER->is_ldap_admin()) {
			$this->template = 'master.tpl';
			$this->template_args['first_year'] = User::get_gradyear(12);
			if (isset($I2_ARGS[1])) {
				$this->template_args['user'] = new User($I2_ARGS[1]);
				$photonames = $this->template_args['user']->photonames;
				$this->photonames = [];
				foreach ($photonames as $photo) {
					$text = ucfirst(strtolower(substr($photo, 0, -5)));
					$this->photonames[$photo] = $text;
				}
				$this->template_args['photonames'] = $this->photonames;
			} else {
				$res = Search::get_results();
				if ($res) {
					$this->template_args['info'] = $res;
				}
			}
			return array('Privacy','Change privacy settings');
		} else {
			$this->template_args['user'] = $I2_USER;
			$photonames = $this->template_args['user']->photonames;
			$this->photonames = [];
			foreach ($photonames as $photo) {
				$text = ucfirst(strtolower(substr($photo, 0, -5)));
				$this->photonames[$photo] = $text;
			}
			$this->template_args['photonames'] = $this->photonames;
			return array('Privacy','Your privacy info');
		}
	}

	public function display_pane($display) {
		$display->disp($this->template,$this->template_args);
	}

	public function get_name() {
		return 'Privacy';
	}

}
?>
