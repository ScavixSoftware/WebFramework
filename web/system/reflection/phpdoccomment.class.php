<?
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
 * Represents a PHP DocComment as described in http://en.wikipedia.org/wiki/PHPDoc
 * 
 * Use <PhpDocComment::Parse> to create an instance.
 */
class PhpDocComment
{
	var $ShortDesc = "";
	var $LongDesc = "";
	var $Tags = array();
	var $Attributes = array();
	
	/**
	 * Creates a PhpDocComment instance from a string
	 * 
	 * See <System_Reflector::getCommentObject> for how to use this best.
	 * @param string $comment Valid DocComment string
	 * @return boolean|PhpDocComment False on error, else a PhpDocComment object
	 */
	static function Parse($comment)
	{
		$res = new PhpDocComment();
		
		if( !preg_match('/^\s*\/\*\*(.*)\*\/\s*$/s',$comment,$m) )
			return false;
		
		$comment = explode("\n",$m[1]);
		foreach( $comment as $i=>$l )
			$comment[$i] = trim(ltrim($l,"\t *"));
		$comment = implode("\n",$comment);
		
		$comment = trim($comment);
		$m = explode("@",$comment,2);
		$m = explode("\n\n",$m[0],2);
		$isMatch = preg_match('/^@attribute/',trim($m[0]));
		
		if ($isMatch !== false && $isMatch == 0)
			$res->ShortDesc = trim($m[0]);
		
		if (isset($m[1]))
		{
			$isMatch = preg_match('/^@attribute/',trim($m[1]));
			if ($isMatch !== false && $isMatch == 0)
				$res->LongDesc = trim($m[1]);
		}
		
		preg_match_all('/^@([^\s]+)([^@]*)/ms',$comment,$m,PREG_SET_ORDER);
		foreach( $m as $p )
		{
			if( starts_with($p[1],'attribute[') )
				continue;
			if( $p[1] == 'param_array' && preg_match('/([^\s]+)\s+([^\s]+)\s+(.*)/',$p[2],$ma) )
			{
				foreach( explode(",",$ma[2]) as $t )
					$res->Tags[] = array(
						'tag' => "param",
						'data' => trim($ma[1]." ".$t." ".$ma[3])
					);
				continue;
			}
			$res->Tags[] = array(
				'tag' => $p[1],
				'data' => trim($p[2])
			);
		}
		
		preg_match_all('/^@attribute\[([^\]]*)\]/ms',$comment,$m,PREG_SET_ORDER);
		foreach( $m as $p )
		{
			$res->Attributes[] = array(
				'data' => $p[1]
			);
		}
		
		if( !$res->LongDesc && $res->ShortDesc && ends_with($res->ShortDesc, '.') )
			$res->LongDesc = '';
		return $res;
	}
	
	/**
	 * Ensures that there's a short description set.
	 * 
	 * @param string $default_description Text to set if there's no ShortDesc yet
	 * @return void
	 */
	function EnsureDescription($default_description)
	{
		if( !$this->ShortDesc )
			$this->ShortDesc = $default_description;
		if( $this->LongDesc === false )
			$this->LongDesc = "";
	}
	
	/**
	 * Ensures that there's a description set for the given tag.
	 * 
	 * Note that this will set the $default_description for all tags that match $tag, so do not use with 'param' and 
	 * others that may appear multiple times.
	 * @param string $tag Tag name like 'internal', 'override'
	 * @param string $default_description Text to set if there's none yet
	 * @return void
	 */
	function EnsureTagDescription($tag, $default_description)
	{
		foreach( $this->Tags as $i=>$t )
			if( $t['tag'] == $tag && !$t['data'] )
				$this->Tags[$i]['data'] = $default_description;
		$this->_tagbuf = array();
	}
	
	private function getTag($name,$properties)
	{
		if( !isset($this->_tagbuf) )
			$this->_tagbuf = array();
		
		if( !isset($this->_tagbuf[$name]) )
		{
			$this->_tagbuf[$name] = array();
			foreach( $this->Tags as $t )
			{
				if( $t['tag'] != $name )
					continue;

				$p = new stdClass();
				if( preg_match_all('/([^\s]+)/',$t['data'],$matches) )
				{
					$props = array();
					for($i=0;$i<count($properties)-1;$i++)
						$props[] = array_shift($matches[1]);
					$props[] = implode(" ",$matches[1]);
					
					foreach( $properties as $i=>$n )
						$p->$n = $props[$i];
				}
				else
				{
					foreach( $properties as $i=>$n )
						$p->$n = '';
				}
				$this->_tagbuf[$name][] = $p;
			}
		}
		return $this->_tagbuf[$name];
	}
	
