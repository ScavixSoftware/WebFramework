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

use ScavixWDF\Base\Control;

/**
 * Represents a colgroup element but in div annotation.
 * 
 */
class ColGroup extends Control
{
    var $current_col = false;

	function __initialize()
	{
		parent::__initialize("div");
		$this->class = "colgroup";
	}

	/**
	 * Adds a col element.
	 * 
	 * @param mixed $width Columns width
	 * @param string $align Alignment
	 * @return Control The created col element
	 */
    function &NewCol($width=false,$align=false)
    {
        $this->current_col = new Control("div");
		$this->current_col->class = "col";
		if( $width )
			$this->current_col->width = $width;
		if( $align )
			$this->current_col->align = $align;

		$this->content($this->current_col);
        return $this->current_col;
    }
	
	/**
	 * Sets properties of a specified col element.
	 * 
	 * @param int $index Zero based index of col element
	 * @param mixed $width Columns width
	 * @param string $align Alignment
	 * @return Control The changed col element
	 */
	function SetCol($index,$width=false,$align=false)
	{
		while( count($this->_content) <= $index )
			$this->NewCol();
		
		if( $width )
			$this->_content[$index]->width = $width;
		if( $align )
			$this->_content[$index]->align = $align;
		
		return $this->_content[$index];
	}
	
	function SetAlignment($alignment)
	{
		foreach( $alignment as $i=>$a )
		{
			switch( strtolower($a) )
			{
				case 'l':
				case 'left':
					$this->SetCol($i,false,'left');
					break;
				case 'r':
				case 'right':
					$this->SetCol($i,false,'right');
					break;
				case 'c':
				case 'center':
					$this->SetCol($i,false,'center');
					break;
			}
		}
		return $this;
	}
}
