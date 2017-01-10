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
 * These are unsiversal HTML attributes.
 * Each (1st dimension) array key represents an attribute and the value (array) contains
 * all tags it is allowed to be used in.
 */
$GLOBALS['html_universals'] = array(
	'class' => array('base', 'basefont', 'head', 'html', 'meta', 'param', 'script', 'style', 'title'),
	'id' => array('base', 'head', 'html', 'meta', 'script', 'style', 'title'),
	'style' => array('base', 'basefont', 'head', 'html', 'meta', 'param', 'script', 'style', 'title'),
	'title' => array('base', 'basefont', 'head', 'html', 'meta', 'param', 'script', 'style', 'title'),
	'dir' => array('applet', 'base', 'basefont', 'br', 'frame', 'frameset', 'hr', 'iframe', 'param', 'script'),
	'lang' => array('applet', 'base', 'basefont', 'br', 'frame', 'frameset', 'hr', 'iframe', 'meta', 'param', 'script'),
	'onclick' => array('applet', 'base', 'basefont', 'bdo', 'br', 'font', 'frame', 'frameset', 'head', 'html', 'iframe', 'isindex', 'param', 'script', 'style', 'title'),
	'ondblclick' => array('applet', 'base', 'basefont', 'bdo', 'br', 'font', 'frame', 'frameset', 'head', 'html', 'iframe', 'isindex', 'param', 'script', 'style', 'title'),
	'onmousedown' => array('applet', 'base', 'basefont', 'bdo', 'br', 'font', 'frame', 'frameset', 'head', 'html', 'iframe', 'isindex', 'param', 'script', 'style', 'title'),
	'onmouseup' => array('applet', 'base', 'basefont', 'bdo', 'br', 'font', 'frame', 'frameset', 'head', 'html', 'iframe', 'isindex', 'param', 'script', 'style', 'title'),
	'onmouseover' => array('applet', 'base', 'basefont', 'bdo', 'br', 'font', 'frame', 'frameset', 'head', 'html', 'iframe', 'isindex', 'param', 'script', 'style', 'title'),
	'onmousemove' => array('applet', 'base', 'basefont', 'bdo', 'br', 'font', 'frame', 'frameset', 'head', 'html', 'iframe', 'isindex', 'param', 'script', 'style', 'title'),
	'onmouseout' => array('applet', 'base', 'basefont', 'bdo', 'br', 'font', 'frame', 'frameset', 'head', 'html', 'iframe', 'isindex', 'param', 'script', 'style', 'title'),
	'onkeypress' => array('applet', 'base', 'basefont', 'bdo', 'br', 'font', 'frame', 'frameset', 'head', 'html', 'iframe', 'isindex', 'param', 'script', 'style', 'title'),
	'onkeydown' => array('applet', 'base', 'basefont', 'bdo', 'br', 'font', 'frame', 'frameset', 'head', 'html', 'iframe', 'isindex', 'param', 'script', 'style', 'title'),
	'onkeyup' => array('applet', 'base', 'basefont', 'bdo', 'br', 'font', 'frame', 'frameset', 'head', 'html', 'iframe', 'isindex', 'param', 'script', 'style', 'title'),
	'contextmenu' => array()
);
/**
 * These are HTML tags.
 * Each (1st dimension) array key represents a tag and the value (array) contains
 * all attributes that are allowed to use with it.
 */
