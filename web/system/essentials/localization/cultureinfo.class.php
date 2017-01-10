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

use Closure;
use DateTime;
use DateTimeZone;
use Exception;
use ScavixWDF\Base\DateTimeEx;
use ScavixWDF\WdfException;

/**
 * Represents culture information.
 * 
 * See http://msdn.microsoft.com/en-us/library/system.globalization.cultureinfo%28v=vs.71%29.aspx
 * for some theory
 */
class CultureInfo
{
	var $Code;
	var $ParentCode;
	var $Iso2;
	var $EnglishName;
	var $NativeName;

	var $DateTimeFormat;
	var $CurrencyFormat;
	var $PercentFormat;
	var $NumberFormat;
	var $IsRTL;

	var $Region;
	var $Parent;

	var $DefaultDateFormat = DateTimeFormat::DF_SHORTDATE;
	var $DefaultTimeFormat = DateTimeFormat::DF_SHORTTIME;
	
	private $_alwaysConvertTimesToTimezone = false;
	
	public $CurrenyConversionFunction = false;
	
	function  __construct($code="",$parent="",$iso="",$english="",$native="",$rtl="")
	{
		$this->Code = $code;
		$this->ParentCode = $parent;
		$this->Iso2 = $iso;
		$this->EnglishName = $english;
		$this->NativeName = $native;
		$this->IsRTL = $rtl == "1";

//		$this->TimeZone = getTimeZone();
	}
	
	function __sleep()
	{
		$res = array();		
		foreach( get_object_vars($this) as $name=>$val)
		{
			switch( $name )
			{
				case '_alwaysConvertTimesToTimezone':
				case 'CurrenyConversionFunction':
					break;
				default:
					$res[] = $name;
					break;
			}
		}
		return $res;
	}

	protected function _ensureTimeStamp($date)
	{
		if( is_string($date) )
		{
			// check if a valid int is given (ex: '123123123')
			if( !preg_match('/[^0-9]+/',$date) )
				return $date + 0;
			else
				return strtotime($date);
		}
		elseif( ($date instanceof DateTime) || ($date instanceof DateTimeEx) )
			return intval($date->format('U'));
		return $date;
	}

	private function _ensureDateTimeFormat()
	{
		if( $this->DateTimeFormat )
			return $this->DateTimeFormat;

		$reg = $this->DefaultRegion();
		$ci = $reg->DefaultCulture();
		if( $ci->DateTimeFormat )
			return $ci->DateTimeFormat;
		return false;
	}

	/**
	 * Gets the default region.
	 * 
	 * If set, will return the current Region.
	 * @return RegionInfo The default region
	 */
	function DefaultRegion()
	{
		if( isset($this->Region) )
			return $this->Region;
		$reg = internal_getRegionsForLanguage($this->Code);
		return $reg[0];
	}

	/**
	 * Returns all regions.
	 * 
	 * @param bool $only_codes If true will only return their codes
	 * @return mixed <RegionInfo> or string array for regions
	 */
	function GetRegions($only_codes=false)
	{
		$ci = $this->ResolveToLanguage();
		$regions = internal_getRegionsForLanguage($ci->Code);
		if( !$only_codes )
			return $regions;
		$res = array();
		foreach( $regions as $r )
			$res[] = $r->Code;
		return $res;
	}
	
	/**
	 * Returns a <CultureInfo> object for another region
	 * 
	 * @param mixed $region_code Region code or <RegionInfo> object
	 * @return CultureInfo The resultung culture or false on error
	 */
	function OtherRegion($region_code)
	{
        if( $region_code instanceof RegionInfo )
			return internal_getCultureInfo($this->ResolveToLanguage()->Code.'-'.$region_code->Code);
		
		$region_code = strtoupper($region_code);
		foreach( internal_getRegionsForLanguage($this->Code) as $r )
			if( $r->Code == $region_code )
				return internal_getCultureInfo($this->ResolveToLanguage()->Code.'-'.$region_code);
		return false;
	}

	/**
	 * Resolves to a region neutral culture
	 * 
	 * @return CultureInfo The culture representing the language
	 */
	function ResolveToLanguage()
	{
		$res = clone $this;
		while( isset($res->Parent) && !$res->IsNeutral() )
			$res = $res->Parent;
		return $res;
	}
    
	/**
	 * Ensures that the Region is set.
	 * 
	 * That may be the current one or the default one.
	 * @return CultureInfo A culture with a region set
	 */
    function EnsureRegion()
	{
        $res = clone $this;
		return $res->OtherRegion($res->DefaultRegion());
    }
    
