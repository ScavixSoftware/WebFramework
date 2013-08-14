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
 
use ScavixWDF\Localization\Localization;

/**
 * This is a PDF document with some invoice specific features.
 * 
 */
class InvoicePdf extends PdfDocument
{
	var $CI = false;
	var $Language = false;
	
	var $Logo = false;
	var $CellHeight = 20;
	
	var $InvoiceNumber = false;
	var $VatPercent = 19.0;
	var $VatCountryCode = false;
	var $OrderDate = false;
	var $PaidHintProcessor = false;

	// result of the generation:
	var $InvoiceTotal = 0;
	var $InvoiceTaxTotal = 0;

	// this enables the caller to overwrite the texts:
	var $Texts = array(
		"INVOICE_NR" => "INVOICE_NR",
		"INVOICE_SENDER" => "INVOICE_SENDER",
		"INVOICE_LOCATION_DATE" => "INVOICE_LOCATION_DATE",		
		"INVOICE_SUBTOTAL" => "INVOICE_SUBTOTAL",
		"INVOICE_VAT_ID" => "INVOICE_VAT_ID",
		"INVOICE_VAT" => "INVOICE_VAT",
		"INVOICE_TOTALTEXT" => "INVOICE_TOTALTEXT",
		"TXT_INVOICE_VAT_REVERSE_CHARGE_HINT" => "TXT_INVOICE_VAT_REVERSE_CHARGE_HINT", 
		"TXT_INVOICE_PAID_HINT" => "TXT_INVOICE_PAID_HINT",
		"INVOICE_PRODUCT" => "INVOICE_PRODUCT",
		"INVOICE_PRICE" => "INVOICE_PRICE",
		"INVOICE_QTY" => "INVOICE_QTY",
		"INVOICE_TOTAL" => "INVOICE_TOTAL",
		"INVOICE_COMPANY_ADDRESS" => "INVOICE_COMPANY_ADDRESS",
		"INVOICE_FOOTER" => "INVOICE_FOOTER",
		"INVOICE_EXTRA_FOOTER" => false,  // i.e. INVOICE_BANK_DETAILS
		);
	
	var $Firstname = "";
	var $Lastname = "";
	var $Companyname = "";
	var $Address1 = "";
	var $Address2 = "";
	var $Zip = "";
	var $City = "";
	var $Country = "";	
	var $VatNumber = false;
	var $Hint = "";
	
