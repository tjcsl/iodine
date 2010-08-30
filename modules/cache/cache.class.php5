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
		$server=i2config_get('server','localhost','memcached');
		$port=intval(i2config_get('port','11211','memcached'));
		d("connecting to memcached: server $server on port $port");
		$result = $this->mcache->connect($server, $port);
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
	 * @param Object $module the module (or any object, really) that is talking to memcached
	 * @param string $key the key the module wants to use for this object
	 * @param mixed $val the value to store in memcached
	 * @param int $expire optional time for value to expire
	 * 
	 * @return bool true on success, false on failure.
	 */
	public function store($module, $key, $val, $expire=null)
	{
		$name=get_class($module);
		$db=i2config_get('database','iodine','mysql');
		$hash=sha1($db."??".$name."::".$key);
		if(!isset($expire)) $expire=intval(i2config_get('expire', '120', 'memcached'));
		return $this->mcache->set($hash, $val, 0, $expire);
	}

	/**
	 * Remove a variable from memcached
	 * 
	 * @param Object $module the module (or any object, really) that is talking to memcached
	 * @param string $key the key the module wants to use for this object
	 * @return bool true on success, false on failure
	 */
	public function remove($module, $key)
	{
		$name=get_class($module);
		$hash=sha1(i2config_get('database','iodine','mysql')."??".$name."::".$key);
		return $this->mcache->delete($hash);
	}
	/**
	 * Read a variable form memcached
	 * 
	 * @param Object $module the module (or any object, really) that is talking to memcached
	 * @param string $key the key the module wants to use for this object
	 * @param mixed $val FALSE on failure, otherwise the value that was stored with by the module with the key
	 */
	public function read($module, $key)
	{
		$name=get_class($module);
		$hash=sha1(i2config_get('database','iodine','mysql')."??".$name."::".$key);
		d("reading $hash from memcache");
		if($hash===null) return false;
		$val=$this->mcache->get($hash);
		if($val===false)
		{
			d("$hash not found in memcache");
			return false;
		}
		d("memcache lookup $hash succeeded");
		return $val;
	}
}