	/**
	 * Check if theres at least one of the given annotations present
	 * 
	 * Will use all given arguments as input
	 * <code php>
	 * $dc->hasOne('internal','deprecated','override');
	 * </code>
	 * @return bool true or false
	 */
	function hasOne()
	{
		foreach( func_get_args() as $name )
			if( $this->has($name) )
				return true;
		return false;
	}
	
	/**
	 * Checks if there's a specific annotation present.
	 * 
	 * @param string $name Name of annotation to check
	 * @return bool true or false
	 */
	function has($name)
	{
		foreach( $this->Tags as $t )
			if( $t['tag'] == $name )
				return true;
		return false;
	}
	
	/**
	 * Returns a specific annotation
	 * 
	 * If scheme is `<at>mySomething This is my comment` you can call it like `get('mySomething');`
	 * @param string $name Name of the annotation
	 * @return mixed The description (may be empty) or false
	 */
	function get($name)
	{
		$tag = $this->getTag($name,array('desc'));
		if( count($tag) == 0 )
			return false;
		return $tag[0]->desc;
	}
	
	/**
	 * Lists all param docs
	 * 
	 * Every method parameter should have an <at>param block in the DocComment.
	 * This returns all of them
	 * @return array All param block
	 */
	function getParams()
	{
		$res = $this->getTag('param',array('type','var','desc'));
		foreach( $res as $param )
		{
			$param->typeArray = explode('|', $param->type);
			sort($param->typeArray);
			$param->type = implode("|", $param->typeArray);
		}
		return $res;
	}
	
	/**
	 * Returns docs for a specified parameter
	 * 
	 * Every method parameter should have an <at>param block in the DocComment.
	 * This method returns it
	 * @param string $name Parameter name
	 * @return mixed The parameter description or false on error
	 */
	function getParam($name)
	{
		foreach( $this->getParams() as $p )
			if( $p->var == $name )
				return $p;
		return false;
	}
	
	/**
	 * Gets the return documentation
	 * 
	 * Every method should have a <at>return block in the DocComment.
	 * This method returns it
	 * @return mixed The return doc or false on error
	 */
	function getReturn()
	{
		$res = $this->getTag('return',array('type','desc'));
		return ($res && isset($res[0]))?$res[0]:false;
	}
	
	/**
	 * Gets the deprecated note if present
	 * 
	 * Every DocComment may contain a <at>deprecated part.
	 * This method returns it
	 * @return mixed The deprecated note if present or false
	 */
	function getDeprecated()
	{
		return $this->get('deprecated');
	}
	
	/**
	 * Returns the description ready for use in markdown syntax
	 * 
	 * Markdown is our favorite for automated documentation creation as GitHub supports it directly for their Wiki.
	 * This method makes some preparations for the doccomment to be complatible with MD.
	 * @return string MD prepared string
	 */
	function RenderAsMD()
	{
		$desc  = $this->ShortDesc?$this->ShortDesc:'';
		$desc .= $this->LongDesc?"\t\n".str_replace("\n","\t\n",$this->LongDesc):'';
		
		$internal = $this->get('internal');
		if( $internal !== false )
			$desc = "**INTERNAL** $internal\t\n$desc";

		$deprecated = $this->getDeprecated();
		if( $deprecated  !== false )
			$desc = "**DEPRECATED** $deprecated\t\n$desc";

		$override = $this->get('override');
		if( $override !== false )
			$desc = "**OVERRIDE** $override\t\n$desc";

		$shortcut = $this->get('shortcut');
		if( $shortcut !== false )
			$desc = "**SHORTCUT** $shortcut\t\n$desc";

		$implements = $this->get('implements');
		if( $implements !== false )
			$desc = "**IMPLEMENTS** $implements\t\n$desc";
		
		if( !$this->entities )
		{
			$this->entities = array();
			$this->mdEscapeEntity($this->entities,'ul');
			$this->mdEscapeEntity($this->entities,'ol');
			$this->mdEscapeEntity($this->entities,'li');
			$this->mdEscapeEntity($this->entities,'img');
		}
		
		$desc = str_replace(array('<at>','<b>','</b>','<code>','</code>','<br/>'),array('@','**','**','```','```',"\t\n"),$desc);
		$desc = str_replace(array_keys($this->entities),array_values($this->entities),$desc);
		$desc = preg_replace('/<code ([^>]*)>/','```$1', $desc);
		$desc = str_replace("```\t","```",$desc); // tripple ` followed by tab will break the output!
		return $desc;
	}
	
	var $entities = false;
	private function mdEscapeEntity(&$data,$what)
	{
		$data["<$what>"] = htmlspecialchars("<$what>");
		$data["<$what/>"] = htmlspecialchars("<$what/>");
		$data["</$what>"] = htmlspecialchars("</$what>");
	}
}