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
namespace ScavixWDF\Localization;

use DateTimeZone;
use ScavixWDF\Base\Args;
use ScavixWDF\WdfException;

/**
 * Helper class to wrap some tool functions.
 * 
 */
class Localization
{
	const USE_DEFAULT = 0x00;
	const USE_IP = 0x01;
	const USE_BROWSER = 0x02;

	/**
	 * Gets a <CultureInfo> object representing a language.
	 * 
	 * @param string $language_code Lanugage code (DE, EN, ...)
	 * @return CultureInfo The object or false on error
	 */
	public static function getLanguageCulture($language_code)
	{
		$ci = internal_getLanguage($language_code);
		if( !$ci )
		{
			$ci = internal_getCultureInfo($language_code);
			if( $ci )
				$ci = $ci->ResolveToLanguage();
		}
		return $ci?$ci:false;
	}

	/**
	 * Tries to match the remote IP to a culture.
	 * 
	 * @return CultureInfo The detected culture or false
	 */
	public static function getIPCulture()
	{
		if( function_exists('get_countrycode_by_ip') )
		{
			$country = get_countrycode_by_ip();
			if($country)
			{
				$region = internal_getRegion($country);
				if($region)
					return $region->DefaultCulture();
			}
		}
        if(isset($_SERVER["GEOIP_COUNTRY_CODE"]))
		{
            $region = internal_getRegion($_SERVER["GEOIP_COUNTRY_CODE"]);
            if($region)
				return $region->DefaultCulture();
		}
		return false;
	}

