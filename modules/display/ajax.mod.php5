<?php
/**
 * @package core
 * @subpackage Display
 * Contains Ajax support
 */

/**
 * @package core
 * @subpackage Display
 * Contains Ajax support
 */
class Ajax {
	/**
	 * If the requested iodine module is ajax, the core will not call the
	 * display_loop for a module named ajax, but instead will call this
	 * function, where $module is I2_ARGS[1].
	 **/
	function returnResponse($module) {
		global $I2_SQL, $I2_ARGS, $I2_LDAP;

		// Stop the display of debugging information
		Display::stop_display();

		if(get_i2module($module)) {
			$mod = new $module();
			if(method_exists($mod,'ajax'))
				return $mod->ajax();
			else
				echo "Error: Attempted to use a module that does not support ajax.";
		}

      /*if($module == "intrabox") {
			$uid = $I2_ARGS[2];
			$boxes = explode(",", strtr($I2_ARGS[3], array("intrabox_" => "")));
			if($boxes[1] == "") {
				unset($boxes[1]);
			}
			$boxes_todo = $I2_SQL->query("SELECT name, box_order, intrabox.boxid FROM intrabox_map LEFT JOIN intrabox USING (boxid) WHERE uid=%d AND (name IN (%S)" . ($boxes[1] ? "" : " OR intrabox.boxid=(SELECT MAX(boxid) FROM intrabox_map WHERE uid=%d)") . ") ORDER BY box_order", $uid, $boxes, $uid)->fetch_all_arrays(MYSQLI_ASSOC);
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
			echo implode(",", flatten($I2_SQL->query("SELECT name FROM intrabox LEFT JOIN intrabox_map USING (boxid) WHERE uid=%d ORDER BY box_order ASC", $uid)->fetch_all_arrays(MYSQLI_NUM)));
      }*/
		if($module == 'webpage_title') {
			$retvalue = "";
			if(!($row = $I2_LDAP->search_base(LDAP::get_user_dn($I2_ARGS[2]), 'webpage')))
				return NULL;
			$urls = $row->fetch_single_value();

			if(!is_array($urls))
				$urls = array($urls);

			if($urls[0] == "") {
				echo "0" . "\n";
				return "0" . "\n";
			}

			echo sizeof($urls) . "\n";
			$retvalue .= sizeof($urls) . "\n";

			foreach ($urls as &$url) {
				if($handle = fopen($url, 'rb')) {
					$title = '';

					$text = '';
					while(TRUE) {
						// fread()'s maximum number of bytes at a time is 8192.
						$text .= fread($handle, 8192);
						if(feof($handle))
							break;
					}
					fclose($handle);

					$matches = array();
					preg_match('/<title>(.*)<\/title>/', $text, $matches);
					if(isset($matches[1]))
						$title =  $matches[1];
					else
						$title = $url;
					// now replace the <script> tags
					$title = strip_tags(preg_replace('/<script.*>.*<\/script>/', '', $title));
					echo $title . "\n";
					$retvalue .= $title . "\n";
				}
				else {
					echo $url;
					$retvalue .= $url;
				}
			}
			return $retvalue;
		}
	}
}
?>
