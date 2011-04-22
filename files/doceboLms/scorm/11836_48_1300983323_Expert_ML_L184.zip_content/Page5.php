<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><?php 
if ($GLOBALS['ClassToInstanciate'] == 'QuestionManager')
{
	class QuestionManager
	{
		var $NumberOfQuestions;
		var $ScoringMethod;
		var $inQCUMode;
		var $weight;

		var $Answer;

		function QuestionManager()
		{
			$this->NumberOfQuestions = 4; // more than one means a tabular QCM/QCU
			$this->ScoringMethod = "";
			$this->inQCUMode = true;
			$this->weight = 1;
			
			
			$this->Answer[0][0] = false;
			
			$this->Answer[0][1] = true;
			
			$this->Answer[1][0] = false;
			
			$this->Answer[1][1] = true;
			
			$this->Answer[2][0] = false;
			
			$this->Answer[2][1] = true;
			
			$this->Answer[3][0] = true;
			
			$this->Answer[3][1] = false;
						
		}

		function GetScore()
		{
			$score = 0;

			if ($this->inQCUMode)
			{		
				for ($n = 0; $n < count($this->Answer); $n++) 	
					if ( isset($_GET["TABQCU".$n]) ) 
					{
						$score += ($this->Answer[$n][$_GET["TABQCU".$n]] ? 1 : 0);
					}
			
				$score = $score / $this->NumberOfQuestions;
			}
			else
			{
				$UserAnswers  = array();
				
				for($i = 0; $i < $this->NumberOfQuestions; $i++) 
				{
					$UserAnswers[$i] = array();
					
					for($j = 0; $j < count($this->Answer[$i]); $j++)
					{
						$UserAnswers[$i][$j] = false;
					}
					
					if (isset($_GET["TABQCM".$i]))
					{
						for($j = 0; $j < count($_GET["TABQCM".$i]); $j++)
							$UserAnswers[$i][$_GET["TABQCM".$i][$j]] = true;
					}
				}
			
				if($this->ScoringMethod == "AllRight")
				{
					$score = 1;

					for($i = 0; $i < $this->NumberOfQuestions; $i++) 
					{
						for($j = 0; $j < count($this->Answer[$i]); $j++)
						{
							if ($UserAnswers[$i][$j] != $this->Answer[$i][$j])
								return 0;
						}
					}
				}
				else
				{
					$NbsMaxRightAnswer = 0;

					for($i = 0; $i < $this->NumberOfQuestions; $i++) 
					{		
						$NbsRightAnswer = 0;

						for($j = 0; $j < count($this->Answer[$i]); $j++)
						{
							if ($this->Answer[$i][$j] == true)
								$NbsMaxRightAnswer ++;

							if ($UserAnswers[$i][$j])
								if ($this->Answer[$i][$j])
									$NbsRightAnswer++;
								else
									$NbsRightAnswer--;
						}

						$score += $NbsRightAnswer;
					}

					if ($score < 0) return 0;

					if ($NbsMaxRightAnswer == 0) 
						$score = 0;
					else
						$score = $score / $NbsMaxRightAnswer;
				}
			}

				
			// no negative score allowed :
			if ($score < 0)
				$score = 0;
				

			return $score * $this->weight;
		}
	};

	
}
else
{
	class QuestionDisplayer extends QuestionDisplayerBase
	{
		var $AnswerText;
	
		function QuestionDisplayer()
		{
			$this->QuestionDisplayerBase();
			$this->QuestionType = 'tabqcuqcm';
		}
	
		function PrintCorrection($bInCorrection)
		{
			if ($bInCorrection)
			{
				$AnswerText =	"<table width=\"100%\" class=\"Correction\" ><tr><td class=\"Correction ProcessInlineImages\">";

				$Score = $GLOBALS['QuizzManager']->GetCurrentPageData("Score");
				$ScoreMax = $GLOBALS['QuizzManager']->GetCurrentPageData("MaxScore");

				if ($ScoreMax > 0)
				{
					if ($Score <= 0) 
						$AnswerText .= "<p><span style=\"font-weight: bold\" epiLang=\"HintHeaderBadString\"></span></p>";
					else if ($Score < $ScoreMax)
						$AnswerText .= "<p><span style=\"font-weight: bold\" epiLang=\"HintHeaderHalfString\"></span></p>";
					else 
						$AnswerText .= "<p><span style=\"font-weight: bold\" epiLang=\"HintHeaderCorrectString\"></span></p>";
				}
				
				
				$AnswerText .= "<p></p>";
				
				
				if ($Score != $ScoreMax)
				{
					$AnswerText .= "<p><span epiLang=\"CorrectionTextHead\"></span></p>";
					$AnswerText .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">";
					$AnswerText .= "<tr><td class=\"Correction\" valign=\"top\">The customer thought that only the salad was good.</td>";
					$AnswerText .= "<td class=\"Correction\" valign=\"top\" align=\"center\">";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "False<br>";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "</td></tr>";
					$AnswerText .= "";
					$AnswerText .= "<tr><td class=\"Correction\" valign=\"top\">The customer finally agrees to pay for the food.</td>";
					$AnswerText .= "<td class=\"Correction\" valign=\"top\" align=\"center\">";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "False<br>";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "</td></tr>";
					$AnswerText .= "";
					$AnswerText .= "<tr><td class=\"Correction\" valign=\"top\">The manager is forced to call the police.</td>";
					$AnswerText .= "<td class=\"Correction\" valign=\"top\" align=\"center\">";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "False<br>";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "</td></tr>";
					$AnswerText .= "";
					$AnswerText .= "<tr><td class=\"Correction\" valign=\"top\">The customer ate some of the chicken, even though it was overcooked.</td>";
					$AnswerText .= "<td class=\"Correction\" valign=\"top\" align=\"center\">";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "True<br>";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "</td></tr>";
					$AnswerText .= "";
					$AnswerText .= "</table>";
				}
				
				$AnswerText .= "</td></tr></table>";
				
				echo $AnswerText;
			}
		}
		
		function IsSelected($bInCorrection, $QuestionNumber, $Column)
		{
		
			$Answer[0][0] = false;
			
			$Answer[0][1] = true;
			
			$Answer[1][0] = false;
			
			$Answer[1][1] = true;
			
			$Answer[2][0] = false;
			
			$Answer[2][1] = true;
			
			$Answer[3][0] = true;
			
			$Answer[3][1] = false;
					

			$Answers = $GLOBALS['QuizzManager']->GetCurrentPageData('Answers');


			if (isset($Answers["TABQCU".$QuestionNumber]))
			{
				if (isset($Answers["TABQCU".$QuestionNumber]))
					return ($Answers["TABQCU".$QuestionNumber] == $Column) ? "checked" : "";
				else
					return "";
			}
					
		}


		function GetValue($bInCorrection)
		{	
			$Answers = $GLOBALS['QuizzManager']->GetCurrentPageData('Answers');

			if (isset($Answers["MORETAB"]))
				return stripslashes(($Answers["MORETAB"]));

			return '';
		}


		function DisplayPage($bInCorrection, $bLastQuestion, $HasAnsweredAllQuestions)
		{
?><html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Expert, Microlearning, Lesson 184</title>
<meta name="Generator" content="Epistema EasyQuizz">
<style type="text/css">
		img {	behavior: url(_ressources/iepngfix.htc) }
	</style>
<link rel="stylesheet" href="_images/default_css.css" type="text/css"><script type="text/javascript" language="JavaScript" src="_scripts/prototype.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/language.js"></script><script type="text/javascript" language="JavaScript" src="_default_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_default_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/Question.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/OpenPopupImage.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/Bar.js"></script><script type="text/vbscript" language="VBScript">
	
		// Catch FS Commands in IE, and pass them to the corresponding JavaScript function.
	
		Sub FlashMovie_FSCommand(ByVal command, ByVal args)
			call FlashMovie_DoFSCommand(command, args)
		end sub
		
	</script><script type="text/javascript" language="JavaScript" src="_scripts/InlineMedia.js"></script><script type="text/javascript" language="JavaScript">
		
		var AvailableMedia = new Array();
		
		AvailableMedia[0] = "C1_W37_D4_complaint1.mp3";
		
		AvailableMedia[1] = "C1_W37_D4_stipulate1.mp3";
		
		AvailableMedia[2] = "C1_W37_D4_stipulate2.mp3";
		

		var AvailableReducedImages = new Array();
		</script><?php 

	echo $GLOBALS['QuizzManager']->GetTOCScript();
	
?><meta http-equiv="imagetoolbar" content="no">
</head>
<body dir="ltr" leftmargin="0" marginheight="0" marginwidth="0" topmargin="0">
<link rel="stylesheet" href="_ressources/quizz.css" type="text/css">
<form name="MyQuizzForm" action="_ManagerFrame.php" method="get"><input type="hidden" name="PageNumber" value="4"><input type="hidden" name="Direction" value="0"><input type="hidden" name="ShowAnswer" value="0"><input type="hidden" name="TimeMax" value="0"><input type="hidden" name="MaxTries" value="0"><input type="hidden" name="NavigationDirectAccess" value=""><script type="text/javascript" language="JavaScript">
	// ***** Record and Compare configuration *****

	// Lock applets
	var lockApplets = true;

	// Submit page only when all recordings have been recorded and listened
	var checkSubmit = false;

	// ********************************************
</script><script type="text/javascript" src="_ressources/RecordAndCompare.js" language="JavaScript"></script><table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainTable">
<tr>
<td width="10" valign="top"><img src="_images/t.gif" width="1" height="1"></td>
<td width="800" valign="top">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="5" height="4"></td>
<td width="13" height="4"></td>
<td width="100%" height="4"></td>
<td width="18" height="4" align="right"></td>
<td width="9" height="4"></td>
</tr>
<tr>
<td height="99"></td>
<td colspan="3" valign="top" bgcolor="#FFFFFF">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td class="topBar" align="center"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com
                /pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="800" height="160" id="banner_monday" align="center"><param name="movie" value="_images/banner_1.swf"><embed src="_images/banner_1.swf" quality="high" width="800" height="160" name="movie" align="center" type="application/x-shockwave-flash"></embed></object></td>
</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr valign="bottom">
<td class="menuBar" valign="middle">&nbsp;&nbsp;<?php 
	echo '<span id="question_number" class="question_number" >' . $GLOBALS['QuizzManager']->GetQuestionNavigator() . '</span>';
?></td>
<td height="42" align="right" valign="middle" class="menuBar"><a class="prev" id="prev" href="javascript:Previous()"><span epiLang="PrevString"></span><img src="_images/pag_prec.png" align="middle" border="0" style="margin:10px"></a><?php 
if ($GLOBALS['QuizzManager']->CanShowNextButton())
{
?><a class="next" id="next" href="javascript:Next();"><img src="_images/flech_blc.png" align="middle" border="0" style="margin:10px"><span epiLang="NextString"></span></a><?php 
}

?></td>
</tr>
</table>
</td>
<td>&nbsp;</td>
</tr>
<tr>
<td><img src="_images/t.gif" width="5" height="1"></td>
<td colspan="3" valign="top" bgcolor="#FFFFFF">
<table width="100%" height="300" border="0" cellpadding="0" cellspacing="5" bgcolor="#FFFFFF">
<tr>
<td width="200" valign="top" class="menuGauche">
<table border="0" cellspacing="0" cellpadding="7">
<tr>
<td class="text_noir"><br><br><br><br><?php 

	$NoCounter = false;

	

	if ($GLOBALS['QuizzManager']->GetCurrentPageData("Disabled"))
		$NoCounter = true;
		
	if ($GLOBALS['QuizzManager']->GetCurrentPageData("HintCycle") == 1)
		$NoCounter = true;

	if ($bInCorrection)
		$NoCounter = true;

	if (!$NoCounter && !$GLOBALS['QuizzManager']->NoTimer &&
		( $GLOBALS['QuizzManager']->IsGlobalTimer))
		{
?><table border="0" cellspacing="0" cellpadding="2">
<tr>
<td class="text_noir" valign="top"><span id="TimerBar" class="TimerBar"></span></td>
</tr>
</table><?php 
		}
?>
</td>
</tr>
</table>
</td>
<td class="text_noir" valign="top">
<table class="DarkMainColor" width="100%" border="0" cellspacing="0" cellpadding="7">
<tr>
<td class="text_noir" colspan="2" height="270" valign="top">
<p class="text1Copie"><b><table id="Question_text" class="Question_text" width="100%" cellspacing="0" cellpadding="4">
<tr class="Question_text">
<td class="Question_text ProcessInlineImages" valign="top">[[snd:C1_W37_D4_complaint1.mp3]]<br><br><br>Indicate whether the statement is true or false.</td>
</tr>
<tr class="Question_text">
<td class="Question_text" align="center" valign="top">
</td></tr>
</table></b></p>
<p><table id="Question_answers" class="Question_answers" width="100%" cellspacing="0">
<tr class="Question_answers">
<th class="Question_answers"> </th>
<th class="Question_answers ProcessInlineImages" align="center">True</th>
<th class="Question_answers ProcessInlineImages" align="center">False</th>
</tr>
<tr class="AnswerRow1">
<td class="Question_answers ProcessInlineImages">The customer thought that only the salad was good.</td>
<td class="Question_answers" valign="bottom" align="center"><input type="radio" name="TABQCU0" value="0" <?=$this->IsSelected($bInCorrection, 0, 0) ?> ></td>
<td class="Question_answers" valign="bottom" align="center"><input type="radio" name="TABQCU0" value="1" <?=$this->IsSelected($bInCorrection, 0, 1) ?> ></td>
</tr>
<tr class="AnswerRow0">
<td class="Question_answers ProcessInlineImages">The customer finally agrees to pay for the food.</td>
<td class="Question_answers" valign="bottom" align="center"><input type="radio" name="TABQCU1" value="0" <?=$this->IsSelected($bInCorrection, 1, 0) ?> ></td>
<td class="Question_answers" valign="bottom" align="center"><input type="radio" name="TABQCU1" value="1" <?=$this->IsSelected($bInCorrection, 1, 1) ?> ></td>
</tr>
<tr class="AnswerRow1">
<td class="Question_answers ProcessInlineImages">The manager is forced to call the police.</td>
<td class="Question_answers" valign="bottom" align="center"><input type="radio" name="TABQCU2" value="0" <?=$this->IsSelected($bInCorrection, 2, 0) ?> ></td>
<td class="Question_answers" valign="bottom" align="center"><input type="radio" name="TABQCU2" value="1" <?=$this->IsSelected($bInCorrection, 2, 1) ?> ></td>
</tr>
<tr class="AnswerRow0">
<td class="Question_answers ProcessInlineImages">The customer ate some of the chicken, even though it was overcooked.</td>
<td class="Question_answers" valign="bottom" align="center"><input type="radio" name="TABQCU3" value="0" <?=$this->IsSelected($bInCorrection, 3, 0) ?> ></td>
<td class="Question_answers" valign="bottom" align="center"><input type="radio" name="TABQCU3" value="1" <?=$this->IsSelected($bInCorrection, 3, 1) ?> ></td>
</tr>
</table></p>
<p><span id="Correction" class="Correction"><?php 
	$this->PrintCorrection($bInCorrection);
?></span></p>
<p><div id="Comment" class="Comment"></div>
</p>
</td>
</tr>
</table>
<table width="100%" height="92" border="0" align="right" cellpadding="0" cellspacing="0" class="DarkMainBottom">
<tr valign="middle">
<td width="100%" height="39" align="right" valign="middle" class="menu">&nbsp;<img src="_images/valid.png" align="middle">&nbsp;&nbsp;&nbsp;<?php 
if (empty($GLOBALS['QuizzManager']->HideResultPage))
{
?><a class="submit" id="submit" href="javascript:SubmitPage();"><?php 
	
		$bShowContinue = false;
		
			
		
		if ($bInCorrection)
			$bShowContinue = true;
			
		if ($GLOBALS['QuizzManager']->GetCurrentPageData("HintCycle") == 1)
			$bShowContinue = true;

		if ($GLOBALS['QuizzManager']->GetCurrentPageData("Disabled"))
			$bShowContinue = true;

		if ($bShowContinue)
			echo '<span epiLang="ContinueQuizzString"></span>';
		else
			echo '<span epiLang="ValidString"></span>';
			
		?></a><?php 
}
?></td>
<td rowspan="2" align="left" valign="top" class="menu">&nbsp;</td>
</tr>
<tr valign="middle">
<td width="93%" align="right" valign="middle" class="menu">&nbsp;</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
<td><img src="_images/t.gif" width="9" height="1"></td>
</tr>
<tr>
<td width="5" height="10"></td>
<td width="13" height="10"></td>
<td width="100%" height="10"></td>
<td width="18" height="10" align="right"></td>
<td width="9" height="10"><img src="_images/t.gif" width="9" height="10"></td>
</tr>
</table>
</td>
<td valign="top"><img src="_images/t.gif" width="1" height="1"></td>
</tr>
</table><script type="text/javascript" language="JavaScript">
  bDontScrollDraggables = true;
</script></form><script type="text/javascript" language="JavaScript" src="_scripts/TableauQCM.js"></script><script type="text/javascript" language="JavaScript">

	myQuizz = new TableauQCM("myQuizz", document.MyQuizzForm,
													 4, 
													 "OneRightOnePoint", 
													 true);
	
	
	myQuizz.DontAllowSubmitIfEmpty = false;
	
		myQuizz.RightAnswer[0] = new Array();
		
			myQuizz.RightAnswer[0][0] = false;
		
			myQuizz.RightAnswer[0][1] = true;
		
		myQuizz.RightAnswer[1] = new Array();
		
			myQuizz.RightAnswer[1][0] = false;
		
			myQuizz.RightAnswer[1][1] = true;
		
		myQuizz.RightAnswer[2] = new Array();
		
			myQuizz.RightAnswer[2][0] = false;
		
			myQuizz.RightAnswer[2][1] = true;
		
		myQuizz.RightAnswer[3] = new Array();
		
			myQuizz.RightAnswer[3][0] = true;
		
			myQuizz.RightAnswer[3][1] = false;
		</script><script type="text/javascript" language="JavaScript">

	var StartTime = new Date();	
	var myTimer = null;
	var bCountDown = null;
	var nbsTries;
	
	<?php 
	
	$DisableQuizzNow = false;

	

	if ($GLOBALS['QuizzManager']->GetCurrentPageData("HintCycle") == 1)
		$DisableQuizzNow = true;

	if ($GLOBALS['QuizzManager']->GetCurrentPageData("Disabled"))
		$DisableQuizzNow = true;

	if ($bInCorrection)
	{
		$DisableQuizzNow = true;
		echo "var IsInCorrection = true;\n";
	}
	else
		echo "var IsInCorrection = false;\n";
	
	if ($DisableQuizzNow)
		echo "myQuizz.DisableForm();\n";
	else
		echo "PrepareCounter();\n";
		
?>
	
	function SubmitPage()
	{
		if (myQuizz.enable && !myQuizz.canSubmit())
			return;
	
		<?php 
			$noTries = false;
			
			$noTries = true;
			
		
		if ($bLastQuestion &&
				!$bInCorrection &&
				($DisableQuizzNow || $noTries))
		{
			if (!$HasAnsweredAllQuestions)
			{
		?>
			if (!confirm(EpiLangJS.YouHaveReachTheLastPageButHanventAnsweredAllQuestions))
				return;
		<?php 
			}
			else
			{
		?>

			if (!confirm(EpiLangJS.YouHaveReachTheLastPage))
				return;

		<?php 
			}
		}
		?>
		
		document.MyQuizzForm.Direction.value = 0;
		myQuizz.submit();
	}
	
	function Next()
	{
		if (myQuizz.enable && !myQuizz.canSubmit())
			return;

		<?php 
		
		if ($bLastQuestion && !$bInCorrection)
		{
			if (!$HasAnsweredAllQuestions)
			{
		?>
			if (!confirm(EpiLangJS.YouHaveReachTheLastPageButHanventAnsweredAllQuestions))
				return;
		<?php 
			}
			else
			{
		?>

			if (!confirm(EpiLangJS.YouHaveReachTheLastPage))
				return;

		<?php 
			}	
		}
		?>
		
		document.MyQuizzForm.Direction.value = 1;
		myQuizz.submit();
	}
	
	function Previous()
	{
		document.MyQuizzForm.Direction.value = -1;
		myQuizz.submit();
	}	

	function navigate_to_page(theSelect)
	{
		var SelectValue = theSelect.options[theSelect.selectedIndex].value;
	
		if (SelectValue == 'END')
		{
			if (!FinishNow() && theSelect.originalIndex > 0)
			{
				// revert to the current page selection
				theSelect.selectedIndex = theSelect.originalIndex - 1;
			}
		}
		else
		{
			document.MyQuizzForm.Direction.value = 2;
			myQuizz.submit();
		}
	}

	function DirectAccessTo(randomPageId)
	{
		if (randomPageId == -1)
			if (!FinishNow())
				return;

		document.MyQuizzForm.Direction.value = 4;
		document.MyQuizzForm.NavigationDirectAccess.value = randomPageId;
		
		myQuizz.submit();
	}
	
	function FinishNow()
	{
		<?php 
		
		if (!$bInCorrection)
		{
			if (!$HasAnsweredAllQuestions)
			{
		?>
			if (!confirm(EpiLangJS.YouHanventAnsweredAllQuestions))
				return false;
		<?php 
			}
			else
			{
		?>

			if (!confirm(EpiLangJS.DoYouWantToEndTheQuestionnaireNow))
				return false;

		<?php 
			}	
		}
		?>
		
		document.MyQuizzForm.Direction.value = 3;
		myQuizz.submit();
		return true;
	}
	
	function ShowAnswers()
	{
		document.MyQuizzForm.Direction.value = 0;
		document.MyQuizzForm.ShowAnswer.value = 1;
		myQuizz.submit();
	}	
	
	function PrepareCounter()
	{
<?php 
		if (!$GLOBALS['QuizzManager']->NoTimer)
		{
			$remainingTime = 0;
			$TimerMax = 0;
			$bShowCounter = false;

			if ($GLOBALS['QuizzManager']->IsGlobalTimer)
			{
				$remainingTime = $GLOBALS['QuizzManager']->GlobalTimeMax - $GLOBALS['QuizzManager']->GetSpentTime();

				$TimerMax = $GLOBALS['QuizzManager']->GlobalTimeMax;
				$bShowCounter = true;
			}
	

			if ($bShowCounter)
			{
				$GlobCounter = ($GLOBALS['QuizzManager']->IsGlobalTimer) ? "true" : "false";

				echo 'bCountDown = new Bar("bCountDown", 60, 6, '.$TimerMax.', \'_images/pt_PRBAR_on.gif\', \'_images/pt_PRBAR_off.gif\', \'_images/pt_PRBAR_half_on.gif\', \'_images/pt_PRBAR_half_off.gif\', '.$GlobCounter.', EpiLang.TimerFinished);';
				echo 'if (document.getElementById)';
				echo '	bCountDown.Build(document.getElementById("TimerBar"), '.$remainingTime.', EpiLang.GlobalTimeString, EpiLang.QuestionTimeString);';

				echo 'myTimer = new CountDown("myTimer");';
				echo 'myTimer.OnTick = _OnTick;';
				echo 'myTimer.OnTimer = _OnTimer;';
				echo 'myTimer.Start('.$remainingTime.', '.$TimerMax.', 30);';
			}
		}
?>
	}
	
	
	// -------- Timer management --------------
	function _OnTick()
	{
		bCountDown.SetPosition(myTimer.timeLeft);
	}
	
	function _OnTimer()
	{
		// this is called when the timer arrives to its end.
		
		bCountDown.SetPosition(0);
		
		myQuizz.DisableForm();
		

<?php 
		if (!$bInCorrection && !$GLOBALS['QuizzManager']->NoTimer)
		{
			if ($GLOBALS['QuizzManager']->IsGlobalTimer)
			{
				echo '		alert(EpiLangJS.TheQuizzIsFinished);';
			}
		}
?>		
		
	}

	function ShowHint()
	{
		myQuizz.ShowHint(false, "", "");
	}
	
	// -------- utilities --------------
	function SetElementVisible(elementID, bVisible)
	{
		// Show/Hide functions for pointer objects
		if (document.getElementById && document.getElementById(elementID))
		{
			if (bVisible)
			{
				document.getElementById(elementID).style.visibility = "visible";
			}
			else
			{
			  document.getElementById(elementID).style.visibility = "hidden";
			}
		}	
	}
	
	function layerWrite(id,text) 
	{
		if (document.getElementById && document.getElementById(id))
			document.getElementById(id).innerHTML = text;
	}

<?php 
	// Add the HeartBeat
	if (!empty($GLOBALS['QuizzManager']->UseHeartBeat))
	{
		if (empty($GLOBALS['QuizzManager']->HeartBeatPulsationPeriod))
			$GLOBALS['QuizzManager']->HeartBeatPulsationPeriod = 5; // Seconds

		$TimerMax = 0;

		if ($GLOBALS['QuizzManager']->IsGlobalTimer)
			$TimerMax = $GLOBALS['QuizzManager']->GlobalTimeMax;
	

		echo '	window.setTimeout(\'Pulse()\', '.$GLOBALS['QuizzManager']->HeartBeatPulsationPeriod * 1000 .');' . "\n";
		echo '	function Pulse()' . "\n";
		echo '	{' . "\n";
		echo '		var myAjax = new Ajax.Request(\'_ManagerFrame.php\',' . "\n";
		echo '													{ parameters: { HeartBeat: \'true\'' . "\n";
		echo '																				},' . "\n";
		echo '														onSuccess: OnPulseSuccess' . "\n";
		echo '													});' . "\n";
		echo '	}' . "\n";

		echo '	function OnPulseSuccess(originalRequest, json)' . "\n";
		echo '	{' . "\n";
		echo '		if (json && json.Return == "Acknowledged")' . "\n";
		echo '		{' . "\n";
		echo '			window.setTimeout(\'Pulse()\', '.$GLOBALS['QuizzManager']->HeartBeatPulsationPeriod * 1000 .');' . "\n";
		echo '			myTimer.timeLeft = '.$TimerMax.' - json.ElapsedTime; ' . "\n";
		echo '		}' . "\n";
		echo '	}' . "\n";
	}

?></script><script type="text/javascript" language="JavaScript">
	EpiLangManager.TranslatePage(document) ;
</script></body>
</html><?php 
		} // DisplayPage
	}; // class
} // else
?>
