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
namespace ScavixWDF\Controls\Table;

use ExcelCulture;
use PDO;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_IOFactory;
use ScavixWDF\ICallable;
use ScavixWDF\Localization\CultureInfo;
use ScavixWDF\Model\DataSource;

default_string("TXT_NO_DATA_FOUND","no data found");

/**
 * Allows to easily integrate database tables into UI.
 * 
 */
class DatabaseTable extends Table implements ICallable
{
	const PB_NOPROCESSING = 0x00;
	const PB_STRIPHTML = 0x01;
	const PB_HTMLSPECIALCHARS = 0x02;

	var $DataSource = false;
	var $ResultSet = false;
	var $DataTable = false;
	var $Sql = false;
	var $CacheExecute = false;

	var $Columns = false;
	var $Join = false;
	var $Where = false;
	var $GroupBy = false;
	var $Having = false;
	var $OrderBy = false;
	var $Limit = false;

	var $OnAddHeader = false;
	var $OnAddRow = false;
	var $ExecuteSqlHandler = false;

	public $noDataAsRow = false;
	public $contentNoData = "TXT_NO_DATA_FOUND";

	var $ParsingBehaviour = self::PB_HTMLSPECIALCHARS;
    
    var $SlimSerialization = false;

	/**
	 * @param DataSource $datasource DataSource to use
	 * @param string $datatype Datatype to be rendered
	 * @param string $datatable Data tyble to be rendered
	 */
	function __initialize($datasource,$datatype=false,$datatable=false)
	{
		parent::__initialize();
		$this->DataSource = $datasource;
		
		if( $datatype )
			$this->DataTable = $this->DataSource->TableForType($datatype);
		elseif( $datatable )
			$this->DataTable = $datatable;
		
		store_object($this);
	}
    
    function __sleep()
    {
        $res = get_object_vars($this);
        if( $this->SlimSerialization )
        {
            unset($res['_content']);
            unset($res['current_row_group']);
            unset($res['current_row']);
        }
        return array_keys($res);
    }
    
	private function ExecuteSql($sql,$prms=array())
	{
        if( $this->logIfSlow )
            $logtimer = start_timer("[".\ScavixWDF\Model\ResultSet::MergeSql($this->DataSource,$sql,$prms)."]");
        
		if( $this->ExecuteSqlHandler )
			call_user_func($this->ExecuteSqlHandler,$this,$sql,$prms);
		else
		{
			if( $this->ItemsPerPage )
            {
				$this->ResultSet = $this->DataSource->PageExecute($sql,$this->ItemsPerPage,$this->CurrentPage,$prms);
                if(($this->ResultSet->Count() == 0) && ($this->CurrentPage > 1))
                {
                    // no items on current page, so reset to first page
                    $this->ResetPager();
                    $this->ResultSet = $this->DataSource->PageExecute($sql,$this->ItemsPerPage,$this->CurrentPage,$prms);
                }
            }
			else
			{
				if( $this->CacheExecute )
					$this->ResultSet = $this->DataSource->CacheExecuteSql($sql,$prms);
				else
					$this->ResultSet = $this->DataSource->ExecuteSql($sql,$prms);
			}
		}
		if( $this->DataSource->ErrorMsg() )
			log_error(get_class($this).": ".$this->DataSource->ErrorMsg());
        elseif( isset($logtimer) )
            finish_timer($logtimer,$this->logIfSlow);
	}

	/**
	 * @override
	 */
	function Clear()
	{
		$this->ResultSet = false;
		return parent::Clear();
	}

