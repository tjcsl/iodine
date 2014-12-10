<?php
/**
* A module that requires students to accept the policy agreement for Eighth Period.
* Default module should be changed to news after completion of this module.
* @package welcome
* @subpackage policyaccept
*/

class PolicyAccept extends Module {

	private $template_args = [];
	private $data;

	function init_pane() {
		global $I2_USER, $I2_QUERY, $I2_CACHE;
        if(isset($I2_QUERY['forcein'])) {
            $I2_USER->startpage="policyaccept";
            $I2_CACHE->remove('User', 'ldap_user_info_'.$I2_USER->iodineuidnumber);
        }
		if( isset($_POST['accept']) && $_POST['accept']) {
			//user finished the intro blurb
			$I2_USER->eighthAgreement = 'TRUE';
			//reset to news
			$I2_USER->startpage = "news";
			$I2_CACHE->remove('User','ldap_user_info_'.$I2_USER->iodineuidnumber);

			redirect();
		} else {
			$I2_USER->chrome = "FALSE";
			return array('Welcome');
		}
	}
	
	function display_pane($disp) {
		global $I2_USER, $I2_SQL;
		$disp->disp( "policyaccept.tpl", array( 'user' => $I2_USER) );
		//TODO: Perhaps there is a better way than force flushing...maybe not. --wyang 2007/09/06
		$disp->flush_buffer();
		Display::stop_display();
		$I2_USER->chrome = "TRUE";
	}

	function get_name() {
		return 'welcome';
	}

}
?>
