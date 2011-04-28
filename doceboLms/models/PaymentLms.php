<?php

include_once(_lib_.'/mvc/lib.model.php');

class PaymentLms extends Model
{
	public function addPayment($command_id, $amount_ttc, $data = array())
	{
		$fields = array(
			'command_id' => (int) $command_id,
			'amount_ttc' => (float) $amount_ttc,
			'crea' => time()
			);
		
		$dataFields = array(
			'payer_status',
			'payer_email',
			'payment_type',
			'mc_currency',
    		'address_street',
		    'address_city',
    	    'address_zip',
    	    'address_country',
			);
		
		foreach($dataFields as $key)
		{
			if(isset($data[$key])) $fields[$key] = '"'.addslashes($data[$key]).'"';
		}
		
		$sql = "INSERT INTO payment (".implode(',', array_keys($fields)).", temp) VALUES (".implode(',', $fields).", \"".addslashes(serialize($data))."\")";
		$this->db->query($sql);
		
		$payment_id = mysql_insert_id();
		return $this->getPayment($payment_id);
	}
	
	public function getInvoice($payment_id)
	{
		$result = false;
		
	    $payment_id = (int) $payment_id;
		$sql = "SELECT * FROM invoice WHERE payment_id=".$payment_id;
		$result = $this->db->fetch_obj($this->db->query($sql));
		
		return $result;
	}
	
	public function getPayment($payment_id)
	{
	    $result = false;
		$payment_id = (int) $payment_id;
		$sql = "SELECT * FROM payment WHERE payment_id=".$payment_id." LIMIT 1";
		$result = $this->db->fetch_obj($this->db->query($sql));
		return $result;
	}
}