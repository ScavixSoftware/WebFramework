<?php
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
namespace ScavixWDF\Model\Driver;

use DateTime;
use PDO;
use ScavixWDF\Model\ColumnSchema;
use ScavixWDF\Model\ResultSet;
use ScavixWDF\Model\TableSchema;
use ScavixWDF\ToDoException;
use ScavixWDF\WdfDbException;

/**
 * MySQL database driver.
 * 
 */
class MySql implements IDatabaseDriver
{
	private $_pdo;

	/**
	 * @implements <IDatabaseDriver::initDriver>
	 */
	function initDriver($datasource,$pdo)
	{
        global $CONFIG;
		$this->_ds = $datasource;
		$this->_pdo = $pdo;
        if(isset($CONFIG['model'][$datasource->_storage_id]) && isset($CONFIG['model'][$datasource->_storage_id]['bufferedquery']) && $CONFIG['model'][$datasource->_storage_id]['bufferedquery'])
            $this->_pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true); 
//        if(!isset($CONFIG['model'][$datasource->_storage_id]['forceutf8']) || (isset($CONFIG['model'][$datasource->_storage_id]['forceutf8']) && $CONFIG['model'][$datasource->_storage_id]['forceutf8']))
        {
            $this->_pdo->exec("SET CHARACTER SET utf8; SET NAMES utf8");
        }
        $this->_pdo->Driver = $this;
	}

	/**
	 * @implements <IDatabaseDriver::listTables>
	 */
	function listTables()
	{
		$sql = 'SHOW TABLES';
		$tables = array();
		foreach($this->_pdo->query($sql) as $row)
			$tables[] = $row[0];
		return $tables;
	}

	/**
	 * @implements <IDatabaseDriver::getTableSchema>
	 */
    function &getTableSchema($tablename)
	{
		$sql = 'SHOW CREATE TABLE `'.$tablename.'`';
		$tableSql = $this->_pdo->query($sql);
		if( !$tableSql )
			WdfDbException::Raise("Table `$tablename` not found!","PDO error info: ",$this->_pdo->errorInfo());

        $tableSql = $tableSql->fetch();
        $tableSql = $tableSql[1];

		$res = new TableSchema($this->_ds, $tablename);
		$sql = "show columns from `$tablename`";
		foreach($this->_pdo->query($sql) as $row)
		{
			
			$size = false;
			if( preg_match('/([a-zA-Z]+)\(*(\d*)\)*/',$row['Type'],$match) )
			{
				$row['Type'] = $match[1];
				$size = $match[2];
			}
			if( $row['Key'] == 'PRI' )
				$row['Key'] = 'PRIMARY';

			$col = new ColumnSchema($row['Field']);
			$col->Type = $row['Type'];
			$col->Size = $size;
			$col->Null = $row['Null'];
			$col->Key = $row['Key'];
			$col->Default = $row['Default'];
			$col->Extra = $row['Extra'];
			$res->Columns[] = $col;
		}

		return $res;
	}

	/**
	 * @implements <IDatabaseDriver::listColumns>
	 */
	function listColumns($tablename)
	{
		$sql = 'SHOW COLUMNS FROM `'.$tablename.'`';
		$cols = array();
		foreach($this->_pdo->query($sql) as $row)
			$cols[] = $row[0];
		return $cols;
	}

	/**
	 * @implements <IDatabaseDriver::tableExists>
	 */
	function tableExists($tablename)
	{
		$sql = 'SHOW TABLES LIKE ?';
		$stmt = $this->_pdo->prepare($sql);//,array(PDO::ATTR_CURSOR=>PDO::CURSOR_SCROLL));
		$stmt->setFetchMode(PDO::FETCH_NUM);
		$stmt->bindValue(1,$tablename);
		if( !$stmt->execute() )
			WdfDbException::RaiseStatement($stmt);
		$row = $stmt->fetch();
		return count($row)>0;
	}

	/**
	 * @implements <IDatabaseDriver::createTable>
	 */
	function createTable($objSchema)
	{ ToDoException::Raise("implement MySql->createTable()"); }

	/**
	 * @implements <IDatabaseDriver::getSaveStatement>
	 */
	function getSaveStatement($model,&$args,$columns_to_update=false)
	{
        $argnum = 0;
		$cols = array();
		$pks = $model->GetPrimaryColumns();
		$all = array();
		$vals = array();
		$pkcols = array();
		$pks2 = array();

		foreach( $pks as $col )
		{
			if( isset($model->$col) )
			{
				$pkcols[] = "`$col`=:$col";
				$all[] = "`$col`";
				$vals[] = ":$col";
				$args[":$col"] = $model->$col;
			}
			$pks2[$col] = $col;
		}
		$columns_to_update = $columns_to_update?$columns_to_update:$model->GetColumnNames(true);
		foreach( $columns_to_update as $col )
		{
			if( isset($pks2[$col]) || !$model->HasColumn($col) )
				continue;
			
			// isset returns false too if $this->$col is set to NULL, so we need some more logic here
			if( !isset($model->$col) )
			{
				if( !isset($ovars) )
				{
					$ovars = get_object_vars($model);
					$ovars = array_combine(array_keys($ovars),array_fill(0,count($ovars),true));
				}
				if( !isset($ovars[$col]) )
					continue;
			}
			
			$tv = $model->TypedValue($col);
			if( is_string($tv) && strtolower($tv)=="now()" )
			{
				$cols[] = "`$col`=NOW()";
				$all[] = "`$col`";
				$vals[] = "NOW()";
			}
			else
			{
                $argn = ":arg".($argnum++);
				$cols[] = "`$col`=$argn";
				$all[] = "`$col`";
				$vals[] = "$argn";
				$args["$argn"] = $tv;
			
				if( $args["$argn"] instanceof DateTime )
					$args["$argn"] = $args["$argn"]->format("c");
			}
		}
		
		if( $model->_saved )
		{
			if( count($cols) == 0 )
				return false;
			
			$sql  = "UPDATE `".$model->GetTableName()."`";
			$sql .= " SET ".implode(",",$cols);
			$sql .= " WHERE ".implode(" AND ",$pkcols);
			$sql .= " LIMIT 1";
		}
		else
		{
			if( count($all) == 0 )
				$sql = (\ScavixWDF\Model\Model::$SaveDelayed?"INSERT DELAYED INTO `":"INSERT INTO `").$model->GetTableName()."`";
			else
				$sql  = (\ScavixWDF\Model\Model::$SaveDelayed?"INSERT DELAYED INTO `":"INSERT INTO `").$model->GetTableName()."`(".implode(",",$all).")VALUES(".implode(',',$vals).")";
		}
		return new ResultSet($this->_ds, $this->_pdo->prepare($sql));
	}
	
	/**
	 * @implements <IDatabaseDriver::getDeleteStatement>
	 */
	function getDeleteStatement($model,&$args)
	{
		$pks = $model->GetPrimaryColumns();
		$cols = array();		
		foreach( $pks as $col )
		{
			if( isset($model->$col) )
			{
				$cols[] = "`$col`=:$col";
				$args[":$col"] = $model->$col;
			}
		}
		if( count($cols) == 0 )
			return false;
		
		$sql = "DELETE FROM `".$model->GetTableName()."` WHERE ".implode(" AND ",$cols)." LIMIT 1";
		return new ResultSet($this->_ds, $this->_pdo->prepare($sql));
	}
	
	/**
	 * @implements <IDatabaseDriver::getPagedStatement>
	 */
	function getPagedStatement($sql,$page,$items_per_page)
	{
		$offset = ($page-1)*$items_per_page;
        if(intval($offset) < 0)
            $offset = 0;
		$sql = preg_replace('/LIMIT\s+[\d\s,]+/', '', $sql);
		$sql .= " LIMIT $offset,$items_per_page";
		return new ResultSet($this->_ds, $this->_pdo->prepare($sql));
	}
	
	/**
	 * @implements <IDatabaseDriver::getPagingInfo>
	 */
	function getPagingInfo($sql,$input_arguments=null)
	{
		if( !preg_match('/LIMIT\s+([\d\s,]+)/i', $sql, $amounts) )
			return false;
		
		$amounts = explode(",",$amounts[1]);
		if( count($amounts) > 1 )
			list($offset,$length) = $amounts;
		else
			list($offset,$length) = array(0,$amounts[0]);
		$offset = intval($offset);
		$length = intval($length);
		
        $key = 'DB_Cache_FoundRows_'.md5($sql.serialize($input_arguments));
        $found_rows = cache_get($key,false,false,true);
        if( $found_rows === false )
        {
            $sql = preg_replace('/LIMIT\s+[\d\s,]+/i', '', $sql);
            if( stripos($sql, 'select * from') === 0 )
                $sql = "SELECT 1 FROM".substr($sql,13);
            $sql = "SELECT count(*) FROM ($sql) AS x";

            $ok = $this->_ds->ExecuteScalar($sql,is_null($input_arguments)?array():array_values($input_arguments));
            $total = intval($ok);
            if( $ok === false )
                $this->_ds->LogLastStatement("Error querying paging info");
        }
        else
            $total = intval($found_rows);
        
		return array
		(
			'rows_per_page'=> $length,
			'current_page' => $length==0?0:floor($offset / $length) + 1,
			'total_pages'  => $length==0?0:ceil($total / $length),
			'total_rows'   => $total,
			'offset'       => $offset,
		);
	}
	
	/**
	 * @implements <IDatabaseDriver::Now>
	 */
	function Now($seconds_to_add=0)
	{
		return "(NOW() + INTERVAL $seconds_to_add SECOND)";
	}
    
	/**
	 * @implements <IDatabaseDriver::PreprocessSql>
	 */
    function PreprocessSql($sql)
    {
        return str_ireplace("INSERT OR IGNORE", "INSERT IGNORE", $sql);
    }
}
