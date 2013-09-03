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

/**
 * Interface for database drivers.
 * 
 * Although ScavixWDF uses <PDO> each database has it's specialities so we need a driver interface.
 * The actual database drivers must imeplement this to be compatible with ScavixWDF.
 */
interface IDatabaseDriver
{
	/**
	 * Initializes the driver.
	 * 
	 * @param DataSource $datasource The datasource object using this driver (it is called $this over there)
	 * @param PDOPdoLayer $pdo The PDo database connection
	 * @return void
	 */
	function initDriver($datasource,$pdo);
	
	/**
	 * Returns the schema of a table.
	 * 
	 * @param string $tablename The tables name
	 * @return TableSchema The tables schema
	 */
    function &getTableSchema($tablename);

	/**
	 * Returns a list of all tables.
	 * 
	 * @return array List of tables
	 */
	function listTables();
	
	/**
	 * Returns a list of all column names in a table.
	 * 
	 * @param string $tablename Name of table to query
	 * @return array Array of column names
	 */
	function listColumns($tablename);

	/**
	 * Checks if a table exists.
	 * 
	 * @param string $tablename name of table to check for
	 * @return bool true or false
	 */
	function tableExists($tablename);
	
	/**
	 * Create a table from an objects schema.
	 * 
	 * We are not sure if we will ever implement this side as we prefer to create our tables in the database.
	 * @param TableSchema $objSchema Tableschema as specified by the Model
	 * @return void
	 */
	function createTable($objSchema);

	/**
	 * Creates a valid save statement to store an object into the database.
	 * 
	 * @param Model $model The model to store
	 * @param array $args <b>OUT:</b>The values extracted from $model that have changed wince last save (or all if new object)
	 * @param array $columns_to_update If given only these fields will be updated. If not Model tries to detect changed columns automatically.
	 * @return string SQL statement with argument placeholders for all change columns (see $args)
	 */
	function getSaveStatement($model,&$args,$columns_to_update=false);
	
	/**
	 * Creates a valid statement to delete the given model from the database.
	 * 
	 * @param Model $model Model to delete
	 * @param type $args <b>OUT:</b>The values extracted from $model to uniquely identify it (the primary key values)
	 * @return string SQL statement with argument placeholders to identify $model (see $args)
	 */
	function getDeleteStatement($model,&$args);
	
	/**
	 * Create a pages statement from an unpaged one.
	 * 
	 * @param string $sql The original statement
	 * @param int $page The one-based page index
	 * @param int $items_per_page Items per page
	 * @return string Paged SQL statement
	 */
	function getPagedStatement($sql,$page,$items_per_page);
	
	/**
	 * Returns paging informaton about a query.
	 * 
	 * @param string $sql SQL statement to get information for
	 * @param array $input_arguments Input parameters for the SQL statement
	 * @return array An array containing: rows_per_page, current_page, total_pages, total_rows, offset
	 */
	function getPagingInfo($sql,$input_arguments=null);
    
	/**
	 * Returns an SQL query to get the current datetime.
	 * 
	 * @param int $seconds_to_add Offset to now in seconds (can also be negative)
	 * @return string Query to get now from the Db
	 */
    function Now($seconds_to_add=0);
    
	/**
	 * Preprocesses an SQL statement.
	 * 
	 * Some drivers need to perform some extra parameter specifier processing or stuff.
	 * Can be implemented here as this will be called with every query.
	 * @param string $sql RAW SQL statement
	 * @return string Statement valid for this drivers database
	 */
    function PreprocessSql($sql);
}
