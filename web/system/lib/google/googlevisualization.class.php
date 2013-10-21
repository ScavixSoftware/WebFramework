<?php
/**
 * Scavix Web Development Framework
 *
 * Copyright (c) since 2012 Scavix Software Ltd. & Co. KG
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
 * @copyright since 2012 Scavix Software Ltd. & Co. KG
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 */
namespace ScavixWDF\Google;

use MC_Google_Visualization;
use PDO;
use ScavixWDF\ICallable;
use ScavixWDF\Model\DataSource;

/**
 * Base class for google visualization controls.
 * 
 */
abstract class GoogleVisualization extends GoogleControl implements ICallable
{
	public static $DefaultDatasource = false;
	
	var $_data = array();
	
	var $_entities = array();
	var $_ds;
	
	var $gvType;
	var $gvOptions;
	var $gvQuery;
	
	/**
	 * Static creator function.
	 * 
	 * @param string $title Title string
	 * @return GoogleVisualization Created control
	 */
	static function Make($title=false)
	{
		$className = get_called_class();
		$res = new $className();
		if( $title )
			return $res->opt('title',$title);
		return $res;
	}
	
	/**
	 * @param string $type Type of google visualization
	 * @param array $options Options. Depends on $type
	 * @param string $query A valid google query string. See [queryobjects](https://developers.google.com/chart/interactive/docs/reference#queryobjects)
	 * @param DataSource $ds DataSource to use, will fall back to GoogleVisualization::$DefaultDatasource or (if that is not set) to <model_datasource>('internal')
	 */
	function __initialize($type=false,$options=array(),$query=false,$ds=false)
	{
		parent::__initialize();
		$this->addClass('google_vis');
		
		$this->_ds = $ds?$ds:(self::$DefaultDatasource?self::$DefaultDatasource:model_datasource('internal'));
		
		$this->gvType = $type?$type:substr(get_class_simple($this),2);
		$this->gvOptions = $options?$options:array();
		$this->gvQuery = $query;
		
		$this->content("<div class='loading'>&nbsp;</div>");
		store_object($this);
	}
	
	/**
	 * @override
	 */
	function PreRender($args = array())
	{
		$id = $this->id;
		$opts = json_encode($this->gvOptions);
		if( count($this->_data)>0 )
		{
			$d = system_to_json($this->_data);
			$init = "var d=google.visualization.arrayToDataTable($d); var c=new google.visualization.{$this->gvType}($('#$id').get(0));c.draw(d,$opts);";
		}
		else
		{
			$q = buildQuery($this->id,'Query');
			$init = "var $id = new google.visualization.Query('$q');$id.setQuery('{$this->gvQuery}');$id.send(function(r){ if(r.isError()){ $('#$id').html(r.getDetailedMessage()); }else{ var c=new google.visualization.{$this->gvType}($('#$id').get(0));c.draw(r.getDataTable(),$opts);}});";
		}
		$this->_addLoadCallback('visualization', $init);
		
		if( isset($this->gvOptions['width']) )
			$this->css('width',"{$this->gvOptions['width']}px");
		if( isset($this->gvOptions['height']) )
			$this->css('height',"{$this->gvOptions['height']}px");
		
		return parent::PreRender($args);
	}
	
	protected function _loadPackage($package)
	{
		if( isset(self::$_apis['visualization']) )
			self::$_apis['visualization'][1]['packages'][] = $package;
		else
			parent::_loadApi('visualization','1',array('packages'=>array($package)));
	}
	
	protected function _createMC($ds)
	{
		$paths = explode(PATH_SEPARATOR,ini_get('include_path'));
		$paths[] = __DIR__;
		array_unique($paths);
		ini_set('include_path',implode(PATH_SEPARATOR,$paths));
		require_once('MC/Google/Visualization.php');
		return new MC_Google_Visualization( 
				new PDO($ds->GetDsn(),$ds->Username(),$ds->Password() ), 
				strtolower(array_pop(explode("\\",get_class($ds->Driver))))
			);
	}
	
	protected function _dbTypeToGType($db_type)
	{
		switch( strtolower($db_type) )
		{
			case 'int':
			case 'integer':
				return 'number';
			case 'date':
				return 'date';
			case 'datetime':
				return 'datetime';
		}
		return 'text';
	}
	
	/**
	 * @internal AJAX callback for google queries.
	 * 
	 * See https://developers.google.com/chart/interactive/docs/reference#queryobjects
	 */
	function Query()
	{
		log_debug("{$this->id}->Query()",$_REQUEST,$this);
		$mc = $this->_createMC($this->_ds);
		foreach( $this->_entities as $name=>$spec )
		{
			$mc->addEntity($name, $spec);
			if( !isset($d) ){ $mc->setDefaultEntity($name); $d=true; }
		}
		$mc->handleRequest();
		die("");
	}
	
