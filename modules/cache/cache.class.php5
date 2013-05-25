<?php
/**
 * Contains the definition for the class {@link Cache}
 * @package core
 * @subpackage Cache
 * @filesource
 */

/**
 * Interacts with memcached.
 * @package core
 * @subpackage Cache
 */
class Cache {
	/**
	 * The memcached object
	 * @access private
	 */
	private $mcache;
	
	/**
	 * Class constructor.
	 * Not especially interesting.
	 * @access public
	 */
	public function __construct()
	{
		$this->mcache=new Memcache;
		d("connecting to memcached: server ".MEMCACHE_SERVER." on port ".MEMCACHE_PORT);
		$result = $this->mcache->pconnect(MEMCACHE_SERVER,MEMCACHE_PORT);
		if(!$result)
		{
			global $I2_ERR;
			//XXX: Should this be a critical error? Currently it is.
			$I2_ERR->fatal_error('memcache server connection failed!', 1);
		}
	}
	
	/**
	 * Store a variable in memcached
	 * 
	 * @param Object|string $module the module (or any object, really) that is talking to memcached or a string if no object is available
	 * @param string $key the key the module wants to use for this object
	 * @param mixed $val the value to store in memcached
	 * @param int $expire optional time for value to expire
	 * 
	 * @return bool true on success, false on failure.
	 */
	public function store($module, $key, $val, $expire=null)
	{
		global $I2_FS_ROOT;
		if(gettype($module)=="string")
		       $name=$module;
		else
		       $name=get_class($module);
		$hash=sha1($I2_FS_ROOT."??".$name."::".$key);
		if(!isset($expire)) $expire=intval(MEMCACHE_DEFAULT_TIMEOUT);
		d("Storing item in memcached: $name::$key");
		return $this->mcache->set($hash, $val, 0, $expire);
	}

	/**
	 * Remove a variable from memcached
	 * 
	 * @param Object|string $module the module (or any object, really) that is talking to memcached or a string if no object is available
	 * @param string $key the key the module wants to use for this object
	 * @return bool true on success, false on failure
	 */
	public function remove($module, $key)
	{
		global $I2_FS_ROOT;
		if(gettype($module)=="string")
			$name=$module;
		else
			$name=get_class($module);
		$hash=sha1($I2_FS_ROOT."??".$name."::".$key);
		d("deleting $name::$key from memcache",7);
		return $this->mcache->delete($hash);
	}
	/**
	 * Read a variable form memcached
	 * 
	 * @param Object|string $module the module (or any object, really) that is talking to memcached or a string if no object is available
	 * @param string $key the key the module wants to use for this object
	 * @param mixed $val FALSE on failure, otherwise the value that was stored with by the module with the key
	 */
	public function read($module, $key)
	{
		global $I2_FS_ROOT;
		if(gettype($module)=="string")
			$name=$module;
		else
			$name=get_class($module);
		$hash=sha1($I2_FS_ROOT."??".$name."::".$key);
		d("reading $name::$key from memcache",7);
		if($hash===null) return false;
		$val=$this->mcache->get($hash);
		if($val===false)
		{
			d("$name::$key not found in memcache",6);
			return false;
		}
		d("memcache lookup $name::$key succeeded",7);
		return $val;
	}
}
?>
