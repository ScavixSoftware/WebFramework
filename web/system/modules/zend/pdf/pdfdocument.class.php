<?php

/**
 * Extends <Zend_Pdf> with some useful/essential methods.
 * 
 */
class PdfDocument extends Zend_Pdf
{
	const AL_LEFT = 0;
	const AL_CENTER = 1;
	const AL_RIGHT = 2;
	
	var $FirstPageStart = 535;
	var $OtherPagesStart = 700;
	var $PageBreakBottomOffset = 60;
	var $LineHeight = 12;

	var $Title = false;
	var $Author = false;
	var $Subject = false;
	var $Creator = false;
	var $Producer = false;
	private $_pdfMarks = array('Title','Author','Subject','Creator','Producer');
	
	protected $currentPage = false;
	
	public function __construct()
	{
		parent::__construct();
		$this->Font = Zend_Pdf_Font::fontWithPath(zend_font_path().'ARIALUNI.TTF');
	}
	
	/**
	 * Renders the document to a PDF file.
	 * 
	 * @param string $filename Filename to store in
	 * @return void
	 */
	public function RenderToFile($filename)
	{
		$temp_file = tempnam(sys_get_temp_dir(),"zend_generated_pdf_doc_").".pdf";
		$this->save($temp_file);

		$marks = array();
		foreach( $this->_pdfMarks as $m )
			if( $this->$m )
				$marks[] = "$m (".chr(254).chr(255).iconv('UTF-8','UTF-16BE',$this->$m).")";
		if( count($marks)>0 )
		{
			$marks = "[ /".implode("\n  /",$marks)."\n  /DOCINFO pdfmark";
			$marksfile = tempnam(sys_get_temp_dir(),"zend_generated_pdf_doc_marks").".pdf";
			file_put_contents($marksfile, $marks);
			$temp_file .= " $marksfile";
		}
		
		$temp_file2 = $filename;
		$out = array();
		// with -dPDFSETTINGS=/printer it will convert all lines to light blue, so i removed it
		$tmp = exec("gs -q -sSAFER -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=$filename -dCompatibilityLevel=1.6 -f $temp_file 2>&1", $out);
		@unlink($temp_file);
		if( isset($marksfile) )
			@unlink($marksfile);
	}
	
	/**
	 * Gets the current active page.
	 * 
	 * Creates one if needed.
	 * @return Zend_Pdf_Page The current page
	 */
	public function GetCurrentPage()
	{
		if( !$this->currentPage )
			$this->currentPage = $this->createNewPage();
		return $this->currentPage;
	}
	
	protected function testNewPage($y, $offset)
	{
		return ($y-$offset) < $this->PageBreakBottomOffset;
	}
	
	protected function stepOnY(&$y, $offset)
	{
		$y -= $offset;
		if( $y < $this->PageBreakBottomOffset )
		{
			$this->addFooter();
			$this->createNewPage();
			$this->addHeader();
			$y = $this->OtherPagesStart;
		}
	}
	
	protected function &createNewPage()
	{
		$this->currentPage = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
		$this->currentPage->setLineWidth(0.5);
		$this->currentPage->setLineColor(new Zend_Pdf_Color_GrayScale(0.8));
		$this->pages[] = $this->currentPage;
		return $this->currentPage;
	}
	
	protected function textWidth($text,$font_size)
	{
		//make into a character array
		$charArray = array();
		$text = iconv('UTF-8', 'UTF-16BE//IGNORE', $text);
		$res_len = 0;
		for($x=0; $x<strlen($text); $x++)
			$charArray[] = (ord($text[$x++]) << 8) | ord($text[$x]);
		$lengths = $this->Font->widthsForGlyphs($charArray);
		$fontGlyphWidth = array_sum($lengths);
		return $fontGlyphWidth / $this->Font->getUnitsPerEm() * $font_size;
	}
	
	protected function textHeight($text)
	{
		if(strpos($text, "\n") !== false)
		{
			$rows = explode("\n", $text);
			return $this->LineHeight * count($rows);
		}
		return $this->LineHeight;
	}
	
