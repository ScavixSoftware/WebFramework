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
namespace ScavixWDF;

/**
 * This is a wrapper/router for system (ScavixWDF) resources.
 * 
 * It tries to map *WdfResource* urls to the file in the local filessystem and writes it out using readfile().
 * This is to let users place the ScavixWDF folder outside the doc root while still beeing able to access resources in there
 * without having to create a domain for that. Natually doing that would be better because faster!
 */
class WdfResource implements ICallable
{
	/**
	 * @internal Returns a JS resource
	 * @attribute[RequestParam('res','string')]
	 */
	function js($res)
	{
		$res = explode("?",$res);
		$res = realpath(__DIR__."/../../js/".$res[0]);
		
		header('Content-Type: text/javascript');
		readfile($res);
		die();
	}
	
	/**
	 * @internal Returns a CSS resource
	 * @attribute[RequestParam('res','string')]
	 */
	function skin($res)
	{
		$res = explode("?",$res);
		$res = realpath(__DIR__."/../../skin/".$res[0]);
		
		header('Content-Type: text/css');
		readfile($res);
		die();
	}
}