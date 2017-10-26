<?php
/**
 * Scavix Web Development Framework
 *
 * Copyright (c) since 2017 Scavix Software Ltd. & Co. KG
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
 * @author Scavix Software Ltd. & Co. KG http://www.scavix.com <info@scavix.com>
 * @copyright since 2017 Scavix Software Ltd. & Co. KG
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 */
namespace ScavixWDF\Session;

use Exception;
use ScavixWDF\WdfException;
use function get_class_simple;
use function log_debug;
use function log_trace;
use function unserializer_active;

/**
 */
class RedisStore extends ObjectStore
{
    protected $serializer;
    
    protected $socket;
    protected function getSocket()
    {
        return $this->socket
            ? $this->socket
            : ($this->socket = stream_socket_client($GLOBALS['CONFIG']['session']['redisstore']['server']));
    }
    
    protected function _key($key)
    {
        if( strpos($key,session_id()."_")===0 )
            return $key;
        return session_id()."_$key";
    }
    
    public function __construct()
    {
        global $CONFIG;
        
        if( !isset($CONFIG['session']['redisstore']['server']) )
            $CONFIG['session']['redisstore']['server'] = 'localhost:6379';
        
        $this->serializer = new Serializer();
        
        if( !isset($_SESSION['object_ids']) )
            $_SESSION['object_ids'] = [];
    }
    
    private function makePaket($args)
    {
        $cmd = '*' . count($args) . "\r\n";
        foreach ($args as $item) {
            $cmd .= '$' . strlen($item) . "\r\n" . $item . "\r\n";
        }
        return $cmd;
    }
    
    private function exec($method,$args=[])
    {
        if( $method == 'multi' )
        {
            $cmd = "multi\r\n";
            foreach( $args as $a )
                $cmd .= $this->makePaket($a);
            $cmd .= "exec\r\n";
        }
        else
        {
            array_unshift($args, $method);
            $cmd = $this->makePaket($args);
        }
        
        fwrite($this->getSocket(), $cmd);
        try
        {
            $res = $this->getResponse();
            if( $method == 'multi' && $res == 'OK' )
            {
                $res = $this->getResponse();
                log_debug('multi response',$res);
            }
            return $res;
        }
        catch (Exception $ex) 
        {
            log_error($ex,"CMD",$cmd);
        }
        return false;
    }
    
    private function getResponse()
    {
        do
        {
            $line = fgets($this->getSocket());
        
            list($type, $result) = array($line[0], substr($line, 1, strlen($line) - 3));
            if ($type == '-') { // error message
                throw new Exception($result);
            } elseif ($type == '$') { // bulk reply
                if ($result == -1) {
                    $result = null;
                } else {
                    $line = fread($this->getSocket(), $result + 2);
                    $result = substr($line, 0, strlen($line) - 2);
                }
            } elseif ($type == '*') { // multi-bulk reply
                $count = ( int ) $result;
                for ($i = 0, $result = array(); $i < $count; $i++) {
                    $result[] = $this->getResponse();
                }
            }
        }
        while( is_string($result) && trim($result) == 'QUEUED' );
        return $result;
    }
    
    function set($key,$value)
    {
        return $this->exec('setex',[$this->_key($key),'300',$value]);
    }
    function get($key)
    {
        $res = $this->exec('get',[$this->_key($key)]);
        if( !$res )
            log_debug("get returned nothing");
        return $res;
    }
    function del($key)
    {
        return $this->exec('del',[$this->_key($key)]);
    }
    function expire($key)
    {
        return $this->exec('expire',[$this->_key($key),300]);
    }
    
    function Store(&$obj,$id="")
    {
        $start = microtime(true);
		$id = strtolower($id);
		if( $id == "" )
		{
			if( !isset($obj->_storage_id) )
				WdfException::Raise("Trying to store an object without storage_id!");
			$id = $obj->_storage_id;
		}
		else
			$obj->_storage_id = $id;
        
        $content = $this->serializer->Serialize($obj);
        
        $this->set($id,$content);
        $GLOBALS['object_storage'][$id] = $obj;
        $this->_stats(__METHOD__,$start);
    }
    
	function Delete($id)
    {
        $start = microtime(true);
		if( is_object($id) && isset($id->_storage_id) )
			$id = $id->_storage_id;
        
        if( isset($GLOBALS['object_storage'][$id]) )
            unset($GLOBALS['object_storage'][$id]);
		$this->del($id);
        $this->_stats(__METHOD__,$start);
    }
    
	function Exists($id)
    {
        $start = microtime(true);
		if( is_object($id) && isset($id->_storage_id) )
			$id = $id->_storage_id;
		$id = strtolower($id);
		if( isset($GLOBALS['object_storage'][$id]) )
            $res = true;
        else
            $res = $this->exec('exists',[$this->_key($id)]);
        $this->_stats(__METHOD__,$start);
		return $res;
    }
    
	function Restore($id)
    {
        $start = microtime(true);
		$id = strtolower($id);

		if( isset($GLOBALS['object_storage'][$id]) )
			$res = $GLOBALS['object_storage'][$id];
        else
        {
            $data = $this->get($id);
            $res = $this->serializer->Unserialize($data);
            $GLOBALS['object_storage'][$id] = $res;
        }
        $this->_stats(__METHOD__,$start);
		return $res;
    }
    
    function CreateId(&$obj)
    {
        $start = microtime(true);
		if( unserializer_active() )
		{
			log_trace("create_storage_id while unserializing object of type ".get_class_simple($obj));
			$obj->_storage_id = "to_be_overwritten_by_unserializer";
			return $obj->_storage_id;
		}

		$cn = strtolower(get_class_simple($obj));
		if( !isset($_SESSION['object_ids'][$cn]) )
			$_SESSION['object_ids'][$cn] = 1;
		else
			$_SESSION['object_ids'][$cn]++;

        $obj->_storage_id = $cn.$_SESSION['object_ids'][$cn];
        $this->_stats(__METHOD__,$start);
        return $obj->_storage_id;
    }
    
    function Cleanup($classname=false)
    {
        $start = microtime(true);
        if( $classname )
        {
            $classname = strtolower($classname);
            foreach( $GLOBALS['object_storage'] as $id=>&$obj )
            {
                if( get_class_simple($obj,true) == $classname )
                    $this->Delete($id);
            }
            $this->_stats(__METHOD__."/CN",$start);
            return;
        }
        
        $this->_stats(__METHOD__,$start);
    }
    
    function Update($keep_alive=false)
    {
        $start = microtime(true);
        
        if( $keep_alive )
            $ids = $this->exec('keys',[session_id()."_*"]);
        else
            $ids = array_keys($GLOBALS['object_storage']);

        foreach( $ids as $id )
            $this->expire($id);
        $this->_stats(__METHOD__.($keep_alive?"/KA":''),$start);
    }
    
    function Migrate($old_session_id, $new_session_id)
    {
        $start = microtime(true);
        $ids = $this->exec('keys',["{$old_session_id}_*"]);
        foreach( array_unique($ids) as $id )
        {
            $nid = str_replace("{$old_session_id}_","{$new_session_id}_", $id);
            $this->exec('rename',[$id,$nid]);
        }
        $this->_stats(__METHOD__,$start);
    }
}
