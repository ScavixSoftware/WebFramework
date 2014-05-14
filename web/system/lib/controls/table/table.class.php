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

use ScavixWDF\Base\AjaxResponse;
use ScavixWDF\Base\Control;
use ScavixWDF\Controls\Anchor;
use ScavixWDF\Localization\CultureInfo;
use ScavixWDF\WdfException;

/**
 * An HTML table in DIV notation.
 * 
 */
class Table extends Control
{
    var $header = false;
    var $footer = false;
	var $colgroup = false;
    var $current_row_group = false;
    var $current_row = false;
    var $current_cell = false;

    var $Caption = false;

	var $RowGroupOptions = array();
	var $RowOptions = array();
	var $ColFormats = array();
	var $Culture = false;
	
	var $DataCallback = false;
	
	var $ItemsPerPage = false;
	var $CurrentPage = false;
	var $MaxPagesToShow = false;
	var $TotalItems = false;
	var $HidePager = false;
	
	function __initialize()
	{
		parent::__initialize("div");
		$this->class = 'table';
		$this->script("$('#{self}').table();");
	}
	
	/**
	 * Sets the format for a specific column.
	 * 
	 * @param int $index Zero based column index
	 * @param string $format See <CellFormat> for explanation
	 * @param bool $blank_if_false If shall be empty if content is false (that may be 0 or '' too)
	 * @param type $conditional_css See <CellFormat> for explanation
	 * @return Table `$this`
	 */
	function SetColFormat($index,$format,$blank_if_false=false,$conditional_css=array())
	{
		$this->ColFormats[$index] = new CellFormat($format, $blank_if_false, $conditional_css);
		if( array_key_exists('copy',$conditional_css) )
		{
			$this->ColFormats[$index]->conditional_css['copy'] = $this->ColFormats[$this->ColFormats[$index]->conditional_css['copy']];
		}
		return $this;
	}
	
	/**
	 * Gets the <CellFormat> for a column.
	 * 
	 * @param int $index Zero based column index
	 * @return CellFormat The <CellFormat> object
	 */
	function GetColFormat($index)
	{
		if( !isset($this->ColFormats[$index]) )
			return new CellFormat('%s');
		return $this->ColFormats[$index];
	}

	/**
	 * Clears the complete table.
	 * 
	 * @return Table `$this`
	 */
	function Clear()
	{
		$this->current_row_group = false;
		$this->current_row = false;
		$this->current_cell = false;
		if( $this->_actions )
			$this->content($this->_actions,true);
		else
			$this->clearContent();
		return $this;
	}

	/**
	 * Gets the table header.
	 * 
	 * Creates one if needed.
	 * @return THead The tables header
	 */
    function &Header()
    {
        if( !$this->header )
            $this->header = new THead();
        return $this->header;
    }

	/**
	 * Gets the table footer.
	 * 
	 * Creates one if needed.
	 * @param bool $clear If true deleted previously set footer
	 * @return TFoot The tables footer
	 */
    function &Footer($clear=false)
    {
        if( $clear || !$this->footer )
            $this->footer = new TFoot();
        return $this->footer;
    }

	/**
	 * Gets the <ColGroup> definition
	 * 
	 * Creates one if needed.
	 * @return ColGroup The tables <ColGroup> object
	 */
	function &ColGroup()
	{
        if( !$this->colgroup )
            $this->colgroup = new ColGroup();
        return $this->colgroup;
	}

	/**
	 * Creates a new row group and sets it the current
	 * 
	 * Newly created rows will then be added to this group.
	 * @param array $options See <TBody> for options
	 * @return TBody The row group
	 */
    function &NewRowGroup($options=false)
    {
		if( !$options )
			$options = $this->RowGroupOptions;
        $this->current_row_group = new TBody($options,"tbody",$this);
		$this->current_row_group->RowOptions = $this->RowOptions;

        $this->content($this->current_row_group);
        return $this->current_row_group;
    }

	/**
	 * Creates a new row.
	 * 
	 * Will be added to the current row group (which wis created if none yet).
	 * @param array $data Data to be added to the row automatically
	 * @param array $options Rows options, see <TBody::NewRow>
	 * @return Tr The new <Tr> object
	 */
    function &NewRow($data=false,$options=false)
    {
        if( !$this->current_row_group )
            $this->NewRowGroup();

		if( !$options )
			$options = $this->RowOptions;

		$this->current_row =& $this->current_row_group->NewRow($data,$options);

        return $this->current_row;
    }