	/**
	 * Draws text on the curren page.
	 * 
	 * @param int $x X coordinate
	 * @param int $y Y coordinate
	 * @param int $font_size Font size
	 * @param string $text The text to be drawn
	 * @param int $alignment PdfDocument::AL_LEFT, PdfDocument::AL_CENTER or PdfDocument::AL_RIGHT
	 * @return int Returns the height of the drawn text (to easily change the $y coordinate for the next call)
	 */
	public function drawText($x, $y, $font_size, $text, $alignment = self::AL_LEFT)
    {
		if( !$this->currentPage )
			$this->currentPage = $this->createNewPage();
		
		$charEncoding = 'UTF-8';
		$this->currentPage->setFont($this->Font, $font_size);
		
		$text = trim($text);
		$text = str_ireplace("\r\n","\n",$text);
		$text = str_ireplace("<br/>","\n",$text);
		$text = str_ireplace("<br>","\n",$text);
		$text = str_ireplace("<br />","\n",$text);
		$text = wordwrap($text, 110, "\n", false);
		
		switch( $alignment )
		{
			case self::AL_CENTER:
				$x = $x - ($this->textWidth($text,$font_size) / 2);
				break;
			case self::AL_RIGHT:
				$x = $x - $this->textWidth($text,$font_size);
				break;
		}
		
		// spacial handling for multi-line texts
		if(strpos($text, "\n") !== false)
		{
			$rows = explode("\n", $text);
			foreach ($rows as $row)
			{
				$row = str_replace("\r", "", $row);
				$this->currentPage->drawText($row, $x, $y, $charEncoding);
				$y -= 12;
			}
			return $this->LineHeight * count($rows);
		}
		
		/**
         * Handle Arabic text (mantis #6712)
         */
        if(0 < preg_match('/\p{Arabic}/u', $text))
        {
            system_load_module(__DIR__."/../../arabic.php");
            $arglyphs = new I18N_Arabic('Glyphs');
            $text = $arglyphs->utf8Glyphs($text);
        }
        /**
         * Handle Hebrew text.
         */
        else if(0 < preg_match('/\p{Hebrew}/u', $text))
        {
            $text = iconv("ISO-8859-8", "UTF-8", hebrev(iconv("UTF-8", "ISO-8859-8//IGNORE", $text)));
        }
		
        $this->currentPage->drawText($text, $x, $y, $charEncoding);				
		return $this->LineHeight;
    }
	
	/**
	 * Draws a line on the current page.
	 * 
	 * @param int $x1 X coordinate of the start point
	 * @param int $y1 Y coordinate of the start point
	 * @param int $x2 X coordinate of the end point
	 * @param int $y2 Y coordinate of the end point
	 * @param string $color Valid HTML color value, see <Zend_Pdf_Color_Html>
	 * @return PdfDocument `$this`
	 */
	public function drawLine($x1, $y1, $x2, $y2, $color='black')
    {
		if( !$this->currentPage )
			$this->currentPage = $this->createNewPage();
		if( $color )
			$this->currentPage->setLineColor(Zend_Pdf_Color_Html::color($color));
		$this->currentPage->drawLine($x1, $y1, $x2, $y2);
		return $this;
	}
	
	/**
	 * Drwas an image to the current page.
	 * 
	 * @param mixed $image Local path to image as string or <Zend_Pdf_Image>
	 * @param int $x1 X coordinate of the top left corner
	 * @param int $y1 Y coordinate of the top left corner
	 * @param int $x2 X coordinate of the bottom right corner
	 * @param int $y2 Y coordinate of the bottom right corner
	 * @return PdfDocument `$this`
	 */
	public function drawImage($image, $x1, $y1, $x2, $y2)
	{
		if( is_string($image) )
			$image = Zend_Pdf_Image::imageWithPath($image);
		if( !$this->currentPage )
			$this->currentPage = $this->createNewPage();
		$this->currentPage->drawImage($image, $x1, $y1, $x2, $y2);
		return $this;
	}
	
	/**
	 * Drwas a rectangle to the current page.
	 * 
	 * @param int $x1 X coordinate of the top left corner
	 * @param int $y1 Y coordinate of the top left corner
	 * @param int $x2 X coordinate of the bottom right corner
	 * @param int $y2 Y coordinate of the bottom right corner
	 * @param string $line_color Valid HTML color value for the border, see <Zend_Pdf_Color_Html>
	 * @param string $fill_color Valid HTML color value to fill the rectangle, see <Zend_Pdf_Color_Html>
	 * @return PdfDocument `$this`
	 */
	public function drawRectangle($x1, $y1, $x2, $y2, $line_color='black', $fill_color='')
	{
		if( !$this->currentPage )
			$this->currentPage = $this->createNewPage();
		
		if( $line_color && $fill_color )
		{
			$this->currentPage->setLineColor(Zend_Pdf_Color_Html::color($line_color));
			$this->currentPage->setFillColor(Zend_Pdf_Color_Html::color($fill_color));
			$this->currentPage->drawRectangle($x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_FILL_AND_STROKE);
		}
		elseif( $line_color && !$fill_color )
		{
			$this->currentPage->setLineColor(Zend_Pdf_Color_Html::color($line_color));
			$this->currentPage->drawRectangle($x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
		}
		elseif( !$line_color && $fill_color )
		{
			$this->currentPage->setFillColor(Zend_Pdf_Color_Html::color($fill_color));
			$this->currentPage->drawRectangle($x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_FILL);
		}
		return $this;
	}
}
