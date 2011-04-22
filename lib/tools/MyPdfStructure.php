<?php

include_once(_base_."/lib/tools/MyPdf.php");

abstract class MyPdfStructure
{
    public $logoHeader = false;
	protected $pdf, $from, $to, $top;
	
	public function __construct()
	{
		$this->pdf = new MyPdf();
		$this->from = array();
		$this->to = array();
		
		$from['name'] = 'YES - Your English Solution';
		$from['address'] = '1 rue de Gramont';
		$from['zip'] = '75002';
		$from['city'] = 'PARIS';
		$from['phone'] = '01 40 15 63 08';
		$from['fax'] = '';
		$from['mail'] = '';
		
		$this->setFrom($from);
	}
	
	public function setFrom($infos)
	{
		$this->from = $infos;
	}
	
	public function setTo($infos)
	{
		$this->to['name'] = $infos->client;
		$this->to['address'] = $infos->address_street;
		$this->to['zip'] = $infos->address_zip;
		$this->to['city'] = ucfirst($infos->address_city);
		$this->to['phone'] = '';
		$this->to['fax'] = '';
	}
	
	public function render()
	{
		$this->setHeader();
		$this->setFooter();
		$this->pdf->setFont(10);
		$this->createFrom();
		$this->createTo();
		return $this->pdf->render();
	}
	
	protected function setHeader()
	{
	    if($this->logoHeader !== false)
	    {
    		$image = Zend_Pdf_Image::imageWithPath($this->logoHeader);
    		$this->pdf->addImage($image, 10, 10, 167, 61);
	    }
		
		$top = 130;
		$this->pdf->addText('Expéditeur', 50, $top);
		$this->pdf->addRectangle(50, $top+15, 200, 100);
		
		$this->pdf->addText('Destinataire', 330, $top);
		$this->pdf->addRectangle(330, $top+15, 200, 100);
	}
	
	protected function setFooter()
	{
		$this->pdf->setFont(10);
		$this->pdf->addText('YES - http://www.yourenglishsolution.fr - Siret : 500 492 210 00058', 130, 800);
	}
	
	protected function createFrom()
	{
		$top = 150;
		if(count($this->from) > 0)
		{
			$this->pdf->addText($this->from['name'], 55, $top);
			$this->pdf->addText($this->from['address'], 55, $top+15);
			$this->pdf->addText($this->from['zip'].' '.$this->from['city'], 55, $top+30);
			$this->pdf->addText('Mel : '.$this->from['mail'], 55, $top+55);
			$this->pdf->addText('Tel : '.$this->from['phone'], 55, $top+70);
			if($this->from['fax'] != '') $this->pdf->addText('Fax : '.$this->from['fax'], 55, $top+85);
			
		}
	}
	
	protected function createTo()
	{
		$top = 150;
		if(count($this->to) > 0)
		{
			$this->pdf->addText($this->to['name'], 335, $top);
			$this->pdf->addText($this->to['address'], 335, $top+15);
			$this->pdf->addText($this->to['zip'].' '.$this->to['city'], 335, $top+30);
			$this->pdf->addText('Tel : '.$this->to['phone'], 335, $top+55);
			if($this->to['fax'] != '') $this->pdf->addText('Fax : '.$this->to['fax'], 335, $top+70);
		}
	}
	
	protected function createContent($lines)
	{
		$top = 350;
		$nbLines = count($lines) + 1;
		
		$this->pdf->setFont(8);
		$this->pdf->addText('Prix indiqués en EURO', 407, $top-10);
		
		// Dessin du contour du tableau
		$this->pdf->addRectangle(50, $top, 480, 15*$nbLines);
		$this->pdf->addLine(370, $top, 15*$nbLines, true);
		$this->pdf->addLine(450, $top, 15*$nbLines, true);
		
		// Noms de colonnes
		$this->pdf->setFont(10, true);
		$this->pdf->addText('Désignation', 55, $top+2);
		$this->pdf->addText('Qté', 375, $top+3);
		$this->pdf->addText('Total', 455, $top+3);
		
		$this->pdf->setFont(10);
		
		// Dessin des lignes
		foreach($lines as $line)
		{
			$top += 15;
			$this->pdf->addText(utf8_encode($line->label), 55, $top+2);
			$this->pdf->addText(1, 375, $top+2);
			$this->pdf->addText(self::getNumberFormat($line->amount_ht), 455, $top+2);
			$this->pdf->addLine(50, $top, 480);
		}
		
		$this->top = $top;
		return $top;
	}
	
	protected function createTotals($totals)
	{
		$top = $this->top;
		$top += 25;
		$height = 15*count($totals);
		
		$this->pdf->addRectangle(370, $top, 160, $height);
		$this->pdf->addLine(450, $top, $height, true);
		
		$this->pdf->setFont(10, true);
		
		// Dessin du total
		foreach($totals as $total)
		{
			$this->pdf->setFont(10, true);
			$this->pdf->addText($total['label'], 375, $top+2);
			
			$this->pdf->setFont(10);
			$this->pdf->addText(self::getNumberFormat($total['price']), 455, $top+2);
			$top += 15;
			$this->pdf->addLine(370, $top, 160);
		}
		
		$this->top = $top;
		return $top;
	}
	
	private static function getNumberFormat($number)
	{
		return number_format($number, 2, ',', ' ');
	}
}