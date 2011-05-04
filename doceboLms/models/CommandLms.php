<?php

include_once(_lib_.'/mvc/lib.model.php');
include_once(_adm_.'/lib/lib.code.php');
include_once(_lms_.'/lib/lib.course.php');
include_once(_lms_.'/lib/lib.subscribe.php');
include_once(_lms_.'/models/ProductLms.php');
include_once(_lms_.'/models/CommandLineLms.php');
include_once(_lms_.'/models/PaymentLms.php');
include_once(_lms_.'/models/InvoiceLms.php');
include_once(_lms_.'/models/InvoiceLineLms.php');

class CommandLms extends Model
{
	protected $_table = 'command';
	
	public function exists($command_id)
	{
		$sql = "SELECT count(*) as nb FROM command WHERE command_id='$command_id'";
		$row = $this->db->fetch_obj($this->db->query($sql));
		return ((int) $row->nb) > 0;
	}
	
	public function getTotalMonths($user_id)
	{
	    $count = 0;
	    $commands = $this->getUserCommands($user_id);
	    
	    foreach($commands as $command)
	    {
	        $product = $this->getProduct($command->command_id);
	        $count += (int) $product->abo_months;
	    }
	    return $count;
	}
	
	public function getUserCommands($user_id)
	{
	    $result = array();
		$sql = "SELECT * FROM command WHERE user_id=".$user_id;
		$rows = $this->db->query($sql);
		
		while($row = $this->db->fetch_obj($rows))
		{
			$result[] = $row;
		}
		
		return $result;
	}
	
	public function getUser($command_id)
	{
		$command_id = (int) $command_id;
		$sql = "SELECT * FROM %adm_user WHERE idst=(SELECT user_id FROM command WHERE command_id=".$command_id.")";
		return $this->db->fetch_obj($this->db->query($sql));
	}
	
	public function getCommand($command_id)
	{
		$result = false;
		if($this->exists($command_id))
		{
			$command_id = (int) $command_id;
			$sql = "SELECT * FROM command WHERE command_id=".$command_id;
			$result = $this->db->fetch_obj($this->db->query($sql));
		}
		return $result;
	}
	
	public function createCommand($user_id, $product_id = 0)
	{
		$mProduct = new ProductLms();
		$product = $mProduct->getProduct($product_id);
		
		$tva_rate = 0.196;
		$discount_rate = (float) $product->discount_rate;
		$amount_ht = $product->amount_ht;
		$amount_discount = $amount_ht * $discount_rate;
		$amount_tva = ($amount_ht - $amount_discount) * $tva_rate;
		$amount_ttc = $amount_ht - $amount_discount + $amount_tva;
		
		$sql = "INSERT INTO command (user_id, product_id, amount_ht, discount_rate, tva_rate, amount_tva, amount_ttc, crea) VALUES ('$user_id', '".$product_id."', '".$amount_ht."', '".$discount_rate."', '$tva_rate', '$amount_tva', '$amount_ttc', UNIX_TIMESTAMP())";
		$this->db->query($sql);
		$command_id = mysql_insert_id();
		
		return $command_id;
	}
	
	public function getProduct($command_id)
	{
		$products = array();
		$command = $this->getCommand($command_id);
		
		$mProducts = new ProductLms();
		$product = $mProducts->getProduct($command->product_id);
		
		return $product;
	}
	
	public function getPayment($command_id)
	{
		$result = array();
		$command_id = (int) $command_id;
		$sql = "SELECT * FROM payment WHERE command_id=".$command_id;
		$result = $this->db->fetch_obj($this->db->query($sql));
		return $result;
	}
	
	public function receivePayment($command_id, $amount_ttc, $data = array())
	{
	    $amount_ttc = (float) $amount_ttc;
	    
		if($this->exists($command_id))
		{
			$mPayment = new PaymentLms();
			$command = $this->getCommand($command_id);
			
			// On enregistre le paiement
			$payment = $mPayment->addPayment($command->command_id, $amount_ttc, $data);
			
			// On compte le nombre de paiement qu'il y a eu pour cet utilisateur
			$sql = "SELECT count(*) as nb FROM payment WHERE command_id IN (SELECT command_id FROM command WHERE user_id=".$command->user_id.")";
			$row = $this->db->fetch_obj($this->db->query($sql));
			$count = (int) $row->nb;
			
			if((float) $command->amount_ttc == $amount_ttc)
			{
				// On a trouvé le produit
				$mProducts = new ProductLms();
				$product = $mProducts->getProduct($command->product_id);
				
				// On construit le code qui va nous permettre de savoir quel groupe de code utiliser
				$codeGroupCode = $product->code.$count;
				
				$sql = "SELECT * FROM %adm_code_groups WHERE description LIKE '$codeGroupCode'";
				$codeGroup = $this->db->fetch_obj($this->db->query($sql));
				
				// On génère le code et on le lance
				$codeManager = new CodeManager();
				$code = $codeManager->generateCode($codeGroup->idCodeGroup);
				$valid_code = $codeManager->controlCodeValidity($code);

				if($valid_code == 1) // Le code est utilisable
				{
					// On liste les cours du code
					$array_course = $codeManager->getCourseAssociateWithCode($code);
					
					$array_course_name = array();
					$man_course = new Man_Course();
					$subscribe = new CourseSubscribe_Management();
					
					// On assigne chacun des cours à l'utilisateur
					foreach($array_course as $id_course)
					{
						$query_control = "SELECT COUNT(*)"
							." FROM %lms_courseuser"
							." WHERE idCourse = ".$id_course
							." AND idUser = ".$command->user_id;
						
						list($control) = sql_fetch_row(sql_query($query_control));
						
						if($control == 0)
						{
							$subscribe->subscribeUser((int) $command->user_id, $id_course, '3');
						}
					}
					
					// Le code a été utilisé
					$codeManager->setCodeUsed($code, $command->user_id);
					
					$sql = "UPDATE command SET paid=UNIX_TIMESTAMP() WHERE command_id=".$command->command_id;
					$this->db->query($sql);
				}
			}
			
			// on crée la facture correspondant au paiement
			$this->createInvoice($payment->payment_id);
		}
	}
	
	public function createInvoice($payment_id)
	{
	    $mInvoice = new InvoiceLms();
	    $mInvoice->createInvoice($payment_id);
	}
}