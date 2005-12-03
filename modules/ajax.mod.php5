<?php
/**
* @package core
* @subpackage Display
*/

/**
* @package core
* @subpackage Display
*/
class Ajax {
	function returnResponse($module) {
		global $I2_SQL, $I2_ARGS;
		if($module == "intrabox") {
			$uid = $I2_ARGS[2];
			$boxes = explode(",", strtr($I2_ARGS[3], array("intrabox_" => "")));
			$boxes_todo = $I2_SQL->query("SELECT name, box_order, intrabox.boxid FROM intrabox_map LEFT JOIN intrabox USING (boxid) WHERE uid=%d AND name IN (%S) ORDER BY box_order", $uid, $boxes)->fetch_all_arrays(MYSQL_ASSOC);
			print_r($boxes);
			print_r($boxes_todo);
			if(strcasecmp($boxes_todo[0]['name'], $boxes[0]) == 0) { // Before
				$I2_SQL->query("UPDATE intrabox_map SET box_order=box_order-1 WHERE box_order > %d AND box_order < %d AND uid=%d ORDER BY box_order ASC", $boxes_todo[0]['box_order'], $boxes_todo[1]['box_order'], $uid);
				$I2_SQL->query("UPDATE intrabox_map SET box_order=%d WHERE uid=%d AND boxid=%d ORDER BY box_order ASC", $boxes_todo[1]['box_order'] - 1, $uid, $boxes_todo[0]['boxid']);
			}
			else { // After
				$I2_SQL->query("UPDATE intrabox_map SET box_order=box_order+1 WHERE box_order >= %d AND box_order < %d AND uid=%d ORDER BY box_order ASC", $boxes_todo[0]['box_order'], $boxes_todo[1]['box_order'], $uid);
				$I2_SQL->query("UPDATE intrabox_map SET box_order=%d WHERE uid=%d AND boxid=%d ORDER BY box_order ASC", $boxes_todo[0]['box_order'], $uid, $boxes_todo[1]['boxid']);
			}
			echo implode(",", flatten($I2_SQL->query("SELECT name FROM intrabox LEFT JOIN intrabox_map USING (boxid) WHERE uid=%d ORDER BY box_order ASC", $uid)->fetch_all_arrays(MYSQL_NUM)));
		}
	}
}
?>
