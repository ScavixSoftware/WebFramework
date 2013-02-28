<?
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

/**
 * A geo chart.
 * 
 * See https://developers.google.com/chart/interactive/docs/gallery
 */
class gvGeoChart extends GoogleVisualization
{
	/**
	 * @override
	 */
	function __initialize($options=array(),$query=false,$ds=false)
	{
		parent::__initialize('GeoChart',$options,$query,$ds);
		$this->_loadPackage('geochart');
	}
	
	/**
	 * @shortcut <GoogleVisualization::opt>('displayMode',$mode)
	 */
	function setDisplayMode($mode)
	{
		return $this->opt('displayMode',$mode);
	}
	
	/**
	 * @shortcut <GoogleVisualization::opt>('colorAxis',array('minValue'=>$min,'maxValue'=>$max,'colors'=>$colors))
	 */
	function setColorAxis($min,$max,$colors)
	{
		return $this->opt('colorAxis',array('minValue'=>$min,'maxValue'=>$max,'colors'=>$colors));
	}
	
	/**
	 * @shortcut <GoogleVisualization::opt>('region',$region)
	 */
	function setRegion($region)
	{
		return $this->opt('region',$region);
	}
}