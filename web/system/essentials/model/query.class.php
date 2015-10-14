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

use DateTime;
use PDO;
use ScavixWDF\WdfDbException;

/**
 * @internal SQL common query builder
 */
class Query
{
	var $_object = false;
	var $_ds = false;
	var $_knownmodels = array();

	var $_initialSequence = false;
	var $_where = false;
	var $_currentTree = false;

	var $_values = array();
	var $_statement = false;

    function __construct(&$obj,&$datasource,$conditions_separator="WHERE")
	{
		if( !unserializer_active() )
		{
			$this->_object = $obj;
			$this->_ds = $datasource;
			$this->_where = new ConditionTree(-1,"AND",$conditions_separator);
			$this->_currentTree = $this->_where;
			$this->_knownmodels = array($obj);
		}
	}
	
	function __toString()
	{
		return $this->_initialSequence . $this->__generateSql();
	}
	
	public function GetSql()
	{
		if( !$this->_statement )
			return "";
		return $this->_statement->GetSql();
	}

	public function GetArgs()
	{
		if( !$this->_statement )
			return array();
		return $this->_statement->GetArgs();
	}
	
	public function GetPagingInfo($key=false)
	{
		if( !$this->_statement )
			return "";
		return $this->_statement->GetPagingInfo($key);
	}

	function __execute($injected_sql=false, $injected_arguments=array(), $ctor_args=null)
	{
		$sql = $injected_sql?$injected_sql:$this->__toString();
		if( $injected_arguments )
		{
			if( is_array($injected_arguments) )
				$this->_values = $injected_arguments;
			else
				$this->_values = array($injected_arguments);
		}

		$this->_statement = $this->_ds->Prepare($sql);
		foreach( $this->_values as $i=>$v )
		{
			if( is_integer($v) )
				$this->_statement->bindValue($i+1,$v,PDO::PARAM_INT);
			elseif( $v instanceof DateTime )
				$this->_statement->bindValue($i+1,$v->format("Y-m-d H:i:s"));
			else
				$this->_statement->bindValue($i+1,$v);
		}
		if( !$this->_statement->execute() )
			WdfDbException::Raise($this->_statement->ErrorOutput(),"\nArguments:",$this->_values,"\nMerged:",ResultSet::MergeSql($this->_ds,$sql, $this->_values));
		
		$res = $this->_statement->fetchAll(PDO::FETCH_CLASS,get_class($this->_object),$ctor_args);
		return $res;
	}

	protected function &__conditionTree()
	{
		return $this->_currentTree;
	}

	protected function __fqFields(&$property)
	{
		if( !$property )
			return;
		if( !is_array($property) )
			$property->__fqFields($this->_knownmodels);
		else
			foreach( $property as &$p )
				if( system_method_exists($p, '__fqFields') )
					$p->__fqFields($this->_knownmodels);
	}

	protected function __generateSql()
	{
		if( count($this->_knownmodels) > 0 )
			$this->__fqFields($this->_where);
		$sql = $this->_where->__generateSql();
		return $sql;
	}

	function where($defaultOperator = "AND")
	{
		$this->_where = new ConditionTree(-1,$defaultOperator);
		$this->_currentTree = $this->_where;
	}

	function andAll()
	{
		$this->__conditionTree()->SetOperator("AND");
	}

	function orAll()
	{
		$this->__conditionTree()->SetOperator("OR");
	}

	function andX($count)
	{
		$this->__conditionTree()->Nest($count,"AND");
	}

	function orX($count)
	{
		$this->__conditionTree()->Nest($count,"OR");
	}
	
	function sql($sql,$args=array())
	{
		$this->__conditionTree()->Add($sql);
		foreach( $args as $v ) $this->_values[] = $v;
	}

	function equal($property,$value,$value_is_sql=false)
	{
		//debug("equal($property,$value)");
		if( $value instanceof ColumnAttribute || $value_is_sql )
			$this->__conditionTree()->Add(new Condition("=",$property,$value));
		else
		{
			$this->__conditionTree()->Add(new Condition("=",$property));
			$this->_values[] = $value;
		}			
	}
	
