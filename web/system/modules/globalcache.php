<?
/**
 * Scavix Web Development Framework
 *
 * Copyright (c) 2007-2012 PamConsult GmbH
 * Copyright (c) since 2013 Scavix Software Ltd. & Co. KG
 *
 * This library is free software; you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General
 * Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any
 * later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library. If not, see <http://www.gnu.org/licenses/>
 *
 * @author PamConsult GmbH http://www.pamconsult.com <info@pamconsult.com>
 * @copyright 2007-2012 PamConsult GmbH
 * @author Scavix Software Ltd. & Co. KG http://www.scavix.com <info@scavix.com>
 * @copyright since 2012 Scavix Software Ltd. & Co. KG
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 */

define('globalcache_CACHE_OFF',0);
define('globalcache_CACHE_EACCELERATOR',1);
define('globalcache_CACHE_MEMCACHE',2);
define('globalcache_CACHE_ZEND',3);
define('globalcache_CACHE_APC',4);
define('globalcache_CACHE_DB',5);

/**
 * Initializes the globalcache module.
 * 
 * @return void
 */
function globalcache_init()
{
	global $CONFIG;
	if( !isset($CONFIG['globalcache']['CACHE']) || !$CONFIG['globalcache']['CACHE'] )
		$CONFIG['globalcache']['CACHE'] = (function_exists('apc_store') ? globalcache_CACHE_APC : globalcache_CACHE_ZEND);

	if(isset($CONFIG['globalcache']['key_prefix']))
		$GLOBALS['globalcache_key_prefix'] = "K".md5($_SERVER['SERVER_NAME']."-".$CONFIG['globalcache']['key_prefix']."-".getAppVersion('nc'));
	else
		$GLOBALS["globalcache_key_prefix"] = "K".md5($_SERVER['SERVER_NAME']."-".session_name()."-".getAppVersion('nc'));

    register_hook_function(HOOK_POST_INIT,'globalcache_initialize');
}

/**
 * Cleans up a key
 * 
 * Zend only allows A-Za-z0-9 characters, so everything else will be stripped out
 * @param string $key The key to be cleaned up
 * @return string Valid key
 */
function globalcache_cleanupkey($key)
{
	return preg_replace("/[^A-Za-z0-9]/", "", $key);
}

/**
 * @internal Delayed init method
 */
function globalcache_initialize()
{
    global $CONFIG, $LOCALHOST;
	$ret = true;

	switch($CONFIG['globalcache']['CACHE'])
	{
		case globalcache_CACHE_OFF:
		case globalcache_CACHE_APC:
			break;

		case globalcache_CACHE_EACCELERATOR:
			if( !isset($CONFIG['globalcache']['server']) )
				$CONFIG['globalcache']['server'] = (isset($LOCALHOST) ? $LOCALHOST : "localhost");
			break;

		case globalcache_CACHE_MEMCACHE:
			$GLOBALS["memcache_object"] = new Memcache();
		//	$GLOBALS["memcache_object"]->addServer($CONFIG['memcache']['server'], 11211);
            if(!isset($CONFIG['globalcache']['port']))
                $CONFIG['globalcache']['port'] = ini_get('memcache.default_port');
            if(!isset($CONFIG['globalcache']['server']))
                $CONFIG['globalcache']['server'] = 'localhost';
			$try = 1;
			$tries = 5;
			while($try++ <= $tries)
			{
				try {
					if($GLOBALS["memcache_object"]->connect($CONFIG['globalcache']['server'], $CONFIG['globalcache']['port']))
						return true;
				} catch(Exception $ex) {}
			}

			if($try >= $tries)
				die("globalcache_init unable to connect to memcache server ".$CONFIG['globalcache']['server'].":".$CONFIG['globalcache']['port']);
			break;

		case globalcache_CACHE_ZEND:
			system_load_module("modules/zend.php");
			zend_load("Zend/Cache.php");

			$frontendOptions = array(
				'automatic_serialization' => true,
				'lifetime' => 3600,
				'write_control' => false,
				'cache_id_prefix' => $GLOBALS["globalcache_key_prefix"]
			);

			$usememcache = extension_loaded('memcache'); // do we want to store in memcache or in files
			if($usememcache)
			{
				$backendOptions  = array(
					'servers' => array(array('host' => $CONFIG['globalcache']['server'],'port' => $CONFIG['globalcache']['port'], 'persistent' => true, 'weight' => 1, 'timeout' => 5, 'retry_interval' => 15, 'status' => true )),
					'read_control' => false,
					'hashed_directory_level' => 2,
					'automatic_cleaning_factor' => 200
				);
				$GLOBALS["zend_cache_object"] = Zend_Cache::factory('Core',
													'Memcached',
													$frontendOptions,
													$backendOptions);
			}
			else
			{
				// store data in temp files
				if(isset($CONFIG['model']['ado_cache']))
					$cache_dir = $CONFIG['model']['ado_cache'];
				else
					$cache_dir = sys_get_temp_dir ()."/".$_SERVER["HTTP_HOST"]."/";

				if(!is_dir($cache_dir))
					@mkdir($cache_dir);

				if(!is_dir($cache_dir))
					die("globalcache temp dir not found");

				$backendOptions  = array(
					'cache_dir' => $cache_dir,
					'read_control' => false,
					'hashed_directory_level' => 2,
					'automatic_cleaning_factor' => 200
				);
				$GLOBALS["zend_cache_object"] = Zend_Cache::factory('Core',
													'File',
													$frontendOptions,
													$backendOptions);

			}
			break;
            
        case globalcache_CACHE_DB:
            break;
	}
}

