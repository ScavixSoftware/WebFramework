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

use ScavixWDF\ICallable;
use ScavixWDF\Localization\Localization;

default_string('ERR_JAVASCRIPT_AND_COOKIES_REQUIRED','This page requires JavaScript and Cookies.');
/**
 * Base class for all Html pages.
 * 
 * Will perform all rendering and collect js, css, meta and more.
 * @attribute[Resource('jquery.js')]
 * @attribute[Resource('jquery.json.js')]
 * @attribute[Resource('htmlpage.js')]
 */
class HtmlPage extends Template implements ICallable
{
	var $meta = array();
	var $js = array();
	var $css = array();
	var $docready = array();
	var $plaindocready = array();
	var $wdf_settings = array('focus_first_input'=>true);

	/**
	 * Setting this to a filename (relative to class) will load it as subtemplate
	 * @var bool|string Templatename or false
	 */
	var $SubTemplate = false;

	/**
	 * @param string $title Page title
	 * @param string $body_class Optional value for the class attribute of the &lt;body&gt; element
	 */
	function __initialize($title="", $body_class=false)
	{
		// this makes HtmlPage.tpl.php the 'one and only' template
		// for all derivered classes, unless they override it after
		// parent::__initialize with $this->file = X
		$file = str_replace(".class.php",".tpl.php",__FILE__);
		parent::__initialize($file);

		$this->set("title",$title);
		$this->set("meta",$this->meta);
		$this->set("js",$this->js);
		$this->set("css",$this->css);
		$this->set("content",array());
		$this->set("docready",$this->docready);
		$this->set("plaindocready",$this->plaindocready);
		
		if( $body_class )
			$this->set("bodyClass","$body_class");

		if( resourceExists("favicon.ico") )
			$this->set("favicon", resFile("favicon.ico"));
		
		// set up correct display on mobile devices: http://stackoverflow.com/questions/8220267/jquery-detect-scroll-at-bottom
		$this->addMeta("viewport","width=device-width, height=device-height, initial-scale=1.0");
	}

	/**
	 * @override
	 */
	function WdfRenderAsRoot()
	{
		execute_hooks(HOOK_PRE_RENDER,array($this));

		$init_data = $this->wdf_settings;
		$init_data['request_id'] = request_id();
		$init_data['site_root']  = cfg_get('system','url_root');
		
		if( cfg_getd('system','attach_session_to_ajax',false) )
		{
			$init_data['session_id'] = session_id();
			$init_data['session_name'] = session_name();
		}
		if( isDevOrBeta() )
			$init_data['log_to_console'] = true;
		if( $GLOBALS['CONFIG']['system']['ajax_debug_argument'] ) 
			$init_data['log_to_server'] = $GLOBALS['CONFIG']['system']['ajax_debug_argument'];
		
		$this->set("wdf_init","wdf.init(".json_encode($init_data).");");
		$this->set("docready",$this->docready);
		$this->set("plaindocready",$this->plaindocready);

		return parent::WdfRenderAsRoot();
	}
	
	/**
	 * @override
	 */
	function WdfRender()
	{
		if( !$this->get('isrtl') && system_is_module_loaded('localization') )
		{
			$ci = Localization::detectCulture();
			if( $ci->IsRTL )
				$this->set("isrtl", " dir='rtl'");
		}
		
		$res = $this->__collectResources();
		$this->js = array_reverse($this->js,true);
		foreach( array_reverse($res) as $r )
		{
			if( starts_with(pathinfo($r,PATHINFO_EXTENSION), 'css') )
				$this->addCss($r);
			else
				$this->addjs($r);
		}
		$this->js = array_reverse($this->js,true);
		
		$this->set("css",$this->css);
		$this->set("js",$this->js);
		$this->set("meta",$this->meta);
		$this->set("content",$this->_content);
		
		return parent::WdfRender();
	}

	/**
	 * Adds a meta tag to the page
	 * 
	 * Like this &lt;meta name='$name' content='$content' scheme='$scheme'/&gt;
	 * @param string $name The name
	 * @param string $content The content
	 * @param string $scheme The scheme
	 * @param string $type The meta-tags name ('name','http-equiv',...)
	 * @return HtmlPage `$this`
	 */
	function addMeta($name,$content,$scheme="",$type='name')
	{
		$meta = "\t<meta $type='$name' content='$content'".(($scheme=="")?"":" scheme='$scheme'")."/>\n";
		$this->meta[$name.$content] = $meta;
		return $this;
	}