$GLOBALS['html_attributes'] = array(
	'a' => array('Bedeutung','Attribut','accesskey','charset','coords','href','hreflang','name','onblur','onfocus','rel','rev','shape','tabindex','target','type'),
	'applet' => array('align','alt','archive','code','codebase','height','hspace','name','object','vspace','width'),
	'area' => array('alt','accesskey','coords','href','nohref','onblur','onfocus','shape','tabindex','target'),
	'base' => array('href','target'),
	'basefont' => array('color','face','size'),
	'bdo' => array('dir'),
	'blockquote' => array('cite'),
	'body' => array('alink','background','bgcolor','link','onload','onunload','text','vlink'),
	'br' => array('clear'),
	'button' => array('accesskey','disabled','name','onblur','onfocus','tabindex','type','value'),
	'caption' => array('align'),
	'col' => array('align','char','charoff','span','valign','width'),
	'colgroup' => array('align','char','charoff','span','valign','width'),
	'del' => array('cite','datetime'),
	'dir' => array('compact'),
	'div' => array('align'),
	'dl' => array('compact'),
	'font' => array('color','face','size'),
	'form' => array('action','accept','accept-charset','enctype','method','name','onreset','onsubmit','target'),
	'frame' => array('frameborder','longdesc','marginwidth','marginheight','name','noresize','scrolling','src'),
	'frameset' => array('cols','onload','onunload','rows'),
	'h1' => array('align'),
	'h2' => array('align'),
	'h3' => array('align'),
	'h4' => array('align'),
	'h5' => array('align'),
	'h6' => array('align'),
	'head' => array('profile'),
	'hr' => array('align','noshade','size','width'),
	'html' => array('version'),
	'iframe' => array('align','frameborder','height','longdesc','marginwidth','marginheight','name','scrolling','src','width','type'),
	'img' => array('align','alt','border','height','hspace','ismap','longdesc','name','src','usemap','vspace','width','onload'),
	'input' => array('accept','accesskey','align','alt','checked','disabled','ismap','maxlength','name','onblur','onchange','onfocus','onselect','readonly','size','src','tabindex','type','usemap','value','placeholder','autocomplete'),
	'ins' => array('cite','datetime'),
	'isindex' => array('prompt'),
	'label' => array('accesskey','for','onblur','onfocus'),
	'legend' => array('accesskey','align'),
	'li' => array('type','value'),
	'link' => array('charset','href','hreflang','media','rel','rev','target','type'),
	'map' => array('name'),
	'menu' => array('compact'),
	'meta' => array('name','content','http-equiv','scheme'),
	'object' => array('align','archive','border','classid','codebase','codetype','data','declare','height','hspace','name','standby','tabindex','type','usemap','vspace','width'),
	'ol' => array('compact','start','type'),
	'optgroup' => array('disabled','label'),
	'option' => array('disabled','label','selected','value'),
	'p' => array('align'),
	'param' => array('id','name','value','valuetype','type'),
	'pre' => array('width'),
	'q' => array('cite'),
	'script' => array('charset','defer','event','language','for','src','type'),
	'select' => array('disabled','multiple','name','onblur','onchange','onfocus','size','tabindex','title'),
	'style' => array('media','title','type'),
	'table' => array('align','border','bgcolor','cellpadding','cellspacing','frame','rules','summary','width'),
	'tbody' => array('align','char','charoff','valign'),
	'td' => array('abbr','align','axis','bgcolor','class','char','charoff','colspan','headers','height','nowrap','rowspan','scope','valign','width'),
	'textarea' => array('accesskey','cols','disabled','name','onblur','onchange','onfocus','onselect','readonly','rows','tabindex','value'),
	'tfoot' => array('align','char','charoff','valign'),
	'th' => array('abbr','align','axis','bgcolor','char','charoff','colspan','headers','height','nowrap','rowspan','scope','valign','width'),
	'thead' => array('align','char','charoff','valign'),
	'tr' => array('align','bgcolor','char','charoff','valign'),
	'ul' => array('compact','type'),
	'menu' => array('type'),
	'menuitem' => array('label'),
	'audio' => array('controls','autoplay','loop','preload','src','onload'),
	'source' => array('src','type','onload'),
);

/**
 * Tags that need a closing tag wether there's content or not.
 */
$GLOBALS['html_close_tag_needed'] = array(
	'span','textarea','div','td','select','audio','iframe'
);
$GLOBALS['html_close_tag_needed'] = array_combine($GLOBALS['html_close_tag_needed'], $GLOBALS['html_close_tag_needed']);

/**
 * Tags that will NOT be echoed when there's no content
 */
$GLOBALS['html_skip_if_empty'] = array(
	'tbody','thead','tfoot','tr'
);
$GLOBALS['html_skip_if_empty'] = array_combine($GLOBALS['html_skip_if_empty'], $GLOBALS['html_skip_if_empty']);

