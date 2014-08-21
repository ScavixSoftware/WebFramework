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
namespace ScavixWDF\JQueryUI\Slider;

use ScavixWDF\Base\Control;
use ScavixWDF\JQueryUI\uiControl;

/**
 * Double slider input control allowing you to input time values.
 * 
 */
class uiTimeInput extends uiControl
{
	/**
	 * @param int $defvalue Initial value (seconds)
	 * @param string $onchange onChange JS code
	 */
	function __initialize($defvalue=0, $onchange = "")
	{
		parent::__initialize("div");
		$this->InitFunctionName = false;

		$defvalue = intval($defvalue);

		$m = floor($defvalue / 60);
		$s = $defvalue % 60;

		$id = $this->id;
		$this->class = "timeinput ui-widget-content ui-widget ui-corner-all";
		$this->css("border","1px solid transparent");
		$this->onmouseover = "$(this).css({border:''});";
		$this->onmouseout = "$(this).css({border:'1px solid transparent'});";

		$minutes = new uiSlider();
		$minutes->id = "{$id}_euro";
		$minutes->range = 'min';
		$minutes->min = 0;
		$minutes->max = 120;
		$minutes->value = $m;
		$minutes->css("margin-bottom","8px");
		$minutes->onslide  = "function(event, ui){ $('#{$id}_euro_value').text(ui.value<10?'0'+ui.value:ui.value);";
		$minutes->onslide .= "$('#{$id}_hidden').val( parseInt($('#{$id}_euro_value').text())*60 + parseInt($('#{$id}_cent_value').text()) ).change(); }";
		$minutes->onmouseover = "$('#{$id}_euro_value').css({color:'red'});";
		$minutes->onmouseout = "$('#{$id}_euro_value').css({color:'black'});";

		$seconds = new uiSlider();
		$seconds->id = "{$id}_cent";
		$seconds->range = 'min';
		$seconds->min = 0;
		$seconds->max = 59;
		$seconds->value = $s;
		$seconds->onslide = "function(event, ui){ $('#{$id}_cent_value').text(ui.value<10?'0'+ui.value:ui.value); ";
		$seconds->onslide .= "$('#{$id}_hidden').val( parseInt($('#{$id}_euro_value').text())*60 + parseInt($('#{$id}_cent_value').text()) ).change(); }";
		$seconds->onmouseover = "$('#{$id}_cent_value').css({color:'red'});";
		$seconds->onmouseout = "$('#{$id}_cent_value').css({color:'black'});";

		$container = new Control("div");
		$container->class = "container";
		$container->content($minutes);
		$container->content($seconds);

		$value = new Control("div");
		$value->class = "value";

		$minuteval = new Control("div");
		$minuteval->id = "{$id}_euro_value";
		$minuteval->css("float","left");
		$minuteval->content($m<9?"0$m":$m);

		$secval = new Control("div");
		$secval->id = "{$id}_cent_value";
		$secval->css("float","left");
		$secval->content($s<9?"0$s":$s);

		$value->content($minuteval);
		$value->content("<div style='float:left'>:</div>");
		$value->content($secval);
		
		$this->content($container);
		$this->content($value);
		$this->content("<input type='hidden' id='{$id}_hidden' name='{$id}' value='$defvalue' onchange='$onchange'/>");
		$this->content("<br style='clear:both; line-height:0'/>");
	}
}
