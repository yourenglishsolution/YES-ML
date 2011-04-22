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
			$this->NumberOfQuestions = 7; // more than one means a tabular QCM/QCU
			$this->ScoringMethod = "";
			$this->inQCUMode = true;
			$this->weight = 7;
			
			
			$this->Answer[0][0] = false;
			
			$this->Answer[0][1] = true;
			
			$this->Answer[1][0] = true;
			
			$this->Answer[1][1] = false;
			
			$this->Answer[2][0] = false;
			
			$this->Answer[2][1] = true;
			
			$this->Answer[3][0] = false;
			
			$this->Answer[3][1] = true;
			
			$this->Answer[4][0] = true;
			
			$this->Answer[4][1] = false;
			
			$this->Answer[5][0] = true;
			
			$this->Answer[5][1] = false;
			
			$this->Answer[6][0] = false;
			
			$this->Answer[6][1] = true;
						
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
	
		var $NumberOfQuestions;
		var $ScoringMethod;
		var $inQCUMode;
		var $weight;

		var $Answer;
			
		function QuestionDisplayer()
		{
			$this->QuestionDisplayerBase();
			$this->QuestionType = 'tabqcuqcm';

			$this->NumberOfQuestions = 7; // more than one means a tabular QCM/QCU
			$this->ScoringMethod = "";
			$this->inQCUMode = true;
			$this->weight = 7;
			
			
			$this->Answer[0][0] = false;
			
			$this->Answer[0][1] = true;
			
			$this->Answer[1][0] = true;
			
			$this->Answer[1][1] = false;
			
			$this->Answer[2][0] = false;
			
			$this->Answer[2][1] = true;
			
			$this->Answer[3][0] = false;
			
			$this->Answer[3][1] = true;
			
			$this->Answer[4][0] = true;
			
			$this->Answer[4][1] = false;
			
			$this->Answer[5][0] = true;
			
			$this->Answer[5][1] = false;
			
			$this->Answer[6][0] = false;
			
			$this->Answer[6][1] = true;
					
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
					$AnswerText .= "<tr><td class=\"Correction\" valign=\"top\">cockapoo</td>";
					$AnswerText .= "<td class=\"Correction\" valign=\"top\" align=\"center\">";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "hybrid<br>";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "</td></tr>";
					$AnswerText .= "";
					$AnswerText .= "<tr><td class=\"Correction\" valign=\"top\">zebra</td>";
					$AnswerText .= "<td class=\"Correction\" valign=\"top\" align=\"center\">";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "purebred<br>";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "</td></tr>";
					$AnswerText .= "";
					$AnswerText .= "<tr><td class=\"Correction\" valign=\"top\">grolar</td>";
					$AnswerText .= "<td class=\"Correction\" valign=\"top\" align=\"center\">";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "hybrid<br>";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "</td></tr>";
					$AnswerText .= "";
					$AnswerText .= "<tr><td class=\"Correction\" valign=\"top\">zorse</td>";
					$AnswerText .= "<td class=\"Correction\" valign=\"top\" align=\"center\">";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "hybrid<br>";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "</td></tr>";
					$AnswerText .= "";
					$AnswerText .= "<tr><td class=\"Correction\" valign=\"top\">donkey</td>";
					$AnswerText .= "<td class=\"Correction\" valign=\"top\" align=\"center\">";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "purebred<br>";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "</td></tr>";
					$AnswerText .= "";
					$AnswerText .= "<tr><td class=\"Correction\" valign=\"top\">jaguar</td>";
					$AnswerText .= "<td class=\"Correction\" valign=\"top\" align=\"center\">";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "purebred<br>";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "</td></tr>";
					$AnswerText .= "";
					$AnswerText .= "<tr><td class=\"Correction\" valign=\"top\">cama</td>";
					$AnswerText .= "<td class=\"Correction\" valign=\"top\" align=\"center\">";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "hybrid<br>";
					$AnswerText .= "";
					$AnswerText .= "";
					$AnswerText .= "</td></tr>";
					$AnswerText .= "";
					$AnswerText .= "</table>";
				}
				
				if ($GLOBALS['QuizzManager']->InCorrection())
					$AnswerText .= '<p align="right" style="font-weight:bold"><a class="TOC_Content" href="_ManagerFrame.php?PageNumber='.$GLOBALS['QuizzManager']->GetCurrentPageIndex().'&Direction=4&NavigationDirectAccess=END"><span epiLang="GoBackToEndPage">Go back to the end page</span></a></p>';

				$AnswerText .= "</td></tr></table>";
				
				echo $AnswerText;
			}
		}
		
		function echoCorrectAnswer($bInCorrection, $QuestionNumber, $Column)
		{
			if (!$bInCorrection)
				return;
		
		
			$Answer[0][0] = false;
			
			$Answer[0][1] = true;
			
			$Answer[1][0] = true;
			
			$Answer[1][1] = false;
			
			$Answer[2][0] = false;
			
			$Answer[2][1] = true;
			
			$Answer[3][0] = false;
			
			$Answer[3][1] = true;
			
			$Answer[4][0] = true;
			
			$Answer[4][1] = false;
			
			$Answer[5][0] = true;
			
			$Answer[5][1] = false;
			
			$Answer[6][0] = false;
			
			$Answer[6][1] = true;
					

			$Answers = $GLOBALS['QuizzManager']->GetCurrentPageData('Answers');


			$NumberOfQuestions = 7; // more than one means a tabular QCM/QCU

			$UserAnswers  = array();

			for($i = 0; $i < $NumberOfQuestions; $i++) 
			{
				$UserAnswers[$i] = array();

				for($j = 0; $j < count($Answer[$i]); $j++)
				{
					$UserAnswers[$i][$j] = false;
				}


				if (isset($Answers["TABQCU".$i]))
				{
					for($j = 0; $j < count($Answers["TABQCU".$i]); $j++)
						$UserAnswers[$i][$Answers["TABQCU".$i][$j]] = true;
				}			

			}

			if ($Answer[$QuestionNumber][$Column])
			{
				if ($UserAnswers[$QuestionNumber][$Column])
					echo '<img src="_images/correct_answer.gif" style="margin-right: 5px">';
				else
					echo '<img src="_images/expected_answer.gif" style="margin-right: 5px">';
			}
			else
			{
				if ($UserAnswers[$QuestionNumber][$Column])
					echo '<img src="_images/wrong_answer.gif" style="margin-right: 5px">';
				else
					echo '<img src="_images/wrong_answer.gif" style="visibility: hidden; margin-right: 5px">';
			}				
		}

		function IsSelected($bInCorrection, $QuestionNumber, $Column)
		{
		
			$Answer[0][0] = false;
			
			$Answer[0][1] = true;
			
			$Answer[1][0] = true;
			
			$Answer[1][1] = false;
			
			$Answer[2][0] = false;
			
			$Answer[2][1] = true;
			
			$Answer[3][0] = false;
			
			$Answer[3][1] = true;
			
			$Answer[4][0] = true;
			
			$Answer[4][1] = false;
			
			$Answer[5][0] = true;
			
			$Answer[5][1] = false;
			
			$Answer[6][0] = false;
			
			$Answer[6][1] = true;
					

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
<title>Animal hybrids</title>
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
		
		AvailableMedia[0] = "B1_W12_D4_glow1.mp3";
		
		AvailableMedia[1] = "B1_W12_D4_glow2.mp3";
		

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
<td class="Question_text ProcessInlineImages" valign="top"><i>Are the animals purebred (from the same species) a hybrid (from two or more species)?</i></td>
</tr>
<tr class="Question_text">
<td class="Question_text" align="center" valign="top">
</td></tr>
</table></b></p>
<p class="nswr"><table id="Question_answers" class="Question_answers" width="100%" cellspacing="0">
<tr class="Question_answers">
<th class="Question_answers"> </th>
<th class="Question_answers ProcessInlineImages" align="center">purebred</th>
<th class="Question_answers ProcessInlineImages" align="center">hybrid</th>
</tr>
<tr class="AnswerRow1">
<td class="Question_answers ProcessInlineImages">cockapoo</td>
<td class="Question_answers" valign="bottom" align="center"><?$this->echoCorrectAnswer($bInCorrection, 0, 0); ?><input type="radio" name="TABQCU0" value="0" <?=$this->IsSelected($bInCorrection, 0, 0) ?> ></td>
<td class="Question_answers" valign="bottom" align="center"><?$this->echoCorrectAnswer($bInCorrection, 0, 1); ?><input type="radio" name="TABQCU0" value="1" <?=$this->IsSelected($bInCorrection, 0, 1) ?> ></td>
</tr>
<tr class="AnswerRow0">
<td class="Question_answers ProcessInlineImages">zebra</td>
<td class="Question_answers" valign="bottom" align="center"><?$this->echoCorrectAnswer($bInCorrection, 1, 0); ?><input type="radio" name="TABQCU1" value="0" <?=$this->IsSelected($bInCorrection, 1, 0) ?> ></td>
<td class="Question_answers" valign="bottom" align="center"><?$this->echoCorrectAnswer($bInCorrection, 1, 1); ?><input type="radio" name="TABQCU1" value="1" <?=$this->IsSelected($bInCorrection, 1, 1) ?> ></td>
</tr>
<tr class="AnswerRow1">
<td class="Question_answers ProcessInlineImages">grolar</td>
<td class="Question_answers" valign="bottom" align="center"><?$this->echoCorrectAnswer($bInCorrection, 2, 0); ?><input type="radio" name="TABQCU2" value="0" <?=$this->IsSelected($bInCorrection, 2, 0) ?> ></td>
<td class="Question_answers" valign="bottom" align="center"><?$this->echoCorrectAnswer($bInCorrection, 2, 1); ?><input type="radio" name="TABQCU2" value="1" <?=$this->IsSelected($bInCorrection, 2, 1) ?> ></td>
</tr>
<tr class="AnswerRow0">
<td class="Question_answers ProcessInlineImages">zorse</td>
<td class="Question_answers" valign="bottom" align="center"><?$this->echoCorrectAnswer($bInCorrection, 3, 0); ?><input type="radio" name="TABQCU3" value="0" <?=$this->IsSelected($bInCorrection, 3, 0) ?> ></td>
<td class="Question_answers" valign="bottom" align="center"><?$this->echoCorrectAnswer($bInCorrection, 3, 1); ?><input type="radio" name="TABQCU3" value="1" <?=$this->IsSelected($bInCorrection, 3, 1) ?> ></td>
</tr>
<tr class="AnswerRow1">
<td class="Question_answers ProcessInlineImages">donkey</td>
<td class="Question_answers" valign="bottom" align="center"><?$this->echoCorrectAnswer($bInCorrection, 4, 0); ?><input type="radio" name="TABQCU4" value="0" <?=$this->IsSelected($bInCorrection, 4, 0) ?> ></td>
<td class="Question_answers" valign="bottom" align="center"><?$this->echoCorrectAnswer($bInCorrection, 4, 1); ?><input type="radio" name="TABQCU4" value="1" <?=$this->IsSelected($bInCorrection, 4, 1) ?> ></td>
</tr>
<tr class="AnswerRow0">
<td class="Question_answers ProcessInlineImages">jaguar</td>
<td class="Question_answers" valign="bottom" align="center"><?$this->echoCorrectAnswer($bInCorrection, 5, 0); ?><input type="radio" name="TABQCU5" value="0" <?=$this->IsSelected($bInCorrection, 5, 0) ?> ></td>
<td class="Question_answers" valign="bottom" align="center"><?$this->echoCorrectAnswer($bInCorrection, 5, 1); ?><input type="radio" name="TABQCU5" value="1" <?=$this->IsSelected($bInCorrection, 5, 1) ?> ></td>
</tr>
<tr class="AnswerRow1">
<td class="Question_answers ProcessInlineImages">cama</td>
<td class="Question_answers" valign="bottom" align="center"><?$this->echoCorrectAnswer($bInCorrection, 6, 0); ?><input type="radio" name="TABQCU6" value="0" <?=$this->IsSelected($bInCorrection, 6, 0) ?> ></td>
<td class="Question_answers" valign="bottom" align="center"><?$this->echoCorrectAnswer($bInCorrection, 6, 1); ?><input type="radio" name="TABQCU6" value="1" <?=$this->IsSelected($bInCorrection, 6, 1) ?> ></td>
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
													 7, 
													 "OneRightOnePoint", 
													 true);
	
	
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
