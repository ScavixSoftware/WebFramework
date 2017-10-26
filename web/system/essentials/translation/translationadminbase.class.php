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

use ScavixWDF\Admin\SysAdmin;
use ScavixWDF\Base\AjaxResponse;
use ScavixWDF\Base\Template;

/**
 * Base class for translation handlers.
 * 
 * @attribute[NoMinify]
 */
abstract class TranslationAdminBase extends SysAdmin
{
	function __initialize($title = "", $body_class = false)
	{
		parent::__initialize($title, $body_class);
		$this->subnav('New strings', get_class_simple($this), 'NewStrings');
		$this->subnav('Fetch strings', get_class_simple($this), 'Fetch');
	}
	
    /**
	 * @internal New string page
     */
    function NewStrings()
    {
        $this->_contentdiv->content("<h1>New strings</h1>");
		$this->_contentdiv->content("<p>Default language is '{$GLOBALS['CONFIG']['localization']['default_language']}', so please create new strings accordingly.</p>");
        $ds = model_datasource($GLOBALS['CONFIG']['translation']['sync']['datasource']);
		translation_add_unknown_strings(array());
		foreach( $ds->Query('wdf_unknown_strings')->all() as $row )
        {
			$ns = Template::Make('translationnewstring');
			foreach( $row->GetColumnNames() as $col )
				$ns->set($col,$row->$col);
            $this->_contentdiv->content($ns);
        }
		if( !isset($row) )
			$this->_contentdiv->content("<p>no requested strings found</p>");
		$this->_contentdiv->content("<h1 style='clear:both'>Manually add string</h1>");
		Template::Make('translationnewstringmanually')->appendTo($this->_contentdiv);
    }
    
    /**
	 * @internal Delete a string
     * @attribute[RequestParam('term','string')]
     */
    function DeleteString($term)
    {
        $ds = model_datasource($GLOBALS['CONFIG']['translation']['sync']['datasource']);
        $ds->ExecuteSql("DELETE FROM wdf_unknown_strings WHERE term=?",$term);
        return AjaxResponse::None();
    }
	
	/**
	 * Fetch strings from the translation system into the project.
	 * 
	 * They will be stored in the strings directory as PHP files for easy inclusion.
	 * @param array $languages Array of language codes to be fetched
	 * @return void
	 */
	abstract function Fetch($languages = false);
    
    /**
	 * Import strings from the XX.inc.php translation system into the database
	 * 
	 * @param array $languages Array of language codes to be fetched
	 * @return void
	 */
	abstract function Import($languages = false, $clearbeforeimport = false);
	
	/**
	 * Creates a new string from unknowns table.
	 * 
	 * This transforms an unknown string (found in sourcecode) into a full translation term that can be 
	 * edited in the translation system.
	 * @param string $term The identifier (like TXT_MYSTRING1)
	 * @param string $text Content, in the default application language
	 * @return void
	 */
	abstract function CreateString($term,$text);
}