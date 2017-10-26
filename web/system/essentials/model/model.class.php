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

use Iterator;
use ArrayAccess;
use Countable;
use DateTime;
use Exception;
use ScavixWDF\Base\DateTimeEx;
use ScavixWDF\WdfDbException;
use ScavixWDF\WdfException;


/**
 * This is base class for data objects.
 * 
 * It provides all the stuff to handle DB access really simple following the
 * ActiveRecord paradigm.
 * Implements Iterator, Countable and ArrayAccess for ease of use in for and foreach loops.
 * Also has methods like all(), like() and so on to access your data the really easy way:
 * <code>
 * $some_does = MyModelClass::Make()->orAll()->like('firstname','%john%')->equal('lastname','doe');
 * foreach( $some_does as $sd ) echo $sd;
 * </code>
 */
abstract class Model implements Iterator, Countable, ArrayAccess
{
	/**
	 * Derivered classes must implement this and return the table name they are stored in.
	 * 
	 * @return string Table name
	 */
	abstract function GetTableName();
	
	public static $DefaultDatasource = false;
    public static $SaveDelayed = false;
	
	protected static $_schemaCache = array();
	private static $_typeMap = array();
	protected $_className = false;
	protected $_isInherited = false;
	protected $_cacheKey;
	
    protected $_ds = false;
    protected $_tableSchema = false;

	var $_query = false;
	protected $_results = false;
	protected $_index = 0;
	protected $_fieldValues = array();
	protected $_dbValues = array();
	
	var $_querySql = false;
	var $_queryArgs = array();
	
	var $_saved = false;

	/**
	 * @implements <Iterator::rewind>
	 */
	function rewind() { $this->_index = 0; }
	
	/**
	 * @implements <Iterator::current>
	 */
    function current() { $this->__ensureResults(); return isset($this->_results[$this->_index])?$this->_results[$this->_index]:null; }
	
	/**
	 * @implements <Iterator::key>
	 */
    function key() { return $this->_index; }
	
	/**
	 * @implements <Iterator::next>
	 */
    function next() { $this->_index++; }
	
	/**
	 * @implements <Iterator::valid>
	 */
    function valid() { $this->__ensureResults(); return isset($this->_results[$this->_index]); }

	/**
	 * @implements <ArrayAccess::offsetSet>
	 */
	public function offsetSet($offset, $value)
	{
		$this->__ensureResults();
		if( $this->_query )
			$this->_results[$offset] = $value;
		else
			$this->$offset = $value;
	}
	
	/**
	 * @implements <ArrayAccess::offsetExists>
	 */
    public function offsetExists($offset)
	{
		$this->__ensureResults();
		if( $this->_query )
			return isset($this->_results[$offset]);
		return isset($this->$offset);
	}
	
	/**
	 * @implements <ArrayAccess::offsetUnset>
	 */
    public function offsetUnset($offset)
	{
		$this->__ensureResults();
		if( $this->_query )
			unset($this->_results[$offset]);
		else
			unset($this->$offset);
	}
	
	/**
	 * @implements <ArrayAccess::offsetGet>
	 */
    public function offsetGet($offset)
	{
		$this->__ensureResults();
		if( $this->_query )
			return isset($this->_results[$offset]) ? $this->_results[$offset] : null;
		return isset($this->$offset) ? $this->$offset : null;
	}

	/**
	 * Returns the amount of results in the current query.
	 * 
	 * @return int Amount of results
	 */
	function count(){ $this->__ensureResults(); return count($this->_results); }
	
	/**
	 * Returns an array containing all results.
	 * 
	 * In fact you may use the <Model> itself in foreach loops or stuff, but sometimes
	 * it is better to get a plain array. For example if you need to test with `is_array`.
	 * @return array Array of results (may be empty)
	 */
	function results()
	{
		if( $this->_query )
		{
			$this->__ensureResults(); 
			return $this->_results;
		}
		return array($this);
	}
	
	/**
	 * Enumerates all values from a column of the current result.
	 * 
	 * <code php>
	 * $emails = MyModel::Make()->lt('id',1000)->enumerate('email',true);
	 * </code>
	 * @param string $property_or_fieldname Property-/Fieldname
	 * @param bool $distinct If true will <array_unique> the results.
	 * @return array Array of values
	 */
	function enumerate($property_or_fieldname, $distinct=true)
	{
		$res = array();
		foreach( $this as $tmp )
			if( !$distinct || !in_array($tmp->$property_or_fieldname, $res) )
				$res[] = $tmp->$property_or_fieldname;
		return $res;
	}

	/**
	 * Returns true is this is a query, false if this represents a datatset
	 * 
	 * @return bool true or false
	 */
	public function IsQuery()
	{
		return $this->_query;
	}
	
	/**
	 * Returns true is this is a dataset, false if this represents a query
	 * 
	 * @return bool true or false
	 */
	public function IsRow()
	{
		return !$this->IsQuery();
	}
	
	/**
	 * @shortcut <ResultSet::LogDebug>
	 */
	public function LogDebug()
	{
		$this->__ensureResults();
		$this->_ds->LastStatement->LogDebug();
	}

