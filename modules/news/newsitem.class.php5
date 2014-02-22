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
	protected $info = [];

	/**
	* An array of NewsItem objects whose info hasn't been fetched yet.
	*/
	private static $unfetched = [];

	/**
	* The list of what's shaded and what's not.
	*/
	private static $shadecache;
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
			case 'title':
			case 'authorID':
			case 'posted':
			case 'expire':
			case 'visible':
			case 'groups':
			case 'read':
			case 'public':
			case 'liked':
			case 'likecount':
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
			$item->info['groups'] = [];
		}

		foreach($I2_SQL->query('SELECT nid,gid FROM news_group_map WHERE nid IN (%D)', array_keys(self::$unfetched)) as $row) {
			$item = self::$unfetched[$row['nid']];
			try{
				$item->info['groups'][] = new Group($row['gid']);
			} catch (I2Exception $e) {
				d('Group '.$row['gid'].' no longer exists, skipping...',3);
			}
		}

		// Fetches the read status
		foreach(self::$unfetched as $item) {
			$item->info['read'] = FALSE;
		}
		foreach($I2_SQL->query('SELECT nid FROM news_read_map WHERE uid = %d AND nid IN (%D)', $I2_USER->uid, array_keys(self::$unfetched)) as $row) {
			self::$unfetched[$row['nid']]->info['read'] = TRUE;
		}

		foreach($I2_SQL->query('SELECT id,title,authorID,posted,expire,visible,public,text FROM news WHERE id IN (%D)', array_keys(self::$unfetched)) as $row) {
			$item = self::$unfetched[$row['id']];
			$item->info['title'] = $row['title'];
			$item->info['authorID'] = $row['authorID'];
			$item->info['posted'] = $row['posted'];
			$item->info['expire'] = $row['expire'] === NULL ? '' : $row['expire'];
			$item->info['visible'] = $row['visible'];
			$item->info['public'] = $row['public'];
			$item->info['text'] = $row['text'];
			$item->info['liked'] = 0;
			$item->info['likecount'] = 0;
		}
		
		// get the number of users who have "liked" the news post
		// check if the user has "liked" the news post with the uid=%d select trick
		$checkstr='uid='.$I2_USER->uid;
		foreach($I2_SQL->query('SELECT COUNT(*),nid,uid=%d FROM news_likes WHERE nid IN (%D) GROUP BY nid', $I2_USER->uid, array_keys(self::$unfetched)) as $row) {
			self::$unfetched[$row['nid']]->info['likecount'] = $row['COUNT(*)'];
			self::$unfetched[$row['nid']]->info['liked'] = $row[$checkstr];
		}

		self::$unfetched = [];
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
		$allitems = [];
		$myitems = [];
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
	 * All the existant newsitems which anyone has access to.
	 *
	 * Gets an array of all readable newsitems.
	 *
	 * @static
	 * @access public
	 * @return array An array of all Newsitems.
	 */
	public static function get_all_items_nouser($expired = false) {
		global $I2_SQL;
		$allitems = [];
		$myitems = [];
		$qstring = 'SELECT id FROM news ' . ($expired ? '' : 'WHERE expire > NOW() OR expire IS NULL ') .
			'ORDER BY posted DESC';
		foreach($I2_SQL->query($qstring)->fetch_all_single_values() as $nid) {
			$allitems[] = new Newsitem($nid);
		}
		foreach($allitems as $item) {
			if($item->visible)
				$myitems[] = $item;
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
		// run through HTMLPurifier
		global $I2_FS_ROOT;
		require_once($I2_FS_ROOT."lib/HTMLPurifier.standalone.php");
		$purifier = new HTMLPurifier();
		$text = $purifier->purify($text);
		//$text = str_replace('&','&amp;',$text);
		//$text = str_replace('\r','<br />',$text);
		$text = preg_replace("/\r\n|\n|\r/", " ", $text);
		$text = preg_replace('/<br\\s*?\/??>/i', '<br />', $text);
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
	public static function post_item($author, $title, $text, $groups, $expire, $visible, $public) {
		global $I2_SQL,$I2_USER,$I2_ROOT;

		$newsadm = new Group('admin_news');
		if(!$newsadm->has_member()) {
			foreach($groups as $group) {
				if(!$group->has_permission($I2_USER,Permission::getPermission(News::PERM_POST))) {
					throw new I2Exception("You do not have permission to post to the group {$group->name}");
				}
			}
		}

		$text = self::clean_text($text);
		$I2_SQL->query('INSERT INTO news SET authorID=%d, title=%s, text=%s, posted=CURRENT_TIMESTAMP, expire=%s, visible=%d, public=%d', $author->uid, $title, $text, $expire, $visible, $public);

		$nid = $I2_SQL->query('SELECT LAST_INSERT_ID()')->fetch_single_value();
		
		foreach ($groups as $group) {
			if($group->name == NULL) {
				break;
			}
			$I2_SQL->query('INSERT INTO news_group_map SET nid=%d, gid=%s', $nid, $group->name);
		}

		d("Notifying");
		self::notify_email($author, $title, $text, $groups, $nid);
		// Update the feeds.
		Feeds::update();
		self::notify_twitter($author, $title, $text, $groups, $nid);
		/*
		//Post to Twitter.
		$test1=TRUE;
		if($public==0) //Only display stuff that's public.
			$test1=FALSE;
		$test2=FALSE;	//Stuff only goes on the feed if "all" can see it.
		foreach ($groups as $group) {
			if($group->gid == 1) {
				$test2=TRUE;
				break;
			}
		}
		if($test1&&$test2) {
			// Set username and password
			$username = i2config_get('twitter_username','TJIntranet','twitter');
			$password = i2config_get('twitter_password','password','twitter');
			// The message you want to send
			$message = "";
			$message .= $title;
			$message .=": ";
			$url = $I2_ROOT."news/view/".$nid;
			$message .= substr(strip_tags($text),0,140-(strlen($message)+strlen($url)+4))."... ".$url;

			// The twitter API address
			$url = 'http://twitter.com/statuses/update.xml';
			// Alternative JSON version
			// $url = 'http://twitter.com/statuses/update.json';
			// Set up and execute the curl process
			$curl_handle = curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, "$url");
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl_handle, CURLOPT_POST, 1);
			curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "status=$message");
			curl_setopt($curl_handle, CURLOPT_USERPWD, "$username:$password");
			$buffer = curl_exec($curl_handle);
			curl_close($curl_handle);
		}
		*/

		return true;
	}

	
	/**
	  * Email out newsposts to those who have subscribed to them.
	  */
	public static function notify_email($author, $title, $text, $groups, $nid) {
		global $I2_FS_ROOT, $I2_SQL;

		if(strpos($I2_FS_ROOT, "home/") !== false) {
			d("Not emailing due to sandbox use.");
			return false;
		}

		// Mail out the news post to any users who have subscribed to the news and can read it.
		$groupstring = "";
		foreach($groups as $group)
			$groupstring .= $group->name . ", ";
		$groupstring = substr($groupstring, 0,-2);
		$subj = "[Iodine-news] ".strip_tags($title);
		
		$messagecontents = "Posted by " . $author->name . " to " . $groupstring . ":<br />\r\n<br />\r\n" . $text ."<br />\r\n<br />\r\n-----------------------------------------<br />\r\nAutomatically sent by the Iodine news feed. Do not reply to this email. To unsubscribe from these messages, unselect 'Send all news posts to me by e-mail' in your Iodine preferences. If you are not a TJHSST student and believe that you are receiving this message in error, contact intranet@tjhsst.edu for assistance.";

		d($messagecontents);
		// Check permissions and send mail
		$news = new NewsItem($nid);
		foreach($I2_SQL->query('SELECT * FROM news_forwarding')->fetch_all_arrays() as $target) {
			$user = new User($target[0]);
			if($news->readable($user)) {
				i2_mail($user->mail,$subj,$messagecontents, true);
			}
		}
	}
	
	public function twitternotify() {
		return Newsitem::notify_twitter($this->author,$this->title,$this->text,$this->groups,$this->nid);

	}

	/**
	  * Check if Twitter postings should be done for this post.
	  * If the twitter->enabled option in config.ini.php5 is true,
	  * and the group list includes "all," then it will be posted.
	  */
	public static function should_notify_twitter($groups) {
		$enabled = i2config_get("enabled", false, "twitter");
		$public = false;
		$public_gid = i2config_get("public_gid", 1, "twitter");
		foreach ($groups as $group) {
			if($group->gid == $public_gid) {
				$public = true;
				break;
			}
		}
		return ($enabled && $public);
	}

	/**
	  * Notify this posting through the TJ Intranet twitter
	  * feed (TJIntranet) using OAuth through TwitterAPIExchange
	  * https://github.com/J7mbo/twitter-api-php
	  */
	public static function notify_twitter($author, $title, $text, $groups, $nid) {
		global $I2_ROOT, $I2_FS_ROOT;
		require_once $I2_FS_ROOT."lib/TwitterAPIExchange.php";
		if(self::should_notify_twitter($groups)) {
				

			$url = $I2_ROOT."news/show/".$nid;
			$message = strip_tags($title).": ";
			/* max t.co URL length is 20 chars, 4 chars for elipsis */
			$message.= substr(strip_tags($text),0,140-(strlen($message)+strlen($url)+4))."... ".$url;
			$message=str_replace("&nbsp;"," ",$message);
			d("Posting to Twitter: $message", 7);

			$settings = array(
				"oauth_access_token" => i2config_get("oauth_access_token", "", "twitter"),
				"oauth_access_token_secret" => i2config_get("oauth_access_token_secret", "", "twitter"),
				"consumer_key" => i2config_get("consumer_key", "", "twitter"),
				"consumer_secret" => i2config_get("consumer_secret", "", "twitter")
			);

			$tw = new TwitterAPIExchange($settings);
			$resp = $tw->buildOauth("https://api.twitter.com/1.1/statuses/update.json", "POST")
				            ->setPostfields(array("status" => $message))
				            ->performRequest();
			d($resp);
			d_r($resp);
			return json_decode($resp);
		} else {
			d("Not posting to twitter, not in public group");
			return json_encode(array("error"=>"Not in public group."));
		}

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
			$groups = [];
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
		// Update the feeds.
		Feeds::update();
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
	public function edit($title, $text, $groups, $expire, $visible, $public) {
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

		$I2_SQL->query('UPDATE news SET title=%s, text=%s, expire=%s, visible=%d, public=%d WHERE id=%d', $title, $text, $expire, $visible, $public, $this->mynid);

		// flush the group mappings for this post and recreate them entirely
		$I2_SQL->query('DELETE FROM news_group_map WHERE nid=%d', $this->mynid);
		foreach ($groups as $group) {
			if($group->name == NULL) {
				break;
			}
			$I2_SQL->query('INSERT INTO news_group_map SET nid=%d, gid=%s', $this->mynid, $group->name);
		}
		// Update the feeds.
		Feeds::update();
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
		global $I2_SQL, $I2_USER;

		if($user===NULL) {
			$user = $I2_USER;
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
		global $I2_SQL, $I2_USER;
		
		if($user===NULL) {
			$user = $I2_USER;
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
		if(!isset(self::$shadecache))
			self::regenshadecache();
		return in_array($this->nid,self::$shadecache);
	}

	/**
	 * Generate a cache of all shaded news posts.
	 *
	 * @access private
	 */
	private static function regenshadecache(){
		global $I2_SQL,$I2_USER;
		self::$shadecache = $I2_SQL->query('SELECT nid FROM news_shaded_map WHERE uid=%d', $I2_USER->uid)->fetch_all_single_values(MYSQLI_ASSOC);
	}
}
?>
