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
 * Helper class for currency formatting.
 * 
 */
class CurrencyFormat
{
	var $DecimalDigits;
	var $DecimalSeparator;
	var $GroupSeparator;
	var $Symbol;
	var $Code;
	var $PositiveFormat;
	var $NegativeFormat;
	var $EnglishName;
	var $NativeName;

	function  __construct($digits="",$decsep="",$groupsep="",$symbol="",$isosymbol="",$pos="",$neg="",$english="",$native="")
	{
		$this->DecimalDigits = $digits;
		$this->DecimalSeparator = $decsep;
		$this->GroupSeparator = $groupsep;
		$this->Symbol = $symbol;
		$this->Code = $isosymbol;
		$this->PositiveFormat = $pos;
		$this->NegativeFormat = $neg;
		$this->EnglishName = $english;
		$this->NativeName = $native;
	}

	/**
	 * Formats a currency value to string.
	 * 
	 * @param float $amount Value to format
	 * @param bool $use_plain If true will use curreny code instead of symbol
	 * @param bool $only_value If true totally skips currency code or symbol
	 * @return string Formatted currency string
	 */
	function Format($amount, $use_plain=false, $only_value=false)
	{
		$val = number_format(abs($amount),$this->DecimalDigits,$this->DecimalSeparator,$this->GroupSeparator);
		if( strlen($this->GroupSeparator) > 0 && !$use_plain )
		{
			$ord = uniord($this->GroupSeparator);
			$val = str_replace($this->GroupSeparator[0],"&#$ord;",$val);
		}

		if( $only_value )
			return $val;

		$tmp = ($amount >= 0)?$this->PositiveFormat:$this->NegativeFormat;

		if( $use_plain )
			$tmp = str_replace($this->Symbol,$this->Code,$tmp);

		return unicode_cleanup_rtl(str_replace("%v", $val, $tmp));
	}

	/**
	 * Converts a string formatted as supposed in this currency back to float.
	 * 
	 * @param string $str String containing currency
	 * @return float The value or false on error
	 */
	function StrToCurrencyValue($str)
	{
//		log_debug("StrToCurrencyValue($str)");
//		log_debug($this);
		$number =  str_replace($this->GroupSeparator,"",$str);
		$number =  str_replace($this->Code,"",$number);
		$number =  str_replace($this->Symbol,"",$number);
//		log_debug($number);
		if( $this->DecimalSeparator != '.' )
			$number =  str_replace($this->DecimalSeparator,".",$number);

		if( !is_numeric($number) )
			return false;
//		log_debug(floatval($number));
		return floatval($number);
	}
}
