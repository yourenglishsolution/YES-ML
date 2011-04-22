<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><?php 
if ($GLOBALS['ClassToInstanciate'] == 'QuestionManager')
{
	class QuestionManager
	{
		var $numberOfMissingWords;	
		var $weight;

		var $Answer;
		var $ScoringMethod;

		function QuestionManager()
		{
			$this->ScoringMethod = "OneRightOnePoint";
		
			$this->numberOfMissingWords = 4;
			$this->weight = 4;

			
			$this->Answer[0] = "Please just go away!";
			
			$this->Answer[1] = "Kelly doesn't agree with the outcome but it is the popular opinion.";
			
			$this->Answer[2] = "I am going to keep explaining how to use the new equipment until everyone understands.";
			
			$this->Answer[3] = "I doubt Mitch will ever get married.";
							
		}

		function GetScore()
		{
			$score = 0;
			$maxScore = $this->numberOfMissingWords;

			if ($maxScore == 0)
				return 0;

			$bQuestionIsAnswered = false;

			for ($n = 0; $n < count($this->Answer); $n++) 		
			{
				if ($_GET["M".($n+1)] === '')
					continue;
				
				$bQuestionIsAnswered = true;
				
				$UserAnswer = $_GET["M".($n+1)];
				
				if (get_magic_quotes_gpc())
					$UserAnswer = stripslashes($UserAnswer);
					
				if ($this->Answer[$n] == $UserAnswer)
					$score++;
	
			}

			if (!$bQuestionIsAnswered)
				return 0;

			if ($this->ScoringMethod == 'AllRight')
				$score = ($score == $maxScore) ? $this->weight : -1 * $this->weight;
			else
				$score = ($this->weight * $score) / $maxScore;

	
			// no negative score allowed :
			if ($score < 0)
				$score = 0;
	
			
			return $score;
		}
	};

	
}
else
{
	class QuestionDisplayer extends QuestionDisplayerBase
	{
		var $AnswerText;

		var $numberOfMissingWords;	
		var $weight;

		var $Answer;
		var $ScoringMethod;

		function QuestionDisplayer()
		{
			$this->QuestionDisplayerBase();
			$this->QuestionType = 'matching_question';

			$this->ScoringMethod = "OneRightOnePoint";
		
			$this->numberOfMissingWords = 4;
			$this->weight = 4;

			
			$this->Answer[0] = "Please just go away!";
			
			$this->Answer[1] = "Kelly doesn't agree with the outcome but it is the popular opinion.";
			
			$this->Answer[2] = "I am going to keep explaining how to use the new equipment until everyone understands.";
			
			$this->Answer[3] = "I doubt Mitch will ever get married.";
					
		}

		function PrintCorrection($bInCorrection)
		{
			if ($bInCorrection)
			{
				$AnswerText = "<table width=\"100%\" class=\"Correction\" ><tr><td class=\"ProcessInlineImages Correction\">";

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
					$AnswerText .= "go fly a kite &rarr; Please just go away!<br>ride the tide &rarr; Kelly doesn't agree with the outcome but it is the popular opinion.<br>drive home &rarr; I am going to keep explaining how to use the new equipment until everyone understands.<br>pigs might fly &rarr; I doubt Mitch will ever get married.<br>";
				}

				if ($GLOBALS['QuizzManager']->InCorrection())
					$AnswerText .= '<p align="right" style="font-weight:bold"><a class="TOC_Content" href="_ManagerFrame.php?PageNumber='.$GLOBALS['QuizzManager']->GetCurrentPageIndex().'&Direction=4&NavigationDirectAccess=END"><span epiLang="GoBackToEndPage">Go back to the end page</span></a></p>';

				$AnswerText .= "</td></tr></table>";

				echo $AnswerText;
			}
		}		

		function echoCorrectAnswer($bInCorrection, $SelectName)
		{
			if (!$bInCorrection)
				return;

			$GetData = $GLOBALS['QuizzManager']->GetCurrentPageData('Answers');

			if (!isset($GetData[$SelectName]))
			{
				echo '<img src="_images/wrong_answer.gif" style="vertical-align: middle; margin-right: 5px">';
				return;
			}
			
			$UserAnswer = $GetData[$SelectName];

			$n = str_replace('M', '', $SelectName) - 1;

			if (get_magic_quotes_gpc())
				$UserAnswer = stripslashes($UserAnswer);

			if ($this->Answer[$n] == $GetData["M".($n+1)])
				echo '<img src="_images/correct_answer.gif" style="vertical-align: middle; margin-right: 5px">';
			else
				echo '<img src="_images/wrong_answer.gif" style="vertical-align: middle; margin-right: 5px">';
		}

		function IsSelected($bInCorrection, $SelectName, $OptionText)
		{	
			$Answers = $GLOBALS['QuizzManager']->GetCurrentPageData('Answers');

			if (isset($Answers[$SelectName]))
			{
				$OptionText = str_replace("&gt;", ">", $OptionText);
				$OptionText = str_replace("&lt;", "<", $OptionText);
				
				if (get_magic_quotes_gpc())
					$Answers[$SelectName] = stripslashes($Answers[$SelectName]);
				
				return ($Answers[$SelectName] == $OptionText) ? "selected" : "";
			}
			
			return ($OptionText == "") ? "selected" : "";
		}

		function DisplayPage($bInCorrection, $bLastQuestion, $HasAnsweredAllQuestions)
		{
?><html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Expressions</title>
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Droid+Sans|Droid+Serif|Droid+Sans+Mono">
<link rel="stylesheet" type="text/css" href="_ressources/reset.css" media="all">
<link rel="stylesheet" type="text/css" href="_ressources/style.css" media="all">
<link rel="stylesheet" type="text/css" href="_ressources/stylesheets/formalize.css" media="all"><script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script><script src="_ressources/javascripts/jquery.formalize.js"></script><script type="text/javascript" language="javascript" src="_scripts/ResetAllCombos.js"></script><meta http-equiv="imagetoolbar" content="no">
<link rel="stylesheet" href="_images/default_css.css" type="text/css"><script type="text/javascript" language="JavaScript" src="_scripts/prototype.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/language.js"></script><script type="text/javascript" language="JavaScript" src="_default_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_default_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/Question.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/OpenPopupImage.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/Bar.js"></script><script type="text/vbscript" language="VBScript">
	
		// Catch FS Commands in IE, and pass them to the corresponding JavaScript function.
	
		Sub FlashMovie_FSCommand(ByVal command, ByVal args)
			call FlashMovie_DoFSCommand(command, args)
		end sub
		
	</script><script type="text/javascript" language="JavaScript" src="_scripts/InlineMedia.js"></script><script type="text/javascript" language="JavaScript">
		
		var AvailableMedia = new Array();
		
		AvailableMedia[0] = "B1_W12_D1_recollection1.mp3";
		
		AvailableMedia[1] = "B1_W12_D1_recollection2.mp3";
		
		AvailableMedia[2] = "B1_W12_D2_pay1.mp3";
		
		AvailableMedia[3] = "B1_W12_D2_pay2.mp3";
		

		var AvailableReducedImages = new Array();
		</script><?php 

	echo $GLOBALS['QuizzManager']->GetTOCScript();
	
?><meta http-equiv="imagetoolbar" content="no">
</head>
<body dir="ltr">
<form name="MyQuizzForm" action="_ManagerFrame.php" method="get"><input type="hidden" name="PageNumber" value="3"><input type="hidden" name="Direction" value="0"><input type="hidden" name="ShowAnswer" value="0"><input type="hidden" name="TimeMax" value="0"><input type="hidden" name="MaxTries" value="0"><input type="hidden" name="NavigationDirectAccess" value=""><script type="text/javascript" language="JavaScript">
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
<td class="Question_text ProcessInlineImages" valign="top"><i>Match the expression with the sentence it could be used with or could replace.</i></td>
</tr>
<tr class="Question_text">
<td class="Question_text" align="center" valign="top">
</td></tr>
</table></b></p>
<p class="nswr"><table id="Question_answers" class="Question_answers" width="100%" cellspacing="0">
<tr class="AnswerRow1">
<td class="Question_answers ProcessInlineImages">go fly a kite </td>
<td class="Question_answers" valign="middle"><?php $this->echoCorrectAnswer($bInCorrection, 'M1'); ?><select onclick="resetAllCombos(this);" size="1" class="TAT_Selects" name="M1"><option value="" <?=$this->IsSelected($bInCorrection, "M1", "")?> ></option><option value="I am going to keep explaining how to use the new equipment until everyone understands." <?=$this->IsSelected($bInCorrection, "M1", "I am going to keep explaining how to use the new equipment until everyone understands.")?> >I am going to keep explaining how to use the new equipment until everyone understands.</option><option value="I doubt Mitch will ever get married." <?=$this->IsSelected($bInCorrection, "M1", "I doubt Mitch will ever get married.")?> >I doubt Mitch will ever get married.</option><option value="Kelly doesn't agree with the outcome but it is the popular opinion." <?=$this->IsSelected($bInCorrection, "M1", "Kelly doesn't agree with the outcome but it is the popular opinion.")?> >Kelly doesn't agree with the outcome but it is the popular opinion.</option><option value="Please just go away!" <?=$this->IsSelected($bInCorrection, "M1", "Please just go away!")?> >Please just go away!</option></select></td>
</tr>
<tr class="AnswerRow0">
<td class="Question_answers ProcessInlineImages">ride the tide </td>
<td class="Question_answers" valign="middle"><?php $this->echoCorrectAnswer($bInCorrection, 'M2'); ?><select onclick="resetAllCombos(this);" size="1" class="TAT_Selects" name="M2"><option value="" <?=$this->IsSelected($bInCorrection, "M2", "")?> ></option><option value="I am going to keep explaining how to use the new equipment until everyone understands." <?=$this->IsSelected($bInCorrection, "M2", "I am going to keep explaining how to use the new equipment until everyone understands.")?> >I am going to keep explaining how to use the new equipment until everyone understands.</option><option value="I doubt Mitch will ever get married." <?=$this->IsSelected($bInCorrection, "M2", "I doubt Mitch will ever get married.")?> >I doubt Mitch will ever get married.</option><option value="Kelly doesn't agree with the outcome but it is the popular opinion." <?=$this->IsSelected($bInCorrection, "M2", "Kelly doesn't agree with the outcome but it is the popular opinion.")?> >Kelly doesn't agree with the outcome but it is the popular opinion.</option><option value="Please just go away!" <?=$this->IsSelected($bInCorrection, "M2", "Please just go away!")?> >Please just go away!</option></select></td>
</tr>
<tr class="AnswerRow1">
<td class="Question_answers ProcessInlineImages">drive home </td>
<td class="Question_answers" valign="middle"><?php $this->echoCorrectAnswer($bInCorrection, 'M3'); ?><select onclick="resetAllCombos(this);" size="1" class="TAT_Selects" name="M3"><option value="" <?=$this->IsSelected($bInCorrection, "M3", "")?> ></option><option value="I am going to keep explaining how to use the new equipment until everyone understands." <?=$this->IsSelected($bInCorrection, "M3", "I am going to keep explaining how to use the new equipment until everyone understands.")?> >I am going to keep explaining how to use the new equipment until everyone understands.</option><option value="I doubt Mitch will ever get married." <?=$this->IsSelected($bInCorrection, "M3", "I doubt Mitch will ever get married.")?> >I doubt Mitch will ever get married.</option><option value="Kelly doesn't agree with the outcome but it is the popular opinion." <?=$this->IsSelected($bInCorrection, "M3", "Kelly doesn't agree with the outcome but it is the popular opinion.")?> >Kelly doesn't agree with the outcome but it is the popular opinion.</option><option value="Please just go away!" <?=$this->IsSelected($bInCorrection, "M3", "Please just go away!")?> >Please just go away!</option></select></td>
</tr>
<tr class="AnswerRow0">
<td class="Question_answers ProcessInlineImages">pigs might fly </td>
<td class="Question_answers" valign="middle"><?php $this->echoCorrectAnswer($bInCorrection, 'M4'); ?><select onclick="resetAllCombos(this);" size="1" class="TAT_Selects" name="M4"><option value="" <?=$this->IsSelected($bInCorrection, "M4", "")?> ></option><option value="I am going to keep explaining how to use the new equipment until everyone understands." <?=$this->IsSelected($bInCorrection, "M4", "I am going to keep explaining how to use the new equipment until everyone understands.")?> >I am going to keep explaining how to use the new equipment until everyone understands.</option><option value="I doubt Mitch will ever get married." <?=$this->IsSelected($bInCorrection, "M4", "I doubt Mitch will ever get married.")?> >I doubt Mitch will ever get married.</option><option value="Kelly doesn't agree with the outcome but it is the popular opinion." <?=$this->IsSelected($bInCorrection, "M4", "Kelly doesn't agree with the outcome but it is the popular opinion.")?> >Kelly doesn't agree with the outcome but it is the popular opinion.</option><option value="Please just go away!" <?=$this->IsSelected($bInCorrection, "M4", "Please just go away!")?> >Please just go away!</option></select></td>
</tr>
</table></p>
<p class="crrctn"><span id="Correction" class="Correction"><?php 
	$this->PrintCorrection($bInCorrection);
?></span></p>
<p class="cmmnt"><div id="Comment" class="Comment"></div>
</p>
</div><script type="text/javascript" language="JavaScript">
bDontScrollDraggables = true;
</script></form><script type="text/javascript" language="JavaScript" src="_scripts/Matching.js"></script><script type="text/javascript" language="JavaScript">
		
	myQuizz = new Question_Matching("myQuizz", document.MyQuizzForm, "OneRightOnePoint");
		
	
	myQuizz.DontAllowSubmitIfEmpty = false;
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
