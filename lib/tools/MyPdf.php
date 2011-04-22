<?php

class MyPdf extends Zend_Pdf
{
	private $currentPage = 0, $width = 0, $height = 0, $size;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->addPage();
		$this->setFont(12);
		$this->pages[$this->currentPage]->setLineWidth(.50);
	}
	
	// Change la page de travail en cours
	public function switchPage($page_id)
	{
		$this->currentPage = $page_id;
	}
	
	public function addImage($image, $x, $y, $width, $height)
	{
		$x1 = $x;
		$y1 = $this->height - $y - $height;
		$x2 = $x + $width;
		$y2 = $this->height - $y;
		$this->pages[$this->currentPage]->drawImage($image, $x1, $y1, $x2, $y2);
	}
	
	public function addRectangle($x, $y, $width, $height)
	{
		$x1 = $x;
		$y1 = $this->height - $y - $height;
		$x2 = $x + $width;
		$y2 = $this->height - $y;
		$this->pages[$this->currentPage]->drawRectangle($x1, $y1, $x2, $y2, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
	}
	
	public function addLine($x, $y, $width, $vertical=false)
	{
		$x1 = $x;
		$y1 = $this->height - $y;
		
		if(!$vertical)
		{
			$x2 = $x + $width;
			$y2 = $this->height - $y;
		}
		else
		{
			$x2 = $x;
			$y2 = $y1 - $width;
		}
		
		$this->pages[$this->currentPage]->drawLine($x1, $y1, $x2, $y2);
	}
	
	public function addText($text, $x, $y)
	{
		$x = $x;
		$y = $this->height - $y - $this->size;
		
		$this->pages[$this->currentPage]->drawText($text, $x, $y, 'UTF-8');
	}
	
	public function setFont($size=10, $bold=false, $italic=false)
	{
		$this->size = $size;
		
		$font_type = Zend_Pdf_Font::FONT_HELVETICA;
		if($bold !== false && $italic !== false) $font_type = Zend_Pdf_Font::FONT_HELVETICA_BOLD_ITALIC;
		elseif($bold !== false) $font_type = Zend_Pdf_Font::FONT_HELVETICA_BOLD;
		elseif($italic !== false) $font_type = Zend_Pdf_Font::FONT_HELVETICA_ITALIC;
		
		$font = Zend_Pdf_Font::fontWithName($font_type);
		$this->pages[$this->currentPage]->setFont($font, $size);
	}
	
	// Ajoute une page au document
	private function addPage($type = Zend_Pdf_Page::SIZE_A4)
	{
		$new_id = count($this->pages);
		$this->pages[$new_id] = $this->newPage($type);
		$this->currentPage = $new_id;
		
		$this->width  = $this->pages[$this->currentPage]->getWidth();
		$this->height = $this->pages[$this->currentPage]->getHeight();
		
		return $this->currentPage;
	}
}