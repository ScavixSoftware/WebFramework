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

/**
 * Wraps ReflectionClass and provides additional functionality regarding Attributes and DocComments
 * 
 * This is central class as it ensures correct DocComment parsing even if a bytecode cache (like APC) is active and removing them.
 * There's also intensive caching active to improve speed.
 */
class System_Reflector extends ReflectionClass
{
	var $_attribute_cache = array();
	protected $Instance = false;
	protected $Classname = false;

	public function __construct($classname)
	{
		if( !isset($GLOBALS['reflector_cache']) )
			$GLOBALS['reflector_cache'] = array();

		parent::__construct($classname);
	}

	/**
	 * Create a System_Reflector instance
	 * 
	 * Return a reflector for the given classname or object.
	 * @param string|object $classname Classname or object to be reflected
	 * @return System_Reflector A new instance of type System_Reflector
	 */
	public static function &GetInstance($classname)
	{
		if( is_object($classname) )
		{
			$inst = $classname;
			$classname = get_class($classname);
		}
		$classnamel = strtolower($classname);

		if( isset($GLOBALS['reflector_cache']) && isset($GLOBALS['reflector_cache'][$classnamel]) )
		{
			$res = $GLOBALS['reflector_cache'][$classnamel];
			if( isset($inst) )
				$res->Instance = $inst;
			return $res;
		}
		$res = new System_Reflector($classname);

		if( isset($inst) )
			$res->Instance = $inst;
		$res->Classname = $res->getName();

		// now using a filemtime cache to check if an update is needed
		$fn = $res->getFileName();
		$ftime = filemtime($fn);
		$mtime = cache_get("filemtime_$fn");
		if( ($mtime === false) || ($ftime > $mtime) )
		{
//			log_debug("Updating cache for $fn (ftime: $ftime mtime: $mtime");
			$res->UpdateCache();
			cache_set("filemtime_$fn",$ftime);
		}
//		else
//			log_debug("Using cache (".md5($fn).") for $fn");

		$GLOBALS['reflector_cache'][$classnamel] = $res;
		return $res;
	}

	private function UpdateCache()
	{
		$this->_getComment();
		$methods = $this->getMethods();
		foreach( $methods as &$refmeth )
		{
			if( $refmeth->getDeclaringClass()->getName() == $this->getName() )
				$this->_getComment($refmeth->getName());
		}
	}

	private function _getCacheKey($name,$filter)
	{
		global $CONFIG;
		if( $name == "" )
			$name = "CLASS";
		if( !is_array($filter) )
			$key = "$name:$filter";
		else
			$key = "$name:".implode(",",$filter);
		return $CONFIG['session']['session_name']."-".$key;
	}

	private function _setCached(&$cache,$name,$filter,$value)
	{
		$key = $this->_getCacheKey($name,$filter);
		$cache[$key] = $value;
//		log_debug("_setCached: $key",$value);
		cache_set("ref_attr_$key",$value);
	}

	private function _getCached(&$cache,$name,$filter)
	{
		$key = $this->_getCacheKey($name,$filter);
		if( isset($cache[$key]) )
		{
			//log_debug("using ref cache $key");
			return $cache[$key];
		}
		return cache_get("ref_attr_$key");
	}

	private function _getComment($method_name = false, $return_empty = false)
	{
		global $CONFIG;
		$ref = $method_name?$this->getMethod($method_name):$this;
		$key = $CONFIG['session']['session_name']."-".($method_name?$this->Classname."::".$method_name:$this->Classname);

		$comment = cache_get("doccomment_$key");
		if( $comment === false )
			$comment = trim($ref->getDocComment());
		if( $comment && (strpos($comment, "/**") === 0) )
			cache_set("doccomment_$key",$comment);
		else
		{
			$comment = cache_get("doccomment_$key");
			if( (!$comment || (strpos($comment, "/**") === false)) && !$return_empty )
			{
				$fn = $ref->getFileName();
				if( !$fn )
					return "";
				$lines = file_get_contents($ref->getFileName());
//				log_error($lines);
				$lines = explode("\n",$lines);
				$doc = array();
				$collecting = false;
				for( $i=$ref->getStartLine()-1; $i>1; $i-- )
				{
					$line = $lines[$i];
					if($line == "")
						continue;
					if( !$collecting && strpos($line,'*/') !== false )
						$collecting = true;
					if( $collecting )
					{
						$doc[] = trim($line);
						if( (strpos($line,'/*') !== false || preg_match('/^\s*function\s/i',$line) > 0) )
							break;
					}
				}
				if(count($doc) > 0)
				{
					$comment = implode("\n",array_reverse($doc));
					cache_set("doccomment_$key",$comment." ");
				}
			}
		}

		return $comment;
	}

