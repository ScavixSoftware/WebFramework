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

/**
 * <SysAdmin> handler for translations.
 * 
 * @attribute[NoMinify]
 */
class TranslationAdmin extends TranslationAdminBase
{
	var $ds;
	
	function __initialize($title = "", $body_class = false)
    {
        parent::__initialize($title, $body_class);
        if( isset($GLOBALS['CONFIG']['translation']['sync']['scavix_datasource']) && $GLOBALS['CONFIG']['translation']['sync']['scavix_datasource'] )
			$this->ds = model_datasource($GLOBALS['CONFIG']['translation']['sync']['scavix_datasource']);
		elseif( isset($GLOBALS['CONFIG']['translation']['sync']['datasource']) && $GLOBALS['CONFIG']['translation']['sync']['datasource'] )
			$this->ds = model_datasource($GLOBALS['CONFIG']['translation']['sync']['datasource']);
		else
			WdfException::Raise("ScavixTranslations datasource missing!");
		
		$this->ds->ExecuteSql("CREATE TABLE IF NOT EXISTS `wdf_translations` (
				`lang` VARCHAR(10) NULL,
				`id` VARCHAR(100) NULL,
				`content` TEXT NULL,
				PRIMARY KEY (`lang`, `id`) );");
		
		$this->subnav('Translate', 'TranslationAdmin', 'Translate');
		$this->subnav('Import', 'TranslationAdmin', 'Import');
    }
	
	private function fetchTerms($lang_code,$defaults = false)
    {
        $rs = $this->ds->ExecuteSql("SELECT id,content FROM wdf_translations WHERE lang=?",$lang_code);
        $res = array();
        foreach( $rs as $lang )
        {
            $res[$lang['id']] = isset($lang['content'])&&$lang['content']?$lang['content']:'';
            if( !$res[$lang['id']] && $defaults )
                $res[$lang['id']] = $defaults[$lang['id']];
        }
        return $res;
    }
	
	private function _languageSelect($lang)
	{
		$sel = new Select();
		$sel->SetCurrentValue($lang);
		foreach( Localization::get_language_names() as $code=>$name )
			$sel->AddOption($code,$name);
		return $sel;
	}
	
    /**
	 * @internal Fetch action handler
     * @attribute[RequestParam('languages','array',false)]
     */
    function Fetch($languages = false)
    {
        global $CONFIG;
        
        $this->_contentdiv->content("<h1>Fetch strings</h1>");
        $db_languages = $this->ds->ExecuteSql("SELECT DISTINCT lang FROM wdf_translations ORDER BY lang")->Enumerate('lang',false);
		$max = $this->ds->ExecuteScalar("SELECT MAX(cnt) FROM (SELECT count(*) as cnt FROM wdf_translations GROUP BY lang) AS x");
		foreach( $db_languages as $i=>$lang )
		{
			$count = $this->ds->ExecuteScalar("SELECT count(*) FROM wdf_translations WHERE lang=?",$lang);
			
			$db_languages[$i] = new stdClass();
			$db_languages[$i]->name = Localization::getCultureInfo($lang)->EnglishName;
			$db_languages[$i]->code = $lang;
			$db_languages[$i]->percentage = round($count / $max * 100,0);
		}
        
        if( !$languages )
        {
            $div = $this->_contentdiv->content(new Form());
            foreach( $db_languages as $lang )
            {
                $cb = $div->content( new CheckBox('languages[]') );
                $cb->value = $lang->code;
                $div->content($cb->CreateLabel($lang->name." ({$lang->code}, {$lang->percentage}% complete)"));
                $div->content("<br/>");
            }
            $a = $div->content(new Anchor('#','Select all'));
            $a->script("$('#{$a->id}').click(function(){ $('input','#{$div->id}').attr('checked',true); });");
            $div->content("&nbsp;&nbsp;");
            $div->AddSubmit("Fetch");
            return;
        }
        
        $head = array();
        foreach( $db_languages as $lang )
            $head[$lang->code] = array('percentage_complete'=>$lang->percentage/100, 'percentage_empty'=>(1-$lang->percentage/100), 'syntax_error_qty'=>0);
        $info = "\$GLOBALS['translation']['properties'] = ".var_export($head,true);
        
        $defaults = $this->fetchTerms($CONFIG['localization']['default_language']);
        foreach( array_unique($languages) as $lang )
        {
            $lang = strtolower($lang);
            $data = $lang == $CONFIG['localization']['default_language']?$defaults:$this->fetchTerms($lang,$defaults);
            $strings = "\$GLOBALS['translation']['strings'] = ".var_export($data,true);
            file_put_contents(
                $CONFIG['translation']['data_path'].$lang.'.inc.php', 
                "<?\n$info;\n$strings;\n"
            );
            $this->_contentdiv->content("<div>Created translation file for $lang</div>");
        }
		
		$ds = model_datasource($GLOBALS['CONFIG']['translation']['sync']['datasource']);
		$ds->ExecuteSql("TRUNCATE TABLE wdf_unknown_strings");
		$this->_contentdiv->content("<div>Cleared the unknown strings table</div>");
		
		foreach( cache_list_keys() as $key )
		{
			if( starts_with($key, 'lang_') )
				cache_del($key);
		}
		$this->_contentdiv->content("<div>Cleared the string cache</div>");
    }
    
