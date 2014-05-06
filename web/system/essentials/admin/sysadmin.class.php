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
namespace ScavixWDF\Admin;

use ScavixWDF\Base\AjaxResponse;
use ScavixWDF\Base\Control;
use ScavixWDF\Base\HtmlPage;
use ScavixWDF\Base\Template;
use ScavixWDF\Controls\Anchor;
use ScavixWDF\Controls\Form\CheckBox;
use ScavixWDF\Controls\Form\Form;
use ScavixWDF\Controls\Form\Select;
use ScavixWDF\Controls\Form\TextInput;
use ScavixWDF\Controls\Table\Table;

/**
 * ScavixWDF sysadmin page
 * 
 * This is a tweak mechanism that allows you to manage your application.
 * For example you can create strings, manage the cache and check the PHP configuration.
 * @attribute[NoMinify]
 */
class SysAdmin extends HtmlPage
{
	var $PrefedinedCacheSearches = array('autoload_template','autoload_class',
		'lang_','method_','ref_attr_','resource_','filemtime_','doccomment_','DB_Cache_');
    
    protected $_contentdiv = false;
	protected $_subnav = false;
	
	function __initialize($title = "", $body_class = false)
    {
        global $CONFIG;
		
		header("Content-Type: text/html; charset=utf-8"); // overwrite previously set header to ensure we deliver HTML
		unset($CONFIG["use_compiled_js"]);
		unset($CONFIG["use_compiled_css"]);
        
        if( current_event(true) != 'login'
            &&
            (   
                !isset($_SESSION['admin_handler_username']) 
                || !isset($_SESSION['admin_handler_password']) 
                || $_SESSION['admin_handler_username'] != $CONFIG['system']['admin']['username']
                || $_SESSION['admin_handler_password'] != $CONFIG['system']['admin']['password'] 
            ) )
            redirect('SysAdmin','Login');
        
        parent::__initialize("SysAdmin - $title", 'sysadmin');
        $this->_translate = false;
        
        if( current_event(true) != 'login' )
        {
            $nav = parent::content(new Control('div'));
            $nav->class = "navigation";
			
			foreach( $CONFIG['system']['admin']['actions'] as $label=>$def )
			{
				if( !class_exists(fq_class_name($def[0])) )
					continue;
				$nav->content( new Anchor(buildQuery($def[0],$def[1]),$label) );
			}
            $nav->content( new Anchor(buildQuery('SysAdmin','Cache'),'Cache') );
            $nav->content( new Anchor(buildQuery('SysAdmin','PhpInfo'),'PHP info') );
            $nav->content( new Anchor(buildQuery('TranslationAdmin','NewStrings'),'Translations') );
            $nav->content( new Anchor(buildQuery('SysAdmin','Testing'),'Testing') );
            $nav->content( new Anchor(buildQuery('',''),'Back to app') );
            $nav->content( new Anchor(buildQuery('SysAdmin','Logout'),'Logout', 'logout') );
			
			$this->_subnav = parent::content(new Control('div'));
        }
        
        $this->_contentdiv = parent::content(new Control('div'))->addClass('content');
        
        $copylink = new Anchor('http://www.scavix.com', '&#169; 2012-'.date('Y').' Scavix&#174; Software Ltd. &amp; Co. KG');
        $copylink->target = '_blank';
        $footer = parent::content(new Control('div'))->addClass('footer');
		$footer->content("<br class='clearer'/>");
        $footer->content($copylink);
        
        if( (current_event() == strtolower($CONFIG['system']['default_event'])) && !system_method_exists($this, current_event()) )
            redirect('SysAdmin', 'Index');
    }

	/**
	 * @override Redirects contents to inner content div
	 */
	function content($content)
	{
		return $this->_contentdiv->content($content);
	}
	
	protected function subnav($label,$controller,$method)
	{
		if( $this->_subnav )
		{
			$this->_subnav->content( new Anchor(buildQuery($controller,$method),$label) );
			$this->_subnav->class = "navigation";
		}
	}
	
	/**
	 * @internal SysAdmin index page.
	 */
	function Index()
	{
		$this->content("<h1>Welcome,</h1>");
		$this->content("<p>please select an action from the top menu.</p>");
	}
	
    /**
	 * @internal SysAdmin login page.
     * @attribute[RequestParam('username','string',false)]
     * @attribute[RequestParam('password','string',false)]
     */
	function Login($username,$password)
	{
        global $CONFIG;
        
        if( $username===false || $password===false )
        {
            $this->content("<br/><br/>");
            $this->content(Template::Make('sysadminlogin'));
            return;
        }
        
        if( $username != $CONFIG['system']['admin']['username'] || $password != $CONFIG['system']['admin']['password'] )
            redirect(get_class_simple($this),'Login');
        
        $_SESSION['admin_handler_username'] = $username;
        $_SESSION['admin_handler_password'] = $password;
        redirect(get_class_simple($this));
	}
    
