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

/**
 * @internal SQL SELECT query builder
 */
class SelectQuery extends Query
{
	var $_groupBy = array();
	var $_having = false;
	var $_orderBy = array();
	var $_limit = array();
	var $_join = array();

	function __construct(&$obj=null,&$datasource=null,$select_statement=false)
	{
		parent::__construct($obj,$datasource,$select_statement?"":"WHERE");
		if( !unserializer_active() )
		{
			if( !$select_statement )
				$this->_initialSequence = "SELECT * FROM `{$obj->GetTableName()}`";
			else
				$this->_initialSequence = "";
		}
	}

	function setResultFields($fields_as_array_or_commaseparated)
	{
		$cols = is_array($fields_as_array_or_commaseparated)
			?implode(",",$fields_as_array_or_commaseparated)
			:$fields_as_array_or_commaseparated;
		$this->_initialSequence = str_replace("*",$cols,$this->_initialSequence);
	}

	protected function __generateSql()
	{
		if( count($this->_knownmodels) > 0 )
		{
			$this->__fqFields($this->_groupBy);
			$this->__fqFields($this->_orderBy);
			$this->__fqFields($this->_join);
			if( $this->_having )
				$this->__fqFields($this->_having);
		}

		$sql = parent::__generateSql();
	
		if( count($this->_join) > 0 )
		{
			//debug($this->_join);
			$tmp = array();
			foreach( $this->_join as $j )
				$tmp[] = $j->__generateSql();
			$sql = implode(" ",$tmp)." ".$sql;
		}

		if( count($this->_groupBy) > 0 )
		{
			$tmp = array();
			foreach( $this->_groupBy as $g )
				$tmp[] = "$g";

			$sql .= " GROUP BY ".implode(",",$tmp);
		}
		
		if( $this->_having instanceof ConditionTree )
			$sql .= $this->_having->__generateSql();

		if( count($this->_orderBy) > 0 )
		{
			$tmp = array();
			foreach( $this->_orderBy as $k=>$d )
				if( $k != '{SPECIAL}' )
					$tmp[] = "$k $d";
				else
					$tmp[] = "$d";

			$sql .= " ORDER BY ".implode(",",$tmp);
		}

		if( count($this->_limit) > 0 )
		{
			$sql .= " LIMIT {$this->_limit[0]},{$this->_limit[1]}";
		}

		return $sql;
	}

	function groupBy($property)
	{
		$this->_groupBy[] = $property;
	}

	function having($defaultOperator = "AND")
	{
		$this->_having = new ConditionTree(-1,$defaultOperator,"HAVING");
		$this->_currentTree = $this->_having;
	}

	function orderBy($property,$direction)
	{
		$this->_orderBy[$property] = $direction;
	}

	function limit($offset,$limit)
	{
		$this->_limit = array($offset,$limit);
	}

	/**
	 * Join two database tables
	 * @param string $direction E.g. 'LEFT', 'RIGHT' or 'FULL'. Also 'LEFT OUTER'.
	 * @param object $model An instance of a Model subclass.
	 */
	function join($direction,$model)
	{
		$direction = strtoupper($direction);
		$this->_join[] = new ConditionTree(-1,"AND","$direction JOIN `{$model->GetTableName()}` ON");
		$this->_currentTree = $this->_join[count($this->_join)-1];
		$this->_knownmodels[] = $model;
	}
}
