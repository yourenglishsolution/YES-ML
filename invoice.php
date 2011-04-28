<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

define("IN_DOCEBO", true);
define("_deeppath_", '');
require(dirname(__FILE__).'/base.php');

require(_base_.'/lib/lib.bootstrap.php');
Boot::init(BOOT_USER);

// mise en place des répertoires et chargement des classes
set_include_path('.'
. PATH_SEPARATOR . _base_.'/lib/'
. PATH_SEPARATOR . _base_.'/lib/Zend/'
. PATH_SEPARATOR . get_include_path());

// On insère manuellement l'autoloader
include_once _base_."/lib/Zend/Loader/Autoloader.php";
$autoloader = Zend_Loader_Autoloader::getInstance();

if(!isset($_GET['payment'])) exit();
$payment_id = (int) $_GET['payment'];

include_once(_lms_."/models/PaymentLms.php");
$mPayment = new PaymentLms();
$payment = $mPayment->getPayment($payment_id);

include_once(_lms_."/models/CommandLms.php");
$mCommand = new CommandLms();
$command = $mCommand->getCommand($payment->command_id);

$user = Docebo::user();

if($user->idst != $command->user_id) exit();

$invoice = $mPayment->getInvoice($payment->payment_id);

include_once(_base_."/lib/tools/YesInvoice.php");
$pdf = new YesInvoice();
$pdf->load($invoice->invoice_id);

header('Content-type: application/pdf');
header("Content-Disposition: attachment; filename=\"Facture YES - ".date('d/m/Y', $invoice->crea).".pdf\"");

echo $pdf->render();