	function notEqual($property,$value)
	{
		//debug("equal($property,$value)");
		if( $value instanceof ColumnAttribute )
			$this->__conditionTree()->Add(new Condition("!=",$property,$value));
		else
		{
			$this->__conditionTree()->Add(new Condition("!=",$property));
			$this->_values[] = $value;
		}			
	}
	
	function greaterThan($property,$value)
	{
		//debug("equal($property,$value)");
		if( $value instanceof ColumnAttribute )
			$this->__conditionTree()->Add(new Condition(">",$property,$value));
		else
		{
			$this->__conditionTree()->Add(new Condition(">",$property));
			$this->_values[] = $value;
		}			
	}
	
	function greaterThanOrEqualTo($property,$value)
	{
		//debug("equal($property,$value)");
		if( $value instanceof ColumnAttribute )
			$this->__conditionTree()->Add(new Condition(">=",$property,$value));
		else
		{
			$this->__conditionTree()->Add(new Condition(">=",$property));
			$this->_values[] = $value;
		}			
	}
	
	function lowerThan($property,$value)
	{
		//debug("equal($property,$value)");
		if( $value instanceof ColumnAttribute )
			$this->__conditionTree()->Add(new Condition("<",$property,$value));
		else
		{
			$this->__conditionTree()->Add(new Condition("<",$property));
			$this->_values[] = $value;
		}			
	}
	
	function lowerThanOrEqualTo($property,$value)
	{
		//debug("equal($property,$value)");
		if( $value instanceof ColumnAttribute )
			$this->__conditionTree()->Add(new Condition("<=",$property,$value));
		else
		{
			$this->__conditionTree()->Add(new Condition("<=",$property));
			$this->_values[] = $value;
		}			
	}
	
	function binary($property,$value)
	{
		//debug("equal($property,$value)");
		if( $value instanceof ColumnAttribute )
			$this->__conditionTree()->Add(new Condition("=",$property,$value,"BINARY "));
		else
		{
			$this->__conditionTree()->Add(new Condition("=",$property,"?","BINARY "));
			$this->_values[] = $value;
		}			
	}

	function like($property,$value,$flipped=false)
	{
		if( $value instanceof ColumnAttribute )
			$this->__conditionTree()->Add(new Condition("LIKE",$property,$value));
		else
		{
			if( $flipped )
			{
				$this->__conditionTree()->Add(new Condition("LIKE","?",$value));
				$this->_values[] = $property;
			}
			else
			{
				$this->__conditionTree()->Add(new Condition("LIKE",$property));
				$this->_values[] = $value;
			}
		}			
	}

	function rlike($property,$value,$flipped=false)
	{
		if( $value instanceof ColumnAttribute )
			$this->__conditionTree()->Add(new Condition("RLIKE",$property,$value));
		else
		{
			if( $flipped )
			{
				$this->__conditionTree()->Add(new Condition("RLIKE","?",$value));
				$this->_values[] = $property;
			}
			else
			{
				$this->__conditionTree()->Add(new Condition("RLIKE",$property));
				$this->_values[] = $value;
			}
		}
	}

	public function in($property,$values)
	{
		if( count($values) == 0 )
			return;

		if( !is_array($values) )
			$values = array($values);
		$this->__conditionTree()->Add(new Condition("IN",$property,array_fill(0,count($values),"?")));
		foreach( $values as $value )
			$this->_values[] = $value;
	}
	
	public function notIn($property,$values)
	{
		if( count($values) == 0 )
			return;

		if( !is_array($values) )
			$values = array($values);
		$this->__conditionTree()->Add(new Condition("NOT IN",$property,array_fill(0,count($values),"?")));
		foreach( $values as $value )
			$this->_values[] = $value;
	}

	public function isNull($property)
	{
		$this->__conditionTree()->Add(new Condition("IS",$property,"NULL"));
	}

