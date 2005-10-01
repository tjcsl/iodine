<?php
/**
* Just contains one smarty function to create an html form.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package smarty
*/

/**
* A smarty function to make an html form.
*
* Just creates a simple HTML form, parameters are Smarty standard.
*/
function smarty_block_i2form($params,$content,&$smarty,&$repeat) {
	if ($content == null) {
		return "<form method='post' action='core.php'>";
	} else {
		return "$content</form>";
	}	
}
?>
