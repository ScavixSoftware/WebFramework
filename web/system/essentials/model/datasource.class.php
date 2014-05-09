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
namespace ScavixWDF\Model;

use Exception;
use PDO;
use ScavixWDF\Model\Driver\MySql;
use ScavixWDF\Model\Driver\SqLite;
use ScavixWDF\WdfDbException;

/**
 * Provides access to a database.
 * 
 * Use this to execute SQL statements directly when you need to do so.
 */
class DataSource 
{
    private $_dsn;
	private $_username;
	private $_password;
    private $_pdo;
	
	private $_last_affected_rows_count = 0;
	
    public $_storage_id;
	public $Driver;
	public $LastStatement = false;
    
	/**
	 * Returns a <DataSource> by name.
	 * 
	 * You may use this as alternative for <Model>::$DefaultDatasource by ignoring the $name parameter;
	 * <code php>
	 * $a = Model::$DefaultDatasource;
	 * // is the same as
	 * $b = DataSource::Get();
	 * </code>
	 * @param string $name Aliasname for the datasource or (default) false to get the default datasource
	 * @return DataSource The requested datasource
	 */
	public static function Get($name=false)
	{
		if( !$name )
			return Model::$DefaultDatasource;
		return model_datasource($name);
	}

	/**
	 * Sets the default datasource.
	 * 
	 * This is nicer alternative to setting <Model>::$DefaultDatasource manually.
	 * <code php>
	 * $ds = Datasource::SetDefault('system');
	 * // or
	 * $ds = model_datasource('system');
	 * Datasource::SetDefault($ds);
	 * // or
	 * $ds = Model::$DefaultDatasource = model_datasource('system');
	 * </code>
	 * @param mixed $ds The default datasource or it's aliasname
	 * @return DataSource The newly set default <DataSource> object
	 */
	public static function SetDefault($ds)
	{
		if( !($ds instanceof DataSource) )
			$ds = model_datasource($ds);
		Model::$DefaultDatasource = $ds;
		return $ds;
	}
	
    function __construct($alias=false, $dsn=false, $username=false, $password=false)
    {
		if( !$alias || !$dsn )
			return;
		
		$test = parse_url($dsn);
		if( isset($test['host']) )
		{
			if( $username || $password )
				log_warn("Oldschool DSN overrides username and/or password given");
			$dsn = "{$test['scheme']}:host={$test['host']};dbname=".trim($test['path'],' /').";";
			$username = $test['user'];
			$password = $test['pass'];
		}
		
        $this->_storage_id = $alias;
        $this->_dsn = $dsn;
		$this->_username = $username;
		$this->_password = $password;
		
        try{ 
			$this->_pdo = new PdoLayer($dsn,$username,$password); 
		}catch(Exception $ex){ WdfDbException::Raise("Error connecting database",$dsn,$ex); }
		if( !$this->_pdo )
			WdfDbException::Raise("Something went horribly wrong with the PdoLayer");
		$this->_pdo->setAttribute( PDO::ATTR_STATEMENT_CLASS, array( "\\ScavixWDF\\Model\\WdfPdoStatement", array($this,$this->_pdo) ) );

		$driver = $this->_pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
		switch( $driver )
		{
			case 'sqlite': 
                // trick out the autoloader as it consults the cache which needs a model thus circular...
                require_once(__DIR__.'/driver/sqlite.class.php');
                $this->Driver = new SqLite(); 
                break;
			case 'mysql': 
                // trick out the autoloader as it consults the cache which needs a model thus circular...
                require_once(__DIR__.'/driver/mysql.class.php');
                $this->Driver = new MySql(); 
                break;
			default: WdfDbException::Raise("Unknown DB driver: $driver");
		}
		$this->Driver->initDriver($this,$this->_pdo);
    }
	
	function __get($varname)
	{
		/*--- Compatibility to old model ---*/
		switch($varname)
		{
			case "DB": return $this;
		}
	}
	
	function __sleep()
	{
		return array('_storage_id');
	}
	
