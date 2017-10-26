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

use DateTime;
use MC_Google_Visualization;
use PDO;
use ScavixWDF\ICallable;
use ScavixWDF\Localization\CultureInfo;
use ScavixWDF\Model\DataSource;

/**
 * Base class for google visualization controls.
 * 
 */
abstract class GoogleVisualization extends GoogleControl implements ICallable
{
	public static $UseMaterialDesign = false;
	public static $DefaultDatasource = false;
	public static $Colors = false;
	
	var $_columnDef = false;
	var $_data = array();
	var $_rowCallbacks = array();
	var $_roleCallbacks = array();
	
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
			$res->opt('title',$title);
		if( self::$Colors )
			$res->opt('colors',self::$Colors);		
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
		
		$this->_ds = $ds?$ds:(self::$DefaultDatasource?self::$DefaultDatasource:DataSource::Get());
		
        $this->gvOptions = ['tooltip' => ['isHtml' => true]];
        
		$this->gvType = $type?$type:substr(get_class_simple($this),2);
		$this->gvOptions = $options?array_merge($this->gvOptions,$options):$this->gvOptions;
		$this->gvQuery = $query;
		
		$this->content("<div class='loading'>&nbsp;</div>");
		store_object($this);
	}
	
	private function _applyRowCallbacks($row)
	{
		foreach( $this->_rowCallbacks as $rcb )
			$row = $rcb($row);
		return $row;
	}
	
	/**
	 * @override
	 */
	function PreRender($args = array())
	{
		$this->_data = array_values_rec($this->_data,2);
		
		if( count($this->_data)>1 || $this->_columnDef )
		{
			$id = $this->id; $d = "d$id"; $c = "c$id";
            if(isset($this->gvOptions['isStacked']) && isset($this->gvOptions['colors']) && is_array($this->gvOptions['colors']) && (count($this->gvOptions['colors']) > 1))
                $opts = json_encode($this->gvOptions, JSON_FORCE_OBJECT);
            else
                $opts = json_encode($this->gvOptions);
            $coldefs = false;
            if($this->_columnDef)
                $coldefs = array_values($this->_columnDef);

			array_walk_recursive($this->_data, function(&$item, &$key) use ($coldefs) { 
                //log_debug($key, $item);
                if( $item instanceof DateTime) 
                    $item = "[jscode]new Date(".($item->getTimestamp()*1000).")";
                elseif($coldefs)
                {
                    if((count($coldefs) >= $key) && isset($coldefs[$key]) && isset($coldefs[$key][1]))
                    {
//                        log_debug($key, $item, $coldefs[$key][1]);
                        switch($coldefs[$key][1])
                        {
                            case 'date':
                                $stime = strtotime($item.' 00:00:00');
                                if(date('Y-m-d', $stime) == $item)
                                    $item = ['v' => "[jscode]new Date(".($stime * 1000).")", 'f' => ($this->_culture ? $this->_culture->FormatDate($item) : $item)];
                                break;
                        }
                    }
                }
            });
            
			$data = system_to_json($this->_data);
			if( self::$UseMaterialDesign && in_array($this->gvType, array('Bar', 'Column')))
			{
				$js = "var $d=google.visualization.arrayToDataTable($data);\n"
					. "var $c=new google.charts.Bar($('#$id').get(0));\n"
					. "google.visualization.events.addListener($c, 'ready', function(){ $('#$id').data('ready',true); });\n"
					. "$c.draw($d,google.charts.{$this->gvType}.convertOptions($opts));\n"
					. "$('#$id').data('googlechart', $c).data('chartdata',$d).data('chartoptions',google.charts.{$this->gvType}.convertOptions($opts));";
			}
			else
			{
				$js = "var $d=google.visualization.arrayToDataTable($data);\n"
					. "var $c=new google.visualization.{$this->gvType}($('#$id').get(0));\n"
					. "google.visualization.events.addListener($c, 'ready', function(){ $('#$id').data('ready',true); });\n"
					. "$c.draw($d,$opts);\n"
					. "$('#$id').data('googlechart', $c).data('chartdata',$d).data('chartoptions',$opts);";
			}
			$this->_addLoadCallback('visualization', $js, true);
		}
		else
		{
			$t = $this->opt('title');
			$this->css('text-align','center')
				->content( ($t?"<b>$t:</b> ":"").tds("TXT_NO_DATA", "No data found") , true);
		}
		if( isset($this->gvOptions['width']) )
			$this->css('width', is_numeric($this->gvOptions['width'])?"{$this->gvOptions['width']}px":"{$this->gvOptions['width']}");
		if( isset($this->gvOptions['height']) )
			$this->css('height',is_numeric($this->gvOptions['height'])?"{$this->gvOptions['height']}px":"{$this->gvOptions['height']}");
		
		return parent::PreRender($args);
	}
	
	protected function _loadPackage($package)
	{
		if( isset(self::$_apis['visualization']) )
		{
			if( !in_array($package, self::$_apis['visualization'][1]['packages']) )
				self::$_apis['visualization'][1]['packages'][] = $package;
		}
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
		return $this->opt('width',$width)->opt('height',$height);
	}
	
	/**
	 * @shortcut <GoogleVisualization::opt>('title',$title);
	 */
	function setTitle($title)
	{
		return $this->opt('title',$title);
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
	 * Sets up an SQL query (optionally with arguments) as data for this visualization.
	 * 
	 * @param string $sql The SQL statement
	 * @param array $args Optional arguments
	 * @param mixed $datasource Optional <DataSource> to be used
	 * @return GoogleVisualization `$this`
	 */
	function setSqlQuery($sql,$args=array(),$datasource=false)
	{
		if( $datasource )
			$this->setDataSource($datasource);
		
		return $this->setResultSet($this->_ds->ExecuteSql($sql,$args));
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
		$args = func_get_args();
		if( count($args)==1 && is_array($args[0]) )
			$args = array_shift($args);
		$this->_data = array($this->_applyRowCallbacks($args));
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
		$args = func_get_args();
		if( count($args)==1 && is_array($args[0]) )
			$args = array_shift($args);
		$this->_data[] = $this->_applyRowCallbacks($args);
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
		
		foreach( $rows as $i=>$r )
			$rows[$i] = $this->_applyRowCallbacks($r);
		
		if( count($this->_data)>0 )
			$this->_data = array_merge(array($this->_data[0]),$rows);
		else
			$this->_data = $rows;
		return $this;
	}
	
	/**
	 * Interconnects two visualizations 'select' events.
	 * 
	 * @param GoogleVisualization $other_vis The other visualization
	 * @return GoogleVisualization `$this`
	 */
	function linkSelect($other_vis)
	{
		$js = "google.visualization.events.addListener($('#{$this->id}').data('googlechart'), 'select', function(){ $('#{$other_vis->id}').data('googlechart').setSelection($('#{$this->id}').data('googlechart').getSelection()); });";
		$this->_addLoadCallback('visualization', $js);
		return $this;
	}
	
	/**
	 * Manually adds a column specification to the visualization.
	 * 
	 * @param string $name Column name
	 * @param string $label Column label
	 * @param string $type Type of values
	 * @return GoogleVisualization `$this`
	 */
	function addColumn($name,$label=false,$type=false,$style=false)
	{
		if( isset(self::$Colors[$name]) )
		{
			$cols = force_array($this->opt('colors'));
			$cols[] = $style = self::$Colors[$name];
			$this->opt('colors',$cols);			
		}
		$this->_columnDef[$label] = array($name,$type,$style);
		return $this;
	}
	
	/**
	 * Adds a callback method that will be called for each added data row.
	 * 
	 * @param Closure $callback Method to be called
	 * @return GoogleVisualization `$this`
	 */
	function addRowCallback($callback)
	{
		$this->_rowCallbacks[] = $callback;
		return $this;
	}
	
	/**
	 * Adds a role to the last added column.
	 * 
	 * A role consists of a name and a callback that will be polled for each column in each data row.
	 * The callback must return the value for the column role.
	 * Note that this is only implementend for role 'annotation'.
	 * 
	 * See https://developers.google.com/chart/interactive/docs/roles
	 * 
	 * @param string $role Role specifier
	 * @param Closure $callback Callback function
	 * @return GoogleVisualization `$this`
	 */
	function addColumnRole($role,$callback = false)
	{
		$key = "{$role}_".count($this->_roleCallbacks);
		$this->_columnDef[$key] = $role;
		if($callback !== false)
			$this->_roleCallbacks[$key] = array($role,$callback);
		return $this;
	}
	
	private function getTypedValue($v,$type)
	{
		$ci = $this->_culture;
		switch( $type )
		{
			case 'int': 
			case 'integer': 
				$v = intval($v); 
				break;
			case 'float': 
			case 'double': 
			case 'number': 
				$v = floatval($v);
                if( $ci )
                    $v = array('v'=>$v,'f'=>$ci->FormatNumber($v,false,true)); 
				break;
			case 'currency': 
				$v = floatval($v);
				if( $ci )
					$v = array('v'=>$v,'f'=>$ci->FormatCurrency($v,true));
				break;
			case 'date':
                if(strtotime($v))
                {
                    $v = new DateTime($v);
                    if( $ci )
                        $v = array('v'=>$v,'f'=>$ci->FormatDate($v));
                }
				break;
			case 'time': 
				$v = new DateTime($v);
				if( $ci )
					$v = array('v'=>$v,'f'=>$ci->FormatTime($v));
				break;
			case 'datetime': 
				$v = new DateTime($v);
				if( $ci )
					$v = array('v'=>$v,'f'=>$ci->FormatDateTime($v));
				break;
			case 'timeofday': 
				$v = explode(':',$v);
				break;
			case 'duration': 
                $v = floatval($v);
                $h = floor($v);
                $m = ($v - $h) * 60;
                $v = array('v'=>$v,'f'=>sprintf("%d:%02d",$h,$m));
				break;
		}
		return $v;
	}
	
	/**
	 * Adds a <ResultSet> as data for this visualization.
	 * 
	 * The set may contain any column but it must contain all columns defined
	 * thru <GoogleVisualization::addColumn> or <GoogleVisualization::addColumnRole>.
	 * 
	 * @param type $rs <ResultSet> with data.
	 * @return GoogleVisualization `$this`
	 */
	function setResultSet($rs)
	{
		$head = array();
		foreach( $this->_columnDef as $key=>$def )
		{
			if( isset($this->_roleCallbacks[$key]) )
				$head[] = array('role'=>$def);
			else
				$head[] = $key;
		}
		$this->_data = array($head);
		foreach( $rs->results() as $row )
		{
			$d = array();
			foreach( $this->_columnDef as $key=>$def )
			{
				list($name,$type) = $def;
				if( isset($this->_roleCallbacks[$key]) )
				{
					list($role,$callback) = $this->_roleCallbacks[$key];
					$d[$key] = $callback($role,$d,$row);
					continue;
				}
				if( !isset($row[$name]) )
					$row[$name] = "";
				$d[$name] = $this->getTypedValue($row[$name],$type);
			}
			$this->_data[] = $this->_applyRowCallbacks($d);
		}
		return $this;
	}
	
	function setMultiSeriesResultSet($rs,$xAxisCol,$newColSpecifier,$newColValue,$newcolformat = 'number')
	{
		$results = $rs->results();
		
		$xAxisColDef = $xAxisCol;
		if( !isset($this->_columnDef[$xAxisCol]) )
		{
			$found = false;
            if( is_array($this->_columnDef) )
            {
                foreach( $this->_columnDef as $key=>$def )
                {
                    list($name,$type) = $def;
                    if( $name == $xAxisCol )
                    {
                        $xAxisColDef = $key;
                        $found = true;
                        break;
                    }
                }
            }
			if( !$found )
				$this->addColumn($xAxisCol,$xAxisCol,'string');
		}
		foreach( $results as $row )
		{
			$key = $row[$newColSpecifier];
			if( isset($this->_columnDef[$key]) )
				continue;
			$this->addColumn($key,$key,$newcolformat);
		}
		
		$head = array();
		foreach( $this->_columnDef as $key=>$def )
		{
			if( isset($this->_roleCallbacks[$key]) )
				$head[] = array('role'=>$def);
			else
				$head[] = "$key";
		}
		$this->_data = array($head);

		foreach( $results as $row )
		{
			$xVal = $row[$xAxisCol];
			if( !isset($this->_data[$xVal]) )
			{
				$this->_data[$xVal] = array_combine(array_keys($this->_columnDef), array_fill(0,count($this->_columnDef),0));
				$this->_data[$xVal][$xAxisColDef] = $xVal;
			}
			$this->_data[$xVal][$row[$newColSpecifier]] = $this->getTypedValue($row[$newColValue],$newcolformat);
		}
		return $this;
	}
    
    function makeContinousDateAxis($format='Y-m-d')
    {
        $keys = array_keys($this->_data);
        array_shift($keys); // shift off column definition

        $start = \ScavixWDF\Base\DateTimeEx::Make(array_shift($keys));
        $end = \ScavixWDF\Base\DateTimeEx::Make(array_pop($keys));
        $null = array_combine(array_keys($this->_columnDef), array_fill(0,count($this->_columnDef),0));
        
        $first = $this->_data[0][0]; // get the key of the first column
        
        if( $start > $end )
        {
            $reverse = $start;
            $start = $end;
            $end = $reverse;
        }
        
        $res = [];
        while( $start < $end )
        {
            $now = $start->format($format);
            if( isset($this->_data[$now]) )
                $res[$now] = $this->_data[$now];
            else
            {
                $res[$now] = $null;
                $res[$now][$first] = $now; // first column is the _data key
            }
            $start = $start->Offset(1,'day');
        }
        
        // preserve column definition in the first place
        if( isset($reverse) )
            $this->_data = array_merge([0=>$this->_data[0]],array_reverse($res));
        else
            $this->_data = array_merge([0=>$this->_data[0]],$res);
        
        return $this;
    }
}
