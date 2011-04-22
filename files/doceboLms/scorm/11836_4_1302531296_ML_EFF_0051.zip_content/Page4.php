<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><?php 
$GLOBALS['CaseSensitive'] = false;

$GLOBALS['AccentSensitive'] = true;


if ($GLOBALS['ClassToInstanciate'] == 'QuestionManager')
{
	class QuestionManager
	{
		var $numberOfMissingWords;	
		var $weight;

		var $Answer;

		function QuestionManager()
		{
			$this->numberOfMissingWords = 8;
			$this->weight = 8;

		
			$anArray = Array();

			
					$anArray[0] = trim("talking to a brick wall");
				

			$this->Answer[0] = $anArray;
			
		
			$anArray = Array();

			
					$anArray[0] = trim("talk shop");
				

			$this->Answer[1] = $anArray;
			
		
			$anArray = Array();

			
					$anArray[0] = trim("all talk and no trousers");
				

			$this->Answer[2] = $anArray;
			
		
			$anArray = Array();

			
					$anArray[0] = trim("talk of the town");
				

			$this->Answer[3] = $anArray;
			
		
			$anArray = Array();

			
					$anArray[0] = trim("talk is cheap");
				

			$this->Answer[4] = $anArray;
			
		
			$anArray = Array();

			
					$anArray[0] = trim("talk out of the back of your head");
				

			$this->Answer[5] = $anArray;
			
		
			$anArray = Array();

			
					$anArray[0] = trim("pep talk");
				

			$this->Answer[6] = $anArray;
			
		
			$anArray = Array();

			
					$anArray[0] = trim("talk turkey");
				

			$this->Answer[7] = $anArray;
			
			
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
		
		var $numberOfMissingWords;	
		var $weight;

		var $Answer;
		
		function QuestionDisplayer()
		{
			$this->QuestionDisplayerBase();
			$this->QuestionType = 'tat';
			
			$this->AnswerText = '';

			$this->AnswerText .= "1.  It is hard to talk to Phillip because I know he never listens to me.  I don't know what he thinks about, but I know he doesn't pay attention.  <u>talking to a brick wall</u><br><br>2.  Tom and Bill get together every Sunday evening to discuss work projects.  <u>talk shop</u><br><br>3.  My boss has a lot of good ideas and she always talks about them, but she won't ever implement any of them.  <u>all talk and no trousers</u><br><br>4.  The whole office is talking about Mary's promotion; it's the most popular topic of conversation right now.  <u>talk of the town</u><br><br>5.  Harry says he can ride a motorcycle, but I will believe it when I see it.  <u>talk is cheap</u><br><br>6.  Mandy doesn't make any sense when she hasn't slept well.  I don't think she slept much last night because I can't understand anything she is saying.  <u>talk out of the back of your head</u><br><br>7.  The teacher really wants to motivate her students because they have a very important exam this week.  She is going to talk to them this afternoon. <u>pep talk</u><br><br>8.  The politician is going to speak with his advisors this week because he wants to discuss an important bill that he is going to vote on.  He wants his staff to be able to question him about it.  He will try to answer all their questions frankly.  <u>talk turkey</u>";

			$this->numberOfMissingWords = 8;
			$this->weight = 8;


			$anArray = Array();
			
					$anArray[0] = trim("talking to a brick wall");
				

			$this->Answer[0] = $anArray;

			$anArray = Array();
			
					$anArray[0] = trim("talk shop");
				

			$this->Answer[1] = $anArray;

			$anArray = Array();
			
					$anArray[0] = trim("all talk and no trousers");
				

			$this->Answer[2] = $anArray;

			$anArray = Array();
			
					$anArray[0] = trim("talk of the town");
				

			$this->Answer[3] = $anArray;

			$anArray = Array();
			
					$anArray[0] = trim("talk is cheap");
				

			$this->Answer[4] = $anArray;

			$anArray = Array();
			
					$anArray[0] = trim("talk out of the back of your head");
				

			$this->Answer[5] = $anArray;

			$anArray = Array();
			
					$anArray[0] = trim("pep talk");
				

			$this->Answer[6] = $anArray;

			$anArray = Array();
			
					$anArray[0] = trim("talk turkey");
				

			$this->Answer[7] = $anArray;
	
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

			$n = str_replace('T', '', $SelectName) - 1;

			if (get_magic_quotes_gpc())
				$UserAnswer = stripslashes($UserAnswer);

			// htmlenquote so that accents go through strtolower
			$UserAnswer = htmlentities($UserAnswer, ENT_COMPAT, 'UTF-8');
			for ($i = 0; $i < count($this->Answer[$n]); $i++)
				$this->Answer[$n][$i] = htmlentities($this->Answer[$n][$i], ENT_COMPAT, 'UTF-8');

			if (!$GLOBALS['AccentSensitive'])
			{
				$UserAnswer = QuestionDisplayer::RemoveAccents($UserAnswer);

				for ($i = 0; $i < count($this->Answer[$n]); $i++)
					$this->Answer[$n][$i] = QuestionDisplayer::RemoveAccents($this->Answer[$n][$i]);
			}

			if (!$GLOBALS['CaseSensitive'])
			{
				$UserAnswer = strtolower($UserAnswer);

				for ($i = 0; $i < count($this->Answer[$n]); $i++)
					$this->Answer[$n][$i] = strtolower($this->Answer[$n][$i]);
			}

			$UserAnswer = trim($UserAnswer);

			if (in_array($UserAnswer, $this->Answer[$n]))
				echo '<img src="_images/correct_answer.gif" style="vertical-align: middle; margin-right: 5px">';
			else
				echo '<img src="_images/wrong_answer.gif" style="vertical-align: middle; margin-right: 5px">';
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
<title>Expressions</title>
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
		
		AvailableMedia[0] = "B1_W11_D1_shout1.mp3";
		
		AvailableMedia[1] = "B1_W11_D1_shout2.mp3";
		

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
<td class="Question_text ProcessInlineImages" valign="top"><i>Write the appropriate expression (exactly as it looks in the list below) in the blank at the end of the sentences.  This exercise might be a bit tough.  Maybe you should read all of the sentences first and then make a decision.</i><br><br><b>talk shop</b><br><b>talking to a brick wall</b><br><b>pep talk</b><br><b>talk turkey</b><br><b>talk is cheap</b><br><b>talk of the town</b><br><b>talk out of the back of your head</b><br><b>all talk and no trousers</b></td>
</tr>
<tr class="Question_text">
<td class="Question_text" align="center" valign="top">
</td></tr>
</table></b></p>
<p class="nswr"><span id="Question_answers" class="Question_answers ProcessInlineImages" style="line-height: 2em">1.  It is hard to talk to Phillip because I know he never listens to me.  I don't know what he thinks about, but I know he doesn't pay attention.  <?php 
$this->echoCorrectAnswer($bInCorrection, 'T1');				
echo '<input type="text" ';

echo ' name="T1" value="'.htmlspecialchars($this->GetValue($bInCorrection, 'T1')).'">';
?><br><br>2.  Tom and Bill get together every Sunday evening to discuss work projects.  <?php 
$this->echoCorrectAnswer($bInCorrection, 'T2');				
echo '<input type="text" ';

echo ' name="T2" value="'.htmlspecialchars($this->GetValue($bInCorrection, 'T2')).'">';
?><br><br>3.  My boss has a lot of good ideas and she always talks about them, but she won't ever implement any of them.  <?php 
$this->echoCorrectAnswer($bInCorrection, 'T3');				
echo '<input type="text" ';

echo ' name="T3" value="'.htmlspecialchars($this->GetValue($bInCorrection, 'T3')).'">';
?><br><br>4.  The whole office is talking about Mary's promotion; it's the most popular topic of conversation right now.  <?php 
$this->echoCorrectAnswer($bInCorrection, 'T4');				
echo '<input type="text" ';

echo ' name="T4" value="'.htmlspecialchars($this->GetValue($bInCorrection, 'T4')).'">';
?><br><br>5.  Harry says he can ride a motorcycle, but I will believe it when I see it.  <?php 
$this->echoCorrectAnswer($bInCorrection, 'T5');				
echo '<input type="text" ';

echo ' name="T5" value="'.htmlspecialchars($this->GetValue($bInCorrection, 'T5')).'">';
?><br><br>6.  Mandy doesn't make any sense when she hasn't slept well.  I don't think she slept much last night because I can't understand anything she is saying.  <?php 
$this->echoCorrectAnswer($bInCorrection, 'T6');				
echo '<input type="text" ';

echo ' name="T6" value="'.htmlspecialchars($this->GetValue($bInCorrection, 'T6')).'">';
?><br><br>7.  The teacher really wants to motivate her students because they have a very important exam this week.  She is going to talk to them this afternoon. <?php 
$this->echoCorrectAnswer($bInCorrection, 'T7');				
echo '<input type="text" ';

echo ' name="T7" value="'.htmlspecialchars($this->GetValue($bInCorrection, 'T7')).'">';
?><br><br>8.  The politician is going to speak with his advisors this week because he wants to discuss an important bill that he is going to vote on.  He wants his staff to be able to question him about it.  He will try to answer all their questions frankly.  <?php 
$this->echoCorrectAnswer($bInCorrection, 'T8');				
echo '<input type="text" ';

echo ' name="T8" value="'.htmlspecialchars($this->GetValue($bInCorrection, 'T8')).'">';
?></span></p>
<p class="crrctn"><span id="Correction" class="Correction"><?php 
	$this->PrintCorrection($bInCorrection);
?></span></p>
<p class="cmmnt"><div id="Comment" class="Comment"></div>
</p>
</div><script type="text/javascript" language="JavaScript">
bDontScrollDraggables = true;
</script></form><script type="text/javascript" language="JavaScript" src="_scripts/TexteATrous.js"></script><script type="text/javascript" language="JavaScript">
	
	myQuizz = new TexteATrous("myQuizz",
														document.MyQuizzForm,
														8,
														false);
	
	
	myQuizz.CaseSensitive = false;
	
	myQuizz.AccentSensitive = true;
	
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
