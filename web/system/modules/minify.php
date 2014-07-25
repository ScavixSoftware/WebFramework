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

use ScavixWDF\Base\Template;
use ScavixWDF\Reflection\ResourceAttribute;
use ScavixWDF\Reflection\WdfReflector;
use ScavixWDF\WdfException;

/**
 * Initializes the minify module.
 * 
 * Note that this module can be used best from SysAdmin!
 * @return void
 */
function minify_init()
{
	global $CONFIG;
	classpath_add(__DIR__."/minify/");
	admin_register_handler('Minify','MinifyAdmin','Start');
	
	cfg_check('minify','target_path','Minify module needs a target_path');
	cfg_check('minify','base_name','Minify module needs a base_name');
	cfg_check('minify','url','Minify module needs an url');
	
	register_hook_function(HOOK_PRE_RENDER, 'minify_pre_render_handler');
}

/**
 * Handler for HOOK_PRE_RENDER
 * 
 * Checks if there are minified files present as sets up ScavixWDF to use them.
 * Do not call this directly!
 * @param mixed $args Do not call!
 * @return void
 */
function minify_pre_render_handler($args)
{
	if( count($args)>0 )
	{
		if( minify_forbidden($args[0]) )
			return;
	}
	
	$target_base_name = cfg_get('minify','target_path');
	system_ensure_path_ending($target_base_name,true);
	$target_base_name .= cfg_get('minify','base_name');
	$base_uri = cfg_get('minify','url');
	use_minified_file($target_base_name, 'js', $base_uri);
	use_minified_file($target_base_name, 'css', $base_uri);
}

/**
 * Checks if minifying a classes resources is explicitely forbidden
 * 
 * Uses NoMinify attribute to check that
 * @param string $classname Classname to check for
 * @return bool true or false
 */
function minify_forbidden($classname)
{
	if( is_string($classname) && strpos($classname, '.') !== false )
	{
		$classname = explode('.',$classname);
		$classname = $classname[0];
	}
	try
	{
		$ref = WdfReflector::GetInstance($classname);		
		return count($ref->GetClassAttributes('NoMinify')) > 0;
	}
	catch(Exception $ignored)
	{
		try
		{
			Template::Make($classname); // check if the name refers to an anonymous template
			return false;
		}
		catch (Exception $ex)
		{
			WdfException::Log("minify_forbidden($classname)",$ex);
			return false;
		}
	}
}

/**
 * Performs minifying.
 * 
 * This is best called from SysAdmin.
 * @param array $paths Array fo paths to scan for content
 * @param string $target_base_name Base name for the minify files
 * @param string $nc_argument NoCache argument to add to the filename
 * @return void
 */
function minify_all($paths,$target_base_name,$nc_argument)
{
	$target_base_name .= (isSSL()?".1":".0");
	foreach( system_glob($target_base_name.".*.*") as $f )
		unlink($f);
	
	$v = preg_replace('/[^\d]*/', "", $nc_argument);
	minify_js($paths,$target_base_name.".$v.js");
	minify_css($paths,$target_base_name.".$v.css",$v);
}

/**
 * @internal Used from <minify_pre_render_handler>().
 */
function use_minified_file($target_base_name,$kind,$base_uri)
{
	global $CONFIG;
	$target_base_name .= (isSSL()?".1":".0");
	foreach( system_glob($target_base_name.".*.$kind") as $f )
	{
		$CONFIG["use_compiled_$kind"] = $base_uri.basename($f);
		return;
	}
	unset($CONFIG["use_compiled_$kind"]);
}

/**
 * @internal Performs JavaScript minifying
 */
function minify_js($paths,$target_file)
{
	require_once(__DIR__."/minify/jsmin.php");
	$files = minify_collect_files($paths, 'js');
	log_debug("JS files to minify: ",$files);
	//die("stopped");
	$code = "";
	foreach( $files as $f )
	{
        if(starts_with($f, "/") && !starts_with($f, "//"))
            $f = (isSSL() ? "https" : "http")."://{$_SERVER['SERVER_NAME']}".$f;        
		$js = sendHTTPRequest($f,false,false,$response_header);
		if( mb_detect_encoding($js) != "UTF-8" )
			$js = mb_convert_encoding($js, "UTF-8");
		if( stripos($response_header,"404 Not Found") !== false )
			continue;
		
		$js = "/* FILE: $f */\n$js";
		if( !isset($GLOBALS['nominify']) )
		{
			try {
				$code .= jsmin::minify($js)."\n";
			} catch(Exception $ex)
			{
				WdfException::Log("EXCEPTION occured in jsmin::minify ($js)",$ex);
				$code .= $js."\n";	
			}
		}
		else
			$code .= $js."\n";
	}
	
	global $ext_resources;
	foreach( array_unique($ext_resources) as $ext )
		$code .= "$.getScript('$ext', function(){ wdf.debug('external script loaded:','$ext'); });";
	
	file_put_contents($target_file, $code);
}

