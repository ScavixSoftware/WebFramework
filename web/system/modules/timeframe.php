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

use ScavixWDF\WdfException;
 
/**
 * Initializes the timeframe module.
 * 
 * @return void
 */
function timeframe_init()
{
	global $CONFIG;

	if( !isset($CONFIG['timeframe']['datefield']) )
		$CONFIG['timeframe']['datefield'] = 'date';

	if( !isset($CONFIG['timeframe']['get_timeframe_func']) )
		$CONFIG['timeframe']['get_timeframe_func'] = 'getUserSetting';

	if( !isset($CONFIG['timeframe']['set_timeframe_func']) )
		$CONFIG['timeframe']['set_timeframe_func'] = 'setUserSetting';

	$GLOBALS['timeframe']['datefield'] = $CONFIG['timeframe']['datefield'];
	$GLOBALS['timeframe']['data_object'] = false;
}

/**
 * @deprecated Should become useless once <TimeFrame> is rewritten
 */
interface ITimeframeDataobject
{
	function GetTimeframe();
	function SetTimeframe($frame);
}

/**
 * @deprecated This is really oldschool calculating. Better reimplement using the <DateTimeEx> features. Also need to get rid of the <ITimeframeDataobject> binding to the UI.
 */
class TimeFrame
{
	private static function _getTimeFrameSetting()
	{
		global $CONFIG;
		if( $GLOBALS['timeframe']['data_object'] )
			return $GLOBALS['timeframe']['data_object']->GetTimeframe();

		if( $CONFIG['timeframe']['get_timeframe_func'] == 'getUserSetting' )
			return getUserSetting("timeframe", "today");
		else
			return $CONFIG['timeframe']['get_timeframe_func']();
	}

	private static function _setTimeFrameSetting($frame)
	{
		global $CONFIG;
		if( $GLOBALS['timeframe']['data_object'] )
			return $GLOBALS['timeframe']['data_object']->SetTimeframe($frame);

		if( $CONFIG['timeframe']['set_timeframe_func'] == 'setUserSetting' )
			return setUserSetting("timeframe", $frame);
		else
			return $CONFIG['timeframe']['set_timeframe_func']($frame);
	}

	private static function _ensureDate($date)
	{
		if( !preg_match('/[^0-9]+/',$date) )
			$d = $date + 0;
		else
			$d = strtotime($date);
		return $d;
	}

	static function SetDataObject(&$obj)
	{
		if( $obj instanceof ITimeframeDataobject )
			$GLOBALS['timeframe']['data_object'] = $obj;
		else
			WdfException::Raise("Trying to set an invalid dataobject. ITimeframeDataobject needed!");
	}

	static function currentTimeFrame()
	{
		return self::_getTimeFrameSetting();
	}

	static function setTimeFrame($tf)
	{
		if( is_array($tf) )
		{
			if( count($tf) == 2 )
			{
				foreach( $tf as $i=>$t )
				{
					if( is_string($t) )
					{
						$tf[$i] = self::_ensureDate($t);
						if( $tf[$i] === false || $tf[$i] < 0 )
							return false;
					}
				}
				$s = date('Y-m-d',$tf[0]);
				$e = date('Y-m-d',$tf[1]);
				self::_setTimeFrameSetting("$s|$e");
			}
		}
		elseif(in_array($tf, array("today", "last24h", "yesterday", "daybeforeyesterday", 
			"curweek", "lastweek", "curmonth", "lastmonth", "curyear", "lastyear","curquarter","lastquarter",
			"last7days","last14days","last30days","last365days", "all")))
			self::_setTimeFrameSetting($tf);
	}

	static function ContainsToday()
	{
		$tf = TimeFrame::currentTimeFrame();
		if($tf == "last24h")
			return false;
		$d = TimeFrame::FirstDate();
		if( date("Ymd") == date("Ymd",$d) )
			return true;
		$d = TimeFrame::LastDate();
		if( date("Ymd") == date("Ymd",$d) )
			return true;
		return false;
	}

