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
namespace ScavixWDF\Google;

use ScavixWDF\Base\Control;
use ScavixWDF\Base\HtmlPage;

/**
 * Base class for all google controls.
 * 
 * Ensures all libraries are loaded correctly and stuff.
 * @attribute[ExternalResource('//www.google.com/jsapi')]
 */
class GoogleControl extends Control
{
	protected static $_apis = array();
	private static $_delayedHookAdded = false;
	
	/**
	 * @param string $tag Allows to specify another tag for the wrapper control, default for google controls is &lt;span&gt;
	 */
	function __initialize($tag='span')
	{
		parent::__initialize($tag);
	}
	
	/**
	 * @override
	 */
	function PreRender($args = array())
	{
		// we register a new HOOK_PRE_RENDER handler here so that it will be executed when all others are
		// finished. so derivered classes can add their loader code in PreRender as usual.
		if( count($args) > 0 )
		{
			$controller = $args[0];
			if( $controller instanceof HtmlPage )
			{
				if( !self::$_delayedHookAdded )
				{
					self::$_delayedHookAdded = true;
					register_hook(HOOK_PRE_RENDER, $this, 'AddLoaderCode');
				}
			}
		}
		return parent::PreRender($args);
	}
	
	/**
	 * @internal PreRender HOOK handler
	 */
	function AddLoaderCode($args)
	{
		$loader = array();
		foreach( self::$_apis as $api=>$definition )
		{
			list($version,$options) = $definition;
			if( isset($options['callback']) )
				$options['callback'] = "function(){ ".implode("\n",$options['callback'])." }";
			$loader[] = "google.load('$api','$version',".system_to_json($options).");";
		}
		$controller = $args[0];
		$controller->addDocReady($loader,false); // <- see the 'false'? we add these codes inline, not into the ready handler as this crashes
	}
	
	protected function _loadApi($api,$version,$options)
	{
		self::$_apis[$api] = array($version, $options);
	}
	
	protected function _addLoadCallback($api,$script)
	{
		if( !isset(self::$_apis[$api][1]['callback']) )
			self::$_apis[$api][1]['callback'] = array();
		
		if( is_array($script) )
			self::$_apis[$api][1]['callback'][] = implode("\n",$script);
		else
			self::$_apis[$api][1]['callback'][] = $script;
	}
}