	/**
	 * Creates a new cell
	 * 
	 * New row will be created if there's not one already.
	 * @param mixed $content Content to be added to the cell automatically
	 * @return Td The new <Td> object
	 */
    function &NewCell($content=false)
    {
        if( !$this->current_cell )
            $this->NewRow();

		$this->current_cell = $this->current_row_group->NewCell($content);
        return $this->current_cell;
    }
	
	/**
	 * Returns the current row, if any.
	 * 
	 * @return Tr The current row object or false
	 */
	function GetCurrentRow()
	{
		if( !$this->current_row_group )
            $this->NewRowGroup();
		return $this->current_row_group->GetCurrentRow();
	}

	/**
	 * @override
	 */
	function PreRender($args=array())
	{
		if( isset($this->RowOptions['hoverclass']) && $this->RowOptions['hoverclass'] )
		{
			$over = "function(){ $(this).addClass('{$this->RowOptions['hoverclass']}') }";
			$out  = "function(){ $(this).removeClass('{$this->RowOptions['hoverclass']}') }";
			$rowhover = "$('#{$this->id} tbody tr').hover($over,$out);";
			$this->script($rowhover);
		}
		parent::PreRender($args);
	}
	
	protected function _ensureCaptionObject()
	{
		if( $this->Caption && !($this->Caption instanceof Control) )
        {
			$tmp = new Control("div");
			$tmp->content($this->Caption);
			$tmp->class = 'caption';
			$this->Caption = $tmp;
		}
	}
	
	/**
	 * @override 
	 */
	function WdfRender()
    {
		if( $this->DataCallback )
		{
			$this->Clear();
			$args = array($this);
			system_call_user_func_array_byref($this->DataCallback[0], $this->DataCallback[1], $args);
		}
			
		if( $this->ItemsPerPage && !$this->HidePager )
		{
			$pager = $this->RenderPager();
			$this->content($pager);
		}
        if( $this->footer )
            $this->prepend($this->footer);//array_merge(array($this->footer),$this->_content);
        if( $this->header )
            $this->prepend($this->header);//array_merge(array($this->header),$this->_content);

		if( $this->colgroup )
			$this->prepend($this->colgroup);//array_merge(array($this->colgroup),$this->_content);

        if( $this->Caption )
        {
			$this->_ensureCaptionObject();
            $this->prepend($this->Caption);//array_merge(array($this->Caption),$this->_content);
        }
		
        foreach( $this->_content as &$c )
        {
			if( !is_object($c) || (get_class_simple($c) != "TBody") )
				continue;
            foreach( $c->_content as $r )
			{
				if( !($r instanceof Tr) )
					continue;

				$rcnt = count($r->_content);
				for($i=0; $i<$rcnt; $i++)
				{
					if( $r->_content[$i]->CellFormat )
						$r->_content[$i]->CellFormat->Format($r->_content[$i], $this->Culture);
					elseif( isset($this->ColFormats[$i]) )
						$this->ColFormats[$i]->Format($r->_content[$i], $this->Culture);
				}
			}
        }
		return parent::WdfRender();
    }
	
/* --------------- High level methods returning $this for easy usage --------------------- */
	
	/**
	 * Just sets the caption.
	 * 
	 * @param string $cap Caption text
	 * @return Table `$this`
	 */
	function SetCaption($cap)
	{
		$this->Caption = $cap;
		return $this;
	}
	
	/**
	 * Takes all arguments given and uses each as row-title.
	 * 
	 * @return Table `$this`
	 */
	function SetHeader()
	{
		$this->Header()->NewRow(func_get_args());
		return $this;
	}
	
	/**
	 * Takes all arguments given and uses each as row-title.
	 * 
	 * @return Table `$this`
	 */
	function SetFooter()
	{
		$this->Footer()->NewRow(func_get_args());
		return $this;
	}
	
	/**
	 * Same as NewRowGroup($options) but returns $this to allow method chaining.
	 * 
	 * @param array $options See <TBody> for options
	 * @return Table `$this`
	 */
	function AddNewRowGroup($options=false)
	{
		$this->NewRowGroup($options);
		return $this;
	}
	
