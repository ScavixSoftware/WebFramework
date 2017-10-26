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
namespace ScavixWDF\Reflection;

/**
 * Base class for ScavixWDF annotation implementation.
 * 
 * All attributes must inherit this class and can the be noted as attributes to classes and/or methods like this:
 * <at>attribute[classname(constructor arguments)]
 * - note that an argument class is named MyFirstAttribute the classname may be MyFirst or MyFirstAttribute
 * - note that the part in the brackets[] will be eval'd, so stay in PHP syntax in there.
 * - note that you may leave out the brackets () if there are not required constructor arguments in your attribute
 * sample
 * <at>attribute[MyFirst('bla')]
 * <at>attribute[MyFirstAttribute()]
 * <at>attribute[MyFirst]
 * 
 * Some more samples can be found at <WdfReflector::GetClassAttributes>
 */
class WdfAttribute
{
	var $Reflector = null;
	var $Class = null;
	var $Object = null;
	var $Method = null;
	var $Field = null;
    
	function __construct() {}
	
	function __sleep()
	{
		$this->Reflector = $this->Object = null;
		return array_keys(get_object_vars($this));
	}
}
