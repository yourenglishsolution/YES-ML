<?php

include_once(_lib_.'/mvc/lib.model.php');
include_once(_lms_.'/models/CommandLms.php');
include_once(_lms_.'/models/ProductLms.php');
include_once(_lms_.'/models/PaymentLms.php');

class InvoiceLms extends Model
{
	protected $_table = 'invoice';
	
	public function exists($invoice_id)
	{
		$sql = "SELECT count(*) as nb FROM invoice WHERE invoice_id='$invoice_id'";
		$row = $this->db->fetch_obj($this->db->query($sql));
		return ((int) $row->nb) > 0;
	}
	
	public function createInvoice($payment_id)
	{
	    // On charge le paiement
	    $mPayment = new PaymentLms();
	    $payment = $mPayment->getPayment($payment_id);
	    
	    // On charge l'utilisateur
	    $mCommand = new CommandLms();
	    $command = $mCommand->getCommand($payment->command_id);
	    $user = $mCommand->getUser($command->command_id);
	    
	    $amount_ht = round($command->amount_ht, 2);
	    $discount_rate = $command->discount_rate;
	    $amount_ttc = round($command->amount_ttc, 2);
	    $amount_tva = $amount_ttc - ($amount_ht - round($amount_ht * $discount_rate, 2)); // Pour gérer les arrondis
	    
	    // On insère la facture
	    $sql = "INSERT INTO invoice (command_id, payment_id, client, address_street, address_city, address_zip, address_country, amount_ht, discount_rate, tva_rate, amount_tva, amount_ttc, crea) VALUES (".$payment->command_id.", ".$payment->payment_id.", '".$user->lastname." ".$user->firstname."', '".$payment->address_street."', '".$payment->address_city."', '".$payment->address_zip."', '".$payment->address_country."', '".$amount_ht."', '".$discount_rate."', '".$command->tva_rate."', '".$amount_tva."', '".$amount_ttc."', UNIX_TIMESTAMP())";
	    $this->db->query($sql);
		$invoice_id = mysql_insert_id();
		
		// On récupère le produit concernée dans la commande
		$mProduct = new ProductLms();
		$product = $mProduct->getProduct($command->product_id);
		
		// On crée les lignes de la facture
		$sql = "INSERT INTO invoice_line (invoice_id, product_id, label, amount_ht) VALUES ('".$invoice_id."', '".$command->product_id."', '".$product->title."',  '".$product->amount_ht."')";
		$this->db->query($sql);
	}
	
	public function getInvoice($invoice_id)
	{
	    $result = false;
		if($this->exists($invoice_id))
		{
			$invoice_id = (int) $invoice_id;
			$sql = "SELECT * FROM invoice WHERE invoice_id=".$invoice_id;
			$result = $this->db->fetch_obj($this->db->query($sql));
		}
		return $result;
	}
	
	public function getLines($invoice_id)
	{
		$mLine = new InvoiceLineLms();
		return $mLine->dbSearch(array('invoice_id' => (int) $invoice_id));
	}
}