    function __construct($datasource=null)
    {
		$this->_className = get_class($this);
		$this->_isInherited = $this->_className != "Model";
		if( !$datasource )
		{
			if( !self::$DefaultDatasource )
			{
				$aliases = array_keys($GLOBALS['MODEL_DATABASES']);
				self::$DefaultDatasource = model_datasource(array_pop($aliases));
			}
			if( self::$DefaultDatasource )
				$this->__initialize(self::$DefaultDatasource);
			else
				$this->__initialize();
		}
		else
			$this->__initialize($datasource);
	}
	
	function __initialize($datasource=null)
	{
		if( $datasource && !($datasource instanceof DataSource) )
			WdfDbException::Raise("Invalid argument. Object of type DataSource expected",$datasource);
		
        $this->_ds = $datasource;
		if( $this->_ds )
		{
			if( !isset($this->_cacheKey) || !$this->_cacheKey )
				$this->_cacheKey = $this->_ds->Database().$this->_className;
			if( !unserializer_active() )
				$this->__ensureTableSchema();
		}
		else
			log_trace("Missing datasource argument");
    }
	
	function __wakeup()
	{
		if( isset($this->_query) )
			return;
		$q = $this->_ds->Query($this->GetTableName());
		foreach( $this->GetPrimaryColumns() as $pk )
			$q = $q->eq($pk,$this->$pk);
		$q = $q->current();
		foreach( $this->GetColumnNames() as $cn )
			$this->$cn = $q->$cn;
		$this->__init_db_values();
	}
	
	function __init_db_values($known_as_empty=false)
	{
		$this->_saved = !$known_as_empty;
		$this->_dbValues = array();
		if( !$this->_tableSchema )
			$this->__ensureTableSchema();
		foreach( $this->_tableSchema->ColumnNames() as $col )
		{
			if( $known_as_empty )
			{
				$this->$col = null;
				$this->_dbValues[$col] = null;
			}
			else
			{
				$this->$col = !isset($this->$col)?null:$this->__typedValue($col); // do not use $this->TypedValue because may be overridden
				$this->_dbValues[$col] = $this->$col;
			}
		}
	}
	
	private function __typeOf($column_name)
	{
		if( isset(self::$_typeMap[$this->_cacheKey][$column_name]) )
			self::$_typeMap[$this->_cacheKey][$column_name];
				
		if( !$this->_tableSchema )
			$this->__ensureTableSchema();
		if( $res = $this->_tableSchema->TypeOf($column_name) )
		{
			self::$_typeMap[$this->_cacheKey][$column_name] = $res;
			return $res;
		}
		if( isset($this->$column_name) )
		{
			self::$_typeMap[$this->_cacheKey][$column_name] = gettype($this->$column_name);
			return self::$_typeMap[$this->_cacheKey][$column_name];
		}
		return false;
	}
	
	private function __typedValue($column_name)
	{
		if( !isset($this->$column_name) )
			return null;
		return $this->__toTypedValue($column_name, $this->$column_name);
	}
	
	private function __toTypedValue($column_name,$value)
	{
		if( isset(self::$_typeMap[$this->_cacheKey][$column_name]) )
			$t = self::$_typeMap[$this->_cacheKey][$column_name];
		else
			$t = $this->__typeOf($column_name);
		
		switch( $t )
		{
			case 'int':
			case 'integer':
				return intval($value);
			case 'float':
			case 'double':
				return floatval($value);
			case 'date':
			case 'time':
			case 'datetime':
			case 'timestamp':
				
				try
				{
					return Model::EnsureDateTime($value);
				}
				catch(Exception $ex)
				{
					WdfException::Log("date/time error with value (".gettype($value).")$value",$ex);
				}
				break;
		}
		return $value;
	}

	public function __clone()
	{
		if( $this->_query )
			$this->_query = clone $this->_query;
		$this->_results = false;
		$this->_index = 0;
	}
	
	protected function __ensureTableSchema()
	{
		if( $this->_tableSchema )
			return $this->_tableSchema;
		
		if( !$this->_ds )
			WdfDbException::Raise("Missing Datasource");
		
		if( !isset(self::$_schemaCache[$this->_cacheKey]) )
		{
			self::$_schemaCache[$this->_cacheKey] = $this->_tableSchema = $this->_ds->Driver->getTableSchema($this->GetTableName());
		}
		else
		{
			$this->_tableSchema = self::$_schemaCache[$this->_cacheKey];
		}
		if( !($this->_tableSchema) )
			WdfDbException::Raise("Error using table schema");
		
		return $this->_tableSchema;
	}
	
	protected function __ensureResults($ctor_args=null)
	{
		if( $this->_results === false )
		{
			if( $this->_query )
				$this->_results = $this->_query->__execute($this->_querySql,$this->_queryArgs,$ctor_args);
			$this->_index = 0;
		}
	}
	
