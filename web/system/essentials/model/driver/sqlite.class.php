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
 
/**
 * SqLite database driver.

 */
class SqLite implements IDatabaseDriver
{
	private $_ds;
	private $_pdo;

	private function columnDef($colAttr)
	{
		switch( strtolower($colAttr->Type) )
		{
			case 'string':
				if( isset($colAttr->Size) && $colAttr->Size>0 )
					return $colAttr->Name.' VARCHAR('.$colAttr->Size.')';
				return $colAttr->Name.' TEXT';
			case 'integer':
			case 'int':
				if( isset($colAttr->Size) && $colAttr->Size>0 )
					return $colAttr->Name.' INTEGER('.$colAttr->Size.')';
				return $colAttr->Name.' INTEGER';
			case 'boolean':
			case 'bool':
				return $colAttr->Name.' INTEGER(1)';
		}
		WdfDbException::Raise("Unknown columne type {$colAttr->Type}");
	}

	/**
	 * @implements <IDatabaseDriver::initDriver>
	 */
	function initDriver($datasource,$pdo)
	{
		$this->_ds = $datasource;
		$this->_pdo = $pdo;
	}

	/**
	 * @implements <IDatabaseDriver::listTables>
	 */
	function listTables()
	{
		$sql = 'SELECT tbl_name FROM sqlite_master WHERE type="table" ORDER BY tbl_name';
		$tables = array();
		foreach($this->_pdo->query() as $row)
			$tables[] = $row['tbl_name'];
		return $tables;
	}

	/**
	 * @implements <IDatabaseDriver::getTableSchema>
	 */
    function &getTableSchema($tablename)
	{
		if( strtolower($tablename) == 'sqlite_master' )
		{
			$res = new TableSchema($this->_ds, $tablename);
			$col = new ColumnSchema('type');
			$col->Type = 'text';
			$col->Null = true;
			$res->Columns[] = $col;
			$col = new ColumnSchema('name');
			$col->Type = 'text';
			$col->Null = true;
			$res->Columns[] = $col;
			$col = new ColumnSchema('tbl_name');
			$col->Type = 'text';
			$col->Null = true;
			$res->Columns[] = $col;
			$col = new ColumnSchema('rootpage');
			$col->Type = 'integer';
			$col->Null = true;
			$res->Columns[] = $col;
			$col = new ColumnSchema('sql');
			$col->Type = 'text';
			$col->Null = true;
			$res->Columns[] = $col;
			return $res;
		}		
		
		$tableSql = $this->_pdo->query(
			'SELECT sql FROM sqlite_master WHERE type="table" AND name = "'.$tablename.'"'
		)->fetch();
		$tableSql = $tableSql['sql'];

		if( !$tableSql )
			WdfDbException::Raise("Table `$tablename` not found!","PDO error info: ",$this->_pdo->errorInfo());

		$res = new TableSchema($this->_ds, $tablename);
		$sql = 'PRAGMA table_info("'.$tablename.'")';
		foreach($this->_pdo->query($sql) as $row)
		{
			$col = new ColumnSchema($row['name']);
			$col->Type = $row['type'];
			$col->Null = $row['notnull'] == 0;
			$col->Key = ($row['pk']==1)?"PRIMARY":null;
			$res->Columns[] = $col;
		}
		return $res;
	}

	/**
	 * @implements <IDatabaseDriver::listColumns>
	 */
	function listColumns($tablename)
	{
		$sql = 'PRAGMA table_info("'.$tablename.'")';
		$cols = array();
		foreach($this->_pdo->query($sql) as $row)
			$cols[] = $row['name'];
		return $cols;
	}

	/**
	 * @implements <IDatabaseDriver::tableExists>
	 */
	function tableExists($tablename)
	{
		$sql = 'SELECT tbl_name FROM sqlite_master WHERE type="table" AND tbl_name=?';
		$stmt = $this->_pdo->prepare($sql);//,array(PDO::ATTR_CURSOR=>PDO::CURSOR_SCROLL));
		$stmt->setFetchMode(PDO::FETCH_NUM);
		$stmt->bindValue(1,$tablename);
		if( !$stmt->execute() )
			WdfDbException::Raise($stmt->errorInfo());
		$row = $stmt->fetch();
		return is_array($row) && count($row)>0;
	}

	/**
	 * @implements <IDatabaseDriver::createTable>
	 */
	function createTable($objSchema)
	{
		$sql = array();

		foreach( $objSchema->Columns as $col )
			$sql[] = $this->columnDef($col);

		$sql = 'CREATE TABLE "'.$objSchema->Table.'" ('."\n".implode(",\n",$sql)."\n".')';
		$stmt = $this->_pdo->prepare($sql);//,array(PDO::ATTR_CURSOR=>PDO::CURSOR_SCROLL));
		if( !$stmt->execute() )
			WdfDbException::Raise($stmt->errorInfo());
	}

	/**
	 * @implements <IDatabaseDriver::getSaveStatement>
	 */
	function getSaveStatement($model,&$args)
	{ ToDoException::Raise("implement SqLite->getSaveStatement()"); }
	
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
		
		$sql = "DELETE FROM `".$model->GetTableName()."` WHERE ".implode(" AND ",$cols);
		return new ResultSet($this->_ds, $this->_pdo->prepare($sql));
	}
	
	/**
	 * @implements <IDatabaseDriver::getPagedStatement>
	 */
	function getPagedStatement($sql,$page,$items_per_page)
	{
		$offset = ($page-1)*$items_per_page;
		$sql = preg_replace('/LIMIT\s+[\d\s,]+/', '', $sql);
		$sql .= " LIMIT $offset,$items_per_page";
		return new ResultSet($this->_ds, $this->_pdo->prepare($sql));
	}
	
	/**
	 * @implements <IDatabaseDriver::getPagingInfo>
	 */
	function getPagingInfo($sql,$input_arguments=null)
	{ 
		if( !preg_match('/LIMIT\s+([\d\s,]+)/', $sql, $amounts) )
			return false;
		
		$amounts = explode(",",$amounts[1]);
		if( count($amounts) > 1 )
			list($offset,$length) = $amounts;
		else
			list($offset,$length) = array(0,$amounts[0]);
		$offset = intval($offset);
		$length = intval($length);
		
		$sql = preg_replace('/LIMIT\s+[\d\s,]+/', '', $sql);
		$sql = "SELECT count(*) FROM ($sql) AS x";
		$stmt = $this->_pdo->prepare($sql);
		$stmt->execute(array_values($input_arguments));
		$total = intval($stmt->fetchColumn());
		
		return array
		(
			'rows_per_page'=> $length,
			'current_page' => floor($offset / $length) + 1,
			'total_pages'  => ceil($total / $length),
			'total_rows'   => $total,
			'offset'       => $offset,
		);
	}
	
	/**
	 * @implements <IDatabaseDriver::Now>
	 */
	function Now($seconds_to_add=0)
	{
		$seconds_to_add = ($seconds_to_add>=0)?"+$seconds_to_add":"-$seconds_to_add";
		return "(datetime('now','$seconds_to_add seconds','localtime'))";
	}
    
	/**
	 * @implements <IDatabaseDriver::PreprocessSql>
	 */
    function PreprocessSql($sql)
    {
        return $sql;
    }
}
