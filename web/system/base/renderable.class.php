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
namespace ScavixWDF\Base;

use ScavixWDF\Reflection\ResourceAttribute;

/**
 * Base class for all HTML related stuff.
 * 
 * @attribute[Resource('jquery.js')]
 */
abstract class Renderable 
{
	var $_translate = true;
	var $_storage_id;
	var $_content = array();
	var $_script = array();

	/**
	 * Renders this control as controller.
	 * 
	 * Extending classes must implement this (<Control>, <Template>).
	 * @return string The rendered control
	 */
	abstract function WdfRenderAsRoot();
	
	/**
	 * Renders this control.
	 * 
	 * Extending classes must implement this (<Control>, <Template>).
	 * @return string The rendered control
	 */
	abstract function WdfRender();
	
	function __getContentVars(){ return array('_content'); }
	
	function __collectResources()
	{
		global $CONFIG;
		
		$min_js_file = isset($CONFIG['use_compiled_js'])?$CONFIG['use_compiled_js']:false;
		$min_css_file = isset($CONFIG['use_compiled_css'])?$CONFIG['use_compiled_css']:false;
		
		if( $min_js_file && $min_css_file )
			return array($min_css_file,$min_js_file);

		$res = $this->__collectResourcesInternal($this);
		if( !$min_js_file && !$min_css_file )
			return $res;
		
		$js = array(); $css = array();
		foreach( $res as $r )
		{
			if( ends_with($r, '.js') )
			{
				if( !$min_js_file )
					$js[] = $r;
			}
			else
			{
				if( !$min_css_file )
					$css[] = $r;
			}
		}
		
		if( $min_js_file )
		{
			$css[] = $min_js_file;
			return $css;
		}
		
		$js[] = $min_css_file;
		return $js;
	}
	
	private function __collectResourcesInternal($template)
	{
		$res = array();
		
		if( is_object($template) )
		{
			$classname = get_class($template);
			
			// first collect statics from the class definitions
			$static = ResourceAttribute::ResolveAll( ResourceAttribute::Collect($classname) );
			$res = array_merge($res,$static);
			
			if( $template instanceof Renderable )
			{
				// then check all contents and collect theis includes
				foreach( $template->__getContentVars() as $varname )
				{
					$sub = array();
					foreach( $template->$varname as $var )
					{
						if( is_object($var)|| is_array($var) )
							$sub = array_merge($sub,$this->__collectResourcesInternal($var));
					}
					$res = array_merge($res,$sub);
				}
				
				// finally include the 'self' stuff (<classname>.js,...)
				// Note: these can be forced to be loaded in static if they require to be loaded before the contents resources
				$classname = get_class_simple($template);
				//log_debug("checking res $classname");
				$parents = array(); $cnl = strtolower($classname);
				do
				{
					if( resourceExists("$cnl.css") )
						$parents[] = resFile("$cnl.css");
					if( resourceExists("$cnl.js") )
						$parents[] = resFile("$cnl.js");
					//log_debug("info",fq_class_name($classname),get_parent_class(fq_class_name($classname)));
					$classname = array_pop(explode('\\',get_parent_class(fq_class_name($classname))));
					//log_debug("  parent = $classname");
					$cnl = strtolower($classname);
				}
				while($classname != "");
				$res = array_merge($res,array_reverse($parents));
			}
		}
		elseif( is_array($template) )
		{
			foreach( $template as $var )
			{
				if( is_object($var)|| is_array($var) )
					$res = array_merge($res,$this->__collectResourcesInternal($var));
			}
		}
		return array_unique($res);
	}
}
