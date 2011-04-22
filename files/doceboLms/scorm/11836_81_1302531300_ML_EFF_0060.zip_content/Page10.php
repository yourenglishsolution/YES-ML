<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><?php 
if ($GLOBALS['ClassToInstanciate'] == 'QuestionManager')
{
	class QuestionManager
	{
		var $ScoringMethod;
		var $inQCUMode;
		var $weight;

		var $Answer;

		function QuestionManager()
		{
			$this->ScoringMethod = "OneRightOnePoint";
			$this->inQCUMode = true;
			$this->weight = 1;

			
			$this->Answer[0] = false;
			$this->AnswersWeight[0] = -1 -0;
			
			$this->Answer[1] = false;
			$this->AnswersWeight[1] = -1 -0;
			
			$this->Answer[2] = true;
			$this->AnswersWeight[2] = -1 -0;
			
		}

		function GetScore()
		{
			$score = 0;



			if($this->ScoringMethod == "UserDefined")
			{
				$MaxRowWeight = 0;

				for($j = 0; $j < count($this->Answer); $j++)
					if ($MaxRowWeight < $this->AnswersWeight[$j])
						$MaxRowWeight = $this->AnswersWeight[$j];

				$NbsRightAnswer = 0;
				
				if (isset($_GET["QCU"])) 
					$score = $this->AnswersWeight[$_GET["QCU"]];
				
				// set the score back to 1
				if ($MaxRowWeight != 0)
					$score = ($score / $MaxRowWeight);
			}
			else
			{
				if (isset($_GET["QCU"])) 
					$score = ($this->Answer[$_GET["QCU"]] ? 1 : 0);
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

		var $ScoringMethod;
		var $inQCUMode;
		var $weight;

		var $Answer;

		function QuestionDisplayer()
		{
			$this->QuestionDisplayerBase();
			$this->QuestionType = 'qcuqcm';

			$this->ScoringMethod = "OneRightOnePoint";
			$this->inQCUMode = true;
			$this->weight = 1;

			
			$this->Answer[0] = false;
			$this->AnswersWeight[0] = -1 -0;
			
			$this->Answer[1] = false;
			$this->AnswersWeight[1] = -1 -0;
			
			$this->Answer[2] = true;
			$this->AnswersWeight[2] = -1 -0;
			
		}

		function GetQuestionsArray()
		{
			$ret = array();

			
			$ret[] = array('correct' => false,
									   'html' => "operas"
			);			
			
			$ret[] = array('correct' => false,
									   'html' => "haiku"
			);			
			
			$ret[] = array('correct' => true,
									   'html' => "limerick"
			);			
			
			
			return $ret;
		}

		function PrintCorrection($bInCorrection)
		{
			if ($bInCorrection)
			{
				$answers = $this->GetQuestionsArray();
				$theQuestionHasACorrectAnswer = false;
				foreach ($answers as $anAnswer)
					if ($anAnswer['correct'])
						$theQuestionHasACorrectAnswer = true;

				if (!$theQuestionHasACorrectAnswer)
					return;
					
				$AnswerText =	'<table width="100%" class="Correction" ><tr><td class="Correction ProcessInlineImages">';

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
					$AnswerText .= '<table width="100%" border="0" cellspacing="0" cellpadding="1">';
					$AnswerText .= '<tr>';
					$AnswerText .= "<td class=\"Correction ProcessInlineImages\" align=\"left\">";				

					$CorrectAnswers = $this->GetQuestionsArray();

					foreach ($CorrectAnswers as $anAnswer)
					{
						if ($anAnswer['correct'])
							$AnswerText .= '<li>' . $anAnswer['html'] . '</li>';
					}

					$AnswerText .= '</td></tr>';
					$AnswerText .= '</table>';
				}
			
				if ($GLOBALS['QuizzManager']->InCorrection())
					$AnswerText .= '<p align="right" style="font-weight:bold"><a class="TOC_Content" href="_ManagerFrame.php?PageNumber='.$GLOBALS['QuizzManager']->GetCurrentPageIndex().'&Direction=4&NavigationDirectAccess=END"><span epiLang="GoBackToEndPage">Go back to the end page</span></a></p>';
					
				$AnswerText .= '</td></tr></table>';

				echo $AnswerText;
			}
		}
		

		function echoCorrectAnswer($bInCorrection, $Column)
		{
			if (!$bInCorrection)
				return;
		
			$Answers = $GLOBALS['QuizzManager']->GetCurrentPageData('Answers');

			
			$Answer[0] = false;
			
			$Answer[1] = false;
			
			$Answer[2] = true;
			

			$UserAnswers = array();

			for($j = 0; $j < count($Answer); $j++)
				$UserAnswers[$j] = false;


			if (isset($Answers["QCU"]))
				for($j = 0; $j < count($Answers["QCU"]); $j++)
					$UserAnswers[$Answers["QCU"][$j]] = true;


			if ($Answer[$Column])
			{
				if ($UserAnswers[$Column])
					echo '<img src="_images/correct_answer.gif" style="margin-right: 5px">';
				else
					echo '<img src="_images/expected_answer.gif" style="margin-right: 5px">';
			}
			else
			{
				if ($UserAnswers[$Column])
					echo '<img src="_images/wrong_answer.gif" style="margin-right: 5px">';
				else
					echo '<img src="_images/wrong_answer.gif" style="visibility: hidden; margin-right: 5px">';
			}
		}		
		
		function IsSelected($bInCorrection, $Column)
		{
			$Answers = $GLOBALS['QuizzManager']->GetCurrentPageData('Answers');


			if (isset($Answers["QCU"]))
				return ($Answers["QCU"] == $Column) ? "checked" : "";
			else
				return "";

		}

		function GetValue($bInCorrection, $SelectName)
		{	
			$Answers = $GLOBALS['QuizzManager']->GetCurrentPageData('Answers');

			if (isset($Answers[$SelectName]))
				return stripslashes($Answers[$SelectName]); 

			return '';
		}
		
		function DisplayPage($bInCorrection, $bLastQuestion, $HasAnsweredAllQuestions)
		{
	
?><html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Word power</title>
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Droid+Sans|Droid+Serif|Droid+Sans+Mono">
<link rel="stylesheet" type="text/css" href="_ressources/reset.css" media="all">
<link rel="stylesheet" type="text/css" href="_ressources/style.css" media="all">
<link rel="stylesheet" type="text/css" href="_ressources/stylesheets/formalize.css" media="all"><script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script><script src="_ressources/javascripts/jquery.formalize.js"></script><link rel="stylesheet" href="_images/default_css.css" type="text/css"><script type="text/javascript" language="JavaScript" src="_scripts/prototype.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/language.js"></script><script type="text/javascript" language="JavaScript" src="_default_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_default_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/Question.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/OpenPopupImage.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/Bar.js"></script><script type="text/vbscript" language="VBScript">
	
		// Catch FS Commands in IE, and pass them to the corresponding JavaScript function.
	
		Sub FlashMovie_FSCommand(ByVal command, ByVal args)
			call FlashMovie_DoFSCommand(command, args)
		end sub
		
	</script><script type="text/javascript" language="JavaScript" src="_scripts/InlineMedia.js"></script><script type="text/javascript" language="JavaScript">
		
		var AvailableMedia = new Array();
		
		AvailableMedia[0] = "B1_W12_D5_sew1.mp3";
		
		AvailableMedia[1] = "B1_W12_D5_sew2.mp3";
		
		AvailableMedia[2] = "B1_W6_D3_pond1.mp3";
		
		AvailableMedia[3] = "B1_W6_D3_pond2.mp3";
		

		var AvailableReducedImages = new Array();
		</script><?php 

	echo $GLOBALS['QuizzManager']->GetTOCScript();
	
?><meta http-equiv="imagetoolbar" content="no">
</head>
<body dir="ltr">
<form name="MyQuizzForm" action="_ManagerFrame.php" method="get"><input type="hidden" name="PageNumber" value="9"><input type="hidden" name="Direction" value="0"><input type="hidden" name="ShowAnswer" value="0"><input type="hidden" name="TimeMax" value="0"><input type="hidden" name="MaxTries" value="0"><input type="hidden" name="NavigationDirectAccess" value=""><script type="text/javascript" language="JavaScript">
  // ***** Record and Compare configuration *****

  // Lock applets
  var lockApplets = true;

  // Submit page only when all recordings have been recorded and listened
  var checkSubmit = false;

  // ********************************************
</script><div id="core">
</div>
<div id="cntrls">
<div id="cntrls_c">
<div id="cntrls_dcrs"></div>
<div id="cntrls_btns"><?php 
	echo '<span id="question_number" class="question_number" >' . $GLOBALS['QuizzManager']->GetQuestionNavigator() . '</span>';
?><a class="prev" id="prev" href="javascript:Previous()"></a><a class="submit" id="next" href="javascript:SubmitPage();"></a></div>
</div>
</div>
<div id="lssn">
<p class="txt"><b><table id="Question_text" class="Question_text" width="100%" cellspacing="0" cellpadding="4">
<tr class="Question_text">
<td class="Question_text ProcessInlineImages" valign="top"><i>A ____ is a five-line poem with a strict form and are frequently witty or humorous.</i></td>
</tr>
<tr class="Question_text">
<td class="Question_text" align="center" valign="top">
</td></tr>
</table></b></p>
<p class="nswr"><table id="Question_answers" class="Question_answers" width="100%" cellspacing="0">
<tr class="AnswerRow1">
<td class="Question_answers" valign="top" align="center" width="50"><?$this->echoCorrectAnswer($bInCorrection, 0); ?><input id="Q1" type="radio" name="QCU" value="0" <?=$this->IsSelected($bInCorrection, 0) ?> ></td>
<td valign="top" class="Question_answers ProcessInlineImages">operas </td><td valign="top" class="Question_answers">
</td>
</tr>
<tr class="AnswerRow0">
<td class="Question_answers" valign="top" align="center" width="50"><?$this->echoCorrectAnswer($bInCorrection, 1); ?><input id="Q2" type="radio" name="QCU" value="1" <?=$this->IsSelected($bInCorrection, 1) ?> ></td>
<td valign="top" class="Question_answers ProcessInlineImages">haiku </td><td valign="top" class="Question_answers">
</td>
</tr>
<tr class="AnswerRow1">
<td class="Question_answers" valign="top" align="center" width="50"><?$this->echoCorrectAnswer($bInCorrection, 2); ?><input id="Q3" type="radio" name="QCU" value="2" <?=$this->IsSelected($bInCorrection, 2) ?> ></td>
<td valign="top" class="Question_answers ProcessInlineImages">limerick </td><td valign="top" class="Question_answers">
</td>
</tr>
</table></p>
<p class="crrctn"><span id="Correction" class="Correction"><?php 
	$this->PrintCorrection($bInCorrection);
?></span></p>
<p class="cmmnt"><div id="Comment" class="Comment"></div>
</p>
</div><script type="text/javascript" language="JavaScript">
bDontScrollDraggables = true;
</script></form><script type="text/javascript" language="JavaScript" src="_scripts/TableauQCM.js"></script><script type="text/javascript" language="JavaScript">

	myQuizz = new TableauQCM("myQuizz", document.MyQuizzForm, 
													 1, "OneRightOnePoint",
													 true);
	
	
	myQuizz.DontAllowSubmitIfEmpty = false;
	

	myQuizz.RightAnswer[0] = new Array();
	myQuizz.AnswersWeight[0] = new Array();
	
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
