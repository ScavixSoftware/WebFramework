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
*  @attribute[Resource('FusionCharts.js')]
 */
class FusionChart extends Control
{
	var $Width;
	var $Height;
	var $Align = "left";
	protected $Plots = array();
	protected $Trendlines = array();
	protected $LabelFormatFunction = false;
	var $Title;
	var $Type;
	var $BgColor = "ffffff";
	var $NumberSuffix = "%E2%82%AC";
	var $ShowLegend = "1";
	var $ShowValues = "0";
	var $canvasBorderThickness  = "1";
	var $ShowPercentValues = "0";
	var $RotateValues='0';
	var $PlaceValuesInside='0';
	var $slantLabels = "1";
	var $LabelDisplay = "";
	var $LegendPosition='BOTTOM';
	var $EnableSmartLabels='0';
	var $ShowZeroPies = "1";
	var $ShowLabels = "1";
	var $connectNullData = "0";
	var $ShowAsPercent = false;
	var $ChartLeftMargin = "";
	var $ChartRightMargin = "";
	var $ChartTopMargin = "";
	var $ChartBottomMargin = "";
	var $ShowSumInLegend = false;
	var $yAxisMaxValue = null;
	var $drawAnchors = true;
	var $anchorAlpha  = 100;
	var $xAxisLabels;
	var $legendCaption ;
	var $anchorRadius ;
	var $forceDecimals;
	var $adjustDiv;
	var $xAxisName = "";
	var $yAxisName = "";
	var $rotateLabels = "";
	var $yAxisValueDecimals;
	var $disableDefaultAnimation = false;
	var $paletteColors = false;
	var $plotSpacePercent = null;
	var $showPlotBorder = null;
	var $decimals = 0;
	var $showYAxisValues = 1;
	//private $arPlotColors = array("e59001", "658966", "B8D1BB", "E02E31", "FFFFFF", "EAA42F", "000000");
	protected $arPlotColors = array("CEF1FE", "57D2FF", "6ace18", "ffe788", "FFFF00", "000000", "1d43d4", "58b35b", "eac33b", "ee3e67","4895ec","0066ff","ff4be3","ff0003","a2ff1b","ffffff","642fbd","ff7c24","a97715");

	private $_arLabelCharsFind = array('&amp;', '%;', "\n", "\r");
	private $_arLabelCharsReplace = array('%26', '%25', '', "\\n");

	function __initialize($type = "MSColumnLine3D", $title = "", $width = 1000, $height = 450, $plotColors=false)
	{
		if($type === null)
			$type = "MSColumnLine3D";

		parent::__initialize("div");

		if( $plotColors )
			$this->arPlotColors = $plotColors;
		$this->style = 'display:block; float:left';
		$this->content("The chart is currently loading. Please stand by...<br/>");
		$this->content('FusionCharts needs Adobe Flash Player to run. If you\'re unable to see the chart here, it means that your browser does not seem to have the Flash Player Installed. You can downloaded it <a href="http://www.adobe.com/products/flashplayer/" target="_blank"><u>here</u></a> for free.');

		$this->Type = $type;
		$this->Title = $title;
		$this->Width = $width;
		$this->Height = $height;

		$this->Prepare();
	}

	function Prepare($async=false, &$handler=false, $method=false)
	{
		global $CONFIG;

		if( $async )
		{
			if( !$handler || !$method )
				WdfException::Raise("Data handler and method needed for asynchronous charts");
			$data_url = buildQuery($handler->id,$method);
		}
		else
		{
			if( $handler && $method )
				$data = $handler->$method($this);
//			else
//				$data = $this->CleanupXML($this->RenderXML());
		}

		$str_data = ($this->Title)?$this->Title:'';
		$swfurl = $CONFIG['system']['system_uri'].appendVersion("/modules/charting/charts/".$this->Type.".swf");
		$swfurl .= "?PBarLoadingText=".urlencode($str_data.":\n".getString(tds("TXT_CHART_LOADING",'loading')));
		$swfurl .= "&ChartNoDataText=".urlencode($str_data.":\n".getString(tds("TXT_NO_DATA_FOUND",'no data found')));
		$swfurl .= "&InvalidXMLText=".urlencode($str_data.":\n".getString(tds("TXT_CHART_INVALID_XML",'invalid XML')));

		$settings = array(
			'swfurl' => $swfurl,
			'chartid' => $this->id,
			'width' => $this->Width,
			'height' => $this->Height,
			'debug' => 0
		);
		if( isset($data) )
			$settings['data'] = $data;
		elseif( isset($data_url) )
			$settings['dataurl'] = $data_url;

		$this->_script = array();
		$this->script("initFusionChart(".json_encode($settings).");");
	}

