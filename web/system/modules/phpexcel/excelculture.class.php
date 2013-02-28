<?php

/**
 * Overrides some methods for Excel compatibility.
 * 
 */
class ExcelCulture extends CultureInfo
{
	private static $FORMAT_MAP = array();
	
	static function FromCode($code)
	{
		$res = new ExcelCulture();
		$ci = Localization::getCultureInfo($code);
		
		foreach( get_object_vars($ci) as $prop=>$value )
			$res->$prop = $value;
	
		return $res;
	}
	
	function FormatDate($date, $format_id = false)
	{
		$date = $this->_ensureTimeStamp($date);
		return PHPExcel_Shared_Date::FormattedPHPToExcel(date("Y",$date),date("m",$date),date("d",$date));
	}
	
	function FormatTime($date, $format_id = false)
	{
		$date = $this->_ensureTimeStamp($date);
		return fmod(PHPExcel_Shared_Date::PHPToExcel($date),1);
	}
	
	function FormatDateTime($date, $format_id = false)
	{
		$date = $this->_ensureTimeStamp($date);
		return PHPExcel_Shared_Date::PHPToExcel($date);
	}
	
	function FormatInt($number)
	{
		return intval($number);
	}
	
	function FormatNumber($number, $decimals = false, $use_plain = false)
	{
		return doubleval($number);
	}
	
	function FormatCurrency($amount, $use_plain = false, $only_value = false, $escape_group_separator = true)
	{
		return doubleval($amount);
	}
	
	function GetExcelFormat($cellformat)
	{
		$f = strtolower($cellformat->GetFormat());
		if( isset(self::$FORMAT_MAP[$f]) )
			return self::$FORMAT_MAP[$f];
		switch( $f )
		{
			case 'time':
			case 'duration':
				self::$FORMAT_MAP[$f] = PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4;
				break;
			case 'date':
				self::$FORMAT_MAP[$f] = PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY;
				break;
			case 'datetime':
				self::$FORMAT_MAP[$f] = PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME;
				break;
			case 'currency':
				$res = '#'.
					$this->CurrencyFormat->GroupSeparator.'##0'.
					$this->CurrencyFormat->DecimalSeparator.
					str_repeat('0', $this->CurrencyFormat->DecimalDigits);
				log_debug("CurrencyFormat -> ".$res);
				$pos = str_replace('%v', $res, $this->CurrencyFormat->PositiveFormat);
				$neg = str_replace('%v', $res, $this->CurrencyFormat->NegativeFormat);
				self::$FORMAT_MAP[$f] = "$pos;$neg";
				break;
			case 'int':
			case 'integer':
				$res = '#'.
					$this->NumberFormat->GroupSeparator.'##0';
				log_debug("IntegerFormat -> ".$res);
				$pos = $res;
				$neg = str_replace('%v', $res, $this->NumberFormat->NegativeFormat);
				self::$FORMAT_MAP[$f] = "$pos;$neg";
				break;
			case 'float':
			case 'double':
				$res = '#'.
					$this->NumberFormat->GroupSeparator.'##0'.
					$this->NumberFormat->DecimalSeparator.
					str_repeat('0', $this->NumberFormat->DecimalDigits);
				log_debug("DoubleFormat -> ".$res);
				$pos = $res;
				$neg = str_replace('%v', $res, $this->NumberFormat->NegativeFormat);
				self::$FORMAT_MAP[$f] = "$pos;$neg";
				break;
			default:
				log_warn("Unknown column format: $f");
				self::$FORMAT_MAP[$f] = PHPExcel_Style_NumberFormat::FORMAT_GENERAL;
				break; 
		}
		return self::$FORMAT_MAP[$f];
	}
}