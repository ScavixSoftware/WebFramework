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
use ScavixWDF\WdfException;

/**
 * Helper class to deal with datetime values.
 * 
 */
class DateTimeFormat
{
	const DF_FULL		= 0x00;
	const DF_YEARMONTH	= 0x01;
	const DF_MONTHDAY	= 0x02;
	const DF_LONGDATE	= 0x03;
	const DF_LONGTIME	= 0x04;
	const DF_SHORTDATE	= 0x05;
	const DF_SHORTTIME	= 0x06;

	private static $PatternPlaceholders = array(
			"d4","d3","d2","d1",
			"h2","h1","H2","H1",
			"m2","m1",
			"M4","M3","M2","M1",
			"s2","s1",
			"y4","y3","y2","y1",
			"t2","t1"
		);

	var $MonthNames;
	var $ShortMonthNames;
	var $GenitiveMonthNames;
	var $DayNames;
	var $ShortDayNames;
	var $FirstDayOfWeek;
	var $FullDateTimePattern;
	var $LongDatePattern;
	var $LongTimePattern;
	var $MonthDayPattern;
	var $ShortDatePattern;
	var $ShortTimePattern;
	var $YearMonthPattern;
	var $AM;
	var $PM;

	function  __construct($fdow="",$fdtp="",$ldp="",$ltp="",$mdp="",$sdp="",$stp="",$ymp="",$am="",$pm="")
	{
		$this->FirstDayOfWeek = $fdow;
		$this->FullDateTimePattern = $fdtp;
		$this->LongDatePattern = $ldp;
		$this->LongTimePattern = $ltp;
		$this->MonthDayPattern = $mdp;
		$this->ShortDatePattern = $sdp;
		$this->ShortTimePattern = $stp;
		$this->YearMonthPattern = $ymp;
		$this->AM = $am;
		$this->PM = $pm;
	}

	/**
	 * Formats a detetime value to a string.
	 * 
	 * @param int $date Time value (see <time>)
	 * @param int $format_id Format identifier (one of the DateTimeFormta::DT_* constants)
	 * @return string Formatted string
	 */
	function Format($date, $format_id)
	{
		if( is_array($format_id) )
		{
			$res = array();
			foreach( $format_id as $fi )
				$res[] = $this->Format($date, $fi);
			return implode("",$res);
		}

		// convert 'int as string' to int
		if( !preg_match('/[^0-9]+/',$format_id) )
			$format_id = $format_id + 0;

		// choose format string
		if( is_string($format_id) )
			$format = $format_id;
		else
		{
			switch( $format_id )
			{
				case self::DF_YEARMONTH:	$format = $this->YearMonthPattern; break;
				case self::DF_MONTHDAY:		$format = $this->MonthDayPattern; break;
				case self::DF_LONGDATE:		$format = $this->LongDatePattern; break;
				case self::DF_LONGTIME:		$format = $this->LongTimePattern; break;
				case self::DF_SHORTDATE:	$format = $this->ShortDatePattern; break;
				case self::DF_SHORTTIME:	$format = $this->ShortTimePattern; break;
				default:					$format = $this->FullDateTimePattern; break;
			}
		}

		// array of patterns to be replaced
		$pattern = self::$PatternPlaceholders;

		// throw away the %
//		$format = str_replace("%", "", $format);
		// find all placeholders
		$arplaceholders = array();
		$pl = sizeof($pattern);
		for($j = 0; $j < $pl; $j++)
		{
			$p = $pattern[$j];
			if(strpos($format, $p) !== false)
				$arplaceholders[] = $p;
		}

//		$slf = strlen($format);
//		$pl = sizeof($pattern);
//		for( $i=0; $i<$slf; $i++ )
//		{
////			foreach( $pattern as $k=>$p )
//			for($j = 0; $j < $pl; $j++)
//			{
//				$p = $pattern[$j];
//				$slp = strlen($p);
//				$test = substr($format,$i,$slp);
//				if( $test == $p )
//				{
//					$arplaceholders[$p] = "";
//					$i += $slp;
//					break;
//				}
//			}
//		}

		$i = 0;
		foreach($arplaceholders as $k=>$p)
		{
			$repl = "";
			switch($p)
			{
				case 'd4':
					$repl = $this->DayNames[date('w',$date)];
					break;
				case 'd3':
					$repl = $this->ShortDayNames[date('w',$date)];
					break;
				case 'd2':
					$repl = date('d',$date);
					break;
				case 'd1':
					$repl = date('j',$date);
					break;

//				case 'fffffff':
//				case 'ffffff':
//				case 'fffff':
//				case 'ffff':
//				case 'fff':
//				case 'ff':
//				case 'f':
//					$repl = substr(date('u',$date),0,strlen($k));
//					break;

				case 'h2':
					$repl = date('h',$date);
					break;
				case 'h1':
					$repl = date('g',$date);
					break;
				case 'H2':
					$repl = date('H',$date);
					break;
				case 'H1':
					$repl = date('G',$date);
					break;

				case 'm2':
				case 'm1':
					$repl = date('i',$date);
					break;

				case 'M4':
					$repl = $this->MonthNames[date('n',$date) - 1];
					break;
				case 'M3':
					$repl = $this->ShortMonthNames[date('n',$date) - 1];
					break;
				case 'M2':
					$repl = date('m',$date);
					break;
				case 'M1':
					$repl = date('n',$date);
					break;

				case 's2':
				case 's1':
					$repl = date('s',$date);
					break;

				case 'y4':
				case 'y3':
					$repl = date('Y',$date);
					break;
				case 'y2':
				case 'y1':
					$repl = date('y',$date);
					break;

				case 't2':
					$repl = (date('A',$date)=='AM'?$this->AM:$this->PM);
					break;
				case 't1':
					$repl = substr((date('A',$date)=='AM'?$this->AM:$this->PM),0,1);
					break;
			}

			if($repl != "")
				$format = str_replace($p, $repl, $format);
		}

//		$format = str_replace($arreplace, array_values($arplaceholders), $format);
		return $format;
	}

