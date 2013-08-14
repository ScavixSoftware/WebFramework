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
namespace ScavixWDF\Reflection;

/**
 * Specifies that a resource file is needed.
 * 
 * Note this above a class to force the resouce to be automatically searched and added the resulting output.
 * <code>
 * <at>attribute[Resource('myownscript.js')]
 * <at>attribute[Resource('ineed/thisstyle.css')]
 * </code>
 */
class ResourceAttribute extends WdfAttribute
{
	var $Path;
	
	function __construct($path)
	{
		$this->Path = $path;
	}
	
	/**
	 * Resolves this resource to a URL
	 * 
	 * Will call <resFile>() to do so, so result will be callable from the current location.
	 * @return string URL to resource
	 */
	function Resolve()
	{
		return resFile($this->Path);
	}
	
	/**
	 * Will collect all Resource attributes from a given classname
	 * 
	 * Will also step down the inheritance graph to collect Resources from there.
	 * @param string|object $classname Classname or object to collect resources for
	 * @return array Array of resource attributes
	 */
	public static function Collect($classname)
	{
		$ref = WdfReflector::GetInstance($classname);
		$attrs = $ref->GetClassAttributes(array('Resource','ExternalResource'));
		$ref = $ref->getParentClass();
		$parents = $ref?self::Collect($ref->getName()):array();
		$attrs = array_merge($parents,$attrs);
		return $attrs;
	}
	
	/**
	 * Resolves an array of ResourceAttributes
	 * 
	 * Calls <ResourceAttribute::Resolve>() for each and returns an array of resolved URLs
	 * @param array $array_of_res_attr Resources to be resolved
	 * @return array An array of URLs to the resources
	 */
	public static function ResolveAll($array_of_res_attr)
	{
		$res = array();
		foreach( $array_of_res_attr as $a )
			$res[] = $a->Resolve();
		return array_unique($res);
	}
}