	/**
	 * Checks if this object is region neutral.
	 * 
	 * That means it represents a language.
	 * @return bool true or false
	 */
	function IsNeutral()
	{
		return !isset($this->Region);
	}

	/**
	 * Checks if this is representation of a language.
	 * 
	 * Sample: 'en-US' is child of 'en'
	 * @param mixed $parent Culture code or <CultureInfo> object
	 * @return bool true or false
	 */
	function IsChildOf($parent)
	{
		if( $this->IsNeutral() )
			return false;
		if( $parent instanceof CultureInfo )
			$parent = $parent->Code;
		if( isset($this->Parent->Code) )
			return $this->Parent->Code == $parent;
		return false;
	}

	/**
	 * Checks if this represents the language of another culture.
	 * 
	 * Sample: 'en' is parent of 'en-US'
	 * @param mixed $child Culture code or <CultureInfo> object
	 * @return boolean true or false
	 */
	function IsParentOf($child)
	{
		if( is_string($child) )
			$child = Localization::getCultureInfo($child);
		if( !($child instanceof CultureInfo))
			return false; 

		return $child->IsChildOf($this);
	}

	/**
	 * Sets the timezone.
	 * 
	 * @param string $timezone Timezone identifier
	 * @param bool $alwaysConvertTimesToTimezone If true format methods will convert values to this timezone
	 * @return void
	 */
	function SetTimezone($timezone, $alwaysConvertTimesToTimezone = false)
	{
		$this->TimeZone = $timezone;
		$this->_alwaysConvertTimesToTimezone = $alwaysConvertTimesToTimezone;
	}
	
	/**
	 * Sets the currency
	 * 
	 * @param string $code Currency code
	 * @return void
	 */
	function SetCurrency($code)
	{
		$ci = internal_getCulturesByCurrency($code);
		$ci = internal_getCultureInfo($ci[0]);
		if( $ci && $this->CurrencyFormat->Code != $ci->CurrencyFormat->Code )
			$this->CurrencyFormat = $ci->CurrencyFormat;
	}

	/**
	 * @shortcut <NumberFormat::Format>($number, $decimals, $use_plain)
	 */
	function FormatNumber($number, $decimals=false, $use_plain=false)
	{
		if( !$this->NumberFormat )
			return "No NumberFormat for {$this->Code}";
		return $this->NumberFormat->Format($number, $decimals, $use_plain);
	}

	/**
	 * Formats a duration given in seconds as string.
	 * 
	 * Format is like this:
	 * <code php>
	 * $ci = Localozation::getBrowserCulture();
	 * $with_days = $ci->FormatDuration(86500);      // "1 d 00:01:40"
	 * $no_days   = $ci->FormatDuration(86500,true); // "24:01:40"
	 * <code>
	 * 
	 * This is not very nicely globalozed, but it works for now.
	 * @param int $durationInSeconds Duration to be formatted
	 * @param bool $days_as_hours If true, no days will be printed, but hours may extend the 23 mark
	 * @return string Formatted duration string
	 */
	function FormatDuration($durationInSeconds,$days_as_hours=false)
	{
		$days = floor($durationInSeconds / 86400);
		$durationInSeconds -= $days * 86400;
		$hours = floor($durationInSeconds / 3600);
		$durationInSeconds -= $hours * 3600;
		$minutes = floor($durationInSeconds / 60);
		$seconds = $durationInSeconds - $minutes * 60;

		$ret = '';
		if($days > 0)
		{
			if( $days_as_hours )
				$hours += $days * 24;
			else
				$ret .= $days.' d';
		}
		if($hours > 0)
		{
			$ret .= ' '.$hours .':';
			$ret .= str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':';
		}
		else
			$ret .= ' '.$minutes.':';
		$ret .= str_pad(round($seconds, 0), 2, '0', STR_PAD_LEFT);
		return trim($ret);
	}

	/**
	 * @shortcut <NumberFormat::Format($number, 0)
	 */
	function FormatInt($number)
	{
		return $this->FormatNumber($number, 0);
	}

	/**
	 * @shortcut <CurrencyFormat::Format>($amount, $use_plain, $only_value, $escape_group_separator)
	 */
	function FormatCurrency($amount, $use_plain=false, $only_value=false)
	{
		if( !$this->CurrencyFormat )
			return "No CurrencyFormat for {$this->Code}";
			
		if( $this->CurrenyConversionFunction instanceof Closure )
		{
			$conv = $this->CurrenyConversionFunction;
			$amount = $conv($this,$amount);
		}

		return $this->CurrencyFormat->Format($amount, $use_plain, $only_value);
	}

