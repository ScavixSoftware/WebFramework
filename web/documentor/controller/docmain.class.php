<?
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

use ScavixWDF\Base\Control;
use ScavixWDF\Base\HtmlPage;
use ScavixWDF\Base\Template;
use ScavixWDF\Controls\Anchor;
use ScavixWDF\Reflection\PhpDocComment;

class DocMain extends HtmlPage
{
	const MAX_FILES = 5000;
	var $sums = array('comment'=>0,'short'=>0,'long'=>0,'param'=>0,'return'=>0);
	var $errors = array();
	var $curSection = false;
	static $known_token = array('`string`','`int`','`integer`','`bool`','`boolean`','`mixed`','`object`','`array`','`DIRECTORY_SEPARATOR`');
	
	private function _endSection()
	{
		if( !$this->curSection )
			return;
		if( $this->curSection->class == 'file' )
			$this->content($this->curSection->WdfRender());
		$this->curSection = false;
	}
	
	private function _startSection($text)
	{
		if( $this->curSection )
			$this->_endSection();
		
		$this->curSection = new Control('div');
		$this->curSection->content($text);
		return $this->curSection;
	}
	
	private function _warn($text,$section=false)
	{
		if( !$this->curSection )
			$this->_section('BLANK'); 
		$this->curSection->content("<div class='warn'>".htmlspecialchars($text)."</div>\n");
		$this->curSection->class = 'file';
		if( $section )
			$this->sums[$section]++;
	}
	
	private function _getDc($data,$type='function')
	{
		$prefix = is_in($type,'function','class')?"$type {$data['name']}":"$type{$data['name']}";
		$dc = PhpDocComment::Parse($data['comment']);
		$is_private = starts_with($data['name'],'_') 
			|| contains($data['modifiers'],'private','protected')
			|| ($dc && $dc->has('private'));
		
		if( !$dc )
		{
			if( !$is_private )
				$this->_warn("$prefix MISSING DocComment",'comment');
			return array(false,$is_private);
		}
		if( $dc->hasOne('internal','override','deprecated','shortcut','implements') )
			return array($dc,$is_private);
		
		if( !$is_private )
		{
			if( !$dc->ShortDesc ) 
				$this->_warn("$prefix MISSING ShortDesc",'short');
			if( $dc->LongDesc === false  )
				$this->_warn("$prefix MISSING LongDesc",'long');
		}
		
		if( $type == 'class' )
			return array($dc,$is_private);
		
		if( !$is_private && !$dc->has('return') ) 
			$this->_warn("$prefix MISSING @return",'return');
		return array($dc,$is_private);
	}
	
	function Init()
	{
		$this->content(Template::Make('intro'))->set("run",new Anchor(buildQuery('DocMain','Run'),'Run'));
	}
	
	function Download()
	{
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment");
		readfile(__DIR__.'/wdf_docs.zip');
		die();
	}
	