/**
 * @internal Performs CSS minifying
 */
function minify_css($paths,$target_file,$nc_argument=false)
{
	require_once(__DIR__."/minify/cssmin.php");
	global $current_url;
	$files = minify_collect_files($paths, 'css');
	log_debug("CSS files to minify: ",$files);	
	//die("stopped");
	$code = "";
	$res = array();
	$map = array();
	
	foreach( $files as $f )
	{
		if( !$f )
			continue;
        if(starts_with($f, "/") && !starts_with($f, "//"))
            $f = (isSSL() ? "https" : "http")."://{$_SERVER['SERVER_NAME']}".$f;
		$css = sendHTTPRequest($f,false,false,$response_header);
		if( stripos($response_header,"404 Not Found") !== false )
			continue;
		if( mb_detect_encoding($css) != "UTF-8" )
			$css = mb_convert_encoding($css, "UTF-8");
		$current_url = parse_url($f);
		$current_url['onlypath'] = dirname($current_url['path'])."/";
		
		if( $nc_argument )
		{
			$current_url['onlypath'] = preg_replace('|/nc(.*)/|U',"/nc$nc_argument/",$current_url['onlypath']);
		}
		
		$css = preg_replace_callback("/url\s*\((.*)\)/siU", "minify_css_translate_url", $css);
		$css = preg_replace_callback("/AlphaImageLoader\(src='([^']*)'/siU", "minify_css_translate_url", $css);
		$css = "/* FILE: $f */\n$css";
		if( !isset($GLOBALS['nominify']) )
			$code .= cssmin::minify($css)."\n";
		else
		{
			$code .= "$css\n";

			$test = str_replace("\r","",str_replace("\n","",$css));
			$test = preg_replace('|/\*.*\*/|U',"",$test);
			preg_match_all("/([^}]+){([^:]+:[^}]+)}/U", $test, $items, PREG_SET_ORDER);

			foreach( $items as $item )
			{
				$keys = explode(",",$item[1]);
				foreach( $keys as $k )
				{
					$k = trim($k);
					if( isset($res[$k]) )
					{
						if( $f != $map[$k] )
							$mem = $res[$k];
					}
					else
					{
						$map[$k] = $f;
						$res[$k] = array();
					}

					foreach( explode(";",$item[2]) as $e )
					{
						$e = trim($e);
						if( $e === "" )
							continue;
						$res[$k][] = $e;
					}
					sort($res[$k]);
					$res[$k] = array_unique($res[$k]);
					if( isset($mem) )
					{
						if( implode(",",$res[$k]) != implode(",",$mem) )
						{
							if( count($res[$k]) == count($mem) )
								log_debug("[$k] defninition overrides previously defined\nFILE: $f\nPREV: {$map[$k]}\nNEW : ".implode(";",$res[$k])."\nORIG: ".implode(";",$mem)."\n");
							else
							{
								foreach( $mem as $m )
									if( !in_array($m,$res[$k]) )
									{
										log_debug("[$k] defninition extends/overrides previously defined\nFILE: $f\nPREV: {$map[$k]}\nNEW : ".implode(";",$res[$k])."\nORIG: ".implode(";",$mem)."\n");
									}
							}
						}
//						else
//							log_debug("[$k] already defined [$f -> {$map[$k]}]");
						unset($mem);
					}
				}
			}
		}
	}
	file_put_contents($target_file, $code);
}

/**
 * @internal Translates an URL in a CSS file into something absolute
 */
function minify_css_translate_url($match)
{
	global $current_url;
	$copy = $current_url;
	$url = parse_url(trim($match[1],"\"' "));
	$url = array_merge($copy,$url);	
	if( isset($url['host']) )
	{
		if( !isset($url['scheme']) || !$url['scheme'] )
			$url['scheme'] = urlScheme();
		$url = $url['scheme']."://".$url['host'].(isset($url['port'])?":{$url['port']}":"").$url['onlypath'].$url['path'];
	}
	else
		$url = $url['onlypath'].$url['path'];
	return "url($url)";
}

/**
 * @internal Collects files for minifying
 */