	/**
	 * @shortcut <DateTimeFormat::Format>
	 */
	function FormatDate($date, $format_id=false, $convert_to_timezone='default')
	{
		if( $convert_to_timezone==='default' ) $convert_to_timezone = $this->_alwaysConvertTimesToTimezone;
		$date = $convert_to_timezone?$this->GetTimezoneDate($date):$this->_ensureTimeStamp($date);
		if( $format_id === false ) $format_id = $this->DefaultDateFormat;
		$dtf = $this->_ensureDateTimeFormat();

		if( !$dtf )
			return "No DateTimeFormat for {$this->Code}";
		
		if( !($dtf instanceof DateTimeFormat) )
		{
			log_error("No DateTimeFormat instance: {$this->Code}",$dtf);
			return "No DateTimeFormat instance: {$this->Code}";
		}
		return $dtf->Format($date, $format_id);
	}

	/**
	 * @shortcut <DateTimeFormat::Format>
	 */
	function FormatTime($date, $format_id=false, $convert_to_timezone='default')
	{
        // If the time to be printed is in another DST range than we are 'now'
        // we must remove that gap to not confuse users with times beeing for example  8:00 instead of 9:00
        // when we look at them from our point of view.
        if( false && $date instanceof DateTime)
        {
            $local = date("I"); $byobj = $date->format("I");
            if( $local < $byobj )
                $date->add(\DateInterval::createFromDateString("-1 hour"));
            elseif( $local > $byobj )
                $date->add(\DateInterval::createFromDateString("1 hour"));
        }
        
		if( $convert_to_timezone==='default' ) $convert_to_timezone = $this->_alwaysConvertTimesToTimezone;
		$date = $convert_to_timezone?$this->GetTimezoneDate($date):$this->_ensureTimeStamp($date);
		if( $format_id === false ) $format_id = $this->DefaultTimeFormat;
		$dtf = $this->_ensureDateTimeFormat();
		
		if( !$dtf )
			return "No DateTimeFormat for {$this->Code}";
		
		if( !($dtf instanceof DateTimeFormat) )
		{
			log_error("No DateTimeFormat instance: {$this->Code}",$dtf);
			return "No DateTimeFormat instance: {$this->Code}";
		}
		
		$res = $dtf->Format($date, $format_id);
		if( isset($this->TimeZone) && isset($this->AppendTimeZone) && $this->AppendTimeZone )
			$res .= " {$this->TimeZone}";
		return $res;
	}

	/**
	 * @shortcut <DateTimeFormat::Format>
	 */
	function FormatDateTime($date, $use_long = false, $convert_to_timezone='default')
	{
		if( $use_long )
			return $this->FormatDate($date,DateTimeFormat::DF_LONGDATE,$convert_to_timezone)." ".$this->FormatTime($date,DateTimeFormat::DF_LONGTIME,$convert_to_timezone);
		return $this->FormatDate($date,false,$convert_to_timezone)." ".$this->FormatTime($date,false,$convert_to_timezone);
	}
	
	/**
	 * @shortcut <PercentFormat::Format($number, $decimals)
	 */
	function FormatPercent($number, $decimals=false)
	{
		if( $this->PercentFormat )
			return $this->PercentFormat->Format($number, $decimals);
		return $this->FormatNumber($number, $decimals)."%";
	}

	/**
	 * Converts a datetime value to this objects timezone
	 * 
	 * @param mixed $date Date as string, integer or <DateTime>
	 * @return int Converted time
	 */
	function GetTimezoneDate($date)
	{
		$date = $this->_ensureTimeStamp($date);
		if( !isset($this->TimeZone) || !$this->TimeZone )
			return $date;
		
		$dt = new DateTime(date('Y-m-d H:i:s', $date));
		try{
			$tz = new DateTimeZone($this->TimeZone);
			$dt->setTimezone($tz);
		}catch(Exception $ex){ 
			$this->TimeZone = "";
			WdfException::Log($ex);
		}
		return strtotime($dt->format('Y-m-d H:i:s'));
	}
	
	/**
	 * Returns the given date/time converted to server's timezone.
	 * 
	 * @param mixed $date Date as string, integer or <DateTime>
	 * @return int Converted time
	 */
	function GetServerDate($date)
	{
		$dtz = date_default_timezone_get();
		$date = $this->_ensureTimeStamp($date);
		if( !isset($this->TimeZone) || !$this->TimeZone || ($this->TimeZone == $dtz) )
			return $date;
		
		$dt = new DateTime(date('Y-m-d H:i:s', $date), new DateTimeZone($this->TimeZone));
		$dt->setTimezone(new DateTimeZone($dtz));
		return strtotime($dt->format('Y-m-d H:i:s'));
	}
}