	function &AddPlot($title = "", $type = "")
	{
		if($type == "")
			$type = $this->Type;
		$plot = new Plot($type, $title);
		$this->Plots[] = $plot;
		return $plot;
	}

	function &AddTrendLine($displayName,$startValue,$endValue=false)
	{
		$tl = new Trendline($displayName,$startValue,$endValue);
		$this->Trendlines[] = $tl;
		return $tl;
	}

	function IsMultiPlot()
	{
		return substr($this->Type,0,2) == 'MS' ||
			count($this->Plots) > 1 ||
			$this->Type == "StackedBar2D" ||
			$this->Type == "StackedColumn2D" ||
			$this->Type == "StackedArea2D"	 ||
			$this->Type == "StackedBar3D" ||
			$this->Type == "StackedColumn3D";
	}

	function IsAllowedObject($type)
	{

		if( $this->Type == 'StackedBar2D' && $type == "ANCHORS" )
			return false;
		if( substr($this->Type,0,2) == 'MS')
			if( substr($this->Type,2,  strlen($this->Type)) == 'StackedColumn2D' && $type == "ANCHORS" )
				return false;
		else
			if( $this->Type == 'StackedColumn2D' && $type == "ANCHORS" )
				return false;
		if( $this->Type == 'Column2D' && $type == "ANCHORS" )
			return false;
		if( $this->Type == 'Pie2D' && $type == "ANCHORS" )
			return false;
		if( $this->Type == 'Pie2D' && $type == "DATAVALUES" )
			return false;
		if( $this->Type == 'Pie3D' && $type == "ANCHORS" )
			return false;
		if( $this->Type == 'Pie3D' && $type == "DATAVALUES" )
			return false;
		return true;
	}

	function FormatLabel($label)
	{
		if( $this->LabelFormatFunction )
			return call_user_func($this->LabelFormatFunction,$label);
			//return $this->LabelFormatFunction($label);
		return $label;
	}

	function CleanupXML($xml)
	{
//		$xml = str_replace("			", " ", $xml);
//		$xml = str_replace("   ", " ", $xml);
//		$xml = str_replace("   ", " ", $xml);
//		$xml = str_replace("\r\n", "", $xml);
//		$xml = str_replace("\n", "", $xml);
//		$xml = str_replace("\t", "", $xml);
		return $xml;
	}