	/**
	 * @internal Builds the SQL query and executed it
	 */
	final function GetData()
	{
		if( !$this->Sql )
		{
			if( !$this->Columns )
				$this->Columns = $this->GetColumns();

			if( !$this->Join )
				$this->Join = $this->GetJoin();
            
			if( !$this->Where )
				$this->Where = $this->GetWhere();

			if( !$this->GroupBy )
				$this->GroupBy = $this->GetGroupBy();

			if( !$this->Having )
				$this->Having = $this->GetHaving();

			if( !$this->OrderBy )
				$this->OrderBy = $this->GetOrderBy();

			if( !$this->Limit )
				$this->Limit = $this->GetLimit();

			if( is_array($this->Columns) )
			{
				foreach( $this->Columns as $k=>$v )
					if( !preg_match('/[^a-zA-Z0-9]/',$v) )
						$this->Columns[$k] = "`$v`";
			}

			$this->Columns = is_array($this->Columns)?implode(",",$this->Columns):$this->Columns;
			$this->Join = $this->Join?$this->Join:"";
			$this->Where = $this->Where?$this->Where:"";
			$this->GroupBy = $this->GroupBy?$this->GroupBy:"";
			$this->OrderBy = $this->OrderBy?$this->OrderBy:"";

            if( $this->Where && !preg_match('/^\s+WHERE\s+/',$this->Where) ) $this->Where = " WHERE ".$this->Where;
			if( $this->Join && !preg_match('/^(LEFT|INNER|RIGHT|\s+)+JOIN\s+/',$this->Join) ) $this->Join = " LEFT JOIN ".$this->Join;
			if( $this->GroupBy && !preg_match('/^\s+GROUP\sBY\s+/',$this->GroupBy) ) $this->GroupBy = " GROUP BY ".$this->GroupBy;
			if( $this->Having && !preg_match('/^\s+HAVING\s+/',$this->Having) ) $this->Having = " HAVING ".$this->Having;
			if( $this->OrderBy && !preg_match('/^\s+ORDER\sBY\s+/',$this->OrderBy) ) $this->OrderBy = " ORDER BY ".$this->OrderBy;
			if( $this->Limit && !preg_match('/^\s+LIMIT\s+/',$this->Limit) ) $this->Limit = " LIMIT ".$this->Limit;

            if( $this->ItemsPerPage && !$this->HidePager )
                $sql = "SELECT SQL_CALC_FOUND_ROWS @fields@ FROM @table@@join@@where@@groupby@@having@@orderby@@limit@";
            else
                $sql = "SELECT @fields@ FROM @table@@join@@where@@groupby@@having@@orderby@@limit@";
			$sql = str_replace("@fields@",$this->Columns,$sql);
			$sql = str_replace("@table@","`".$this->DataTable."`",$sql);
			$sql = str_replace("@join@",$this->Join,$sql);
			$sql = str_replace("@where@",$this->Where,$sql);
			$sql = str_replace("@groupby@",$this->GroupBy,$sql);
			$sql = str_replace("@having@",$this->Having,$sql);
			$sql = str_replace("@orderby@",$this->OrderBy,$sql);
			$sql = str_replace("@limit@",$this->Limit,$sql);
			$this->Sql = $sql;
		}

		$this->Clear();
		$this->ExecuteSql($this->Sql);
	}

	/**
	 * Allows to override the default execute method
	 * 
	 * This will allow you to integrate your own execution handler
	 * @param object $handler Object containing the handler method
	 * @param string $function Name of handler method
	 * @return void
	 */
	function OverrideExecuteSql(&$handler,$function)
	{
		$this->ExecuteSqlHandler = array($handler,$function);
	}
	
	/**
	 * Allows to assign your own handler to the AddHeader function
	 * 
	 * Sometimes you do not want to inherit from this, but create a table and assign the handlers
	 * to another object.
	 * @param object $handler Object containing the handler method
	 * @param string $function Name of the handler method
	 * @return DatabaseTable `$this`
	 */
	function AssignOnAddHeader(&$handler,$function)
	{
		$res = $this->OnAddHeader;
		$this->OnAddHeader = array($handler,$function);
		return $this;
	}
	/**
	 * Allows to assign your own handler to the AddRow function
	 * 
	 * Sometimes you do not want to inherit from this, but create a table and assign the handlers
	 * to another object.
	 * @param object $handler Object containing the handler method
	 * @param string $function Name of the handler method
	 * @return DatabaseTable `$this`
	 */
	function AssignOnAddRow(&$handler,$function)
	{
		$res = $this->OnAddRow;
		$this->OnAddRow = array($handler,$function);
		return $this;
	}

	protected function GetColumns(){return array("*");}
	protected function GetJoin(){return "";}
	protected function GetWhere(){return "";}
	protected function GetGroupBy(){return "";}
	protected function GetHaving(){return "";}
	protected function GetOrderBy(){return "";}
	protected function GetLimit(){return "";}
	
	/**
	 * Default AddRow method
	 * 
	 * This will be called for each row to add (from the execution routines).
	 * If you override this in derivered classes you can easily react on that.
	 * Uses <Table::NewRow>() internally
	 * @param array $data Row as assaciative array
	 * @return void
	 */
	function AddRow(&$data) { $this->NewRow($data); }
	
	/**
	 * Default AddHeader method
	 * 
	 * Creates a table header with the given keys as text.
	 * Uses <Table::Header>() internally
	 * @param array $keys Array of columns this <DatabaseTable> contains
	 * @return void
	 */
	function AddHeader($keys)
	{
		$head = array_combine($keys,$keys);
		$this->Header()->NewRow($head);
	}