	/**
	 * Returns a single value from the first result object in the query.
	 * 
	 * <code php>
	 * $name = $ds->Query('sometable')->eq('id')->scalar('name');
	 * </code>
	 * @param string $property Property name
	 * @param mixed $default Default if nothing was found
	 * @return mixed The value of $default
	 */
	public function scalar($property,$default=null)
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->setResultFields($property);
		$res->_query->limit(0,1);
		$res->__ensureResults();
		$res = $res->current();
		if($res)
		{
			if(is_array($property))
			{
				// multiple fields requested
				$ret = array();
				foreach($property as $p)
					$ret[$p] = $res->$p;
				return $ret;
			}
			else
				if(isset($res->$property))
					return $res->$property;
		}
		return $default;
	}
	
	/**
	 * @shortcut <ResultSet::GetPagingInfo>
	 */
	public function GetPagingInfo($key=false)
	{
		$this->__ensureResults();
		return $this->_query->GetPagingInfo($key);
	}
	
	/**
	 * Returns all column values.
	 * 
	 * @return array plain array of values
	 */
	public function FieldValues()
	{
		$res = array();
		foreach( func_get_args() as $col )
			$res[] = $this->__typedValue($col);
		return $res;
	}
	
	/**
	 * @internal Wrapper around private method to allow overriding without breaking internal functionality
	 */
	public function TypeOf($column_name)
	{
		return $this->__typeOf($column_name);
	}
	
	/**
	 * @internal Wrapper around private method to allow overriding without breaking internal functionality
	 */
	public function TypedValue($column_name)
	{
		return $this->__typedValue($column_name);
	}

	/**
	 * @shortcut <Model::Make>($datasource)
	 */
	public static function &Select($datasource)
	{
		$className = get_called_class();
		$res = new $className($datasource);
		$res->__ensureSelect();
		return $res;
	}
	
	/**
	 * Queries the database for <Model>s but using an SQL statement.
	 * 
	 * Use this if you do not like the QueryBuilder or if you have really complicated queries.
	 * @param string $sql Statement
	 * @param array $args Arguments
	 * @param DataSource $datasource Use this datasource
	 * @return <Model> The result set
	 */
	public static function &Query($sql,$args=array(),$datasource=null)
	{
		$className = get_called_class();
		$res = new $className($datasource);
		$res->__ensureSelect($sql);
		//$sql = preg_replace('/^select\s(.*)\swhere\s/Ui','',$sql);
		$res->_query->sql($sql,force_array($args));
		return $res;
	}

	/**
	 * Static creator method for easy Model instaciation and instant method chaining
	 * 
	 * <code php>
	 * $new_datasets = MyModelClass::Make()->youngerThan('created',1,'month');
	 * </code>
	 * There's also a shortcut syntax to load a dataset automatically, but this will only work if the tables primary key
	 * constist of only one column:
	 * <code php>
	 * $loaded = MyModelClass::Make(null,2);
	 * </code>
	 * @param DataSource $datasource <DataSource> to bind to, defaults to <Model>::$DefaultDatasource
	 * @param mixed $pk_value Primary key value
	 * @return Model Returns the created model or null, if nothing can be found for a specified $pk_value
	 */
	public static function &Make($datasource=null,$pk_value=false)
    {
		$className = get_called_class();
		$res = new $className($datasource);
		if( $pk_value !== false )
		{
			$q = $res->andAll();
			$pkcols = $res->GetPrimaryColumns();
			if( count($pkcols) == 1 && !is_array($pk_value) )
				$pk_value = array($pkcols[0] => $pk_value);
			foreach( $pkcols as $pkc )
			{
				if( !isset($pk_value[$pkc]) )
					WdfDbException::Raise("Missing value for primary key column '{$pkc}'");
				$q = $q->equal($pkc,$pk_value[$pkc]);
			}
			return $q->current();
		}
		else
			$res->__init_db_values(true);
		return $res;
    }
	
	/**
	 * Creates a typed <Model> object from array-based data.
	 * 
	 * You may optionally add a datasource.
	 * <code php>
	 * function make_new_contact(array $data)
	 * {
	 *     ContactModel::MakeFromData($data)->Save();
	 * }
	 * </code>
	 * 
	 * @param array $data Associative array with data
	 * @param DataSource $datasource Optional datasource to assign to the created <Model>
	 * @param bool $allFields If true, all data is taken to the result, not only that one that are present in the columns of the type
	 * @param bool $className Optional classname to allow anonymous calls like `Model::MakeFromData`
	 * @return subclass_of_Model The newly created typed <Model>
	 */
	public static function MakeFromData($data,$datasource=null,$allFields=false,$className=false)
	{
		$className = $className?$className:get_called_class();
		$res = new $className($datasource);
		$pks = $res->GetPrimaryColumns();
        
        if( $allFields )
		{
			$columns = array_diff(
				array_keys($data),
				array_keys(get_object_vars($res))
			);
		}
		else
			$columns = $res->GetColumnNames();
        
		foreach( $columns as $cn )
		{
			if( isset($data[$cn]) )
				$res->$cn = $data[$cn];
			$i = array_search($cn, $pks);
			if( $i !== false )
				unset($pks[$i]);
		}
		$res->__init_db_values(false);
		$res->_saved = count($pks)==0;
		return $res;
	}
	
	/**
	 * Typecasts a <Model> (sub-)class to another type.
	 * 
	 * <code php>
	 * $entry = $ds->Query('my_table')->eq('id',1)->current(); // $entry is instance of CommonModel
	 * $entry = MyTableModel::CastFrom($entry);                // now it is type of MyTableModel
	 * </code>
	 * @param Model $model Object of (sub-)type <Model>
	 * @param bool $allFields If true, all data is taken to the result, not only that one that are present in the columns of the type
	 * @param bool $className Optional classname to allow anonymous calls like `Model::MakeFromData`
	 * @return subclass_of_Model The typed object
	 */
	public static function CastFrom($model,$allFields=false,$className=false)
	{
        $className = $className?$className:get_called_class();
		$res = new $className($model->_ds);
		$pks = $res->GetPrimaryColumns();
		
		if( $allFields )
		{
			$columns = array_diff(
				array_keys(get_object_vars($model)),
				array_keys(get_object_vars($res))
			);
		}
		else
			$columns = $res->GetColumnNames();
		
		foreach( $columns as $cn )
		{
			if( isset($model->$cn) )
				$res->$cn = $model->$cn;
			$i = array_search($cn, $pks);
			if( $i !== false )
				unset($pks[$i]);
		}
		$res->__init_db_values(false);
		$res->_saved = count($pks)==0;
		return $res;
	}
	
	/**
	 * Static tool method to ensure $value is of type <DateTimeEx>.
	 * 
	 * @param mixed $value Some value representing a datetime
	 * @param bool $convert_now_to_value if true will check if `$value=='now()'` and if so return `new DateTimeEx` instead of `'now()'`
	 * @return mixed <DateTimeEx> value or 'now()' if $convert_now_to_value is fale and value is 'now()'
	 */
	public static function EnsureDateTime($value,$convert_now_to_value=false)
	{
		if( $value === null )
			return null;
		if( $value instanceof DateTimeEx )
			return $value;
		if( $value instanceof DateTime )
			return DateTimeEx::Make($value);
		if( is_string($value) )
		{
			// special handling for NOW() argument
			if( strtolower($value) == "now()" )
			{
				if( $convert_now_to_value )
					return new DateTimeEx();
				return "now()";
			}
			// check if we have a timestamp as string
			if( is_numeric($value) )
			{
				$res = new DateTimeEx();
				$res->setTimestamp(intval($value));
				return $res;
			}
			else
			{
				// add eventually missing time part
				$value = preg_replace(
						'/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/',
						'$1-$2-$3 00:00:00',
						$value);
				// add eventually missing fractional seconds part to ISO 8601 format
				$value = preg_replace(
						'/([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})([+-]{1})([0-9]{2}):([0-9]{2})/',
						'$1-$2-$3T$4:$5:$6.00$7$8:$9',
						$value);
			}
		}
		elseif( is_integer($value) || is_float($value) || is_double($value) )
		{
			$res = new DateTimeEx();
			$res->setTimestamp($value);
			return $res;
		}
		return new DateTimeEx($value);
	}
	
	/**
	 * Returns the names of all primary columns.
	 * 
	 * @return array List of all columns that belong to the primary key
	 */
	public function GetPrimaryColumns()
	{
		return $this->__ensureTableSchema()->PrimaryColumnNames();
	}

	/**
	 * Returns a list of column names.
	 * 
	 * If $changed_only is true will only return names of fields which values have been changed compared to the saved values.	 * 
	 * @param bool $changed_only Return only changed columns names
	 * @return array A list of column names
	 */
	public function GetColumnNames($changed_only = false)
	{
		if( !$changed_only )
			return $this->__ensureTableSchema()->ColumnNames();
		
		$res = array();
		//$cols = array_diff(array_keys(get_object_vars($this)), array_keys(get_class_vars(get_class($this))));
		foreach( $this->__ensureTableSchema()->ColumnNames() as $col )
		{
			if( isset($this->$col) )
			{
				if( !isset($this->_dbValues[$col]) )
					$res[] = $col;
				else
				{
					$v1 = $this->__typedValue($col);
					if( $v1 instanceof DateTime )
						$v1 = $v1->format('U');
					
					$v2 = $this->__toTypedValue($col,$this->_dbValues[$col]);
					if( $v2 instanceof DateTime )
						$v2 = $v2->format('U');
					
					if( $v1 != $v2 )
						$res[] = $col;
				}
			}
			else
			{
				if( isset($this->_dbValues[$col]) )
					$res[] = $col;
			}
		}
		return $res;
		//return array_keys($this->_changedColumns);
    }
    
    public function GetChanges()
	{
		$res = array();
		foreach( $this->GetColumnNames(true) as $col )
		{
            $v1 = $this->__typedValue($col);
            if( $v1 instanceof DateTime )
                $v1 = $v1->format('U');

            if( isset($this->_dbValues[$col]) )
            {
                $v2 = $this->__toTypedValue($col,$this->_dbValues[$col]);
                if( $v2 instanceof DateTime )
                    $v2 = $v2->format('U');
            }
            else
                $v2 = null;
            $res[$col] = [$v2,$v1];
		}
		return $res;
    }
    
    public function HasChanged($col)
    {
        if( isset($this->$col) )
        {
            if( !isset($this->_dbValues[$col]) )
                return true;
         
            $v1 = $this->$col;
            if( $v1 instanceof DateTime )
                $v1 = $v1->format('U');

            $v2 = $this->_dbValues[$col];
            if( $v2 instanceof DateTime )
                $v2 = $v2->format('U');

            return $v1 != $v2;
        }
        return isset($this->_dbValues[$col]);
    }

	/**
	 * Checks if this <Model> has a column $name.
	 * 
	 * @param string $name Column name to check for
	 * @return bool true or false
	 */
	public function HasColumn($name)
	{
		return $this->__ensureTableSchema()->HasColumn($name);
	}

	/**
	 * Creates a full qualified fieldname.
	 * 
	 * That is ```tablename`.field_name``
	 * @param string $name Name to FQ
	 * @return string FQ fieldname
	 */
	public function FullQualifiedFieldName($name)
	{
		return "`{$this->GetTableName()}`.".$this->__ensureFieldname($name);
	}
	
	/**
	 * Returns all field values a array.
	 * 
	 * @return array Associative array of fieldname=>value pairs
	 */
	public function AsArray()
	{
		$res = array();
		$filter = func_get_args();
		if( count($filter)>0 )
		{
            if( count($filter)==1 && is_array($filter[0]) )
                $filter = $filter[0];
                
			foreach( $filter as $cn )
				if( isset($this->$cn) )
					$res[$cn] = $this->__typedValue($cn);
		}
		else
		{
			foreach( $this->GetColumnNames() as $cn )
				if( count($filter)==0 || in_array($cn, $filter) )
					$res[$cn] = $this->__typedValue($cn);
		}
		return $res;
	}
	
	/**
	 * Uses <system_sanitize_parameters> to sanitze all field values.
	 * 
	 * @return void
	 */
	public function SanitizeValues()
	{
		$vals = $this->AsArray();
		system_sanitize_parameters($vals);
		foreach( $vals as $k=>$v )
			$this->$k = $v;
	}

	function __ensureFieldname($name)
	{
		if( $this->HasColumn($name) )
			return "`$name`";
		WdfDbException::Raise("Unknown column '$name' in table '{$this->_tableSchema->Name}'");
	}

	private function __ensureSelect($select_statement=false)
	{
		if( !$this->_query )
			$this->_query = new SelectQuery($this,$this->_ds,$select_statement);
	}

	/**
	 * Ensures a valid select query.
	 * 
	 * Similar to <Model::noop> but will not add the `1=1` condition.
	 * @return Model `clone $this`
	 */
	public function all()
	{
		$res = clone $this;
		$res->__ensureSelect();
		return $res;
	}

	/**
	 * Marks that from now on all following conditions will be `AND` combined.
	 * 
	 * Note that this is default behaviour!
	 * <code php>
	 * $q1 = MyModel::Make()->andAll()->eq('id',1)->gt('sort',2);
	 * // as andAll() is default that is the same like this
	 * $q2 = MyModel::Make()->eq('id',1)->gt('sort',2);
	 * </code>
	 * @return Model `clone $this`
	 */
	function andAll()
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->andAll();
		return $res;
	}

	/**
	 * Marks that from now on all following conditions will be `OR` combined.
	 * 
	 * <code php>
	 * $q = MyModel::Make()->orAll()->eq('id',1)->eq('id',2);
	 * </code>
	 * @return Model `clone $this`
	 */
	function orAll()
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->orAll();
		return $res;
	}

	/**
	 * Marks that the next X conditions will be `AND` combined.
	 * 
	 * <code php>
	 * $q1 = MyModel::Make()->orAll()->eq('id',1)->andX(2)->gt('sort',2)->lt('sort',10);
	 * // SELECT FROM my_model WHERE id=1 OR (sort>2 AND sort<10)
	 * </code>
	 * @param int $count How many following calls shall be AND-combined
	 * @return Model `clone $this`
	 */
	function andX($count)
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->andX($count);
		return $res;
	}

	/**
	 * Marks that the next X conditions will be `OR` combined.
	 * 
	 * <code php>
	 * $q1 = MyModel::Make()->orX(2)->eq('id',1)->andX(2)->gt('sort',2)->lt('sort',10);
	 * // SELECT FROM my_model WHERE id=1 OR (sort>2 AND sort<10)
	 * </code>
	 * @param int $count How many following calls shall be OR-combined
	 * @return Model `clone $this`
	 */
	function orX($count)
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->orX($count);
		return $res;
	}

	/**
	 * Adds a HAVING statement.
	 * 
	 * @param string $defaultOperator 'AND' or 'OR'
	 * @return Model `clone $this`
	 */
	public function having($defaultOperator = "AND")
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->having($defaultOperator);
		return $res;
	}
	
	/**
	 * Adds a raw SQL part to the statement.
	 * 
	 * @param string $sql_statement_part The raw SQL code
	 * @param array $args The arguments of the raw SQL query part
	 * @return Model `clone $this`
	 */
	public function sql($sql_statement_part,$args=array())
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->sql($sql_statement_part,force_array($args));
		return $res;
	}

	/**
	 * Check if a field has a value.
	 * 
	 * @param string $property Property-/Fieldname
	 * @param mixed $value Value to check for
	 * @param bool $value_is_sql if true, $value is treaded as SQL keyword/function/... and will fremain unescaped (sample: now())
	 * @return Model `clone $this`
	 */
	public function equal($property,$value,$value_is_sql=false)
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->equal($this->__ensureFieldname($property),$value_is_sql?$value:$this->__toTypedValue($property,$value),$value_is_sql);
		return $res;
	}
	
	/**
	 * Check if a field has NOT a value.
	 * 
	 * @param string $property Property-/Fieldname
	 * @param mixed $value Value to check for
	 * @return Model `clone $this`
	 */
	public function notEqual($property,$value)
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->notEqual($this->__ensureFieldname($property),$this->__toTypedValue($property,$value));
		return $res;
	}
	
	/**
	 * Check if a fields value is lower than or equal to something.
	 * 
	 * @param string $property Property-/Fieldname
	 * @param mixed $value Value to check against
	 * @return Model `clone $this`
	 */
	public function lowerThanOrEqualTo($property,$value)
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->lowerThanOrEqualTo($this->__ensureFieldname($property),$this->__toTypedValue($property,$value));
		return $res;
	}
	
	/**
	 * Check if a fields value is lower than something.
	 * 
	 * @param string $property Property-/Fieldname
	 * @param mixed $value Value to check against
	 * @return Model `clone $this`
	 */
	public function lowerThan($property,$value)
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->lowerThan($this->__ensureFieldname($property),$this->__toTypedValue($property,$value));
		return $res;
	}
	
	/**
	 * Check if a fields value is greater than or equal to something.
	 * 
	 * @param string $property Property-/Fieldname
	 * @param mixed $value Value to check against
	 * @return Model `clone $this`
	 */
	public function greaterThanOrEqualTo($property,$value)
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->greaterThanOrEqualTo($this->__ensureFieldname($property),$this->__toTypedValue($property,$value));
		return $res;
	}
	
	/**
	 * Check if a fields value is greater than something.
	 * 
	 * @param string $property Property-/Fieldname
	 * @param mixed $value Value to check against
     * @param bool $value_is_sql if true, $value is treaded as SQL keyword/function/... and will fremain unescaped (sample: now())
	 * @return Model `clone $this`
	 */
	public function greaterThan($property,$value,$value_is_sql=false)
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->greaterThan($this->__ensureFieldname($property),$value_is_sql?$value:$this->__toTypedValue($property,$value),$value_is_sql);
		return $res;
	}
	
	/**
	 * Check if a fields value is binary equal to another value.
	 * 
	 * @param string $property Property-/Fieldname
	 * @param mixed $value Value to check against
	 * @return Model `clone $this`
	 */
	public function binary($property,$value)
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->andX(2);
		$res->_query->equal($this->__ensureFieldname($property),$this->__toTypedValue($property,$value));
		$res->_query->binary($this->__ensureFieldname($property),$this->__toTypedValue($property,$value));
		return $res;
	}

	/**
	 * Check if a fields value is LIKE another value.
	 * 
	 * See http://www.w3schools.com/sql/sql_like.asp
	 * @param string $property Property-/Fieldname
	 * @param mixed $value Value to check against
	 * @param bool $flipped If true, expects the roles of $property and $value switched
	 * @return Model `clone $this`
	 */
	public function like($property,$value,$flipped=false)
	{
		$res = clone $this;
		$res->__ensureSelect();
		if( $flipped )
			$res->_query->like("$property",$this->__ensureFieldname($value),$flipped);
		else
			$res->_query->like($this->__ensureFieldname($property),"$value",$flipped);
		return $res;
	}

	/**
	 * Check if a fields value is RLIKE another value.
	 * 
	 * MySQL specific: see http://dev.mysql.com/doc/refman/5.1/en/regexp.html
	 * @todo Check how <SqLite> must handle this. Perhaps use <IDatabaseDriver::PreprocessSql>?
	 * @param string $property Property-/Fieldname
	 * @param mixed $value Value to check against
	 * @param bool $flipped If true switches the roles of $property and $value
	 * @return Model `clone $this`
	 */
	public function rlike($property,$value,$flipped=false)
	{
		$res = clone $this;
		$res->__ensureSelect();
		if( $flipped )
			$res->_query->rlike("$property",$this->__ensureFieldname($value),$flipped);
		else
			$res->_query->rlike($this->__ensureFieldname($property),"$value",$flipped);
		return $res;
	}

	/**
	 * Checks if a fields value is one of the $values.
	 * 
	 * @param string $property Property-/Fieldname
	 * @param array $values Array of values to check against
	 * @return Model `clone $this`
	 */
	public function in($property,$values)
	{
		$res = clone $this;
		$res->__ensureSelect();
		if( $values !== null && count($values)>0 )
			$res->_query->in($this->__ensureFieldname($property),$values);
		else
			$res->_query->sql("0=1"); // false condition if there's nothing in the values
		return $res;
	}
	
	/**
	 * Checks if a fields value is NOT one of the $values.
	 * 
	 * @param string $property Property-/Fieldname
	 * @param array $values Array of values to check against
	 * @return Model `clone $this`
	 */
	public function notIn($property,$values)
	{
		$res = clone $this;
		$res->__ensureSelect();
        if( $values !== null && count($values)>0 )
            $res->_query->notIn($this->__ensureFieldname($property),$values);
        else
			$res->_query->sql("1=1"); // true condition if there's nothing in the values
		return $res;
	}

	/**
	 * Checks if a fields value is NULL.
	 * 
	 * @param string $property Property-/Fieldname
	 * @return Model `clone $this`
	 */
	public function isNull($property)
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->isNull($this->__ensureFieldname($property));
		return $res;
	}

	/**
	 * Checks if a fields value is NOT NULL.
	 * 
	 * @param string $property Property-/Fieldname
	 * @return Model `clone $this`
	 */
	public function notNull($property)
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->notNull($this->__ensureFieldname($property));
		return $res;
	}

	/**
	 * Adds a orderBy statement to the query.
	 * 
	 * @param string $property Property-/Fieldname to order by
	 * @param string $direction 'ASC' or 'DESC'
	 * @return Model `clone $this`
	 */
	public function orderBy($property,$direction = "ASC",$checkfieldname=true)
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->orderBy(((starts_iwith($property, 'FIELD(') || !$checkfieldname) ? $property : $this->__ensureFieldname($property)),$direction);
		return $res;
	}
	
	/**
	 * Like <Model::orderBy> but adds 'ORDER BY rand()'.
	 * 
	 * @return Model `clone $this`
	 */
	public function shuffle()
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->orderBy('{SPECIAL}','rand()');
		return $res;
	}

	/**
	 * Adds a groupBy statement to the query.
	 * 
	 * @param string $property Property-/Fieldname to group by
	 * @return Model `clone $this`
	 */
	public function groupBy($property)
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->groupBy($this->__ensureFieldname($property));
		return $res;
	}

	/**
	 * @shortcut <Model::page>(0,$limit);
	 */
	public function limit($limit)
	{
		return $this->page(0,$limit);
	}

	/**
	 * Adds paging to the query.
	 * 
	 * @param int $offset Zero-based offset
	 * @param int $items Maximum items to return
	 * @return Model `clone $this`
	 */
	public function page($offset,$items)
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->limit($offset,$items);
		return $res;
	}

	/**
	 * Join two database tables.
	 * 
	 * @param string $direction E.g. 'LEFT', 'RIGHT' or 'FULL'. Also 'LEFT OUTER'.
	 * @param Model $model An instance of a Model subclass.
	 * @return Model `clone $this`
	 */
	public function join($direction,$model)
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->join($direction,$model);
		return $res;
	}
	
	/**
	 * Condition: column $property must be datetime and it's value newer than given interval
	 * 
	 * See <DateTimeEx::youngerThan>
	 * @param string $property Properpy-/Fieldname
	 * @param int $value Offset value
	 * @param string $interval Unit
	 * @return Model `clone $this`
	 */
	public function newerThan($property,$value,$interval)
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->newerThan($property,$value,$interval);
		return $res;
	}
	
	/**
	 * Condition: column $property must be datetime and it's value older than given interval
	 * 
	 * See <DateTimeEx::olderThan>
	 * @param string $property Properpy-/Fieldname
	 * @param int $value Offset value
	 * @param string $interval Unit
	 * @return Model `clone $this`
	 */
	public function olderThan($property,$value,$interval)
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->olderThan($property,$value,$interval);
		return $res;
	}
	
	/**
	 * @shortcut <Model::olderThan>($property,0,'second')
	 */
	public function isPast($property)
	{
		return $this->olderThan($property, '0', 'second');
	}
	
	/**
	 * Filters by date values in the future
	 * @shortcut <Model::newerThan>($property,0,'second')
	 */
	public function isFuture($property)
	{
		return $this->newerThan($property, '0', 'second');
	}
		
	/**
	 * This is just a 'no operation' method.
	 * 
	 * You may use to ensure there's a valid query built by adding `1=1` to the conditions
	 * <code php>
	 * $q = MyModel::Make()->noop();
	 * $m1 = $q->eq('id',1)->current();
	 * $m2 = $q->eq('id',2)->current();
	 * </code>
	 * @return Model `clone $this`
	 */
	public function noop()
	{
		$res = clone $this;
		$res->__ensureSelect();
		$res->_query->noop();
		return $res;
	}
	
	/**
	 * @internal Returns the name of the assigned <DataSource> (the alias)
	 */
	function DataSourceName()
	{
		if( $this->_ds )
			return $this->_ds->_storage_id;
		elseif( self::$DefaultDatasource )
			return self::$DefaultDatasource->_storage_id;
		WdfDbException::Raise("Model has no valid DataSource");
	}
	
	/**
	 * Loads a Model using SQL.
	 * 
	 * All field values will be loaded from DB.
	 * @param string $where WHERE-part of the SQL statement.
	 * @param array $arguments Arguments used in $where
	 * @return boolean true if dataset was found, else false
	 */
	public function Load($where, $arguments=false)
	{
		$q = new SelectQuery($this, $this->_ds);
		$sql = $q->__toString()." WHERE ".$where;
		
		if( $arguments !== false && !is_array($arguments) ) $arguments = array($arguments);
		$q = $this->_ds->ExecuteSql($sql,$arguments);
		if( $q->rowCount() > 0 )
		{
			foreach( $this->GetColumnNames() as $col )
				$this->$col = $q[$col];
			$this->_query = false;
			$this->_results = false;
			$this->_index = 0;
			$this->__init_db_values();
			return true;
		}
		else
			$this->__init_db_values(true);
		
		return false;
	}
	
	/**
	 * Saves this model to the database.
	 * 
	 * New datasets will be inserted, loaded ones will be updated automatically.
	 * If $columns_to_update is given only those columns will be stored. This may be useful to avoid DB conflicts in multithread scenarios.
	 * @param array $columns_to_update If given only these fields will be updated. If not Model tries to detect changed columns automatically.
	 * @return boolean In fact always true, WdfDbException will be thrown in error case
	 */
	public function Save($columns_to_update=false)
	{
		$args = array();
		$stmt = $this->_ds->Driver->getSaveStatement($this,$args,$columns_to_update);

		if( !$stmt )
			return true; // nothing to save
				
		if( !$stmt->execute($args) )
			WdfDbException::RaiseStatement($stmt);

		$pkcols = $this->GetPrimaryColumns();
		if( count($pkcols) == 1 )
		{
			$id = $pkcols[0];
			if( !isset($this->$id) )
				$this->$id = $this->_ds->LastInsertId();
		}
		$this->__init_db_values();
		return true;
	}
	
	/**
	 * Passes all given arguments as array to the Save method.
	 * 
	 * Use it like this: `$model->Update('age','last_action');`
	 * when you want to ensure that only these columns are written.
	 * See <Model::Save>() for more information.
	 * @return <Model> `clone $this`
	 */
	public function Update()
	{
		$this->Save(func_get_args());
		return $this;
	}
	
	/**
	 * Deletes this model from the database.
	 * 
	 * @return boolean true or false
	 */
	public function Delete()
	{
		$args = array();
		$stmt = $this->_ds->Driver->getDeleteStatement($this,$args);
		if( !$stmt || !$stmt->execute($args) )
		{
			if( $stmt )
				log_error(get_class($this)."->Delete failed: ",$stmt->ErrorOutput());
			else
				log_error(get_class($this)."->Delete failed: ",$this);
			return false;
		}
		return true;
	}
	
	/**
	 * Selects Models from the database with a partial SQL statement.
	 * 
	 * @param string $where WHERE-part of the SQL statement.
	 * @param array $prms Arguments used in $where
	 * @return array Array of <Model> datasets
	 */
	public function Find($where="",$prms=array())
	{
		$sql = "SELECT * FROM ".$this->GetTableName().($where?" WHERE $where":"");
		$q = new SelectQuery($this, $this->_ds);
		return $q->__execute($sql, $prms);
	}
	
	/**
	 * @shortcut <Model::equal>($property,$value,$value_is_sql)
	 */
	function eq($property,$value,$value_is_sql=false) { return $this->equal($property,$value,$value_is_sql); }
	
	/**
	 * @shortcut <Model::notEqual>($property,$value)
	 */
	function neq($property,$value) { return $this->notEqual($property,$value); }
	
	/**
	 * @shortcut <Model::lowerThanOrEqualTo>($property,$value)
	 */
	function lte($property,$value) { return $this->lowerThanOrEqualTo($property,$value); }	
	
	/**
	 * @shortcut <Model::newerThan>($property, $value, $interval)
	 */
	public function yt($property,$value,$interval){ return $this->newerThan($property, $value, $interval); }
	
	/**
	 * @shortcut <Model::newerThan>($property, $value, $interval)
	 */	
	public function youngerThan($property,$value,$interval){ return $this->newerThan($property, $value, $interval); }
	
	/**
	 * @shortcut <Model::olderThan>($property, $value, $interval)
	 */
	public function ot($property,$value,$interval){ return $this->olderThan($property, $value, $interval); }	
	
	/**
	 * @shortcut <Model::lowerThan>($property, $value)
	 */
	function lt($property,$value) { return $this->lowerThan($property,$value); }

	/**
	 * @shortcut <Model::greaterThanOrEqualTo>($property, $value)
	 */
	function gte($property,$value) { return $this->greaterThanOrEqualTo($property,$value); }

	/**
	 * @shortcut <Model::greaterThan>($property, $value)
	 */
	function gt($property,$value,$value_is_sql=false) { return $this->greaterThan($property,$value,$value_is_sql); }
	
	/**
	 * Calls a callback function for each result dataset.
	 * 
	 * Callback function will receive each row as <Model> object and must return the (eventually changed) <Model> object.
	 * Note that this method will not clone the result, but return the object itself!
	 * @param mixed $callback Anonymous callback function
	 * @return Model Returns `$this`
	 */
	function process($callback)
	{
		$this->__ensureResults();
		$len = count($this->_results);
		for($i=0; $i<$len; $i++)
			$this->_results[$i] = $callback($this->_results[$i]);
		return $this;
	}
}