    /**
	 * @internal SysAdmin logout event.
     */
    function Logout()
    {
        unset($_SESSION['admin_handler_username']);
        unset($_SESSION['admin_handler_password']);
        redirect(get_class_simple($this),'Login');
    }
	
	/**
	 * @internal SysAdmin cache manager.
	 * @attribute[RequestParam('search','string',false)]
	 * @attribute[RequestParam('show_info','bool',false)]
	 * @attribute[RequestParam('kind','string','Search key')]
     */
    function Cache($search,$show_info,$kind)
    {
		$this->content("<h1>Cache contents</h1>");
		
		$form = $this->content( new Form() );
		$form->AddText('search',$search);
		$form->AddSubmit('Search key')->name = 'kind';
		$form->AddSubmit('Search content')->name = 'kind';
		
		$form->content( '&nbsp;&nbsp;&nbsp;' );
		$form->content( new Anchor(buildQuery('SysAdmin','CacheClear'),'Clear the complete cache') );
		
		if( system_is_module_loaded('globalcache') )
		{
			$form->content( '&nbsp;&nbsp;' );
			$form->content( new Anchor(buildQuery('SysAdmin','Cache','show_info=1'),'Global cache info') );
		}
		
		$form->content( '<div><b>Predefined searches:</b><br/>' );
		foreach( $this->PrefedinedCacheSearches as $s )
		{
			$form->content( new Anchor(buildQuery('SysAdmin','Cache',"search=$s"),"$s") );
			$form->content( '&nbsp;' );
		}
		$form->content( '</div>' );
		
		if( !isset($_SESSION['admin_handler_last_cache_searches']) )
			$_SESSION['admin_handler_last_cache_searches'] = array();

		if( count($_SESSION['admin_handler_last_cache_searches']) > 0 )
		{
			$form->content( '<div><b>Last searches:</b><br/>' );
			foreach( $_SESSION['admin_handler_last_cache_searches'] as $s )
			{
				list($k,$s) = explode(":",$s);
				$form->content( new Anchor(buildQuery('SysAdmin','Cache',"search=$s".($k!='key'?'&kind=Search content':'')),"$k:$s") );
				$form->content( '&nbsp;' );
			}
			$form->content( '</div>' );
		}
		
		if( $show_info && system_is_module_loaded('globalcache') )
			$form->content( "<pre>".globalcache_info()."</pre>" );
		
		if( $search )
		{
			if( !in_array($search,$this->PrefedinedCacheSearches) )
			{
				$_SESSION['admin_handler_last_cache_searches'][] = ($kind=='Search content')?"content:$search":"key:$search";
				$_SESSION['admin_handler_last_cache_searches'] = array_unique($_SESSION['admin_handler_last_cache_searches']);
			}
			
			$this->content("<br/>");
			$tabform = $this->content( new Form() );
			$tabform->action = buildQuery('SysAdmin','CacheDelMany');
			$tab = $tabform->content(new Table())->addClass('bordered');
			$tab->SetHeader('','key','action');
			$q = buildQuery('SysAdmin','CacheDel');
			foreach( cache_list_keys() as $key )
			{
				$found = ($kind=='Search content')
					?stripos( render_var(cache_get($key,"")), $search) !== false
					:stripos( $key, $search) !== false;
				if( $found )
				{
					$cb = new CheckBox('keys[]');
					$cb->value = $key;
					
					$del = new Anchor('','delete');					
					$del->onclick = "$.post('$q',{key:'".addslashes($key)."'},function(){ $('#{$del->id}').parents('.tr').fadeOut(function(){ $(this).remove(); }); })";
					$tab->AddNewRow($cb,$key,$del);
				}
			}
			$footer = $tab->Footer()->NewCell();
			$footer->colspan = 2;
			$footer->content( new Anchor('','all') )->onclick = "$('#{$tab->id} .tbody input').prop('checked',true);";
			$footer->content('&nbsp;');
			$footer->content( new Anchor('','none') )->onclick = "$('#{$tab->id} .tbody input').prop('checked',false)";
			
			$footer = $tab->Footer()->NewCell();
			$footer->content( new Anchor('','delete') )->onclick = "$('#{$tabform->id}').submit()";
		}
    }
	
	/**
	 * @internal SysAdmin cache manager: delete event.
	 * @attribute[RequestParam('key','string',false)]
     */
    function CacheDel($key)
	{
		cache_del($key);
		return AjaxResponse::None();
	}
	
