<?php

include_once(_base_."/lib/tools/MyPdfStructure.php");
include_once(_lms_."/models/InvoiceLms.php");

class YesInvoice extends MyPdfStructure
{
	private $invoice = null;
	
	public function __construct()
	{
	    $this->logoHeader = _PDF_LOGO_HEADER;
		parent::__construct();
	}
	
	public function load($invoice_id)
	{
	    $mInvoice = new InvoiceLms();
		$this->invoice = $mInvoice->getInvoice($invoice_id);
		$this->setTo($this->invoice);
	}
	
	public function render()
	{
		$this->moreContent();
		return parent::render();
	}
	
	protected function moreContent()
	{
		// Informations header
		$this->createHeaderInfos();
		
		// Dessin des lignes
		$mInvoice = new InvoiceLms();
		$lines = $mInvoice->getLines($this->invoice->invoice_id);
		$top = parent::createContent($lines);
		
		// Liste champs du total
		$totals = array();
		$totals[] = array('label' => 'Total HT', 'price' => $this->invoice->amount_ht);
		$totals[] = array('label' => 'TVA', 'price' => $this->invoice->amount_tva);
		if($this->invoice->discount > 0) $totals[] = array('label' => 'Remise', 'price' => $this->invoice->discount);
		$totals[] = array('label' => 'Total TTC *', 'price' => $this->invoice->amount_ttc);
		
		$top = parent::createTotals($totals);
	}
	
	private function createHeaderInfos()
	{
		$top = 25;
		$this->pdf->addText('Date : '.date('d/m/Y', $this->invoice->crea), 370, $top);
		$this->pdf->addText('Facture n° : '.sprintf("%04s", $this->invoice->invoice_id).date('ym'), 370, $top+15);
	}
}