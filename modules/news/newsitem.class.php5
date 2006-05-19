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
class NewsItem {

	/**
	 * The id number for this newsitem.
	 */
	private $mynid;

	/**
	 * The php magical __get method.
	 *
	 * Used as $newsitem-><field> to get a field, for example
	 * <code>
	 * $newsitem = new Newsitem(2);
	 * $title = $newsitem->title;
	 * </code>
	 * In addition to basic fields, there is also a "groupsstring" field
	 * that returns a string containing all of the newsitem's groups,
	 * seperated by commas.
	 *
	 * @access public
	 * @param mixed $var The field for which to get data.
	 * @return mixed The requested data.
	 */
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
				return new User($this->__get('authorID'));
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
					try {
						$group = new Group($gid);
						$groupsarray[] = $group->name;
					} catch (I2Exception $e) {
					}
				}
				return implode(', ', $groupsarray);
		}
	}

	/**
	 * The Newsitem class constructor.
	 *
	 * This takes a news id number as an argument. If the requested newsitem
	 * does not exist, an {@link I2Exception} is thrown.
	 *
	 * @access public
	 * @param int $nid The id of the requested news item.
	 */
	public function __construct($nid) {
		$this->mynid = $nid;

		if(!$this->item_exists()) {
			throw new I2Exception("News item $nid was referenced, but does not exist");
		}
	}

	/**
	 * All the existant newsitems.
	 *
	 * Gets an array of all newsitems.
	 *
	 * @static
	 * @access public
	 * @return array An array of all Newsitems.
	 */
	public static function get_all_items() {
		global $I2_SQL;
		$items = array();
		foreach($I2_SQL->query('SELECT id FROM news ORDER BY posted DESC')->fetch_all_single_values() as $nid) {
			$items[] = new Newsitem($nid);
		}
		return $items;
	}

	/**
	 * The function to clean text for input
	 *
	 * Prepairs text for insertion in MySQL
	 *
	 * @static
	 * @access public
	 * @param string $text The text to clean
	 */

	public static function clean_text($text) {
			  $text = str_replace('&','&amp;',$text);
			  $text = str_replace('\r','<br />',$text);
			  $text = str_replace('"','&quot;',$text);
			  $text = str_replace('\'','&#039;',$text);
			  return $text;
	}


	/**
	 * The function to post a brand-new news item.
	 *
	 * Creates a new newsitem in the database.
	 *
	 * @static
	 * @access public
	 * @param string $author The author of the news post.
	 * @param string $title The title of the news post.
	 * @param string $text The content of the news post.
	 * @param string $group
	 */
	public static function post_item($author, $title, $text, $groups) {
		global $I2_SQL,$I2_USER;

		$newsadm = new Group('admin_news');
		if(!$newsadm->has_member()) {
			foreach($groups as $group) {
				if(!$group->has_permission($I2_USER,News::PERM_POST)) {
					throw new I2Exception("You do not have permission to post to the group {$group->name}");
				}
			}
		}

	//	$text = self::clean_text($text);

		$I2_SQL->query('INSERT INTO news SET authorID=%d, title=%s, text=%s, posted=CURRENT_TIMESTAMP', $author->uid, $title, $text);

		$nid = $I2_SQL->query('SELECT LAST_INSERT_ID()')->fetch_single_value();
		
		foreach ($groups as $group) {
				  if($group->gid == NULL) {
							 break;
				  }
				  $I2_SQL->query('INSERT INTO news_group_map SET nid=%d, gid=%d', $nid, $group->gid);
		}

		return true;
	}


	/**
	 * The function to delete a news item.
	 *
	 * Removes the requested item from the database.
	 *
	 * @static
	 * @access public
	 * @param integer $nid The id number of the post to delete.
	 */
	public static function delete_item($nid) {
		global $I2_SQL;
		$I2_SQL->query('DELETE FROM news WHERE id=%d', $nid);
		$I2_SQL->query('DELETE FROM news_group_map WHERE nid=%d', $nid);
		$I2_SQL->query('DELETE FROM news_read_map WHERE nid=%d',$nid);
	}

	/**
	* Deletes this news item.
	*
	* @see delete_item
	*/
	public function delete() {
		return self::delete_item($this->mynid);
	}
	
	/**
	 * Determine if this news item exists.
	 *
	 * Finds out if this Newsitem object exists in the database. This is
	 * just an internal method, called by the constructor, to make the code
	 * look easier and more modularized.
	 *
	 * @return boolean Whether this item exists or not.
	 */
	private function item_exists() {
		global $I2_SQL;
		if ($I2_SQL->query('SELECT id FROM news WHERE id=%d', $this->mynid)->fetch_single_value() != NULL) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Edit this news post.
	 *
	 * Updates the title, content, and groups of a newsitem.
	 *
	 * @access public
	 * @param string $title The new title for the news post.
	 * @param string $text The new content for the news post.
	 * @param string $groupnames The new comma-seperated list of groups.
	 */
	public function edit($title, $text, $groups) {
		global $I2_SQL;

		$I2_SQL->query('UPDATE news SET title=%s, text=%s WHERE id=%d', $title, $text, $this->mynid);

		// flush the group mappings for this post and recreate them entirely
		$I2_SQL->query('DELETE FROM news_group_map WHERE nid=%d', $this->mynid);
		foreach ($groups as $group) {
				if($group->gid == NULL) {
						  break;
				}
				$I2_SQL->query('INSERT INTO news_group_map SET nid=%d, gid=%d', $this->mynid, $group->gid);
		}
	}

	/**
	 * Determine whether a user can read the newsitem.
	 *
	 * Finds whether the supplied user (or, if not, the current user) is in
	 * any group that can read this news post.
	 *
	 * @access public
	 * @param User $user The user for which to determine permission.
	 */
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

	/**
	 * Determine if this news item has been read by a user.
	 *
	 * Returns whether a user (by default, the curent user) has marked
	 * this item as "read".
	 *
	 * @access public
	 * @param User $user The user for which to find the item status.
	 */
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

	/**
	 * Determine if this post is editable by a user.
	 *
	 * Returns whether a user (by default, the current user) has permission
	 * to edit this post.
	 *
	 * @access public
	 * @param User $user The user for which to determine permission.
	 */
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

	/**
	* Cross posts this news post to a set of groups.
	*
	* @param Array $groups An array of GIDs to cross post the news post to.
	*/
	public function xpost($gids) {
		global $I2_SQL, $I2_USER;

		$groups = Groups::generate($gids);

		$query = 'INSERT INTO news_group_map (nid, gid) VALUES ';
		foreach($groups as $group) {
			if(!$group->has_permission($I2_USER,News::PERM_POST)) {
				throw new I2Exception("You do not have permission to post something to {$group->name}");
			}
			$query .= "({$this->mynid},%d),";
		}
		$query = substr($query,0,-1) . ';';


	//	$I2_SQL
	}
}
?>