	function __wakeup()
	{
		global $CONFIG;
		if( isset($CONFIG['session']) && isset($CONFIG['session']['own_serializer']) && $CONFIG['session']['own_serializer'] )
		{
			$name = explode("::",$this->_storage_id,2);
			if( count($name) < 2 )
				$name = array($this->_storage_id,$this->_storage_id);

			$ds = model_datasource($name[1]);
			if( $ds != null )
			{
				$this->_storage_id = $ds->_storage_id;
				$this->_dsn = $ds->_dsn;
				$this->_username = $ds->_username;
				$this->_password = $ds->_password;
				$this->_pdo = $ds->_pdo;
				$this->Driver = $ds->Driver;
			}
			else
				register_hook(HOOK_POST_INITSESSION,$this,'__wakeup_extended');
		}
		else
			register_hook(HOOK_POST_INITSESSION,$this,'__wakeup_extended');
	}
	
	function __wakeup_extended()
	{
		$name = explode("::",$this->_storage_id,2);
		if( count($name) < 2 )
			$name = array($this->_storage_id,$this->_storage_id);

		$ds = model_datasource($name[1]);
		$this->_storage_id = $ds->_storage_id;
		$this->_dsn = $ds->_dsn;
		$this->_username = $ds->_username;
		$this->_password = $ds->_password;
		$this->_pdo = $ds->_pdo;
		$this->Driver = $ds->Driver;
		log_debug($this->_storage_id." -> ".$this->Database(),"HOOK::__wakeup_extended");
	}
	
	function __equals(&$ds)
	{
		if( !is_object($ds) || get_class($this) != get_class($ds) )
			return false;

		return $this->_dsn == $ds->_dsn && $this->_username == $ds->_username && $this->_password == $ds->_password;
	}
	
	/**
	 * Returns the DSN
	 * 
	 * @return string The Dsn
	 */
	function GetDsn()
	{
		return $this->_dsn;
	}
	
	/**
	 * Escapes an argument
	 * 
	 * The result will not contain escaping chars, but only perform an 'inner escaping'.
	 * This is basically `substr($this->Quote,1,-1)`
	 * @param string $value Argument to be escaped
	 * @return string escaped argument
	 */
	function EscapeArgument($value)
	{
		$res = $this->_pdo->quote($value);
		return substr($res, 1, count($res)-2);
	}
	
	/**
	 * Quotes an argument
	 * 
	 * @param string $value The argument to quote
	 * @return string The quoted argument
	 */
	function QuoteArgument($value)
	{
		return $this->_pdo->quote($value);
	}

	/**
	 * Prepares a statement
	 * 
	 * @param string $sql SQL statement
	 * @return ResultSet Prepared statement
	 */
	function Prepare($sql)
	{
		$stmt = $this->_pdo->prepare($sql);
		if( !$stmt )
			WdfDbException::Raise("Invalid SQL: $sql");
		return new ResultSet($this,$stmt);
	}

	/**
	 * Executes an SQL statement.
	 * 
	 * @param string $sql SQL statement
	 * @param array $parameter Arguments
	 * @return ResultSet The query result
	 */
	function ExecuteSql($sql,$parameter=array())
	{
		if( !is_array($parameter) )
			$parameter = array($parameter);
		
		$stmt = $this->Prepare($sql);
		if( !$stmt->execute($parameter) )
			WdfDbException::Raise("SQL Error: ".$stmt->ErrorOutput(),"\nArguments:",$parameter,"\nMerged:",ResultSet::MergeSql($this,$sql,$parameter));
		$this->_last_affected_rows_count = $stmt->Count();
		return $stmt;
	}
	