	function Run()
	{
		$summary = $this->content(new Control('div'));
		$run = new Anchor(buildQuery('DocMain','Run'),'Run again');
		$down = new Anchor(buildQuery('DocMain','Download'),'Download');
		$preview = new Anchor(buildQuery('Preview'),'Preview');
		$this->content('<div style="text-align: center; font-size: 18px; font-weight: bold">');
		$this->content($run->WdfRender()."&nbsp;&nbsp;".$down->WdfRender()."&nbsp;&nbsp;".$preview->WdfRender());
		$this->content('</div>');
		
		foreach( system_glob_rec(__DIR__.'/out','*') as $file )
			unlink($file);
		cache_clear();
		
		$path = realpath(__DIR__.'/../../system/');
		$i = 1;
		global $home, $processed_files;
		$home = array('funcs'=>array(),'classes'=>array(),'methods'=>array(),'tree'=>array(),'interfaces'=>array());
		$processed_files = array();
		$all_files = system_glob_rec($path,'*.php');
		$cnt_all_files = count($all_files);
		foreach( $all_files as $file )
		{
			if( $this->skip($file) )
			{
				$cnt_all_files--;
				continue;
			}
			
			$title = str_replace($path.'/','',$file);
			$fn_cls = __DIR__.'/out/classes_'.str_replace('.php.md','.md',str_replace('/', '_', $title).'.md');
			$fn_fnc = __DIR__.'/out/functions_'.str_replace('.php.md','.md',str_replace('/', '_', $title).'.md');
			
			$this->_startSection("FILE: $file");
			$data = $this->process($file);
			if( $i++ > self::MAX_FILES )
			{
				$this->content("<h1>Stopping, still ".($cnt_all_files-self::MAX_FILES)." missing</h1>");
				break;
			}

			// functions
			$lines = array();
			foreach( $data['functions'] as $func )
			{
				$l = $this->funcToMd($func);
				if( $l )
				{
					$home['funcs'][$func['name']] = basename($fn_fnc,'.md')."#wiki-".md5($func['name']);
					$lines[] = $l;
//					$processed_files[$title][] = $func['name']; // we do not want functions in the folder tree
				}
			}
			if( count($lines) > 0 )
				file_put_contents($fn_fnc, $this->escapeMd("# Functions in file $title\n".implode("\n",$lines)));

			// classes
			$lines = array();

			foreach( $data['classes'] as $class )
			{
//				log_if($class['name']=="uiControl",$class['name'],$class);
				$lines[] = $this->classToMd($class,basename($fn_cls,'.md'));
				if( $class['type']=='interface' && !isset($home['interfaces'][$class['type']]) )
					$home['interfaces'][$class['name']] = array();
				if( isset($class['implements']) )
				{
					foreach( $class['implements'] as $int )
					{
						if( !isset($home['interfaces'][$int]) )
							$home['interfaces'][$int] = array($class['name']);
						else
							$home['interfaces'][$int][] = $class['name'];
					}
				}
				$processed_files[$title][] = $class['name'];
			}
			if( count($lines) > 0 )
				file_put_contents($fn_cls, $this->escapeMd("# Classes in file $title\n".implode("\n",$lines)));
		}
		$this->_endSection();
		
		$this->writeIndexes();
		$this->createLinks();
		$this->writeZip();
		
		if( array_sum($this->sums) > 0 || count($this->errors)>0 )
		{
			$summary->addClass('summary');
			$summary->content("<b>Summary:</b><br/>");
			if( $this->sums['comment'] > 0 )
				$summary->content("Missing comments: {$this->sums['comment']}<br/>");
			if( $this->sums['short'] > 0 )
				$summary->content("Missing short descriptions: {$this->sums['short']}<br/>");
			if( $this->sums['long'] > 0 )
				$summary->content("Missing long descriptions: {$this->sums['long']}<br/>");
			if( $this->sums['param'] > 0 )
				$summary->content("Missing param descriptions: {$this->sums['param']}<br/>");
			if( $this->sums['return'] > 0 )
				$summary->content("Missing return value descriptions: {$this->sums['return']}<br/>");
			
			foreach( $this->errors as $err )
				$summary->content("$err<br/>");
		}
	}
	
	function writeZip()
	{
		unlink(__DIR__.'/wdf_docs.zip');
		$zip = new ZipArchive();
		$zip->open(__DIR__.'/wdf_docs.zip',ZIPARCHIVE::CREATE);
		foreach( system_glob(__DIR__.'/out/*') as $md )
			$zip->addFile($md,basename($md));
		$zip->close();
	}
	