/**
 * Save a value/object in the global cache.
 * 
 * @param string $key the key of the value
 * @param mixed $value the object/string to save
 * @param int $ttl time to live (in seconds) of the caching
 * @return bool true if ok, false on error
 */
function globalcache_set($key, $value, $ttl = false)
{
    if( !hook_already_fired(HOOK_POST_INIT) )
        return false;
    
	global $CONFIG;
	try
	{
		switch($CONFIG['globalcache']['CACHE'])
		{
			case globalcache_CACHE_OFF:
				return true;
				break;

			case globalcache_CACHE_APC:
//				log_debug("globalcache_set",$key,$value);
				return apc_store($GLOBALS["globalcache_key_prefix"].$key, $value, $ttl);
				break;

			case globalcache_CACHE_ZEND:
//				log_error("setting $key = $value");
				return $GLOBALS["zend_cache_object"]->save($value, globalcache_cleanupkey($GLOBALS["globalcache_key_prefix"].$key), array(), $ttl);
				break;

			case globalcache_CACHE_EACCELERATOR:
				$ret = eaccelerator_put($GLOBALS["globalcache_key_prefix"].md5($key), $value, ($ttl ? $ttl : 0));
	//			log_error("globalcache_set: $key v: $value ttl: $ttl eacc: ".($GLOBALS["globalcache_use_eaccelerator"] ? "true" : "false")." ret: ".($ret ? "true" : "false"));
				return $ret;
				break;

			case globalcache_CACHE_MEMCACHE:
				return $GLOBALS["memcache_object"]->set($GLOBALS["globalcache_key_prefix"].md5($key), $value, 0, ($ttl ? time() + $ttl : 0));
				break;
            
            case globalcache_CACHE_DB:
                $val = session_serialize($value);
                $ds = model_datasource($CONFIG['globalcache']['datasource']);
				try
				{
					if( $ttl > 0 )
					{
						$ds->ExecuteSql(
							"REPLACE INTO wdf_cache(ckey,full_key,cvalue,valid_until)VALUES(?,?,?,".$ds->Driver->Now($ttl).")",
							array(md5($key),$key,$val)
						);
					}
					else
						$ds->ExecuteSql("REPLACE INTO wdf_cache(ckey,full_key,cvalue)VALUES(?,?,?)",array(md5($key),$key,$val));
				}
				catch(Exception $ex)
				{
					$ds->ExecuteSql("CREATE TABLE IF NOT EXISTS wdf_cache (
                        ckey VARCHAR(32)  NOT NULL,
                        cvalue TEXT  NOT NULL,
                        valid_until DATETIME  NULL,
						full_key TEXT  NOT NULL,
                        PRIMARY KEY (ckey))");
					
					if( $ttl > 0 )
						$ds->ExecuteSql(
							"REPLACE INTO wdf_cache(ckey,full_key,cvalue,valid_until)VALUES(?,?,?,".$ds->Driver->Now($ttl).")",
							array(md5($key),$key,$val)
						);
					else
						$ds->ExecuteSql("REPLACE INTO wdf_cache(ckey,full_key,cvalue)VALUES(?,?,?)",array(md5($key),$key,$val));
				}
                return true;
                break;
		}
	}
	catch(Exception $ex)
	{
		WdfException::Log($ex);
		die($ex->__toString());
	}
	return false;
}

/**
 * Get a value/object from the global cache.
 * 
 * @param string $key the key of the value
 * @param mixed $default a default return value if the key can not be found in the cache
 * @return mixed The object from the cache or `$default`
 */