	/**
	 * Adds a new row, takes all arguments given and uses each as new data-cell.
	 * 
	 * @return Table `$this`
	 */
	function AddNewRow()
	{
		$this->NewRow(func_get_args());
		return $this;
	}
	
	/**
	 * Takes one argument for each (previously set) column
	 * 
	 * possible values: l, r, c (or: left, right, center) as strings
	 * sample $tab->SetAlignment('l','l','c','r') when there are 4+ columns
	 * to skip a column just pass an empty string: $tab->SetAlignment('l','','','r')
	 * @return Table `$this`
	 */
	function SetAlignment()
	{
		$cg = $this->ColGroup();
		$head = $this->Header();
		$foot = $this->Footer();
		foreach( func_get_args() as $i=>$a )
		{
			switch( strtolower($a) )
			{
				case 'l':
				case 'left':
					$cg->SetCol($i,false,'left');
					$head->GetCell($i)->align = 'left';
					$foot->GetCell($i)->align = 'left';
					break;
				case 'r':
				case 'right':
					$cg->SetCol($i,false,'right');
					$head->GetCell($i)->align = 'right';
                    $foot->GetCell($i)->align = 'right';
					break;
				case 'c':
				case 'center':
					$cg->SetCol($i,false,'center');
					$head->GetCell($i)->align = 'center';
                    $foot->GetCell($i)->align = 'center';
					break;
			}
		}
		return $this;
	}
	
	/**
	 * Takes one argument for each (previously set) column
	 * 
	 * possible values: see CellFormat class
	 * sample $tab->SetFormat('int','f2') when there are 2+ columns
	 * to skip a column just pass an empty string: $tab->SetFormat('int','','','f2')
	 * @return Table `$this`
	 */
	function SetFormat()
	{
		foreach( func_get_args() as $i=>$f )
		{
			if( $f == "" )
				continue;
			$this->SetColFormat($i, $f);
		}
		return $this;
	}
	
	/**
	 * Just sets the culture.
	 * 
	 * This will be used when value are formatted using a <CellFormat> specified via <Table::SetColFormat> or <Table::SetFormat>
	 * @param CultureInfo $ci <CultureInfo> object speficying the culture
	 * @return Table `$this`
	 */
	function SetCulture($ci)
	{
		$this->Culture = $ci;
		return $this;
	}
	
	var $_actions = false;
	var $_rowModels = array();
	var $_actionHandler = array();
	var $_sortHandler = false;
	
	/**
	 * Adds a data object to the current row.
	 * 
	 * This will be stored for AJAX acceess
	 * @param mixed $model Data object
	 * @return Table `$this`
	 */
	function AddDataToRow($model)
	{
		if( !$this->current_row )
			WdfException::Raise("No row added yet");
		$this->current_row->id = $this->current_row->_storage_id;
		$this->_rowModels[$this->current_row->id] = $model;
		return $this;
	}
	
	/**
	 * Gets the model for a specific row id.
	 * 
	 * Note that $row_id is the id of the <Tr> object, not the index in the row listing!
	 * @param string $row_id Id of the <Tr> object
	 * @return mixed The data object
	 */
	function GetRowModel($row_id)
	{
		return $this->_rowModels[$row_id];
	}
	
	/**
	 * Adds an action to the current row.
	 * 
	 * This is in fact a little icon displayed on hovering the row. Clicking on it
	 * will trigger an AJAX action.
	 * @param string $icon Valid <uiControl::Icon>
	 * @param string $label Action label (alt and tootltip text)
	 * @param object $handler Object handling the action
	 * @param string $method Objects method that handles the action
	 * @return Table `$this`
	 */
	function AddRowAction($icon,$label,$handler=false,$method=false)
	{
		if( !$this->_actions )
			$this->_actions = $this->content(new Control('div'))->css('display','none')->css('position','absolute')->addClass('ui-table-actions');
		
		$ra = new Control('span');
		$ra->class = "ui-icon ui-icon-$icon";
		$ra->title = $label;
		$ra->id = $ra->_storage_id;
		$ra->setData('action',$icon);
		
		$this->_actions->content( $ra->wrap('div') );
		
		if( $handler && $method )
			$this->_actionHandler[$icon] = array($handler,$method);

		store_object($this);
		return $this;
	}
	
