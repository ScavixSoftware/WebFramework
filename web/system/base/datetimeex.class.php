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
namespace ScavixWDF\Base;

use DateInterval;
use DateTime;
use ScavixWDF\WdfException;

/**
 * Extends <DateTime> with sime useful methods.
 * 
 */
class DateTimeEx extends DateTime
{
	const SECONDS = 'sec';
	const MINUTES = 'min';
	const HOURS   = 'hour';
	const DAYS    = 'day';
	const WEEKS   = 'weeks';
	const MONTHS  = 'month';
	const YEARS   = 'year';
	
	function __toString()
	{
		return $this->format("Y-m-d H:i:s");
	}

	/**
	 * Creates a new DateTimeEx object ready for method chaining.
	 * 
	 * @param mixed $source <DateTimeEx>, <DateTime> or anything <DateTime> accepts in it's constructor
	 * @return DateTimeEx The created instance
	 */
	public static function Make($source=false, $format = false)
	{
		if( $source )
		{
            if( $source === "now()" )
                return new DateTimeEx();
            if( $format )
            {
                $source = \DateTime::createFromFormat($format,$source);
                if( !$source )
                    WdfException::Raise("Error creating DateTime object from format '$format' and source '$source'");
            }
			if( $source instanceof DateTimeEx )
				return clone $source;
			if( $source instanceof DateTime )
				return new DateTimeEx( $source->format('c') );
			if( is_numeric($source) )
				return new DateTimeEx( date('c',$source) );
			return new DateTimeEx($source);
		}
		return new DateTimeEx();
	}
	
	/**
	 * Returns a new DateTimeEx object representing 'now'.
	 * 
	 * @return DateTimeEx The created instance
	 */
	public static function Now()
	{
		return new DateTimeEx();
	}
	
	/**
	 * Returns a new DateTimeEx object representing the current day at midnight.
	 * 
	 * @return DateTimeEx The created instance
	 */
	public static function Today()
	{
		return new DateTimeEx(date('Y-m-d 0:00:00'));
	}
	
	/**
	 * Returns a new DateTimeEx object representing the first day of the year.
	 * 
	 * If `$date` is given that date's first day of year will be returned.
	 * @param mixed $date Starting point for the calculation
	 * @return DateTimeEx The created instance
	 */
	public static function FirstDayOfYear($date=false)
	{
		return new DateTimeEx(date('Y-1-1 00:00:00', intval(DateTimeEx::Make($date)->format('U'))));
	}
	
	/**
	 * Returns a new DateTimeEx object representing the first day of the month.
	 * 
	 * If `$date` is given that date's first day of month will be returned.
	 * @param mixed $date Starting point for the calculation
	 * @return DateTimeEx The created instance
	 */
	public static function FirstDayOfMonth($date=false)
	{
		return new DateTimeEx(date('Y-m-1 00:00:00', intval(DateTimeEx::Make($date)->format('U'))));
	}
	
	/**
	 * Returns a new DateTimeEx object representing the first day of the week.
	 * 
	 * If `$date` is given that date's first day of week will be returned.
	 * @param mixed $date Starting point for the calculation
	 * @return DateTimeEx The created instance
	 */
	public static function FirstDayOfWeek($date=false)
	{
		$dt = new DateTimeEx(date('Y-m-d 0:00:00', intval(DateTimeEx::Make($date)->format('U'))));
		$dt->sub(new DateInterval('P'.($dt->format('w') > 0 ? $dt->format('w')-1 : 7).'D'));
		return $dt;
	}
	
	/**
	 * Adds an offset.
	 * 
	 * Will not modify this object but return a clone.
	 * $value and $interval are in fact only separated for good readability:
	 * <code php>
	 * $dte->Offset(1,'day')->Offset('1 day',''); // all the same
	 * $dte->Offset('1 day ','+ 1 year')->Offset(1,'day + 1 year'); // all the same
	 * </code>
	 * @param string $value See <DateInterval::createFromDateString>
	 * @param string $interval See <DateInterval::createFromDateString>
	 * @return DateTimeEx A new instance
	 */
	public function Offset($value,$interval='')
	{
		$di = DateInterval::createFromDateString("$value $interval");
		$res = clone $this;
		$res->add($di);
		return $res;
	}
    
    public function midnight()
    {
        return new DateTimeEx(date("Y-m-d 00:00:00",$this->getTimestamp()));
    }
	