	static function FirstDate($format=false)
	{
		$tf = TimeFrame::currentTimeFrame();
		switch($tf)
		{
			case "last7days":
			case "last14days":
			case "last30days":
			case "last365days":
				$days = intval(str_replace('days', '', substr($tf,4)));
				$d = time() - ($days * 24 * 60 * 60);
				$s = mktime(0, 0, 0, date("m", $d), date("d", $d), date("Y", $d));
				break;
			case "today":
				$d = time();
				$s = mktime(0, 0, 0, date("m", $d), date("d", $d), date("Y", $d));
				break;

			case "last24h":
				$s = time() - (1 * 24 * 60 * 60);
				break;
			
			case "yesterday":
				$d = time() - (1 * 24 * 60 * 60);
				$s = mktime(0, 0, 0, date("m", $d), date("d", $d), date("Y", $d));
				break;

			case "daybeforeyesterday":
				$d = time() - (2 * 24 * 60 * 60);
				$s = mktime(0, 0, 0, date("m", $d), date("d", $d), date("Y", $d));
				break;

			case "curweek":
				$dow = date("N",time());
				$s = time()+(($dow - 1) * -86400);
				break;

			case "lastweek":
				$dow = date("N",time());
				$x = $dow - 1;
				$y = 7 - $dow;
				$s = time()+(($x+7) * -86400);
				break;

			case "curmonth":
				$s = mktime(0, 0, 0, date("m"), 1, date("Y"));
				break;

			case "lastmonth":
				$s = mktime(0, 0, 0, date("m")-1, 1, date("Y"));
				break;

			case "curquarter":
				$y = date("Y",time());
				$quart = ceil(date("m")/3);
				switch( $quart )
				{
					case 1:	$s = mktime(0, 0, 0,  1, 1, $y); break;
					case 2:	$s = mktime(0, 0, 0,  4, 1, $y); break;
					case 3:	$s = mktime(0, 0, 0,  7, 1, $y); break;
					case 4:	$s = mktime(0, 0, 0, 10, 1, $y); break;
				}
				break;

			case "lastquarter":
				$y = date("Y",time());
				$quart = ceil(date("m")/3) - 1;
				if( $quart < 1 )
				{
					$y--;
					$quart = 4;
				}
				switch( $quart )
				{
					case 1:	$s = mktime(0, 0, 0,  1, 1, $y); break;
					case 2:	$s = mktime(0, 0, 0,  4, 1, $y); break;
					case 3:	$s = mktime(0, 0, 0,  7, 1, $y); break;
					case 4:	$s = mktime(0, 0, 0, 10, 1, $y); break;
				}
				break;

			case "curyear":
				$s = mktime(0, 0, 0, 1, 1, date("Y"));
				break;

			case "lastyear":
				$s = mktime(0, 0, 0, 1, 1, date("Y")-1);
				break;

			case "all":
				return null;
				break;

			default:
				$tf = explode("|",$tf);
				if( count($tf) != 2 )
					$s = null;
				else
					$s = self::_ensureDate($tf[0]);
				break;
		}
		if( $format )
			return date($format,$s);
		return $s;
	}