	/**
	 * Adds a link tag to the page
	 * 
	 * Like this: &lt;link rel='$rel' type='$type' title='$title' href='$href'/&gt;
	 * @param string $rel The rel attribute
	 * @param string $href The href attribute
	 * @param string $type The type attribute
	 * @param string $title The title attribute
	 * @return HtmlPage `$this`
	 */
	function addLink($rel,$href,$type="",$title="")
	{
		if( isset($this->meta[$rel.$href.$type]) )
			return;
		$meta = "\t<link rel='$rel' type='$type' title=\"$title\" href='$href'/>\n";
		$this->meta[$rel.$href.$type] = $meta;
		return $this;
	}

	/**
	 * Adds a script tag to the page
	 * 
	 * Like this: &lt;script type='text/javascript' src='$src'&gt;&lt;/script&gt;
	 * @param string $src The src attribute
	 * @return HtmlPage `$this`
	 */
	function addJs($src)
	{
		if( isset($this->js[$src]) )
			return;
		$js = "\t<script type='text/javascript' src='$src'></script>\n";
		$this->js[$src] = $js;
		return $this;
	}

	/**
	 * Adds a link tag (type=css) to the page 
	 * 
	 * Like this: &lt;link rel='stylesheet' type='text/css' href='$src'/&gt;
	 * @param string $src The src attribute
	 * @return HtmlPage `$this`
	 */
	function addCss($src)
	{
		if( isset($this->css[$src]) )
			return;
		$css = "\t<link rel='stylesheet' type='text/css' href='$src'/>\n";
		$this->css[$src] = $css;
		return $this;
	}

	/**
	 * Adds code to the document ready event.
	 * 
	 * @param mixed $js_code JS code as string or array
	 * @param bool $jq_wrapped If true adds the code to the ready event handler, else will be added inline into the head script element
	 * @return HtmlPage `$this`
	 */
	function addDocReady($js_code,$jq_wrapped=true)
	{
		if( is_array($js_code) )
			$js_code = implode("\n",$js_code);
		if( !trim($js_code) )
			return $this;

		$k = "k".md5($js_code);
		if( $jq_wrapped )
		{
			if( !isset($this->docready[$k]) )
				$this->docready[$k] = $js_code;
		}
		else
		{
			if( !isset($this->plaindocready[$k]) )
				$this->plaindocready[$k] = $js_code;
		}
		return $this;
	}

	/**
	 * Sets InternetExplorer 'Pinned Site Metadata'
	 * 
	 * See http://msdn.microsoft.com/en-us/library/ie/gg491732%28v=vs.85%29.aspx
	 * @param string $application See [application-name](http://msdn.microsoft.com/en-us/library/ie/gg491732%28v=vs.85%29.aspx#application-name)
	 * @param string $tooltip See [msapplication-tooltip](http://msdn.microsoft.com/en-us/library/ie/gg491732%28v=vs.85%29.aspx#msapplication-tooltip)
	 * @param string $start_url See [msapplication-starturl](http://msdn.microsoft.com/en-us/library/ie/gg491732%28v=vs.85%29.aspx#msapplication-starturl)
	 * @param string $button_color See [msapplication-navbutton-color](http://msdn.microsoft.com/en-us/library/ie/gg491732%28v=vs.85%29.aspx#msapplication-navbutton-color)
	 * @return HtmlPage `$this`
	 */
	function SetIE9PinningData($application,$tooltip,$start_url,$button_color=false)
	{
		if ( !isset($_SERVER['HTTP_USER_AGENT']) || ((strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 9' ) === false) && (strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 10' ) === false)) )
			return;
		$this->addMeta('application-name',"$application");
		$this->addMeta('msapplication-tooltip',"$tooltip");
		$this->addMeta('msapplication-starturl',"$start_url");
		$this->addMeta('msapplication-window',"width=1024;height=768");
		if( $button_color )
			$this->addMeta('msapplication-navbutton-color',"$button_color");
		return $this;
	}
	
	/**
	 * Adds a task to the InternetExplorer 'Jump List'
	 * 
	 * See http://msdn.microsoft.com/en-us/library/ie/gg491725%28v=vs.85%29.aspx
	 * @param string $name The task name that appears in the Jump List
	 * @param string $url The address that is launched when the item is clicked
	 * @param string $icon_url The icon resource that appears next to the task in the Jump List
	 * @param string $window_type One of 'tab', 'self' or 'window'
	 * @return HtmlPage `$this`
	 */
	function AddIE9PinningTask($name,$url,$icon_url,$window_type="tab")
	{
		if ( !isset($_SERVER['HTTP_USER_AGENT']) || ((strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 9' ) === false) && (strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 10' ) === false)) )
			return;
		$this->addMeta('msapplication-task',"name=$name; action-uri=$url; icon-uri=$icon_url; window-type=$window_type");
		return $this;
	}
}
