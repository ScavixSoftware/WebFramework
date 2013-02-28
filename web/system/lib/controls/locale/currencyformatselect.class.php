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
 * Selector for currency formats.
 * 
 * @attribute[Resource('locale_settings.js')]
 */
class CurrencyFormatSelect extends Select
{
	/**
	 * @param string $currency_code A valid currency code
	 * @param mixed $selected_format The currently selected format
	 */
	function __initialize($currency_code, $selected_format=false)
	{
		parent::__initialize();
		$this->script("Locale_Settings_Init();");
		$this->setData('role', 'currenyformat');
		$this->setData('controller', buildQuery($this->id));
		
		if( $selected_format )
			$this->SetCurrentValue( $selected_format );
		$samples = $this->getCurrencySamples($currency_code,1234.56,true);
		foreach($samples as $code => $label)
			$this->AddOption($code, $label);
	}
	
	/**
	 * Returns a list of option elements.
	 * 
	 * Called via AJAX to dynamically update the control.
	 * @attribute[RequestParam('currency','string')]
	 * @param string $currency Valid currency string
	 * @return <AjaxResponse::Text> Html string with options
	 */
	public function ListOptions($currency)
	{
		$samples = $this->getCurrencySamples($currency,1234.56,true);
		$res = array();
		foreach($samples as $code=>$item)
			$res[] = "<option value='$code'>$item</option>";
		return AjaxResponse::Text(implode("\n",$res));
	}
	
	private function getCurrencySamples($currency_code, $sample_value, $unique_values = false)
	{
		$cultures = internal_getCulturesByCurrency($currency_code);

		$res = array();
		foreach( $cultures as $culture_code )
		{
			$ci = self::getCultureInfo($culture_code);
			if( !$ci )
				continue;

			$res[$culture_code] = $ci->FormatCurrency($sample_value);
		}
		if( $unique_values )
			return array_unique($res);
		return $res;
	}
}