    /**
	 * @internal Create new string handler
     * @attribute[RequestParam('term','string')]
     * @attribute[RequestParam('text','string','')]
     */
    function CreateString($term,$text)
    {
        global $CONFIG;
		$text = urldecode($text);
		$this->ds->ExecuteSql("REPLACE INTO wdf_translations(lang,id,content)VALUES(?,?,?)",array($CONFIG['localization']['default_language'],$term,$text));
        cache_del('lang_'.$term);
        return $this->DeleteString($term);
    }
	
	/**
	 * @attribute[RequestParam('lang','string',false)]
	 * @attribute[RequestParam('offset','int',0)]
	 */
	function Translate($lang,$offset)
	{
		global $CONFIG;
		$lang = $lang?$lang:$CONFIG['localization']['default_language'];
		
		$this->content( $this->_languageSelect($lang) )
			->script("$('#{self}').change(function(){ wdf.redirect({lang:$(this).val()}); });");

		$tab = Table::Make()->addClass('translations')
			->SetHeader('Term','Default','Content','')
			->setData('lang',$lang)
			->appendTo($this);
		
		$translated = array();
		foreach( $this->ds->ExecuteSql("SELECT id,content FROM wdf_translations WHERE lang=?",$lang) as $row )
			$translated[$row['id']] = $row['content'];
		
		$rs = $this->ds->Query('wdf_translations')->eq('lang',$CONFIG['localization']['default_language'])->page($offset,20);
		foreach( $rs as $term )
		{
			if( $lang == $CONFIG['localization']['default_language'] )
				$translation = $term->content;
			else
				$translation = isset($translated[$term->id])?$translated[$term->id]:'';
			
			$ta = new TextArea($translation);
			$ta->class = $term->id;
			$btn = new Button('Save');
			$btn->addClass('save')->setData('term',$term->id);
			
			$tab->AddNewRow($term->id,$term->content,$ta,$btn);
		}
		
		$pi = $rs->GetPagingInfo();
		for($page=1;$page<$pi['total_pages'];$page++)
		{
			$offset = ($page-1) * $pi['rows_per_page'];
			$label = ($offset+1)."-".($page*$pi['rows_per_page']);
			if( $page == $pi['current_page'] )
				$this->content("<b>$label</b>");
			else
				$this->content(new Anchor(buildQuery('TranslationAdmin','Translate',"lang=$lang&offset=$offset"),"$label"));
			$this->content("&nbsp;");
		}
	}
	
	/**
	 * @internal Save string handler
	 * @attribute[RequestParam('lang','string')]
     * @attribute[RequestParam('term','string')]
     * @attribute[RequestParam('text','string','')]
     */
	function SaveString($lang,$term,$text)
	{
		$text = urldecode($text);
		if( $text )
			$this->ds->ExecuteSql("REPLACE INTO wdf_translations(lang,id,content)VALUES(?,?,?)",array($lang,$term,$text));
		else
			$this->ds->ExecuteSql("DELETE FROM wdf_translations WHERE lang=? AND id=?",array($lang,$term));
        cache_del('lang_'.$term);
		return AjaxResponse::None();
	}
	
	/**
	 * @attribute[RequestParam('lang','string',false)]
	 */
	function Import($lang)
	{
		global $CONFIG;
		$lang = $lang?$lang:$CONFIG['localization']['default_language'];
		
		if( isset($_FILES['json_file']) )
		{
			$json_string = file_get_contents($_FILES['json_file']['tmp_name']);
			log_debug("Import($lang,)",strlen($json_string));
			unlink($_FILES['json_file']['tmp_name']);
			$count = 0;
			foreach( json_decode($json_string,true) as $entry )
			{
				$entry = array_values($entry);
				if( count($entry) < 2 || !$entry[1] )
					continue;
				$this->ds->ExecuteSql("REPLACE INTO wdf_translations(lang,id,content)VALUES(?,?,?)",array($lang,$entry[0],$entry[1]));
				$count++;
			}
			$this->content("<h2>$count terms imported</h2>");
		}
		
		$form = $this->content( new Form() );
		$form->content($this->_languageSelect($lang));
		$form->AddFile('json_file');
		$form->AddSubmit('Import');		
	}
}