	/**
	 * Calculates the age in x
	 * 
	 * Depending on $unit returns the age of the object in years, months, days, hours, minutes or secods.
	 * @param string $unit Values: sec, min, hour, day, weeks, month, year
	 * @param DateTimeEx $zero_point The point in time this DateTimeEx object shall be compared to. Defaults to <DateTimeEx::Now>()
	 * @return float The calculated age
	 */
	public function Age($unit, $zero_point=false)
	{
		$now = $zero_point?$zero_point:self::Now();
		$factor = ($this>$now)?-1:1;
		$diff = $now->diff($this);
		switch( $unit )
		{
			case self::YEARS:
				return $factor * $diff->y;
			case self::MONTHS:
				return $factor * ($diff->y*12 + $diff->m);
			case self::DAYS:
				return $factor * $diff->days;
			case self::HOURS:
				return $factor * ($diff->days*24 + $diff->h);
			case self::MINUTES:
				return $factor * ($diff->days*1440 + $diff->h*60 + $diff->i);
			case self::SECONDS:
				return $factor * ($diff->days*86400 + $diff->h*3600 + $diff->i*60 + $diff->s);
		}
		WdfException::Raise("Getting the age is not possible in unit '$unit'");
	}
	
	/**
	 * Checks if this object is younger than x
	 * 
	 * Best to understand with some samples:
	 * <code php>
	 * $dte->youngerThan(1,'day');
	 * $dte->youngerThan(30,'seconds');
	 * $dte->youngerThan(15,'months');
	 * </code>
	 * @param int $value Offset value
	 * @param string $interval Unit
	 * @return bool true or false
	 */
	public function youngerThan($value,$interval)
	{
		$other = new DateTime("-$value $interval");
		return $this > $other;
	}
	
	/**
	 * Checks if this object is older than x
	 * 
	 * Best to understand with some samples:
	 * <code php>
	 * $dte->olderThan(1,'day');
	 * $dte->olderThan(30,'seconds');
	 * $dte->olderThan(15,'months');
	 * </code>
	 * @param int $value Offset value
	 * @param string $interval Unit
	 * @return bool true or false
	 */
	public function olderThan($value,$interval)
	{
		$other = new DateTime("-$value $interval");
		return $this < $other;
	}
	
	/**
	 * @shortcut <DateTimeEx::youngerThan>($days,DateTimeEx::DAYS)
	 */	
	public function yt_days($days)
	{
		return $this->youngerThan($days, self::DAYS);
	}
	
	/**
	 * @shortcut <DateTimeEx::youngerThan>($days,DateTimeEx::HOURS)
	 */	
	public function yt_hours($hours)
	{
		return $this->youngerThan($hours, self::HOURS);
	}
	
	/**
	 * @shortcut <DateTimeEx::youngerThan>($days,DateTimeEx::MINUTES)
	 */	
	public function yt_mins($minutes)
	{
		return $this->youngerThan($minutes, self::MINUTES);
	}
	
	/**
	 * @shortcut <DateTimeEx::olderThan>($days,DateTimeEx::DAYS)
	 */	
	public function ot_days($days)
	{
		return $this->olderThan($days, self::DAYS);
	}
	
	/**
	 * @shortcut <DateTimeEx::olderThan>($days,DateTimeEx::HOURS)
	 */	
	public function ot_hours($hours)
	{
		return $this->olderThan($hours, self::HOURS);
	}
	
	/**
	 * @shortcut <DateTimeEx::olderThan>($days,DateTimeEx::MINUTES)
	 */	
	public function ot_mins($minutes)
	{
		return $this->olderThan($minutes, self::MINUTES);
	}
	
	/**
	 * @shortcut <DateTimeEx::Age>(DateTimeEx::SECONDS)
	 */	
	public function age_secs()
	{
		return $this->Age(self::SECONDS);
	}
	
	/**
	 * Checks if this represents a date/time in the future.
	 * 
	 * That sounds mystic, in fact just `return $this > self::Now();`
	 * @return bool true or false
	 */
	public function is_future_date()
	{
		return $this > self::Now();
	}
	
	/**
	 * Checks if this represents a date/time in the past.
	 * 
	 * In fact just `return $this < self::Now();`
	 * @return bool true or false
	 */
	public function is_past_date()
	{
		return $this < self::Now();
	}
}
