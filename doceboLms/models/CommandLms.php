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
	        $products = $this->getProducts($command->command_id);
	        
	        foreach($products as $product)
	        {
	            $count += (int) $product->abo_months;
	        }
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
	
	public function getUserProductCommand($user_id, $product_id)
	{
		$result = false;
		$sql = "SELECT c.* FROM command c LEFT JOIN command_line cl ON (cl.command_id=c.command_id) WHERE paid=0 AND product_id=".$product_id." AND user_id=".$user_id." LIMIT 1";
		$rows = $this->db->query($sql);
		
		while($row = $this->db->fetch_obj($rows))
		{
			$result = $row;
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
	
	public function getInvoice($command_id)
	{
		$result = false;
		
		if($this->exists($command_id))
		{
		    $command_id = (int) $command_id;
			$sql = "SELECT * FROM invoice WHERE command_id=".$command_id;
			$result = $this->db->fetch_obj($this->db->query($sql));
		}
		
		return $result;
	}
	
	public function createCommand($user_id, $productsId = array())
	{
		$products = array();
		$total_ht = 0;
		
		$mProduct = new ProductLms();
		
		foreach($productsId as $id)
		{
			$product = $mProduct->getProduct($id);
			$total_ht += (float) $product->amount_ht;
			$products[] = $product;
		}
		
		$tva_rate = 0.196;
		$total_tva = $total_ht * $tva_rate;
		$total_ttc = $total_ht + $total_tva;
		
		$sql = "INSERT INTO command (user_id, amount_ht, tva_rate, amount_tva, amount_ttc, crea) VALUES ('$user_id', '$total_ht', '$tva_rate', '$total_tva', '$total_ttc', UNIX_TIMESTAMP())";
		$this->db->query($sql);
		$command_id = mysql_insert_id();
		
		foreach($products as $product)
		{
			$abo = (ProductLms::getTTC($product->amount_ht)/$product->abo_months);
			$sql = "INSERT INTO command_line (command_id, product_id, amount_ht, amount_month_ttc) VALUES ('$command_id', '".$product->idProduct."', '".$product->amount_ht."', '".$abo."')";
			$this->db->query($sql);
		}
		
		return $command_id;
	}
	
	public function getLines($command_id)
	{
		$mLine = new CommandLineLms();
		return $mLine->dbSearch(array('command_id' => (int) $command_id));
	}
	
	public function getProducts($command_id)
	{
		$products = array();
		$mProducts = new ProductLms();
		$lines = $this->getLines($command_id);
		
		foreach($lines as $line)
		{
			$products[] = $mProducts->getProduct($line->product_id);
		}
		
		return $products;
	}
	
	public function getPayments($command_id)
	{
		$result = array();
		$command_id = (int) $command_id;
		$sql = "SELECT * FROM payment WHERE command_id=".$command_id;
		$list = $this->db->query($sql);
		
		while($row = $this->db->fetch_obj($list))
		{
			$result[] = $row;
		}
		
		return $result;
	}
	
	public function updatePayed($command_id)
	{
		$complete = false;
		$total_payments = 0;
		$payments = $this->getPayments($command_id);
		$command = $this->getCommand($command_id);
		
		foreach($payments as $pay)
		{
			$total_payments += (float) $pay->amount_ttc;
		}
		
		if($command->amount_ttc >= $total_payments)
		{
			$sql = "UPDATE command SET paid=UNIX_TIMESTAMP() WHERE command_id='$command_id'";
			$this->db->query($sql);
			$complete = true;
		}
		
		return $complete;
	}
	
	public function receivePayment($command_id, $amount_ttc, $data = array())
	{
		if($this->exists($command_id))
		{
			$mPayment = new PaymentLms();
			$command = $this->getCommand($command_id);
			
			// On enregistre le paiement
			$payment = $mPayment->addPayment($command->command_id, $amount_ttc, $data);
			
			// On cherche quel produit est lié au paiement (via sont prix TTC mensuel) - TODO pas top ça...
			$lines = $this->getLines($command->command_id);
			
			// On compte le nombre de paiement qu'il y a eu pour cet utilisateur
			$sql = "SELECT count(*) as nb FROM payment WHERE command_id IN (SELECT command_id FROM command WHERE user_id=".$command->user_id.")";
			$row = $this->db->fetch_obj($this->db->query($sql));
			$count = (int) $row->nb;
			
			$mProducts = new ProductLms();
			
			foreach($lines as $line)
			{
				// On cast les prix pour éviter une différence de type
				if((float) $line->amount_month_ttc == (float) $amount_ttc)
				{
					// On a trouvé le produit
					$product = $mProducts->getProduct($line->product_id);
					
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
					}
					
					// On compte le nombre de paiement qu'il y a eu pour cet utilisateur
        			$sql = "SELECT count(*) as nb FROM payment WHERE command_id = ".$command->command_id;
        			$tempRow = $this->db->fetch_obj($this->db->query($sql));
        			$tempCount = (int) $tempRow->nb;
			
					if($tempCount == (int) $product->abo_months)
					{
						$sql = "UPDATE command SET paid=UNIX_TIMESTAMP() WHERE command_id=".$command->command_id;
						sql_query($sql);
						
						$this->createInvoice($command->command_id);
					}
				}
			}
		}
	}
	
	public function createInvoice($command_id)
	{
	    $mInvoice = new InvoiceLms();
	    $mInvoice->createInvoice($command_id);
	}
}