/**
 * Base class for interactive webpage content like AJAX TextInputs.
 * 
 * @attribute[Resource('jquery.js')]
 */
class Control extends Renderable
{
	var $Tag = "";
	
	var $_css = array();
	var $_attributes = array();
	var $_data_attributes = array();
	
	var $_extender = array();

	var $_skipRendering = false;
	
	function __getContentVars(){ return array_merge(parent::__getContentVars(),array('_extender')); }

	/**
	 * The one and only constructor for all subclasses.
	 * 
	 * These must not implement a constructor but the __initialize method.
	 */
	function __construct()
	{
		if( !hook_already_fired(HOOK_PRE_RENDER) )
		{
			register_hook(HOOK_PRE_RENDER,$this,"PreRender");
		}
		else
			if( !hook_already_fired(HOOK_POST_EXECUTE) )
			{
				register_hook(HOOK_POST_EXECUTE,$this,"PreRender");
			}

		if( !unserializer_active() )
		{
			create_storage_id($this);
			$args = func_get_args();
			if( count($args)!=1 || $args[0]!=='Make is calling so skip __initialize call')
				system_call_user_func_array_byref($this, '__initialize', $args);
		}
	}

	/**
	 * Override this method instead of writing a constructor.
	 * 
	 * @param string $tag The HTML Tag of this control. Default ""
	 */
	function __initialize($tag = "")
	{
		$this->Tag = strtolower($tag);
        $class = strtolower(get_class_simple($this));

        if( $class != $this->Tag && $class != "control" )
            $this->class = $class;
	}

	/**
	 * @internal Magic method __get.
	 * See [Member overloading](http://ch2.php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members)
	 */
	function __get($name)
	{
		// automatically set the id when it's required (ex:for ajax)
		if( $name == "id" && !isset($this->_attributes[$name]) )
			$this->_attributes[$name] = $this->_storage_id;

		if( isset($this->_attributes[$name]) )
			return $this->_attributes[$name];

		foreach( $this->_extender as &$ex)
		{
			if( property_exists($ex,$name) )
				return $ex->$name;
		}
		
		return null;
	}

	/**
	 * @internal Magic method __set.
	 * See [Member overloading](http://ch2.php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members)
	 */
	function __set($varname,$value)
	{
		if( !$this->IsAllowedAttribute($varname) )
		{
			foreach( $this->_extender as &$ex)
			{
				if( property_exists($ex,$varname) )
				{
					$ex->$varname = $value;
					return;
				}
			}
			WdfException::Raise("'$varname' is not an allowed attriute for a control of type '{$this->Tag}'");
		}
		$this->_attributes[$varname] = $value;

		if( strtolower($varname) == "id" )
			$this->_storage_id = $value;
	}
	
	/**
	 * @internal Magic method __call.
	 * See [Member overloading](http://ch2.php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members)
	 */
	public function __call($name, $arguments)
	{
        foreach( $this->_extender as &$ex)
		{
			if( system_method_exists($ex,$name) )
				return system_call_user_func_array_byref($ex, $name, $arguments);
		}
		WdfException::Raise("Call to undefined method '$name' on object of type '".get_class($this)."'");
    }

	/**
	 * @internal Magic method __isset.
	 * See [Member overloading](http://ch2.php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members)
	 */
	public function __isset($name)
	{
		if( property_exists($this,$name) )
			return true;

		if( array_key_exists($name,$this->_attributes) )
			return true;

        foreach( $this->_extender as &$ex)
		{
			if( property_exists($ex,$name) )
				return true;
		}
		return false;
    }

	/**
	 * @internal Checks if this class implements a method.
	 * @param string $name name of the Method
	 * @return bool true|false
	 */
	public function __method_exists($name)
	{
		if( system_method_exists($this,$name) )
			return true;

        foreach( $this->_extender as &$ex)
		{
			if( system_method_exists($ex,$name) )
				return true;
		}
		return false;
    }
	
