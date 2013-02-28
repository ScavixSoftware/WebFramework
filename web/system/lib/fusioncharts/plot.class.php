<?php
/**
 * Scavix Web Development Framework
 *
 * Copyright (c) 2007-2012 PamConsult GmbH
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
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 */
 
/**
 * @deprecated You will need to buy a license frm them, s please use <GoogleVisualization> instead
 */
class Plot
{
	var $Title = "noname";
	var $Style = "bar";
	var $Data = null;
	var $Color = "";
	var $plot_group = "";
    var $ShowSumInLegend = false;
	var $AutoSlice = true;
	var $alpha = null;

	function Plot($style,$title,$lineColor="")
	{
		$this->Style = $style;
		$this->Title = $title;
		$this->Data = array();
		$this->Color = $lineColor;
	}

	function GetLabels()
	{
		$res = array();
		$dcnt = count($this->Data);
		for($i=0; $i<$dcnt; $i++)
			$res[] = $this->Data[$i]->Label;

		return $res;
	}

	function &AddValue($val="", $label="", $skip_if_zero=false)
	{
		if( $skip_if_zero && $val == 0 )
		{
			$null = null;
			return $null;
		}
		$v = new PlotValue($val);
		if( $label )
		{
			$v->Label = $label;
			$v->ToolTip = "$label: {$val}";
		}
		$this->Data[] = $v;
		return $v;
	}

	function &Insert($val,$label,$index)
	{
		$v = new PlotValue($val);
		$v->Label = $label;

		$dcnt = count($this->Data);
		for($index=0; $index<$dcnt; $index++)
			if( $this->Data[$index]->CompareTo($v) > 0 )
				break;

		for($i=$dcnt; $i>$index; $i--)
			$this->Data[$i] = $this->Data[$i-1];

		$this->Data[$index] = $v;
		return $v;
	}

	function getValue($label,$return_obj_reference = false)
	{
		foreach( $this->Data as &$val )
			if( $val->Label == $label )
			{
				if($return_obj_reference)
					return $val;
				else
					return $val->Value;
			}
		return 0;
	}

	function hasGroup()
	{
		if(!empty($this->plot_group))
			return true;
		return false;
	}

	function hasValue($label)
	{
		foreach( $this->Data as &$val )
			if( $val->Label == $label )
			{
				//log_debug($this->Title." has value '$label': ".$val->Value);
				return true;
			}
		return false;
	}

	function addToValue($label,$inc)
	{
		$dcnt = count($this->Data);
		for($i=0; $i<$dcnt; $i++)
			if( $this->Data[$i]->Label == $label )
			{
				$this->Data[$i]->Value += $inc;
				return;
			}
	}

	function PercentValue($label,$max)
	{
		$dcnt = count($this->Data);
		for($i=0; $i<$dcnt; $i++)
		{
			if( $this->Data[$i]->Label == $label )
			{
				if( $max != 0 && $this->Data[$i]->Value != "" )
					$this->Data[$i]->Value = $this->Data[$i]->Value / $max * 100;
				//log_debug($this->Title." PercentValue($label,$max) -> ".$this->Data[$i]->Value);
				break;
			}
		}
	}

	function Render(&$chart)
	{
		$plotxml = "";
		if($chart->IsMultiPlot())
		{
            $sum = 0;
            foreach($this->Data as $val)
                $sum += $val->Value;

			$plotxml .= "<dataset color='".$this->Color."' lineThickness='2' anchorRadius='4' ";
			if($this->Title != "")
				$plotxml .= "seriesName='".$this->Title.($this->ShowSumInLegend ? " (".$GLOBALS["BRANDING"]->FormatNumber($sum).")" : "")."' ";
			$plotxml .= "renderAs='".$this->Style."'";
			
			if( isset($this->alpha) ) $plotxml .= " alpha='".$this->alpha."'";
			
			$plotxml .= ">";
		}
		$i = 0;
		foreach($this->Data as $val)
		{
			if( $this->AutoSlice )
			{
				if((substr($this->Style, 0, 3) == "Pie") && ($i < 3))
					$val->Sliced = true;
			}
			$plotxml .= $val->Render($chart);
			if( $val->Label != "" && count($this->Data) > count($chart->xAxisLabels)  )
				$chart->xAxisLabels[$i] = $val->Label;
			$i++;
		}

		if($chart->IsMultiPlot())
			$plotxml .= "</dataset>";

		return $plotxml;
	}
}