function globalcache_get($key, $default = false)
{
    if( !hook_already_fired(HOOK_POST_INIT) )
        return $default;
    
	global $CONFIG;
	try {
		switch($CONFIG['globalcache']['CACHE'])
		{
			case globalcache_CACHE_OFF:
				return $default;
				break;

			case globalcache_CACHE_APC:
				$ret = apc_fetch($GLOBALS["globalcache_key_prefix"].$key, $success);
				return $success?$ret:$default;
				break;

			case globalcache_CACHE_ZEND:
				$ret = $GLOBALS["zend_cache_object"]->load(globalcache_cleanupkey($GLOBALS["globalcache_key_prefix"].$key));
				return ($ret === false)?$default:$ret;
				break;

			case globalcache_CACHE_EACCELERATOR:
				$ret = eaccelerator_get($GLOBALS["globalcache_key_prefix"].md5($key));
				return is_null($ret)?$default:$ret;
				break;

			case globalcache_CACHE_MEMCACHE:
				$ret = $GLOBALS["memcache_object"]->get($GLOBALS["globalcache_key_prefix"].md5($key));
				return ($ret === false)?$default:$ret;
				break;
            
            case globalcache_CACHE_DB:
                $ds = model_datasource($CONFIG['globalcache']['datasource']);
				try
				{
					$ret = $ds->ExecuteScalar("SELECT cvalue FROM wdf_cache WHERE ckey=? AND (valid_until IS NULL OR valid_until>=".$ds->Driver->Now().")",
						array(md5($key)));
				}catch(Exception $ex){ return $default; }
                if( $ret === false )
                    return $default;
                return session_unserialize($ret);
                break;
		}
	}
	catch(Exception $ex)
	{
		WdfException::Log($ex);
		die($ex->__toString());
	}
	return $ret;
}

/**
 * Empty the whole cache.
 * 
 * @return bool true if ok, false on error
 */
function globalcache_clear()
{
    if( !hook_already_fired(HOOK_POST_INIT) )
        return false;
    
	global $CONFIG;
	switch($CONFIG['globalcache']['CACHE'])
	{
		case globalcache_CACHE_OFF:
			return true;
			break;

		case globalcache_CACHE_APC:
			return apc_clear_cache('user');
			break;

		case globalcache_CACHE_ZEND:
			return $GLOBALS["zend_cache_object"]->clean();
			break;

		case globalcache_CACHE_EACCELERATOR:
			return eaccelerator_clear();
			break;

		case globalcache_CACHE_MEMCACHE:
			return $GLOBALS["memcache_object"]->flush();
			break;
        
        case globalcache_CACHE_DB:
            $ds = model_datasource($CONFIG['globalcache']['datasource']);
			try{ $ds->ExecuteSql("DELETE FROM wdf_cache"); }catch(Exception $ex){}
			break;
	}
	return false;
}

/**
 * Delete a value from the global cache.
 * 
 * @param string $key the key of the value
 * @param mixed $value the object/string to save
 * @param int $ttl time to live (in seconds) of the caching
 * @return bool true if ok, false on error
 */
function globalcache_delete($key)
{
    if( !hook_already_fired(HOOK_POST_INIT) )
        return false;
    
	global $CONFIG;
	switch($CONFIG['globalcache']['CACHE'])
	{
		case globalcache_CACHE_OFF:
			return true;
			break;

		case globalcache_CACHE_APC:
			return apc_delete($GLOBALS["globalcache_key_prefix"].$key);
			break;

		case globalcache_CACHE_ZEND:
			return $GLOBALS["zend_cache_object"]->remove(globalcache_cleanupkey($GLOBALS["globalcache_key_prefix"].$key));
			break;

		case globalcache_CACHE_EACCELERATOR:
			return eaccelerator_rm($GLOBALS["globalcache_key_prefix"].md5($key));
			break;

		case globalcache_CACHE_MEMCACHE:
			return $GLOBALS["memcache_object"]->delete($GLOBALS["globalcache_key_prefix"].md5($key));
			break;
        
        case globalcache_CACHE_DB:
            $ds = model_datasource($CONFIG['globalcache']['datasource']);
			try{ $ds->ExecuteSql("DELETE FROM wdf_cache WHERE ckey=?",md5($key)); }catch(Exception $ex){}
            return true;
			break;
	}
	return false;
}

/**
 * Returns information about the cache usage.
 * 
 * Note: this currently returns various different information and format thus needs to be streamlined.
 * @return mixed Cache information
 */