	function createLinks()
	{
		global $home;
		
		$quickref = @file_get_contents(file_exists(__DIR__.'/quick.ref')?__DIR__.'/quick.ref':"http://php.net/quickref.php");
		if( $quickref && preg_match_all('|<li><a href="([^"]+)">([^<]+)</a></li>|', $quickref, $documented) )
		{
			file_put_contents(__DIR__.'/quick.ref', $quickref);
			list(,$doc_links,$doc_names) = $documented;
			$documented = array_combine($doc_names, $doc_links);
			$home['funcs'] = array_merge($documented,$home['funcs']);

			// add PHP predefined functions
			$def_funcs = get_defined_functions();
			foreach( $def_funcs['internal'] as $f )
				if( isset($documented[$f]) )
					$home['funcs'][$f] = strtolower("http://www.php.net".$documented[$f]);
		
			// add PHP predefined classes
			foreach( array_merge(get_declared_classes(),get_declared_interfaces()) as $c )
				if( isset($documented[strtolower($c)]) )
					$home['classes'][$c] = strtolower("http://www.php.net".$documented[strtolower($c)]);
		}
		else
			$this->errors[] = "Could not create PHP internal functions/classes links";
		
		// sorting the found information
		foreach( array_keys($home) as $key )
			natksort($home[$key]);
		
		$linking = function($match)
		{
			global $home, $current_link_file; 
			
			if( $match[1] != 'array' )
			{
				if( isset($home['funcs'][$match[1]]) )
					return "[{$match[1]}]({$home['funcs'][$match[1]]})";
				if( isset($home['classes'][$match[1]]) )
					return "[{$match[1]}]({$home['classes'][$match[1]]})";
			}
			
			$p = explode("::",$match[1]);
			if( count($p) < 2 )
				return $match[0];
			
			if( $p[0] == "PARENTCLASSES" )
			{
				$parents = array();
				$class = $p[1];
				do{
					$class = DocMain::getParent($class);
					if( $class ) $parents[] = DocMain::linkCls($class);
				}while( $class );
				if( count($parents) == 0 )
					return "";
				return "\n\nExtends: ".implode(" &raquo; ",$parents);
			}

			if( $p[0] == "SUBCLASSES" )
			{
				$subs = array();
				DocMain::getSubclasses($p[1], $subs, false);
				if( count($subs) == 0 )
					return "";
				return "\n\nSubclasses: ".implode(", ",$subs);
			}
			
			if( $p[0] == "OVERRIDE" )
			{
				if( count($p) != 3 )
					return "";
				$class = $p[1];
				do{
					$class = DocMain::getParent($class);
					if( !$class ) break;
					if( isset($home['methods'][$class][$p[2]]) )
						return "[$class::{$p[2]}]({$home['methods'][$class][$p[2]]})";
				}while( true );
				return "";
			}

			if( !isset($home['methods'][$p[0]][$p[1]]) )
			{
				if( !in_array($match[0], DocMain::$known_token) )
				{
					$lnk = DocMain::linkCls($p);
					if( $lnk ) return $lnk;
					log_debug("NO match",$match[0],"in $current_link_file",$p);
				}
				return $match[0];
			}
			
			return "[{$match[1]}]({$home['methods'][$p[0]][$p[1]]})";
		};
		
		$regex1 = '/<([a-zA-Z0-9_:]+)>/';
		$regex2 = '/`([a-zA-Z0-9_:]+)`/';
		global $current_link_file;
		foreach( system_glob(__DIR__.'/out/*_*') as $md ) // index files have to '_' in the filename
		{
			$current_link_file = $md;
			$cont = file_get_contents($md);
			$cont = preg_replace_callback($regex1,$linking,$cont);
			$cont = preg_replace_callback($regex2,$linking,$cont);
			file_put_contents($md,$cont);
		}
	}
	
