<?php

include_once(_lib_.'/mvc/lib.model.php');
include_once(_lms_.'/models/CommandLms.php');
include_once(_lms_.'/models/ProductLms.php');

class InvoiceLms extends Model
{
	protected $_table = 'invoice';
	
	public function exists($invoice_id)
	{
		$sql = "SELECT count(*) as nb FROM invoice WHERE invoice_id='$invoice_id'";
		$row = $this->db->fetch_obj($this->db->query($sql));
		return ((int) $row->nb) > 0;
	}
	
	public function createInvoice($command_id)
	{
	    $mCommand = new CommandLms();
	    $command = $mCommand->getCommand($command_id);
	    
	    $user = $mCommand->getUser($command_id);
	    
	    // Les champs que l'on souhaite récupérer
	    $userInfos = array(
	    'address_street' => '',
	    'address_city' => '',
	    'address_zip' => '',
	    'address_country' => '',
	    );
	    
	    // On rempli un maximum de champs
	    $payments = $mCommand->getPayments($command_id);
	    foreach($payments as $payment)
	    {
	        foreach($userInfos as $key => $value)
	        {
	            if($value == '') $userInfos[$key] = $payment->$key;
	        }
	    }
	    
	    // On insère la facture
	    $sql = "INSERT INTO invoice (command_id, client, address_street, address_city, address_zip, address_country, amount_ht, tva_rate, amount_tva, amount_ttc, crea) VALUES ('$command_id', '".$user->lastname." ".$user->firstname."', '".$userInfos['address_street']."', '".$userInfos['address_city']."', '".$userInfos['address_zip']."', '".$userInfos['address_country']."', ".$command->amount_ht."', '".$command->tva_rate."', '".$command->amount_tva."', '".$command->amount_ttc."', UNIX_TIMESTAMP())";
		$this->db->query($sql);
		$invoice_id = mysql_insert_id();
		
		// On crée les lignes de la facture
		foreach($mCommand->getLines($command_id) as $line)
		{
		    $mProduct = new ProductLms();
		    $product = $mProduct->getProduct($line->product_id);
		    
		    $sql = "INSERT INTO invoice_line (invoice_id, product_id, label, amount_ht) VALUES ('".$invoice_id."', '".$line->product_id."', '".$product->title."',  '".$line->amount_ht."')";
		    $this->db->query($sql);
		}
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