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
namespace ScavixWDF\Base;

use ScavixWDF\WdfException;

/**
 * Building blocks of web pages.
 * 
 * Each template consist of a logic part and a layout part. The logic part is optional and can be handled
 * by this (base) class (see <Template::Make>).
 * @attribute[Resource('jquery.js')]
 */
class Template extends Renderable
{
	var $_data = array();
	var $file = "";
	
	function __getContentVars(){ return array_merge(parent::__getContentVars(),array('_data')); }

	/**
	 * Creates a template with layout only.
	 * 
	 * Sometimes you just want to separate parts of your layout without giving them some special logic.
	 * You may just store them as *.tpl.php files and create a template from them like this:
	 * <code php>
	 * // assuming template file is 'templates/my.tpl.php'
	 * $tpl = Template::Make('my');
	 * $tpl->set('myvar','I am just layout');
	 * <code>
	 * @param string $template_basename Name of the template
	 * @return Template The created template
	 */
	static function Make($template_basename=false)
	{
		$className = get_called_class();
		if( $template_basename && file_exists($template_basename) )
			$tpl_file = $template_basename;
		else
		{
			if( !$template_basename )
				$template_basename = $className;
			$tpl_file = false;
			foreach( array_reverse(cfg_get('system','tpl_ext')) as $tpl_ext )
			{
				$tpl_file = __search_file_for_class($template_basename,$tpl_ext);
				if( $tpl_file )
					break;
			}
		}
		if( !$tpl_file )
			WdfException::Raise("Template not found: $template_basename");
		
		$res = new $className($tpl_file);
		return $res;
	}
	
	/**
	 * The one and only constructor for all subclasses.
	 * 
	 * These must not implement a constructor but the __initialize method.
	 */
	function __construct()
	{
		if( !hook_already_fired(HOOK_PRE_RENDER) )
			register_hook(HOOK_PRE_RENDER,$this,"PreRender");
		elseif( !hook_already_fired(HOOK_POST_EXECUTE) )
			register_hook(HOOK_POST_EXECUTE,$this,"PreRender");
		
		if( !unserializer_active() )
		{
			$args = func_get_args();
			system_call_user_func_array_byref($this, '__initialize' ,$args);
		}
	}

	/**
	 * Override this method instead of writing a constructor.
	 * 
	 * @param string $file Template file for this class. Usually '' (empty string)
	 */
	function __initialize($file = "")
	{
		$this->file = $file;

		if( !unserializer_active() )
		{
			create_storage_id($this);
			$this->set('id',$this->_storage_id);
		}
	}
	
	/**
	 * @internal Magic method __get.
	 * See [Member overloading](http://ch2.php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members)
	 */
	function __get($name)
	{
		if( isset($this->_data[$name]) )
			return $this->_data[$name];
		return null;
	}
	
	/**
	 * Will be executed on HOOK_PRE_RENDER.
	 * 
	 * Prepares the template for output.
	 * @internal
	 */
	function PreRender($args=array())
	{
		if( count($args) > 0 && count($this->_script) > 0 )
		{
			$controller = $args[0];
			if( $controller instanceof HtmlPage )
			{
				$controller->addDocReady(implode("\n",$this->_script)."\n");
			}
		}
	}

	/**
	 * Set a variable for use in template file.
	 * 
	 * @param string $name Var can be use in template under this name
	 * @param mixed $value The value
	 * @return Template `$this`
	 */
	public function set($name, $value)
	{
		if( $value instanceof Renderable )
			$value->_parent = $this;
		$this->_data[$name] = $value;
		if( $name == 'id' )
			$this->_storage_id = $value;
		return $this;
	}
	