	/**
	 * Static creator method
	 * 
	 * This is cabable of creating derivered classes too:
	 * <code php>
	 * Control::Make('div')->content('Doh!');
	 * TextInput::Make()->css('width','300px');
	 * </code>
	 * @param string $tag HTML tag name (like div, span, a, img,...)
	 * @return Control The created control
	 */
	public static function Make($tag=false)
    {
		$className = get_called_class();
		$res = new $className('Make is calling so skip __initialize call');
		$args = func_get_args();
		system_call_user_func_array_byref($res, '__initialize', $args);
		return $res;
	}

	/**
	 * Adds a CSS property to the control.
	 * 
	 * If value is an integer (or numeric string like '12') 'px' will be added.
	 * @param string $name Name of the CSS property (like width, background-image,...)
	 * @param string $value Value of the CSS property
	 * @return Control `$this`
	 */
	function css($name,$value)
	{
		$name = strtolower($name);
		$this->_css[$name] = is_numeric($value)?$value.'px':$value;
		return $this;
	}

	/**
	 * Checks whether this control needs a closing tag (in HTML code).
	 * 
	 * @return bool true if needed
	 */
	protected function CloseTagNeeded()
	{
		return (isset($GLOBALS['html_close_tag_needed'][$this->Tag]) || (count($this->_content) > 0));
	}

	/**
	 * Checks if the given attribute is valid for a html element like this (depending on tag).
	 * 
	 * @param string $attr The attribute to check
	 * @return bool true if valid
	 */
	protected function IsAllowedAttribute($attr)
	{
		$attr = strtolower($attr);
		$isattr = isset($GLOBALS['html_attributes'][$this->Tag]);
		if( $isattr && !isset($GLOBALS['html_attributes-keys'][$this->Tag]))
			$GLOBALS['html_attributes-keys'][$this->Tag] = array_flip($GLOBALS['html_attributes'][$this->Tag]);
		if($isattr && isset($GLOBALS['html_attributes-keys'][$this->Tag][$attr]) )
			return true;
		else
		{
			if( isset($GLOBALS['html_universals'][$attr]) )
			{
				if(!isset($GLOBALS['html_universals-keys'][$attr]))
					$GLOBALS['html_universals-keys'][$attr] = array_flip($GLOBALS['html_universals'][$attr]);
				if(!isset($GLOBALS['html_universals-keys'][$attr][$this->Tag]))
					return true;
			}
		}
		return false;
	}

	/**
	 * Will be executed on HOOK_PRE_RENDER.
	 * 
	 * Adds this controls init code to rendering <HtmlPage> if root is of that type.
	 * @internal
	 */
	function PreRender($args=array())
	{
		if( $this->_skipRendering )
			return;
        
		if( count($args) > 0 && count($this->_script) > 0 )
		{
			if( !$this->_parent )
				return;// log_debug("Skipping ready-script addition for control without parent",$this);
			$controller = $args[0];
			if( $controller instanceof HtmlPage )
				$controller->addDocReady(implode("\n",$this->_script));
		}
	}

	/**
	 * @shortcut <Control::script>
	 */
	function addDocReady($js_code)
	{
		$this->script($js_code);
	}

	/**
	 * @override
	 */
	function WdfRenderAsRoot()
	{
		if( !hook_already_fired(HOOK_PRE_RENDER) )
		{
			$this->_skipRendering = true;
			execute_hooks(HOOK_PRE_RENDER,array($this));
		}
		return $this->WdfRender();
	}

