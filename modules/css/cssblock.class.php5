<?php
/**
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage CSS
* @filesource
*/

/**
* @package modules
* @subpackage CSS
*/
class CSSBlock {

	private $name;

	private $parent;

	public function __construct($name = '`DEFAULT`', $parent = NULL) {
		$this->name = $name;
		$this->parent = $parent;
	}

	public function get_parent() {
		return $this->parent;
	}

	public function get_name() {
		return $this->name;
	}
}

?>