	protected function _preProcessData($row)
	{
		if( ($this->ParsingBehaviour & self::PB_STRIPHTML) > 0 )
			foreach( $row as $k=>$v )
				$row[$k] = strip_tags($v);
		if( ($this->ParsingBehaviour & self::PB_HTMLSPECIALCHARS) > 0 )
			foreach( $row as $k=>$v )
				$row[$k] = htmlspecialchars($v);

		if( $this->ParsingBehaviour == self::PB_NOPROCESSING )
		{
			foreach( $row as $k=>$v )
			{

				$c = 0;
				if( preg_match_all('/<([^\s\/>]+)>/', $v, $tags, PREG_SET_ORDER) )
				{
					foreach( $tags as $t )
					{
						if( !preg_match_all('/<\/'.$t[1].'>/', $v, $ctags, PREG_SET_ORDER) )
							continue;
						$c++;
					}
				}

				$c1 = count(explode('"',$v));
				$c2 = count(explode("'",$v));
				$c3 = count(explode(">",$v));
				$c4 = count(explode("<",$v));
				if( count($tags)!=$c || ($c1 & 1)==0 || ($c2 & 1)==0 || ($c3 & 1)==0 || ($c4 & 1)==0 )
					$row[$k] = htmlspecialchars($v);
			}
		}
		return $row;
	}
	
	/**
	 * @override Calls <DatabaseTable::GetData>() and loops thru the <ResultSet> creating the table content before calling <OVERRIDE::DatabaseTable::PreRender>
	 */
	function PreRender($args = array())
	{
        // stop rebuilding the table of row-action was clicked: 
        // - performance 
        // - row-ids would change and trigger error on subsequent clicked actions
        if( current_event() == 'onactionclicked' && current_controller(false) instanceof Table )
            return parent::PreRender($args);
        
		$this->GetData();
		
        if( !$this->ResultSet || $this->ResultSet->Count()==0 )
		{
			if( !$this->noDataAsRow )
	           return $this->contentNoData;
			
			if( !$this->header )
				if( $this->OnAddHeader )
					$this->OnAddHeader[0]->{$this->OnAddHeader[1]}($this, array());
				else
					$this->AddHeader(array());
				
			$td = $this->SetColFormat(0,"")->NewCell($this->contentNoData);
			$td->colspan = $this->header->GetMaxCellCount();
			$this->HidePager = true;
		}
        else
        {
            $this->_rowModels = array();
            foreach( $this->ResultSet as $raw_row )
            {
				$row = $this->_preProcessData($raw_row);

                if( !$this->header )
                    if( $this->OnAddHeader )
						$this->OnAddHeader[0]->{$this->OnAddHeader[1]}($this, array_keys($row));
                    else
                        $this->AddHeader(array_keys($row));

                if( $this->OnAddRow )
                    $this->OnAddRow[0]->{$this->OnAddRow[1]}($this, $row);
                else
                    $this->AddRow($row);
				
				$this->AddDataToRow($raw_row);
            }
			if( $this->ItemsPerPage )
				$this->HidePager = false;
        }
		parent::PreRender($args);
	}

	const EXPORT_FORMAT_XLS  = 'xls';
	const EXPORT_FORMAT_XLSX = 'xlsx';
	const EXPORT_FORMAT_CSV  = 'csv';
	
	static $export_def = array
	(
		'xls'  => array( 'fn'=>'export_{date}.xls',  'mime'=>'application/vnd.ms-excel' ),
		'xlsx' => array( 'fn'=>'export_{date}.xlsx', 'mime'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ),
		'csv'  => array( 'fn'=>'export_{date}.csv',  'mime'=>'text/csv' ),
	);
	
	/**
	 * @internal Currently untested, so marked <b>internal</b>
	 * @attribute[RequestParam('format','string')]
	 */
	function Export($format, $rowcallback = null)
	{
		switch( $format )
		{
			case self::EXPORT_FORMAT_XLS:
			case self::EXPORT_FORMAT_XLSX:
				$this->_exportExcel($format,$rowcallback);
				break;
			case self::EXPORT_FORMAT_CSV:
				$this->_exportCsv($rowcallback);
				break;
		}
	}
	
	private function _export_get_header()
	{
		$res = array();
		if( $this->header )
		{
			foreach( $this->header->Rows() as $row )
			{
				$line = array();
				foreach( $row->Cells() as $cell )
				{
					$cc = trim(strip_tags($cell->GetContent()));
					if( translation_string_exists($cc) )
						$cc = getString($cc);
					$line[] = $cc;
				}
			}
			$res[] = $line;
		}
		return $res;
	}
	
