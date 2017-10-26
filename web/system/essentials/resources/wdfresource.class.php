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
	 * Writes out correct cache headers.
	 * 
	 * Writes best matching and of course correct caching headers to the browser
	 * for a given file (full path).
	 * @param string $file Full path and filename
	 * @return void
	 */
	public static function ValidatedCacheResponse($file)
	{
		$etag = md5($file);
		$days = 365*86400;
		$cached = cache_get("etag_$etag",false);
		$mtime = gmdate("D, d M Y H:i:s GMT",filemtime($file));
		header("Expires: ".gmdate("D, d M Y H:i:s e",time()+$days));
		header("Last-Modified: ".$mtime);
		header('Pragma: public');
		header("Cache-Control: public, max-age=$days");
		header("ETag: $etag");
		$headers = getallheaders();
		if( $cached )
		{
			if( isset($headers['If-None-Match']) && $headers['If-None-Match'] == $etag )
			{
				header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
				die();
			}
			if( isset($headers['If-Modified-Since']) && strtotime($headers['If-Modified-Since']) >= strtotime($mtime) )
			{
				header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
				die();
			}
		}
		cache_set("etag_$etag",$mtime);
	}
	
	/**
	 * @internal Returns a JS resource
	 * @attribute[RequestParam('res','string')]
	 */
	function js($res)
	{
		$res = explode("?",$res);
		$res = realpath(__DIR__."/../../js/".$res[0]);
		header('Content-Type: text/javascript');
		if( $res )
		{
			WdfResource::ValidatedCacheResponse($res);
			readfile($res);
		}
		else
			header("HTTP/1.0 404 Not Found");
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
		if(ends_iwith($res, '.css'))
			header('Content-Type: text/css');
		elseif(ends_iwith($res, '.png'))
			header('Content-Type: image/png');
		elseif(ends_iwith($res, '.jpg'))
			header('Content-Type: image/jpeg');
		elseif(ends_iwith($res, '.gif'))
			header('Content-Type: image/gif');
		if( $res )
		{
			WdfResource::ValidatedCacheResponse($res);
            die( $this->resolveUrls($res) );
		}
		else
			header("HTTP/1.0 404 Not Found");
		die();
	}
	
	/**
	 * @internal Compiles a LESS file to CSS and delivers that to the browser
	 * @attribute[RequestParam('file','string')]
	 */
	function CompileLess($file)
	{
		$vars = isset($_SESSION['resources_less_variables'])?$_SESSION['resources_less_variables']:array();
        $dirs = isset($_SESSION['resources_less_dirs'])?$_SESSION['resources_less_dirs']:false;
		$file_key = md5($file.serialize($vars).serialize($dirs));
		
		$less = resFile(basename($file),true);
		$css = sys_get_temp_dir().'/'.$file_key.'.css';
		$cacheFile = sys_get_temp_dir().'/'.$file_key.'.cache';
		
		header('Content-Type: text/css');
		
		if( file_exists($css) && file_exists($cacheFile) )
			$cache = unserialize(file_get_contents($cacheFile));
		else
			$cache = $less;
		
		require_once(__DIR__.'/lessphp/lessc.inc.php');
		$compiler = new \lessc();
		$compiler->setVariables($vars);
        if( $dirs )
            $compiler->setImportDir(array_merge([''],$dirs));
        
		$newCache = $compiler->cachedCompile($cache);
		if( !is_array($cache) || $newCache["updated"] > $cache["updated"] )
		{
			file_put_contents($cacheFile, serialize($newCache));
			file_put_contents($css, $newCache['compiled']);
		}
		WdfResource::ValidatedCacheResponse($less);
        die( $this->resolveUrls($css) );
	}
    
    private function resolveUrls($file)
    {
        return preg_replace_callback("/url\s*\(['\"]*resfile\/(.*)['\"]*\)/siU",function($match)
        {
            $url = trim($match[1],"\"' ");
            return "url('".resFile($url)."')";
        }, file_get_contents($file));
    }
}