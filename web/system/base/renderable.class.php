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
use ScavixWDF\WdfException;

/**
 * Base class for all HTML related stuff.
 * 
 * @attribute[Resource('jquery.js')]
 */
abstract class Renderable 
{
	var $_translate = true;
	var $_storage_id;
	var $_parent = false;
	var $_content = array();
	var $_script = array();

	/**
	 * Renders this Renderable as controller.
	 * 
	 * Extending classes must implement this (<Control>, <Template>).
	 * @return string The rendered object
	 */
	abstract function WdfRenderAsRoot();
	
	/**
	 * Renders this Renderable.
	 * 
	 * Extending classes must implement this (<Control>, <Template>).
	 * @return string The rendered object
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
				
				// for Template class check the template file too
				if( $template instanceof Template )
				{
					$fnl = strtolower(array_shift(explode(".",basename($template->file))));
					if( get_class_simple($template,true) != $fnl )
					{
						if( resourceExists("$fnl.css") )
							$res[] = resFile("$fnl.css");
						if( resourceExists("$fnl.js") )
							$res[] = resFile("$fnl.js");
					}
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
	
	/**
	 * Captures `$this` to the given `$variable`.
	 * 
	 * This may me used to capture an instance from a method chain like this:
	 * <code php>
	 * TextInput::Make()->capture($tb)->appendTo($some_container)->par()->prepend($tb->CreateLabel('enter mail:'));
	 * </code>
	 * @param Renderable $variable Variable to assign `$this` to
	 * @return Renderable `$this`
	 */
	function capture(&$variable)
	{
		$variable = $this;
		return $this;
	}
	
	/**
	 * Adds content to the Renderable.
	 * 
	 * Note that this will not return `$this` but the $content.
	 * This allows for method chaining like this:
	 * <code php>
	 * $this->content( new Control('div') )->css('border','1px solid red')->addClass('mydiv')->content('DIVs content');
	 * </code>
	 * @param mixed $content The content to be added
	 * @param bool $replace if true replaces the whole content.
	 * @return mixed The content added
	 */
	function &content($content,$replace=false)
	{
		if( $content instanceof Renderable )
			$content->_parent = $this;
		if( !$replace && is_array($content) )
			foreach( $content as &$c )
				$this->content($c);
		elseif( $replace )
		{
			foreach( $this->_content as &$c )
				if( $c instanceof Renderable )
					$c->_parent = false;
			$this->_content = is_array($content)?$content:array($content);
		}
		else
			$this->_content[] = $content;
		return $this->_content[count($this->_content)-1];
	}
	
	/**
	 * Clears all contents.
	 * 
	 * @return Renderable `$this`
	 */
	function clearContent()
	{
		foreach( $this->_content as &$c )
			if( $c instanceof Renderable )
				$c->_parent = false;
		$this->_content = array();
		return $this;
	}
	
	/**
	 * Gets the number of contents.
	 * 
	 * @return int Length of the contents array
	 */
	function length()
	{
		return count($this->_content);
	}
	
	/**
	 * Gets the content at index $index.
	 * 
	 * @param int $index Zero based index of content to get
	 * @return mixed Content at index $index
	 */
	function get($index)
	{
		if( isset($this->_content[$index]) )
			return $this->_content[$index];
		WdfException::Raise("Index out of bounds: $index");
	}
	
	/**
	 * Returns the first content.
	 * 
	 * Note that this does not behave like <Renderable::get>(0) because it wont throw an <Exception>
	 * when there's no content, but return a new empty <Control> object.
	 * @return Renderable First content or new empty <Control>
	 */
	function first()
	{
		if( isset($this->_content[0]) )
			return $this->_content[0];
		return log_return("Renderable::first() is empty",new Control());
	}
	
    /**
	 * Returns the last content.
	 * 
	 * Note that this does not behave like <Renderable::get>(&lt;last_index&gt;) because it wont throw an <Exception>
	 * when there's no content at last_index, but return a new empty <Control> object.
	 * @return Renderable Last content or new empty <Control>
	 */
	function last()
	{
		if( count($this->_content)>0 )
			return $this->_content[count($this->_content)-1];
		return log_return("Renderable::last() is empty",new Control());
	}
	
	/**
	 * Returns this Renderables parent object.
	 * 
	 * Note that this will throw an <Exception> when `$this` has not (yet) been added to another <Renderable>.
	 * @return Renderable Parent object
	 */
	function par()
	{
		if( !($this->_parent instanceof Renderable) )
			WdfException::Raise("Parent must be of type Renderable");
		return $this->_parent;
	}
	