	function writeIndexes()
	{
		global $home, $processed_files;
		
		// sorting the found information
		foreach( array_keys($home) as $key )
			natksort($home[$key]);
		
		// write functions index
		$last_letter = false; $lines = array("# Function listing:");
		foreach( $home['funcs'] as $name=>$file )
		{
			if( $last_letter != strtoupper($name[0]) )
			{
				$last_letter = strtoupper($name[0]);
				$lines[] = "\n## $last_letter\n\n";
			}
			$lines[] = "* [$name]($file)";
		}
		file_put_contents(__DIR__.'/out/functions.md', $this->escapeMd(implode("\n",$lines)));

		// write class index
		$last_letter = false; $lines = array("# Class listing:");
		foreach( $home['classes'] as $name=>$file )
		{
			if( isset($home['interfaces'][$name]) )
				continue;
			if( $last_letter != strtoupper($name[0]) )
			{
				$last_letter = strtoupper($name[0]);
				$lines[] = "\n## $last_letter\n\n";
			}
			$lines[] = "* [$name]($file)";
		}
		file_put_contents(__DIR__.'/out/classes.md', $this->escapeMd(implode("\n",$lines)));
		
		// write inheritance tree
		$lines = array("# Class inheritance:");
		DocMain::getSubclasses(false,$lines);
		file_put_contents(__DIR__.'/out/inheritance.md', $this->escapeMd(implode("\n",$lines)));
		
		// write interface listing
		$lines = array("# Interfaces:");
		foreach( $home['interfaces'] as $name=>$implementors )
		{
			if( !isset($home['classes'][$name]) )
				continue;
			$lines[] = "* ".self::linkCls($name);
			foreach( $implementors as $impl )
				$lines[] = "\t* ".self::linkCls($impl);
		}
		file_put_contents(__DIR__.'/out/interfaces.md', $this->escapeMd(implode("\n",$lines)));
		
		// write folder structure
		log_debug("Tree",$processed_files);
		$lines = array("# Folder tree:");
		$written_tree = array();
		ksort($processed_files);
		foreach( $processed_files as $file=>$classes )
		{
			$parts = explode("/",$file);
			$basename = explode('.',array_pop($parts));
			$basename = $basename[0];
			$pre = "";
			while( count($written_tree) > count($parts) )
				array_pop($written_tree);
			for($i=0;$i<count($parts);$i++)
			{
				$p = $parts[$i];
				if( isset($written_tree[$i]) && $written_tree[$i] == $p )
				{
					$pre .= "\t";
					continue;
				}
				$written_tree[$i] = $p;
				$lines[] = "$pre* **$p**";
				$pre .= "\t";
			}
			if( count($classes)>1 )
			{
				$lines[] = "$pre* **$basename**";
				$pre .= "\t";
			}
			foreach( $classes as $cls )
				if( isset($home['funcs'][$cls]) )
					$lines[] = "$pre* [$cls]({$home['funcs'][$cls]})";
				else
					$lines[] = "$pre* ".self::linkCls($cls);
		}
		file_put_contents(__DIR__.'/out/foldertree.md', $this->escapeMd(implode("\n",$lines)));
	}
	
	static function getSubclasses($parent,&$res,$pre='* ')
	{
		global $home;
		foreach( $home['tree'] as $name=>$extends )
		{
			if( isset($home['interfaces'][$name]) )
				continue;
			if( $parent == $extends )
			{
				$file = $home['classes'][$name];
				$res[$name] = $pre."[$name]($file)";
				if( $pre )
					DocMain::getSubclasses($name,$res,"\t$pre");
			}
		}
	}
	
	static function getParent($class)
	{
		global $home;
		foreach( $home['tree'] as $extends=>$name )
			if( $class == $extends )
				return $name;
		return false;
	}
	
	static function linkCls($name)
	{
		global $home;
		
		if( is_array($name) )
		{
			list($name,$meth) = $name;
			if( isset($home['classes'][$name]) && strpos($home['classes'][$name], "php.net") )
			{
				$m = str_replace("_", "-", ltrim(strtolower($meth),'_'));
				$fn = basename($home['classes'][$name],'.php');
				$nn = str_replace("class.", "", $fn).".$m.php";
				return "[$name::$meth](".str_replace("$fn.php",$nn,$home['classes'][$name]).")";
			}
		}
		
		if( isset($home['classes'][$name]) )
			return "[$name]({$home['classes'][$name]})";
		if( starts_with($name,'Zend_') )
			return "[{$name}](http://framework.zend.com/)";
		log_debug("No classlink found: $name");
		return $name;
	}
	
