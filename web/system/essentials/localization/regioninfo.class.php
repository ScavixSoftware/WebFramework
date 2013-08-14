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
namespace ScavixWDF\Localization;

/**
 * This represents a region in a culture.
 * 
 * Let's say this is the 'US' in 'en-US'.
 */
class RegionInfo
{
	var $Code;
	var $EnglishName;
	var $NativeName;
	var $KnownCultures;

	function  __construct($code="",$english="",$native="",$cultures="")
	{
		$this->Code = $code;
		$this->EnglishName = $english;
		$this->NativeName = $native;
		$this->KnownCultures = $cultures;
	}

	/**
	 * Gets the default culture.
	 * 
	 * @return CultureInfo The default culture or false on error
	 */
	function DefaultCulture()
	{
		foreach( $this->KnownCultures as $kc )
		{
			$ci = internal_getCultureInfo($kc);
			if( $ci ) return $ci;
		}
		return false;
	}

	/**
	 * Ensures the given $culture to a culture valid for this region.
	 * 
	 * In other words: Checks all cultures that are valid for this region and returns the match.
	 * @param mixed $culture <CultureInfo> or culture code
	 * @return CultureInfo The found culture or false
	 */
	function GetCulture($culture)
	{
		if( $culture instanceof CultureInfo )
			$culture = $culture->Code;

		foreach( $this->KnownCultures as $kc )
		{
			$ci = internal_getCultureInfo($kc);
			if( strtolower($ci->Code) == strtolower($culture) )
				return $ci;

			if( $ci->IsParentOf($culture) || $ci->IsChildOf($culture) )
				return $ci;
		}
		return false;
	}

	/**
	 * Checks if this region contains a culture.
	 * 
	 * Useful to check if a language is spoken in a region.
	 * @param mixed $culture <CultureInfo> or culture code
	 * @return bool true or false
	 */
	function ContainsCulture($culture)
	{
		if( $culture instanceof CultureInfo )
			$culture = $culture->Code;

		foreach( $this->KnownCultures as $kc )
		{
			if( strtolower($kc) == strtolower($culture) )
				return true;

			$ci = internal_getCultureInfo($kc);
			if( $ci->IsParentOf($culture) || $ci->IsChildOf($culture) )
				return true;
		}
		return false;
	}
}
