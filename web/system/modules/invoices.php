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
 
/**
 * initializes the invoices module.
 * 
 * @return void
 */
function invoices_init()
{
	global $CONFIG;

	$CONFIG['class_path']['model'][] = __DIR__.'/invoices/';

	system_load_module("modules/zend.php");
	zend_load("Zend/Pdf.php");
	zend_load("pdf/Cell.php");
	zend_load("pdf/pdfdocument.class.php");
	
	if(!isset($GLOBALS['VAT_COUNTRIES']))
		WdfException::Raise("VAT_COUNTRIES not defined (invoices_init)");
}

/**
 * @internal Checks if all invoices requirements are loaded/configured
 */
function invoices_check_requirements()
{
	global $CONFIG;
	if( !isset($CONFIG['invoices']['logofile']) )
		WdfException::Raise("\$CONFIG['invoices']['logofile'] not defined");	
	if(!file_exists($CONFIG['invoices']['logofile']))
		WdfException::Raise("invoice logo (".$CONFIG['invoices']['logofile'].") not found");	
}

/**
 * Returns the standard logo.
 * 
 * This is defined in `cfg_get('invoices','logofile')`
 * @return string The path to the standard logo
 */
function invoiceStandardLogo()
{
	invoices_check_requirements();
	return $GLOBALS['CONFIG']['invoices']['logofile'];
}

/**
 * @shortcut <PdfDocument::RenderToFile>($filename)
 */
function writePdfToFile(PdfDocument $pdf_doc, $filename)
{
	$pdf_doc->RenderToFile($filename);
	return $filename;
}

/**
 * Checks if a vat number is valid
 * 
 * @param type $vat_number VAT number to be checked
 * @return bool true if valid, else false
 */
function check_vat_number($vat_number)
{
	$vat = strtoupper(str_replace(array(" ", "-", ",", ".", "/", "\\"), "", $vat_number));
	if (preg_match("/^(AT|BE|BG|CY|CZ|DE|DK|EE|EL|ES|FI|FR|GB|HU|IE|IT|LT|LU|LV|MT|NL|PL|PT|RO|SE|SI|SK)(.*)/i", $vat, $matches))
	{
		$country_code = strtoupper($matches[1]);
		$vat = $matches[2];
	}
	if( !isset($country_code) )
		return false;
	
	$regex = array(
		'AT'=>'/(U[0-9]{8})/i',
		'BE'=>'/(0[0-9]{9})/i',
		'BG'=>'/([0-9]{9,10})/i',
		'CY'=>'/([0-9]{8}[a-z])/i',
		'CZ'=>'/([0-9]{8}|[0-9]{9}|[0-9]{10})/i',
		'DE'=>'/([0-9]{9})/i',
		'DK'=>'/([0-9]{8})/i',
		'EE'=>'/([0-9]{9})/i',
		'EL'=>'/([0-9]{9})/i',
		'ES'=>'/([a-z][0-9]{8}|[0-9]{8}[a-z]|[a-z][0-9]{7}[a-z])/i',
		'FI'=>'/([0-9]{8})/i',
		'FR'=>'/([a-z0-9]{2}[0-9]{9})/i',
		'GB'=>'/([0-9]{9}|[0-9]{12}|GD[0-9]{3}|HA[0-9]{3})/i',
		'HU'=>'/([0-9]{8})/i',
		'IE'=>'/([0-9][a-z0-9\+\*][0-9]{5}[a-z])/i',
		'IT'=>'/([0-9]{11})/i',
		'LT'=>'/([0-9]{9}|[0-9]{12})/i',
		'LU'=>'/([0-9]{8})/i',
		'LV'=>'/([0-9]{11})/i',
		'MT'=>'/([0-9]{8})/i',
		'NL'=>'/([0-9]{9}B[0-9]{2})/i',
		'PL'=>'/([0-9]{10})/i',
		'PT'=>'/([0-9]{9})/i',
		'RO'=>'/([0-9]{2,10})/i',
		'SE'=>'/([0-9]{12})/i',
		'SI'=>'/([0-9]{8})/i',
		'SK'=>'/([0-9]{10})/i',
	);
	
	if( !isset($regex[$country_code]) )
		return false;
	
	if( !preg_match($regex[$country_code],$vat,$m) )
		return false;

	// only ask service is syntax-check is ok
	if( $m[1] == $vat )
	{
		try{
			$res = cache_get("vat_check_{$country_code}_{$vat}");
			if( !$res )
			{			
				$sc = new SoapClient("http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl");
				$test = $sc->checkVat(array('countryCode'=>$country_code,'vatNumber'=>$vat));
				if( !$test->valid )
					log_debug("VAT syntax ok, but SOAP says not",$vat_number,$country_code,$vat,$test);
				
				$res = $test->valid?"valid":"invalid";
				cache_set("vat_check_{$country_code}_{$vat}", $res);
			}
			elseif( $res != "valid" )
				log_debug("VAT syntax ok, but CACHE says not",$vat_number,$country_code,$vat);
			return $res == "valid";
		}catch(Exception $ex){ WdfException::Log($ex); }
		return true; // ignore service exceptions
	}
	return false;
}
