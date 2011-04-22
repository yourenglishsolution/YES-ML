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

$QuestionAnswers = array();

include_once(str_replace(basename(__FILE__), 'data.php', __FILE__));

if (file_exists(str_replace(basename(__FILE__), 'userdata.php', __FILE__)))
	include(str_replace(basename(__FILE__), 'userdata.php', __FILE__));

if (isset($QuestionAnswersSerialized) && $QuestionAnswersSerialized != '')
{
	$QuestionAnswers = array_merge(unserialize(urldecode($QuestionAnswersSerialized)), $QuestionAnswers);
}

if (!isset($_SESSION))
{
	session_name(ereg_replace("[^[:alnum:]]", "", dirname($_SERVER['PHP_SELF'])));
	session_start();
}

if (!empty($Passwd))
{
	if (isset($_POST["Passwd"]))
		$_SESSION['passwd'] = $_POST["Passwd"];

	$NotLogged = ($_SESSION['passwd'] != $Passwd);
}
else
	$NotLogged = false;

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
.theme  { color: navy; font-size: 12px; background-color: #DDDDDD }
.theme_bigger  { color: navy; font-size: 14px; background-color: #DDDDDD }
td { font-size: 10px }
select { font-size: 10px }
input { font-size: 10px }
.AnswersHeaders  { color: white; font-weight: bold; background-color: #8aa5c4 }
.AnswerRow0 { background-color: white }
.AnswerTables { }
.AnswerRow1 { background-color: #E9E9E9 }
table.QuestionTable  { border: solid 1px black; border-collapse:collapse }
th.headers   { color: navy; font-weight: bold; font-size: 12px; background-color: silver; border: solid 1px black }
th.subheaders  { font-size: 10px; background-color: silver; border: solid 1px black }
.QuestionTable table  { border: solid 1px black; border-collapse: collapse  }
.QuestionTable td  { border: solid 1px black }
table.noBorder  { border: none 1px }
td.noBorder  { border: none 1px }
.noprint { }
--></style>
		<style type="text/css" media="print"><!--
body { font-size: 12px; font-family: Verdana, Arial, Helvetica, Geneva, Swiss, SunSans-Regular }
table.QuestionTable  { border: solid 1px black; border-collapse:collapse }
th.headers   { color: navy; font-weight: bold; font-size: 12px; background-color: silver; border: solid 1px black }
th.subheaders  { font-size: 10px; background-color: silver; border: solid 1px black }
td { font-size: 10px;}
.AnswerTables { page-break-inside : avoid }
.AnswersHeaders  { color: white; font-weight: bold; background-color: #8aa5c4; border: solid 0.2mm black }
.AnswerRow0 { background-color: white; border: solid 0.2mm black  }
.AnswerRow1 { background-color: #E9E9E9; border: solid 0.2mm black  }
.noprint { display: none }
table.noBorder  { border: none 1px }
td.noBorder  { border: none 1px }
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
	if (!isset($GLOBALS["seeingwhat"]))
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
								<? 	}
?><a href="index.php"><span epiLang="BackToMainReport">Revenir au <b>rapport principal</b></span></a></td>
							<td valign="middle" align="right">
				<form style="padding: 0px; margin: 0px" action="index.php" method="post" name="RemoveEntry"><input type="hidden" name="removeWho" value="<?=$_GET["seeingwho"] ?>"><table border="0" cellspacing="0" cellpadding="3">
										<tr>
											<td align="right"><a href="javascript:window.print()"><span epiLang="PrintTheReport"><b>Imprimer</b> le rapport</span></a></td>
											<td align="center"><a href="javascript:window.print()"><img src="_images/print.gif" alt="" width="26" height="22" align="absmiddle" border="0"></a></td>
										</tr>
										<tr>
											<td align="right"><a href="admin.php">Acc&eacute;der &agrave; la <b>page d'administration</b></a></td>
											<td align="center"><a href="admin.php"><img src="_images/admin.gif" alt="" width="30" height="30" align="absmiddle" border="0"></a></td>
										</tr>
										<tr>
											<td align="right"><a href="javascript:document.RemoveEntry.submit();"><span epiLang="DeleteThisEntry"><b>Supprimer</b> cette entr&eacute;e</span></a></td>
											<td align="center"><input type="image" src="_images/trash.gif" alt="" align="absmiddle" border="0"></td>
										</tr>
									</table>
								</form>
			</td>
		</tr>
	</table>

		<hr align="center" noshade size="1" width="80%"><br>
<?
	}

	if (isset($GLOBALS["seeingwhat"]))
	{
		ShowAnswersFor($GLOBALS["seeingwhat"]);
	}
	else if (!empty($_GET["seeinguserid"]))
  {
    for ($i = (count($QuestionAnswers) - 1); $i >= 0; $i--)
    {
      if ($QuestionAnswers[$i]["UserId"] == $_GET["seeinguserid"])
      {
        ShowAnswersFor($QuestionAnswers[$i]);
        break;
      }
    }
  }
	else if (!empty($_GET["seeingwho"]) && $_GET["seeingwho"] != "tous")
	{
		ShowAnswersFor($QuestionAnswers[$_GET["seeingwho"]]);
	}
	else
	{
		foreach($QuestionAnswers as $UserAnswer)
		{
			ShowAnswersFor($UserAnswer);
		}
	}
}



function ShowAnswersFor(&$UserAnswer)
{
	global $QuestionDefinition;
	global $Themes;

	$TimeString = '';

	if (isset($UserAnswer["time_spent"]) && $UserAnswer["time_spent"] !== false)
	{
		$TimeString = '<span epiLang="TimeSpentOnTheQuiz">Temps pass&eacute; sur le questionnaire : </span>';

		$timeInSec = $UserAnswer["time_spent"];

		if ($timeInSec < 60)
			$TimeString .= $timeInSec . ' <span epiLang="sec">sec.</span>';
		else
		{
			$Minutes = floor($timeInSec / 60);
			$timeInSec = $timeInSec % 60;

			if ($Minutes < 60)
			{
				if ($timeInSec != 0)
					$TimeString .= $Minutes . ' <span epiLang="min">min</span> ' . $timeInSec . ' <span epiLang="sec">sec.</span>' ;
				else
					$TimeString .= $Minutes . ' <span epiLang="min">min</span>';
			}
			else
			{
				$Hours = floor($Minutes / 60);
				$Minutes = $Minutes % 60;

				$TimeString .= $Hours . ":" . $Minutes . ":" . $timeInSec;
			}
		}

		$TimeString .= '<br>';
	}

	$ScoreString = '';
	if (isset($UserAnswer["score"]) && $UserAnswer["score"] != -1)
		$ScoreString = '<span epiLang="FinalScoreField">Score global de l\'&eacute;valuation : </span>' . round($UserAnswer["score"], 2) . ' / ' . round($UserAnswer["score_max"], 2) . '<br>';

	$WhenString = '<span epiLang="DateField">Date de passation : </span>' . date("d/m/Y H:i", $UserAnswer["when"]) . '<br>';

	echo '<table width="630" border="0" cellspacing="0" cellpadding="0"><tr>';
	echo '<td valign="top"><h3>';
	echo $UserAnswer["UserFirstname"] . ' ' . $UserAnswer["Username"];
	echo '</h3></td>';
	echo '<td align="right"><b>';
	echo $TimeString;
	echo $ScoreString;
	echo $WhenString;
	echo '</b></td></tr></table><br>';

	// Show the profiles summary

	$counts = array();

	$i = 1;

	foreach ($QuestionDefinition as $Question)
	{
		if ($Question["Type"] == "SURVEYQCU" ||
				$Question["Type"] == "SURVEYQCM")
		{
			$v = array();

			foreach ($QuestionDefinition[$i - 1]["Answers"] as $QuestionRow)
			{
				$v[] = array("value" => false);
			}

			$IsQCM = false;

			$ThisAnswer = GetAnswersForQuestion($i, $Question, $UserAnswer);

 			if (isset($ThisAnswer['checked']) && is_array($ThisAnswer['checked']))
			{
				foreach ($ThisAnswer['checked'] as $AnswerItems)
					$v[$AnswerItems]["value"] = true;
			}
			else if (isset($ThisAnswer['checked']))
			{
				$v[$ThisAnswer['checked']]["value"] = true;
			}

			foreach($v as $k => $section)
			{
				if ($section["value"])
				{
					if (!empty($QuestionDefinition[$i - 1]["Weights"][$k]))
					{
						foreach ($QuestionDefinition[$i - 1]["Weights"][$k] as $weights)
						{
							if (!isset($counts[$weights['guid']]))
								$counts[$weights['guid']] = 0;

							$counts[$weights['guid']] += $weights['weight'];
						}
					}
				}
			}
		}
		else if ($Question["Type"] == "SURVEYTABQCU" ||
						 $Question["Type"] == "SURVEYTABQCM")
		{
			$AnswersRow = array();

			foreach ($QuestionDefinition[$i - 1]["Answers"] as $QuestionRow)
				$AnswersRow[] = array("values" => array());

			foreach ($AnswersRow as $k => $aRow)
			{
				$ThisAnswer = GetAnswersForQuestion($i, $Question, $UserAnswer);

				if (isset($ThisAnswer[$k]) && is_array($ThisAnswer[$k]))
				{
					foreach ($ThisAnswer[$k] as $SubAnswers)
						$AnswersRow[$k]["values"][$SubAnswers] = true;
				}
				else if (isset($ThisAnswer[$k]))
				{
					$AnswersRow[$k]["values"][$ThisAnswer[$k]] = true;
				}
			}

			foreach ($AnswersRow as $rowindex => $answer)
			{
				foreach ($QuestionDefinition[$i - 1]["Weights"][$rowindex] as $colindex => $Weights)
				{
					if (!empty($answer["values"][$colindex]))
					{
						foreach ($Weights as $aWeight)
						{
							if (!isset($counts[$aWeight['guid']]))
								$counts[$aWeight['guid']] = 0;

							$counts[$aWeight['guid']] += $aWeight['weight'];
						}
					}
				}
			}
		}

		$i++;
	}

	EchoProfiles($counts);


	// End profiles

	$i = 1;
	$QuestionNumber = 1;
	$ThemeGUID = "";
	$current_theme = "";
	$sub_score = array();
	$sub_score_max = array();

	$total_score = 0;
	$max_score = 0;

	foreach ($QuestionDefinition as $Question)
	{
		if ($Question["Type"] != "CREDENTIALS" &&
				$Question["Type"] != "EXPLANATION")
		{
			if ($ThemeGUID != $Question["ThemeGUID"])
			{
				$ThemeGUID = $Question["ThemeGUID"];

				if (isset($Themes[$ThemeGUID]["title"]))
					$current_theme = $Themes[$ThemeGUID]["title"];
				else
					$current_theme = '';

				$sub_score[$ThemeGUID] = 0;
				$sub_score_max[$ThemeGUID] = 0;
			}

			if (!isset($sub_score[$ThemeGUID])) $sub_score[$ThemeGUID] = 0;
			if (!isset($sub_score_max[$ThemeGUID])) $sub_score_max[$ThemeGUID] = 0;
			if (isset($UserAnswer["scores"][$i - 1]))
			{
				$sub_score[$ThemeGUID] += $UserAnswer["scores"][$i - 1]['sc'];
				$sub_score_max[$ThemeGUID] += $UserAnswer["scores"][$i - 1]['msc'];
				$total_score  += $UserAnswer["scores"][$i - 1]['sc'];
				$max_score += $UserAnswer["scores"][$i - 1]['msc'];
			}
		}

		$i++;
	}

	$i = 1;
	$ThemeGUID = "";
	$current_theme = "";

	foreach ($QuestionDefinition as $Question)
	{
		$ThisAnswer = GetAnswersForQuestion($i, $Question, $UserAnswer);

		if ($Question["Type"] != "CREDENTIALS" &&
				$Question["Type"] != "EXPLANATION")
		{
			$bShowTenEntriesLink = false;

			if ($ThemeGUID != $Question["ThemeGUID"])
			{
				$ThemeGUID = $Question["ThemeGUID"];

				if (isset($Themes[$ThemeGUID]["title"]))
					$current_theme = $Themes[$ThemeGUID]["title"];
				else
					$current_theme = '';

				echo '<table border="0" class="QuestionTable" cellspacing="0" cellpadding="3" width="100%"><tr><td class="theme_bigger"><b>' . str_replace('|', '</b> &gt; <b>', $Question["Theme"]) . '</b></td><td  width="90" align="right" bgcolor="#DDDDDD"><b>Score : ' . round($sub_score[$ThemeGUID], 2) . ' / ' . round($sub_score_max[$ThemeGUID], 2) . '</b></td></tr></table><br>' . "\n";
			}

			$QuestionType = "";
			switch ($Question["Type"])
			{
				case 'FLASH' : $QuestionType = '<span epiLang="FlashTypeName">Flash question</span>'; break;
				case 'READING_ASSESSMENT'		: $QuestionType = '<span epiLang="ReadingAssessmentTypeName">Reading assessment question</span>'; break;
				case 'SHUNTING' : $QuestionType = '<span epiLang="ShuntingQuestionTypeName">Shunting question</span>'; break;
				case 'SURVEYQCU':
				case 'QCU'    : $QuestionType = '<span epiLang="QCUTypeName">Question &agrave; choix unique</span>'; break;
				case 'SURVEYQCM':
				case 'QCM'    : $QuestionType = '<span epiLang="QCMTypeName">Question &agrave; choix multiple</span>'; break;
				case 'SURVEYTABQCU':
				case 'TABQCU' : $QuestionType = '<span epiLang="TABQCUTypeName">Question matricielle &agrave; choix unique</span>'; break;
				case 'SURVEYTABQCM':
				case 'TABQCM' : $QuestionType = '<span epiLang="TABQCMTypeName">Question matricielle &agrave; choix multiple</span>'; break;
				case 'TAT'    : $bShowTenEntriesLink = true; $QuestionType = '<span epiLang="TATTypeName">Question &agrave; trous</span>'; break;
				case 'MATCH'  : $bShowTenEntriesLink = true; $QuestionType = '<span epiLang="MATCHTypeName">Question d\'appariement</span>'; break;
				case 'DRAGDROP': $bShowTenEntriesLink = true; $QuestionType = '<span epiLang="DRAGDROPTypeName">Question glisser/d&eacute;poser</span>'; break;
				case 'SORT'   : $bShowTenEntriesLink = true; $QuestionType = '<span epiLang="SORTTypeName">Question d\'ordonnancement</span>'; break;
				case 'FORM'   : $bShowTenEntriesLink = true; $QuestionType = '<span epiLang="FORMTypeName">Question formulaire</span>'; break;
				case 'TEXT'   : $bShowTenEntriesLink = true; $QuestionType = '<span epiLang="TEXTTypeName">Question texte</span>'; break;
			}

			echo '<table width="630" border="0" cellspacing="0" cellpadding="3" class="QuestionTable">' . "\n";
			echo '	<tr>';
			echo '		<th class="headers" colspan="3">'.InsertInlineImages($Question["Question"]).'</th>';
			echo '	</tr>' . "\n";
			echo '	<tr>';
			echo '		<th class="subheaders" width="90" align="center" nowrap><span epiLang="QuestionNumber">Question n&deg;</span>'.$QuestionNumber.'</th>';

			if (!empty($UserAnswer["scores"][$i - 1]['msc']))
			{
				echo '		<th class="subheaders" width="450"><b><span epiLang="TypeField">Type&nbsp;:</span></b> '. $QuestionType .'</th>';
				echo '		<th class="subheaders" width="90" align="right" nowrap><b><span epiLang="ScoreField">Score&nbsp;:</span></b> '. round($UserAnswer["scores"][$i - 1]['sc'], 2) .'/'. round($UserAnswer["scores"][$i - 1]['msc'], 2) .'</th>';
			}
			else
				echo '		<th class="subheaders" colspan="2" width="540"><b><span epiLang="TypeField">Type&nbsp;:</span></b> '. $QuestionType .'</th>';

			echo '	</tr>' . "\n";
			echo '	<tr>';
			echo '		<td colspan="3" align="center" bgcolor="white">	';

			if ($Question["Type"] == "QCU" ||
					$Question["Type"] == "QCM" ||
					$Question["Type"] == "SURVEYQCU" ||
					$Question["Type"] == "SURVEYQCM" ||
					$Question["Type"] == "SHUNTING")
			{
				$v = array();

				foreach ($QuestionDefinition[$i - 1]["Answers"] as $QuestionRow)
				{
					$v[] = array("value" => 0,
											 "string" => $QuestionRow);
				}

				$IsQCM = false;

				if (isset($ThisAnswer['checked']) && is_array($ThisAnswer['checked']))
					foreach ($ThisAnswer['checked'] as $AnswerItems)
					{
						if (!isset($v[$AnswerItems]["value"])) $v[$AnswerItems]["value"] = 0;
						$v[$AnswerItems]["value"] ++;
						$IsQCM = true;
					}
				else if (isset($ThisAnswer['checked']))
				{
					if (!isset($v[$ThisAnswer['checked']]["value"])) $v[$ThisAnswer['checked']]["value"] = 0;

					$v[$ThisAnswer['checked']]["value"] ++;
				}

				$HasMore = isset($ThisAnswer['more']) && is_array($ThisAnswer['more']);

				echo '<table border="0" cellspacing="0" cellpadding="2">';
				echo '<tr><td class="AnswersHeaders" >&nbsp;</td><td class="AnswersHeaders" ><span epiLang="Answer">R&eacute;ponse</span></td>';

				if ($HasMore)
					echo '<td class="AnswersHeaders" ><span epiLang="MoreField">Texte compl&eacute;mentaire</span></td>';

				echo '</tr>';
				$TextRow = 0;
				foreach($v as $k => $section)
				{
					echo '	<tr><td class="AnswerRow'.($TextRow % 2).'">' .($k + 1).' - '. InsertInlineImages($section["string"]) . '</td>';

					if ($section["value"] > 0)
						echo '<td align="center" class="AnswerRow'.($TextRow % 2).'">&bull;</td>';
					else
						echo '<td class="AnswerRow'.($TextRow % 2).'">&nbsp;</td>';

					if ($HasMore)
					{
						if (isset($ThisAnswer['more'][$k + 1]))
							echo '<td class="AnswerRow'.($TextRow % 2).'">'.$ThisAnswer['more'][$k + 1].'</td>';
						else
							echo '<td class="AnswerRow'.($TextRow % 2).'">&nbsp;</td>';
					}

					echo '</tr>';
					$TextRow ++;
				}

				echo '</table>';
			}
			else if ($Question["Type"] == "TEXT")
			{
				echo trim($ThisAnswer);
			}
			else if ($Question["Type"] == "TAT")
			{
				if (is_array($ThisAnswer))
					echo implode(', ', InsertInlineImages($ThisAnswer));
			}
			else if ($Question["Type"] == "MATCH")
			{
				if (is_array($ThisAnswer))
					echo implode(', ', InsertInlineImages($ThisAnswer));
			}
			else if ($Question["Type"] == "DRAGDROP")
			{
				if (is_array($ThisAnswer))
					echo InsertInlineImages(GetDragDropEntry($Question, $ThisAnswer));
			}
			else if ($Question["Type"] == "SORT")
			{
				if (is_array($ThisAnswer))
					echo implode(', ', InsertInlineImages($ThisAnswer));
			}
			else if ($Question["Type"] == "FORM")
			{
				if (is_array($ThisAnswer))
					echo implode(', ', $ThisAnswer);
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

				$RowMax = array();

				foreach ($AnswersRow as $k => $aRow)
				{
					if (isset($ThisAnswer[$k]) && is_array($ThisAnswer[$k])) // QCM
					{
						foreach ($ThisAnswer[$k] as $SubAnswers)
						{
							if (!isset($AnswersRow[$k]["values"][$SubAnswers])) $AnswersRow[$k]["values"][$SubAnswers] = 0;

							$AnswersRow[$k]["values"][$SubAnswers] ++;
							if (!isset($RowMax[$k]) || $RowMax[$k] < $AnswersRow[$k]["values"][$SubAnswers])
								$RowMax[$k] = $AnswersRow[$k]["values"][$SubAnswers];
						}
					}
					else if (isset($ThisAnswer[$k]))	// QCU
					{
						if (!isset($AnswersRow[$k]["values"][$ThisAnswer[$k]])) $AnswersRow[$k]["values"][$ThisAnswer[$k]] = 0;

						$AnswersRow[$k]["values"][$ThisAnswer[$k]] ++;

						if (!isset($RowMax[$k]) || $RowMax[$k] < $AnswersRow[$k]["values"][$ThisAnswer[$k]])
							$RowMax[$k] = $AnswersRow[$k]["values"][$ThisAnswer[$k]];
					}
				}

				echo '<table border="0" cellspacing="0" cellpadding="2"><tr><td class="AnswersHeaders" align="center">&nbsp;</td>';
				foreach ($QuestionDefinition[$i - 1]["Columns"] as $column)
					echo '<td class="AnswersHeaders" align="center">'.InsertInlineImages($column).'</td>';
				echo '</tr>';

				foreach ($AnswersRow as $rowindex => $answer)
				{
					echo '<tr><td class="AnswerRow'.($rowindex % 2).'">'.InsertInlineImages($answer["string"]).'</td>';

					foreach ($QuestionDefinition[$i - 1]["Columns"] as $k => $column)
					{
						$str = '';
						if (empty($answer["values"][$k]))
							$str = '&nbsp;';
						else
						{
							if ($answer["values"][$k] > 0)
								$str = '&bull;';
						}

						if (isset($RowMax[$rowindex]) &&
								isset($answer["values"][$k]) &&
								$RowMax[$rowindex] == $answer["values"][$k])
							$str = '<b>' . $str . '</b>';

						echo '<td nowrap class="AnswerRow'.($rowindex % 2).'" align="center">'.$str.'</td>';
					}

					echo '</tr>';
				}
				echo '</table>';

				if ($Question["HasComments"] && !empty($ThisAnswer['more']))
					echo '<span epiLang="OtherField">Autres (pr&eacute;cisez) :</span> ' . trim($ThisAnswer['more']);
			}

			echo '		</td>';
			echo '	</tr>' . "\n";
			echo '</table><br>' . "\n";

			$QuestionNumber++;
		}

		$i++;
	}
}

	?>

</td>
		</tr>
			<tr>
				<td align="center" style="font-size=10px"><a href="http://www.easyquizz.com/" target="_blank"><span epiLang="ReportGeneratedWithEasyquizz">Rapport g&eacute;n&eacute;r&eacute; par Easyquizz le </span><? echo date("d/m/Y") ?></a><br><?

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

?>

</td>
			</tr>
		</table>
	</body>
	<script type="text/javascript" language="JavaScript">
		EpiLangManager.TranslatePage(document);
	</script>
</html>

<?
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

function EchoProfiles(&$counts)
{
	global $Profiles;

	if (empty($counts))
		return;

	$TotalWeight = 0;
	foreach ($Profiles as $aProfile)
	{
		if (!empty($counts[$aProfile['guid']]))
		{
			if ($counts[$aProfile['guid']] < 0)
				$counts[$aProfile['guid']] = 0;

			$TotalWeight += $counts[$aProfile['guid']];
		}
	}

	echo '<div align="center"><table width="80%" border="0" cellspacing="0" cellpadding="2">' . "\n";

	$RadarParams = array();

	foreach ($Profiles as $aProfile)
	{
		if (!empty($counts[$aProfile['guid']]))
			$ratio = $counts[$aProfile['guid']] * 100 / $TotalWeight;
		else
			$ratio = 0;

		$ratio = number_format($ratio, 2);

		$RadarParams[] = 't[]=' . rawurlencode($aProfile['label']) . '&v[]=' . $ratio . '&m[]=100';

		echo '<tr><td valign="top"><b>'.$aProfile['label'].'</b></td><td align="right" valign="top"><b>'.$ratio.' %</b></td></tr>' . "\n";
		echo '<tr><td colspan="2" valign="top">'.$aProfile['description'].'</td></tr>' . "\n";
	}

	echo '</table>' . "\n";

	if (count($RadarParams) > 2)
		echo '<img src="stats/radar.php?encoding=UTF-8&width=600&height=400&'.implode('&', $RadarParams).'" alt="Radar" border="0">' . "\n";

	echo '</div>' . "\n";
}

function GetAnswersForQuestion($i, $questionDefinition, &$answer)
{
	if (isset($answer['id_map']) && !empty($questionDefinition['UID']))
	{
		if (isset($answer['id_map'][$questionDefinition['UID']]) &&
				isset($answer["answers"][$answer['id_map'][$questionDefinition['UID']]]))
			return $answer["answers"][$answer['id_map'][$questionDefinition['UID']]];
		else
			return false;
	}

	if (isset($answer["answers"][$i - 1]))
		return $answer["answers"][$i - 1];
	else
		return false;
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