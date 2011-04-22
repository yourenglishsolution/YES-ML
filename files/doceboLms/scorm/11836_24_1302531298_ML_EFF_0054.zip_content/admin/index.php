<?php
/**
 * Easyquizz Pro admin's page
 *
 * Encoding: ISO-8859-1
 * @package Easyquizz Pro
 * @author Epistema {@link http://www.epistema.com}
 * @copyright Copyright 2001 - 2006, Epistema
 * @filesource
 */

$QuestionAnswers = array();

include_once("data.php");

if (file_exists("userdata.php"))
	include_once("userdata.php");

if (isset($QuestionAnswersSerialized) && $QuestionAnswersSerialized != '')
	$QuestionAnswers = array_merge(unserialize(urldecode($QuestionAnswersSerialized)), $QuestionAnswers);

$GLOBALS['FROMDATE'] = false;
$GLOBALS['TODATE'] = false;

if (!empty($_GET['DateFrom']))
	$GLOBALS['FROMDATE'] = MakeDate($_GET['DateFrom']);
else
	$_GET['DateFrom'] = '';

if (!empty($_GET['DateTo']))
	$GLOBALS['TODATE'] = MakeDate($_GET['DateTo'], 1);
else
	$_GET['DateTo'] = '';

$QuestionAnswers = array_filter($QuestionAnswers, 'filterDates');

if (isset($_POST["removeWho"]))
{
	array_splice($QuestionAnswers, $_POST["removeWho"], 1);
	$datafile = fopen('userdata.php', 'w');
	$str = "<? \n".' $QuestionAnswersSerialized = "' . urlencode(serialize($QuestionAnswers)) . '" ?>';
	fwrite($datafile, $str);
	fclose($datafile);
	header('Location: index.php?go');
	exit();
}

session_name(ereg_replace("[^[:alnum:]]", "", dirname($_SERVER['PHP_SELF'])));
session_start();

if (!empty($Passwd))
{
	if (isset($_POST["Passwd"]))
		$_SESSION['passwd'] = $_POST["Passwd"];

	$NotLogged = ($_SESSION['passwd'] != $Passwd);
}
else
	$NotLogged = false;

