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
 * Wrapper for anonymous chained database queries
 * 
 * Sometimes you will be too lazy to create a model class for each of your database tables.
 * Or perhaps there are some cross-tables that is not needed for, but you need to query them.
 * So this is for you:
 * <code php>
 * $dataSource->Query('my_db_table')->all();
 * </code>
 * Will create a CommonModel and let you perform all chaining methods on it as if it was a real typed model.
 */
class CommonModel extends Model
{
	var $_tableName = false;
	
	/**
	 * Returns the table name
	 * 
	 * In CommonModel this is what you wanted to query.
	 * @return string Table name
	 */
	function GetTableName()
	{
		return $this->_tableName;
	}
	
	function __construct($datasource=null, $tablename=null)
    {
		if( $tablename )
		{
			$this->_tableName = $tablename;
			if( $datasource )
				$this->_cacheKey = $datasource->Database().$this->_tableName;
		}
		parent::__construct($datasource);
	}
	
	protected function __ensureResults($ctor_args=null)
	{
		if( !$ctor_args )
			$ctor_args = array($this->_ds,$this->_tableName);
		return parent::__ensureResults($ctor_args);
	}
	
	public function Convert($className)
	{
		if( $this->IsRow() )
			return Model::CastFrom($this,true,$className);
		$res = array();
		foreach( $this->results() as $obj )
			$res[] = Model::CastFrom($obj,true,$className);
		return $res;
	}
}