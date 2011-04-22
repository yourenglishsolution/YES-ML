<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><?php 
if ($GLOBALS['ClassToInstanciate'] == 'QuestionManager')
{
	class QuestionManager
	{
		var $weight;

		var $Answer;

		function QuestionManager()
		{
			$this->weight = 0;
			$this->Answer = "";
		}

		function GetScore()
		{
			return 0;
		}
	};

	
}
else
{
	class QuestionDisplayer extends QuestionDisplayerBase
	{
		function QuestionDisplayer()
		{
			$this->QuestionDisplayerBase();
			$this->QuestionType = 'question_page';
		}
		
		function PrintCorrection($bInCorrection)
		{
			if ($bInCorrection)
			{
				$AnswerText =	"<table width=\"100%\" class=\"Correction\" ><tr><td class=\"ProcessInlineImages Correction\">";

				if ($GLOBALS['QuizzManager']->InCorrection())
					$AnswerText .= '<p align="right" style="font-weight:bold"><a class="TOC_Content" href="_ManagerFrame.php?PageNumber='.$GLOBALS['QuizzManager']->GetCurrentPageIndex().'&Direction=4&NavigationDirectAccess=END"><span epiLang="GoBackToEndPage">Go back to the end page</span></a></p>';
				
				$AnswerText .=	"</td></tr></table>";

				echo $AnswerText;
			}
		}			
		
		function DisplayPage($bInCorrection, $bLastQuestion, $HasAnsweredAllQuestions)
		{
?><html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Adverb clauses</title>
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
		
		AvailableMedia[0] = "B1_W11_D2_simmer1.mp3";
		
		AvailableMedia[1] = "B1_W11_D2_simmer2.mp3";
		

		var AvailableReducedImages = new Array();
		</script><?php 

	echo $GLOBALS['QuizzManager']->GetTOCScript();
	
?><meta http-equiv="imagetoolbar" content="no">
</head>
<body dir="ltr">
<form name="MyQuizzForm" action="_ManagerFrame.php" method="get"><input type="hidden" name="PageNumber" value="1"><input type="hidden" name="Direction" value="0"><input type="hidden" name="ShowAnswer" value="0"><input type="hidden" name="TimeMax" value="0"><input type="hidden" name="MaxTries" value="0"><input type="hidden" name="NavigationDirectAccess" value=""><script type="text/javascript" language="JavaScript">
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
<td class="Question_text ProcessInlineImages" valign="top"><sup><b>Adverb clauses</b></sup><br><sup><b></b></sup><br>Bob saw his cousin <b>when she was in New York</b>. <b> During her visit</b>, they went to a couple of museums, the Statue of Liberty, a few plays and the zoo.  Bob was glad that she came, <b>although her visit was quite tiring</b>.  <b>After she left</b>, Bob slept for ten hours.<br><br>The parts in bold are adverb clauses.  These types of clauses express time, why something happens, contrasts, causes &amp; effects and conditions.  They are not complete sentences therefore they must be used in conjunction with another clause.<br><br><b>After playing all day,</b> the children were quite hungry.<br><br>Adverb clauses can include almost all types of speech but the following words usually start adverb clauses.  Review the following list of words but keep in mind that it is not a complete list.<br><br><u>To talk about time:</u><br>after, before, when, while, as, by the time, whenever, since, until, as soon as, once, as long as, during<br><b>Before calling home</b>, Andy checked his watch to make sure he wouldn't wake up his wife.<br><br><u>To show cause &amp; effect relationships:</u><br>because, since, now that, as, as long as, inasmuch as, so (that), in order that, therefore<br><b>Because she was allergic to nuts</b>, Cindy refused the cookies.<br><br><u>To compare and contrast things:</u><br>although, even though, though, whereas, while, despite, in spite of<br>Hank hates swimming<b> although he likes the beach</b>.<br><br><u>To express conditions:</u><br>if, unless, only if, whether or not, even if, providing (that), in order to, provided (that), in case, in the event (that), suppose, supposing<br><b>If you're not going to eat the hamburger</b>, give it to me.</td>
</tr>
<tr class="Question_text">
<td class="Question_text" align="center" valign="top">
</td></tr>
</table></b></p>
<p class="nswr"></p>
<p class="crrctn"><span id="Correction" class="Correction"><?php 
	$this->PrintCorrection($bInCorrection);
?></span></p>
<p class="cmmnt"><div id="Comment" class="Comment"></div>
</p>
</div><script type="text/javascript" language="JavaScript">
bDontScrollDraggables = true;
</script></form><script type="text/javascript" language="JavaScript">

	myQuizz = new Question("myQuizz", document.MyQuizzForm);
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
