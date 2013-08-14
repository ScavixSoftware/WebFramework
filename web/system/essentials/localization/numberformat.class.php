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
 * Helper class to deal with numbers.
 * 
 */
class NumberFormat
{
	var $DecimalDigits;
	var $DecimalSeparator;
	var $GroupSeparator;
	var $NegativeFormat;

	function  __construct($digits="",$decsep="",$groupsep="",$neg="")
	{
		$this->DecimalDigits = $digits;
		$this->DecimalSeparator = $decsep;
		$this->GroupSeparator = $groupsep;
		$this->NegativeFormat = $neg;
	}

	/**
	 * Formats a number to string.
	 * 
	 * @param float $number The value
	 * @param int $decimals Number of decimals, defaults to this objects DecimalDigits property
	 * @param bool $use_plain If true skips formatting
	 * @return string The formatted string
	 */
	function Format($number, $decimals = false, $use_plain = false)
	{
		$number = doubleval($number);
		$val = number_format($number,$decimals!==false?$decimals:$this->DecimalDigits,$this->DecimalSeparator,$this->GroupSeparator);
		if( strlen($this->GroupSeparator) > 0 && !$use_plain )
		{
			//log_debug("plain");
			$ord = uniord($this->GroupSeparator);
			$val = str_replace($this->GroupSeparator[0],"&#$ord;",$val);
		}
		//log_debug($val);
		if( $number >= 0 )
			return $val;
		return str_replace("%v", $val, $this->NegativeFormat);
	}

	/**
	 * Converts a number matching this objects formatting back to a float.
	 * 
	 * @param string $str String containing the formattedc number
	 * @return float The number or false on error
	 */
	function StrToNumber($str)
	{
		$number =  str_replace($this->GroupSeparator,"",$str);
		if( $this->DecimalSeparator != '.' )
			$number =  str_replace($this->DecimalSeparator,".",$number);

		if( !is_float(floatval($number)) || !is_numeric($number) )
			return false;

		return floatval($number);
	}
}