	/**
	 * Executes a statement and caches the result.
	 * 
	 * Of course returns the cached result if called again and cached result is still alive.
	 * @param string $sql SQL statement
	 * @param array $prms Arguments for the query
	 * @param int $lifetime Lifetime in seconds
	 * @return ResultSet The ResultSet
	 */
	function CacheExecuteSql($sql,$prms=array(),$lifetime=300)
	{
		if( !system_is_module_loaded('globalcache') )
			return $this->ExecuteSql($sql, $prms);
		
		$key = 'DB_Cache_Sql_'.md5( $sql.serialize($prms) );
		$null = null;
		if( is_null($res = cache_get($key, $null, true, false)) )
		{
			$res = $this->ExecuteSql($sql, $prms);
			if( $res )
			{
				$res->fetchAll();
				$data = $res->serialize();
				cache_set($key, $data, $lifetime, true, false);
			}
		}
		else
			$res = ResultSet::unserialize($res);
		return $res;
	}
	
	/**
	 * @shortcut to <DataSource::DLookUp> but uses cache
	 */
	function CacheDLookUp($field_name, $table_name = "", $where_condition = "", $parameter = array(),$lifetime=300)
	{
		if( !system_is_module_loaded('globalcache') )
			return $this->DLookUp($field_name, $table_name, $where_condition, $parameter);
		
		$key = 'DB_Cache_Look_'.md5( $field_name.$table_name.$where_condition.serialize($parameter) );
		$null = null;
		if( is_null($res = cache_get($key, $null, true, false)) )
		{
			$res = $this->DLookUp($field_name, $table_name, $where_condition, $parameter);
			cache_set($key, $res, $lifetime, true, false);
		}
		return $res;
	}
	
	/**
	 * Entry point for anonymous queries.
	 * 
	 * If you dont want to write a <Model> class for a table you can use this method to create an anonymous query:
	 * <code php>
	 * $entries = $dataSource->Query('my_bog_entries')->youngerThan('created',1,'month');
	 * </code>
	 * @param string $tablename Name of table to query
	 * @return CommonModel The query as CommonModel
	 */
	function Query($tablename)
	{
		return new CommonModel($this,$tablename);
	}
	
	/**
	 * Creates a typed <Model> from an array of data values.
	 * 
	 * Not nice, but fast.
	 * @param string $type Type of <Model> class to create
	 * @param array $fields Data for the new model, keys must be columns, values will be assigned
	 * @param bool $as_new If true treats created Model as new instead of as if it was loaded from database.
	 * @return <Model> The created model
	 */
	function ModelFromArray($type,$fields,$as_new=false)
	{
		$obj = new $type($this);
		$obj->__init_db_values($as_new);
		$attr = array_change_key_case(array_flip($obj->GetColumnNames()), CASE_LOWER);
		foreach( $fields as $k=>$v )
			if( array_key_exists(strtolower($k), $attr) )
				$obj->$k = $v;
		return $obj;
	}
	
	/**
	 * Checks if a table exists.
	 * 
	 * @param string $name Name of table to check
	 * @return bool true or false
	 */
	function TableExists($name)
	{
		return $this->Driver->tableExists($name);
	}
	
	/**
	 * Return now how the database sees it.
	 * 
	 * @param int $seconds_to_add Offset to now in seconds, may be negative too.
	 * @return string String representing now
	 */
	function Now($seconds_to_add=0)
	{
		$sql = $this->Driver->Now($seconds_to_add);
		$rs = $this->CacheExecuteSql("SELECT $sql as dt",array(),1);
		return $rs['dt'];
	}
	
	/**
	 * Returns the table where a <Model> is stored.
	 * 
	 * @param string $type Classname of <Model> to check
	 * @return string Table name
	 */
	function TableForType($type)
	{
		$obj = new $type($this);
		return $obj->GetTableName();
	}
	
	/**
	 * Executes a query and returns the first column of the first row.
	 * 
	 * @param string $sql SQL statement
	 * @param array $prms Arguments for $sql
	 * @return mixed The first scalar
	 */
	function ExecuteScalar($sql,$prms=array())
	{
		$stmt = $this->Prepare($sql);
		$stmt->execute($prms);
		$this->_last_affected_rows_count = $stmt->Count();
        $stmt->FetchMode = PDO::FETCH_NUM;
		return $stmt->fetchScalar();
	}
	