	private function _getAttributes($comment,$filter,$object=false,$method=false,$field=false,$allowAttrInheritance=true)
	{
		$pattern = '/@attribute\[([^\]]*)\]/im';
		if( !preg_match_all($pattern, $comment, $matches) )
			return array();
		
		if( !is_array($filter) )
			$filter = array($filter);
		foreach( $filter as $i=>$f )
			$filter[$i] = strtolower(str_replace("Attribute","",$f));

		$res = array();
		$pattern = '/([^\(]*)\((.*)\)/im';
		foreach( $matches[1] as $m )
		{			
			$m = trim($m);
			
			if( preg_match_all($pattern, $m, $inner) )
			{
				$name = str_replace("Attribute","",$inner[1][0]);
				$attr = $name."Attribute({$inner[2][0]})";
			}
			else
			{
				$name = str_replace("Attribute","",$m);
				$attr = $name."Attribute()";
			}

			if( !__search_file_for_class($name."Attribute") )
			{
				if( $name!='NoMinify' )
					log_trace("Invalid Attribute: $m ({$name}Attribute) found in Comment '$comment'");
				continue;
			}

			eval('$attr = new '.$attr.';');
			$name = strtolower($name);
			$add = count($filter) == 0;
			foreach( $filter as $f )
			{
				if( $f == $name || ($allowAttrInheritance && is_subclass_of($attr, $f."Attribute")) )
				{
					$add = true;
					break;
				}
			}

			if( $add )
			{				
				$attr->Reflector = $this;
				$attr->Class = $this->Classname;
				if( $object && is_object($object) ) $attr->Object = $object;
				if( $method ) $attr->Method = $method;
				if( $field ) $attr->Field = $field;
				$res[] = $attr;
			}
		}
		return $res;
	}

	/**
	 * Creates a new object of the reflected type
	 * 
	 * Will call the constructor for the reflected type like this:
	 * <code php>
	 * return $this->newInstanceArgs($args);
	 * </code>
	 * @param array $args Constructor arguments
	 * @return object The new instance
	 */
	public function CreateObject($args)
	{
		return $this->newInstanceArgs($args);
	}

	/**
	 * Returns class attributes.
	 * 
	 * Class attributes are DocComment parts following the syntax <at>attribute[&lt;Attribute&gt;].
	 * <at> := The <at> sign.
	 * &lt;Attribute&gt; :=	Construction string of the attribute. May miss the part 'Attribute' at the end
	 *					and empty brackets.
	 * Exapmples:
	 * <code>
	 * <at>attribute[Right]
	 * <at>attribute[RightAttribute]
	 * <at>attribute[Right()]
	 * <at>attribute[RightAttribute()]
	 * <at>attribute[Right('false')]
	 * <at>attribute[RightAttribute('true || false')]
	 * </code>
	 * @param string|array $filter	Return only Attributes tha match the given filter.
	 *									May be string for a single attribute or array of string for
	 *									multiple attributes.
	 * @param bool $allowAttrInheritance If true, filter not only matches directly, but also all classes derivered from a valid filter
	 * @return array An array of objects derivered from <System_Attribute>.
	 */
	public function GetClassAttributes($filter=array(), $allowAttrInheritance=true)
	{
		if( !is_array($filter) )
			$filter = array($filter);
		
		$res = $this->_getCached($this->_attribute_cache,$this->Classname,$filter);
		if( $res )
			return $res;

		$comment = $this->_getComment();
		$res = $this->_getAttributes($comment,$filter,$this->Instance,false,false,$allowAttrInheritance);
		$this->_setCached($this->_attribute_cache,"",$filter,$res);
		
		return $res;
	}