	private function _export_get_data(CultureInfo $ci=null, $rowcallback = null)
	{
		$copy = clone $this;
		$copy->ItemsPerPage = false; 
		if( $ci )
			$copy->Culture = $ci;
		$copy->GetData();
		
		$res = array();
		$copy->ResultSet->FetchMode = PDO::FETCH_ASSOC;
        $cols = [];
        foreach( $this->Columns as $c )
            $cols[] = trim($c,"`");
		foreach( $copy->ResultSet as $row )
		{
			$row = $copy->_preProcessData($row);
            if( $rowcallback != null )
                $row = $rowcallback($row);
            $r = [];
            foreach( $cols as $k )
                if(isset($row[$k]))
                    $r[$k] = $row[$k];
                else
                    $r[$k] = null;

            if( !isset($format_buffer) )
			{
				$i=0; $format_buffer = array();
				foreach( $r as $k=>$v )
				{
                    if( isset($this->ColFormats[$i]) )
                        $format_buffer[$k] = $this->ColFormats[$i];
                    $i++;
				}
			}
			foreach( $format_buffer as $k=>$cellformat )
				$r[$k] = $cellformat->FormatContent($r[$k],$copy->Culture);
			$res[] = $r;
		}
		return $res;
	}
	
	protected function _exportExcel($format=self::EXPORT_FORMAT_XLSX, $rowcallback = null)
	{		
		system_load_module(__DIR__.'/../../../modules/mod_phpexcel.php');
		$xls = new PHPExcel();
		$sheet = $xls->getActiveSheet();
		$row = 1;
		$max_cell = 0;
		
		$ci = ExcelCulture::FromCode(isset($this->Culture) ? $this->Culture->Code : 'en-US');
		$head_rows = $this->_export_get_header();
		$first_data_row = count($head_rows)+1;

		foreach( array_merge($head_rows,$this->_export_get_data($ci,$rowcallback)) as $data_row )
		{
			$i = 0;
			foreach( $data_row as $val )
			{
				$sheet->setCellValueByColumnAndRow($i, $row, $val);
				$i++;
				if( $i>$max_cell )$max_cell = $i;
			}
			$row++;
		}
		for($i=0; $i<=$max_cell; $i++)
		{
			$sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
			if( isset($this->ColFormats[$i]) )
			{
				$ef = $ci->GetExcelFormat($this->ColFormats[$i]);
				$col = PHPExcel_Cell::stringFromColumnIndex($i);
				$sheet->getStyle("$col$first_data_row:$col$row")
					->getNumberFormat()
					->setFormatCode($ef);
			}
		}
		
		if( $format == self::EXPORT_FORMAT_XLS )
			$xlswriter = PHPExcel_IOFactory::createWriter($xls, 'Excel5');
		else
			$xlswriter = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
		
		$filename = str_replace("{date}",date("Y-m-d_H-i-s"),self::$export_def[$format]['fn']);
		$mime = self::$export_def[$format]['mime'];
		
		header("Content-Type: $mime");
		header("Content-Disposition: attachment; filename=\"".$filename."\";");
		header("Content-Transfer-Encoding: binary");
		header('Expires: 0');
		header('Pragma: public');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header("Cache-Control: private",false);
		$xlswriter->save('php://output');
		die('');
	}
	
	protected function _exportCsv($rowcallback = null)
	{
		$esc = '"';
		$sep = ',';
		$newline = "\n";
		$csv = array();
		foreach( array_merge($this->_export_get_header(),$this->_export_get_data(null,$rowcallback)) as $row )
		{
			$csv_line = array();
			foreach( $row as $val )
			{
				if( strpos($val, $sep) !== false )
					$csv_line[] = "$esc$val$esc";
				else
					$csv_line[] = $val;
			}
			$csv[] = implode($sep,$csv_line);
		}
		
		$csv = implode($newline,$csv);
		$filename = str_replace("{date}",date("Y-m-d_H-i-s"),self::$export_def[self::EXPORT_FORMAT_CSV]['fn']);
		$mime = self::$export_def[self::EXPORT_FORMAT_CSV]['mime'];
		
		header("Content-Type: $mime");
		header("Content-Disposition: attachment; filename=\"".$filename."\";");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".strlen($csv));
		header('Expires: 0');
		header('Pragma: public');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header("Cache-Control: private",false);
		die($csv);
	}
	
	/**
	 * @override <Table::AddPager> and hides $total_items argument
	 */
	function AddPager($items_per_page = 15, $current_page=1, $max_pages_to_show=10)
	{
		return parent::AddPager(0,$items_per_page,$current_page,$max_pages_to_show);
	}
	
	protected function RenderPager()
	{
		$this->TotalItems = $this->ResultSet?$this->ResultSet->GetpagingInfo('total_rows'):0;
		return parent::RenderPager();
	}
    
    var $logIfSlow = false;
    function LogIfSlow($min_ms)
    {
        $this->logIfSlow = $min_ms;
        return $this;
    }
}