function globalcache_info()
{
    if( !hook_already_fired(HOOK_POST_INIT) )
        return false;
    
	global $CONFIG;
	$ret = false;
	switch($CONFIG['globalcache']['CACHE'])
	{
		case globalcache_CACHE_APC:
			$status = apc_cache_info('user');
			return(var_export($status, true));

		case globalcache_CACHE_OFF:
		case globalcache_CACHE_ZEND:
		case globalcache_CACHE_EACCELERATOR:
			return "no stats available";
			break;

		case globalcache_CACHE_MEMCACHE:
			$status = $GLOBALS["memcache_object"]->getStats();
			return(var_export($status, true));

			if($status == false)
				return "no memcache stats available";
			$ret = "";
			$ret .= "Memcache Server version:".$status ["version"]."\r\n";
			$ret .= "Process id of this server process: ".$status ["pid"]."\r\n";
			$ret .= "Number of seconds this server has been running: ".$status ["uptime"]."\r\n";
			$ret .= "Accumulated user time for this process: ".$status ["rusage_user"]." seconds\r\n";
			$ret .= "Accumulated system time for this process: ".$status ["rusage_system"]." seconds\r\n";
			$ret .= "Total number of items stored by this server ever since it started: ".$status ["total_items"]."\r\n";
			$ret .= "Number of open connections: ".$status ["curr_connections"]."\r\n";
			$ret .= "Total number of connections opened since the server started running: ".$status ["total_connections"]."\r\n";
			$ret .= "Number of connection structures allocated by the server: ".$status ["connection_structures"]."\r\n";
			$ret .= "Cumulative number of retrieval requests: ".$status ["cmd_get"]."\r\n";
			$ret .= "Cumulative number of storage requests: ".$status ["cmd_set"]."\r\n";

			if((real) $status ["cmd_get"] > 0)
			{
				$percCacheHit=((real)$status ["get_hits"]/ (real)$status ["cmd_get"] *100);
				$percCacheHit=round($percCacheHit,3);
				$percCacheMiss=100-$percCacheHit;

				$ret .= "Number of keys that have been requested and found present: ".$status ["get_hits"]." ($percCacheHit%)\r\n";
				$ret .= "Number of items that have been requested and not found: ".$status ["get_misses"]."($percCacheMiss%)\r\n";
			}
			$MBRead= (real)$status["bytes_read"]/(1024*1024);

			$ret .= "Total number of bytes read by this server from network: ".$MBRead." Mega Bytes\r\n";
			$MBWrite=(real) $status["bytes_written"]/(1024*1024) ;
			$ret .= "Total number of bytes sent by this server to network: ".$MBWrite." Mega Bytes\r\n";
			$MBSize=(real) $status["limit_maxbytes"]/(1024*1024) ;
			$ret .= "Number of bytes this server is allowed to use for storage: ".$MBSize." Mega Bytes\r\n";
			$ret .= "Number of valid items removed from cache to free memory for new items: ".$status ["evictions"]."\r\n";
			break;
			
        case globalcache_CACHE_DB:
            $ds = model_datasource($CONFIG['globalcache']['datasource']);
			try{ 
				$ret  = "Global cache is handled by DB module.\n";
				$ret .= "Datasource: {$CONFIG['globalcache']['datasource']}\n";
				$ret .= "DSN: ".$ds->GetDsn()."\n";
				$ret .= "Records: ".$ds->ExecuteScalar("SELECT count(*) FROM wdf_cache")."\n";
			}catch(Exception $ex){}
			break;
	}

	return $ret;
}

/**
 * Gets a list of all keys in the cache.
 * 
 * @return array list of all keys
 */
function globalcache_list_keys()
{
    if( !hook_already_fired(HOOK_POST_INIT) )
        return array();
    
	global $CONFIG;
	switch($CONFIG['globalcache']['CACHE'])
	{
		case globalcache_CACHE_DB:
			$ds = model_datasource($CONFIG['globalcache']['datasource']);
			try
			{
				$rs = $ds->ExecuteSql("SELECT full_key FROM wdf_cache WHERE (valid_until IS NULL OR valid_until>=".$ds->Driver->Now().")");
				return $rs->Enumerate('full_key');
			}catch(Exception $ex){}
			return array(); 
            
        case globalcache_CACHE_APC:
            $ret = array();
            $cacheinfo = apc_cache_info('user');
            $keyprefixlen = strlen($GLOBALS["globalcache_key_prefix"]);
            foreach($cacheinfo['cache_list'] as $cacheentry)
                $ret[] = substr($cacheentry['info'], $keyprefixlen);
            return $ret;
            break;
            
		default:
			WdfException::Raise("globalcache_list_keys not implemented for handler {$CONFIG['globalcache']['CACHE']}");
			break;
	}
	return array();
}
