<?php
/**
* Just contains the definition for the class {@link Newsitem}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage News
* @filesource
*/

/**
* The module that takes care of news items, posting, etc.
* @package modules
* @subpackage News
*/
class Newsitem {

	private $mynid;

	public function __get($var) {
		global $I2_SQL;
		switch($var) {
			case 'id':
			case 'nid':
				return $this->mynid;
			case 'title':
				return $I2_SQL->query('SELECT title FROM news WHERE id=%d',$this->mynid)->fetch_single_value();
			case 'text':
				return $I2_SQL->query('SELECT text FROM news WHERE id=%d',$this->mynid)->fetch_single_value();
			case 'authorID':
				return $I2_SQL->query('SELECT authorID FROM news WHERE id=%d',$this->mynid)->fetch_single_value();
			case 'author':
				$user = new User($this->__get('authorID'));
				return $user;
			case 'revised':
				return $I2_SQL->query('SELECT revised FROM news WHERE id=%d',$this->mynid)->fetch_single_value();
			case 'posted':
				return $I2_SQL->query('SELECT posted FROM news WHERE id=%d',$this->mynid)->fetch_single_value();
			case 'groups':
				return $I2_SQL->query('SELECT gid FROM news_group_map WHERE nid=%d',$this->mynid)->fetch_all_single_values();
			case 'groupsstring':
				$groupsarray = array();
				$gidsarray = $this->__get('groups');
				foreach($gidsarray as $gid) {
					$group = new Group($gid);
					$groupsarray[] = $group->name;
				}
				return implode(', ', $groupsarray);
		}
	}

	public function __construct($nid) {
		$this->mynid = $nid;

		if(!$this->item_exists()) {
			throw new I2Exception("News item $nid was referenced, but does not exist");
		}
	}

	public static function get_all_items() {
		global $I2_SQL;
		$items = array();
		foreach($I2_SQL->query('SELECT id FROM news ORDER BY posted DESC')->fetch_all_single_values() as $nid) {
			$items[] = new Newsitem($nid);
		}
		return $items;
	}

	public static function post_item($author, $title, $text, $groupnames = NULL) {
		global $I2_SQL;

		if ($groupnames != NULL) {
			$groups = array();
			foreach (explode(',', $groupnames) as $groupname) {
				$groupname = trim($groupname);
				$groups[] = new Group($groupname);
			}
		}

		$I2_SQL->query('INSERT INTO news SET authorID=%d, title=%s, text=%s, posted=CURRENT_TIMESTAMP', $author->uid, $title, $text);
		$nid = $I2_SQL->query('SELECT LAST_INSERT_ID()')->fetch_single_value();

		if(isset($groups)) {
			foreach ($groups as $group) {
				$I2_SQL->query('INSERT INTO news_group_map SET nid=%d, gid=%d', $nid, $group->gid);
			}
		}
	}

	public static function delete_item($nid) {
		global $I2_SQL;
		$I2_SQL->query('DELETE FROM news WHERE id=%d', $nid);
		$I2_SQL->query('DELETE FROM news_group_map WHERE nid=%d', $nid);
		$I2_SQL->query('DELETE FROM news_read_map WHERE nid=%d',$nid);
	}
	
	public function item_exists() {
		global $I2_SQL;
		if ($I2_SQL->query('SELECT id FROM news WHERE id=%d', $this->mynid)->fetch_single_value() != NULL) {
			return true;
		}
		return false;
	}

	public function edit($title, $text, $groupnames = NULL) {
		global $I2_SQL;

		if ($groupnames != NULL) {
			$groups = array();
			foreach (explode(',', $groupnames) as $groupname) {
				$groupname = trim($groupname);
				$groups[] = new Group($groupname);
			}
		}

		$I2_SQL->query('UPDATE news SET title=%s, text=%s, revised=CURRENT_TIMESTAMP WHERE id=%d', $title, $text, $this->mynid);

		// flush the group mappings for this post and recreate them entirely
		$I2_SQL->query('DELETE FROM news_group_map WHERE nid=%d', $this->mynid);
		if(isset($groups)) {
			foreach ($groups as $group) {
				$I2_SQL->query('INSERT INTO news_group_map SET nid=%d, gid=%d', $this->mynid, $group->gid);
			}
		}
	}

	public function readable($user = NULL) {
		global $I2_SQL;

		if($user===NULL) {
			$user = $GLOBALS['I2_USER'];
		}

		$gids = $this->groups;
		if(count($gids) == 0) {
			// if no groups were specified, anyone can read it
			return true;
		}

		if($this->authorID == $user->uid) {
			// author can always read
			return true;
		}

		if($user->is_group_member('admin_news')) {
			// news admins can read anything
			return true;
		}

		foreach($gids as $gid) {
			$group = new Group($gid);
			if($group->has_member($user)) {
				return true;
			}
		}
		
		// User was in none of the groups
		return false;
	}

	public function has_been_read($user = NULL) {
		global $I2_SQL;

		if($user===NULL) {
			$user = $GLOBALS['I2_USER'];
		}
		
		$ret = $I2_SQL->query('SELECT * FROM news_read_map WHERE nid=%d AND uid=%d', $this->mynid, $user->uid)->fetch_single_value();
		if(isset($ret)) {
			return true;
		}
		
		return false;
	}

	public function editable($user = NULL) {
		global $I2_SQL;
		
		if($user===NULL) {
			$user = $GLOBALS['I2_USER'];
		}

		if($user->uid == $this->authorID || $user->is_group_member('admin_news')) {
			return true;
		}

		return false;
	}
}
?>
