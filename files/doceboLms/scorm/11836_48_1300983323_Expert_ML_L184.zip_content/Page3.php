<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><?php 
if ($GLOBALS['ClassToInstanciate'] == 'QuestionManager')
{
	class QuestionManager
	{
		var $nbRows;	
		var $weight;

		var $Answer;
		var $ScoringMethod;

		function QuestionManager()
		{
			$this->ScoringMethod = "OneRightOnePoint";

			$this->nbRows = 4;
			$this->weight = 1;

			
			$this->Answer[] = "0";
			
			$this->Answer[] = "1";
			
			$this->Answer[] = "2";
			
			$this->Answer[] = "3";
							
		}

		function GetScore()
		{
			$score = 0;
			$bQuestionIsAnswered = false;
			$maxScore = $this->nbRows;
			
			if ($maxScore == 0)
				return 0;
				
			foreach ($this->Answer as $n => $expectedAnswer)
			{
				if ($_GET["DD".($n+1)] === '')
					continue;
				
				$bQuestionIsAnswered = true;
				
				if ($expectedAnswer == $_GET["DD".($n+1)])
					$score++;
				else
					$score--;
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

		function QuestionDisplayer()
		{
			$this->QuestionDisplayerBase();
			$this->QuestionType = 'dragdrop_question';
		}

		function PrintCorrection($bInCorrection)
		{
			if ($bInCorrection)
			{
				$AnswerText =	"<table width=\"100%\" class=\"Correction\" ><tr><td class=\"ProcessInlineImages Correction\">";

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

				
				$AnswerText .= "<p><br><b>Manager- </b>Hello, I'm the manager. Your waiter said you wanted to talk with me?<br><b>Customer- </b>Yes, that's right. I'm sorry to say that I was just served the worst food I've ever had in any restaurant, and I don't feel I should have to pay for it.<br><b>Manager- </b>What exactly was the problem?<br><b>Customer- </b>Well, first of all, the chicken was undercooked, the fries were soggy, and your salad consisted mainly of wilted, brown, lettuce leaves. And, by the way, vinegar is not normally considered a dressing.<br><b>Manager- </b>You should have told the waiter to take the food back if you weren't satisfied with it.<br><b>Customer- </b><i>(sarcastically) </i>Gee, why didn't I think of that? <i>(angrily) </i>Of course, I had him take it back, but, when it returned, it looked like the chicken had been in a nuclear explosion. See this piece of coal here? That's supposed to be my chicken.<br><b>Manager- </b>Quite frankly, I think you're blowing things a little out of proportion. It may be slightly overcooked  but it doesn't look so bad to me, besides, it appears you've already eaten half of it.<br><b>Customer- </b>And exactly how was I to know how inedible the food was unless I tried to eat it? No, I'm not paying and that's that. If you want to go ahead and call the police, fine. I'll take the remains of this burn victim as evidence. I'm sure once they see it, you're the one who's going to be arrested, not me.<br><b>Manager- </b>Listen, if you don't pay, don't ever expect to eat in this restaurant again.<br><b>Customer- </b>In that case, I'll be happy not to pay.</p>";
				
				
				if ($Score != $ScoreMax)
				{				
					$AnswerText .= "<p><span epiLang=\"CorrectionTextHead\"></span></p>";
					
					$AnswerText .=	"<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\">";

					
					$AnswerText .=	"<tr>";
					$AnswerText .=	"<td class=\"Correction\" ><li>undercooked</td>";

					

					$AnswerText .=	"<td class=\"Correction\" ><li>I asked for medium, not rare.</td>";

					

					$AnswerText .=	"</tr>";
					
					$AnswerText .=	"<tr>";
					$AnswerText .=	"<td class=\"Correction\" ><li>soggy</td>";

					

					$AnswerText .=	"<td class=\"Correction\" ><li>Aren't these shrimp supposed to be crisp?</td>";

					

					$AnswerText .=	"</tr>";
					
					$AnswerText .=	"<tr>";
					$AnswerText .=	"<td class=\"Correction\" ><li>inedible</td>";

					

					$AnswerText .=	"<td class=\"Correction\" ><li>Eat it? I can't even stand to look at it.</td>";

					

					$AnswerText .=	"</tr>";
					
					$AnswerText .=	"<tr>";
					$AnswerText .=	"<td class=\"Correction\" ><li>serve</td>";

					

					$AnswerText .=	"<td class=\"Correction\" ><li>The waitress will bring your food in a few minutes.</td>";

					

					$AnswerText .=	"</tr>";
					

					$AnswerText .=	"</table>";
				}
				
				$AnswerText .=	"</td></tr></table>";

				echo $AnswerText;
			}
		}		

		function getUserAnswer($bInCorrection, $RowId)
		{	
			//returns '' or a number from 0 to N corresponding to the answer to the given row
			$Answers = $GLOBALS['QuizzManager']->GetCurrentPageData('Answers');

			if (!isset($Answers["DD".$RowId]) || $Answers["DD".$RowId] === '')
				return "'-'";
			else
				return $Answers["DD".$RowId];

			return "'-'";
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
<form name="MyQuizzForm" action="_ManagerFrame.php" method="get"><input type="hidden" name="PageNumber" value="2"><input type="hidden" name="Direction" value="0"><input type="hidden" name="ShowAnswer" value="0"><input type="hidden" name="TimeMax" value="0"><input type="hidden" name="MaxTries" value="0"><input type="hidden" name="NavigationDirectAccess" value=""><script type="text/javascript" language="JavaScript">
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
<td class="Question_text ProcessInlineImages" valign="top">Drag the sentence on the right to the word or phrase on the left.</td>
</tr>
<tr class="Question_text">
<td class="Question_text" align="center" valign="top">
</td></tr>
</table></b></p>
<p><script type="text/javascript" src="_scripts/wz_dragdrop.js"></script><script type="text/javascript" language="JavaScript">
		var bDontScrollDraggables;
</script><style type="text/css" media="print, screen, all">
.basket {visibility: hidden}
</style>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td valign="top"><table id="Question_answers" bgcolor="black" class="Question_answers" cellpadding="1" cellspacing="1">
<tr class="AnswerRow1">
<td bgcolor="white" height="50" class="Question_answers">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td valign="top" align="center" class="ProcessInlineImages">undercooked</td>
</tr>
</table><input type="hidden" value="" name="DD1" id="DDId1"></td>
<td bgcolor="white" valign="top" width="100" height="50" class="dragOverOff" id="DropPlaceHolder1"> </td>
</tr>
<tr class="AnswerRow0">
<td bgcolor="white" height="50" class="Question_answers">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td valign="top" align="center" class="ProcessInlineImages">soggy</td>
</tr>
</table><input type="hidden" value="" name="DD2" id="DDId2"></td>
<td bgcolor="white" valign="top" width="100" height="50" class="dragOverOff" id="DropPlaceHolder2"> </td>
</tr>
<tr class="AnswerRow1">
<td bgcolor="white" height="50" class="Question_answers">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td valign="top" align="center" class="ProcessInlineImages">inedible</td>
</tr>
</table><input type="hidden" value="" name="DD3" id="DDId3"></td>
<td bgcolor="white" valign="top" width="100" height="50" class="dragOverOff" id="DropPlaceHolder3"> </td>
</tr>
<tr class="AnswerRow0">
<td bgcolor="white" height="50" class="Question_answers">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td valign="top" align="center" class="ProcessInlineImages">serve</td>
</tr>
</table><input type="hidden" value="" name="DD4" id="DDId4"></td>
<td bgcolor="white" valign="top" width="100" height="50" class="dragOverOff" id="DropPlaceHolder4"> </td>
</tr>
</table></td>
<td valign="top"> </td>
<td valign="top"><table width="150" height="150" border="0" cellspacing="0" cellpadding="3">
<tr>
<td id="Basket" class="basket" valign="top">
<table id="bloc2" style="position:relative" width="150" height="60" class="draggableTable" border="0"><tr>
<td>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td valign="top" align="center" class="ProcessInlineImages">Eat it? I can't even stand to look at it.</td>
</tr>
</table>
</td></tr></table>
<table id="bloc1" style="position:relative" width="150" height="60" class="draggableTable" border="0"><tr>
<td>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td valign="top" align="center" class="ProcessInlineImages">Aren't these shrimp supposed to be crisp?</td>
</tr>
</table>
</td></tr></table>
<table id="bloc3" style="position:relative" width="150" height="60" class="draggableTable" border="0"><tr>
<td>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td valign="top" align="center" class="ProcessInlineImages">The waitress will bring your food in a few minutes.</td>
</tr>
</table>
</td></tr></table>
<table id="bloc0" style="position:relative" width="150" height="60" class="draggableTable" border="0"><tr>
<td>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td valign="top" align="center" class="ProcessInlineImages">I asked for medium, not rare.</td>
</tr>
</table>
</td></tr></table>
</td>
</tr>
</table></td>
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
</script></form><script type="text/javascript" language="JavaScript" src="_scripts/DragDrop.js"></script><script type="text/javascript">

myQuizz = new Question_DragDrop("myQuizz", document.MyQuizzForm, "OneRightOnePoint");


myQuizz.DontAllowSubmitIfEmpty = false;

	myQuizz.RightAnswer[0] = "0";

	myQuizz.RightAnswer[1] = "1";

	myQuizz.RightAnswer[2] = "2";

	myQuizz.RightAnswer[3] = "3";
				

// warning : the dd.elements index is reversed to this one !
SET_DHTML(CURSOR_MOVE, "bloc3", "bloc2", "bloc1", "bloc0");

var DropDestinations = new Array('DropPlaceHolder1', 'DropPlaceHolder2', 'DropPlaceHolder3', 'DropPlaceHolder4');
var DropInputs = new Array('DDId1', 'DDId2', 'DDId3', 'DDId4');

var userAnswers = new Array(<?=$this->getUserAnswer($bInCorrection, 1) ?>, <?=$this->getUserAnswer($bInCorrection, 2) ?>, <?=$this->getUserAnswer($bInCorrection, 3) ?>, <?=$this->getUserAnswer($bInCorrection, 4) ?>);

// Onload : we reset the posted values
for (var i = 0; i < DropDestinations.length; i++)
{
	if (userAnswers[i] != '-')
		MoveObjectToDestination(dd.elements[dd.Int(userAnswers[i])], i);
}

var basketElement = document.getElementById('Basket');
basketElement.style.visibility = "visible";

/* THIS ONE IS CALLED ONCE AN ITEM IS DROPPED
See the description of my_PickFunc for what's accessible from here.
Here may be investigated, for example, what's the name (dd.obj.name)
of the dropped item, and where (dd.obj.x, dd.obj.y) it has been dropped... */
function my_DropFunc()
{
	var debugString = " drop pos : " + dd.obj.x + " x " +dd.obj.y;

	var bDropDestinationFound = false;

	for (var i = 0; i < DropDestinations.length; i++)
	{
		var aDropDestination = document.getElementById(DropDestinations[i]);

		if (IsPointInElement(dd.obj, aDropDestination))
		{
			MoveObjectToDestination(dd.obj, i);
			bDropDestinationFound = true;

 			break;
		}
	}

	if (!bDropDestinationFound)
	{
		var basketElement = document.getElementById('Basket');

		dd.obj.div = basketElement.appendChild(dd.obj.div);
		dd.obj.moveTo(dd.obj.defx, dd.obj.defy);
	}

	for (var i = 0; i < DropDestinations.length; i++)
	{
		var aDropDestination = document.getElementById(DropDestinations[i]);

		NoEmptyDestination(aDropDestination);
	}

	dd.recalc();
}

function IsPointInElement(obj, element)
{
	var testX = dd.e.x;
	var testY = dd.e.y;

	dd.getPageXY(element);

	var testElementLeft = dd.x;
	var testElementRight = dd.x + element.clientWidth;
	var testElementTop = dd.y;
	var testElementBottom = dd.y + element.clientHeight;

	return testX >= testElementLeft && testX <= testElementRight &&
				 testY >= testElementTop && testY <= testElementBottom;
}

function ResetDestination(Destination)
{
	var basketElement = document.getElementById('Basket');

	for (var d_i = 0; d_i < dd.elements.length; d_i++)
	{
		var d_o = dd.elements[d_i];

		if (d_o.div.parentNode.id == Destination.id)
		{
			d_o.div = basketElement.appendChild(d_o.div);
			d_o.moveTo(d_o.defx, d_o.defy);
		}
	}

	Destination.className = "dragOverOff";
	Destination.innerHTML = "";
}

function MoveObjectToDestination(obj, i)
{
	var dest = document.getElementById(DropDestinations[i]);

	ResetDestination(dest);

	obj.div = dest.appendChild(obj.div);
	obj.moveTo(obj.defx, obj.defy);

	// set the form value
	var ObjId = new String(obj.id);
	ObjId = ObjId.substr(4);

	var HiddenFormElement = document.getElementById(DropInputs[i]);
	HiddenFormElement.value = ObjId;
}

function NoEmptyDestination(Destination)
{
	for (var d_i = 0; d_i < dd.elements.length; d_i++)
	{
		var d_o = dd.elements[d_i];

		if (d_o.div.parentNode.id == Destination.id)
		{
			Destination.className = "dragOverSet";
			return;
		}
	}

	Destination.className = "dragOverOff";
	Destination.innerHTML = " ";

	// set the form value
	var ObjId = new String(Destination.id);
	ObjId = dd.Int(ObjId.substr(15));
	var HiddenFormElement = document.getElementById("DDId" + ObjId);
	HiddenFormElement.value = "";	
}

/* my_DragFunc IS CALLED WHILE AN ITEM IS DRAGGED
See the description of my_PickFunc above for what's accessible from here. */
function my_DragFunc()
{
	for (var i = 0; i < DropDestinations.length; i++)
	{
		var aDropDestination = document.getElementById(DropDestinations[i]);

		if (IsPointInElement(dd.obj, aDropDestination))
		{
			aDropDestination.className = "dragOverOn";
		}
		else
		{
			if (aDropDestination.innerHTML == " " ||
					aDropDestination.innerHTML == "&nbsp;")
				aDropDestination.className = "dragOverOff";
			else
				aDropDestination.className = "dragOverSet";
		}
	}
}

function ResetDragDropBasketTopPosition()
{
	var basket = $('Basket');

	var ElementTopOffset = Position.cumulativeOffset(basket)[1];

	var Target = $('Question_answers');
	var dim = Target.getDimensions();
	var TargetBottom = Position.cumulativeOffset(Target)[1] + dim.height;

	if (TargetBottom < document.body.scrollTop)
		basket.style.paddingTop = 3 + TargetBottom - ElementTopOffset;
	else if (document.body.scrollTop > ElementTopOffset)
		basket.style.paddingTop = 3 + document.body.scrollTop - ElementTopOffset;
	else
		basket.style.paddingTop = 3;
}

if (!bDontScrollDraggables)
	window.onscroll = ResetDragDropBasketTopPosition;

</script><script type="text/javascript" language="JavaScript">

	var StartTime = new Date();	
	var myTimer = null;
	var bCountDown = null;
	var nbsTries;
	
						
	myQuizz.Hints = "<br><b>Manager- </b>Hello, I'm the manager. Your waiter said you wanted to talk with me?<br><b>Customer- </b>Yes, that's right. I'm sorry to say that I was just served the worst food I've ever had in any restaurant, and I don't feel I should have to pay for it.<br><b>Manager- </b>What exactly was the problem?<br><b>Customer- </b>Well, first of all, the chicken was undercooked, the fries were soggy, and your salad consisted mainly of wilted, brown, lettuce leaves. And, by the way, vinegar is not normally considered a dressing.<br><b>Manager- </b>You should have told the waiter to take the food back if you weren't satisfied with it.<br><b>Customer- </b><i>(sarcastically) </i>Gee, why didn't I think of that? <i>(angrily) </i>Of course, I had him take it back, but, when it returned, it looked like the chicken had been in a nuclear explosion. See this piece of coal here? That's supposed to be my chicken.<br><b>Manager- </b>Quite frankly, I think you're blowing things a little out of proportion. It may be slightly overcooked  but it doesn't look so bad to me, besides, it appears you've already eaten half of it.<br><b>Customer- </b>And exactly how was I to know how inedible the food was unless I tried to eat it? No, I'm not paying and that's that. If you want to go ahead and call the police, fine. I'll take the remains of this burn victim as evidence. I'm sure once they see it, you're the one who's going to be arrested, not me.<br><b>Manager- </b>Listen, if you don't pay, don't ever expect to eat in this restaurant again.<br><b>Customer- </b>In that case, I'll be happy not to pay.";
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
