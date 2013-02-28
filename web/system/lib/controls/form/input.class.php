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
 * This is a basic &lt;input/&gt;.
 * 
 * Used as base class for all kind of inputs.
 */
class Input extends Control
{
	function __initialize()
	{
		parent::__initialize("input");
	}
	
	/**
	 * Sets the type attribute.
	 * 
	 * @param string $type The type
	 * @return Input `$this`
	 */
	function setType($type)
	{
		if( $type )
			$this->type = $type;
		return $this;
	}
	
	/**
	 * Sets the name attribute.
	 * 
	 * @param string $name The type
	 * @return Input `$this`
	 */
	function setName($name)
	{
		if( $name )
			$this->name = $name;
		return $this;
	}
	
	/**
	 * Sets the value attribute.
	 * 
	 * @param string $value The value
	 * @return Input `$this`
	 */
	function setValue($value)
	{
		if( $value )
			$this->value = $value;
		return $this;
	}
}