	static function LastDate($format=false)
	{
		$e = null;
		$tf = TimeFrame::currentTimeFrame();
		switch($tf)
		{
			case "today":
				$d = time();
				$e = mktime(23, 59, 59, date("m", $d), date("d", $d), date("Y", $d));
				break;
			
			case "yesterday":
				$d = time() - (1 * 24 * 60 * 60);
				$e = mktime(23, 59, 59, date("m", $d), date("d", $d), date("Y", $d));
				break;

			case "curweek":
				$dow = date("N",time());
				$e = time()+((7 - $dow) * 86400);
				break;

			case "lastweek":
				$dow = date("N",time());
				$x = $dow - 1;
				$y = 7 - $dow;
				$e = time()+(($y-7) * 86400);
				break;

			case "curmonth":
				$e = mktime(23, 59, 59, date("m"), date("t"), date("Y"));
				break;

			case "lastmonth":
				$e = mktime(23, 59, 59, date("m")-1, date("t", mktime(0, 0, 0, date("m")-1, 1, date("Y"))), date("Y"));
				break;
			case "last24h":
			case "last7days":
			case "last14days":
			case "last30days":
			case "last365days":
				$e = time();
				break;
			case "curquarter":
				$y = date("Y",time());
				$quart = ceil(date("m")/3);
				switch( $quart )
				{
					case 1:	$e = mktime(23, 59, 59,  3, 31, $y); break;
					case 2:	$e = mktime(23, 59, 59,  6, 30, $y); break;
					case 3:	$e = mktime(23, 59, 59,  9, 30, $y); break;
					case 4:	$e = mktime(23, 59, 59, 12, 31, $y); break;
				}
				break;

			case "lastquarter":
				$y = date("Y",time());
				$quart = ceil(date("m")/3) - 1;
				if( $quart < 1 )
				{
					$y--;
					$quart = 4;
				}
				switch( $quart )
				{
					case 1:	$e = mktime(23, 59, 59,  3, 31, $y); break;
					case 2:	$e = mktime(23, 59, 59,  6, 30, $y); break;
					case 3:	$e = mktime(23, 59, 59,  9, 30, $y); break;
					case 4:	$e = mktime(23, 59, 59, 12, 31, $y); break;
				}
				break;

			case "curyear":
				//$e = mktime(0, 0, 0, 12, 31, date("Y"));
				$e = time();
				break;

			case "lastyear":
				$e = mktime(23, 59, 59, 12, 31, date("Y")-1);
				break;

			case "all":
				return null;
				break;

			default:
				$tf = explode("|",$tf);
				if( count($tf) != 2 )
					$e = null;
				else
					$e = self::_ensureDate($tf[1]);
				break;
		}
		if( $format )
			return date($format,$e);
		return $e;
	}

	static function SQLforTimeframe($datefield=false)
	{
		if( $datefield )
			TimeFrame::setDateField($datefield);

		$s = TimeFrame::FirstDate();
		$e = TimeFrame::LastDate();
//		log_debug("s: ".date("Y-m-d H:i:s", $s)." e: ".date("Y-m-d H:i:s", $e));
		
		return TimeFrame::_generateSQL($s,$e);
	}
	
	static function SQLforPreviousTimeframe($datefield=false)
	{
		if( $datefield )
			TimeFrame::setDateField($datefield);

		$s = TimeFrame::FirstDate();
		$e = TimeFrame::LastDate();
		$d = $e-$s;
		$s -= $d;
		$e -= $d;
		return TimeFrame::_generateSQL($s,$e);
	}

	static function PreparedStatementSQL($datefield,&$values)
	{
		if( $datefield )
			TimeFrame::setDateField($datefield);

		$s = TimeFrame::FirstDate();
		$e = TimeFrame::LastDate();
		$where = "";
		if(($s === null) && ($e === null))
			return 'true';
 		else
			if(($s === null) && ($e !== null))
			{
				$where .= $datefield." = ?";
				$values[] = date("Y-m-d", $e);
			}
			else
				if(($s !== null) && ($e === null))
				{
					$where .= $datefield." = ?";
					$values[] = date("Y-m-d", $s);
				}
				else
				{
					$where .= $datefield." >= ? AND ".$datefield." <= ? ";
					$values[] = date("Y-m-d", $s).' 00:00:00';
					$values[] = date("Y-m-d", $e)." 23:59:59";
				}
		return $where;
	}
	static function SQLforPrevTimeframe($datefield=false)
	{
		if( $datefield )
			TimeFrame::setDateField($datefield);
		$s = TimeFrame::PrevPeriodFirstDate();
		$e = TimeFrame::PrevPeriodLastDate();

		return TimeFrame::_generateSQL($s,$e);
	}

	private static function _generateSQL($s=null,$e=null)
	{
		$df = $GLOBALS['timeframe']['datefield'];

		if(($s === null) && ($e === null))
			$daterangesql = " 1";
		elseif(($s === null) && ($e !== null))
			$daterangesql = " $df='".date("Y-m-d", $e)."'";
		elseif(($s !== null) && ($e === null))
			$daterangesql = " $df='".date("Y-m-d", $s)."'";
		else
//			$daterangesql = " $df>='".date("Y-m-d", $s)." 00:00:00' AND $df<='".date("Y-m-d", $e)." 23:59:59'";
			$daterangesql = " $df>='".date("Y-m-d H:i:s", $s)."' AND $df<='".date("Y-m-d H:i:s", $e)."'";
		return $daterangesql;
	}

