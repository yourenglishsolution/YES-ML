<?php
/**
 * Easyquizz Pro admin's page
 *
 * Encoding: ISO-8859-1
 * @package Easyquizz Pro
 * @author Epistema {@link http://www.epistema.com}
 * @copyright Copyright 2001 - 2008, Epistema
 * @filesource
 */

$trans = array_flip(get_html_translation_table(HTML_ENTITIES));

if (!empty($_FILES['fileToUpload']['size']))
{
	@unlink('userdata.php');
	move_uploaded_file($_FILES['fileToUpload']['tmp_name'], 'userdata.php');
}

if (isset($_POST["PurgeDataNow"]))
{
	@unlink('userdata.php');
}

$QuestionAnswers = array();

include_once("data.php");

if (file_exists("userdata.php"))
	include_once("userdata.php");

if (isset($QuestionAnswersSerialized) && $QuestionAnswersSerialized != '')
{
	$QuestionAnswers = array_merge(unserialize(urldecode($QuestionAnswersSerialized)), $QuestionAnswers);
}

if (isset($_GET["savefile"]))
{
	if (file_exists("userdata.php"))
	{
		PrintHeaders('FichierReponses', 'qso');
		readfile("userdata.php");
		exit();
	}
}

// Excel export
if (isset($_GET["exportexcel"]))
{
	include('export_answers.php');
	exit();
}

if (isset($_GET["exportexcel_marks"]))
{
	include('export_marks.php');
	exit();
}

session_name(ereg_replace("[^[:alnum:]]", "", dirname($_SERVER['PHP_SELF'])));
session_start();

if (isset($_POST["Passwd"]))
	$_SESSION['passwd'] = $_POST["Passwd"];

$NotLogged = ($_SESSION['passwd'] != $Passwd);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=UTF-8">
		<title><?=$QuizzTitle?></title>
		<script type="text/javascript" language="JavaScript" src="../_scripts/language.js" ></script>
		<script type="text/javascript" language="JavaScript" src="../_default_lang/en.js"></script>
		<script type="text/javascript" language="JavaScript" src="../_default_lang/<?=$LangFile?>"></script>
		<script type="text/javascript" language="JavaScript" src="../_lang/<?=$LangFile?>"></script>

		<style type="text/css" media="screen"><!--
