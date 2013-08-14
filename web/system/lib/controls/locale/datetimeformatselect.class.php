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
namespace ScavixWDF\Controls\Locale;

use ScavixWDF\Base\AjaxResponse;
use ScavixWDF\Controls\Form\Select;
use ScavixWDF\Localization\DateTimeFormat;
use ScavixWDF\Localization\Localization;
 
/**
 * Selector for datetime formats.
 * 
 * @attribute[Resource('locale_settings.js')]
 */
class DateTimeFormatSelect extends Select
{
	var $culture_code;
	
	/**
	 * @param string $culture_code Culture code (see <CultureInfo>)
	 * @param mixed $selected_date_format The currently selected date format or false
	 * @param mixed $selected_time_format The currently selected time format or false
	 * @param string $timezone Timezone identifier or false
	 */
	function __initialize($culture_code, $selected_date_format=false, $selected_time_format=false, $timezone=false)
	{
		parent::__initialize();
		$this->script("Locale_Settings_Init();");
		$this->setData('role', 'datetimeformat');
		$this->setData('controller', buildQuery($this->id));
		$this->culture_code = $culture_code;
		
		if( $selected_date_format || $selected_time_format )
			$this->SetCurrentValue(
				json_encode( array( 
					$selected_date_format?$selected_date_format:false,
					$selected_time_format?$selected_time_format:false) )
			);
		
		$df = array(DateTimeFormat::DF_LONGDATE, DateTimeFormat::DF_SHORTDATE, DateTimeFormat::DF_MONTHDAY, DateTimeFormat::DF_YEARMONTH);
		$tf = array(DateTimeFormat::DF_LONGTIME, DateTimeFormat::DF_SHORTTIME);
		
		$value = time();
		$ci = Localization::getCultureInfo($culture_code);
		if( $timezone )
		{
			$ci->SetTimezone($timezone);
			$value = $ci->GetTimezoneDate($value);
		}
		$dtf = $ci->DateTimeFormat;
		foreach( $df as $d )
		{
			foreach( $tf as $t )
			{
				$sv = $dtf->Format($value, $d)." ".$dtf->Format($value, $t);
				$this->AddOption(json_encode(array($d,$t)), $sv);
			}
		}
	}
	
	/**
	 * Returns a list of option elements.
	 * 
	 * Called via AJAX to dynamically update the control.
	 * @attribute[RequestParam('culture_code','string')]
	 * @param string $culture_code Selected culture code
	 * @return <AjaxResponse::Text> Html string with options
	 */
	public function ListOptions($culture_code)
	{
		$this->culture_code = $culture_code;
		
		$df = array(DateTimeFormat::DF_LONGDATE, DateTimeFormat::DF_SHORTDATE, DateTimeFormat::DF_MONTHDAY, DateTimeFormat::DF_YEARMONTH);
		$tf = array(DateTimeFormat::DF_LONGTIME, DateTimeFormat::DF_SHORTTIME);
		
		$value = time();
		$ci = Localization::getCultureInfo($culture_code);
		if(!$ci)
			$ci = Localization::getCultureInfo('en-US');
		$dtf = $ci->DateTimeFormat;
		foreach( $df as $d )
		{
			foreach( $tf as $t )
			{
				$sv = $dtf->Format($value, $d)." ".$dtf->Format($value, $t);
				$res[] = "<option value='".json_encode(array($d,$t))."'>$sv</option>";
			}
		}
		return AjaxResponse::Text(implode("\n",$res));
	}
}

