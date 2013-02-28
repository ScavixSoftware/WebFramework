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
 * This is a checkbox allowing to choose if timezone information shall be added to date/time formats.
 * 
 * Showns an inline sample of how this will look like
 * @attribute[Resource('locale_settings.js')]
 */
class TimeFormatEx extends Control
{
	var $culture_code;
	var $timezone;
	
	/**
	 * @param string $culture_code Code of current <CultureInfo>
	 * @param mixed $date_format Chosen date format
	 * @param mixed $time_format Chosen time format
	 * @param string $timezone Currently chosen timezone
	 * @param bool $append_timezone If true timezome will be appended
	 */
	function __initialize($culture_code, $date_format, $time_format, $timezone=false, $append_timezone=false)
	{
		parent::__initialize();
		$this->script("Locale_Settings_Init();");
		store_object($this);
		
		if( !$timezone )
			$timezone = Localization::getTimeZone();
		$this->timezone = $timezone;
		
		$this->culture_code = $culture_code;
		$txt = $this->_sample(false,$date_format,$time_format);
		if( $append_timezone ) $txt .= " $timezone";
		$sample = new Control('span');
		$sample->append("($txt)")->css('color','gray');
		
		$cb = new CheckBox();
		$cb->setData('role', 'timeformatex')->setData('controller', buildQuery($this->id));
		$cb->value = 1;
		if( $append_timezone )
			$cb->checked = "checked";
		$lab = $cb->CreateLabel(tds("TXT_APPEND_TIMEZONE","Append timezone")." ");
		$lab->content($sample);
		
		$this->append($cb)->append($lab);
	}
	
	private function _sample($json_dtf=false,$date_format=false,$time_format=false)
	{
		$value = time();
		if( $json_dtf )
			list($date_format,$time_format) = json_decode($json_dtf);
		
		$ci = Localization::getCultureInfo($this->culture_code);
		if(!$ci)
			return "";
		$ci->TimeZone = $this->timezone;
		$value = $ci->GetTimezoneDate($value);
		$dtf = $ci->DateTimeFormat;
		return $dtf->Format($value, $date_format)." ".$dtf->Format($value, $time_format);
	}
	
	/**
	 * @internal Will create a new sample based on changed settings.
	 * 
	 * @attribute[RequestParam('append_timezone','bool')]
	 * @attribute[RequestParam('timezone','string')]
	 * @attribute[RequestParam('dtf','string')]
	 * @attribute[RequestParam('culture_code','string')]
	 */
	public function RefreshSample($append_timezone, $timezone, $dtf, $culture_code)
	{
		$this->culture_code = $culture_code;
		$this->timezone = $timezone;
		$txt = $this->_sample($dtf);
		if( $append_timezone ) $txt .= " $timezone";
		$sample = new Control('span');
		$sample->append("($txt)")->css('color','gray');
		return AjaxResponse::Renderable($sample);
	}
}

