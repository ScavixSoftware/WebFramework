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
namespace ScavixWDF\JQueryUI;

use ScavixWDF\Localization\CultureInfo;
use ScavixWDF\Localization\DateTimeFormat;
use ScavixWDF\Localization\Localization;

default_string('BTN_DP_NEXT', 'Next');
default_string('BTN_DP_PREV', 'Prev');
default_string('TXT_DP_CLOSE', 'Close');
default_string('TXT_DP_CURRENT', 'Today');

/**
 * Wraps a jQueryUI DatePicker
 * 
 * See http://jqueryui.com/datepicker/
 */
class uiDatePicker extends uiControl
{
	protected $CultureInfo = false;
	protected $init_code = "datepicker";

	/**
	 * @param mixed $value The default value
	 * @param bool $inline If true will be displayed inline
	 */
	function __initialize($value = false, $inline = false)
	{		
		parent::__initialize($inline?"div":"input");
		$this->Options = array(
			'nextText' => 'BTN_DP_NEXT',
			'prevText' => 'BTN_DP_PREV',
			'buttonText' => '...',
			'closeText' => 'TXT_DP_CLOSE',
			'currentText' => 'TXT_DP_CURRENT',
		);
		if( $value )
		{
			if( !$inline )
			{
				$this->value = $value;
				$this->type = 'text';
			}
			else
				$this->Options['defaultDate'] = $value;
		}
	}

	/**
	 * @override
	 */
	function PreRender($args = array())
	{
		if( !$this->CultureInfo )
			$this->SetCulture(Localization::detectCulture());

		if( isset($this->value) )
			$this->value = get_class_simple($this)=="uiDatePicker"
				?$this->CultureInfo->FormatDate($this->value,DateTimeFormat::DF_SHORTDATE)
				:$this->CultureInfo->FormatDateTime($this->value);
		if( isset($this->Options['defaultDate']) )
			$this->Options['defaultDate'] = get_class_simple($this)=="uiDatePicker"
				?$this->CultureInfo->FormatDate($this->Options['defaultDate'],DateTimeFormat::DF_SHORTDATE)
				:$this->CultureInfo->FormatDateTime($this->Options['defaultDate']);

		$this->script("$('#{$this->id}').{$this->init_code}(".system_to_json($this->Options).");");
		parent::PreRender($args);
	}

	/**
	 * Sets the culture.
	 * 
	 * @param CultureInfo $cultureInfo The (new) culture
	 * @return uiDatePicker `$this`
	 */
	function SetCulture($cultureInfo)
	{
		while( $cultureInfo->IsNeutral() )
			$cultureInfo = $cultureInfo->DefaultRegion()->DefaultCulture();

		$this->CultureInfo = $cultureInfo;
		$format = $cultureInfo->DateTimeFormat->ShortDatePattern;
		$format = str_replace("d1", "d", $format);
		$format = str_replace("d2", "dd", $format);
		$format = str_replace("d3", "D", $format);
		$format = str_replace("d4", "DD", $format);
		
		$format = str_replace("M1", "m", $format);
		$format = str_replace("MM", "M2", $format);
		$format = str_replace("M2", "mm", $format);
		$format = str_replace("M3", "M", $format);
		$format = str_replace("M4", "MM", $format);
		$format = str_replace("M", "m", $format);

		$format = str_replace("yyyy", "y4", $format);
		$format = str_replace("y1", "y", $format);
		$format = str_replace("y2", "y", $format);
		$format = str_replace("y3", "yy", $format);
		$format = str_replace("y4", "yy", $format);
		
		$this->Options['dayNames'] = $cultureInfo->DateTimeFormat->DayNames;
		$this->Options['dayNamesMin'] = $cultureInfo->DateTimeFormat->ShortDayNames;
		$this->Options['dayNamesShort'] = $cultureInfo->DateTimeFormat->ShortDayNames;

		$this->Options['monthNames'] = $cultureInfo->DateTimeFormat->MonthNames;
		$this->Options['monthNamesShort'] = $cultureInfo->DateTimeFormat->ShortMonthNames;
		$this->Options['dateFormat'] = $format;
		
		return $this;
	}
}
