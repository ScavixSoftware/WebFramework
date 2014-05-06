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
namespace ScavixWDF\Controls\Locale;

use ScavixWDF\Controls\Form\Select;
use ScavixWDF\Localization\CultureInfo;
use ScavixWDF\Localization\Localization;

/**
 * Language selector.
 * 
 * @attribute[Resource('locale_settings.js')]
 */
class LanguageSelect extends Select
{
	/**
	 * @param mixed $current_language_code Currently selected language
	 */
	function __initialize($current_language_code=false)
	{
		parent::__initialize();
		$this->script("Locale_Settings_Init();");
		$this->setData('role', 'language');
		
		if( $current_language_code )
		{
			if( $current_language_code instanceof CultureInfo )
				$lang = $current_language_code->ResolveToLanguage();
			else
				$lang = Localization::getLanguageCulture($current_language_code);
			if( !$lang )
				$lang = Localization::detectCulture()->ResolveToLanguage();
			$this->SetCurrentValue($lang->Code);
		}
		foreach(getAvailableLanguages() as $code)
		{
			$lang = Localization::getLanguageCulture($code);
			$this->AddOption($code, "{$lang->NativeName} ({$lang->EnglishName})");
		}
	}
}