	public function notNull($property)
	{
		$this->__conditionTree()->Add(new Condition("IS NOT",$property,"NULL"));
	}
	
	public function newerThan($property,$value,$interval)
	{
		$this->__conditionTree()->Add(new Condition(">",$property,"NOW() - INTERVAL ? $interval"));
		$this->_values[] = $value;
	}
	
	public function olderThan($property,$value,$interval)
	{
		$this->__conditionTree()->Add(new Condition("<",$property,"NOW() - INTERVAL ? $interval"));
		$this->_values[] = $value;
	}
	
	public function noop()
	{
		$this->__conditionTree()->Add(new Condition("=","?","?"));
		$this->_values[] = 1;
		$this->_values[] = 1;
	}
}

/**
 * @internal Helper class for the SQL query builder <Query>
 */
class ConditionTree
{
	private $_firstToken = "WHERE";
	private $_operator = "AND";
	private $_conditions = array();
	private $_maxConditions = -1;
	private $_current = false;
	private $_parent = false;

	function __construct($conditionCount = -1,$operator = "AND", $firstToken = "WHERE")
	{
		$this->_operator = $operator;
		$this->_maxConditions = $conditionCount;
		$this->_current =& $this;
		$this->_firstToken = $firstToken;
	}

	function __fqFields(&$knownModels)
	{
		foreach( $this->_conditions as &$c )
			if( $c instanceof Condition)
				$c->__fqFields($knownModels);
	}

	function __generateSql()
	{
		if( count($this->_conditions) < 1 )
			return "";

		$sql = array();
		foreach( $this->_conditions as $c )
		{
			if( is_string($c) )
				$s = $c;
			elseif( $c instanceof Condition )
				$s = $c->__toSql();
			else
				$s = $c->__generateSql();
			if( $s )
				$sql[] = $s;
		}
		if( count($sql) == 0 )
			return "";
			
		if( $this->_parent )
			$sql = "(".implode(" {$this->_operator} ",$sql).")";
		else
			$sql = " {$this->_firstToken} ".implode(" {$this->_operator} ",$sql);
		return $sql;
	}

	function __ensureClose()
	{
		if( $this->_current->_maxConditions > -1 &&
			count($this->_current->_conditions) == $this->_current->_maxConditions )
		{
			$this->_current =& $this->_current->_parent;
			$this->_current->__ensureClose();
		}
	}

	function SetOperator($operator)
	{
		$this->Nest(-1,$operator);
		//$this->_current->_operator = $operator;
	}

	function Add($condition)
	{
		$this->_current->_conditions[] = $condition;
		$this->__ensureClose();
	}

	function Nest($conditionCount,$operator = "AND")
	{
		$mem =& $this->_current;
		$this->_current->_conditions[] = new ConditionTree($conditionCount,$operator);
		$this->_current =& $this->_current->_conditions[count($this->_current->_conditions)-1];
		$this->_current->_parent = $mem;
	}
}

/**
 * @internal Helper class for the SQL query builder <Query>
 */
class Condition
{
	private $_operator;
	private $_op1;
	private $_op2;
	private $_pre;
	private $_suf;

	function __construct($operator,$op1,$op2 = "?",$prefix="",$suffix="")
	{
		$this->_operator = " $operator ";
		$this->_op1 = $op1;
		$this->_op2 = $op2;
		$this->_pre = $prefix;
		$this->_suf = $suffix;
	}

	function __toSql()
	{
		if( is_array($this->_op2) )
			return "{$this->_op1}{$this->_operator}(".implode(",",$this->_op2).")";
		return "{$this->_pre}{$this->_op1}{$this->_operator}{$this->_op2}{$this->_suf}";
	}

	function __fqFields(&$knownModels)
	{
		return;
		foreach( $knownModels as &$km )
		{
			if( $this->_op1 != "?" )
			{
				$this->_op1 = $km->FullQualifiedFieldName($this->_op1);
				continue;
			}
			if( $this->_op2 != "?" )
				$this->_op2 = $km->FullQualifiedFieldName($this->_op2);
		}
	}
}
