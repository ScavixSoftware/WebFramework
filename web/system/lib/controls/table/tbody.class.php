<?
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
 * This is tbody in div annotation.
 * 
 */
class TBody extends Control
{
	private $table = false;

    var $options = false;
    var $header = false;
    var $current_row = false;
    var $current_cell = false;

	var $RowOptions = array();

	/**
	 * Constructs a new <TBody>.
	 * 
	 * Possible options: 
	 * - 'collapsible' := true|false
	 * - 'visible' := true|false
	 * @param array $options Options
	 * @param string $class Classname
	 * @param Table $parent_table The parent table this belongs to
	 */
	function __initialize($options,$class="tbody",&$parent_table=false)
	{
		parent::__initialize("div");
		$this->class = $class;
        $this->options = $options;
		$this->table = $parent_table;
	}

	/**
	 * Creates a header.
	 * 
	 * We treat tbody headers as the first row in the tbody element.
	 * @return Tr The created header
	 */
    function &Header()
    {
        if( !$this->header )
            $this->header = new Tr($this->RowOptions);
        return $this->header;
    }

	/**
	 * Creates a new row.
	 * 
	 * @param array $data If given creates new cells in the row (see <Tr::NewCell>)
	 * @param array $options See <Tr> for options
	 * @return type
	 */
    function &NewRow($data=false,$options = false)
    {
		$this->current_row = new Tr($options?$options:$this->RowOptions);
        $this->content($this->current_row);

		if( $data )
		{
            $i = 0;
			foreach( $data as $rowdata )
            {
				$cell = $this->current_row->NewCell($rowdata);
                if($this->table && $this->table->colgroup && isset($this->table->colgroup->_content[$i]) &&
					isset($this->table->colgroup->_content[$i]->_attributes["align"]) && $this->table->colgroup->_content[$i]->_attributes["align"] )
                    $cell->align = $this->table->colgroup->_content[$i]->_attributes["align"];
                $i++;
            }
		}
		return $this->current_row;
    }

	/**
	 * @shortcut To create a new cell in the current row (<Tr::NewCell>)
	 */
    function &NewCell($content=false)
    {
        if( !$this->current_row )
            $this->NewRow();
        $this->current_cell =& $this->current_row->NewCell($content);
        return $this->current_cell;
    }

	/**
	 * @shortcut To get a cell from the current row (<Tr::GetCell>)
	 */
	function &GetCell($index)
	{
		$null = null;
		if( !$this->current_row )
			return $null;
		return $this->current_row->GetCell($index);
	}
	
	/**
	 * Returns all rows.
	 * 
	 * @return array List of <Tr> objects
	 */
	function Rows()
	{
		return $this->_content;
	}
	
	/**
	 * Returns the maximum cell count.
	 * 
	 * Loops thru all rows and finds the maximum count of cells in a row.
	 * @return int Maximum cell count
	 */
	function GetMaxCellCount()
	{
		$max = 0;
		foreach( $this->_content as $tr )
			$max = max($max,$tr->CountCells());
		return $max;
	}
	
	/**
	 * Returns all cells contents as array.
	 * 
	 * Note that this will return a two-dimensional array as it
	 * loops thru all rows and for each thru all it's cells.
	 * @return array Contents of all cells
	 */
	function GetCellContentArray()
	{
		$res = array();
		foreach( $this->Rows() as $row )
		{
			$data = array();
			foreach( $row->Cells() as $c )
				$data[] = $c->GetContent();
			$res[] = $data;
		}
		return $res;
	}

	/**
	 * @override
	 */
    function WdfRender()
    {
        if( $this->options )
        {
			if( isset($this->options['collapsible']) && $this->options['collapsible'] )
			{
				if( !isset($this->class) ) $this->class = "";
				$this->class .= " collapsible";

				$colcount = 0;
				foreach( $this->_content as &$row )
                {
					$colcount = max($colcount,count($row->_content));
					if( !isset($row->_content[0]->class) ) $row->_content[0]->class = "";
					$row->_content[0]->class .= " indent";
                }

                if( !$this->header )
                {
                    $this->header = new Tr($this->RowOptions);
                    $hc = $this->header->NewCell('&gt;&gt; Click to expand.');
                    $hc->colspan = $colcount;
                }

				if( !isset($this->header->onclick) )
                    $this->header->onclick = "";

				$speed = "500";
				$func = "function(){ $('#{$this->table->id}').click(); wdf.debug('Click invoked'); }";
				$func = "setTimeout(unescape(".json_encode("$('#{$this->table->id}').click();")."),$speed+10);";
                $this->header->onclick = "$func $(this).siblings('tr').css('display') == 'none' ? $(this).siblings('tr').fadeIn($speed) : $(this).siblings('tr').fadeOut($speed);".$this->header->onclick;

                $this->header->css("cursor","pointer");

				foreach( $this->_content as &$row )
                {
					unset($row->options['oddclass']);
					unset($row->options['evenclass']);
                }
			}

            if( isset($this->options['visible']) && !$this->options['visible'] )
            {
                //$colcount = 0;
                foreach( $this->_content as &$row )
                {
                    //$colcount = max($colcount,count($row->_content));
                    $row->css("display","none");
                }
            }
        }

        if( $this->header )
            $this->_content = array_merge(array($this->header),$this->_content);

        return parent::WdfRender();
    }
}