	function classToMd($class,$link)
	{
		global $home;
		
		list($dc,$is_private) = $this->_getDc($class,'class');

		$mod = implode(" ",$class['modifiers']);
		$hash = md5($class['name']);
		$tpl  = "\n## $mod {$class['type']} <a id='{$hash}'/>{$class['name']}\n".($dc?$dc->RenderAsMD():"NOT DOCUMENTED");
		if( isset($class['extends']) )
		{
			//$tpl .= "\n\nExtends: <{$class['extends']}>";
			$home['tree'][$class['name']] = $class['extends'];
		}
		else
			$home['tree'][$class['name']] = false;

		if( isset($class['implements']) )
			$tpl .= "\n\nImplements: <".implode("> <",$class['implements']).">";

		$tpl .= "<PARENTCLASSES::{$class['name']}>";
		$tpl .= "<SUBCLASSES::{$class['name']}>";

		$lines = array($tpl);
		$home['classes'][$class['name']] = "$link#wiki-$hash";
		if( !$dc || !$dc->hasOne('internal','deprecated') )
		{
			natksort($class['methods']);
			foreach( $class['methods'] as $meth )
			{
				$l = $this->funcToMd($meth,'###',"method {$class['name']}.",$class);
				if( $l )
				{
					$lines[] = $l;
					$home['methods'][$class['name']][$meth['name']] = "$link#wiki-".md5($meth['name']);
				}
			}
		}
		return implode("\n",$lines);
	}
	
	function funcToMd($func,$heading='##',$what='function',$class=false)
	{
		list($dc,$is_private) = $this->_getDc($func,$what);
		if( !$dc && $is_private ) // just skip undocumented private functions
			return false;
		if( $dc && $dc->has('private') ) // skip private functions that are explicitely marked private
			return false;
		
		if( !contains($func['modifiers'],'public','protected','private') )
			array_unshift($func['modifiers'], 'public');
		
		$mod = implode(" ",$func['modifiers']);
		$args = array();
		$hash = md5($func['name']);
		$head = "\n$heading <a id='$hash'/>{$func['name']}\n";
		if( !$dc )
			return $head."\nNOT DOCUMENTED";

		foreach( $func['parameter'] as $arg )
			$args[$arg['name']] = (isset($arg['type'])?"{$arg['type']} ":"").$arg['name'].(isset($arg['default'])?"={$arg['default']}":"");
		
		if( $func['name'] == '__initialize' )
			$dc->EnsureDescription("This is WDF constructor equivalent");
		
		if( $class )
		{
			$dc->EnsureTagDescription('override',"<OVERRIDE::{$class['name']}::{$func['name']}>");
			//log_if($func['name']=='WdfRender',"WdfRender",$dc,$class['name'],$func['name']);
		}
			
		$ret = $dc->getReturn();
		$comment = $dc->RenderAsMD();
		
		if( $dc->hasOne('internal','override','deprecated','shortcut','implements') )
			return "$head$comment";

		$definition = "\n\nDefinition: `$mod function {$func['name']}(".implode(", ",$args).")`";
		$what = $what=='function'?"$what ":$what;
		$ret = !$ret?'NOT DOCUMENTED'
			:( !starts_with($ret->type,'<')?"`{$ret->type}` {$ret->desc}":"{$ret->type} {$ret->desc}" );
		$return     = "\n\nReturns: ".trim($ret);
		$arguments  = array();
		foreach( $func['parameter'] as $arg )
		{
			$p = $dc->getParam($arg['name']);

			$def = (isset($arg['default'])?" [default: {$arg['default']}]":"");
			$arguments[] = $p
				?" * `{$p->type} {$p->var}` ".str_replace("\n", " ", $p->desc)
				:" * `{$arg['name']}$def` NOT DOCUMENTED";

			if( $is_private )
				continue;
			if( !$p )
				$this->_warn("$what{$func['name']} MISSING @param {$arg['name']}",'param');
			else
			{
				if( preg_match('/[^a-z0-9_|]/i',$p->type) )
					$this->_warn("$what{$func['name']} PARAM {$arg['name']} weird TYPE {$p->type}",'param');
				if( !$p->desc )
					$this->_warn("$what{$func['name']} PARAM {$arg['name']} missing DESC",'param');

				if( count($p->typeArray)>1 )
				{
					foreach( $p->typeArray as $t )
						if( !is_in($t,'string','int','integer','bool','boolean','float','double','array','mixed','object') )
							$this->_warn("$what{$func['name']} PARAM {$arg['name']} part of mixed type is unlinkable: $t",'param');
				}
			}
		}
		$arguments = (count($arguments)>0)?"\n\nParameters:\n\n".implode("\n\n",$arguments):'';
		return "$head$comment$definition$return$arguments";
	}
	
