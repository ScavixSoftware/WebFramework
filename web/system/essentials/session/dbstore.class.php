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

/**
 */
class DbStore extends ObjectStore
{
    protected $serializer;
    
    public function __construct()
    {
        global $CONFIG;
        
        if( !isset($CONFIG['session']['dbstore']['datasource']) )
            $CONFIG['session']['dbstore']['datasource'] = 'internal';
        
        $this->ds = model_datasource($CONFIG['session']['dbstore']['datasource']);
        $this->serializer = new Serializer();
        
        if( !isset($_SESSION['object_ids']) )
            $_SESSION['object_ids'] = [];
    }
    
    private function exec($sql,$args=[])
    {
        try
        {
            return $this->ds->ExecuteSql($sql,$args);
        }
        catch (\ScavixWDF\WdfDbException $ex)
        {
            $info = $ex->getErrorInfo();
            if( isset($info[1]) && $info[1] == 1146 )
            {
                $this->ds->ExecuteSql("CREATE TABLE `wdf_objects` (
                        `session_id` VARCHAR(100) NOT NULL,
                        `id` VARCHAR(255) NOT NULL,
                        `classname` VARCHAR(255) NOT NULL,
                        `no` INT(10) UNSIGNED NULL DEFAULT NULL,
                        `created` DATETIME NULL DEFAULT NULL,
                        `last_access` DATETIME NULL DEFAULT NULL,
                        `data` LONGTEXT NULL,
                        PRIMARY KEY (`session_id`, `id`)
                    )
                    COLLATE='utf8_general_ci'
                    ENGINE=InnoDB");
                
                return $this->ds->ExecuteSql($sql,$args);
            }
        }
        return new \ScavixWDF\Model\ResultSet($this->ds);
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
        
//        $content = $this->serializer->Serialize($obj);
//        
//        $cn = strtolower(get_class_simple($obj));
//        $no = str_replace($cn,'',$id);
//
//        $sql = "('".session_id()."','{$id}','{$cn}','$no',now(),now(),'".$this->ds->EscapeArgument($content)."')";
//        $sql = "INSERT DELAYED INTO wdf_objects(session_id,id,classname,no,created,last_access,data)VALUES $sql ON DUPLICATE KEY UPDATE last_access	= now(),data = VALUES(data)";
//        $this->exec($sql);

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
		$this->exec("DELETE FROM wdf_objects WHERE session_id=? AND id=?", [session_id(),$id]);
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
            $res = $this->exec("SELECT id FROM wdf_objects WHERE session_id=? AND id=?", [session_id(),$id])->Count()>0;
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
            $row = $this->exec("SELECT data FROM wdf_objects WHERE session_id=? AND id=?", [session_id(),$id])->current();
            $data = $row['data'];

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

        $this->exec(
            "DELETE FROM wdf_objects WHERE 
                (session_id=? AND (last_access<now()-interval 60 second OR ISNULL(data)) )
                OR (last_access<now()-interval 300 second)",
            [session_id()]
        );
        $this->_stats(__METHOD__,$start);
    }
    
    function Update($keep_alive=false)
    {
        $start = microtime(true);
        
        if( $keep_alive )
        {
            $this->exec("UPDATE wdf_objects SET last_access=now() WHERE session_id=?",[session_id()]);
            $this->_stats(__METHOD__."/KA",$start);
            return;
        }
        
        $sql = [];
        foreach( $GLOBALS['object_storage'] as $id=>$obj )
		{
			try
			{
                $content = $this->serializer->Serialize($obj);
        
                $cn = strtolower(get_class_simple($obj));
                $no = str_replace($cn,'',$id);

                $sql = "('".session_id()."','{$id}','{$cn}','$no',now(),now(),'".$this->ds->EscapeArgument($content)."')";
                $sql = "INSERT DELAYED INTO wdf_objects(session_id,id,classname,no,created,last_access,data)VALUES $sql ON DUPLICATE KEY UPDATE last_access	= now(),data = VALUES(data)";
                $this->exec($sql);
//                $this->Store($obj, $id);
			}
			catch(Exception $ex)
			{
				WdfException::Log("updating storage for object $id [".get_class($obj)."]",$ex);
			}
		}
        $this->_stats(__METHOD__,$start);
    }
    
    function Migrate($old_session_id, $new_session_id)
    {
        $start = microtime(true);
        $this->exec("UPDATE IGNORE wdf_objects SET session_id=? WHERE session_id=?",[$new_session_id,$old_session_id]);
        $this->_stats(__METHOD__,$start);
    }
}