	/**
	 * Adds a value to an already defined var.
	 * 
	 * If $name is not already an array it will be converted to one.
	 * <code php>
	 * $tpl->set('a','one');
	 * $tpl->add2var('a','two');
	 * // $a is now array('one','two')
	 * $tpl->set('a','three');
	 * // $a is now 'three'
	 * $tpl->add2var('b','four');
	 * // $b is now array('four')
	 * </code>
	 * @param string $name Variable name
	 * @param mixed $value Value to add
	 * @return Template `$this`
	 */
	public function add2var($name, $value)
	{
		if( $value instanceof Renderable )
			$value->_parent = $this;
		if( !isset($this->_data[$name]) )
			$this->_data[$name] = array($value);
		elseif( !is_array($this->_data[$name]) )
			$this->_data[$name] = array($this->_data[$name],$value);
		else
			$this->_data[$name][] = $value;
		return $this;
	}

	/**
	 * Sets all template variables.
	 * 
	 * @param array $vars Key=>Value pairs of variables
	 * @param bool $clear Overwrite the whole vars (defaults to false)
	 * @return Template `$this`
	 */
	function set_vars($vars, $clear = false)
	{
		if($clear) {
			$this->_data = $vars;
		}
		else {
			if(is_array($vars))
				$this->_data = array_merge($this->_data, $vars);
			else
				$this->_data[] = $vars;
		}
		return $this;
	}
	
	/**
	 * Gets a variables value.
	 * 
	 * @param string $name Var name
	 * @return mixed Value of var
	 */
	function get($name)
	{
		return isset($this->_data[$name])?$this->_data[$name]:null;
	}
	
	/**
	 * Gets all variables.
	 * 
	 * @return array All variables
	 */
	function get_vars()
	{
		return $this->_data;
	}
	
	/**
	 * @override
	 */
	function WdfRenderAsRoot()
	{
		if( !hook_already_fired(HOOK_PRE_RENDER) )
			execute_hooks(HOOK_PRE_RENDER,array($this));
        return $this->WdfRender();
	}

	/**
	 * @override
	 */
	function WdfRender()
	{
		$tempvars = system_render_object_tree($this->get_vars());
        $scriptcnt = count($this->_script);

		foreach( $GLOBALS as $un_common_k_e_y_value=>&$un_common_v_a_l_value )
			$$un_common_k_e_y_value = $un_common_v_a_l_value;

		$buf = array();
		foreach( $tempvars as $un_common_k_e_y_value=>&$un_common_v_a_l_value )
		{
			if( isset($$un_common_k_e_y_value) )
				$buf[$un_common_k_e_y_value] = $$un_common_k_e_y_value;
			$$un_common_k_e_y_value = $un_common_v_a_l_value;
		}

		if( ($this instanceof HtmlPage) && stripos($this->file,"htmlpage.tpl.php") !== false )
		{
			$__template_file = __autoload__template($this,$this->SubTemplate?$this->SubTemplate:"");
			if( $__template_file === false )
				WdfException::Raise("SubTemplate for class '".get_class($this)."' not found: ".$this->file,$this->SubTemplate);

			if( stripos($__template_file,"htmlpage.tpl.php") === false )
			{
				ob_start();
				require($__template_file);
				$sub_template_content = ob_get_contents();
				ob_end_clean();
			}
			$this->file = __DIR__."/htmlpage.tpl.php";
		}

		$__template_file = __autoload__template($this,$this->file);
		if( $__template_file === false )
			WdfException::Raise("Template for class '".get_class($this)."' not found: ".$this->file);

        $GLOBALS['current_rendering_template'] = $this;
		ob_start();
		require($__template_file);
		$contents = ob_get_contents();
		ob_end_clean();
        unset($GLOBALS['current_rendering_template']);

		foreach( $tempvars as $un_common_k_e_y_value=>&$un_common_v_a_l_value )
			unset($$un_common_k_e_y_value);
		foreach( $buf as $un_common_k_e_y_value=>&$un_common_v_a_l_value )
			$$un_common_k_e_y_value = $un_common_v_a_l_value;
		
		if( system_is_ajax_call() )
        {
            if( count($this->_script)>0 )
    			$contents .= "<script> ".implode("\n",$this->_script)."</script>";
        }
        elseif( $scriptcnt < count($this->_script) ) 
        {
            $contents .= "<script> ".implode("\n",array_slice($this->_script,$scriptcnt))."</script>";
        }
        
		return $contents;
	}
}
