<?php
/**
* Just contains the definition for the class {@link LostItem}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2012 The Intranet 2 Development Team
* @package modules
* @subpackage LostNFound
* @filesource
*/

/**
* The module that keeps track of lost items
* @package modules
* @subpackage LostNFound
*/
class LostItem {

	/**
	 * The id number for this lostitem
	 */
	private $myid;

	/**
	* An associative array containing various information about the lostitem
	*/
	protected $info = array();

	/**
	* An array of NewsItem objects whose info hasn't been fetched yet.
	*/
	private static $unfetched = array();

	/**
	 * The php magical __get method.
	 *
	 * Used as $lostitem-><field> to get a field, for example
	 * <code>
	 * $lostitem = new LostItem(2);
	 * $title = $lostitem->title;
	 * </code>
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
				return $this->myid;
			case 'owner':
				return new User($this->__get('ownerID'));
			case 'text':
			case 'title':
			case 'ownerID':
			case 'posted':
				self::fetch_data();
				break;
		}

		if(!isset($this->info[$var])) {
			
			throw new I2Exception('Invalid attribute passed to LostItem::__get(): '.$var.', or invalid item ID: '.$this->myid);
		}

		return $this->info[$var];
	}

	/**
	* Fetches group and read data for all pending items at once.
	*/
	private static function fetch_data() {
		global $I2_SQL,$I2_USER;

		if(count(self::$unfetched) < 1) {
			return;
		}
		
		// Fetches the read status
		foreach(self::$unfetched as $item) {
			$item->info['read'] = FALSE;
		}
		foreach($I2_SQL->query('SELECT `id`,`title`,`ownerID`,`posted`,`text` FROM lostitems WHERE `id` IN (%D)', array_keys(self::$unfetched)) as $row) {
			$item = self::$unfetched[$row['id']];
			$item->info['title'] = $row['title'];
			$item->info['ownerID'] = $row['ownerID'];
			$item->info['posted'] = $row['posted'];
			$item->info['text'] = $row['text'];
		}
		self::$unfetched = array();
	}

	/**
	 * The LostItem class constructor.
	 *
	 * @access public
	 * @param int $id The id of the requested news item.
	 */
	public function __construct($id, $refetch = FALSE) {
		$this->myid = $id;

		if(isset(self::$unfetched[$id]) && !$refetch) {
			$this->info = &self::$unfetched[$id]->info;
		} else {
			self::$unfetched[$id] = $this;
		}
	}

	/**
	 * All the existant newsitems which the user has access to.
	 *
	 * Gets an array of all lostitems.
	 *
	 * @static
	 * @access public
	 * @return array An array of all Newsitems.
	 */
	public static function get_all_items() {
		global $I2_SQL;
		$allitems = array();
		$qstring = 'SELECT id FROM lostitems ORDER BY posted DESC';
		foreach($I2_SQL->query($qstring)->fetch_all_single_values() as $id) {
			$allitems[] = new LostItem($id);
		}
		return $allitems;
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
		//$text = preg_replace("/\r\n|\n|\r/", " ", $text);
		$text = preg_replace('/\r\n|\n|\r|<br\\s*?\\/??>/i', "<br />", $text);
		//$text = str_replace('"','&quot;',$text);
		return $text;
	}


	/**
	 * Creates a new lostitem in the database.
	 *
	 * @static
	 * @access public
	 * @param string $owner The owner of the item
	 * @param string $title The title of the listing
	 * @param string $text The content of the listing
	 */
	public static function create_item($owner, $title, $text) {
		global $I2_SQL,$I2_USER;
		
		// if no user is specified, assume the current user
		if(!isset($owner) || $owner == null) {
			$owner = $I2_USER->uid;
		}
		
		$text = self::clean_text($text);
		$I2_SQL->query('INSERT INTO lostitems SET ownerID=%d, title=%s, text=%s, posted=CURRENT_TIMESTAMP', $owner->uid, $title, $text);

		$id = $I2_SQL->query('SELECT LAST_INSERT_ID()')->fetch_single_value();
		
		return true;
	}


	/**
	 * The function to delete a news item.
	 *
	 * Removes the requested item from the database.
	 *
	 * @static
	 * @access public
	 * @param integer $id The id number of the item to delete.
	 */
	public static function delete_item($id) {
		global $I2_SQL,$I2_USER;
		
		$item = new LostItem($id);
		if($item->editable()) {
			$I2_SQL->query('DELETE FROM lostitems WHERE id=%d', $id);
		} else {
			throw new I2Exception("You do not have permission to delete this item.");
		}
	}

	/**
	* Deletes this news item.
	*
	* @see delete_item
	*/
	public function delete() {
		return self::delete_item($this->myid);
	}
	
	/**
	 * Finds out if this lostitem object exists in the database. This is
	 * just an internal method, called by the constructor, to make the code
	 * look easier and more modularized.
	 *
	 * @return boolean Whether this item exists or not.
	 */
	private function item_exists() {
		global $I2_SQL;
		if ($I2_SQL->query('SELECT id FROM lostitems WHERE id=%d', $this->myid)->fetch_single_value() != NULL) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Updates the title and text of a lostitem
	 *
	 * @access public
	 * @param string $title The new title for the news post.
	 * @param string $text The new content for the news post.
	 */
	public function edit($title, $text) {
		global $I2_SQL;
		
		if(!$this->editable()) {
			throw new I2Exception("You do not have permission to edit this item");
		}
		
		$I2_SQL->query('UPDATE lostitems SET title=%s, text=%s WHERE id=%d', $title, $text, $this->myid);
	}

	/**
	 * Determine if this item is editable by a user.
	 *
	 * Returns whether a user (by default, the current user) has permission
	 * to edit this post.
	 *
	 * @access public
	 * @param User $user The user for which to determine permission.
	 * @return boolean Whether the item is editable or not
	 */
	public function editable($user = NULL) {
		global $I2_SQL;
		
		if($user === NULL) {
			$user = $GLOBALS['I2_USER'];
		}

		if($user->uid == $this->ownerID || $user->is_group_member('admin_all')) {
			return true;
		}

		/*foreach ($this->__get('groups') as $group) {
			if (! $group->has_permission($user, Permission::getPermission(News::PERM_POST))) {
				return false;
			}
		}

		return true;*/
		return false;
	}
}
?>
