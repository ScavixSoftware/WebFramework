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
class PlotValue
{
	var $Value;
	var $Link;
	var $Label;
	var $Dashed = false;
	var $Sliced = false;
	var $Color = false;
	var $ToolTip;
	var $displayValue = false;
	var $showValue = null;
	var $alpha = null;

	function PlotValue($value="")
	{
		if( $value !== "" )
			$this->Value = $value;
	}

	function Render(&$chart)
	{
		$this->Label = $chart->FormatLabel($this->Label);

		$plotxml = "<set ";
		if($this->Label != "")	$plotxml .= "label='".$chart->PrepareLabel($this->Label)."' ";
		if($this->Link != "")	$plotxml .= "link='".urlencode($this->Link)."' ";
		if($this->Dashed)		$plotxml .= "dashed='1' ";
		if($this->Sliced)
			$plotxml .= "isSliced='".($this->Sliced?'1':'0')."' ";
		if($this->Color)		$plotxml .= "color='{$this->Color}' ";
		if($this->ToolTip || strlen($this->ToolTip)>0)		$plotxml .= "toolText='".$chart->PrepareLabel($this->ToolTip)."' ";
		if($this->Value !== "")	$plotxml .= "value='".$this->Value."' ";
		if($this->displayValue !== "")	$plotxml .= "displayValue='".$this->displayValue."' ";
		if( isset($this->showValue) )	$plotxml .= "showValue='".$this->showValue."' ";
		if( isset($this->alpha) ) $plotxml .= "alpha='".$this->alpha."' ";
		
		$plotxml .= "/>";
		return $plotxml;
	}

	function CompareTo($val)
	{
		return strcasecmp($this->Label,$val->Label);
	}
}