	public function RenderXML()
	{
		// fill missing data in plots with zero values
		$plcnt = count($this->Plots);
		if($plcnt == 0)
			return "";
		for($i=0; $i<$plcnt; $i++)
		{
			$dcnt = count($this->Plots[$i]->Data);
			for($k=0; $k<$dcnt; $k++)
			{
				for($j=0; $j<$plcnt; $j++)
				{
					if( $i==$j ) continue;

					if( !$this->Plots[$j]->hasValue($this->Plots[$i]->Data[$k]->Label)
						&& (
							!isset($this->Plots[$j]->Data[$k]) ||
							$this->Plots[$j]->Data[$k]->Label != $this->Plots[$i]->Data[$k]->Label
						) )
					{
						//log_debug($this->Plots[$j]->Title.": Inserting zero value ".$this->Plots[$i]->Data[$k]->Label);
//						if( strpos($this->Type,"Bar") === false && strpos($this->Type,"Column") === false )
//							$this->Plots[$j]->Insert(0,$this->Plots[$i]->Data[$k]->Label,0);
//						else
							$this->Plots[$j]->Insert("",$this->Plots[$i]->Data[$k]->Label,0);
//						$v = $this->Plots[$j]->Insert(0,$k);
//						$v->Label = $this->Plots[$i]->Data[$k]->Label;
					}
				}
			}
		}

		// recalc values if chart should fill to 100%
		if( $this->ShowAsPercent && count($this->Plots) > 0 )
		{
			//log_debug(get_class($this)." showing as percent");
			foreach( $this->Plots[0]->GetLabels() as $lab )
			{
				$sum = 0;
//				log_debug("Processing '$lab'...");
				foreach( $this->Plots as &$plot )
				{
					$tmp_val = $plot->getValue($lab);
					if( $tmp_val != "" )
						$sum += $plot->getValue($lab);
				}
				foreach( $this->Plots as &$plot )
				{
					$plot->PercentValue($lab,$sum);
				}
			}
			$this->NumberSuffix = "%25";
		}
		//
		$ret = "<chart ";
		if($this->paletteColors)
			$ret .= "palette='2' paletteColors='".$this->paletteColors."' ";
		$ret .= "showPercentValues='".$this->ShowPercentValues."' ";
		$ret .= "numberSuffix='".$this->NumberSuffix."' ";
		$ret .= "placeValuesInside='".$this->PlaceValuesInside."' ";
		$ret .= "decimalSeparator=',' ";
		$ret .= "canvasBorderThickness='".$this->canvasBorderThickness."' ";
		$ret .= "thousandSeparator='.' ";
		$ret .= "decimals='".$this->decimals."' ";
		$ret .= "formatNumberScale='0' ";
		$ret .= "yAxisValuesPadding='10' ";
		$ret .= "bgColor='".$this->BgColor."' ";
		$ret .= "borderThickness='0' ";
		$ret .= "borderColor='ffffff' ";
		$ret .= "connectNullData='".$this->connectNullData."' ";
		$ret .= "canvasBorderColor='a0a0a0' ";
		$ret .= "imageSave='1' ";
		$ret .= "exportEnabled='1' exportHandler='".$this->BuildImageSaveUrl()."' exportAtClient='0' exportAction='download' exportFileName='Chart' ";
		$ret .= "lineDashGap='6' ";
		$ret .= "utCnvBaseFont='Verdana,Arial' ";
		$ret .= "outCnvBaseFontSize='11' ";
		$ret .= "enableSmartLabels='".$this->EnableSmartLabels."' ";
		$ret .= "showZeroPies='".$this->ShowZeroPies."' ";
		$ret .= "showCanvasBg='0' ";
		$ret .= "bgAlpha='0' ";
		$ret .= "canvasBgColor='ffffff' ";
		
		if($this->disableDefaultAnimation)
			$ret .= "defaultAnimation='0' ";
		if(!is_null($this->yAxisMaxValue))
			$ret .= "yAxisMaxValue='".$this->yAxisMaxValue."' ";
		if($this->ChartLeftMargin != "")
			$ret .= "chartLeftMargin='".$this->ChartLeftMargin."' ";
		if($this->ChartRightMargin != "")
			$ret .= "chartRightMargin='".$this->ChartRightMargin."' ";
		if($this->ChartTopMargin != "")
			$ret .= "chartTopMargin='".$this->ChartTopMargin."' ";
		if($this->ChartBottomMargin != "")
			$ret .= "chartBottomMargin='".$this->ChartBottomMargin."' ";
		if(isset($this->forceDecimals))
			$ret .= "forceDecimals='".$this->forceDecimals."' ";
		if(isset($this->yAxisValueDecimals))
			$ret .= "yAxisValueDecimals='".$this->yAxisValueDecimals."' ";
		if(isset($this->adjustDiv))
			$ret .= "adjustDiv='".$this->adjustDiv."' ";
		if(!empty($this->xAxisName))
			$ret.="xAxisName='".$this->xAxisName."' ";
		if(!empty($this->yAxisName))
			$ret.="yAxisName='".$this->yAxisName."' ";
		if( isset($this->plotSpacePercent))
			$ret.="plotSpacePercent='".$this->plotSpacePercent."' ";
		if( isset($this->showPlotBorder))
			$ret.="showPlotBorder='".$this->showPlotBorder."' ";
		if(!$this->showYAxisValues )
			$ret .= "showYAxisValues='0' ";

		//some chart attributes wont take affect if a specific attribute is missing/or not is missing but has the wrong value
		if(strlen($this->ShowLabels) != 0)
		{
			$ret .= "showLabels='".$this->ShowLabels."' ";
			if($this->ShowLabels == "1")
			{
				if(!empty($this->LabelDisplay))
					$ret .= "labelDisplay='".$this->LabelDisplay."' ";
				if(strlen($this->rotateLabels) != 0)
					$ret .= "rotateLabels='".$this->rotateLabels."' ";
				if(strlen($this->slantLabels)!= 0)
					$ret .= "slantLabels ='".$this->slantLabels."' ";
			}
		}
		if(strlen($this->ShowValues)!= 0)
		{
			$ret .= "showValues='".$this->ShowValues."' ";
			if($this->ShowValues == "1")
			{
				if(strlen($this->RotateValues) != 0)
					$ret .= "rotateValues='".$this->RotateValues."' ";
			}
		}
		if(strlen($this->ShowLegend) != 0)
		{
			if($this->ShowLegend == "1")
			{
				$showlegend = stripos($this->Type,"pie") !== false;
				if( !$showlegend )
				{
					foreach($this->Plots as &$plot)
					{
						if($plot->Title != "")
						{
							$showlegend = true;
							break;
						}
					}
				}
				$ret.= "showLegend='".($showlegend ? "1" : "0")."' ";
				if($showlegend)
				{
					//  //put any legend propertys in here
					if(!empty($this->LegendPosition))
						$ret .= "legendPosition='".$this->LegendPosition."' ";
					if(isset($this->legendCaption))
						$ret .= "legendCaption='".$this->legendCaption."' ";
				}
			}
			else
				$ret.= "showLegend='".$this->ShowLegend."' ";
		}
		$ret .= "drawAnchors='".$this->drawAnchors."' ";
		if($this->drawAnchors)
			$ret .= "anchorAlpha='".$this->anchorAlpha."' ";
		

		$plotxml = "";
		$this->xAxisLabels = array();
		$plotindex = 0;
		$prev_group = "";
		foreach($this->Plots as &$plot)
		{
			if( $plot->Color == "" && isset($this->arPlotColors[$plotindex]) )
				$plot->Color = $this->arPlotColors[$plotindex];
			$plotindex++;
			
            if($this->ShowSumInLegend && !$this->ShowAsPercent)
                $plot->ShowSumInLegend = true;
			if($plot->hasGroup())
			{
				if($prev_group != $plot->plot_group)
				{
					if(!empty($prev_group))
						$plotxml .= "</dataset>";
					$plotxml .= "<dataset group='".$plot->plot_group."'>";
					$prev_group = $plot->plot_group;
				}
			}
			else
				if(!empty($prev_group))
					$plotxml .= "</dataset>";
			$plotxml .= $plot->Render($this);
		}

		if($this->IsMultiPlot())
		{
			if(!empty($prev_group))
				if($this->Plots[count($this->Plots)-1]->hasGroup())
					$plotxml .= "</dataset>";
		}

		if( count($this->xAxisLabels) > 20 )
			$ret .= "labelStep='7' ";

		if($this->Title != "")
			$ret .= "caption='".$this->Title."' ";
		if(substr($this->Type, 0, 3) == "Pie")
			$ret .= " pieYScale='60' startingAngle='-180'";
		$ret .= ">";
		$ret .= $plotxml;

		if($this->IsMultiPlot())
		{
			//$ret .= "<dataset></dataset>";

		  	$ret .= "<categories>";
			$axcnt = count($this->xAxisLabels);
		  	for($i = 0; $i < $axcnt; $i++)
			{
				$ret .= "<category label='".(isset($this->xAxisLabels[$i])?$this->xAxisLabels[$i]:'')."' />";
			}
			$ret .= "</categories>";
		}

		if( count($this->Trendlines) > 0 )
		{
			$trendlines = array();
			foreach( $this->Trendlines as $tl )
			{
				$trendlines[] = $tl->Render();
			}
			$ret .= "<trendLines>".implode("",$trendlines)."</trendLines>";
		}

		$ret .= "<styles>";
		$ret .= "<definition>";
		$ret .= "<style name='myToolTipFont' type='font' font='Verdana, Arial' size='12' />";
		$ret .= "<style name='myAxisFont' type='font' font='Verdana, Arial' size='11' />";
		$ret .= "<style name='myBevel' type='bevel' distance='4' />";
		$ret .= "</definition>";
		$ret .= "<application>";
		$ret .= "<apply toObject='ToolTip' styles='myToolTipFont' />";
		$ret .= "<apply toObject='DataLabels' styles='myAxisFont' />";
	    if( $this->IsAllowedObject('DATAVALUES') )
			$ret .= "<apply toObject='DataValues' styles='myAxisFont' />";
	    if( $this->IsAllowedObject('ANCHORS') )
			$ret .= "<apply toObject='ANCHORS' styles='myBevel' />";
	    $ret .= "</application></styles>";
		$ret .= "</chart>";
//log_debug($ret);
		return $ret;
	}

	private function BuildImageSaveUrl()
	{
		global $CONFIG;
		return $CONFIG['system']['system_uri']."modules/charting/FCExporter.php";
	}

	public function PrepareLabel($label)
	{
		$label = utf8_decode(str_replace("'", "%26apos;", $label));
		$label = str_replace("&euro;", "%E2%82%AC", $label);   // if already html input
		$label = htmlentities($label);
		$label = str_replace($this->_arLabelCharsFind, $this->_arLabelCharsReplace, $label);

	 	return $label;
	}
}
