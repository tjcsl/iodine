<?php
/**
* Just contains the definition for the {@link Section} interface.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package core
* @subpackage Scheduling
* @filesource 
*/

/**
* The class that represents one period of one class.
* @package core
* @subpackage Scheduling
*/
interface Section {
	public function get_students();
}
?>
