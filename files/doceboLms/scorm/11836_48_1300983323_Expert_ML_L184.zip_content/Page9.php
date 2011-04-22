<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><?php 
$GLOBALS['CaseSensitive'] = false;

$GLOBALS['AccentSensitive'] = false;


if ($GLOBALS['ClassToInstanciate'] == 'QuestionManager')
{
	class QuestionManager
	{
		var $numberOfMissingWords;	
		var $weight;

		var $Answer;

		function QuestionManager()
		{
			$this->numberOfMissingWords = 2;
			$this->weight = 1;

		
			$anArray = Array();

			
					$anArray[0] = trim("nest egg");
				

			$this->Answer[0] = $anArray;
			
		
			$anArray = Array();

			
					$anArray[0] = trim("squirreling away");
				

			$this->Answer[1] = $anArray;
			
			
		}

		function GetScore()
		{
			$score = 0;

			for ($n = 0; $n < count($this->Answer); $n++) 		
			{
				$UserAnswer = $_GET["T".($n+1)];
				
				if (get_magic_quotes_gpc())
					$UserAnswer = stripslashes($UserAnswer);

				// htmlenquote so that accents go through strtolower
				$UserAnswer = htmlentities($UserAnswer, ENT_COMPAT, 'UTF-8');
				for ($i = 0; $i < count($this->Answer[$n]); $i++)
					$this->Answer[$n][$i] = htmlentities($this->Answer[$n][$i], ENT_COMPAT, 'UTF-8');

				if (!$GLOBALS['AccentSensitive'])
				{
					$UserAnswer = QuestionManager::RemoveAccents($UserAnswer);

					for ($i = 0; $i < count($this->Answer[$n]); $i++)
						$this->Answer[$n][$i] = QuestionManager::RemoveAccents($this->Answer[$n][$i]);
				}
				
				if (!$GLOBALS['CaseSensitive'])
				{
					$UserAnswer = strtolower($UserAnswer);

					for ($i = 0; $i < count($this->Answer[$n]); $i++)
						$this->Answer[$n][$i] = strtolower($this->Answer[$n][$i]);
				}
					
				$UserAnswer = trim($UserAnswer);

				if (in_array($UserAnswer, $this->Answer[$n]))
					$score ++;		
			}

				
			// no negative score allowed :
			if ($score < 0)
				$score = 0;
				

			if ($this->numberOfMissingWords > 0)
				return $this->weight * $score / $this->numberOfMissingWords;
			else
				return 0;
		}

		function RemoveAccents($str)
		{
			return preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde|cedil|ring);/', '$1', $str);
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
			$this->QuestionType = 'tat';
			
			$this->AnswerText = '';

			$this->AnswerText .= "The reason I have this <u>nest egg</u> is because I've been <u>squirreling away</u> about 25% of my pay for the last ten years.";
		}
		
		function PrintCorrection($bInCorrection)
		{
			if ($bInCorrection)
			{
				$AnswerText = "<table width=\"100%\" class=\"Correction\" ><tr><td class=\"Correction ProcessInlineImages\">";

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
					$AnswerText .= $this->AnswerText;
				}
				
				$AnswerText .= "</td></tr></table>";

				echo $AnswerText;
			}
		}		

		function IsSelected($bInCorrection, $SelectName, $OptionText)
		{	
			$Answers = $GLOBALS['QuizzManager']->GetCurrentPageData('Answers');

			if (isset($Answers[$SelectName]))
			{
				$UserAnswer = $Answers[$SelectName];
				
				if (get_magic_quotes_gpc())
					$UserAnswer = stripslashes($UserAnswer);

				// htmlenquote so that accents go through strtolower
				$OptionText = htmlentities($OptionText, ENT_COMPAT, 'UTF-8');
				$UserAnswer = htmlentities($UserAnswer, ENT_COMPAT, 'UTF-8');
		
				if (!$GLOBALS['AccentSensitive'])
				{
					$OptionText = $this->RemoveAccents($OptionText);
					$UserAnswer = $this->RemoveAccents($UserAnswer);
				}
				
				if (!$GLOBALS['CaseSensitive'])
				{
					$OptionText = strtolower($OptionText);
					$UserAnswer = strtolower($UserAnswer);
				}				
	
				return ($UserAnswer == $OptionText) ? "selected" : "";
			}
			
			return ($OptionText == "") ? "selected" : "";
		}
		
		function RemoveAccents($str)
		{
			return preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde|cedil|ring);/', '$1', $str);
		}
		
		function GetValue($bInCorrection, $SelectName)
		{	
			$Answers = $GLOBALS['QuizzManager']->GetCurrentPageData('Answers');

			if (isset($Answers[$SelectName]))
			{
				$UserAnswer = $Answers[$SelectName];
				
				if (get_magic_quotes_gpc())
					$UserAnswer = stripslashes($UserAnswer);
			
				return $UserAnswer; 
			}

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
<form name="MyQuizzForm" action="_ManagerFrame.php" method="get"><input type="hidden" name="PageNumber" value="8"><input type="hidden" name="Direction" value="0"><input type="hidden" name="ShowAnswer" value="0"><input type="hidden" name="TimeMax" value="0"><input type="hidden" name="MaxTries" value="0"><input type="hidden" name="NavigationDirectAccess" value=""><script type="text/javascript" language="JavaScript">
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
<td class="Question_text ProcessInlineImages" valign="top">Fill in the blank with an appropriate expression. Make sure you use it in a grammatically correct way. Choose from the following:<br><b>to have a nest egg </b><br><b>to squirrel away </b><br><b>to stage a comeback </b></td>
</tr>
<tr class="Question_text">
<td class="Question_text" align="center" valign="top">
</td></tr>
</table></b></p>
<p><span id="Question_answers" class="Question_answers ProcessInlineImages" style="line-height: 2em">The reason I have this <?php 
echo '<input type="text" ';

echo ' name="T1" value="'.htmlspecialchars($this->GetValue($bInCorrection, 'T1')).'">';
?> is because I've been <?php 
echo '<input type="text" ';

echo ' name="T2" value="'.htmlspecialchars($this->GetValue($bInCorrection, 'T2')).'">';
?> about 25% of my pay for the last ten years.</span></p>
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
</script></form><script type="text/javascript" language="JavaScript" src="_scripts/TexteATrous.js"></script><script type="text/javascript" language="JavaScript">
	
	myQuizz = new TexteATrous("myQuizz",
														document.MyQuizzForm,
														2,
														false);
	
	
	myQuizz.CaseSensitive = false;
	
	myQuizz.AccentSensitive = false;
	
	myQuizz.DontAllowSubmitIfEmpty = false;
	

	var anArray;

	anArray = new Array();

	
				anArray[0] = "nest egg";
		

	myQuizz.RightAnswer[0] = anArray;


	anArray = new Array();

	
				anArray[0] = "squirreling away";
		

	myQuizz.RightAnswer[1] = anArray;

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