	var $Items = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->OrderDate = time();
		$this->Author = "Scavix";
		$this->Creator = "Scavix";
		$this->Producer = "Scavix";
	}
	
	/**
	 * Prepare the variables for an invoice and generate the PDF then.
	 * 
	 * @return void
	 */
	public function RenderInvoice()
	{
		// prepare CI and language
		if( !$this->CI )
			$this->CI = Localization::getCultureInfo("en-US");
		if( !$this->Language )
			$this->Language = $this->CI->ResolveToLanguage();
		
		// prepare VAT information
		$is_eu_country = $this->VatCountryCode && in_array(strtoupper($this->VatCountryCode), $GLOBALS['VAT_COUNTRIES']);
		$add_taxes = $is_eu_country && (strtoupper($this->VatCountryCode) == "DE" || !$this->VatNumber);
		$rev_charge_hint = $is_eu_country && !$add_taxes;

		if( $this->InvoiceNumber )
			$this->Title = getStringLang($this->Language->Code, $this->Texts["INVOICE_NR"], array("{NR}"=>$this->InvoiceNumber));
		
		$this->RenderPDF($add_taxes, $rev_charge_hint);
	}
	
	/**
	 * Prepare the variables for a credit note and generate the PDF then.
	 * 
	 * @return void
	 */
	public function RenderCreditnote()
	{
		// prepare CI and language
		if( !$this->CI )
			$this->CI = Localization::getCultureInfo("en-US");
		if( !$this->Language )
			$this->Language = $this->CI->ResolveToLanguage();
		
		// prepare VAT information
		// rules for credit notes as defined in mantis #7982
		$revchargecountries = array_merge($GLOBALS['VAT_COUNTRIES'], array("LI", "MC", "CH", "LU"));
		unset($revchargecountries["DE"]); // no revcharge hint for germany
		$rev_charge_hint = $this->VatCountryCode && in_array(strtoupper($this->VatCountryCode), $revchargecountries);
		$add_taxes = (strtoupper($this->VatCountryCode) == "DE");
		
		$this->RenderPDF($add_taxes, $rev_charge_hint);
	}
	
	/**
	 * Renders the PDF.
	 * 
	 * Does not write the file! Call <writePdfToFile> afterwards to write the pdf file
	 * @param boolean $add_taxes Add VAT taxes
	 * @param boolean $rev_charge_hint Add the EU reverse charge hint
	 * @return void
	 */
	private function RenderPDF($add_taxes, $rev_charge_hint)
	{
		$this->InvoiceTotal = 0;
		$this->InvoiceTaxTotal = 0;
		
		$vatpercent = ($add_taxes ? $this->VatPercent : 0);
		
		// prepare properties
		$orderdate = $this->CI->FormatDate($this->OrderDate);
		
		// start by creating a page
		$page = $this->createNewPage();
		
		$this->addHeader();
		
		// draw invoice number
		if( $this->InvoiceNumber )
			$this->drawConstant(440, 720, 12, $this->Texts["INVOICE_NR"], array("{NR}"=>$this->InvoiceNumber));
		
		// draw company address
		$this->drawConstant( 50, 660,  8, $this->Texts["INVOICE_SENDER"]);
		$this->currentPage->drawLine(50,658,288,658);
		
		// draw recipient address
		$this->drawText(50, 647, 10, $this->Firstname.' '.$this->Lastname);
		$this->drawText(50, 637, 10, $this->Companyname);
		$this->drawText(50, 627, 10, $this->Address1);
		$this->drawText(50, 617, 10, $this->Address2);
		$this->drawText(50, 607, 10, $this->Zip.' '.$this->City);
		$this->drawText(50, 597, 10, $this->Country);

		//
		if($this->Hint != "")
			$this->drawText(50, 553, 10, $this->Hint);

		// draw order date
		$this->drawConstant(550, 553, 10, $this->Texts["INVOICE_LOCATION_DATE"], array("{DATE}"=>$orderdate), self::AL_RIGHT);
		$this->currentPage->drawLine(50,550,550,550);
		
		// now loop the items and draw them into the table
		$sum = 0;
		$top = $this->FirstPageStart;
		$this->drawTableHeader($top);
		foreach( $this->Items as $item)
		{
			$itemname = $item['name'];
			$price = $item['price'];
			$qty = $item['amount'];
			$total = $qty * $price;
			
			$h = $this->textHeight($itemname) + 10;
			$h = max($h,$this->CellHeight);

			if( $this->testNewPage($top, $h+($this->CellHeight*3)) )
			{
				$this->addFooter();
				$this->createNewPage();
				$this->addHeader();
				$top = $this->OtherPagesStart;
				$this->drawTableHeader($top);
			}
			
			$this->drawCell( 50,$top,550,$h);
			$this->drawCell(300,$top,550,$h);
			$this->drawCell(400,$top,550,$h);
			$this->drawCell(450,$top,550,$h);
			
			$this->drawText( 55, $top - $this->LineHeight, 10, $itemname);
			$this->drawText(395, $top - $this->LineHeight, 10, $this->CI->FormatCurrency($price,false,false,false), self::AL_RIGHT);
			$this->drawText(425, $top - $this->LineHeight, 10, $this->CI->FormatNumber($qty,0), self::AL_RIGHT);
			$this->drawText(545, $top - $this->LineHeight, 10, $this->CI->FormatCurrency($total,false,false,false), self::AL_RIGHT);

			$this->stepOnY($top, $h);
			$sum += $total;
		}
		
		$taxes = $sum * ( $vatpercent / 100 );
		$verytotal = $sum + $taxes;
		
		$this->InvoiceTotal = $verytotal;
		$this->InvoiceTaxTotal = $taxes;
		
		$this->drawConstant(445, $top - $this->LineHeight, 10, $this->Texts["INVOICE_SUBTOTAL"], array(), self::AL_RIGHT );
		$this->drawCell(450,$top,550);
		$this->drawText(545, $top - $this->LineHeight, 10, $this->CI->FormatCurrency($sum,false,false,false), self::AL_RIGHT);
		$this->stepOnY($top, $this->CellHeight);

		if( $this->VatNumber )
			$this->drawText(50, $top - $this->LineHeight, 10, getStringLang($this->Language->Code, $this->Texts["INVOICE_VAT_ID"], array("{VAT_ID}" => $this->VatNumber)));

		$this->drawConstant(445, $top - $this->LineHeight, 10, $this->Texts["INVOICE_VAT"], array("{TAX}"=>$this->CI->FormatNumber($vatpercent, 0)), self::AL_RIGHT);
		$this->drawCell(450,$top,550);
		$this->drawText(545, $top - $this->LineHeight, 10, $this->CI->FormatCurrency($taxes,false,false,false), self::AL_RIGHT);
		$this->stepOnY($top, $this->CellHeight);
		
		$this->drawConstant(445, $top - $this->LineHeight, 10, $this->Texts["INVOICE_TOTALTEXT"], array(), self::AL_RIGHT);
		$this->drawCell(450,$top,550);
		$this->drawText(545, $top - $this->LineHeight, 10, $this->CI->FormatCurrency($verytotal,false,false,false), self::AL_RIGHT);
	
		if( $rev_charge_hint )
		{
			$this->stepOnY($top, $this->CellHeight * 4);
			$this->drawConstant (50, $top - $this->LineHeight, 10, $this->Texts["TXT_INVOICE_VAT_REVERSE_CHARGE_HINT"], array("{VAT_ID}"=>$this->VatNumber));
		}
		
		// draw 'paid on...' hint
		if( $this->PaidHintProcessor )
		{
			$this->stepOnY($top, $this->CellHeight * 4);
			$this->drawConstant(50, $top - $this->LineHeight, 10, $this->Texts["TXT_INVOICE_PAID_HINT"], array("{date}" => $orderdate, "{payment_processor}" => $this->PaidHintProcessor));
		}
		
		// finally add the footer
		$this->addFooter();
	}
	
	private function drawTableHeader(&$top)
	{
		// start the items table by drawing grid and header captions
		$this->drawCell( 50,$top,550,false,0.9);
		$this->drawCell(300,$top,550,false,0.9);
		$this->drawCell(400,$top,550,false,0.9);
		$this->drawCell(450,$top,550,false,0.9);
		
		$this->drawConstant(175, $top - $this->LineHeight, 10, $this->Texts["INVOICE_PRODUCT"], array(), self::AL_CENTER);
		$this->drawConstant(350, $top - $this->LineHeight, 10, $this->Texts["INVOICE_PRICE"], array(), self::AL_CENTER);
		$this->drawConstant(425, $top - $this->LineHeight, 10, $this->Texts["INVOICE_QTY"], array(), self::AL_CENTER);
		$this->drawConstant(500, $top - $this->LineHeight, 10, $this->Texts["INVOICE_TOTAL"], array(), self::AL_CENTER);
		
		$top -= $this->CellHeight;
	}
	
	private function addHeader()
	{
		// draw logo
		if( $this->Logo )
			$this->currentPage->drawImage(Zend_Pdf_Image::imageWithPath($this->Logo), 50, 750, 100, 800);
		// draw company address
		$this->drawConstant(440, 780, 10, $this->Texts["INVOICE_COMPANY_ADDRESS"]);
	}
	
	private function addFooter()
	{
		$text = getStringLang($this->Language->Code, $this->Texts["INVOICE_FOOTER"]);
		if($this->Texts["INVOICE_EXTRA_FOOTER"] !== false)		// add extra info to footer, i.e. bank details (mantis #7528)
			$text .= "<br/>".getStringLang($this->Language->Code, $this->Texts["INVOICE_EXTRA_FOOTER"]);
		else
			$text .= "<br/>";
		$rows = explode("<br/>", str_replace("\n", "<br/>", $text."<br/><br/>"));
		
		$this->currentPage->setFont($this->Font, 8);
		$this->currentPage->setLineWidth(0.5);
		$this->currentPage->drawLine(50, 65, 550, 65);
		$cell = new Zend_Pdf_Cell($this->currentPage, Zend_Pdf_Cell::POSITION_CENTER_X|Zend_Pdf_Cell::POSITION_BOTTOM);

		foreach ($rows as $row)
		{
			$row = str_replace("\r", "", $row);
			$cell->addText($row, Zend_Pdf_Cell::ALIGN_CENTER, 0, 'UTF-8');
			$cell->newLine();
		}

		$cell->write();
	}
	
	private function drawCell($left,$top,$right,$height=false,$gray_scale=1.0)
	{
		if( $height === false )
			$height = $this->CellHeight;
		$this->currentPage->setFillColor(new Zend_Pdf_Color_GrayScale($gray_scale));
		$this->currentPage->drawRectangle($left,$top,$right,$top - $height,Zend_Pdf_Page::SHAPE_DRAW_FILL_AND_STROKE);
		$this->currentPage->setFillColor(new Zend_Pdf_Color_GrayScale(0.0));
	}
	
	private function drawConstant($x, $y, $font_size, $constant, $replacement_data = array(), $alignment = self::AL_LEFT)
	{
		$text = getStringLang($this->Language->Code, $constant, $replacement_data);
		return $this->drawText($x,$y,$font_size,$text,$alignment);
	}
	
	/**
	 * Adds an item to the invoice.
	 * 
	 * @param string $name Item name
	 * @param float $amount Item amount
	 * @param float $price_single Price per item
	 * @return InvoicePdf `$this`
	 */
	function AddItem($name,$amount,$price_single)
	{
		$this->Items[] = array(
			'name' => $name,
			'amount' => $amount,
			'price' => $price_single,
		);
		return $this;
	}
}