	/**
	 * @internal SysAdmin cache manager: delete many event.
	 * @attribute[RequestParam('keys','array',array())]
     */
	function CacheDelMany($keys)
	{
		foreach( $keys as $k )
			cache_del($k);
		redirect('SysAdmin','Cache');
	}
	
	/**
	 * @internal SysAdmin cache manager: clear event.
     */
	function CacheClear()
	{
		cache_clear();
		redirect('SysAdmin','Cache');
	}
	
	/**
	 * @internal SysAdmin phpinfo.
	 * @attribute[RequestParam('extension','string',false)]
	 * @attribute[RequestParam('search','string',false)]
	 * @attribute[RequestParam('dump_server','bool',false)]
	 */
	function PhpInfo($extension,$search,$dump_server)
	{
		if( $dump_server )
			$search = $extension = "";
		if( $search )
			$extension = null;
		
		foreach( ini_get_all() as $k=>$v)
		{
			$k = explode('.',$k,2);
			if( count($k)<2 )
				$k = array('Core',$k[0]);

			$data[$k[0]][$k[1]] = $v;
		}
		ksort($data);
		
		$tab = $this->content( Table::Make() );
		$tab->addClass('phpinfo')
			->SetCaption("Basic information")
			->AddNewRow("PHP version",phpversion())
			->AddNewRow("PHP ini file",php_ini_loaded_file())
			->AddNewRow("SAPI",php_sapi_name())
			->AddNewRow("OS",php_uname())
			->AddNewRow("Apache version",apache_get_version())
			->AddNewRow("Apache modules",implode(', ',apache_get_modules()))
			->AddNewRow("Loaded extensions",implode(', ',get_loaded_extensions()))
			->AddNewRow("Stream wrappers",implode(', ',stream_get_wrappers()))
			->AddNewRow("Stream transports",implode(', ',stream_get_transports()))
			->AddNewRow("Stream filters",implode(', ',stream_get_filters()))
			;
		
		$ext_nav = $this->content(new Control('div'))->css('margin-bottom','25px');
		$ext_nav->content("Select extension: ");
		$sel = $ext_nav->content(new Select());
		$ext_nav->content("&nbsp;&nbsp;&nbsp;Or search: ");
		$tb = $ext_nav->content(new TextInput());
		$tb->value = $search;
		
		$q = buildQuery('SysAdmin','PhpInfo');
		$sel->onchange = "wdf.redirect({extension:$(this).val()})";
		$tb->onkeydown = "if( event.which==13 ) wdf.redirect({search:$(this).val()})";

		$ext_nav->content('&nbsp;&nbsp;&nbsp;Or ');
		$q = buildQuery('SysAdmin','PhpInfo','dump_server=1');
		$ext_nav->content( new Anchor($q,'dump the $_SERVER variable') );
		
		$get_version = function($ext)
		{
			$res = ($ext=='zend')?zend_version():phpversion($ext);
			return $res?" [$res]":'';
		};
		
		$sel->SetCurrentValue($extension)->AddOption('','(select one)');
		$sel->AddOption('all','All values');
		foreach( array_keys($data) as $ext )
		{
			$ver = ($ext=='zend')?zend_version():phpversion($ext);
			$sel->AddOption($ext,$ext.$get_version($ext)." (".count($data[$ext]).")");
		}
		
		if( $dump_server )
		{
			$tab = $this->content( new Table() )
				->addClass('phpinfo')
				->SetCaption('Contents of the $_SERVER variable')
				->SetHeader('Name','Value');
			foreach( $_SERVER as $k=>$v )
				$tab->AddNewRow($k,$v);
		}
		if( $extension || $extension == 'all' || $search )
		{
			foreach( $data as $k=>$config )
			{
				if( !$search && $k != $extension && $extension != 'all' )
					continue;
				
				$tab = false;
				foreach( $config as $ck=>$v )
				{
					if( $search && stripos($ck,$search)===false && stripos($v['local_value'],$search)===false && stripos($v['global_value'],$search)===false )
						continue;
					
					if( !$tab )
					{
						$tab = $this->content( new Table() )
							->addClass('phpinfo')
							->SetCaption($k.$get_version($k))
							->SetHeader('Name','Local','Master');
					}
					$tr = $tab->NewRow(array($ck,$v['local_value'],$v['global_value']));
					if( $v['local_value']!=='' && $v['local_value'] != $v['global_value'] )
						$tr->GetCell(2)->css('color','red');
				}
			}
		}
	}
	
	/**
	 * @internal This is just an entry point for testing.
	 */
	function Testing()
	{
		
	}
}
