<?php
	/**
	* A module that makes new users.
	* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
	* @copyright 2005 The Intranet 2 Development Team
	* @version 1.0
	* @since 1.0
	* @package modules
	* @subpackage makeuser
	*/

	class Makeuser implements Module {
	
		function init_box($token) {
			return FALSE;
		}

		function display_box($display) {
		}

		function get_name() {
			return "Make User";
		}

		function init_pane($token) {
			global $I2_ARGS,$I2_SQL;
			if (isSet($I2_ARGS['makeuser_uid'])) {
				$I2_SQL->insert($token,'users',
				array('uid','fname','mname','lname',
				'bdate','phone_home','phone_cell','phone_other',
				'address_primary_city','address_primary_state','address_primary_zip','address_primary_street',
				'address_secondary_city','address_secondary_state','address_secondary_zip','address_secondary_street',
				'address_tertiary_city','address_tertiary_state','address_tertiary_zip','address_tertiary_street',
				'sn0','sn1','sn2','sn3','sn4','sn5','sn6','sn7',
				'email0','email1','email2','email3',
				'username','sex','grade','webpage','locker','counselor',
				'startpage',
				'picture0','picture1','picture2','picture3',
				'boxes'
				),
				array(
				$I2_ARGS['makeuser_uid'],$I2_ARGS['makeuser_fname'],$I2_ARGS['makeuser_mname'],$I2_ARGS['makeuser_lname'],
				$I2_ARGS['makeuser_bdate'],$I2_ARGS['makeuser_phone_home'],$I2_ARGS['makeuser_phone_cell'],$I2_ARGS['makeuser_phone_other'],
					//FIXME:  Finish this
				)
				);
				return FALSE;
			}
		}

		function display_pane($display) {
		}
	
	}

?>
