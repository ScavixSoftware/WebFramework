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
 * Modul to localize ip-adresses.
 * 
 * Uses the [free version of GeoIP](http://www.maxmind.com/app/geolitecity) from maxmind.
 * In the majority of cases maxmind publishes updates for the GeoLiteCity.dat on the first day each month.
 * @return void
*/
function geoip_init()
{
	global $CONFIG;
	if( !function_exists('geoip_country_code_by_name') )
	{
		require_once(__DIR__."/geoip/geoip.inc");
		require_once(__DIR__."/geoip/geoipcity.inc");
	}

	if( !system_is_module_loaded('curlwrapper') )
		WdfException::Raise("Missing module: curlwrapper!");
		
	if( !isset($GLOBALS['current_ip_addr']) )
		$GLOBALS['current_ip_addr'] = get_ip_address();
	
	if( !isset($CONFIG['geoip']['city_dat_file']) )
		$CONFIG['geoip']['city_dat_file'] = __DIR__."/geoip/GeoLiteCity.dat";
	
	if( !file_exists($CONFIG['geoip']['city_dat_file']) )
		WdfException::Raise("GeoIP module: missing GeoLiteCity.dat! Get it from http://dev.maxmind.com/geoip/legacy/geolite/");
}

/**
 * Resolves an IP address to a location.
 * 
 * @param string $ip_address IP address to check (defaults to <get_ip_address>)
 * @return stdClass Object containing location information
 */
function get_geo_location_by_ip($ip_address=null)
{
	if( is_null($ip_address) ) 
		$ip_address = $GLOBALS['current_ip_addr'];

	// local ips throw an error, so ignore them:
	if(starts_with($ip_address, "192.168.1."))
		return false;
	if( function_exists('geoip_open') )
	{
		$gi = geoip_open($GLOBALS['CONFIG']['geoip']['city_dat_file'],GEOIP_STANDARD);
		$location = geoip_record_by_addr($gi,$ip_address);
		geoip_close($gi);
		return $location;
	}
	$location = @geoip_record_by_name($ip_address);
	return (object) $location;
}

/**
 * Returns the region name for the current IP address
 * 
 * See <get_ip_address>
 * @return string Location name or empty string if unknown
 */
function get_geo_region()
{
	include(__DIR__."/geoip/geoipregionvars.php");
	if( function_exists('geoip_open') )
	{
		$gi = geoip_open($GLOBALS['CONFIG']['geoip']['city_dat_file'],GEOIP_STANDARD);
		$location = geoip_record_by_addr($gi,$GLOBALS['current_ip_addr']);
		geoip_close($gi);
		if(!isset($GEOIP_REGION_NAME[$location->country_code]))
			return "";
	}
	else
		$location = (object) geoip_record_by_name($GLOBALS['current_ip_addr']);
	return $GEOIP_REGION_NAME[$location->country_code][$location->region];
}

/**
 * Resolves an IP address to geo coordinates.
 * 
 * @param string $ip IP address to resolve (defaults to <get_ip_address>)
 * @return array Associative array with keys 'latitude' and 'longitude'
 */
function get_coordinates_by_ip($ip = false)
{
	// ip could be something like "1.1 ironportweb01.gouda.lok:80 (IronPort-WSA/7.1.1-038)" from proxies
	if($ip === false)
		$ip = $GLOBALS['current_ip_addr'];
	if(starts_with($ip, "1.1 ") || starts_with($ip, "192.168.1."))
		return false;
	
	if( function_exists('geoip_open') )
	{
		$gi = geoip_open($GLOBALS['CONFIG']['geoip']['city_dat_file'],GEOIP_STANDARD);
		$location = geoip_record_by_addr($gi,$ip);
		geoip_close($gi);
	}
	else
		$location = (object) geoip_record_by_name($ip);
	
	if(!isset($location->latitude) && !isset($location->longitude))
	{
		log_error("get_coordinates_by_ip: No coordinates found for IP ".$ip);
		return false;
	}
	
	$coordinates = array();
	$coordinates["latitude"] = $location->latitude;
	$coordinates["longitude"] = $location->longitude;

	return $coordinates;
}

/**
 * Resolves an IP address to a country code
 * 
 * @param string $ipaddr IP address to resolve (defaults to <get_ip_address>)
 * @return array Country code or empty string if not found
 */
function get_countrycode_by_ip($ipaddr = false)
{
	if($ipaddr === false)
		$ipaddr = $GLOBALS['current_ip_addr'];
	if( isset($_SESSION['geoip_countrycode_by_ip_'.$ipaddr]) && $_SESSION['geoip_countrycode_by_ip_'.$ipaddr] != "" )
		return $_SESSION['geoip_countrycode_by_ip_'.$ipaddr];

	if( function_exists('geoip_open') )
	{
		$gi = geoip_open($GLOBALS['CONFIG']['geoip']['city_dat_file'],GEOIP_STANDARD);
		$country_code = geoip_country_code_by_addr($gi,$ipaddr);
		geoip_close($gi);
	}
	else
		$country_code = geoip_country_code_by_name($ipaddr);
	
	if($country_code == "")
	{
		if(isDev() && starts_with($ipaddr, '192.168.1.'))
			$country_code = 'DE';
		else
		{
			$location = get_geo_location_by_ip($ipaddr);
			if($location && isset($location->country_code))
				$country_code = $location->country_code;
		}
	}
	$_SESSION['geoip_countrycode_by_ip_'.$ipaddr] = $country_code."";
	
	return $country_code;
}

/**
 * Returns the country name from the current IP
 * 
 * See <get_ip_address>
 * @return string Country name or empty string if unknown
 */
function get_countryname_by_ip()
{
//	// maxmind installed as server module?
//	if(isset($_SERVER["GEOIP_COUNTRY_CODE"]))
//		return $_SERVER["GEOIP_COUNTRY_CODE"];
	if( function_exists('geoip_open') )
	{
		$gi = geoip_open($GLOBALS['CONFIG']['geoip']['city_dat_file'],GEOIP_STANDARD);
		$country_name = geoip_country_name_by_name($gi,$GLOBALS['current_ip_addr']);
		geoip_close($gi);
	}
	else
		$country_name = geoip_country_name_by_name($GLOBALS['current_ip_addr']);

	return $country_name;
}

/**
 * Returns the timezone for an IP address.
 * 
 * @param string $ip IP address to check (defaults to <get_ip_address>)
 * @return string Timezone identifier or false on error
 */
function get_timezone_by_ip($ip = false)
{
	if($ip === false)
		$ip = $GLOBALS['current_ip_addr'];

	if( starts_with($ip, "1.1 ") || starts_with($ip, "192.168.1.") )
		return false;
    
	$key = "get_timezone_by_ip.".getAppVersion('nc')."-".$ip;
    $ret = cache_get($key);
	if( $ret )
		return $ret;
	
	/*
	// new url with api key:
	$url = "https://api.ipinfodb.com/v3/ip-city/?key=ae4dea477cd8a36cc678c582c3f990fb57a5aae696f878b4e0eee70afa53bf1e&ip=".$GLOBALS['current_ip_addr']."&format=xml";
	try
	{
		$xml = downloadData($url, false, false, 60 * 60, 2);
	}catch(Exception $ex){ WdfException::Log("Unable to get Timezone for ".$ip." ($url)",$ex); return false; }
	if( preg_match_all('/<timeZone>([^<]*)<\/timeZone>/', $xml, $zone, PREG_SET_ORDER) )
	{
		$zone = $zone[0];
		if($zone[1] != "")
		{
            cache_set($key,$zone[1], 24 * 60 * 60);
			return $zone[1];
		}
	}
//	log_error("No timezone found for ".$GLOBALS['current_ip_addr']." via ipinfodb.com");
	 */
	
	$url = "http://ip-api.com/php/".$ip;
	try
	{
		$data = @unserialize(downloadData($url, false, false, 60 * 60, 2));
	}catch(Exception $ex){ WdfException::Log("Unable to get Timezone for ".$ip." ($url) ".$ex->getMessage(),$ex); return false; }
	if($data && $data['status'] == 'success')
	{
		$zone = $data['timezone'];
		cache_set($key, $zone, 24 * 60 * 60);
		return $zone;
	}
	log_error("No timezone found for ".$ip." via ip-api.com");

	$coords = get_coordinates_by_ip($ip);
	if($coords === false)
	{
		log_error("No timezone found for IP ".$ip." (missing coordinates)");
		// disaster-fallback: use our timezone:
		return "Etc/GMT+2";
	}

	// ALTERNATIVE 1:
//	ws.geonames.org had only timeouts on 2/10/2010...
//	$url = "http://ws.geonames.org/timezone?lat=".$coords['latitude'].'&lng='.$coords['longitude'];
	$url = "http://api.geonames.org/timezone?lat=".$coords['latitude'].'&lng='.$coords['longitude']."&username=scavix";
	try
	{
		$xml = downloadData($url, false, false, 60 * 60, 2);
	}catch(Exception $ex){ WdfException::Log("Unable to get Timezone for ".$ip." ($url) ".$ex->getMessage(),$ex); return false; }
	if( preg_match_all('/<timezoneId>([^<]*)<\/timezoneId>/', $xml, $zone, PREG_SET_ORDER) )
	{
		$zone = $zone[0];
		cache_set($key,$zone[1], 24 * 60 * 60);
		return $zone[1];
	}
	log_error("No timezone found for ".$ip." via geonames.org");

	// ALTERNATIVE 2:
	$url = "http://www.earthtools.org/timezone/".$coords['latitude'].'/'.$coords['longitude'];
	try
	{
		$xml = downloadData($url, false, false, 60 * 60, 2);
	}catch(Exception $ex){ WdfException::Log("Unable to get Timezone for ".$ip." ($url)",$ex); return false; }
	if( preg_match_all('/<offset>([^<]*)<\/offset>/', $xml, $zone, PREG_SET_ORDER) )
	{
		$zone = $zone[0];
		$zone[1] = round($zone[1], 0);
		$ret = "Etc/GMT".($zone[1] < 0 ? $zone[1] : "+".$zone[1]);
		cache_set($key,$ret, 24 * 60 * 60);
		return $ret;
	}
	log_error("No timezone found for ".$ip." via earthtools.org");

	// disaster-fallback: use our timezone:
	return "Etc/GMT+2";
}