	/**
	 * @internal Handles row action clicks and calls the defined handlers (<Table::AddRowAction>)
	 * @attribute[RequestParam('action','string')]
	 * @attribute[RequestParam('row','string')]
	 */
	function OnActionClicked($action,$row)
	{
		if( isset($this->_actionHandler[$action]) )
		{
			$model = $this->_rowModels[$row];
			return call_user_func_array($this->_actionHandler[$action],array($this,$action,$model,$row));
		}
		log_warn("No handler defined for $action");
		return AjaxResponse::None();
	}
	
	/**
	 * Sets a sort handler for this table
	 * 
	 * Note: this does not mean that the data can be sorted for display, but that the user may rearrange the rows via mouse drag and drop!
	 * @param object $handler Object handling the drop
	 * @param string $method Method to be called
	 * @return Table `$this`
	 */
	function Sortable($handler,$method)
	{
		$this->_sortHandler = array($handler,$method);
		$s = "wdf.post('{self}/OnReordered',{rows:$('#{self} .tbody .tr').enumAttr('id')}); $('#{self} .ui-table-actions').removeClass('sorting');";
		$s = "$('#{self} .tbody').sortable({distance:5,update: function(){ $s }, start:function(){ $('#{self} .ui-table-actions').addClass('sorting').hide(); } });";
		$this->script($s);
		store_object($this);
		return $this;
	}
	
	/**
	 * @internal Handles the sort-drop event and calls the hanlder (<Table::Sortable>)
	 * @attribute[RequestParam('rows','array',array())]
	 */
	function OnReordered($rows)
	{
		return call_user_func_array($this->_sortHandler,array($this,$rows));
	}
	
	/**
	 * Adds a Pager to the table
	 * 
	 * Will be displayed in the tables footer.
	 * @param int $total_items Total number of items
	 * @param int $items_per_page Items per page to be displayed
	 * @param int $current_page One (1) based index of current page
	 * @param int $max_pages_to_show Maximum links to pages to be shown
	 * @return DatabaseTable `$this`
	 */
	function AddPager($total_items, $items_per_page = 15, $current_page=1, $max_pages_to_show=10)
	{
		$this->TotalItems = $total_items;
		$this->ItemsPerPage = $items_per_page;
		$this->CurrentPage = $current_page;
		$this->MaxPagesToShow = $max_pages_to_show;
		store_object($this);
		return $this;
	}
	
	function SetDataCallback($handler,$method)
	{
		$this->DataCallback = array($handler,$method);
	}
	
	/**
	 * @internal Will be polled via AJAX to change the page if you defined a pager using <DatabaseTable::AddPager>
	 * @attribute[RequestParam('number','int')]
	 */
	function GotoPage($number)
	{
		$this->CurrentPage = $number;
	}
	
	protected function RenderPager()
	{
		$pages = ceil($this->TotalItems / $this->ItemsPerPage);
		if( $pages < 2 )
			return;
		
		log_debug("RenderPager: {$this->CurrentPage}/$pages");
		$ui = new Control('div');
		$ui->addClass("pager");

		if( $this->CurrentPage > 1 )
		{
			$ui->content( new Anchor("javascript: $('#$this->id').gotoPage(1)","|&lt;") );
			$ui->content( new Anchor("javascript: $('#$this->id').gotoPage(".($this->CurrentPage-1).")","&lt;") );
		}

		$start = 1;
		while( $pages > $this->MaxPagesToShow && $this->CurrentPage > $start + $this->MaxPagesToShow / 2 )
			$start++;

		for( $i=$start; $i<=$pages && $i<($start+$this->MaxPagesToShow); $i++ )
		{
			if( $i == $this->CurrentPage )
				$ui->content("<span class='current'>$i</span>");
			else
				$ui->content(new Anchor("javascript: $('#$this->id').gotoPage($i)",$i));
		}

		if( $this->CurrentPage < $pages )
		{
			$ui->content( new Anchor("javascript: $('#$this->id').gotoPage(".($this->CurrentPage+1).")","&gt;") );
			$ui->content( new Anchor("javascript: $('#$this->id').gotoPage($pages)","&gt;|") );
		}
		return $ui;
	}
}
