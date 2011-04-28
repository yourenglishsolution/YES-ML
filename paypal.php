<?php

define("IN_DOCEBO", true);
define("_deeppath_", '');
require(dirname(__FILE__).'/base.php');
define('PAYPAL_EMAIL', _PAYPAL_ACCOUNT_MAIL);

require(_base_.'/lib/lib.bootstrap.php');
Boot::init(BOOT_USER);

include_once(_lms_."/models/CommandLms.php");
include_once(_lms_.'/class/class.phpmailer.php');

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

if(isset($_GET['op']))
{
	// Si on arrive ici c'est que le traitement doit se faire ($_GET['op'] == ok)
	
	// On interroge Paypal pour valider la requête
	$data = $_POST;
	$req = 'cmd=_notify-validate';
	
	foreach($_POST as $key => $value)
	{
		if(get_magic_quotes_gpc()) $value = stripslashes($value);
		$value = urlencode($value);
		$req .= "&$key=$value"; // A voir si le & ne doit pas être remplacé par &amp;
	}
	
	$url = str_replace(array('https://', 'http://'), '', _PAYPAL_ACCOUNT_URL);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
	$result = curl_exec($ch);
	
	if(strcmp($result, "VERIFIED") == 0)
	{
		if(PAYPAL_EMAIL == $data['receiver_email'] && isset($data['custom']))
		{
			if(isset($data['payment_status']) && $data['payment_status'] == 'Completed')
			{
				$custom = explode('.', $data['custom']);
				
				if(count($custom) == 2)
				{
				    ob_start();
				    
					$mCommand = new CommandLms();
					
					$user_id = $custom[0];
					$product_id = $custom[1];
					$amount_ttc = (float) $data['mc_gross']; // Montant de l'abonnement
					
					$command = $mCommand->getUserProductCommand($user_id, $product_id);
					
					if(!$command)
					{
						// Aucune commande n'existe pour ce produit, on en crée une
						$command_id = $mCommand->createCommand($user_id, array($product_id));
						$command = $mCommand->getCommand($command_id);
					}
					
					$mCommand->receivePayment($command->command_id, $amount_ttc, $data);
					
					$user = $mCommand->getUser($command->command_id);
					
					$template = file_get_contents(_MAILS_TEMPLATE_PATH.'confirm_command.php');
					$template = str_replace('%name%', $user->firstname, $template);
					$template = utf8_decode($template); // On gère les accents
					
					$mail = new PHPMailer();
					$mail->AddAddress($user->email); // Destinataire
					$mail->SetFrom('microlearning@yourenglishsolution.fr', 'Microlearning Team'); // Expéditeur
					$mail->AddReplyTo('microlearning@yourenglishsolution.fr', 'Microlearning Team'); // Adresse de réponse
					$mail->Subject = 'YES Microlearning - Confirmation de votre commande';
					$mail->MsgHTML($template);
					$mail->AltBody = strip_tags($template);
					$mail->Send();
				}
				else
				{
					mail('j.pouillard@effigie-creations.com', 'debug', 'mauvais custom');
				}
			}
		}
		else
		{
			// L'email du compte n'est pas identique => tentative de fraude possible
			mail('j.pouillard@effigie-creations.com', 'debug', 'mauvaise email');
		}
	}
	else
	{
		// Erreur HTTP
		mail('j.pouillard@effigie-creations.com', 'debug', 'erreur http');
	}
	
	curl_close($ch);
}

Boot::finalize();