	private
	static function setDateField($datefield)
	{
		$GLOBALS['timeframe']['datefield'] = $datefield;
	}

	static function Hint()
	{
		$tf = TimeFrame::currentTimeFrame();
		switch($tf)
		{
			case "today":
			case "yesterday":
			case "last24h":
			case "curweek":
			case "lastweek":
			case "curmonth":
			case "lastmonth":
			case "curquarter":
			case "lastquarter":
			case "curyear":
			case "lastyear":
			case "last7days":
			case "last14days":
			case "last30days":
				return "[TXT_".strtoupper($tf)."]";
				
			default:
				$s = TimeFrame::FirstDate();
				$e = TimeFrame::LastDate();
				if($e === null || $e == $s || ($e - $s == 86400-1)  )
					$ret = getString("TXT_TIMEFRAME_ONEDAY", array("%s" => $GLOBALS['timeframe']['data_object']->FormatDate($s)));
				else
					$ret = getString("TXT_TIMEFRAME", array("%s" => $GLOBALS['timeframe']['data_object']->FormatDate($s), "%e" => $GLOBALS['timeframe']['data_object']->FormatDate($e)));
				return $ret;
				break;
		}
	}

	static function PrevPeriodFirstDate($format=false)
	{
		$tf = TimeFrame::currentTimeFrame();
		switch($tf)
		{
			case "today":
				$s = mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));
				break;

			case "last24h":
				$s = time() - (2 * 24 * 60 * 60);
				break;
			
			case "yesterday":
				$d = time() - (1 * 24 * 60 * 60);
				$s = mktime(0, 0, 0, date("m", $d), date("d", $d), date("Y", $d));
				break;

			case "daybeforeyesterday":
				$d = time() - (2 * 24 * 60 * 60);
				$s = mktime(0, 0, 0, date("m", $d), date("d", $d), date("Y", $d));
				break;

			case "curweek":
				$dow = date("N",time());
				$x = $dow - 1;
				$s = time()+(($x+7) * -86400);
				break;

			case "lastweek":
				$dow = date("N",time());
				$x = $dow - 2;
				$s = time()+(($x+7) * -86400);
				break;

			case "curmonth":
				$s = mktime(0, 0, 0, date("m")-1, 1, date("Y"));
				break;

			case "lastmonth":
				$s = mktime(0, 0, 0, date("m")-2, 1, date("Y"));
				break;

			case "curquarter":
				$y = date("Y");
				$quart = ceil(date("m")/3) - 1;
				if( $quart < 1 )
				{
					$y--;
					$quart = 4;
				}
				switch( $quart )
				{
					case 1:	$s = mktime(0, 0, 0,  1, 1, $y); break;
					case 2:	$s = mktime(0, 0, 0,  4, 1, $y); break;
					case 3:	$s = mktime(0, 0, 0,  7, 1, $y); break;
					case 4:	$s = mktime(0, 0, 0, 10, 1, $y); break;
				}
				break;

			case "lastquarter":
				$y = date("Y");
				$quart = ceil(date("m")/3) - 2;
				if( $quart < 0 )
				{
					$y--;
					$quart = 3;
				}
				elseif( $quart < 1 )
				{
					$y--;
					$quart = 4;
				}
				switch( $quart )
				{
					case 1:	$s = mktime(0, 0, 0,  1, 1, $y); break;
					case 2:	$s = mktime(0, 0, 0,  4, 1, $y); break;
					case 3:	$s = mktime(0, 0, 0,  7, 1, $y); break;
					case 4:	$s = mktime(0, 0, 0, 10, 1, $y); break;
				}
				break;

			case "curyear":
				$s = mktime(0, 0, 0, 1, 1, date("Y")-1);
				break;

			case "lastyear":
				$s = mktime(0, 0, 0, 1, 1, date("Y")-2);
				break;