body { font-size: 12px; font-family: Verdana, Arial, Helvetica, Geneva, Swiss, SunSans-Regular }
.headers  { color: navy; font-weight: bold; font-size: 12px; background-color: silver }
.subheaders { font-size: 10px; background-color: silver }
td { font-size: 10px }
select { font-size: 10px }
input { font-size: 10px }
.AnswersHeaders  { color: white; font-weight: bold; background-color: #8aa5c4 }
.AnswerRow0 { background-color: white }
.AnswerTables { }
.AnswerRow1 { background-color: #E9E9E9 }
--></style>
		<style type="text/css" media="print"><!--
body { font-size: 12px; font-family: Verdana, Arial, Helvetica, Geneva, Swiss, SunSans-Regular }
.headers  { color: navy; font-weight: bold; font-size: 12px; background-color: silver }
.subheaders { font-size: 10px; background-color: silver }
td { font-size: 10px;}
.AnswerTables { page-break-inside : avoid }
.AnswersHeaders  { color: white; font-weight: bold; background-color: #8aa5c4; border: solid 0.2mm black }
.AnswerRow0 { background-color: white; border: solid 0.2mm black  }
.AnswerRow1 { background-color: #E9E9E9; border: solid 0.2mm black  }
--></style>
	<meta http-equiv="imagetoolbar" content="no" />
	</head>

	<body bgcolor="#ffffff" leftmargin="0" marginheight="0" marginwidth="0" topmargin="0">
	<table width="640" border="0" cellpadding="0" cellspacing="0" height="57">
		<tr height="57">
			<td bgcolor="#8cc919" width="82" height="57"><IMG SRC="_images/LogoEasyQuizz.gif" width="82" height="57"></td>
			<td align="center" bgcolor="#00a8db" height="57"><font size="3" color="white"><span epiLang="QuizReport">Rapport d'utilisation du questionnaire en ligne&nbsp;:</span></font><br>
			<font size="3" color="black"><b><?=$QuizzTitle?></b></font></td>
			<td align="right" bgcolor="#00a8db" width="70" height="57"><img src="_images/LogoE.gif" width="70" height="57"></td>
		</tr>
	</table>
	<table width="640" border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td>
<?
if ($NotLogged)
{
?>
<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
<form action="admin.php" method="post" name="FormName">
	<div align="center">
	<span epiLang="PleaseEnterPasswordToProceedToReport">Veuillez saisir le mot de passe pour acc&eacute;der &agrave; la partie administration&nbsp;:</span><p>
	<input type="password" name="Passwd" size="24">&nbsp;<input type="submit" name="submitButtonName" epiLang="OKButton" value="Ok"></div>
</form>
					<?
}
else
{
?>
					<table class="noprint" width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top"><?
	$bHasCredentials = false;
	foreach ($QuestionAnswers as $k => $answer)
	{
		if (!empty($answer["UserFirstname"]) ||
				!empty($answer["Username"]) ||
				!empty($answer["UserId"]))
		{
			$bHasCredentials = true;
			break;
		}
	}

	if ($bHasCredentials)
	{
?>
								<form action="rapportnominatif.php" method="get" name="FormName">
									<span epiLang="ViewANominativeReport">Voir un rapport nominatif&nbsp;:</span><br>
									<select name="seeingwho" size="1">
										<option value="tous" epiLang="OptionAllTheAnswers">- Toutes les r&eacute;ponses -</option><? 		foreach ($QuestionAnswers as $k => $answer)
		{
			$NameParts = array();
			if (!empty($answer["UserFirstname"])) $NameParts[] = $answer["UserFirstname"];
			if (!empty($answer["Username"])) $NameParts[] = strtoupper($answer["Username"]);
			if (!empty($answer["UserId"])) $NameParts[] = '(' . $answer["UserFirstname"] . ')';

			if (empty($NameParts))
				continue;

			echo '<option value="'.$k.'">'.implode(' ', $NameParts) .'</option>' . "\n";
		}

?>
									</select>&nbsp;<input type="submit" name="submitButtonName" value="OK">
								</form>
								<?
								}
?><a href="index.php"><span epiLang="BackToMainReport">Revenir au <b>rapport principal</b></span></a></td>
						</tr>
					</table>
					<hr align="center" noshade size="1" width="80%">
					<p></p>
					<p></p>
					<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="black">
						<tbody>
							<tr>
								<td class="headers" colspan="2"><span epiLang="AnswerFile">Fichier des r&eacute;ponses</span></td>
							</tr>
							<tr>
								<td colspan="2" valign="top" bgcolor="white">
									<form action="admin.php" method="get" name="FormName">
										<input type="hidden" name="savefile" value="true">
										<table border="0" cellspacing="0" cellpadding="0" width="500" align="center">
											<tr>
												<td align="center" width="25"><img src="_images/IconSave.gif" alt="" width="21" height="18" border="0"></td>
												<td><span epiLang="SaveTheAnswerFileField">Enregistrer le fichier des r&eacute;ponses :</span></td>
												<td align="right"><input type="submit" name="submitbttn" epiLang="SaveButton" value="Enregistrer"></td>
											</tr>
										</table>
									</form>
									<script type="text/javascript">
    <!--
function CheckFileFormat(fileControl, bSubmitPressed)
{
	sFilePath = fileControl.value;

	if (sFilePath.replace(/\s/g, '') != '')
	{
		var iLastIndex = sFilePath.lastIndexOf('.', sFilePath.length - 1);
		var sFileExtension = sFilePath.substr(iLastIndex + 1);

		if ((sFileExtension != 'qso'))
		{
			window.alert(EpiLang.TheFileMustBeAnEasyquizzAnswerFile);

			if (bSubmitPressed)
				return false;
		}
	}
	else
		return !bSubmitPressed;
}
    //-->
    </script>
									<form action="admin.php" enctype="multipart/form-data" method="post" name="formUpload" onsubmit="return CheckFileFormat(this.fileToUpload, true);">
										<table border="0" cellspacing="0" cellpadding="0" width="500" align="center">
											<tr>
												<td align="center" width="25"><img src="_images/IconOpen.gif" alt="" width="21" height="18" border="0"></td>
												<td colspan="2"><span epiLang="RestoreSavedFile">Restaurer un fichier de r&eacute;ponses enregistr&eacute;&nbsp;:</span></td>
											</tr>
											<tr>
												<td width="25"></td>
												<td colspan="2"><input style="width=100%" type="file" name="fileToUpload" size="80" onchange="CheckFileFormat(this, false);"></td>
											</tr>
											<tr>
												<td width="25"></td>
												<td></td>
												<td align="right"></td>
											</tr>
											<tr>
												<td width="25"></td>
												<td></td>
												<td align="right"><input type="submit" name="submitbttn" epiLang="RestoreButton" value="Restaurer"></td>
											</tr>
										</table>
									</form>
									<form action="admin.php" method="get" name="FormName">
										<input type="hidden" name="purgefile" value="true">
										<table border="0" cellspacing="0" cellpadding="0" width="500" align="center">
											<tr>
												<td align="center" width="25"><img src="_images/IconRemove.gif" alt="" width="21" height="19" border="0"></td>
												<td><span epiLang="ResetTheAnswerFile">Remettre &agrave; z&eacute;ro le fichier des r&eacute;ponses :</span></td>
												<td align="right"><input type="submit" name="submitbttn" epiLang="ResetButton" value="Remettre &agrave; z&eacute;ro"></td>
											</tr>
										</table>
									</form>
								</td>
							</tr>
						</tbody>
					</table>
					<p></p>
					<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="black">
						<tbody>
							<tr>
								<td class="headers" colspan="2"><span epiLang="ExportTheAnswerFileInCSV">Export CSV des r&eacute;ponses</span></td>
							</tr>
							<tr>
								<td colspan="2" valign="top" bgcolor="white">
									<p><a href="admin.php?exportexcel=true"><span epiLang="ExportTheAnswerFileInCSVLink">Export des r&eacute;ponses au format CSV&nbsp;: Cliquer ici.</span></a><br></p>
									<p><a href="admin.php?exportexcel_marks=true"><span epiLang="ExportTheMarksInCSVLink">Export des notes au format CSV&nbsp;: Cliquer ici.</span></a><br></p>
									<p><span epiLang="IfYouAreExperiencedInSpreadsheets">Si vous &ecirc;tes un utilisateur exp&eacute;riment&eacute; d'un tableur, vous pouvez utiliser le format CSV&nbsp;pour retraiter les r&eacute;ponses.<br><b>Note&nbsp;: </b>Afin d'assurer une bonne importation des donn&eacute;es dans Microsoft Excel, il est n&eacute;cessaire d'enregistrer le fichier csv sur le disque puis de l'ouvrir dans Excel en utilisant la fonction &quot;Ouvrir&quot; et en sp&eacute;cifiant &quot;Fichier CSV&quot;.</span></p>
								</td>
							</tr>
						</tbody>
					</table>
					<br>
					<hr align="center" noshade size="1" width="80%">
					<br>
				<table width="630" border="0" cellspacing="1" cellpadding="3" bgcolor="black">
					<tbody>
						<tr>
							<td class="headers" colspan="2"><span epiLang="UsageStatistics">Statistiques d'utilisation</span></td>
						</tr>
						<tr>
							<td valign="top" bgcolor="white">
								<p><span epiLang="AnswersCountField">Nombre de r&eacute;ponses&nbsp;:</span> <b><? echo count($QuestionAnswers); ?></b></p>
								<p></p>
							</td>
							<td valign="top" bgcolor="white">
<?
	if (count($QuestionAnswers) > 0)
	{
		echo '<span epiLang="LastTenAnswersField">Liste des 10 derni&egrave;res r&eacute;ponses&nbsp;:</span>';

		$StatRows = array();

		foreach ($QuestionAnswers as $answer)
		{
			$StatRows[$answer["when"]][] = $answer["who"];

			if ($QuestionDefinition[0]["Type"] == "CREDENTIALS")
			{
				$StatRows[$answer["when"]][] = $answer["UserFirstname"] . ' ' . strtoupper($answer["Username"]);
			}
		}

		krsort($StatRows, SORT_NUMERIC);

		echo '<table class="AnswerTables" border="0" cellspacing="1" cellpadding="2"  align="center" bgcolor="black"><tr><td class="AnswersHeaders" ><span epiLang="Date">Date</span></td>';
		echo '<td class="AnswersHeaders" align="center"><span epiLang="Host">Host</span></td>';
		if ($QuestionDefinition[0]["Type"] == "CREDENTIALS")
		{
			echo '<td class="AnswersHeaders" align="center"><span epiLang="UserName">Nom</span></td>';
		}
		echo '</tr>';

		$TextRow = 0;
		foreach ($StatRows as $when => $row)
		{
			echo '<tr><td class="AnswerRow'.($TextRow % 2).'">'.date("d/m/Y G:i:s", $when).'</td><td class="AnswerRow'.($TextRow % 2).'">'.$row[0].'</td>';

			if ($QuestionDefinition[0]["Type"] == "CREDENTIALS")
			{
				echo '<td class="AnswerRow'.($TextRow % 2).'">'.$row[1].'</td>';
			}

			echo '</tr>';
			$TextRow ++;

			if ($TextRow == 10) break;
		}

		echo '</table>';
	}
?>
							</td>
						</tr>
					</tbody>
				</table>

<?
}

if (isset($_GET["purgefile"]))
{
	echo '<form style="border: 0px; font-size: 1px; color: #FFFFFF; background-color: #FFFFFF" action="admin.php" method="post" name="ConfirmPurge">';
	echo '<input type="hidden" name="PurgeDataNow" value="true"></form>';
	echo "<script type=\"text/javascript\" language=\"JavaScript\">";
	echo "if (confirm(EpiLang.AreYouSureResetAllTheData)) document.ConfirmPurge.submit(); </script>";
}



function PrintHeaders($filename = "", $ext = 'csv')
{
	// send the headers:
	if (!empty($_SERVER['HTTP_USER_AGENT']))
		$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
	else if (!empty($HTTP_SERVER_VARS['HTTP_USER_AGENT']))
		$HTTP_USER_AGENT = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
	else if (!isset($HTTP_USER_AGENT))
		$HTTP_USER_AGENT = '';

	// 2. browser and version
	if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version))
		define('PMA_USR_BROWSER_AGENT', 'OPERA');
	else if (ereg('MSIE ([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version))
		define('PMA_USR_BROWSER_AGENT', 'IE');
	else if (ereg('OmniWeb/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version))
		define('PMA_USR_BROWSER_AGENT', 'OMNIWEB');
	else if (ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version))
		define('PMA_USR_BROWSER_AGENT', 'MOZILLA');
	else if (ereg('Konqueror/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version))
		define('PMA_USR_BROWSER_AGENT', 'KONQUEROR');
	else
		define('PMA_USR_BROWSER_AGENT', 'OTHER');

	if (PMA_USR_BROWSER_AGENT == 'IE' || PMA_USR_BROWSER_AGENT == 'OPERA')
		$mime_type = 'application/octetstream';
	else
		$mime_type = 'application/octet-stream';

	$now = gmdate('D, d M Y H:i:s') . ' GMT';

	header('Content-Type: ' . $mime_type);
	header('Expires: ' . $now);

	if (PMA_USR_BROWSER_AGENT == 'IE')
	{
		if ($filename != "")
			header('Content-Disposition: inline; filename="' . $filename . '.' . $ext . '"');
		else
			header('Content-Disposition: inline');

		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	}
	else
	{
		if ($filename != "")
			header('Content-Disposition: attachment; filename="' . $filename . '.' . $ext . '"');
		else
			header('Content-Disposition: attachment');

		header('Pragma: no-cache');
	}
}

function PrepareTextToCSVExport($str)
{
	global $trans;

	if (isset($GLOBALS['Encoding']) && $GLOBALS['Encoding'] == 'UTF-8')
		$str = utf8_decode($str);

	return strip_tags(str_replace('"', '\"', strtr($str, $trans)));
}


	?>



</td>
		</tr>
	</table>
	</body>
	<script type="text/javascript" language="JavaScript">
		EpiLangManager.TranslatePage(document);
	</script>
</html>