// declare the colors (see image.php)
function GetChartColor($index, $bIncludeHash = true)
{
	if (empty($GLOBALS['ChartsColors']))
	{
		$GLOBALS['ChartsColors'] = array();

		// Fill the array with some more values:
		$GLOBALS['ChartsColors'][] = "#".dechex( 51).dechex( 51).dechex(153);
		$GLOBALS['ChartsColors'][] = "#".dechex( 51).dechex(153).dechex(255);
		$GLOBALS['ChartsColors'][] = "#".dechex( 51).dechex(153).dechex(153);
		$GLOBALS['ChartsColors'][] = "#".dechex(255).dechex( 51).'00';
		$GLOBALS['ChartsColors'][] = "#".dechex(255).dechex(153).dechex( 51);
		$GLOBALS['ChartsColors'][] = "#".dechex(255).dechex(204).dechex( 51);


		$GLOBALS['ChartsColors'][] = "#".'00'.dechex( 51).dechex(153);
		$GLOBALS['ChartsColors'][] = "#".'00'.dechex(153).dechex(255);
		$GLOBALS['ChartsColors'][] = "#".'00'.dechex(153).dechex(153);
		$GLOBALS['ChartsColors'][] = "#".'00'.dechex( 51).'00';
		$GLOBALS['ChartsColors'][] = "#".'00'.dechex(153).dechex( 51);
		$GLOBALS['ChartsColors'][] = "#".'00'.dechex(204).dechex( 51);

		$GLOBALS['ChartsColors'][] = "#".dechex(153).dechex( 51).dechex(153);
		$GLOBALS['ChartsColors'][] = "#".dechex(153).dechex(153).dechex(255);
		$GLOBALS['ChartsColors'][] = "#".dechex(153).dechex(153).dechex(153);
		$GLOBALS['ChartsColors'][] = "#".dechex(153).dechex( 51).'00';
		$GLOBALS['ChartsColors'][] = "#".dechex(153).dechex(153).dechex( 51);
		$GLOBALS['ChartsColors'][] = "#".dechex(153).dechex(204).dechex( 51);
	}

	if ($bIncludeHash)
		return $GLOBALS['ChartsColors'][$index % count($GLOBALS['ChartsColors'])];
	else
		return str_replace('#', '', $GLOBALS['ChartsColors'][$index % count($GLOBALS['ChartsColors'])]);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html;charset=UTF-8">
		<script type="text/javascript" language="JavaScript" src="../_scripts/language.js" ></script>
		<script type="text/javascript" language="JavaScript" src="../_default_lang/en.js"></script>
		<script type="text/javascript" language="JavaScript" src="../_default_lang/<?=$LangFile?>"></script>
		<script type="text/javascript" language="JavaScript" src="../_lang/<?=$LangFile?>"></script>
		<title><?=$QuizzTitle?></title>
		<style type="text/css" media="screen"><!--
body { font-size: 12px; font-family: Verdana, Arial, Helvetica, Geneva, Swiss, SunSans-Regular }
td { font-size: 10px }
select { font-size: 10px }
input { font-size: 10px }
.AnswersHeaders  { color: white; font-weight: bold; background-color: #8aa5c4 }
.AnswerRow0 { background-color: white }
.AnswerTables { }
.AnswerRow1 { background-color: #E9E9E9 }
.noprint { }
table.QuestionTable  { border: solid 1px black; border-collapse:collapse }
th.headers   { color: navy; font-weight: bold; font-size: 12px; background-color: silver; border: solid 1px black }
th.subheaders  { font-size: 10px; background-color: silver; border: solid 1px black }
.QuestionTable table  { border: solid 1px black; border-collapse: collapse  }
.QuestionTable td  { border: solid 1px black }
table.noBorder  { border: none 1px }
td.noBorder  { border: none 1px }
--></style>
		<style type="text/css" media="print"><!--
body { font-size: 12px; font-family: Verdana, Arial, Helvetica, Geneva, Swiss, SunSans-Regular }
table.QuestionTable  { border: solid 1px black; border-collapse:collapse }
th.headers   { color: navy; font-weight: bold; font-size: 12px; background-color: silver; border: solid 1px black }
th.subheaders  { font-size: 10px; background-color: silver; border: solid 1px black }
.QuestionTable table  { border: solid 1px black; border-collapse: collapse  }
.QuestionTable td  { border: solid 1px black }
table.noBorder  { border: none 1px }
td.noBorder  { border: none 1px }
td { font-size: 10px;}
.AnswerTables { page-break-inside : avoid }
.AnswersHeaders  { color: white; font-weight: bold; background-color: #8aa5c4; border: solid 0.2mm black }
.AnswerRow0 { background-color: white; border: solid 0.2mm black  }
.AnswerRow1 { background-color: #E9E9E9; border: solid 0.2mm black  }
.noprint { visibility: hidden }
--></style>
	<meta http-equiv="imagetoolbar" content="no" />
	</head>

	<body bgcolor="#ffffff" leftmargin="0" marginheight="0" marginwidth="0" topmargin="0">
	<table width="640" border="0" cellpadding="0" cellspacing="0" height="57">
		<tr height="57">
			<td bgcolor="#8cc919" width="82" height="57"><?
$UseDefaultLogo = true;
if (!empty($LogoImage))
{
	$LogoImage = basename($LogoImage);
	if (file_exists($LogoImage))
	{
		echo '<img src="'.$LogoImage.'" >';
		$UseDefaultLogo = false;
	}
}
if ($UseDefaultLogo)
	echo '<img src="_images/LogoEasyQuizz.gif" width="82" height="57">';
			?></td>
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
<form action="index.php" method="post" name="FormName">
	<div align="center">
		<span epiLang="PleaseEnterPasswordToProceedToReport">Veuillez saisir le mot de passe pour acc&eacute;der &agrave; la partie administration&nbsp;:</span><p>
		<input type="password" name="Passwd" size="24">&nbsp;<input type="submit" name="submitButtonName" epiLang="OKButton" value="Ok">
	</div>
</form>

<?
}
else
{
?>


<table class="noprint" width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
							<td valign="top">
<?
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
			<span epiLang="ViewANominativeReport">Voir un rapport nominatif&nbsp;:</span><br><select name="seeingwho" size="1">
				<option value="tous" epiLang="OptionAllTheAnswers">- Toutes les r&eacute;ponses -</option>
<?
		foreach ($QuestionAnswers as $k => $answer)
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
			</select><input type="submit" name="submitButtonName" epiLang="OKButton" value="OK">
		</form>
<?
	}
?>
		</td>
							<td valign="top" align="right">
								<table class="noprint" border="0" cellspacing="0" cellpadding="3">
			<tr>
				<td valign="middle" align="right">
					<a href="javascript:window.print()"><span epiLang="PrintTheReport"><b>Imprimer</b> le rapport</span></a>
				</td>
				<td valign="middle" align="right">
					<a href="javascript:window.print()"><img src="_images/print.gif" alt="" width="26" height="22" align="absmiddle" border="0"></a>
				</td>
	</tr>
		<tr>
			<td valign="middle" align="right">
				<a href="admin.php"><span epiLang="GoToAdministrationPage">Acc&eacute;der &agrave; la <b>page d'administration</b></span></a>
				</td>
				<td valign="middle" align="right">
				<a href="admin.php"><img src="_images/admin.gif" alt="" width="30" height="30" align="absmiddle" border="0"></a>
			</td>
		</tr>
					</table>
							</td>
						</tr>
</table>

<form method="GET" name="FormName">
	<fieldset>
		<legend><span epiLang="FilterOnTheAnswers">Filtre sur les r&eacute;ponses&nbsp;:</span></legend>

		<span epiLang="DisplayAnswersBetweenTheFollowingDates">Afficher les r&eacute;ponses entre les dates suivantes&nbsp;:</span><br>
		<span epiLang="StartDate">Date de d&eacute;but :</span> <input type="text" value="<?=$_GET['DateFrom'] ?>" name="DateFrom" size="12" />		<i>[dd/mm/yyyy]</i><br>
		<span epiLang="EndDate">Date de fin :</span> <input type="text" name="DateTo" value="<?=$_GET['DateTo'] ?>" size="12" /> <i>[dd/mm/yyyy]</i><br>
		<span epiLang="LeaveEmptyIfNoRestriction">Laisser vide si vous ne voulez pas de limite.</span>

<input type="submit" name="submitButtonName" epiLang="RefreshButton" value="Rafra&icirc;chir" />
	</fieldset>
</form>
					<span epiLang="NumberOfAnswersField">Nombre de r&eacute;ponses&nbsp;:</span> <b><? echo count($QuestionAnswers); ?></b>
					<p><span epiLang="ListOfQuestionsField">Liste des questions&nbsp;:</span></p>
					<?
	$i = 1;
	$QuestionNumber = 1;

	foreach ($QuestionDefinition as $Question)
	{
		if ($Question["Type"] != "CREDENTIALS" &&
				$Question["Type"] != "EXPLANATION")
		{
			$bShowTenEntriesLink = false;

			$QuestionType = "";
			switch ($Question["Type"])
			{
				case 'FLASH'		: $QuestionType = '<span epiLang="FlashTypeName">Flash question</span>'; break;
				case 'READING_ASSESSMENT'		: $QuestionType = '<span epiLang="ReadingAssessmentTypeName">Reading assessment question</span>'; break;
				case 'SHUNTING' : $QuestionType = '<span epiLang="ShuntingQuestionTypeName">Shunting question</span>'; break;
				case 'SURVEYQCU':
				case 'QCU'    	: $QuestionType = '<span epiLang="QCUTypeName">Question &agrave; choix unique</span>'; break;
				case 'SURVEYQCM':
				case 'QCM'    	: $QuestionType = '<span epiLang="QCMTypeName">Question &agrave; choix multiple</span>'; break;
				case 'SURVEYTABQCU':
				case 'TABQCU' 	: $QuestionType = '<span epiLang="TABQCUTypeName">Question matricielle &agrave; choix unique</span>'; break;
				case 'SURVEYTABQCM':
				case 'TABQCM' 	: $QuestionType = '<span epiLang="TABQCMTypeName">Question matricielle &agrave; choix multiple</span>'; break;
				case 'TAT'    	: $bShowTenEntriesLink = true; $QuestionType = '<span epiLang="TATTypeName">Question &agrave; trous</span>'; break;
				case 'MATCH'  	: $bShowTenEntriesLink = true; $QuestionType = '<span epiLang="MATCHTypeName">Question d\'appariement</span>'; break;
				case 'DRAGDROP'	: $bShowTenEntriesLink = true; $QuestionType = '<span epiLang="DRAGDROPTypeName">Question glisser/d&eacute;poser</span>'; break;
				case 'SORT'   	: $bShowTenEntriesLink = true; $QuestionType = '<span epiLang="SORTTypeName">Question d\'ordonnancement</span>'; break;
				case 'FORM'   	: $bShowTenEntriesLink = true; $QuestionType = '<span epiLang="FORMTypeName">Question formulaire</span>'; break;
				case 'TEXT'   	: $bShowTenEntriesLink = true; $QuestionType = '<span epiLang="TEXTTypeName">Question texte</span>'; break;
			}

			if (($Question["Type"] == "TABQCU" ||
					 $Question["Type"] == "TABQCM" ||
					 $Question["Type"] == "SURVEYTABQCU" ||
					 $Question["Type"] == "SURVEYTABQCM") &&
					$Question["HasComments"])
				$bShowTenEntriesLink = true;

			echo '<table width="630" border="0" cellspacing="0" cellpadding="3" class="QuestionTable">' . "\n";
			echo '	<tr>';
			echo '		<th class="headers" colspan="2">'.InsertInlineImages($Question["Question"]).'</th>';
			echo '	</tr>' . "\n";
			echo '	<tr>';
			echo '		<th class="subheaders" align="center"><span epiLang="QuestionNumber">Question n&deg;</span>'.$QuestionNumber.'</th>';
			echo '		<th class="subheaders" width="520"><b><span epiLang="TypeField">Type&nbsp;: </span></b> '. $QuestionType .'</th>';
			echo '	</tr>' . "\n";
			echo '	<tr>';
			echo '		<td colspan="2" align="center" bgcolor="white">	';

			if ($Question["Type"] == "QCU" ||
					$Question["Type"] == "QCM" ||
					$Question["Type"] == "SURVEYQCU" ||
					$Question["Type"] == "SURVEYQCM" ||
					$Question["Type"] == "SHUNTING")
			{
				if (count($QuestionAnswers) > 0)
				{
					$v = array();

					foreach ($QuestionDefinition[$i - 1]["Answers"] as $QuestionRow)
					{
						$v[] = array("value" => 0,
												 "string" => $QuestionRow);
					}

					$TextAnswers = array();

					$IsQCM = false;

					$Total = 0;
					$TextTotal = 0;
					foreach ($QuestionAnswers as $answer)
					{
						$ThisAnswer = GetAnswersForQuestion($i, $QuestionDefinition[$i - 1], $answer);

						if ($ThisAnswer !== false)
						{
							if (isset($ThisAnswer['checked']) && is_array($ThisAnswer['checked']))
								foreach ($ThisAnswer['checked'] as $AnswerItems)
								{
									if (!isset($v[$AnswerItems]["value"])) $v[$AnswerItems]["value"] = 0;
									$v[$AnswerItems]["value"] ++;
									$Total++;
									$IsQCM = true;
								}
							else if (isset($ThisAnswer['checked']) && is_numeric($ThisAnswer['checked']))
							{
								if (!isset($v[$ThisAnswer['checked']]["value"])) $v[$ThisAnswer['checked']]["value"] = 0;

								$v[$ThisAnswer['checked']]["value"] ++;
								$Total++;
							}

							if (isset($ThisAnswer['more']) && is_array($ThisAnswer['more']))
							{
								foreach ($v as $ChoiceIndex => $Choice)
								{
									if (!isset($ThisAnswer['more'][$ChoiceIndex + 1]))
										$ThisAnswer['more'][$ChoiceIndex + 1] = '';
								}

								ksort($ThisAnswer['more'], SORT_NUMERIC);
								$thiskey = implode('@@@@', $ThisAnswer['more']);

								if (!isset($TextAnswers[$thiskey])) $TextAnswers[$thiskey] = 0;
								$TextAnswers[$thiskey] ++;
								$TextTotal++;
							}
						}
					}

					echo '<table width="100%" border="0" cellspacing="0" class="noBorder" cellpadding="0" align="center">' . "\n";
					echo '	<tr>';
					echo '		<td class="noBorder"  valign="top">';

					$TextRow = 0;
					$CamembertSections = array();
					foreach($v as $section)
						$CamembertSections[] = 'v[]=' .$section["value"]. '&t[]='.$section["value"]. '&c[]=' . rawurlencode(GetChartColor($TextRow++));

					echo '<img src="stats/camembert.php?ImageWidth=300&ImageHeight=240&'.implode('&', $CamembertSections).'" alt="Camembert" border="0">' . "\n";

					echo '</td><td class="noBorder" align="right" valign="top">';

					echo '<table align="right" border="0" cellspacing="0" cellpadding="2" bgcolor="black"><tr><td width="10" class="AnswersHeaders" align="center">&nbsp;</td><td class="AnswersHeaders" align="center">&nbsp;</td>';
					echo '<td class="AnswersHeaders" align="center"><span epiLang="RateQuantity">Pourcentage&nbsp;(quantit&eacute;)</span></td>';
					echo '</tr>';
					$TextRow = 0;
					foreach($v as $section)
					{
						if ($Total > 0)
							echo '	<tr><td width="12" align="center" class="AnswerRow'.($TextRow % 2).'"><img src="stats/imagecarre.php?color='.rawurlencode(GetChartColor($TextRow)).'" BORDER=0></td><td class="AnswerRow'.($TextRow % 2).'">' . InsertInlineImages($section["string"]) . '</td><td align="center" class="AnswerRow'.($TextRow % 2).'">' . number_format($section["value"] * 100 / $Total, 2) . '% (' .$section["value"] . ')</td></tr>';
						else
							echo '	<tr><td width="12" align="center" class="AnswerRow'.($TextRow % 2).'"><img src="stats/imagecarre.php?color='.rawurlencode(GetChartColor($TextRow)).'" BORDER=0></td><td class="AnswerRow'.($TextRow % 2).'">' . InsertInlineImages($section["string"]) . '</td><td align="center" class="AnswerRow'.($TextRow % 2).'">' . number_format(0, 2) . '% (' .$section["value"] . ')</td></tr>';

						$TextRow ++;
					}
					echo '</table>';


					echo '</td></tr>' . "\n";
					echo '</table>';

					if (count($TextAnswers) > 0)
					{
						$bShowTenEntriesLink = true;

						arsort($TextAnswers, SORT_NUMERIC);

						echo '<br><br><table border="0" cellspacing="0" cellpadding="2" bgcolor="black"><tr><td colspan="'.count($v).'" class="AnswersHeaders" align="center"><span epiLang="UserEntry">Entr&eacute;e de l\'utilisateur</span></td>';
						echo '<td class="AnswersHeaders" align="center"><span epiLang="RateQuantity">Pourcentage&nbsp;(quantit&eacute;)</span></td>';
						echo '</tr><tr>';

						$TextRow = 0;
						foreach($v as $section)
						{
							echo '	<td class="AnswersHeaders" align="center"><img align="absmiddle" src="stats/imagecarre.php?color='.rawurlencode(GetChartColor($TextRow)).'" border="0"> ' . InsertInlineImages($section["string"]) . '</td>';

							$TextRow ++;
						}

						echo '<td class="AnswersHeaders">&nbsp;</td></tr>';

						$TextRow = 0;
						foreach ($TextAnswers as $answer => $nb)
						{
							if ($TextTotal > 0)
								echo '<tr><td class="AnswerRow'.($TextRow % 2).'">'.str_replace('@@@@', '</td><td class="AnswerRow'.($TextRow % 2).'">', InsertInlineImages($answer)).'</td><td align="center" class="AnswerRow'.($TextRow % 2).'">'.number_format($nb * 100 / $TextTotal, 2).'% ('.$nb.')</td></tr>';
							else
								echo '<tr><td class="AnswerRow'.($TextRow % 2).'">'.str_replace('@@@@', '</td><td class="AnswerRow'.($TextRow % 2).'">', InsertInlineImages($answer)).'</td><td align="center" class="AnswerRow'.($TextRow % 2).'">'.number_format(0, 2).'% ('.$nb.')</td></tr>';
							$TextRow ++;
							if ((!isset($_GET["showall"]) || $_GET["showall"] != "true") && $TextRow == 10) break;
						}

						echo '</table><br>';
					}

				}
				else
				{
					echo '<span epiLang="NoAnswerYet">Pas encore de r&eacute;ponses</span><br>';
				}
			}
			else if ($Question["Type"] == "TEXT")
			{
				$TextAnswers = array();

				if (count($QuestionAnswers) > 0)
				{
					$Total = 0;

					foreach ($QuestionAnswers as $answer)
					{
						$ThisAnswer = GetAnswersForQuestion($i, $QuestionDefinition[$i - 1], $answer);

						if ($ThisAnswer !== false)
						{
							if (!isset($TextAnswers[trim($ThisAnswer)])) $TextAnswers[trim($ThisAnswer)] = 0;

							$TextAnswers[trim($ThisAnswer)] ++;
							$Total ++;
						}
					}

					arsort($TextAnswers, SORT_NUMERIC);

					echo '<table border="0" cellspacing="0" cellpadding="2" bgcolor="black"><tr><td class="AnswersHeaders" align="center"><span epiLang="UserEntry">Entr&eacute;e de l\'utilisateur</span></td>';
					echo '<td class="AnswersHeaders" align="center"><span epiLang="RateQuantity">Pourcentage&nbsp;(quantit&eacute;)</span></td>';
					echo '</tr>';
					$TextRow = 0;
					foreach ($TextAnswers as $answer => $nb)
					{
						if ($Total > 0)
							echo '<tr><td class="AnswerRow'.($TextRow % 2).'">'.InsertInlineImages($answer).'</td><td align="center" class="AnswerRow'.($TextRow % 2).'">'.number_format($nb * 100 / $Total, 2).'% ('.$nb.')</td></tr>';
						else
							echo '<tr><td class="AnswerRow'.($TextRow % 2).'">'.InsertInlineImages($answer).'</td><td align="center" class="AnswerRow'.($TextRow % 2).'">'.number_format(0, 2).'% ('.$nb.')</td></tr>';

						$TextRow ++;
						if ((!isset($_GET["showall"]) || $_GET["showall"] != "true") && $TextRow == 10) break;
					}

					echo '</table><br>';
				}
				else
				{
					echo '<span epiLang="NoAnswerYet">Pas encore de r&eacute;ponses</span><br>';
				}
			}
			else if ($Question["Type"] == "TAT" ||
							 $Question["Type"] == "MATCH" ||
							 $Question["Type"] == "SORT" ||
							 $Question["Type"] == "FORM")
			{
				$TextAnswers = array();

				if (count($QuestionAnswers) > 0)
				{
					$Total = 0;

					foreach ($QuestionAnswers as $answer)
					{
						$ThisAnswer = GetAnswersForQuestion($i, $QuestionDefinition[$i - 1], $answer);

						if (is_array($ThisAnswer))
						{
							$thiskey = implode('@@@@', $ThisAnswer);

							if (!isset($TextAnswers[$thiskey])) $TextAnswers[$thiskey] = 0;

							$TextAnswers[$thiskey] ++;
							$Total ++;
						}
					}

					arsort($TextAnswers, SORT_NUMERIC);

					echo '<table border="0" cellspacing="0" cellpadding="2" bgcolor="black"><tr><td class="AnswersHeaders" align="center"><span epiLang="UserEntry">Entr&eacute;e de l\'utilisateur</span></td>';
					echo '<td class="AnswersHeaders" align="center"><span epiLang="RateQuantity">Pourcentage&nbsp;(quantit&eacute;)</span></td>';
					echo '</tr>';

					$TextRow = 0;
					foreach ($TextAnswers as $answer => $nb)
					{
						if ($Total > 0)
							echo '<tr><td class="AnswerRow'.($TextRow % 2).'">'.str_replace('@@@@', ', ', InsertInlineImages($answer)).'</td><td align="center" class="AnswerRow'.($TextRow % 2).'">'.number_format($nb * 100 / $Total, 2).'% ('.$nb.')</td></tr>';
						else
							echo '<tr><td class="AnswerRow'.($TextRow % 2).'">'.str_replace('@@@@', ', ', InsertInlineImages($answer)).'</td><td align="center" class="AnswerRow'.($TextRow % 2).'">'.number_format(0, 2).'% ('.$nb.')</td></tr>';
						$TextRow ++;


						if ((!isset($_GET["showall"]) || $_GET["showall"] != "true") && $TextRow == 10) break;
					}

					echo '</table><br>';
				}
				else
				{
					echo '<span epiLang="NoAnswerYet">Pas encore de r&eacute;ponses</span><br>';
				}
			}
			else if ($Question["Type"] == "DRAGDROP")
			{
				$TextAnswers = array();

				if (count($QuestionAnswers) > 0)
				{
					$Total = 0;

					foreach ($QuestionAnswers as $answer)
					{
						$ThisAnswer = GetAnswersForQuestion($i, $QuestionDefinition[$i - 1], $answer);

						if ($ThisAnswer !== false)
						{
							$thiskey = GetDragDropEntry($Question, $ThisAnswer);
							if (!isset($TextAnswers[$thiskey])) $TextAnswers[$thiskey] = 0;

							$TextAnswers[$thiskey] ++;
							$Total ++;
						}
					}

					arsort($TextAnswers, SORT_NUMERIC);

					echo '<table border="0" cellspacing="0" cellpadding="2" bgcolor="black"><tr><td class="AnswersHeaders" align="center"><span epiLang="UserEntry">Entr&eacute;e de l\'utilisateur</span></td>';
					echo '<td class="AnswersHeaders" align="center"><span epiLang="RateQuantity">Pourcentage&nbsp;(quantit&eacute;)</span></td>';
					echo '</tr>';

					$TextRow = 0;
					foreach ($TextAnswers as $answer => $nb)
					{
						if ($Total > 0)
							echo '<tr><td class="AnswerRow'.($TextRow % 2).'">'.str_replace('@@@@', ', ', InsertInlineImages($answer)).'</td><td align="center" class="AnswerRow'.($TextRow % 2).'">'.number_format($nb * 100 / $Total, 2).'% ('.$nb.')</td></tr>';
						else
							echo '<tr><td class="AnswerRow'.($TextRow % 2).'">'.str_replace('@@@@', ', ', InsertInlineImages($answer)).'</td><td align="center" class="AnswerRow'.($TextRow % 2).'">'.number_format(0, 2).'% ('.$nb.')</td></tr>';
						$TextRow ++;
						if ((!isset($_GET["showall"]) || $_GET["showall"] != "true") && $TextRow == 10) break;
					}

					echo '</table><br>';
				}
				else
				{
					echo '<span epiLang="NoAnswerYet">Pas encore de r&eacute;ponses</span><br>';
				}
			}
			else if ($Question["Type"] == "TABQCU" ||
							 $Question["Type"] == "TABQCM" ||
							 $Question["Type"] == "SURVEYTABQCU" ||
							 $Question["Type"] == "SURVEYTABQCM")
			{
				$AnswersRow = array();

				foreach ($QuestionDefinition[$i - 1]["Answers"] as $QuestionRow)
				{
					$AnswersRow[] = array("values" => array(),
																"string" => $QuestionRow);
				}

				if (count($QuestionAnswers) > 0)
				{
					$RowTotal = array();
					$RowMax = array();

					foreach ($QuestionAnswers as $answer)
					{
						$ThisAnswer = GetAnswersForQuestion($i, $QuestionDefinition[$i - 1], $answer);

						foreach ($AnswersRow as $k => $aRow)
						{
							if (isset($ThisAnswer[$k]))
							{
								$ThisAnswerForRowK = $ThisAnswer[$k];

								if (!isset($RowTotal[$k]))
									$RowTotal[$k] = 0;

								$RowTotal[$k]++;

								if (is_array($ThisAnswerForRowK))
								{
									foreach ($ThisAnswerForRowK as $SubAnswers)
									{
										if (!isset($AnswersRow[$k]["values"][$SubAnswers]))
											$AnswersRow[$k]["values"][$SubAnswers] = 0;

										$AnswersRow[$k]["values"][$SubAnswers] ++;
										if ($RowMax[$k] < $AnswersRow[$k]["values"][$SubAnswers])
											$RowMax[$k] = $AnswersRow[$k]["values"][$SubAnswers];
									}
								}
								else if (is_numeric($ThisAnswerForRowK))
								{
									if (!isset($AnswersRow[$k]["values"][$ThisAnswerForRowK]))
										$AnswersRow[$k]["values"][$ThisAnswerForRowK] = 0;

									$AnswersRow[$k]["values"][$ThisAnswerForRowK] ++;
									if ($RowMax[$k] < $AnswersRow[$k]["values"][$ThisAnswerForRowK])
										$RowMax[$k] = $AnswersRow[$k]["values"][$ThisAnswerForRowK];
								}
							}
						}
					}

					echo '<table border="0" cellspacing="0" cellpadding="2" bgcolor="black"><tr><td class="AnswersHeaders" align="center">&nbsp;</td>';
					foreach ($QuestionDefinition[$i - 1]["Columns"] as $column)
						echo '<td class="AnswersHeaders" align="center">'.InsertInlineImages($column).'</td>';
					echo '</tr>';

					foreach ($AnswersRow as $rowindex => $answer)
					{
						echo '<tr><td class="AnswerRow'.($rowindex % 2).'">'.InsertInlineImages($answer["string"]).'</td>';

						foreach ($QuestionDefinition[$i - 1]["Columns"] as $k => $column)
						{
							$str = '';
							if ($answer["values"][$k] == 0)
								$str = '0';
							else
							{
								if ($RowTotal[$rowindex] > 0)
									$str = number_format($answer["values"][$k] * 100 / $RowTotal[$rowindex], 2).'% ('.$answer["values"][$k].')';
								else
									$str = number_format(0, 2).'% ('.$answer["values"][$k].')';
							}

							if ($RowMax[$rowindex] == $answer["values"][$k])
								$str = '<b>' . $str . '</b>';

							echo '<td nowrap class="AnswerRow'.($rowindex % 2).'" align="center">'.InsertInlineImages($str).'</td>';
						}

						echo '</tr>';
					}
					echo '</table><br>';

					if ($Question["HasComments"])
					{
						// show an array of the comments

						$TextAnswers = array();

						$Total = 0;

						foreach ($QuestionAnswers as $answer)
						{
							$ThisAnswer = GetAnswersForQuestion($i, $QuestionDefinition[$i - 1], $answer);

							if (isset($ThisAnswer['more']))
							{
								if (!isset($TextAnswers[trim($ThisAnswer['more'])])) $TextAnswers[trim($ThisAnswer['more'])] = 0;
								$TextAnswers[trim($ThisAnswer['more'])] ++;
								$Total ++;
							}
						}

						arsort($TextAnswers, SORT_NUMERIC);

						echo '<table border="0" cellspacing="0" cellpadding="2" bgcolor="black"><tr><td class="AnswersHeaders" align="center"><span epiLang="UserEntry">Entr&eacute;e de l\'utilisateur</span></td>';
						echo '<td class="AnswersHeaders" align="center"><span epiLang="RateQuantity">Pourcentage&nbsp;(quantit&eacute;)</span></td>';
						echo '</tr>';
						$TextRow = 0;
						foreach ($TextAnswers as $answer => $nb)
						{
							if ($Total > 0)
								echo '<tr><td class="AnswerRow'.($TextRow % 2).'">'.InsertInlineImages($answer).'</td><td align="center" class="AnswerRow'.($TextRow % 2).'">'.number_format($nb * 100 / $Total, 2).'% ('.$nb.')</td></tr>';
							else
								echo '<tr><td class="AnswerRow'.($TextRow % 2).'">'.InsertInlineImages($answer).'</td><td align="center" class="AnswerRow'.($TextRow % 2).'">'.number_format(0, 2).'% ('.$nb.')</td></tr>';

							$TextRow ++;
							if ((!isset($_GET["showall"]) || $_GET["showall"] != "true") && $TextRow == 10) break;
						}

						echo '</table><br>';
					}
				}
				else
				{
					echo '<span epiLang="NoAnswerYet">Pas encore de r&eacute;ponses</span><br>';
				}
			}

			if ($bShowTenEntriesLink)
			{
				if ((!isset($_GET["showall"]) || $_GET["showall"] != "true"))
					echo '<div align="right"><a href="index.php?showall=true"><span epiLang="ListLimitedToTenEntries">Tableau limit&eacute; aux 10 plus grandes entr&eacute;es seulement (Afficher toutes les entr&eacute;es...)</span></a></div>';
				else
					echo '<div align="right"><a href="index.php?showall=false"><span epiLang="AllEntries">Toutes les entr&eacute;es (Afficher les 10 plus grandes entr&eacute;es seulement...)</span></a></div>';
			}

			echo '		</td>';
			echo '	</tr>' . "\n";
			echo '</table><br>' . "\n";

			$QuestionNumber++;
		}

		$i++;
	}


?><br>
					<?
}



	?></td>
		</tr>
			<tr>
				<td align="center"><a href="http://www.easyquizz.com/" target="_blank"><span epiLang="ReportGeneratedWithEasyquizz">Rapport g&eacute;n&eacute;r&eacute; par Easyquizz le </span><? echo date("d/m/Y") ?></a><br><?


if (!empty($CopyrightText))
{
	if (empty($CopyrightURL))
		echo $CopyrightText;
	else
		echo '<a href="'.$CopyrightURL.'" target="_blank">'.$CopyrightText.'</a>';
}
else
{
	echo '<a href="http://www.epistema.com/" target="_blank">www.epistema.com</a>';
}

function GetDragDropEntry($questionMetaData, $UserEntry)
{
	$str = '<table class="noBorder" border="0" cellspacing="0" cellpadding="1">';

	if (isset($questionMetaData['Version']) && $questionMetaData['Version'] == 2)
	{
		$ExpectedAnswerIndex = 1;
		foreach ($questionMetaData['Rows'] as $k => $answer)
		{
			if ($answer['distractor'])
				continue;

			if (isset($UserEntry[$ExpectedAnswerIndex]) && is_numeric($UserEntry[$ExpectedAnswerIndex]))
				$str .= '<tr><td class="noBorder">('.($k + 1).') '.$answer['left'].'</td><td class="noBorder">&rarr;</td><td class="noBorder">'.$questionMetaData['Answers'][$UserEntry[$ExpectedAnswerIndex]]['right'].' ('.($UserEntry[$ExpectedAnswerIndex] + 1).')</td></tr>';
			else
				$str .= '<tr><td class="noBorder">('.($k + 1).') '.$answer['left'].'</td><td class="noBorder">&rarr;</td><td class="noBorder">&nbsp;</td></tr>';

			$ExpectedAnswerIndex++;
		}
	}
	else
	{
		foreach ($questionMetaData['Lefts'] as $k => $left)
		{
			if (isset($UserEntry[$k+1]) && is_numeric($UserEntry[$k+1]))
				$str .= '<tr><td class="noBorder">('.($k+1).') '.$left.'</td><td class="noBorder">&rarr;</td><td class="noBorder">'.$questionMetaData['Rights'][$UserEntry[$k + 1]].' ('.($UserEntry[$k + 1]+1).')</td></tr>';
			else
				$str .= '<tr><td class="noBorder">('.($k+1).') '.$left.'</td><td class="noBorder">&rarr;</td><td class="noBorder">&nbsp;</td></tr>';
		}
	}

	$str .= '</table>';

	return $str;
}

?></td>
			</tr>
		</table>
	</body>
	<script type="text/javascript" language="JavaScript">
		EpiLangManager.TranslatePage(document);
	</script>
</html>
<?

function GetAnswersForQuestion($i, $questionDefinition, &$answer)
{
	if (isset($answer['id_map']) && !empty($questionDefinition['UID']))
	{
		if (isset($answer["answers"][$answer['id_map'][$questionDefinition['UID']]]))
			return $answer["answers"][$answer['id_map'][$questionDefinition['UID']]];
		else
			return false;
	}

	if (isset($answer["answers"][$i - 1]))
		return $answer["answers"][$i - 1];
	else
		return false;
}

function MakeDate($str_date, $addDays = 0)
{
	$str_date = strtr($str_date, "/. ", "---");
	$date_parts = explode('-', $str_date);

	if (count($date_parts) != 3)
		return false;

	if ($date_parts[2] < 100)
		$date_parts[2] += 2000;

	return mktime(0, 0, 0, $date_parts[1], $date_parts[0] + $addDays, $date_parts[2]);
}

function filterDates($answer)
{
	if (!empty($GLOBALS['FROMDATE']) && !empty($GLOBALS['TODATE']))
		return ($GLOBALS['FROMDATE'] < $answer['when'] &&  $answer['when'] < $GLOBALS['TODATE']);
	else if (!empty($GLOBALS['FROMDATE']))
		return ($GLOBALS['FROMDATE'] < $answer['when']);
	else if (!empty($GLOBALS['TODATE']))
		return ($answer['when'] < $GLOBALS['TODATE']);
	else
		return true;
}

function InsertInlineImages($str)
{
	if (empty($GLOBALS['inline_images']))
	{
		$GLOBALS['inline_images'] = array();
		$AttachmentFolder = str_replace('admin/' . basename(__FILE__), '_attachments/', str_replace('\\', '/', __FILE__));

		if (!is_dir($AttachmentFolder))
			return $str;

		$d = dir($AttachmentFolder);

		while (false !== ($entry = $d->read()))
		{
			if ($entry == '.' || $entry == '..')
				continue;

			if (is_dir($AttachmentFolder . $entry))
				continue;

			$GLOBALS['inline_images'][] = $entry;
		}

		$d->close();
	}

	foreach ($GLOBALS['inline_images'] as $anImage)
		$str = str_replace($anImage, '<img src="../_attachments/'.$anImage.'" alt="" border="0" valign="absmiddle">', $str);

	return $str;
}

?>