	/**
	 * Sets an option.
	 * 
	 * Valid options vary for the different visualizations.
	 * @param string $name Option name
	 * @param mixed $value OPtion value
	 * @return GoogleVisualization `$this`
	 */
	function opt($name,$value=null)
	{
		if( is_null($value) )
			return isset($this->gvOptions[$name])?$this->gvOptions[$name]:null;
		$this->gvOptions[$name] = $value;
		return $this;
	}
	
	/**
	 * @shortcut <GoogleVisualization::opt>('width',$width)-&gt;<GoogleVisualization::opt>('height',$height)
	 */
	function setSize($width,$height)
	{
		return $this->opt('width',intval($width))->opt('height',intval($height));
	}
	
	/**
	 * Sets the <DataSource> to be used
	 * 
	 * @param mixed $datasource Optional <DataSource> to use. This may also be the name of the <DataSource> to use as `string`.
	 * @return GoogleVisualization `$this`
	 */
	function setDataSource($datasource)
	{
		if( is_string($datasource) )
			$this->_ds = model_datasource($datasource);
		elseif( $datasource instanceof DataSource )
			$this->_ds = $datasource;
		return $this;
	}
	
	/**
	 * Sets up a google query from a database table.
	 * 
	 * See https://developers.google.com/chart/interactive/docs/reference#queryobjects
	 * Calling this will set the <GoogleVisualization> in database mode thus clearing all inline data set with 
	 * <GoogleVisualization::setDataHeader> and <GoogleVisualization::addDataRow>
	 * @param string $table_name Table name
	 * @param mixed $query The [goolge query](https://google-developers.appspot.com/chart/interactive/docs/querylanguage)
	 * @param DataSource $datasource Optional <DataSource> to use. This may also be the name of the <DataSource> to use as `string`.
	 * @return GoogleVisualization `$this`
	 */
	function setDbQuery($table_name,$query,$datasource=false)
	{
		if( $datasource )
			$this->setDataSource($datasource);
		$this->EntityFromTable($table_name);
		$this->gvQuery = $query;
		return $this;
	}
	
	/**
	 * Creates a google query entity from a database table.
	 * 
	 * See https://developers.google.com/chart/interactive/docs/reference#queryobjects
	 * @param string $table_name Table name
	 * @param string $alias Alias name this can be referenced as
	 * @return GoogleVisualization `$this`
	 */
	function EntityFromTable($table_name, $alias=false)
	{
		$schema = $this->_ds->Driver->getTableSchema($table_name);
//		log_debug($schema);
		$entity = array(
			'table' => $schema->Name,
			'fields' => array()
		);
		foreach( $schema->Columns as $col )
			$entity['fields'][$col->Name] = array(
				'field' => $col->Name,
				'type' => $this->_dbTypeToGType($col->Type),
			);
		
		$this->_entities[$alias?$alias:$table_name] = $entity;
		$this->_data = array();
		return $this;
	}
	
	/**
	 * Sets the data header.
	 * 
	 * Calling this will set this into inline mdoe thus removing all database related settings (<GoogleVisualization::setDbQuery>).
	 * @return GoogleVisualization `$this`
	 */
	function setDataHeader()
	{
		$this->_entities = array(); $this->gvQuery = false;
		$this->_data = array(func_get_args());
		return $this;
	}
	
	/**
	 * Adds a data row.
	 * 
	 * If you did not yet specify a header this row will be used as it.
	 * Calling this will set this into inline mdoe thus removing all database related settings (<GoogleVisualization::setDbQuery>).
	 * @return GoogleVisualization `$this`
	 */
	function addDataRow()
	{
		$this->_entities = array(); $this->gvQuery = false;
		$this->_data[] = func_get_args();
		return $this;
	}
	
	/**
	 * Sets all data rows.
	 * 
	 * If you did not yet specify a header first row will be used as it.
	 * Calling this will set this into inline mdoe thus removing all database related settings (<GoogleVisualization::setDbQuery>).
	 * @param array $rows Two-dimensional array containing all the rows data
	 * @return GoogleVisualization `$this`
	 */
	function setDataRows($rows)
	{
		$this->_entities = array(); $this->gvQuery = false;
		if( count($this->_data)>0 )
			$this->_data = array_merge(array($this->_data[0]),$rows);
		else
			$this->_data = $rows;
		return $this;
	}
}
