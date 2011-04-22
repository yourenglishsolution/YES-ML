<?
	include_once('../admin/autodiagnostic.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<meta name="generator" content="Adobe GoLive 6">
		<title>Analyse</title>
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
.noprint { }
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
.noprint { visibility: hidden }
--></style>
	</head>

	<body bgcolor="#ffffff" leftmargin="0" marginheight="0" marginwidth="0" topmargin="0">	
		<table width="640" border="0" cellpadding="0" cellspacing="0" height="57">
			<tr height="57">
				<td bgcolor="#8cc919" width="82" height="57"><?
			
$UseDefaultLogo = true;

if (!empty($LogoImage))
{
	$LogoImage = basename($LogoImage);
	if (file_exists('../admin/'.$LogoImage))
	{
		echo '<img src="../admin/'.$LogoImage.'" >';
		$UseDefaultLogo = false;
	}
}			
			
if ($UseDefaultLogo)
	echo '<img src="_images/LogoEasyQuizz.gif" width="82" height="57">';
			
			?></td>
				<td align="center" bgcolor="#00a8db" height="57"><font size="3" color="white">Analyse du questionnaire en ligne&nbsp;:</font><br>
					<font size="3" color="black"><b><?=$QuizzTitle?></b></font></td>
				<td align="right" bgcolor="#00a8db" width="70" height="57"><img src="_images/LogoE.gif" width="70" height="57"></td>
			</tr>
		</table>
		<table width="640" border="0" cellpadding="5" cellspacing="0">
			<tr>
				<td bgcolor="white" align="center">		 	
<?

	echo '<table width="630" border="0" cellspacing="1" cellpadding="3" bgcolor="black">' . "\n";
	echo '	<tr><td class="headers">Analyse des r&eacute;ponses</td></tr>' . "\n";
	echo '	<tr><td align="center" bgcolor="white">	';

	AfficheCommentaires();
	
	echo '	</td></tr></table>' . "\n";

	echo '<p>&nbsp;</p>' . "\n";

	echo '<table width="630" border="0" cellspacing="1" cellpadding="3" bgcolor="black">' . "\n";
	echo '	<tr><td class="headers">Synth&egrave;se</td></tr>' . "\n";
	echo '	<tr><td align="center" bgcolor="white">	';

	// affiche un tableau avec des histogrammes linéaires des réponses ventilées par thèmes :
	AfficheHistogrammes();

	echo '	</td></tr></table>' . "\n";

?>
		
		</td>
					</tr>
		</table>
		<table width="640" border="0" cellspacing="0" cellpadding="5">
			<tr>
				<td align="center">Rapport g&eacute;n&eacute;r&eacute; par <a href="http://www.easyquizz.com/" target="_blank">Easyquizz</a> le <? echo date("d/m/Y") ?><br>
					<? 

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

?></td>
			</tr>
		</table>
	</body>

</html>