<?
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
 
$GLOBALS['TR_CURRENTLY_ODD'] = false;

/**
 * A table row written as div.
 * 
 */
class Tr extends Control
{
//    var $row_groups = array();
    var $options = false;
    var $current_cell = false;
	var $model = false;

	/**
	 * @param array $options Currently none
	 */
	function __initialize($options=false)
	{
		parent::__initialize("div");
		$this->class = "tr";
        $this->options = $options;

		if( $options )
		{
			if( isset($options['oddclass']) && isset($options['evenclass']) )
			{
				if( !isset($options['hidden']) || !$options['hidden'] )
				{
//					$this->class = $GLOBALS['TR_CURRENTLY_ODD']?$options['oddclass']:$options['evenclass'];
//					$GLOBALS['TR_CURRENTLY_ODD'] = !$GLOBALS['TR_CURRENTLY_ODD'];
				}
			}
			if( isset($options['tr_class']) )
				$this->class = $options['tr_class'];
		}
	}

	/**
	 * Gets the current cell.
	 * 
	 * @return Td The current cell of false if none
	 */
	function &CurrentCell()
	{
		return $this->current_cell;
	}

	/**
	 * Creates a new cell.
	 * 
	 * @param mixed $content Contents for it
	 * @param array $options See <Td::__initialize> for options
	 * @return Td The created cell
	 */
    function &NewCell($content=false,$options=false)
    {
        $cell = new Td($options);
        if( $content !== false )
            $cell->content($content);

        $this->current_cell =& $this->content($cell);
        return $this->current_cell;
    }

	/**
	 * Get the cell at index.
	 * 
	 * @param int $index Zero based index
	 * @return Td The cell
	 */
	function &GetCell($index)
	{
		return $this->_content[$index];
	}
	
	/**
	 * Returns all cells.
	 * 
	 * @return array List of <Td> objects
	 */
	function Cells()
	{
		return $this->_content;
	}
	
	/**
	 * Gets the cell count.
	 * 
	 * @return int Count of cells
	 */
	function CountCells()
	{
		return count($this->_content);
	}

	/**
	 * @override
	 */
	function WdfRender()
	{
		if( isset($this->options['oddclass']) && isset($this->options['evenclass']) )
		{
			if( !isset($this->_css['display']) || $this->_css['display'] != "none" )
			{
				$this->class = $GLOBALS['TR_CURRENTLY_ODD']?$this->options['oddclass']:$this->options['evenclass'];
				$GLOBALS['TR_CURRENTLY_ODD'] = !$GLOBALS['TR_CURRENTLY_ODD'];
			}
		}

		return parent::WdfRender();
	}
}
