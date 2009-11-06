<?php
/**
* Just contains the definition for the class {@link FakeUser}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package core
* @subpackage User
* @filesource
*/

/**
* The fake user information module for Iodine.
* This is used for events when a user no longer exists.
* @package core
* @subpackage FakeUser
*/
class FakeUser {

	public $name_comma;
	public $uid;
	public function __construct($id,$name) {
		$this->uid=$id;
		$this->name_comma=$name;
	}
}

?>
