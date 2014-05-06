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
namespace ScavixWDF\Translation;

use stdClass;
use ScavixWDF\Base\AjaxAction;
use ScavixWDF\Base\AjaxResponse;
use ScavixWDF\Base\Control;
use ScavixWDF\Controls\Anchor;
use ScavixWDF\Controls\Form\Button;
use ScavixWDF\Controls\Form\CheckBox;
use ScavixWDF\Controls\Form\Form;
use ScavixWDF\Controls\Form\Select;
use ScavixWDF\Controls\Form\TextArea;
use ScavixWDF\Controls\Form\TextInput;
use ScavixWDF\Controls\Table\Table;
use ScavixWDF\JQueryUI\Dialog\uiDialog;
use ScavixWDF\Localization\Localization;
use ScavixWDF\WdfException;

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
	
	private function fetchTerms($lang_code,$defaults = array())
    {
        $rs = $this->ds->ExecuteSql("SELECT id,content FROM wdf_translations WHERE lang=?",$lang_code);
        $res = $defaults;
        foreach( $rs as $lang )
            $res[$lang['id']] = isset($lang['content'])&&$lang['content']?$lang['content']:'';
		return $res;
    }
	
	private function _languageSelect($lang)
	{
		$sel = new Select();
		$sel->SetCurrentValue($lang);
		$known = $sel->CreateGroup('Languages with translations');
		$avail = $sel->CreateGroup('Available languages');
		
		$counts = array();
		foreach( $this->ds->ExecuteSql("SELECT lang,count(*) as cnt FROM wdf_translations GROUP BY lang") as $row )
			$counts[$row['lang']] = intval($row['cnt']);
		foreach( Localization::get_language_names() as $code=>$name )
		{
			if( isset($counts[$code]) )
				$name = "$name ({$counts[$code]})";
			$sel->AddOption($code,$name,false,(isset($counts[$code])&&$counts[$code]>0)?$known:$avail);
		}
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
                "<?php\n$info;\n$strings;\n"
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
	
	private function _searchQuery($lang,$search=false)
	{
		$rs = $this->ds->Query('wdf_translations')->eq('lang',$lang);
		if( !$search )
			return $rs;
		$s = str_replace(array('_','%'), array('\_','\%'), $this->ds->EscapeArgument($search));
		$s = str_replace(array('?','*'),array('_','%'),$s);
		$s = "%$s%";
		return $rs->orX(2)->like('id',$s)->like('content',$s);
	}
	
	/**
	 * @internal Entry point for translation admin.
	 * @attribute[RequestParam('lang','string',false)]
	 * @attribute[RequestParam('offset','int',0)]
	 * @attribute[RequestParam('search','text','')]
	 */
	function Translate($lang,$offset,$search)
	{
		global $CONFIG;
		$lang = $lang?$lang:$CONFIG['localization']['default_language'];
		$_SESSION['trans_admin_lang'] = $lang;
		$_SESSION['trans_admin_offset'] = $offset;
		$_SESSION['trans_admin_search'] = $search;
		
		$form = $this->content( new Form() );
		$form->css('margin-bottom','20px')->action = buildQuery('TranslationAdmin','Translate');				
		$form->content("Select language: ");
		$form->content( $this->_languageSelect($lang) )
			->script("$('#{self}').change(function(){ $('#{$form->id}').submit(); });")
			->name = 'lang';
		$form->content("&nbsp;&nbsp;&nbsp;And/Or search: ");
		$form->AddText('search',$search);
		$form->AddHidden('offset',0);
		$form->AddSubmit('Search');
		$form->content("<span style='color:gray'>(? matches single char, * machtes any/no char)</span>");

		$tab = Table::Make()->addClass('translations')
			->SetHeader('Term','Default','Content','')
			->setData('lang',$lang)
			->appendTo($this);
		
		if( $lang != $CONFIG['localization']['default_language'] )
		{
			$translated = array();
			$rs = $this->_searchQuery($lang);
			foreach( $rs as $row )
				$translated[$row['id']] = $row['content'];
		}
		$rs = $this->_searchQuery($CONFIG['localization']['default_language'],$search)->page($offset,10);
		foreach( $rs as $term )
		{
			if( isset($translated) )
				$translation = isset($translated[$term->id])?$translated[$term->id]:'';
			else
				$translation = $term->content;
			
			$ta = new TextArea($translation);
			$ta->class = $term->id;
			$btn = new Button('Save');
			$btn->addClass('save')->setData('term',$term->id);
			
			$tab->AddNewRow($term->id,htmlspecialchars($term->content),$ta,$btn);
			
			$tab->GetCurrentRow()->GetCell(0)->content(Control::Make("span"))
				->addClass('term_action rename')->setData('term', $term->id)->content('rename');
			$tab->GetCurrentRow()->GetCell(0)->content("&nbsp;");
			$tab->GetCurrentRow()->GetCell(0)->content(Control::Make("span"))
				->addClass('term_action remove')->setData('term', $term->id)->content('remove');
		}
		
		$pi = $rs->GetPagingInfo();
		for($page=1;$page<=$pi['total_pages'];$page++)
		{
			$offset = ($page-1) * $pi['rows_per_page'];
			$label = ($offset+1)."-".($page*$pi['rows_per_page']);
			$label = "$page";
			if( $page == $pi['current_page'] )
				$this->content("<b>$label</b>");
			else
				$this->content(new Anchor(buildQuery('TranslationAdmin','Translate',"lang=$lang&offset=$offset&search=$search"),"$label"));
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
	 * Imports a file with translations into the translation system.
	 * 
	 * File must contain vaid JSON data with key-value pairs of strings in an array.
	 * Sample:
	 * <code json>
	 * [
	 *   {"term":"TXT_TERM1","content":"My contents of terms 1"},
	 *   {"or":"TXT_TERM2","can_be":"My contents of terms 2"},
	 *   {"whatever":"TXT_TERM3","wtf_too":"My contents of terms 3"}
	 * ]
	 * </code>
	 * Notes:
	 * - The key names will be ignored, only the order counts
	 * - The contents must be in the language defined by the `$lang` parameter
	 * - The uploaded file must be given as 'json_file' (`$_FILES['json_file']`)
	 * @param string $lang The language the contents are given in
	 * @return void
	 * @internal
	 * @attribute[RequestParam('lang','string',false)]
	 */
	function Import($lang)
	{
		global $CONFIG;
		$lang = $lang?$lang:$CONFIG['localization']['default_language'];
		$is_default = $lang == $CONFIG['localization']['default_language'];
		
		if( isset($_FILES['json_file']) )
		{
			$json_string = file_get_contents($_FILES['json_file']['tmp_name']);
			unlink($_FILES['json_file']['tmp_name']);
			$count = 0; $unknowns = "";
			
			if( !$is_default ) 
				$knowns = $this->ds->ExecuteSql("SELECT DISTINCT id FROM wdf_translations WHERE lang=?",$CONFIG['localization']['default_language'])
					->Enumerate('id',false);
			
			foreach( json_decode($json_string,true) as $entry )
			{
				$entry = array_values($entry);
				if( count($entry) < 2 || !$entry[1] )
					continue;
				$this->ds->ExecuteSql("REPLACE INTO wdf_translations(lang,id,content)VALUES(?,?,?)",array($lang,$entry[0],$entry[1]));
				$count++;
				
				if( !$is_default && !in_array($entry[0],$knowns) )
				{
					$unknowns[] = $entry[0];
					default_string($entry[0],"Imported from $lang: ".$entry[1]);
				}
			}
			if( $is_default )
				$this->ds->ExecuteSql("DELETE FROM wdf_unknown_strings WHERE term IN(SELECT id FROM wdf_translations WHERE lang=?)",$lang);
			else
			{
				log_debug($unknowns);
				translation_add_unknown_strings($unknowns);
			}
			
			$this->content("<h2>$count terms imported</h2>");
		}
		
		$form = $this->content( new Form() );
		$form->content($this->_languageSelect($lang))->name = 'lang';
		$form->AddFile('json_file');
		$form->AddSubmit('Import');		
	}
	
	/**
	 * @internal Renames a term.
	 * 
	 * Sometimes you may want to correct a terms name, so use this one.
	 * @param string $term The original term
	 * @param string $new_term The new term name
	 * @return void
	 * @attribute[RequestParam('term','string')]
	 * @attribute[RequestParam('new_term','string',false)]
	 */
	function Rename($term,$new_term)
	{
		if( !$new_term )
		{
			$dlg = new uiDialog('Rename term');
			$dlg->content("Enter new term: ");
			$ti = $dlg->content(new TextInput($term));
			$dlg->AddButton('Rename', "function(){ wdf.controller.post('Rename',{term:'$term',new_term:$('#{$ti->id}').val()}); }");
			$dlg->AddCloseButton('Cancel');
			return $dlg;
		}
		$this->ds->ExecuteSql("UPDATE wdf_translations SET id=? WHERE id=?",array($new_term,$term));
		return AjaxResponse::Redirect('TranslationAdmin','Translate', array(
			'lang' => $_SESSION['trans_admin_lang'],
			'offset' => $_SESSION['trans_admin_offset'],
			'search' => $_SESSION['trans_admin_search'],
		));
	}
	
	/**
	 * @internal Removes a term.
	 * 
	 * Removes a term from all translations.
	 * @param string $term The term to remove
	 * @return void
	 * @attribute[RequestParam('term','string')]
	 */
	function Remove($term)
	{
		default_string("TITLE_REMOVE_TERM","Remove term");
		default_string("TXT_REMOVE_TERM","Do you really want to remove this term? This cannot be undone!");
		
		if( !AjaxAction::IsConfirmed("REMOVE_TERM") )
			return AjaxAction::Confirm("REMOVE_TERM", 'TranslationAdmin', 'Remove', array('term'=>$term));
		
		$this->ds->ExecuteSql("DELETE FROM wdf_translations WHERE id=?",$term);
		return AjaxResponse::Redirect('TranslationAdmin','Translate', array(
			'lang' => $_SESSION['trans_admin_lang'],
			'offset' => $_SESSION['trans_admin_offset'],
			'search' => $_SESSION['trans_admin_search'],
		));
	}
}