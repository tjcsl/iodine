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
	* An associative array containing various information about the newsitem.
	*/
	protected $info = array();

	/**
	* An array of NewsItem objects whose info hasn't been fetched yet.
	*/
	private static $unfetched = array();

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

		if(isset($this->info[$var])) {
			return $this->info[$var];
		}

		switch($var) {
			case 'id':
			case 'nid':
				return $this->mynid;
			case 'groupsstring':
				return implode(', ', array_map(create_function('$group', 'return $group->name;'), $this->__get('groups')));
			case 'author':
				return new User($this->__get('authorID'));
			case 'text':
				$res = $I2_SQL->query("SELECT `text` FROM news WHERE `id` = %d", $this->mynid);
				if($res->num_rows() < 1) {
					throw new I2Exception('Invalid NID accessed: '.$this->mynid);
				}
				return $res->fetch_single_value();

			case 'title':
			case 'authorID':
			case 'posted':
			case 'expire':
			case 'visible':
			case 'groups':
			case 'read':
				self::fetch_data();
				break;
		}

		if(!isset($this->info[$var])) {
			
			throw new I2Exception('Invalid attribute passed to Newsitem::__get(): '.$var.', or invalid NID: '.$this->mynid);
		}

		return $this->info[$var];
	}

	/**
	* Fetches group and read data for all pending news posts at once.
	*/
	private static function fetch_data() {
		global $I2_SQL,$I2_USER;

		if(count(self::$unfetched) < 1) {
			return;
		}
		// Fetches the groups to which the item was posted
		foreach(self::$unfetched as $item) {
			$item->info['groups'] = array();
		}

		foreach($I2_SQL->query('SELECT `nid`,`gid` FROM news_group_map WHERE `nid` IN (%D)', array_keys(self::$unfetched)) as $row) {
			$item = self::$unfetched[$row['nid']];
			$item->info['groups'][] = new Group($row['gid']);
		}

		// Fetches the read status
		foreach(self::$unfetched as $item) {
			$item->info['read'] = FALSE;
		}
		foreach($I2_SQL->query('SELECT `nid` FROM news_read_map WHERE `uid` = %d AND `nid` IN (%D)', $I2_USER->uid, array_keys(self::$unfetched)) as $row) {
			self::$unfetched[$row['nid']]->info['read'] = TRUE;
		}

		foreach($I2_SQL->query('SELECT `id`,`title`,`authorID`,`posted`,`expire`, `visible` FROM news WHERE `id` IN (%D)', array_keys(self::$unfetched)) as $row) {
			$item = self::$unfetched[$row['id']];
			$item->info['title'] = $row['title'];
			$item->info['authorID'] = $row['authorID'];
			$item->info['posted'] = $row['posted'];
			$item->info['expire'] = $row['expire'] === NULL ? '' : $row['expire'];
			$item->info['visible'] = $row['visible'];
		}
		
		self::$unfetched = array();
	}

	/**
	 * The Newsitem class constructor.
	 *
	 * This takes a news id number as an argument.
	 *
	 * @access public
	 * @param int $nid The id of the requested news item.
	 */
	public function __construct($nid, $refetch = FALSE) {
		$this->mynid = $nid;

		if(isset(self::$unfetched[$nid]) && !$refetch) {
			$this->info = &self::$unfetched[$nid]->info;
		} else {
			self::$unfetched[$nid] = $this;
		}
	}

	/**
	 * All the existant newsitems which the user has access to.
	 *
	 * Gets an array of all readable newsitems.
	 *
	 * @static
	 * @access public
	 * @return array An array of all Newsitems.
	 */
	public static function get_all_items($expired = false) {
		global $I2_SQL;
		$allitems = array();
		$myitems = array();
		$qstring = 'SELECT id FROM news ' . ($expired ? '' : 'WHERE expire > NOW() OR expire IS NULL ') .
			'ORDER BY posted DESC';
		foreach($I2_SQL->query($qstring)->fetch_all_single_values() as $nid) {
			$allitems[] = new Newsitem($nid);
		}
		foreach($allitems as $item) {
			if ($item->readable()) {
				$myitems[] = $item;
			}
		}
		return $myitems;
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
		//$text = str_replace('&','&amp;',$text);
		//$text = str_replace('\r','<br />',$text);
		$text = preg_replace("/\r\n|\n|\r/", "", $text);
		$text = preg_replace('/<br\\s*?\/??>/i', "<br />", $text);
		//$text = str_replace('"','&quot;',$text);
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
	public static function post_item($author, $title, $text, $groups, $expire, $visible) {
		global $I2_SQL,$I2_USER;

		$newsadm = new Group('admin_news');
		if(!$newsadm->has_member()) {
			foreach($groups as $group) {
				if(!$group->has_permission($I2_USER,Permission::getPermission(News::PERM_POST))) {
					throw new I2Exception("You do not have permission to post to the group {$group->name}");
				}
			}
		}

		$text = self::clean_text($text);
		$I2_SQL->query('INSERT INTO news SET authorID=%d, title=%s, text=%s, posted=CURRENT_TIMESTAMP, expire=%s, visible=%d', $author->uid, $title, $text, $expire, $visible);

		$nid = $I2_SQL->query('SELECT LAST_INSERT_ID()')->fetch_single_value();
		
		foreach ($groups as $group) {
			if($group->name == NULL) {
				break;
			}
			$I2_SQL->query('INSERT INTO news_group_map SET nid=%d, gid=%s', $nid, $group->name);
		}

		// Mail out the news post to any users who have subscribed to the news and can read it.
		$groupstring = "";
		foreach($groups as $group)
			$groupstring .= $group->name . ", ";
		$groupstring = substr($groupstring, 0,-2);
		$subj = "[Iodine-news] {$title}";
		$separator = "MAIL-" . md5(date("r",time()));
		$headers = "From: " . i2config_get('sendto', 'intranet@tjhsst.edu', 'suggestion') . "\r\n";
		$headers .= "Reply-To: " . i2config_get('sendto', 'intranet@tjhsst.edu', 'suggestion') . "\r\n";
		$headers .= "Content-Type: multipart/alternative; boundary=\"" . $separator . "\"";
		$messagecontents = "Posted by " . $author->fullname . " to " . $groupstring . ":\r\n\r\n" . $text;
		$message = "--" . $separator . "\r\nContent-Type: text/plain; charset=\"iso-8859-1\"\r\n";
		$message .= strip_tags($messagecontents);
		$message .= "\r\n--" . $separator . "\r\nContent-Type: text/html; charset=\"iso-8859-1\"\r\n";
		$message .= $messagecontents;

		// Check permissions and send mail
		$news = new NewsItem($nid);
		foreach($I2_SQL->query('SELECT * FROM news_forwarding')->fetch_all_arrays() as $target) {
			$user = new User($target[0]);
			if($news->readable($user)) {
				if(gettype($user->mail)=="array") {
					foreach($user->mail as $mail)
						mail($mail,$subj,$message,$headers);
				} else {
					mail($user->mail,$subj,$message,$headers);
				}
			}
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
		global $I2_SQL,$I2_USER;
		
		$newsadm = new Group('admin_news');
		if(!$newsadm->has_member()) {
			//You can't delete a news item unless you can manage news for ALL the groups it is posted to.
			$groups = array();
			foreach($I2_SQL->query('SELECT gid FROM news_group_map WHERE nid=%d', $nid)->fetch_all_arrays() as $group)
			{
				$groups[] = new Group($group[0]);
			}
			foreach($groups as $group)
			{
				if(!$group->has_permission($I2_USER,Permission::getPermission(News::PERM_POST))) {
					throw new I2Exception("You do not have permission to delete news for the group {$group->name}");
				}
			}
		}

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
	public function edit($title, $text, $groups, $expire, $visible) {
		global $I2_SQL,$I2_USER;

		$text = self::clean_text($text);

		$newsadm = new Group('admin_news');
		if(!$newsadm->has_member()) {
			foreach($groups as $group) {
				if(!$group->has_permission($I2_USER,Permission::getPermission(News::PERM_POST))) {
					throw new I2Exception("You do not have permission to edit news for the group {$group->name}");
				}
			}
		}

		$I2_SQL->query('UPDATE news SET title=%s, text=%s, expire=%s, visible=%d WHERE id=%d', $title, $text, $expire, $visible, $this->mynid);

		// flush the group mappings for this post and recreate them entirely
		$I2_SQL->query('DELETE FROM news_group_map WHERE nid=%d', $this->mynid);
		foreach ($groups as $group) {
			if($group->name == NULL) {
				break;
			}
			$I2_SQL->query('INSERT INTO news_group_map SET nid=%d, gid=%s', $this->mynid, $group->name);
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

		$groups = $this->groups;
		if(count($groups) == 0) {
			// if no groups were specified, anyone can read it
			return TRUE;
		}

		if($this->authorID == $user->uid) {
			// author can always read
			return TRUE;
		}

		if($user->is_group_member('admin_news')) {
			// news admins can read anything
			return TRUE;
		}
		if(!$this->visible) {
			return FALSE;
		}
		foreach($groups as $group) {
			if($group->has_member($user)) {
				return TRUE;
			}
		}
		
		// User was in none of the groups
		return FALSE;
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
		global $I2_SQL,$I2_USER;

		if($user===NULL) {
			$user = $I2_USER;
		}

		if($user == $I2_USER) {
			return $this->__get('read');
		}
		
		$ret = $I2_SQL->query('SELECT * FROM news_read_map WHERE nid=%d AND uid=%d', $this->mynid, $user->uid);
		if($ret->num_rows() > 0) {
			return true;
		}
		
		return false;
	}

	/**
	 * Mark item as read
	 *
	 * Marks an item as read
	 *
	 * @access public
	 * @param User $user The user for which to mark the item as read
	 */

	public function mark_as_read($user = NULL) {
		global $I2_SQL,$I2_USER;

		if($user===NULL) {
			$user = &$I2_USER;
		}

		if($user == $I2_USER) {
			$this->info['read'] = TRUE;
		}

		$I2_SQL->query('REPLACE INTO news_read_map (nid,uid) VALUES (%d,%d)', $this->mynid, $user->uid);
	}

	/**
	 * Mark item as unread
	 *
	 * Marks an item as unread
	 *
	 * @access public
	 * @param User $user The user for which to mark the item as unread
	 */
	public function mark_as_unread($user = NULL) {
		global $I2_SQL, $I2_USER;

		if($user===NULL) {
			$user = &$I2_USER;
		}

		if($user == $I2_USER) {
			$this->info['read'] = FALSE;
		}

		$I2_SQL->query('DELETE FROM news_read_map WHERE nid=%d AND uid=%d', $this->mynid, $user->uid);
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

		foreach ($this->__get('groups') as $group) {
			if (! $group->has_permission($user, Permission::getPermission(News::PERM_POST))) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Determine whether or not this post is collapsed by a user.
	 *
	 * Returns whether a user (the current user) has collapsed this
	 * news item.
	 *
	 * @access public
	 */
	public function shaded() {
		global $I2_SQL,$I2_USER;
		$res = $I2_SQL->query('SELECT COUNT(*) FROM news_shaded_map WHERE uid=%d AND nid=%d', $I2_USER->uid, $this->mynid)->fetch_single_value();
		if($res == 0)
			return false;
		return true;
	}

}
?>
