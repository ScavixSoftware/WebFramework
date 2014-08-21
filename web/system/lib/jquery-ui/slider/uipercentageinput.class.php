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
 * Double slider input control allowing you to input percent values.
 * 
 */
class uiPercentageInput extends uiControl
{
	/**
	 * @param float $defvalue Initial value
	 * @param string $onchange onChange JS code
	 * @param string $decimal_point Deciaml separator char
	 */
	function __initialize($defvalue=0, $onchange="",$decimal_point=',')
	{
		parent::__initialize("div");
		$this->InitFunctionName = false;

		$defvalue = floatval(str_replace(",",".",$defvalue));

		$e = floor($defvalue);
		$c = round(($defvalue-$e),2) * 100;

		$id = $this->id;
		$this->class = "currencyinput ui-widget-content ui-widget ui-corner-all";
		$this->css("border","1px solid transparent");
		$this->onmouseover = "$(this).css({border:''});";
		$this->onmouseout = "$(this).css({border:'1px solid transparent'});";

		$integer_place = new uiSlider();
		$integer_place->id = "{$id}_integer_place";
		$integer_place->range = 'min';
		$integer_place->min = 0;
		$integer_place->max = 100;
		$integer_place->value = $e;
		$integer_place->css("margin-bottom","8px");
		$integer_place->onslide  = "function(event, ui){ $('#{$id}_integer_place_value').text(ui.value); ";
		$integer_place->onslide .= "$('#{$id}_hidden').val( $('#{$id}_integer_place_value').text()+'.'+$('#{$id}_decimal_place_value').text() ).change(); }";
		$integer_place->onmouseover = "$('#{$id}_integer_place_value').css({color:'red'});";
		$integer_place->onmouseout = "$('#{$id}_integer_place_value').css({color:'black'});";

		$decimal_place = new uiSlider();
		$decimal_place->id = "{$id}_decimal_place";
		$decimal_place->range = 'min';
		$decimal_place->min = 0;
		$decimal_place->max = 99;
		$decimal_place->value = $c;
		$decimal_place->onslide  = "function(event, ui){ $('#{$id}_decimal_place_value').text(ui.value<10?'0'+ui.value:ui.value); ";
		$decimal_place->onslide .= "$('#{$id}_hidden').val( $('#{$id}_integer_place_value').text()+'.'+$('#{$id}_decimal_place_value').text() ).change(); }";
		$decimal_place->onmouseover = "$('#{$id}_decimal_place_value').css({color:'red'});";
		$decimal_place->onmouseout = "$('#{$id}_decimal_place_value').css({color:'black'});";

		$container = new Control("div");
		$container->class = "container";
		$container->content($integer_place);
		$container->content($decimal_place);

		$value = new Control("div");
		$value->class = "value";

		$integer_place_value = new Control("div");
		$integer_place_value->id = "{$id}_integer_place_value";
		$integer_place_value->css("float","left");
		$integer_place_value->content($e);

		$decimal_place_value = new Control("div");
		$decimal_place_value->id = "{$id}_decimal_place_value";
		$decimal_place_value->css("float","left");
		$decimal_place_value->content($c<9?"0$c":$c);


		$value->content($integer_place_value);
		$value->content("<div style='float:left'>$decimal_point</div>");
		$value->content($decimal_place_value);
		$value->content("<div style='float:left'>%</div>");
		
		$this->content($container);
		$this->content($value);
		$this->content("<input type='hidden' id='{$id}_hidden' name='{$id}' value='$defvalue' onchange='$onchange'/>");
		$this->content("<br style='clear:both; line-height:0'/>");
	}
}