	function escapeMd($c)
	{
		return $c; // GitHub allows underscores in words, so just return for now
		$parts = explode("`",$c);
		for($i=0; $i<count($parts); $i+=2 )
			$parts[$i] = str_replace('_','\_',$parts[$i]);
		return implode("`",$parts);
	}
	
	function skip($fn)
	{
//		if( stripos($fn,'ajaxaction') !== false )
//			return false;
//		return true;
		
		if( fnmatch('*.tpl.php', $fn) )
			return true;
		if( stripos($fn,'modules/oauth_php/') !== false )
			return true;
		if( stripos($fn,'modules/phpexcel/') !== false )
			return true;
		if( stripos($fn,'modules/pear/') !== false )
			return true;
		if( stripos($fn,'modules/mail/') !== false )
			return true;
		if( stripos($fn,'modules/textdata/') !== false )
			return true;
		if( stripos($fn,'modules/zend/zend/') !== false )
			return true;
		if( stripos($fn,'modules/minify/') !== false )
			return true;
		
		if( stripos($fn,'modules/zend/pdf/Cell.php') !== false )
			return true;
		
		if( fnmatch('*.class.php', $fn) )
			return false;
		if( stripos($fn,'/modules/') !== false )
			return false;
		if( stripos($fn,'/essentials/') !== false )
			return false;
		if( starts_with(basename($fn),'system') )
			return false;
		
		return true;
	}
	