			default:
				$tf = explode("|",$tf);
				if( count($tf) != 2 )
					$s = null;
				else
					$s = self::_ensureDate($tf[0]);
				break;
		}
		if( $format )
			return date($format,$s);
		return $s;
	}

	static function PrevPeriodLastDate($format=false)
	{
		$e = null;
		$tf = TimeFrame::currentTimeFrame();
		switch($tf)
		{
			case "today":
				$e = mktime(23, 59, 59, date("m"), date("d")-1, date("Y"));
				break;
			
			case "last24h":
				$e = time() - (1 * 24 * 60 * 60);
				break;			

			case "yesterday":
				$d = time() - (1 * 24 * 60 * 60);
				$e = mktime(23, 59, 59, date("m", $d), date("d", $d), date("Y", $d));
				break;

			case "curweek":
				$dow = date("N",time());
				$y = 7 - $dow;
				$e = time()+(($y-14) * 86400);
				break;

			case "lastweek":
				$dow = date("N",time());
				$y = 7 - $dow;
				$e = time()+(($y-21) * 86400);
				break;

			case "curmonth":
				$e = mktime(23, 59, 59, date("m")-1, date("t"), date("Y"));
				break;

			case "lastmonth":
				$e = mktime(23, 59, 59, date("m")-2, date("t", mktime(0, 0, 0, date("m")-2, 1, date("Y"))), date("Y"));
				break;

			case "curquarter":
				$y = date("Y");
				$quart = ceil(date("m")/3) - 1;
				while( $quart < 1 )
				{
					$y--;
					$quart = 4;
				}
				switch( $quart )
				{
					case 1:	$e = mktime(23, 59, 59,  3, 31, $y); break;
					case 2:	$e = mktime(23, 59, 59,  6, 30, $y); break;
					case 3:	$e = mktime(23, 59, 59,  9, 30, $y); break;
					case 4:	$e = mktime(23, 59, 59, 12, 31, $y); break;
				}
				break;

			case "lastquarter":
				$y = date("Y");
				$quart = ceil(date("m")/3) - 2;
				if( $quart < 0 )
				{
					$y--;
					$quart = 3;
				}
				elseif( $quart < 1 )
				{
					$y--;
					$quart = 4;
				}
				switch( $quart )
				{
					case 1:	$e = mktime(23, 59, 59,  3, 31, $y); break;
					case 2:	$e = mktime(23, 59, 59,  6, 30, $y); break;
					case 3:	$e = mktime(23, 59, 59,  9, 30, $y); break;
					case 4:	$e = mktime(23, 59, 59, 12, 31, $y); break;
				}
				break;

			case "curyear":
				$e = mktime(23, 59, 59, 12, 31, date("Y")-1);
				break;

			case "lastyear":
				$e = mktime(23, 59, 59, 12, 31, date("Y")-2);
				break;

			default:
				$tf = explode("|",$tf);
				if( count($tf) != 2 )
					$e = null;
				else
					$e = self::_ensureDate($tf[1]);
				break;
		}
		if( $format )
			return date($format,$e);
		return $e;
	}

	static function PrevPeriodComparisonHint($addbraces = true)
	{
		switch(TimeFrame::currentTimeFrame())
		{
			case "today":
			case "yesterday":
			case "daybeforeyesterday":
				$repl = getString("TXT_COMPARE_DAYBEFORE");
				break;

			case "curweek":
			case "lastweek":
				$repl = getString("TXT_COMPARE_WEEKBEFORE");
				break;

			case "curmonth":
			case "lastmonth":
				$repl = getString("TXT_COMPARE_MONTHBEFORE");
				break;

			case "curyear":
			case "lastyear":
				$repl = getString("TXT_COMPARE_YEARBEFORE");
				break;
		}
		$ret = getString("TXT_COMPAREHINT", array("%s" => $repl));
		return ($addbraces ? "(".$ret.")" : $ret);
	}

	static function FormatAnyDate($date,$format)
	{
		if( ctype_digit($date) )
		{
			return date($format,$date);
		}
		else
		{
			if( $date == "0000-00-00 00:00:00" )
				return $date;

			if( self::_ensureDate($date) !== false )
				return date($format,self::_ensureDate($date));
			else
				return $date;
		}
	}
}
