<?php
	function smarty_block_i2form($params,$content,&$smarty,&$repeat) {
		if ($content == null) {
			return "<form method='post' action='core.php'>";
		} else {
			return "$content</form>";
		}	
	}
?>