	function process($fn)
	{
		gc_collect_cycles();
		$contents = file_get_contents($fn);
		$token = token_get_all($contents);
		//log_debug("Tokens ",$token);
		$last_comment = '';
		$last_str = '';
		$last_modifiers = array();
		
		$classes = array(); $cur_cls = false;
		$functions = array(); $cur_func = false;
		$cur_arg = false;
		$block = $brackets = 0;
		
		foreach( $token as $tok )
		{
			if( is_array($tok) )
			{
				list($type,$value,$line) = $tok;
//				if( $type != T_WHITESPACE )
//					log_debug("Line $line, Token ".token_name($type)."[$type], Value $value");
				switch( $type )
				{
					case T_DOC_COMMENT:
						if( strpos($value,'@copyright')===false )
							$last_comment = $value;
						break;
					case T_STRING:
//						log_if($value=="Icon", (($cur_cls&&isset($cur_cls['name']))?$cur_cls['name']:'')."::Icon",$cur_cls,$cur_func);
						if( $cur_cls && !isset($cur_cls['name']) )
						{
							$cur_cls['name'] = $value;
//							log_if($value=="uiControl","uiControl",$cur_cls);
						}
						elseif( $cur_func && !isset($cur_func['name']) )
						{
							$cur_func['name'] = $value;//($value!='__initialize')?$value:'Constructor';
						}
						elseif( $cur_arg && !isset($cur_arg['default']) )
							$cur_arg['default'] = $value;
						elseif( $cur_cls && !isset($cur_cls['def_done']) && isset($cur_cls['extends']) && $cur_cls['extends'] === false )
							$cur_cls['extends'] = $value;
						elseif( $cur_cls && !isset($cur_cls['def_done']) && isset($cur_cls['implements']) )
							$cur_cls['implements'][] = $value;
						else
							$last_str = $value;
						break;
					case T_VARIABLE:
						if( $cur_func && isset($cur_func['bracket_val']) )
						{
							$cur_arg = array('name'=>$value);
							if( $last_str )
								$cur_arg['type'] = $last_str;
							$last_str = '';
						}
						break;
					case T_CLASS:
					case T_INTERFACE:
//						if( !in_array('private', $last_modifiers) )
							$cur_cls = array('start_line'=>$line,'methods'=>array(),'block_val'=>$block,'comment'=>$last_comment,
								'modifiers'=>$last_modifiers,'type'=>($type==T_CLASS)?'class':'interface');
						$last_comment = '';$last_str = '';
						$last_modifiers = array();
						break;
					case T_IMPLEMENTS:
						if( $cur_cls )
							$cur_cls['implements'] = array();
						break;
					case T_EXTENDS:
						if( $cur_cls )
							$cur_cls['extends'] = false;
						break;
					case T_ABSTRACT:
					case T_STATIC:
					case T_PRIVATE:
					case T_PROTECTED:
					case T_PUBLIC:
					case T_FINAL:
						$last_modifiers[] = strtolower($value);
						break;
					case T_FUNCTION:
//						if( !in_array('private', $last_modifiers) && !in_array('protected', $last_modifiers) )
							$cur_func = array('start_line'=>$line,'bracket_val'=>$brackets,'comment'=>$last_comment,'parameter'=>array(),'modifiers'=>$last_modifiers);
						$last_comment = '';$last_str = '';
						$last_modifiers = array();
						break;
					case T_CURLY_OPEN:
						$tok = "{";
						break;
				}
			}
			
			if( is_string($tok) )
			{
//				log_debug("String Token $tok");
				switch( $tok )
				{
					case '{': $block++; if( $cur_cls && !isset($cur_cls['def_done']) ) $cur_cls['def_done'] = true; break;
					case '}': 
						$block--;
						
						if( $cur_cls && $block == $cur_cls['block_val'] )
						{
							unset($cur_cls['block_val']);
							unset($cur_cls['def_done']);
							$classes[] = $cur_cls;
							$cur_cls = false;
//							log_debug("finished CLASS");
						}
						break;
					case '(': $brackets++;  break;
					case ')': 
						$brackets--; 						
						if( $cur_func && isset($cur_func['bracket_val']) && $brackets == $cur_func['bracket_val'] )
						{
							unset($cur_func['bracket_val']);
							if( $cur_arg )
							{
								$cur_func['parameter'][] = $cur_arg;
								$cur_arg = false;
							}
							
							if( isset($cur_func['name']) ) // if not it's a closure
							{
//								if( $cur_func['name']=='__initialize' || !starts_with($cur_func['name'], '_') )
								{
									if( $cur_cls )
										$cur_cls['methods'][$cur_func['name']] = $cur_func;
									else
										$functions[] = $cur_func;
								}
							}
							$cur_func = false;
						}
						break;
					case ',':
						if( $cur_arg && $cur_func )
						{
							$cur_func['parameter'][] = $cur_arg;
							$cur_arg = false;
						}
					case ';':
						$last_modifiers = array();
					break;
				}
				continue;
			}
		}
		return array
		(
			'classes' => $classes,
			'functions' => $functions
		);
	}
}