function minify_collect_files($paths,$kind)
{
	global $dependency_info, $res_file_storage, $ext_resources;
	$dependency_info = array();
	$res_file_storage = array();
	$ext_resources = array();
	
	foreach( $paths as $path )
	{
		if( !ends_with($path, "/") ) $path .= "/";
		foreach( system_glob_rec($path,'*.class.php') as $f )
			minify_collect_from_file($kind,$f);
		
		$done_templates = array();
		foreach( array_reverse(cfg_get('system','tpl_ext')) as $tpl_ext )
		{
			$files = system_glob_rec($path,"*.$tpl_ext");
			foreach( $files as $f )
			{
				$skip = false;
				foreach( $done_templates as $d )
					if( isset($res_file_storage[basename($f,".$d")]) ){ $skip = true; break; }
				if( $skip ) continue;
				minify_collect_from_file($kind,$f);
			}
			$done_templates[] = $tpl_ext;
		}
	}
	
	log_debug("minify file info",$dependency_info,$res_file_storage);
	
	$res = array();
	$classes = array_keys($res_file_storage);
	foreach( $classes as $class )
		$res = array_merge($res, minify_resolve_dependencies($class,$dependency_info,$res_file_storage));
	
	unset($dependency_info);
	unset($res_file_storage);
	return array_unique($res);
}

/**
 * @internal Resolves dependencies
 */
function minify_resolve_dependencies($classname,&$dependency_info,&$res_file_storage,$tree=array())
{
	$res = array();
	if( isset($dependency_info[$classname]) )
	{	
		if( !in_array($classname,$tree) )
		{
			$tree[] = $classname;
			foreach( $dependency_info[$classname] as $dependency )
				$res = array_merge($res,  
					minify_resolve_dependencies($dependency, $dependency_info, $res_file_storage, $tree));
		}
	}
	
	if( isset($res_file_storage[$classname]) )
	{
		$res = array_merge($res, $res_file_storage[$classname]);
		unset($res_file_storage[$classname]);
	}
	return $res;
}

/**
 * @internal Collects dependencies from a file
 */
function minify_collect_from_file($kind,$f,$debug_path='')
{
	global $dependency_info, $res_file_storage, $ext_resources;
	
	if( !$f )
		return;
	$classname = fq_class_name(array_shift(explode('.',basename($f))) );
	if( isset($res_file_storage[$classname]) || minify_forbidden($classname) )
		return;
	
	$order = array('static','inherited','instanciated','self');
		//:array('self','incontent','instanciated','inherited');
	
	$res_file_storage[$classname] = array();
	$content = file_get_contents($f);
	
	// remove block-comments
	$content = preg_replace("|/\*.*\*/|sU","",$content);
	do // remove line-comments in loop to catch subsequent comments too (start at the rightmost)
	{
		$c2 = preg_replace("|(.*)//.*$|m","$1",$content);
		if( $content == $c2 )
			break;
		$content = $c2;
	}while( true );
	
	foreach( $order as $o )
	{
		switch( $o )
		{
			case 'inherited':
				if( preg_match_all('/class\s+[^\s]+\s+extends\s+([^\s]+)/', $content, $matches, PREG_SET_ORDER) )
				{
//					log_debug("minify_collect_from_file [$debug_path/$classname]: INHERITED",$matches);
					foreach( $matches as $m )
					{
						$file_for_class = __search_file_for_class($m[1]);
						if( !$file_for_class )
							continue;
						$dependency_info[$classname][] = strtolower($m[1]);
						minify_collect_from_file($kind,$file_for_class,$debug_path.'/'.$classname);
					}
				}
				break;
			case 'instanciated':
				$matches = array();
				if( preg_match_all('/new\s+([^\(]+)\(/', $content, $by_new, PREG_SET_ORDER) )
					$matches = array_merge($matches,$by_new);
				if( preg_match_all('/\s+([^:\s\(\)]+)::Make\(/Ui', $content, $by_make, PREG_SET_ORDER) )
					$matches = array_merge($matches,$by_make);
				if( count($matches)>0 )
				{
//					log_debug("minify_collect_from_file [$debug_path/$classname]: INSTANCIATED",$matches);
					foreach( $matches as $m )
					{
						$file_for_class = __search_file_for_class($m[1]);
						if( !$file_for_class )
							continue;
						$dependency_info[$classname][] = strtolower($m[1]);
						minify_collect_from_file($kind,$file_for_class,$debug_path.'/'.$classname);
					}
				}
				break;
			case 'self':
				if( resourceExists(strtolower("$classname.$kind")) )
				{
					$tmp = resFile(strtolower("$classname.$kind"));
					if( !in_array($tmp,$res_file_storage[$classname]) )
						$res_file_storage[$classname][] = $tmp;
				}
				break;
			case 'static':
				try
				{
					foreach( ResourceAttribute::Collect($classname) as $resource )
					{
						$b = $resource->Resolve();
						if( $resource instanceof \ScavixWDF\Reflection\ExternalResourceAttribute )
						{
							$ext_resources[] = $b;
							continue;
						}
						if( !ends_with($b, $kind) )
							continue;
						$b = strtolower($b);
						if( !in_array($b,$res_file_storage[$classname]) )
							$res_file_storage[$classname][] = $b;
					}
				}
				catch(Exception $ex){}
				break;
		}
	}
}
