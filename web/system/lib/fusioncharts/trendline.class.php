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
namespace ScavixWDF\FusionCharts;

/**
 * @deprecated You will need to buy a license frm them, s please use <GoogleVisualization> instead
 */
class Trendline
{
	var $DisplayName = "";
	var $StartValue = "";
	var $EndValue = "";
	var $Color = "888888";
	var $Thickness = "1";
	var $Alpha = "100";
	var $ValueOnRight ='1';

	function Trendline($displayName,$startValue,$endValue=false)
	{
		$this->DisplayName = $displayName;
		$this->StartValue = $startValue;
		if( $endValue )
			$this->EndValue = $endValue;
	}

	function Render()
	{
		$ret = "<line ";
		$ret .= "startValue='{$this->StartValue}' ";
		$ret .= "endValue='".($this->EndValue == "" ? $this->StartValue : $this->EndValue)."' ";
		$ret .= "color='{$this->Color}' ";
		$ret .= "alpha='{$this->Alpha}' ";
		$ret .= "isTrendZone='".($this->Thickness > 1 ? 1 : 0)."' ";
		$ret .= "thickness='{$this->Thickness}' ";
		$ret .= "displayvalue='{$this->DisplayName}' ";
		$ret .= "valueOnRight='{$this->ValueOnRight}' ";
		$ret .= "/>";
		return $ret;
	}
}
