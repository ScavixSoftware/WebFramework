<?php
/**
 * Scavix Web Development Framework
 *
 * Copyright (c) 2007-2012 PamConsult GmbH
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
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 */

/**
 * Initializes the model essential.
 * 
 * @return void
 */
function model_init()
{
	global $CONFIG;
	
	$CONFIG['class_path']['model'][]   = dirname(__FILE__).'/model/';
	$CONFIG['class_path']['model'][]   = dirname(__FILE__).'/model/driver/';
    
    // trick out the autoloader as it consults the cache which needs a model thus circular...
    require_once(__DIR__.'/model/pdolayer.class.php');
    require_once(__DIR__.'/model/resultset.class.php');
    require_once(__DIR__.'/model/driver/idatabasedriver.class.php');
    require_once(__DIR__.'/model/datasource.class.php');

	$GLOBALS['MODEL_DATABASES'] = array();
	$GLOBALS['MODEL_REGISTER'] = array();

	if( !is_array($CONFIG['model']) )
		WdfDbException::Raise("Please configure at least one DB in CONFIG['model']");

	foreach( $CONFIG['model'] as $name=>$mod )
	{
		if( !is_array($mod) )
			continue;

		if( isset($mod['connection_string']) )
		{
			model_init_db(
				$name,
				$mod['connection_string'],
				isset($mod['datasource_type'])?$mod['datasource_type']:"DataSource"
			);
		}
		else
			WdfDbException::Raise("Unable to initialize database '$name'! Missing CONFIG information.");
	}
}

/**
 * Initializes a database connection.
 * 
 * @param string $name Alias name (like system, internal, data, mydb,...)
 * @param string $constr Connection string
 * @param string $dstype Datasource type
 * @return void
 */
function model_init_db($name,$constr,$dstype='DataSource')
{
	global $MODEL_DATABASES;
	
	$MODEL_DATABASES[$name] = array($dstype,$constr);
}

/**
 * @internal Stores all connections states
 */
function model_store()
{
	global $MODEL_DATABASES;
	foreach( $MODEL_DATABASES as $dbname=>$db )
		if( !is_array($db) )
			store_object($db,$dbname);
}

/**
 * Get a database connection.
 * 
 * @param string $name The datasource alias.
 * @return DataSource The database connection
 */
function &model_datasource($name)
{
	global $MODEL_DATABASES;

	if( strpos($name,"DataSource::") !== false )
	{
		$name = explode("::",$name);
		$name = $name[1];
	}

	if( !isset($MODEL_DATABASES[$name]) )
	{
		if( function_exists('model_on_unknown_datasource') )
		{
			$res = model_on_unknown_datasource($name);
			return $res;
		}
		log_fatal("Unknown datasource '$name'!");
		$res = null;
		return $res;
	}

	if( is_array($MODEL_DATABASES[$name]) )
	{
		list($dstype,$constr) = $MODEL_DATABASES[$name];
		$model_db = new $dstype($name,$constr);
		if( !$model_db )
			WdfDbException::Raise("Unable to connect to database '$name'.");
		$MODEL_DATABASES[$name] = $model_db;
	}

	return $MODEL_DATABASES[$name];
}

/**
 * Get the name/alias of a given DataSource.
 * 
 * @param DataSource $ds The datasource
 * @return string the name/alias
 */
function model_datasource_name(&$ds)
{
	return $ds->_storage_id;
}

/**
 * Creates a valid connection string.
 * 
 * @param string $type Shouls be 'DataSource'
 * @param string $server The Db server
 * @param string $username The DB username
 * @param string $password The DB password
 * @param string $database The database name
 * @return string A valid connection string
 */
function model_build_connection_string($type,$server,$username,$password,$database)
{
	return sprintf("%s://%s:%s@%s/%s",$type,$username,$password,$server,$database);
}
