<?php

// Hack the PHP_SELF so that the sessions starts correctly
$_SERVER['PHP_SELF'] = str_replace('admin/', '', $_SERVER['PHP_SELF']);

include("data.php");

$GLOBALS['NoDisplay'] = true;

include("../_ManagerFrame.php");

$_SESSION['passwd'] = $Passwd;

$GLOBALS["seeingwhat"] = $GLOBALS['QuizzManager']->GetDataForReport();

ob_start();

include('rapportnominatif.php');

$HTMLMail = ob_get_contents();

ob_end_clean();

include_once(str_replace(basename(__FILE__), 'htmlMimeMail/htmlMimeMail.php', __FILE__));

$mail = new htmlMimeMail();
$mail->setHeadCharset('UTF-8');

$GLOBALS['conf']['SMTP Host'] = 'localhost';
//$GLOBALS['conf']['SMTP User'] = 'your_smtp_login';
//$GLOBALS['conf']['SMTP Pass'] = 'your_pass';

$mail->smtp_params['host'] = $GLOBALS['conf']['SMTP Host'];
if (!empty($GLOBALS['conf']['SMTP User']))		$mail->smtp_params['user'] = $GLOBALS['conf']['SMTP User'];
if (!empty($GLOBALS['conf']['SMTP Pass']))		$mail->smtp_params['pass'] = $GLOBALS['conf']['SMTP Pass'];

$mail->setHeader('X-Mailer', 'EasyquizzServer');

$mail->setFrom('no_reply@epistema.com');
$mail->setSubject('[' . $QuizzTitle . '] ' . 'Vos réponses');

$mail->setHtmlCharset('UTF-8');

$TemplateDir = str_replace(basename(__FILE__), '', str_replace('\\', '/', __FILE__));

$mail->setHtml($HTMLMail, '', $TemplateDir);

$toArray[] = $_POST['email_for_report'];

$MailSent = $mail->send($toArray, 'smtp');


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="imagetoolbar" content="no" />
</head>
<body bgcolor="#DDDDDD">

<script type="text/javascript" language="JavaScript">
<!--
<?
if ($MailSent)
	echo '	alert("Votre rapport a été envoyé.");' . "\n";
else
	echo '	alert("Impossible d\'envoyer le rapport. Vérifiez l\'adresse email saisie.");' . "\n";
?>
//-->
</script>
<!--
<?

if (!$MailSent)
{
	echo '<pre>what : <br>' . print_r($mail->errors, true) . '</pre><br>';
}
?>

-->
</body>
</html>