	/**
	 * Returns method attributes.
	 * 
	 * For a detailed description see <System_Reflector::GetClassAttributes>
	 * @param string $method_name The name of the method.
	 * @param string|array $filter	Return only Attributes tha match the given filter. May be string for a single attribute or array of string for multiple attributes.
	 * @param bool $allowAttrInheritance If true, filter not only matches directly, but also all classes derivered from a valid filter
	 * @return array An array of objects derivered from <System_Attribute>.
	 */
	public function GetMethodAttributes($method_name, $filter=array(), $allowAttrInheritance=true)
	{
		$cache_key = $this->Classname."::".$method_name;
		$res = $this->_getCached($this->_attribute_cache,$cache_key,$filter);
		if( $res !== false )
		{
//			die("cached ".render_var($res));
			return $res;
		}
		if( !$this->hasMethod($method_name) )
		{
			if( $this->Instance && $this->Instance instanceof Control )
			{
				foreach( $this->Instance->_extender as &$ex )
				{
					$ref2 = System_Reflector::GetInstance($ex);
					$res = $ref2->GetMethodAttributes($method_name, $filter);
					if( $res && count($res) > 0 )
					{
						$this->_setCached($this->_attribute_cache,$cache_key,$filter,$res);
						return $res;
					}
				}
			}
			//log_debug("method not found: ".$this->getName()."::$method_name");
			return array();
		}

		$method = $this->getMethod($method_name);
		$method_name = $method->getName();

		$comment = $this->_getComment($method_name);

		$res = $this->_getAttributes($comment,$filter,$this->Instance,$method_name,false,$allowAttrInheritance);
		$this->_setCached($this->_attribute_cache,$cache_key,$filter,$res);
		return $res;
	}

	/**
	 * Returns a list of property names
	 * 
	 * May also step down inheritance graph and include properties from there
	 * @param bool $include_derivered If true steps down inheritance graph
	 * @return array An array of property names
	 */
	public function GetPropertyNames($include_derivered = true)
	{
		$properties = parent::getProperties(ReflectionProperty::IS_PUBLIC);
		$res = array();
		foreach( $properties as $prop )
		{
			if( $include_derivered || $prop->getDeclaringClass().getName() == $this->getName() )
				$res[] = $prop->getName();
		}
		return $res;
	}
	
	/**
	 * Returns the DocComment for a method
	 * 
	 * Perhaps use <System_Reflector::getCommentObject>() instead as that one returns a <PhpDocComment> object.
	 * @param string $method_name Name of method
	 * @return string The DocComment
	 */
	public function getCommentString($method_name=false)
	{
		return $this->_getComment($method_name);
	}
	
	/**
	 * Returns the DocComment for a method(or the class) as object
	 * 
	 * This is the modern version of <System_Reflector::getCommentString>().
	 * @param string $method_name Name of method or false if you want the class comment
	 * @return PhpDocComment The DocComment wrapped as PhpDocComment
	 */
	public function getCommentObject($method_name=false)
	{
		$comment = $this->_getComment($method_name);
		return PhpDocComment::Parse($comment);
	}
	
	/**
	 * Overrides <ReflectionClass::getMethod> to enable <System_ReflectionMethod> handling.
	 * 
	 * @param string $name Name of the method to get
	 * @return System_ReflectionMethod A System_ReflectionMethod instance or false on error
	 */
	public function getMethod($name)
	{
		try
		{
			$res = new System_ReflectionMethod($this->Classname,$name);
			return $res;
		}catch(Exception $e){ log_debug("Checking for extender method"); }
		
		if( $this->Instance && $this->Instance instanceof Control )
		{
			foreach( $this->Instance->_extender as &$ex )
			{
				$ref = System_Reflector::GetInstance($ex);
				$res = $ref->getMethod($name);
				if( $res )
					return $res;
			}
		}
		return false;
	}
}