	/**
	 * Same as ExecuteScalar, but uses the cache.
	 * 
	 * @param string $sql SQL statement
	 * @param array $prms Arguments for $sql
	 * @param int $lifetime Lifetime in seconds
	 * @return mixed The first scalar
	 */
	function CacheExecuteScalar($sql,$prms=array(),$lifetime=300)
	{
		if( !system_is_module_loaded('globalcache') )
			return $this->ExecuteScalar($sql, $prms);
		
		$key = 'SB_Cache_Scalar_'.md5( $sql.serialize($prms) );
		$null = null;
		if( is_null($res = cache_get($key, $null, true, false)) )
		{
			$res = $this->ExecuteScalar($sql, $prms);
			cache_set($key, $res, $lifetime, true, false);
		}
		return $res;
	}
	
	/**
	 * @shortcut <DataSource::ExecuteScalar>
	 */
	function GetOne($sql,$prms=array())
	{
		return $this->ExecuteScalar($sql,$prms);
	}
	
	/**
	 * @shortcut for <DataSource::ExecuteScalar>
	 */
	function DLookUp($field_name, $table_name = "", $where_condition = "", $parameter = array())
	{
		$sql = "SELECT " . $field_name . " ". ($table_name ? "FROM " . $table_name : "") . ($where_condition ? " WHERE " . $where_condition : "")." LIMIT 1";
		$res = $this->ExecuteScalar($sql,$parameter);
		return $res===false?null:$res;
	}
	
	/**
	 * Executes a pages query.
	 * 
	 * This will add LIMIT stuff to the statement.
	 * @param string $sql SQL statement
	 * @param int $items_per_page Items per page
	 * @param int $page Page number (1-based!)
	 * @param array $parameter SQL arguments
	 * @return ResultSet The query result
	 */
	function PageExecute($sql,$items_per_page,$page,$parameter=array())
	{
		$stmt = $this->Driver->getPagedStatement($sql,$page,$items_per_page);
		if( !$stmt->execute($parameter) )
			log_error("SQL Error: $sql",$parameter);
		$this->_last_affected_rows_count = $stmt->Count();
		return $stmt;
	}
	
	/**
	 * @shortcut <DataSource::ExecuteSql>
	 */
	function Execute($sql,$args=array())
	{
		return $this->ExecuteSql($sql,$args);
	}
	
	/**
	 * Returns the last errormessage, if any.
	 * 
	 * @return string The last error or false
	 */
	function ErrorMsg()
	{
		$ei = $this->_pdo->errorInfo();
		if( count($ei) == 1 && $ei[0] === "00000" )
			return false;
		if( count($ei) == 0 )
			return false;
		return $ei[2];
	}
	
	/**
	 * Gets the amount of rows affected by the last query.
	 * 
	 * @return int Number of affected rows
	 */
	function getAffectedRowsCount()
	{
		return $this->_last_affected_rows_count;
	}
	
	/**
	 * @shortcut <DataSource::getAffectedRowsCount>
	 */
	function Affected_Rows()
	{
		return $this->_last_affected_rows_count;
	}
	
	/**
	 * Returns the database host.
	 * 
	 * @return string The host or false (for example sqlite has no host)
	 */
	function Host()
	{
		if( !preg_match('/host=([^;]+);*/', $this->_dsn.";", $m) )
			return false;
		return trim($m[1]);
	}
	
	/**
	 * Returns the database username.
	 * 
	 * @return string The username
	 */
	function Username()
	{
		return $this->_username;
	}
	
	/**
	 * Returns the database password.
	 * 
	 * @return string The password
	 */
	function Password()
	{
		return $this->_password;
	}
	
	/**
	 * Returns the database name.
	 * 
	 * @return string The name
	 */
	function Database()
	{
		if( !preg_match('/dbname=([^;]+);*/', $this->_dsn, $m) )
			return false;
		return trim($m[1]);
	}
	
	/**
	 * Returns the id of the last inserted row.
	 * 
	 * @param string $table The table to get last insert id for
	 * @return int The last insert id
	 */
	function LastInsertId($table=null)
	{
		return $this->_pdo->lastInsertId($table);
	}
}