	/**
	 * @override
	 */
	function WdfRender()
	{
		$attr = array();
		foreach( $this->_attributes as $name=>$value )
		{
			if($name{0} != "_")
				$attr[] = "$name=\"".str_replace("\"","&#34;",$value)."\"";
		}
		foreach( $this->_data_attributes as $name=>$value )
			$attr[] = "data-$name='".str_replace("'","\\'",$value)."'";
		
		$content = system_render_object_tree($this->_content);

		if( isset($GLOBALS['html_skip_if_empty'][$this->Tag]) )
			if( trim(implode(" ",$content)) == "" )
				return "";

		$css = array();
		foreach( $this->_css as $key=>$val )
			$css[] = "$key:$val;";

		$attr = count($attr)>0?" ".implode(" ",$attr):"";
		$css = count($css)>0?" style=\"".implode(" ",$css)."\"":"";
		$content = count($content)>0?implode("",$content):"";

		if( $this->Tag )
		{
			if( $content || $this->CloseTagNeeded() )
				$res = "<{$this->Tag}{$attr}{$css}>{$content}</{$this->Tag}>";
			else
				$res = "<{$this->Tag}{$attr}{$css}/>";
		}
		else
			$res = "{$content}";
			
		if( system_is_ajax_call() && count($this->_script)>0 )
        {
            $this->_script[] = "$('#{$this->id}').on('remove',function(){ $('[data-wdf-remove-with=\"{$this->id}\"]').remove(); });";
			$res .= "<script data-wdf-remove-with='{$this->id}'> ".implode("\n",$this->_script)."</script>";
        }
		return $res;
	}

	/**
	 * Extends this control with additional functionality.
	 * 
	 * @param object $extender Object that shall extend this
	 * @return Control `$this`
	 */
	function Extend($extender)
	{
		$key = get_class($extender);
		if( array_key_exists($key,$this->_extender) )
			return;
		$this->_extender[$key] = $extender;
		return $this;
	}

	/**
	 * Adds a value to the 'class' attribute.
	 * 
	 * Note: you may pass multiple classes at once in a tring space separated: 'cls1 cls2'
	 * @param string $class CSS class(es)
	 * @return Control `$this`
	 */
	function addClass($class)
	{
        $class = array_merge(explode(" ",$this->class),explode(" ",$class));
		$this->class = trim(implode(" ",array_unique($class)));
		return $this;
	}

	/**
	 * Removes a value from the 'class' attribute.
	 * 
	 * @param string $class CSS class
	 * @return Control `$this`
	 */
	function removeClass($class)
	{
		$this->class = str_replace($class,"",$this->class);
		$this->class = str_replace("  "," ",trim($this->class));
		return $this;
	}
	
	/**
	 * Set a valud to a data-$name attribute.
	 * 
	 * Those can be accessed in JS easily using jQuery.data method
	 * @param string $name Data name
	 * @param mixed $value Data value (<system_to_json> will be used for arrays and objects) 
	 * @return Control `$this`
	 */
	function setData($name,$value)
	{
		if( is_array($value) || is_object($value) )
			$this->_data_attributes[$name] = system_to_json($value);
		else
			$this->_data_attributes[$name] = $value;
		return $this;
	}
	
	/**
	 * Removes a data-$name attribute.
	 * 
	 * @param string $name Data name
	 * @return Control `$this`
	 */
	function removeData($name)
	{
		if( isset($this->_data_attributes[$name]) )
			unset($this->_data_attributes[$name]);
		return $this;
	}
	
	/**
	 * Attribute handling.
	 * 
	 * This method may be used in four different ways:
	 * 1. to get all attributes
	 * 2. to get one attribute
	 * 3. to set one attribute
	 * 4. to set many attributes
	 * 
	 * To achieve this pass different parameters into like this:
	 * 1. $c->attr() returns all attributes
	 * 2. $c->attr('name') returns the 'name' attributes value
	 * 3. $c->attr('name','mycontrol') sets the 'name' attribute values
	 * 4. $c->attr(array('name'=>'myname','href'=>'my.domain')) sets 'name' and 'href' attribute values
	 * 
	 * Note: Will return `$this` in cases 3. and 4. (the set cases).
	 * @return mixed `$this`, an attribute value or an array of attribute values
	 */
	function attr()
	{
		$cnt = func_num_args();
		switch( $cnt )
		{
			case 0:
				return $this->_attributes;
			case 1: 
				$name = func_get_arg(0);
				if( is_array($name) )
				{
					foreach( $name as $n=>$v )
						$this->attr($n,$v);
					return $this;
				}
				return $this->$name;
			case 2: 
				$name = func_get_arg(0);
				$this->$name = func_get_arg(1);
				return $this;
		}
		WdfException::Raise("Control::attr needs 0,1 or 2 parameters");
	}
    
    function setTitle($title)
    {
        return $this->attr('title',$title);
    }
}
