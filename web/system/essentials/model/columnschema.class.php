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
 * Schema of a database column
 * 
 * Will be created from the DB and used to automatically detect columns and their types.
 */
class ColumnSchema
{
	var $Name;
	var $Type;
	var $Size;
	var $Null;
	var $Key;
	var $Default;
	var $Extra;
	
    function __construct($name)
    {
        $this->Name = $name;
    }
	
	/**
	 * Checks if this column belongs to the primary key
	 * 
	 * In fact just `return $this->Key == "PRIMARY";`
	 * @return bool true or false
	 */
	function IsPrimary()
	{
		return $this->Key == "PRIMARY";
	}
}
