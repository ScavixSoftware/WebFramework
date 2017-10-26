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

use ScavixWDF\WdfException;
use function get_class_simple;
use function log_trace;
use function system_glob_rec;
use function unserializer_active;

/**
 */
class FilesStore extends ObjectStore
{
    protected $serializer;
    protected $path = false;
    
    protected function getPath($sid=false)
    {
        if( $sid )
            return $GLOBALS['CONFIG']['session']['filesstore']['path']."/$sid";
        if( !$this->path )
        {
            $this->path = $GLOBALS['CONFIG']['session']['filesstore']['path']."/".session_id();
            if( !file_exists($this->path) )
                mkdir($this->path);
        }
        return $this->path;
    }
    
    protected function getFile($id)
    {
        return $this->getPath()."/$id";
    }
    
    protected function _key($key)
    {
        return $key;
    }
    
    public function __construct()
    {
        global $CONFIG;
        
        if( !isset($CONFIG['session']['filesstore']['path']) )
            $CONFIG['session']['filesstore']['path'] = sys_get_temp_dir()."/filesstore";
        if( !file_exists($CONFIG['session']['filesstore']['path']) )
            mkdir($CONFIG['session']['filesstore']['path']);
        
        $this->serializer = new Serializer();
        
        if( !isset($_SESSION['object_ids']) )
            $_SESSION['object_ids'] = [];
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
  
        /* serialization and storage will be done in Update method */
//        $content = $this->serializer->Serialize($obj);
//        $this->_stats(__METHOD__.'/SER',$start);
//        $start = microtime(true);
//        file_put_contents($this->getFile($id), $content);
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
		unlink($this->getFile($id));
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
            $res = file_exists($this->getFile($id));
        $this->_stats(__METHOD__,$start);
		return $res;
    }
    
	function Restore($id)
    {
        $start = microtime(true);
		$id = strtolower($id);

		if( isset($GLOBALS['object_storage'][$id]) )
        {
			$res = $GLOBALS['object_storage'][$id];
            $this->_stats(__METHOD__,$start);
        }
        else
        {
            $data = file_get_contents($this->getFile($id));
            $this->_stats(__METHOD__,$start);
            $start = microtime(true);
            $res = $this->serializer->Unserialize($data);
            $GLOBALS['object_storage'][$id] = $res;
            $this->_stats(__METHOD__.'/UNSER',$start);
        }
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
        clearstatcache();
        $p = $GLOBALS['CONFIG']['session']['filesstore']['path'];
        foreach( glob($p.'/*',GLOB_ONLYDIR) as $d )
        {
            if( $d == "$p/." || $d == "$p/.." )
                continue;
            if( time() - filemtime($d) <= 300 )
                continue;
            foreach( glob($d.'/*') as $f )
                if( $d != "$d/." && $d != "$d/.." )
                    unlink($f);
            rmdir($d);
            //log_debug(__METHOD__,"Session removed:",$d);
        }   
        foreach( system_glob_rec($this->getPath(),'*') as $f )
        {
            if( time() - filemtime($f) > 300 )
            {
                unlink($f);
                //log_debug(__METHOD__,"Object removed:",$f);
            }
        }
        $this->_stats(__METHOD__,$start);
    }
    
    function Update($keep_alive=false)
    {
        $start = microtime(true);
        
        if( $keep_alive )
        {
            touch( $this->getPath() );
            foreach( system_glob_rec($this->getFile(''),'*') as $f )
                touch($f);
            return;
        }

        /* Update is guaranteed to be called (see register_shutdown_function), so perform storage here once the script is ready */
        touch( $this->getPath() );
        foreach( $GLOBALS['object_storage'] as $id=>$obj )
        {
            $content = $this->serializer->Serialize($obj);
            file_put_contents($this->getFile($id), $content);
        }
        $this->_stats(__METHOD__.($keep_alive?"/KA":''),$start);
    }
    
    function Migrate($old_session_id, $new_session_id)
    {
        $start = microtime(true);
        @rename($this->getPath($old_session_id),$this->getPath($new_session_id));
        $this->path = false;
        $this->_stats(__METHOD__,$start);
    }
}