	/**
	 * Returns all known DateTime patterns.
	 * 
	 * These are the basic ones referred to by the DF_* constants plus the following
	 * combinations (separated by space):
	 * - DF_SHORTDATE DF_SHORTTIME
	 * - DF_LONGDATE DF_SHORTTIME
	 * - DF_SHORTDATE DF_LONGTIME
	 * - DF_LONGDATE DF_LONGTIME
	 * @return array Array of format strings
	 */
	public function KnownDateTimePatterns()
	{
		return array
		(
			$this->YearMonthPattern,$this->MonthDayPattern,$this->LongDatePattern,$this->LongTimePattern,
			$this->ShortDatePattern,$this->ShortTimePattern,$this->FullDateTimePattern,
			$this->ShortDatePattern." ".$this->ShortTimePattern,
			$this->LongDatePattern." ".$this->ShortTimePattern,
			$this->ShortDatePattern." ".$this->LongTimePattern,
			$this->LongDatePattern." ".$this->LongTimePattern,
		);
	}

	private function _regexEscapeArray($array)
	{
		$res = array();
		foreach( $array as $i )
			$res[] = preg_quote($i, '/');
		return implode("|",$res);
	}

	/**
	 * Converts a string to a unix timestamp.
	 * 
	 * Tries all KnownDateTimePatterns() and uses the best match.
	 * Known Bugs:
	 * - Culture cs-CZ Format dd MMMM
	 * - Culture mt-MT Format dddd, d' ta\' 'MMMM yyyy
	 * - Culture mt-MT Format dddd, d' ta\' 'MMMM yyyy HH:mm:ss
	 * - Culture vi-VN Format dd MMMM
	 *
	 * @param string $str Input string in one of the KnownDateTimePatterns()
	 * @return int The timestamp or FALSE on error
	 */
	public function StringToTime($str)
	{
		$apm1 = substr($this->AM,0,1).'|'.substr($this->PM,0,1);
		$regex_data = array
		(
			"d4" => '('.$this->_regexEscapeArray($this->DayNames).')',
			"d3" => '('.$this->_regexEscapeArray($this->ShortDayNames).')',
			"d2" => '(\d\d)',
			"d1" => '(\d|\d\d)',
			"h2" => '(\d|\d\d)',
			"h1" => '(\d|\d\d)',
			"H2" => '(\d\d)',
			"H1" => '(\d|\d\d)',
			"m2" => '(\d\d)',
			"m1" => '(\d\d)',
			"M4" => '('.$this->_regexEscapeArray($this->MonthNames).')',
			"M3" => '('.$this->_regexEscapeArray($this->ShortMonthNames).')',
			"M2" => '(\d\d)',
			"M1" => '(\d|\d\d)',
			"s2" => '(\d\d)',
			"s1" => '(\d\d)',
			"y4" => '(\d{4})',
			"y3" => '(\d{4})',
			"y2" => '(\d\d)',
			"y1" => '(\d\d)',
			"t2" => '('.$this->AM.'|'.$this->PM.')',
			"t1" => '('.$apm1.')',
		);
		$replacements = array_values($regex_data);
		$rep_keys = array_keys($regex_data);

		$pattern = self::$PatternPlaceholders;
		$formats = $this->KnownDateTimePatterns();

		$found = array();
		$semantics = array();
		foreach( $formats as $format )
		{
			$tmp_s = array();
			for( $iter=0; $iter<strlen($format); $iter++ )
			{
				foreach( $pattern as $k=>$p )
					if( substr($format,$iter,strlen($p)) == $p )
					{
						$tmp_s[] = $rep_keys[$k];
						$format = substr_replace($format,$replacements[$k],$iter,strlen($p));
						$iter += strlen($replacements[$k]);
						break;
					}
			}
			$format = str_replace('.','\.',$format);
			$format = str_replace('/','\/',$format);
			$format = str_replace(' ','\s',$format);
			$format = '/'.$format.'/iU';
			try
			{
				if( preg_match($format, $str, $match) )
					if( count($found) < count($match) )
					{
						$found = $match;
						$semantics = $tmp_s;
					}
			}catch(Exception $ex)
			{
				WdfException::Log("Invalid RegEx: $format",$ex);
			}
		}
		if( count($found) < 1 )
			return false;
		
		$d = 1; // because 0 would mktime return the last day of the previous month, so patterns like MMMM, YYYY would return prev month
		$m = 0;
		$y = 0;
		$h = 0;
		$i = 0;
		$s = 0;
		$apm = $this->AM;
		$fcnt = count($found);
		for($iter=1; $iter<$fcnt;$iter++)
		{
			switch( $semantics[$iter-1] )
			{
				case "d4":
				case "d3":
					break;
				
				case "M4":
					for($mn=0; $mn<count($this->MonthNames); $mn++)
						if( $this->MonthNames[$mn] == $found[$iter] )
						{
							$m = $mn+1;
							break;
						}
					break;
				case "M3":
					$m = array_search($found[$iter],$this->ShortMonthNames);
					$m++;
					break;

				case "d2":
				case "d1":
					$d = $found[$iter];
					break;

				case "h1":
				case "h1":
					$h2 = $found[$iter];
					break;
				case "H2":
				case "H1":
					$h = $found[$iter];
					break;

				case "m2":
				case "m1":
					$i = $found[$iter];
					break;

				case "M2":
				case "M1":
					$m = $found[$iter];
					break;

				case "s2":
				case "s1":
					$s = $found[$iter];
					break;

				case "y4":
				case "y3":
					$y = $found[$iter];
					break;
				case "y2":
				case "y1":
					$y2 = $found[$iter];
					break;

				case "t2":
				case "t1":
					$apm = $found[$iter];
					break;

				default:
					log_debug("Unknown semantic for $iter -> ".$semantics[$iter-1]);
					break;
			}
		}
		if( $h == 0 && isset($h2) )
		{
			$add = ($apm==$this->AM)?0:($apm==substr($this->AM,0,1)?0:12);
			$h = intval($h2) + intval($add);
		}
		if( $y == 0 && isset($y2) )
			$y = intval(substr(date("Y"),0,2).$y2);

		$res = mktime($h, $i, $s, $m, $d, $y);
//		log_debug("$h, $i, $s, $m, $d, $y");
//		log_debug("$str -> ".$res." -> ".date("Y-m-d H:i:s",$res));
		return $res;
	}
}