	/**
	 * Return `$this` objects direct predecessor.
	 * 
	 * Checks the parents content for `$this` and returns the object that was inserted directly before `$this`.
	 * Note that this method may throw an <Exception> when there's no parent or if `$this` is the first child.
	 * @return Renderable This objects predecessor in it's parent's content
	 */
	function prev()
	{
		$i = $this->par()->indexOf($this);
		return $this->par()->get($i-1);
	}

	/**
	 * Return `$this` objects direct successor.
	 * 
	 * Checks the parents content for `$this` and returns the object that was inserted directly after `$this`.
	 * Note that this method may throw an <Exception> when there's no parent or if `$this` is the last child.
	 * @return Renderable This objects successor in it's parent's content
	 */
	function next()
	{
		$i = $this->par()->indexOf($this);
		return $this->par()->get($i+1);
	}
	
	/**
	 * Appends content to this Renderable.
	 * 
	 * This works exactly as <Renderable::content> but will return `$this` instead of the appended content.
	 * @param mixed $content The content to be appended
	 * @return Renderable `$this`
	 */
	function append($content)
	{
		$this->content($content);
		return $this;
	}
	
	/**
	 * Prepends something to the contents of this Renderable.
	 * 
	 * @param mixed $content Content to be prepended
	 * @return Renderable `$this`
	 */
	function prepend($content)
	{
		return $this->insert($content,0);
	}
	
	/**
	 * Inserts something to the contents of this Renderable.
	 * 
	 * @param mixed $content Content to be prepended
	 * @param int $index Zero base index where to insert
	 * @return Renderable `$this`
	 */
	function insert($content,$index)
	{
		if( $index instanceof Renderable )
		{
			$index = $this->indexOf($index);
			if( $index < 0 )
				WdfException::Raise("Cannot insert because index not found");
		}
		$buf = $this->_content;
		$this->_content = array();
		$i = 0;
		foreach( $buf as $b )
		{
			if( $i++ == $index )
				$this->content($content);
			$this->_content[] = $b;
		}
		return $this;
	}
	
	/**
	 * Returns the zero based index of `$content`.
	 * 
	 * Checks the content array for the given `$content` and returns it's index of found.
	 * Returns -1 of not found.
	 * @param mixed $content Content to search for
	 * @return int Zero based index or -1 if not found
	 */
	function indexOf($content)
	{
		$cnt = count($this->_content);
		for($i=0; $i<$cnt; $i++)
			if( $this->_content[$i] == $content )
				return $i;
		return -1;
	}
	
	/**
	 * Wraps this Renderable into another one.
	 * 
	 * Not words, just samples:
	 * <code php>
	 * $wrapper = new Control('div');
	 * $inner = new Control('span');
	 * $inner->content('INNER');
	 * $inner->wrap($wrapper)->content("I am below 'INNER'");
	 * // or
	 * $inner = new Control('span');
	 * $inner->content('INNER');
	 * $inner->wrap('div')->content("I am below 'INNER'");
	 * // or
	 * $inner = new Control('span');
	 * $inner->content('INNER');
	 * $inner->wrap(new Control('div'))->content("I am below 'INNER'");
	 * </code>
	 * @param mixed $tag_or_obj String or <Renderable>, see samples
	 * @return Renderable The (new) wrapping control
	 */
	function wrap($tag_or_obj='')
	{
		$res = ($tag_or_obj instanceof Renderable)?$tag_or_obj:new Control($tag_or_obj);
		$res->content($this);
		return $res;
	}
	
	/**
	 * Append this Renderable to another Renderable.
	 * 
	 * @param mixed $target Object of type <Renderable>
	 * @return Renderable `$this`
	 */
	function appendTo($target)
	{
		if( ($target instanceof Renderable) )
			$target->content($this);
		else
			WdfException::Raise("Target must be of type Renderable");
		return $this;
	}
	
	/**
	 * Adds this Renderable before another Renderable.
	 * 
	 * In fact it will be inserted before the other Renderable into the other Renderables parent.
	 * @param Renderable $target Object of type <Renderable>
	 * @return Renderable `$this`
	 */
	function insertBefore($target)
	{
		if( ($target instanceof Renderable) )
			$target->par()->insert($this,$target);
		else
			WdfException::Raise("Target must be of type Renderable");
		return $this;
	}
	
	/**
	 * Inserts content after this element.
	 * 
	 * @param mixed $content Content to be inserted
	 * @return Renderable `$this`
	 */
	function after($content)
	{
		$this->par()->insert($content,$this->par()->indexOf($this)+1);
		return $this;
	}
}