	/**
	 * Detects the browsers culture settings.
	 * 
	 * @return CultureInfo The detected culture
	 */
	public static function getBrowserCulture()
	{
		if( Args::sanitized('culture', false, 'CG') )
			return self::getCultureInfo(Args::sanitized('culture', false, 'CG'));

		// language detection forced by request (like api calls from client, portal, ...)
		if( isset($_SERVER['HTTP_FORCE_LANGUAGE']) )
		{
			// Prepare the string that looks like this:
			// ja,en-us;q=0.8,de-de;q=0.6,en;q=0.4,de;q=0.2
			$langs = explode(",",$_SERVER['HTTP_FORCE_LANGUAGE']);
			$parts = array();
			foreach( $langs as $k=>$v )
			{
				$v = explode(";",$v);
				$w = isset($v[1]) && (substr($v[1], 0, 2) == "q=") ? substr($v[1], 2)  : 1;
				$parts[$w * 100] = trim($v[0]);
			}
			// check for first valid language
			foreach( $parts as $k=>$v )
			{
				if(strlen($v) == 2)
				{
					// this is only a language, so get the default region
					$regions = internal_getRegionsForLanguage($v);
					$region = $regions[0];
					$v = $region->KnownCultures[0];
				}
				$ci = self::getCultureInfo($v);
				if( $ci )
					return $ci;
			}
		}

		if( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) )
		{
			// Prepare the string that looks like this:
			// ja,en-us;q=0.8,de-de;q=0.6,en;q=0.4,de;q=0.2
			$langs = explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$parts = array();
			foreach( $langs as $k=>$v )
			{
				$v = explode(";",$v);
				$w = isset($v[1]) && (substr($v[1], 0, 2) == "q=") ? substr($v[1], 2)  : 1;
				$parts[$w * 100] = trim($v[0]);
			}
			// check for first valid language
			foreach( $parts as $k=>$v )
			{
				if(strlen($v) == 2)
				{
					// this is only a language, so get the default region
					$regions = internal_getRegionsForLanguage($v);
					if( $regions && count($regions)>0 )
					{
						$region = $regions[0];
						$v = $region->KnownCultures[0];
					}
					else
						continue;
				}
				$ci = self::getCultureInfo($v);
				if( $ci )
					return $ci;
			}
		}
		return false;
	}

	/**
	 * Tries to detect the culture for the remote user.
	 * 
	 * @param array $detectionOrder Array specifying the detection order like this: array(Localization::USE_BROWSER,Localization::USE_IP)
	 * @return CultureInfo The detected culture or $CONFIG['localization']['default_culture']
	 */
	public static function detectCulture($detectionOrder = false)
	{
		global $CONFIG;
		
		if( !$detectionOrder || (count($detectionOrder)==1 && $detectionOrder[0] == self::USE_DEFAULT) )
			$detectionOrder = $CONFIG['localization']['detection_order'];
		
		if( !is_array($detectionOrder) )
			$detectionOrder = array($detectionOrder);
		
		$ci = false;
		foreach( $detectionOrder as $type )
		{
			switch( $type )
			{
				case self::USE_BROWSER:
					$ci = self::getBrowserCulture();
//					log_error("Detected BROWSER Culture: ".$ci->Code);
					break;
				case self::USE_IP:
					$ci = self::getIPCulture();
//					log_error("Detected IP Culture: ".$ci->Code);
					break;
			}
			if( $ci )
				return $ci;
		}
		global $CONFIG;
		$ci = self::getCultureInfo($CONFIG['localization']['default_culture']);
		return $ci;
	}

	/**
	 * Ensures a culture to a given code.
	 * 
	 * Calls <Localization::getCultureInfo> and if that fails <Localization::detectCulture> to ensure
	 * there's a return value.
	 * @param string $code Culture code
	 * @param array $detectionOrder See <Localization::detectCulture>
	 * @return CultureInfo The best match culture for $code
	 */
	public static function ensureCulture($code,$detectionOrder = false)
	{
		$ci = self::getCultureInfo($code);
		if( $ci )
			return $ci;
		return self::detectCulture($detectionOrder);
	}

	/**
	 * Retuns a CultureInfo object.
	 * 
	 * @param string $code Country code, Language code or culture code
	 * @return CultureInfo CultureInfo object representing the culture or false on error
	 */
	public static function getCultureInfo($code)
	{
		global $arBufferedCultures, $CONFIG;

		if(isset($arBufferedCultures["C".$code]))
			return clone $arBufferedCultures["C".$code];

		$ci = internal_getCultureInfo($code);
		if( !$ci )
		{
            if(isset($CONFIG['localization']['default_culture']))
                $ci = internal_getCultureInfo($CONFIG['localization']['default_culture']);
            
//			log_error("NO CI $code");
			return $ci;
		}
		$arBufferedCultures["C".$code] = clone $ci;
		return $ci;
	}

	/**
	 * Returns the timezone by IP.
	 * 
	 * Will fall back to the $CONFIG['localization']['default_timezone'] setting
	 * @param string $ip IP to get the timezone for
	 * @return string Timezone ID
	 */
	public static function getTimeZone($ip = false)
	{
		global $CONFIG;
		if( function_exists('get_timezone_by_ip') )
		{
			$utz = get_timezone_by_ip($ip);
			if( $utz )
				return $utz;
		}
		return $CONFIG['localization']['default_timezone'];
	}

	/**
	 * Returns a list of all defined Timezones.
	 * 
	 * Wraps <DateTimeZone::listIdentifiers>
	 * @return array All Timezone IDs
	 */
	public static function getAllTimeZones()
	{
		return DateTimeZone::listIdentifiers();
	}

	/**
	 * Returns the default Culture 
	 * 
	 * That is set in `$CONFIG['localization']['default_culture']`
	 * @return string Default culture code
	 */
	public static function localization_default_culture()
	{
		return $GLOBALS['CONFIG']['localization']['default_culture'];
	}

	/**
	 * Returns the currently selected currency.
	 * 
	 * @param string $cultureCode The culture code for which we need the currency. defaults to current culture
	 * @param bool $use_code true: return currency ISO code, false: return currency symbol
	 * @return string Currency code|symbol
	 */
	public static function get_currency($cultureCode = false, $use_code = false)
	{
		global $CONFIG;

		if( $cultureCode !== false && !is_string($cultureCode) )
			WdfException::Raise("Who calls this function with a wrong param? Provide string please!");

		switch( strtolower($cultureCode) )
		{
			case "usd":
			case "eur":
				WdfException::Raise("DO NOT PUT CURRENCY CODES INTO CULTURECODE VARIABLES!!!11elf");
		}
		$ci = self::getCultureInfo($cultureCode);
		if( !isset($ci->CurrencyFormat) )
			$ci = $ci->DefaultRegion()->DefaultCulture();

		if( $use_code )
			return $ci->CurrencyFormat->Code;
		return $ci->CurrencyFormat->Symbol;
	}

	/**
	 * Returns a list of all languages.
	 * 
	 * Note that this method returns the english names of the languages (German, French, ...).
	 * @return array Associative array of lang_code=>lang_name pairs
	 */
	public static function get_language_names()
	{
		$res = array();
		$codes = internal_getAllLanguageCodes();
		foreach( $codes as $c )
		{
			$ci = internal_getLanguage($c);
			$res[$c] = $ci->EnglishName;
		}
		natsort($res);
		return $res;
	}
	
	/**
	 * Gets the default culture for a country.
	 * 
	 * @param string $country_code Country code
	 * @return CultureInfo The default culture or false on error
	 */
	public static function get_country_culture($country_code)
	{
		$region = internal_getRegion($country_code);
		if( !$region )
			return false;
		return $region->DefaultCulture();
	}

	/**
	 * Gets a list of country names.
	 * 
	 * @param mixed $culture_filter <CultureInfo> or code specifying a culture that must be present in a country
	 * @return array Associative array of countrycode=>countryname pairs
	 */
	public static function get_country_names($culture_filter=false)
	{
		$regions = internal_getAllRegionCodes();
		$res = array();

		foreach( $regions as $reg )
		{
			$reg = internal_getRegion($reg);
			if( !$culture_filter )
			{
				$res[$reg->Code] = $reg->EnglishName;
				continue;
			}
			if( $reg->ContainsCulture($culture_filter) )
				$res[$reg->Code] = $reg->EnglishName;
		}
		natsort($res);
		return $res;
	}

	/**
	 * Returns a list of all supported currency codes.
	 * 
	 * @return array List of currency codes
	 */
	public static function get_currency_codes()
	{
		return internal_getAllCurrencyCodes();
	}

	/**
	 * Gets a <CultureInfo> from a currency code.
	 * 
	 * @param string $currency_code Valid currency code(see <get_currency_codes>)
	 * @return CultureInfo The detected culture or false on error
	 */
	public static function get_currency_culture($currency_code)
	{
		$cultures = internal_getCulturesByCurrency($currency_code);
		return self::getCultureInfo($cultures[0]);
	}

	/**
	 * Returns all defined regions.
	 * 
	 * @param bool $only_codes If true only the codes are returned
	 * @return array Array of depending on $only_codes only that or complete <RegionInfo> objects
	 */
	public static function get_all_regions($only_codes=false)
	{
		if( $only_codes )
			return internal_getAllRegionCodes();
		
		$res = array();
		foreach( internal_getAllRegionCodes() as $code )
			$res[] = internal_getRegion($code);
		return $res;
	}

	/**
	 * Returns an array of states for a country (USA only ATM).
	 * 
	 * @param type $country_code The country code to list states of
	 * @return array Associative array of code=>name pairs
	 */
	public static function get_country_states($country_code)
	{
		switch($country_code)
		{
			case "US":
				$ret = array(
					"AK" => "Alaska",
					"AL" => "Alabama",
					"AR" => "Arkansas",
					"AZ" => "Arizona",
					"CA" => "California",
					"CO" => "Colorado",
					"CT" => "Connecticut",
					"DC" => "District of Columbia",
					"DE" => "Delaware",
					"FL" => "Florida",
					"GA" => "Georgia",
					"HI" => "Hawaii",
					"IA" => "Iowa",
					"ID" => "Idaho",
					"IL" => "Illinois",
					"IN" => "Indiana",
					"KS" => "Kansas",
					"KY" => "Kentucky",
					"LA" => "Louisiana",
					"MA" => "Massachusetts",
					"MD" => "Maryland",
					"ME" => "Maine",
					"MI" => "Michigan",
					"MN" => "Minnesota",
					"MO" => "Missouri",
					"MS" => "Mississippi",
					"MT" => "Montana",
					"NC" => "North Carolina",
					"ND" => "North Dakota",
					"NE" => "Nebraska",
					"NH" => "New Hampshire",
					"NJ" => "New Jersey",
					"NM" => "New Mexico",
					"NV" => "Nevada",
					"NY" => "New York",
					"OH" => "Ohio",
					"OK" => "Oklahoma",
					"OR" => "Oregon",
					"PA" => "Pennsylvania",
					"RI" => "Rhode Island",
					"SC" => "South Carolina",
					"SD" => "South Dakota",
					"TN" => "Tennessee",
					"TX" => "Texas",
					"UT" => "Utah",
					"VA" => "Virginia",
					"VT" => "Vermont",
					"WA" => "Washington",
					"WI" => "Wisconsin",
					"WV" => "West Virginia",
					"WY" => "Wyoming"
				);
				return $ret;
				break;
				
			default:
				return array();
		}
	}
	
	/**
	 * Returns the A2 ISO3166 country code from a given A3 ISO3166 country code.
	 * 
	 * @param type $country_code The country code as A3
	 * @return string The A2 ISO3166 or false on error
	 */
	public static function get_countrycodeA2ISOfromA3($country_code)
	{
		$ret = array(
			"ALA" => "AX", "AFG" => "AF", "ALB" => "AL", "DZA" => "DZ", "ASM" => "AS",
			"AND" => "AD", "AGO" => "AO", "AIA" => "AI", "ATA" => "AQ", "ATG" => "AG",
			"ARG" => "AR", "ARM" => "AM", "ABW" => "AW", "AUS" => "AU", "AUT" => "AT",
			"AZE" => "AZ", "BHS" => "BS", "BHR" => "BH", "BGD" => "BD", "BRB" => "BB",
			"BLR" => "BY", "BEL" => "BE", "BLZ" => "BZ", "BEN" => "BJ", "BMU" => "BM",
			"BTN" => "BT", "BOL" => "BO", "BIH" => "BA", "BWA" => "BW", "BVT" => "BV",
			"BRA" => "BR", "IOT" => "IO", "BRN" => "BN", "BGR" => "BG", "BFA" => "BF",
			"BDI" => "BI", "KHM" => "KH", "CMR" => "CM", "CAN" => "CA", "CPV" => "CV",
			"CYM" => "KY", "CAF" => "CF", "TCD" => "TD", "CHL" => "CL", "CHN" => "CN",
			"CXR" => "CX", "CCK" => "CC", "COL" => "CO", "COM" => "KM", "COD" => "CD",
			"COG" => "CG", "COK" => "CK", "CRI" => "CR", "CIV" => "CI", "HRV" => "HR",      
			"CUB" => "CU", "CYP" => "CY", "CZE" => "CZ", "DNK" => "DK", "DJI" => "DJ",
			"DMA" => "DM", "DOM" => "DO", "ECU" => "EC", "EGY" => "EG", "SLV" => "SV",
			"GNQ" => "GQ", "ERI" => "ER", "EST" => "EE", "ETH" => "ET", "FLK" => "FK",
			"FRO" => "FO", "FJI" => "FJ", "FIN" => "FI", "FRA" => "FR", "GUF" => "GF",
			"PYF" => "PF", "ATF" => "TF", "GAB" => "GA", "GMB" => "GM", "GEO" => "GE",  
			"DEU" => "DE", "GHA" => "GH", "GIB" => "GI", "GRC" => "GR", "GRL" => "GL",
			"GRD" => "GD", "GLP" => "GP", "GUM" => "GU", "GTM" => "GT", "GIN" => "GN",
			"GNB" => "GW", "GUY" => "GY", "HTI" => "HT", "HMD" => "HM", "HND" => "HN",
			"HKG" => "HK", "HUN" => "HU", "ISL" => "IS", "IND" => "IN", "IDN" => "ID",
			"IRN" => "IR", "IRQ" => "IQ", "IRL" => "IE", "ISR" => "IL", "ITA" => "IT",
			"JAM" => "JM", "JPN" => "JP", "JOR" => "JO", "KAZ" => "KZ", "KEN" => "KE",
			"KIR" => "KI", "PRK" => "KP", "KOR" => "KR", "KWT" => "KW", "KGZ" => "KG",  
			"LAO" => "LA", "LVA" => "LV", "LBN" => "LB", "LSO" => "LS", "LBR" => "LR",
			"LBY" => "LY", "LIE" => "LI", "LTU" => "LT", "LUX" => "LU", "MAC" => "MO",
			"MKD" => "MK", "MDG" => "MG", "MWI" => "MW", "MYS" => "MY", "MDV" => "MV",
			"MLI" => "ML", "MLT" => "MT", "MHL" => "MH", "MTQ" => "MQ", "MRT" => "MR",
			"MUS" => "MU", "MYT" => "YT", "MEX" => "MX", "FSM" => "FM", "MDA" => "MD",  
			"MCO" => "MC", "MNG" => "MN", "MSR" => "MS", "MAR" => "MA", "MOZ" => "MZ",
			"MMR" => "MM", "NAM" => "NA", "NRU" => "NR", "NPL" => "NP", "NLD" => "NL",
			"ANT" => "AN", "NCL" => "NC", "NZL" => "NZ", "NIC" => "NI", "NER" => "NE",
			"NGA" => "NG", "NIU" => "NU", "NFK" => "NF", "MNP" => "MP", "NOR" => "NO",
			"OMN" => "OM", "PAK" => "PK", "PLW" => "PW", "PSE" => "PS", "PAN" => "PA",
			"PNG" => "PG", "PRY" => "PY", "PER" => "PE", "PHL" => "PH", "PCN" => "PN",
			"POL" => "PL", "PRT" => "PT", "PRI" => "PR", "QAT" => "QA", "REU" => "RE",
			"ROU" => "RO", "RUS" => "RU", "RWA" => "RW", "SHN" => "SH", "KNA" => "KN",
			"LCA" => "LC", "SPM" => "PM", "VCT" => "VC", "WSM" => "WS", "SMR" => "SM",
			"STP" => "ST", "SAU" => "SA", "SEN" => "SN", "SCG" => "CS", "SYC" => "SC",
			"SLE" => "SL", "SGP" => "SG", "SVK" => "SK", "SVN" => "SI", "SLB" => "SB",
			"SOM" => "SO", "ZAF" => "ZA", "SGS" => "GS", "ESP" => "ES", "LKA" => "LK",
			"SDN" => "SD", "SUR" => "SR", "SJM" => "SJ", "SWZ" => "SZ", "SWE" => "SE",
			"CHE" => "CH", "SYR" => "SY", "TWN" => "TW", "TJK" => "TJ", "TZA" => "TZ",
			"THA" => "TH", "TLS" => "TL", "TGO" => "TG", "TKL" => "TK", "TON" => "TO",
			"TTO" => "TT", "TUN" => "TN", "TUR" => "TR", "TKM" => "TM", "TCA" => "TC",
			"TUV" => "TV", "UGA" => "UG", "UKR" => "UA", "ARE" => "AE", "GBR" => "GB",
			"USA" => "US", "UMI" => "UM", "URY" => "UY", "UZB" => "UZ", "VUT" => "VU",
			"VAT" => "VA", "VEN" => "VE", "VNM" => "VN", "VGB" => "VG", "VIR" => "VI",
			"WLF" => "WF", "ESH" => "EH", "YEM" => "YE", "ZMB" => "ZM", "ZWE" => "ZW"
		);
		
		if(isset($ret[$country_code]))
			return $ret[$country_code];
		
		return false;
	}
}
