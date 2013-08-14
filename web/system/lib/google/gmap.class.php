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
namespace ScavixWDF\Google;

use Exception;
use stdClass;

/**
 * This is a google map.
 * 
 * See https://developers.google.com/maps/documentation/javascript/tutorial
 */
class gMap extends GoogleControl
{
	const ROADMAP = 'google.maps.MapTypeId.ROADMAP';
	const SATELLITE = 'google.maps.MapTypeId.SATELLITE';
	const HYBRID = 'google.maps.MapTypeId.HYBRID';
	const TERRAIN = 'google.maps.MapTypeId.TERRAIN';
	
	var $gmOptions = array('sensor'=>false,'language'=>'en','region'=>'DE');
	private $_basicOptions = array('center'=>'new google.maps.LatLng(-34.397, 150.644)','zoom'=>13,'mapTypeId'=>self::ROADMAP);
	private $_markers = array();
	private $_addresses = array();
	
	/**
	 * @param array $options See https://developers.google.com/maps/documentation/javascript/tutorial#MapOptions
	 */
	function __initialize($options=array())
	{
		parent::__initialize('div');
		$this->gmOptions = array_merge($this->gmOptions,$options);
		$this->gmOptions['sensor'] = ($this->gmOptions['sensor'])?'true':'false';
		$this->_loadApi('maps','3',array('other_params'=>http_build_query($this->gmOptions)));
	}
	
	/**
	 * @override
	 */
	function PreRender($args = array())
	{
		$id = $this->id;
        $this->_basicOptions['center'] = '[jscode]'.$this->_basicOptions['center'];
        $this->_basicOptions['mapTypeId'] = '[jscode]'.$this->_basicOptions['mapTypeId'];
		$init = array("wdf.gmap.init('$id',".system_to_json($this->_basicOptions).");");
		
		foreach( $this->_markers as $m )
		{
			list($lat,$lng,$opt) = $m;
			$init[] = "wdf.gmap.addMarker('$id',$lat,$lng,".json_encode($opt).")";
		}
		foreach( $this->_addresses as $a )
		{
			$init[] = "wdf.gmap.addAddress('$id',".json_encode($a).")";
		}
    	$init[] = "wdf.gmap.showAllMarkers('$id')";
			
		$this->_addLoadCallback('maps', $init);
		return parent::PreRender($args);
	}
	
	/**
	 * Adds a marker to the map.
	 * 
	 * @param float $lat Latitute
	 * @param float $lng Longitude
	 * @param array $options See https://developers.google.com/maps/documentation/javascript/reference#MarkerOptions
	 * @return gMap `$this`
	 */
	function AddMarker($lat, $lng, $options = array())
	{
		$this->_markers[] = array($lat,$lng,$options);
		return $this;
	}
	
	/**
	 * Shortcut for a named marker.
	 * 
	 * @param float $lat Latitude
	 * @param float $lng Longitude
	 * @param string $title Marker title
	 * @param array $options See https://developers.google.com/maps/documentation/javascript/reference#MarkerOptions
	 * @return gMap `$this`
	 */
	function AddMarkerTitled($lat, $lng, $title, $options = array())
	{
		$options['title'] = $title;
		$this->_markers[] = array($lat,$lng,$options);
		return $this;
	}

	/**
	 * Adds an address to the map.
	 * 
	 * Will use googles geolocation to resolve the address to a marker.
	 * @param string $address The address as string
	 * @return gMap `$this`
	 */
	function AddAddress($address)
	{
		$this->_addresses[] = $address;
		return $this;
	}
	
	/**
	 * Sets the maps center point.
	 * 
	 * @param float $lat Latitude
	 * @param float $lng Longitude
	 * @return gMap `$this`
	 */
	function setCenterPoint($lat,$lng)
	{
		$this->_basicOptions['center'] = "new google.maps.LatLng($lat,$lng)";
		return $this;
	}
	
	/**
	 * Sets the maps type.
	 * 
	 * @param string $type One of gMap::ROADMAP, gMap::SATELLITE, gMap::HYBRID, gMap::TERRAIN
	 * @return gMap `$this`
	 */
	function setType($type)
	{
		$this->_basicOptions['mapTypeId'] = $type;
		return $this;
	}
	
	/**
	 * Sets the maps zoom level.
	 * 
	 * @param int $zoomlevel The initial zoom level
	 * @return gMap `$this`
	 */
	function setZoom($zoomlevel)
	{
		$this->_basicOptions['zoom'] = $zoomlevel;
		return $this;
	}
    
    /**
     * Finds a geolocation from a search string.
	 * 
     * @param string $search Search string
	 * @return mixed An object containing formatted_address, latitude and longitude or false on error
     */
    static public function FindGeoLocation($search)
    {
        $ret = new stdClass();
        $geourl = "http://maps.google.com/maps/api/geocode/xml?address=".urlencode($search)."&sensor=false";
        $xmlsrc = utf8_encode(file_get_contents($geourl));
        try {
            $xml = simplexml_load_string($xmlsrc);
        }
        catch(Exception $e){
            log_error($geourl."\r\n".$xmlsrc);
            return false;
        }

        if( strtoupper($xml->status) == 'OK' )
        {
            $ret->formatted_address =  (string) $xml->result->formatted_address;
            $ret->latitude =  (string) $xml->result->geometry->location->lat;
            $ret->longitude = (string) $xml->result->geometry->location->lng;
            return $ret;
        }
        else if( strtoupper($xml->status) == 'ZERO_RESULTS')
            return false;
        else
        {
            log_error($geourl, $xml);
            return false;
        }
    }
}