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
namespace ScavixWDF\Controls\Table;

use ScavixWDF\Localization\CultureInfo;

/**
 * Handles cell formatting in a <Table>.
 * 
 * Never instanciate this class yourself, this will be done by <Table::SetFormat> or <Table::SetColFormat>.
 * Valid formats are:
 * - duration
 * - fixed, pre, preformatted
 * - date
 * - time
 * - datetime
 * - curreny
 * - int, integer
 * - percent
 * - float, double, f2, d2
 * In fact you may also use `array('float',4)` if you want a float with four decimal places but this array syntax only applies to float/double.
 * How the values are actually formatted depends on the <CultureInfo> you chose.
 */
class CellFormat
{
    var $format = false;
    var $blank_if_false = false;
	var $conditional_css = array();

	function __construct($format=false,$blank_if_false=false,$conditional_css=array())
	{
		if( $format !== false )
		{
			if( is_string($format) )
			{
				switch( strtolower($format) )
				{
					case 'f2': 
					case 'd2': 
					case 'float': 
					case 'double': 
						$format = array('double',2);
						break;
				}
			}			
			$this->format = $format;
			$this->blank_if_false = $blank_if_false;
			$this->conditional_css = $conditional_css;
		}
	}
	
	/**
	 * Gets the format.
	 * 
	 * @return string The format
	 */
	function GetFormat()
	{
		if( is_array($this->format) )
			list($format,$options) = $this->format;
		else
			$format = $this->format;
		return $format;
	}

	/**
	 * @internal Performs formatting of a table cell (<Td>)
	 */
	function Format(&$cell,$culture=false)
	{
		$full_content = $cell->GetContent();
		$content = $this->FormatContent($full_content,$culture);
		if( $this->blank_if_false && !$content )
		{
			$cell->SetContent("");
			return;
		}

		$cell->SetContent( $content );
		$ccss = $this->GetConditonalCss();
		if( $ccss )
		{
			$cs = isset($cell->style)?$cell->style:"";
			$cell->style = $cs.$ccss;
		}
	}
    
    private function getNumeric($val)
    {
        if( is_numeric($val) )
            return 0+$val;
        if( count(explode(",","$val")) == 2 )
            return $this->getNumeric(str_replace(",", ".", $val));
        return false;
    }
	
	/**
	 * Formats a given string.
	 * 
	 * @param string $full_content The string to format
	 * @param CultureInfo $culture <CultureInfo> object or false if not present
	 * @return string The formatted string
	 */
	function FormatContent($full_content,$culture=false)
	{
		$this->content = $content = trim(strip_tags($full_content));
		if( $this->blank_if_false && !$content )
			return "";
		if( !$this->format )
			return $full_content;
		
        if( is_array($this->format) )
		{
			list($format,$options) = $this->format;
			$format = strtolower($format);
			if(!is_array($options))
				$options = array($options);
		}
		else
			$format = strtolower($this->format);

		if( $format == 'duration' )
		{
			if( intval($content)."" != $content )
				return $full_content;
			
			$completedur = $dur = intval($content);
			$s = sprintf("%02u",$dur % 60);
			$dur = floor($dur / 60);
			$h = floor($dur / 60);
			if( $completedur == 0 )
				$content = "0:00";
			elseif( $h > 0 )
			{
				$m = sprintf("%02u",$dur % 60);
				$content = str_replace($content,"$h:$m:$s",$full_content);
			}
			else
			{
				$m = sprintf("%u",$dur % 60);				
				$content = str_replace($content,"$m:$s",$full_content);
			}
		}
		if( $format == 'fixed' || $format == 'pre' || $format == 'preformatted' )
		{
			$content = str_replace($content,"<pre>$content</pre>",$full_content);
		}
		elseif( $culture )
		{
			switch( $format )
			{
				case 'date':
					if( strtotime($content) === false ) return $full_content;
					$content = str_replace($content,$culture->FormatDate($content),$full_content);
					break;
				case 'time':
					if( strtotime($content) === false ) return $full_content;
					$content = str_replace($content,$culture->FormatTime($content),$full_content);
					break;
				case 'datetime':
					if( strtotime($content) === false ) return $full_content;
					$content = str_replace($content,$culture->FormatDateTime($content),$full_content);
					break;
				case 'currency':
                    $v = $this->getNumeric($content);
                    if( $v === false ) return $full_content;
                    $v = $culture->FormatCurrency($v);                    
                    if(isset($options[0]) && ($options[0] === false))
                        $v = str_replace($culture->CurrencyFormat->DecimalSeparator.'00', '', $v);
                    $content = str_replace($content,$v,$full_content);
					break;
				case 'int':
				case 'integer':
					$v = $this->getNumeric($content);
                    if( $v === false ) return $full_content;
					$content = str_replace($content,$culture->FormatInt($v),$full_content);
					break;
				case 'percent':
					$v = $this->getNumeric($content);
                    if( $v === false ) return $full_content;
					$content = str_replace($content,$culture->FormatInt($v)."%",$full_content);
					break;
				case 'float':
				case 'double':
					$v = $this->getNumeric($content);
                    if( $v === false ) return $full_content;
					$content = str_replace($content,$culture->FormatNumber($v,intval($options[0])),$full_content);
					break;
                case 'custom':
                    $content = str_replace($content,sprintf($options[0],$content),$full_content);
                    break;
			}
		}
		else
			$content = str_replace($content,sprintf($format,$content),$full_content);
		
		return $content;
	}

	private function GetConditonalCss()
	{
		$content = $this->content;
		if( !is_numeric($content) )
			return "";
		
		foreach( $this->conditional_css as $cond=>$css )
		{
			switch( strtolower($cond) )
			{
				case 'neg':
				case 'negative':
					if( floatval($content) < 0 )
						return $css;
					break;
				case 'pos':
				case 'positive':
					if( floatval($content) > 0 )
						return $css;
					break;
				case 'copy':
					return $css->GetConditonalCss();
				default:
					if( !preg_match('/(.+)\((\d+)\)/', $cond, $m) )
						break;
					
					$v = intval($m[2]);
					switch( $m[1] )
					{
						case 'gt': 
							if( floatval($content) > $v )
								return $css;
							break;
						case 'gte': 
							if( floatval($content) >= $v )
								return $css;
							break;
						case 'lt': 
							if( floatval($content) < $v )
								return $css;
							break;
						case 'lte': 
							if( floatval($content) <= $v )
								return $css;
							break;
						case 'eq': 
							if( floatval($content) == $v )
								return $css;
							break;
					}
					
					break;
			}
		}
		return "";
	}
}
