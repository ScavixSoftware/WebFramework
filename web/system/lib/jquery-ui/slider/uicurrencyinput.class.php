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
 * Double slider input control allowing you to input currency values.
 * 
 */
class uiCurrencyInput extends uiControl
{
	/**
	 * @param float $defvalue Initial value
	 * @param string $onchange onChange JS code
	 */
	function __initialize($defvalue=0, $onchange="")
	{
		parent::__initialize("div");

		$defvalue = floatval(str_replace(",",".",$defvalue));

		$e = floor($defvalue);
		$c = round(($defvalue-$e),2) * 100;
		log_debug("CurrencyInput($id): $defvalue $e $c");

		$id = $this->id;
		$this->class = "currencyinput ui-widget-content ui-widget ui-corner-all";
		$this->css("border","1px solid transparent");
		$this->onmouseover = "$(this).css({border:''});";
		$this->onmouseout = "$(this).css({border:'1px solid transparent'});";

		$euro = new uiSlider();
		$euro->id = "{$id}_euro";
		$euro->range = 'min';
		$euro->min = 0;
		$euro->max = 100;
		$euro->value = $e;
		$euro->css("margin-bottom","8px");
		$euro->onslide  = "function(event, ui){ $('#{$id}_euro_value').text(ui.value); ";
		$euro->onslide .= "$('#{$id}_hidden').val( $('#{$id}_euro_value').text()+'.'+$('#{$id}_cent_value').text() ).change(); }";
		$euro->onmouseover = "$('#{$id}_euro_value').css({color:'red'});";
		$euro->onmouseout = "$('#{$id}_euro_value').css({color:'black'});";

		$cent = new uiSlider();
		$cent->id = "{$id}_cent";
		$cent->range = 'min';
		$cent->min = 0;
		$cent->max = 99;
		$cent->value = $c;
		$cent->onslide  = "function(event, ui){ $('#{$id}_cent_value').text(ui.value<10?'0'+ui.value:ui.value); ";
		$cent->onslide .= "$('#{$id}_hidden').val( $('#{$id}_euro_value').text()+'.'+$('#{$id}_cent_value').text() ).change(); }";
		$cent->onmouseover = "$('#{$id}_cent_value').css({color:'red'});";
		$cent->onmouseout = "$('#{$id}_cent_value').css({color:'black'});";

		$container = new Control("div");
		$container->class = "container";
		$container->content($euro);
		$container->content($cent);

		$value = new Control("div");
		$value->class = "value";

		$euroval = new Control("div");
		$euroval->id = "{$id}_euro_value";
		$euroval->css("float","left");
		$euroval->content($e);

		$centval = new Control("div");
		$centval->id = "{$id}_cent_value";
		$centval->css("float","left");
		$centval->content($c<9?"0$c":$c);

		$value->content("<div style='float:left'>€</div>");
		$value->content($euroval);
		$value->content("<div style='float:left'>,</div>");
		$value->content($centval);
		
		$this->content($container);
		$this->content($value);
		$this->content("<input type='hidden' id='{$id}_hidden' name='{$id}' value='$defvalue' onchange='$onchange'/>");
		$this->content("<br style='clear:both; line-height:0'/>");
	}
}
