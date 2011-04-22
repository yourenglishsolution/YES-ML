<?
/**
 * Class QuizzManagerBase
 *
 * Encoding: ISO-8859-1
 * @package Easyquizz Pro
 * @author Epistema {@link http://www.epistema.com}
 * @copyright Copyright 2001 - 2006, Epistema
 * @filesource
 */

	session_name(ereg_replace("[^[:alnum:]]", "", dirname($_SERVER['PHP_SELF'])));
	session_start();

/*

Session vars :
	$_SESSION['QuizzSession']
				contains the score, random page mapping, etc. for this try
				this var is cleared when one presses restart quizz

	$_SESSION['QuizzSession']['PageMapping']
				Weither this quizz is randomized or not, this contains the page mapping.

	$_SESSION['QuizzSession']['CurrentPage']
				the (randomized) page we're on.

	$_SESSION['QuizzSession']['PageData']
				the (mapped) page array data. $_SESSION['QuizzSession']['PageData'][0] is
				the first page the user will see, whatever the randomization

	$_SESSION['QuizzSession']['PageData'][nnn] components :
				['Answers'] -> the $this->Get array as passed from the page
				['Disabled'] ->
				['HintCycle'] ->
				['MaxTries'] -> Although this doesn't change, it is set at run time
				['Score'] ->
				['TempScore'] -> score displayed in the question when user can try more than once
				['ShowAnswers'] ->
				['Time'] -> the time in secs the user has been on the page
				['TimeMax'] -> Although this doesn't change, it is set at run time
				['Tries'] ->
				['LoadingTime'] -> When the user came on this question (reset each time we come back to the question)
				['originalId'] -> Index of the page as defined in $this->pages
				['Viewed'] -> If the page has been viewed or not

	$_SESSION['QuizzSession']['GlobalSpentTime']
				global time in seconds

	$_SESSION['QuizzSession']['GlobalLoadingTime']
				when the session has been started (timestamp)

	$_SESSION['QuizzSession']['InCorrection']
				true if we are in correction mode

	$_SESSION['QuizzSession']['GenerationDate']
				this is the generation date of the quizz that has created this session.
				If they are different, we must reset the session !

	$_SESSION['QuizzSession']['AICC_Status']
				i -> incomplete
				c -> completed

	$_SESSION['QuizzSession']['TotalScore']
	$_SESSION['QuizzSession']['TotalScoreMax']
	$_SESSION['QuizzSession']['TotalTime']
				calculated total (max) score, false if the score is not yet calculated

	$_SESSION['QuizzSession']['AICC_Status']
				i -> incomplete
				c -> completed

	$_SESSION['QuizzAICCInfo']
				contains data passed IN through $_GET
				this var is *never* cleared, unless a new $_GET is set, in which case we have two
				situations : the user pressed F5 : we ignore, the user closed and relaunched the quizz,
				we reset these values

*/


class QuestionDisplayerBase
{
	var $QuestionType;

	function QuestionDisplayerBase()
	{
	}

	function GetThemeHTML()
	{
		return $GLOBALS['QuizzManager']->GetThemeHTML();
	}

	function EchoResultDataAndSendAICC($OutputAICCandScormInfo = false,
																		 $FinalMaxScore = 0,
																		 $HideScore = false,
																		 $HideComments = false,
																		 $ShowProfileComments = true,
																		 $NoScore = false)
	{
		if (!isset($_POST['scorm_username']) &&
				!isset($_POST['scorm_userid']))
		{
			// create the form which will send username and userid
			echo '<form name="formulaire" action="_ManagerFrame.php'.$GLOBALS['QuizzManager']->implodeParams($_GET).'" method="POST">';
			echo $GLOBALS['QuizzManager']->implodeParamsAsHiddenInputs($_POST);
			echo '<input type="hidden" name="scorm_username" value="">';
			echo '<input type="hidden" name="scorm_userid" value="">';
			echo '</form>';

			echo '<script type="text/javascript" language="JavaScript">' . "\n";

			// get the username and userid from the LMS
			echo 'var username = "";' . "\n";
			echo 'var userid = "";' . "\n";

			echo 'if (window.parent && window.parent.doLMSGetValue)' . "\n";
			echo '{' . "\n";
			echo '	username = window.parent.doLMSGetValue("cmi.core.student_name");' . "\n";
			echo '	userid = window.parent.doLMSGetValue("cmi.core_student_id");' . "\n";
			echo '	if (userid == "")' . "\n";
			echo '		userid = window.parent.doLMSGetValue("cmi.core.student_id");' . "\n";
			echo '	if (userid == "")' . "\n";
			echo '		userid = window.parent.doLMSGetValue("cmi.learner_id");' . "\n";
			echo '}' . "\n";

			// fill the form with the correct values
			echo '	document.formulaire.scorm_username.value = username;' . "\n";
			echo '	document.formulaire.scorm_userid.value = userid;' . "\n";

			// send the form
			echo '	document.formulaire.submit();'. "\n";

			echo '</script>' . "\n";
		}
		else
		{
			$username = stripslashes($_POST['scorm_username']);
			$userid = stripslashes($_POST['scorm_userid']);

			$ScoresByThemes = $GLOBALS['QuizzManager']->getScoreByTheme();
			$ScoreTable = $GLOBALS['QuizzManager']->getScoreTable($ScoresByThemes,
																														$FinalMaxScore,
																														$NoScore);
			if (!$NoScore)
			{
				if (!$HideScore)
					$this->EchoScoreTable($ScoresByThemes, $ScoreTable);

				if (!$HideComments)
					$GLOBALS['QuizzManager']->EchoComments($ScoreTable);

				if ($ShowProfileComments)
					$GLOBALS['QuizzManager']->EchoProfiles($ScoreTable);
			}

			// If no AICC has been passed in, we try to save the answers to userdata.php
			if (!$GLOBALS['QuizzManager']->GetAICC('aicc_url'))
				$GLOBALS['QuizzManager']->WriteAnswersToUserDataFile($username, $userid);

			if ($OutputAICCandScormInfo)
			{
				$GLOBALS['QuizzManager']->SendScorm();
				$GLOBALS['QuizzManager']->SendAICC(true, $ScoresByThemes);
			}

			$GLOBALS['QuizzManager']->SendNotificationEmail($username);
		}

		echo '<form name="navigationform" action="_ManagerFrame.php" method="post">' . "\n";
		echo '<input type="hidden" name="Correction" value="1" />' . "\n";
		echo '<input type="hidden" name="PageNumber" value="-1" />' . "\n";
		echo '<input type="hidden" name="Direction" value="1" />' . "\n";
		echo '</form>' . "\n";
	}

	function EchoScoreTable(&$ScoresByThemes,
 														 &$ScoreTable)
	{
		echo '<table width="100%" border="0" cellspacing="0" cellpadding="3" class="ScoreTable">';
		echo ' <tr> ';
		echo '  <td class="ScoreHead" height="25" valign="top">'.$GLOBALS['PageString'].'</td>';
		echo '  <td class="ScoreHead" height="25" valign="top">'.$GLOBALS['ScoreString'].'</td>';
		echo '  <td class="ScoreHead" height="25" valign="top" colspan="2">'.$GLOBALS['TimeString'].'</td>';
		echo ' </tr>';

		// Foreach theme, show the questions that have score

		$QuestionNumber = 1;
		$GLOBALS['QuizzManager']->DisplayScoreForTheme($GLOBALS['QuizzManager']->RecursiveThemesAndQuestions, $ScoresByThemes, 0, $QuestionNumber);

		echo ' <tr> ';
		echo '  <td class="ScoreTotal">'.$GLOBALS['TotalString'].'</td>';
		echo '  <td class="ScoreTotal"><span dir="ltr">' . round($ScoreTable['Total']['Score'], 2) . '/' . round($ScoreTable['Total']['ScoreMax'], 2) . '</span></td>';

		if (empty($ScoreTable['Total']['TimeMax']) || $GLOBALS['QuizzManager']->NoTimer)
		{
			echo '<td class="ScoreTotal" style="width:120px" ><span dir="ltr">' . QuizzManagerBase::CalculateTimeString($ScoreTable['Total']['Time']) . '</span></td>';
			echo '<td width="120" class="ScoreTotal">&nbsp;</td>';
		}
		else
		{
			echo '<td class="ScoreTotal" style="width:120px"><span dir="ltr">' . QuizzManagerBase::CalculateTimeString($ScoreTable['Total']['Time']) . '</span>/<span dir="ltr">' . QuizzManagerBase::CalculateTimeString($ScoreTable['Total']['TimeMax']) . '</span></td>';
			echo '<td width="120" class="ScoreTotal">' . QuizzManagerBase::GetHorizontalHistogram($ScoreTable['Total']['Time'] / $ScoreTable['Total']['TimeMax'], 100, 8) . '</td>';
		}
		echo ' </tr>';

		echo '</table>';
	}

	function EchoSendReportByEmail()
	{
		echo '<form target="AiccPostFrame" action="admin/sendreport.php" method="POST" name="FormName">' . "\n";
		echo '	<p><span epiLang="GetReportByEmail">Si vous souhaitez recevoir le rapport de vos r&eacute;ponses, veuillez saisir votre adresse email ci-dessous :</span></p>' . "\n";
		echo '	<p align="center"><input type="text" name="email_for_report" size="50" /> <input epiLang="OKButton" type="submit" name="SendReport" value="OK" /></p>' . "\n";
		echo '</form>' . "\n";
	}
}

class QuizzManagerBase
{
	var $Pages; // pages of the quizz
	var $ScoreComments;
	var $Themes;

	var $AllowBack;
	var $isSurvey;

	var $IsGlobalTimer;
	var $GlobalTimeMax;

	var $bDebug;

	var $QuizzNeedPasswd;
	var $QuizzCorrectPasswd;

	var $GenerationDate;

	var $ShowIntroductionPage;
	var $ShowAnswerAfterQuestion;

	var $QuizzOptions;
	var $RecursiveThemesAndQuestions;

	var $NotifyByEmail;
	var $NotifyRecipient;
	var $NotifySubject;
	var $NotifyMessage;

	var $QuizzTitle;

	var $QuestionsToRemove;
	var $ShowCorrection;

	function QuizzManagerBase()
	{
		// Constructor
		$this->bDebug = false;

		$this->QuizzTitle = '';

		$this->Pages = array();
		$this->ScoreComments = array();
		$this->Themes = array();

		$this->NotifyByEmail = false;
		$this->NotifyRecipient = '';
		$this->NotifySubject = '';
		$this->NotifyMessage = '';

		$this->GenerationDate = '';

		$this->ShowIntroductionPage = true;
		$this->ShowAnswerAfterQuestion = 'false';

		$this->QuizzOptions = array();
		$this->RecursiveThemesAndQuestions = array();

		$this->QuestionsToRemove = array();

		$this->ShowCorrection = true;
	}

	function LoadPersistentData()
	{
		$ClearSession = false;

		if (// empty($_SESSION['QuizzAICCInfo']['aicc_refid']) &&
				isset($_SESSION['QuizzSession']['GenerationDate']) &&
				$_SESSION['QuizzSession']['GenerationDate'] != $this->GenerationDate)
		{
			// The quizz has been generated since the session has last been saved
			// This session is no longer valid :
			$ClearSession = true;

			// Only when not launched with a report
		}

		if ($this->LoadAICCInfo())
			$ClearSession = true;

		if (isset($_GET['restart']) && $_GET['restart'] == 'true')
		{
			$_SESSION['DontReadAICC'] = true;

			if ($this->HasEasyquizzProAPI())
			{
				if (!empty($_SESSION['QuizzAICCInfo']['aicc_refid']))
					$_SESSION['QuizzAICCInfo']['aicc_refid'] = 'create|' . $_SESSION['QuizzAICCInfo']['aicc_refid']; // we restart the quizz, we want a new report
			}

			$ClearSession = true;
		}

		if ($ClearSession)
		{
			unset($_SESSION['QuizzSession']);
			header('location: _ManagerFrame.php');
			exit();
		}

		$this->InitQuizzSession(); // this only inits the session if it doesn't exist yet !

		if (isset($_POST['Correction']))
		{
			$_SESSION['QuizzSession']['InCorrection'] = true;
			$this->SetCurrentPageIndex(0);
		}

		return $ClearSession;
	}

	function LoadOneAICCField($Field, $AICCFieldName, &$RawData)
	{
		$bResetSession = false;

		$EasyquizzProApi = $this->HasEasyquizzProAPI();

		if ($EasyquizzProApi &&
				($Field == 'quizz_init') &&
				!empty($RawData['use_direct_interface']) &&
				!empty($RawData['aicc_sid']))
		{
			include_once($EasyquizzProApi);

			// Load AICC data from the platform database
			$currentReport = new $GLOBALS['classes']['Report']['classname']($RawData['aicc_sid']);
			$aicc_data = $currentReport->getValue('aicc_data');

			if (!isset($_SESSION['QuizzAICCInfo'][$Field]))
				$bResetSession = true;
			else if ($_SESSION['QuizzAICCInfo'][$Field] != $aicc_data)
				$bResetSession = true;

			$_SESSION['QuizzAICCInfo'][$Field] = $aicc_data;

			return $bResetSession;
		}

		if (isset($RawData[$AICCFieldName]))
		{
			if (get_magic_quotes_gpc())
				$thisData = stripslashes($RawData[$AICCFieldName]);
			else
				$thisData = $RawData[$AICCFieldName];

			if (!isset($_SESSION['QuizzAICCInfo'][$Field]))
				$bResetSession = true;
			else if ($_SESSION['QuizzAICCInfo'][$Field] != $thisData)
				$bResetSession = true;

			$_SESSION['QuizzAICCInfo'][$Field] = $thisData;
		}

		return $bResetSession;
	}

	function LoadAICCInfo()
	{
		// $this->Get the AICC parameters passed to the quizz

		if (isset($_SESSION['QuizzAICCInfo']['RawData']))
		{
			$RawData = $_SESSION['QuizzAICCInfo']['RawData'];
			unset($_SESSION['QuizzAICCInfo']['RawData']);
		}
		else
			$RawData = array();

		if (isset($_SESSION['QuizzAICCInfo']['quizz_init']) &&
				$_SESSION['QuizzAICCInfo']['quizz_init'] == 'read')
		{
			unset($RawData['quizz_init']);
		}

		$bResetSession = false;

		$RawData = array_change_key_case($RawData, CASE_LOWER);

		$bResetSession = $this->LoadOneAICCField('aicc_url', 			'aicc_url', 		$RawData) |
										 $this->LoadOneAICCField('aicc_sid', 			'aicc_sid', 		$RawData) |
										 $this->LoadOneAICCField('aicc_refid',		'aicc_refid', 	$RawData) | // Not standard compliant
										 $this->LoadOneAICCField('quizz_init', 		'quizz_init', 	$RawData) | // Not standard compliant
										 $this->LoadOneAICCField('review_mode', 	'review_mode', 	$RawData) | // Not standard compliant
										 $this->LoadOneAICCField('ReportId',			'reportid', 		$RawData) | // Not standard compliant
										 $this->LoadOneAICCField('ForceAICC',			'forceaicc', 		$RawData);  // Not standard compliant

		if ($this->GetAICC('review_mode') == 'true')
		{
			$this->AllowBack = true;
			$this->IsGlobalTimer = false;
			$this->GlobalTimeMax = 0;
			$this->NoTimer = true;
		}

		return $bResetSession;
	}

	function IsRandomQuizzForTheme(&$aTheme)
	{
		if ($aTheme['RandomizeLevel'] &&
				!$aTheme['RandomizeButKeepOrganised'] &&
				!empty($aTheme['children']))
			return true;

		foreach ($aTheme['children'] as $aQuestionOrTheme)
		{
			if ($aQuestionOrTheme['type'] == 'folder')
				if ($this->IsRandomQuizzForTheme($aQuestionOrTheme))
					return true;
		}

		return false;
	}

	function IsRandomQuizz()
	{
		// returns true if one theme is random, has children,
		// and does not keep the sub themes organized

		// This is necessary to know if the themes can be shown on the result page

		return $this->IsRandomQuizzForTheme($this->RecursiveThemesAndQuestions);
	}

	/**
	 * Adds all the questions in a theme, recursively, in $PageMapping
	 */
	function GetQuestionsForTheme($aTheme, &$PageMapping)
	{
		foreach ($aTheme['children'] as $aQuestionOrTheme)
		{
			if ($aQuestionOrTheme['type'] == 'question')
				$PageMapping[] = $aQuestionOrTheme['originalId'];

			if (!empty($aQuestionOrTheme['type']['children']))
				$this->GetQuestionsForTheme($aQuestionOrTheme, $PageMapping);
		}
	}

	function InitPageMappingForTheme($aTheme, &$PageMapping)
	{
		if (empty($aTheme['RandomizeLevel']))
		{
			// Not randomized at all

			foreach ($aTheme['children'] as $aQuestionOrTheme)
			{
				if ($aQuestionOrTheme['type'] == 'question')
					$PageMapping[] = $aQuestionOrTheme['originalId'];

				if (!empty($aQuestionOrTheme['type']['children']))
					$this->InitPageMappingForTheme($aQuestionOrTheme, $PageMapping);
			}
		}
		else if (!empty($aTheme['RandomizeButKeepOrganised']) && $aTheme['RandomizeButKeepOrganised'])
		{
			// We pick questions or thems out of the children

			shuffle($aTheme['children']);
			$aTheme['children'] = array_slice($aTheme['children'], 0, $aTheme['RandomizeCount']);

			// We then go on in normal mode
			foreach ($aTheme['children'] as $aQuestionOrTheme)
			{
				if ($aQuestionOrTheme['type'] == 'question')
					$PageMapping[] = $aQuestionOrTheme['originalId'];

				if (!empty($aQuestionOrTheme['type']['children']))
					$this->InitPageMappingForTheme($aQuestionOrTheme, $PageMapping);
			}
		}
		else
		{
			// Get all the questions in this theme and mix

			$questionsAtThisLevel = array();
			$this->GetQuestionsForTheme($aTheme, $questionsAtThisLevel);

			// extract questions that are explanation pages on top and bottom of the them:
			$ExplanationPagesAtTheBeginning = array();
			$ExplanationPagesAtTheEnd = array();

			$my_keys = array_keys($questionsAtThisLevel);

			for ($my_index = 0; $my_index < count($my_keys); $my_index++)
			{
				$aPageId = $questionsAtThisLevel[$my_keys[$my_index]];

				if ($this->QuestionDefinition[$this->Pages[$aPageId]['uid']]["Type"] == "EXPLANATION")
				{
					array_push($ExplanationPagesAtTheBeginning, $aPageId);
					unset($questionsAtThisLevel[$my_keys[$my_index]]);
				}
				else
					break;
			}

			$my_keys = array_keys($questionsAtThisLevel);

			for ($my_index = count($my_keys) - 1; $my_index >= 0; $my_index--)
			{
				$aPageId = $questionsAtThisLevel[$my_keys[$my_index]];

				if ($this->QuestionDefinition[$this->Pages[$aPageId]['uid']]["Type"] == "EXPLANATION")
				{
					array_unshift($ExplanationPagesAtTheEnd, $aPageId);
					unset($questionsAtThisLevel[$my_keys[$my_index]]);
				}
				else
					break;
			}

			shuffle($questionsAtThisLevel);

			$questionsAtThisLevel = array_slice($questionsAtThisLevel, 0, max(0, $aTheme['RandomizeCount'] - count($ExplanationPagesAtTheBeginning) - count($ExplanationPagesAtTheEnd)));

			foreach ($ExplanationPagesAtTheBeginning as $aPageId)
				array_unshift($questionsAtThisLevel, $aPageId);

			foreach ($ExplanationPagesAtTheEnd as $aPageId)
				array_push($questionsAtThisLevel, $aPageId);

			$questionsAtThisLevel = array_slice($questionsAtThisLevel, 0, $aTheme['RandomizeCount']);

			foreach ($questionsAtThisLevel as $aPageId)
				$PageMapping[] = $aPageId;
		}
	}

	function InitPageMapping()
	{
		if (isset($_SESSION['QuizzSession']['PageMapping']))
			return;

		srand((float)microtime()*1000000);
		$_SESSION['QuizzSession']['AICC_Status'] = 'i';

		$PageMapping = array();

		// Remove the credential page from RecursiveThemesAndQuestions:
		foreach ($this->RecursiveThemesAndQuestions['children'] as $k => $aQuestionOrTheme)
		{
			if ($aQuestionOrTheme['type'] == 'question' && $aQuestionOrTheme['credentials'])
			{
				unset($this->RecursiveThemesAndQuestions['children'][$k]);
				break;
			}
		}

		if (!empty($this->IsAdaptivePath))
			return;

		// Remove the credential page (if any) from the lot:
		$CredentialQuestion = false;
		foreach ($this->Pages as $k => $aPage)
		{
			if ($aPage["credentials"])
			{
				$CredentialQuestion = $k;
				break;
			}
		}

		$this->InitPageMappingForTheme($this->RecursiveThemesAndQuestions, $PageMapping);

		// We reinsert the credential question at the beginning
		if ($CredentialQuestion !== false)
			array_unshift($PageMapping, $CredentialQuestion);

		if ($this->bDebug)
		{
			echo 'Page mapping :<br>';
			foreach ($PageMapping as $k => $v)
				echo 'Page ' . $k . ' -&gt; ' .  $v . '<br>';
		}

		$_SESSION['QuizzSession']['PageMapping'] = $PageMapping;
	}

	/**
	 * Add questions to $_SESSION['QuizzSession']['PageMapping']
	 * If no questions are there, also adds the credential question (if any)
	 */
	function AddQuestionsToPageMappingInAdaptivePath()
	{
		if (empty($_SESSION['QuizzSession']['PageMapping']))
		{
			$_SESSION['QuizzSession']['PageMapping'] = array();

			// Initialization: we add the credential question if necessary:
			$CredentialQuestion = false;

			foreach ($this->Pages as $k => $aPage)
			{
				if ($aPage["credentials"])
				{
					$CredentialQuestion = $k;
					break;
				}
			}

			if ($CredentialQuestion !== false)
				array_unshift($_SESSION['QuizzSession']['PageMapping'], $CredentialQuestion);

			// we add a few questions from the first theme and exit
			$this->AddQuestionsToPageMapping($this->AdaptivePathFirstThemeGuid);

			return;
		}

		// Now add some questions if necessary
		$LastQuestion = end($_SESSION['QuizzSession']['PageData']);

		if (!empty($LastQuestion) && !empty($LastQuestion['Time']))
		{
			// If the user has been 3 times through the same theme, we stop the damage:
			if (count($_SESSION['QuizzSession']['PageData']) >= $this->AdaptivePathQuestionsPerStage * 3)
			{
				$QuestionCountPerTheme = array();

				for ($i = 0; $i < count($_SESSION['QuizzSession']['PageData']); $i++)
				{
					if (!isset($_SESSION['QuizzSession']['PageData'][$i]))
						continue;

					// Get the theme of the question
					$AncestorGuids = $this->GetParentGuids($i);

					$targetGuid = end($AncestorGuids);

					if (empty($QuestionCountPerTheme[$targetGuid]))
						$QuestionCountPerTheme[$targetGuid] = 0;

					$QuestionCountPerTheme[$targetGuid]++;

					if ($QuestionCountPerTheme[$targetGuid] >= $this->AdaptivePathQuestionsPerStage * 3)
						return; // we stop the damage here
				}
			}

			// If the last question is done, we calculate the score out
			// of the N last questions and we branch

			$ScoreForLastFewQuestions = 0;
			$MaxScoreForLastFewQuestions = 0;

			for ($i = count($_SESSION['QuizzSession']['PageData']) - $this->AdaptivePathQuestionsPerStage;
					 $i < count($_SESSION['QuizzSession']['PageData']); $i++)
			{
				if (!isset($_SESSION['QuizzSession']['PageData'][$i]))
					continue;

				$ScoreForLastFewQuestions += $this->GetPageData($i, 'Score');
				$MaxScoreForLastFewQuestions += $this->GetPageData($i, 'MaxScore');
			}

			// Get the theme of the last question, in order to retrieve the rules:
			$LastPageIndex = count($_SESSION['QuizzSession']['PageData']) - 1;
			$AncestorGuids = $this->GetParentGuids($LastPageIndex);

			$rules = array();

			$targetGuid = end($AncestorGuids);

			if ($MaxScoreForLastFewQuestions != 0)
				$ScorePerCent = $ScoreForLastFewQuestions * 100 / $MaxScoreForLastFewQuestions;
			else // The last N questions were not really questions, we go on
			{
				$this->AddQuestionsToPageMapping($targetGuid);
				return;
			}

			foreach ($this->Themes as $guid => $aFolder)
			{
				if (empty($aFolder['AdaptiveQuestionnaireRule']))
					continue;

				if (in_array($guid, $AncestorGuids))
				{
					$rules = $aFolder['AdaptiveQuestionnaireRule'];
					break;
				}
			}

			foreach ($rules as $aRule)
			{
				if ($aRule['from'] == 0)
					$aRule['from'] = -1;

				if ($aRule['from'] < $ScorePerCent && $ScorePerCent <= $aRule['to'])
				{
					$targetGuid = $aRule['target'];
					break;
				}
			}

			$this->AddQuestionsToPageMapping($targetGuid);
		}
	}

	function IsQuestionNotAlreadyInPageMapping($anOriginalId)
	{
		return !in_array($anOriginalId, $_SESSION['QuizzSession']['PageMapping']);
	}

	function AddQuestionsToPageMapping($targetGuid)
	{
		// First check that we are not at the end of the questionnaire :
		$MaxNumberOfQuestions = $this->AdaptivePathQuestionsPerStage * $this->AdaptivePathStagesPerQuestionnaire;

		if (count($_SESSION['QuizzSession']['PageData']) >= $MaxNumberOfQuestions)
			return;

		$TargetTheme = $this->FindThemeWithGuid($targetGuid, $this->RecursiveThemesAndQuestions);

		$subfolders = 0;

		if (!empty($TargetTheme['children']))
		{
			foreach ($TargetTheme['children'] as $aQuestionOrTheme)
			{
				if ($aQuestionOrTheme['type'] == 'folder')
					$subfolders++;
			}
		}

		if ($this->AdaptiveQuestionnaireBalancedPicking && $subfolders > 0)
		{
			$QuestionsToAddPerFolder = ceil($this->AdaptivePathQuestionsPerStage / $subfolders);
			$questionsAdded = 0;

			foreach ($TargetTheme['children'] as $aQuestionOrTheme)
			{
				if ($aQuestionOrTheme['type'] == 'folder')
				{
					$NewPageMapping = array();

					$this->GetQuestionsForTheme($aQuestionOrTheme, $NewPageMapping);

					$NewPageMapping = array_filter($NewPageMapping, array($this, 'IsQuestionNotAlreadyInPageMapping'));
					shuffle($NewPageMapping);
					$NewPageMapping = array_slice($NewPageMapping, 0, min($QuestionsToAddPerFolder, $this->AdaptivePathQuestionsPerStage - $questionsAdded));

					$questionsAdded += count($NewPageMapping);

					$_SESSION['QuizzSession']['PageMapping'] = array_merge($_SESSION['QuizzSession']['PageMapping'], $NewPageMapping);
				}
			}
		}
		else
		{
			$NewPageMapping = array();

			$this->GetQuestionsForTheme($TargetTheme, $NewPageMapping);

			$NewPageMapping = array_filter($NewPageMapping, array($this, 'IsQuestionNotAlreadyInPageMapping'));
			shuffle($NewPageMapping);
			$NewPageMapping = array_slice($NewPageMapping, 0, $this->AdaptivePathQuestionsPerStage);
			$_SESSION['QuizzSession']['PageMapping'] = array_merge($_SESSION['QuizzSession']['PageMapping'], $NewPageMapping);
		}

		// Initialize the PageData for the newly added questions
		for ($i = 0; $i < count($_SESSION['QuizzSession']['PageMapping']); $i++)
		{
			if (!empty($_SESSION['QuizzSession']['PageData'][$i]))
				continue;

			$_SESSION['QuizzSession']['PageData'][] =
						$this->getInitialPageData($_SESSION['QuizzSession']['PageMapping'][$i],
									$this->GetPageData($i, 'uid'),
									$this->ShowAnswerAfterQuestion == 'true' ? 2 : 0);
		}
	}

	function getInitialPageData($originalId, $uid, $HintCycle)
	{
		return array(
							'originalId' => $originalId,
							'uid' => $uid,
							'Answers' => array(),
							'Score' => 0,
							'TempScore' => -1,
							'Time' => 0,
							'Tries' => 0,
							'HintCycle' => $HintCycle,
							'Disabled' => false,
							'ShowAnswers' => false,
							'LoadingTime' => false,
							'TimeMax' => 0,
							'MaxTries' => 0,
							'Viewed' => false
						);
	}

	function GetPageFilename($PageId) // -1 is introduction, 0 to N-1 are questions, N is results
	{
		if ($PageId == -1)
			return "introduction.php";

		if ($PageId == count($_SESSION['QuizzSession']['PageMapping']))
			return "results.php";

		return $this->Pages[$_SESSION['QuizzSession']['PageMapping'][$PageId]]["filename"];
	}

	function IsCurrentPageAQuestion()
	{
		// note : currently, this function only checks weither we are on the introduction
		// page or on the result page. Later we could also check for "non-questions", like
		// explanation pages

		// warning : questions with score = 0 can be test dummy questions, but they could also
		// be questions that the teacher wants to score by himself... Don't assume...!

		return $this->GetCurrentPageIndex() >= 0 &&
					 $this->GetCurrentPageIndex() < $this->GetPageCount();
	}

	function InCorrection()
	{
		return $_SESSION['QuizzSession']['InCorrection'];
	}

	function TranslatePageNumber($RealPageNumber)
	{
		$FlippedMapping = array_flip($_SESSION['QuizzSession']['PageMapping']);

		if ($RealPageNumber == -1)
			return -1;

		if ($RealPageNumber >= count($this->Pages))
			return count($FlippedMapping);

		if (isset($FlippedMapping[$RealPageNumber]))
			return $FlippedMapping[$RealPageNumber];
		else
			return false;
	}

	function GetPageCount()
	{
		$this->InitQuizzSession();

		return count($_SESSION['QuizzSession']['PageMapping']);
	}

	function GetCurrentPageIndex()
	{
		$this->InitQuizzSession();

		return $_SESSION['QuizzSession']['CurrentPage'];
	}

	function SetCurrentPageIndex($NewPageIndex)
	{
		$this->InitQuizzSession();

		if ($NewPageIndex < -1)
			$NewPageIndex = -1;
		else if ($NewPageIndex === 'END' || $NewPageIndex > $this->GetPageCount())
			$NewPageIndex = $this->GetPageCount();

		if (!$this->ShowIntroductionPage && $NewPageIndex == -1)
			$NewPageIndex = 0;

		$_SESSION['QuizzSession']['CurrentPage'] = $NewPageIndex;

		if ($NewPageIndex == $this->GetPageCount())
			$_SESSION['QuizzSession']['InCorrection'] = true;

		return $NewPageIndex;
	}

	function implodeParams($params)
	{
		unset($params['submit']);

		if (empty($params))
			return '';

		$ret = array();

		foreach ($params as $k => $v)
		{
			if (is_array($v))
			{
				foreach ($v as $kk => $vv)
					$ret[] = $k . '['.$kk.']=' . rawurlencode(stripslashes($vv));
			}
			else
				$ret[] = $k . '=' . rawurlencode(stripslashes($v));
		}

		return '?' . implode('&', $ret);
	}

	function implodeParamsAsHiddenInputs($params)
	{
		unset($params['submit']);

		if (empty($params))
			return '';

		$ret = '';

		foreach ($params as $k => $v)
		{
			if (is_array($v))
			{
				foreach ($v as $kk => $vv)
					$ret .= '<input type="hidden" name="'.$k .'['.$kk.']" value="'.htmlspecialchars(stripslashes($vv)).'">';
			}
			else
				$ret .= '<input type="hidden" name="'.$k .'" value="'.htmlspecialchars(stripslashes($v)).'">';
		}

		return $ret;
	}

	function InitQuizzSession()
	{
		if (isset($_SESSION['QuizzSession']))
			return;

		if ($this->bDebug)
			echo '<br>InitQuizzSession';

		$_SESSION['QuizzSession'] = array();
		$_SESSION['QuizzSession']['PageData'] = array();

		$_SESSION['QuizzSession']['GenerationDate'] = $this->GenerationDate;

		$_SESSION['QuizzSession']['GlobalLoadingTime'] = false; // we will init this on the first question
																														// that is not on the introduction

		$_SESSION['QuizzSession']['GlobalSpentTime'] = 0;
		$_SESSION['QuizzSession']['TotalScore'] = false;
		$_SESSION['QuizzSession']['TotalScoreMax'] = false;

		$this->InitPageMapping();

		if (!empty($this->IsAdaptivePath))
			$this->AddQuestionsToPageMappingInAdaptivePath();

		for ($i = 0; $i < $this->GetPageCount(); $i++)
		{
			if (!empty($_SESSION['QuizzSession']['PageData'][$i]))
				continue;

			$_SESSION['QuizzSession']['PageData'][] =
										$this->getInitialPageData($_SESSION['QuizzSession']['PageMapping'][$i],
													$this->GetPageData($i, 'uid'),
													$this->ShowAnswerAfterQuestion == 'true' ? 2 : 0);

		}

		// If the introduction page should not be shown, we init the current page at 0 !
		$this->SetCurrentPageIndex(-1);

		$_SESSION['QuizzSession']['InCorrection'] = false;

		if (isset($_SESSION['DontReadAICC']) && $_SESSION['DontReadAICC'] == true)
		{
			unset($_SESSION['DontReadAICC']);
		}
		else
		{
			if ($this->GetAICC('quizz_init') !== false &&
					$this->GetAICC('quizz_init') != 'read')
			{
				// Data has been passed from Epilearn, we unserialise it :
				if ($this->bDebug)
					echo '<br>Data has been passed from Epilearn, we unserialise it.';

				$session_data = @unserialize($this->GetAICC('quizz_init'));

				if (is_array($session_data))
					$_SESSION['QuizzSession'] = $session_data;

				$_SESSION['QuizzSession']['GlobalLoadingTime'] = time() - $_SESSION['QuizzSession']['GlobalSpentTime'];

				$_SESSION['QuizzAICCInfo']['quizz_init'] = 'read'; // do not read it anymore !

				if ($this->bDebug)
				{
					if (is_array($session_data))
					{
						echo '<pre>Success : ';
						print_r($_SESSION['QuizzSession']);
						echo '</pre>';
					}
					else
					{
						echo 'Failed<br>';
					}
				}
			}
		}
	}

	function GetPageResults($PageId)
	{
		if (isset($_SESSION['QuizzSession']['PageData'][$PageId]['Answers']))
			return $_SESSION['QuizzSession']['PageData'][$PageId]['Answers'];
		else
			return false;
	}

	function HasAnswered($PageId)
	{
		$CurrentAnswers = $this->GetPageResults($PageId);
		if (empty($CurrentAnswers))
			return false;

		if (isset($CurrentAnswers['Flash_Data']))
			return !empty($CurrentAnswers['Flash_Data']);

		if (is_array($CurrentAnswers))
		{
			foreach ($CurrentAnswers as $k => $aRow)
			{
				if (strpos($k, 'QCU') !== false || strpos($k, 'QCM') !== false)
					return true;

				if (is_array($aRow))
				{
					foreach ($aRow as $aCol)
					{
						if (!empty($aCol))
							return true;
					}
				}
				else if (!empty($aRow))
					return true;
			}
		}
		else if (!empty($CurrentAnswers))
			return true;

		return false;
	}

	function SetPageData($RandomPageId, $Field, $Data)
	{
		if (!isset($_SESSION['QuizzSession']['PageData'][$RandomPageId]))
			return;

		switch ($Field)
		{
			case 'Answers' :
				// unset a few $_GET parameters we don't need to store
				unset($Data['ShowAnswer']);
				unset($Data['Direction']);
				unset($Data['PageNumber']);
				unset($Data['MaxTries']);
				unset($Data['TimeMax']);
				unset($Data['NavigationSelect']);
				unset($Data['NavigationDirectAccess']);

				$_SESSION['QuizzSession']['PageData'][$RandomPageId]['Answers'] = $Data;
				break;

			default :
				$_SESSION['QuizzSession']['PageData'][$RandomPageId][$Field] = $Data;
		}
	}

	function SetCurrentPageData($Field, $Data)
	{
		$this->SetPageData($this->GetCurrentPageIndex(), $Field, $Data);
	}

	function GetPageData($RandomPageId, $Field)
	{
		switch ($Field)
		{
			case 'filename' : return $this->GetPageFilename($RandomPageId);

			case 'MaxScore' :
			case 'credentials' :
			case 'Theme'    :
			case 'ThemeGuid':
			case 'uid':
				if (!isset($_SESSION['QuizzSession']['PageMapping'][$RandomPageId]))
					return false;

				return $this->Pages[$_SESSION['QuizzSession']['PageMapping'][$RandomPageId]][$Field];

			default : // dynamic data
				if (isset($_SESSION['QuizzSession']['PageData'][$RandomPageId][$Field]))
					return $_SESSION['QuizzSession']['PageData'][$RandomPageId][$Field];
				else
					return false;
		}
	}

	function GetCurrentPageData($Field)
	{
		return $this->GetPageData($this->GetCurrentPageIndex(), $Field);
	}

	function GetSpentTime()
	{
		if ($this->IsGlobalTimer)
		{
			if (isset($_SESSION['QuizzSession']['GlobalSpentTime']))
				return $_SESSION['QuizzSession']['GlobalSpentTime'];
			else
				return 0;
		}

		if (!$this->IsCurrentPageAQuestion())
			return 0;
		else
    	return $this->GetCurrentPageData('Time');
	}

	function UninitPage($_RawGetValues)
	{
		if (!$this->IsCurrentPageAQuestion())
			return;

		if ($this->bDebug)
			echo 'Uninit page ' . $this->GetCurrentPageIndex() . '/' . $this->GetPageCount() . ':<br>';

		// if direction is 0, then the user has pressed Validate.
		// If it is 1 or -1, he has pressed the navigation buttons,
		if (isset($_RawGetValues["Direction"]))
			$Direction = $_RawGetValues["Direction"];
		else
			$Direction = 0;

		// initialize the static values (never change)
		if (isset($_RawGetValues["TimeMax"]))
			$this->SetCurrentPageData("TimeMax",  $_RawGetValues["TimeMax"]);

		// initialize the static values (never change)
		if (isset($_RawGetValues["MaxTries"]))
			$this->SetCurrentPageData("MaxTries", $_RawGetValues["MaxTries"]);

		if ($this->InCorrection())
			return;

		// Update the global timer
		if ($_SESSION['QuizzSession']['GlobalLoadingTime'] !== false)
		{
			$_SESSION['QuizzSession']['GlobalSpentTime'] = time() - $_SESSION['QuizzSession']['GlobalLoadingTime'];

			if ($this->IsGlobalTimer)
			{
				if ($_SESSION['QuizzSession']['GlobalSpentTime'] > $this->GlobalTimeMax)
				{
					$this->SetCurrentPageData("Time", $this->GetCurrentPageData("Time") + $this->GlobalTimeMax - $_SESSION['QuizzSession']['GlobalSpentTime']);
					$_SESSION['QuizzSession']['GlobalSpentTime'] = $this->GlobalTimeMax;
				}
			}
		}

		$PageWasDisabled = $this->GetCurrentPageData("Disabled");

		// Update the number of tries
		if ($Direction == 0)
		{
			$this->SetCurrentPageData("Tries", $this->GetCurrentPageData("Tries")+1);

			if ($this->GetCurrentPageData("MaxTries") > 0 &&
					$this->GetCurrentPageData("Score") == $this->GetCurrentPageData("MaxScore") &&
					$this->GetCurrentPageData("MaxTries") >= $this->GetCurrentPageData("Tries"))
			{
				$this->SetCurrentPageData("Tries", $this->GetCurrentPageData("MaxTries") + 1);
			}

			$TriesLeft = $this->GetCurrentPageData("MaxTries") - $this->GetCurrentPageData("Tries");

			if (($TriesLeft <= 0) && $this->GetCurrentPageData("HintCycle") > 0)
			{
				$this->SetCurrentPageData("HintCycle", $this->GetCurrentPageData("HintCycle") - 1);

				if ($this->GetCurrentPageData("HintCycle") == 1)
				{
					$this->SetCurrentPageData("ShowAnswers", true);
					$this->SetCurrentPageData("Disabled", true);
				}
			}
		}

		if ($PageWasDisabled)
		{
			// Stop here, the page is disabled
			if ($this->bDebug)
				echo 'No score calculation, page disabled.<br>';

			return;
		}

		// Update the question timer
		$ElapsedTime = time () - $this->GetCurrentPageData('LoadingTime');

		$this->SetCurrentPageData("Time", $this->GetCurrentPageData("Time") + $ElapsedTime);

		if ($this->GetCurrentPageData("TimeMax") > 0)
		{
			if ($this->GetCurrentPageData("Time") > $this->GetCurrentPageData("TimeMax"))
			{
				$ElapsedTime = $this->GetCurrentPageData("TimeMax") - $this->GetCurrentPageData("Time");
				$this->SetCurrentPageData("Time", $this->GetCurrentPageData("TimeMax"));
			}
		}

		if ($this->bDebug)
		{
			echo '<pre>Score calculated for page : <br>';
			print_r($_SESSION['QuizzSession']['PageData'][$this->GetCurrentPageIndex()]);
			echo '</pre>';
		}

		$GLOBALS['ClassToInstanciate'] = 'QuestionManager';
		include($this->GetCurrentPageData("filename")); // instanciate $question

		$UserClassFile = str_replace('inc.manager.php', 'UserQuestionManager.php', str_replace('\\', '/', __FILE__));
		if (file_exists($UserClassFile))
		{
			include_once($UserClassFile);
			$question = new UserQuestionManager();
		}
		else
			$question = new QuestionManager();

		$thisScore = $question->GetScore();

		if ($this->bDebug)
			echo 'Score : ' . $thisScore . '<br>';

		// Save the user answers
		$this->SetCurrentPageData("Answers", $_GET);

		if (!empty($question->ThemeGuids))
			$this->ShuntToTheme($this->GetCurrentPageIndex(),
													$question->GetNextThemeGuid(),
													$question->ThemeGuids);

		$this->SetCurrentPageData("Score", $thisScore);

		if ($Direction == 0)
		{
			$this->SetCurrentPageData("TempScore", $thisScore);

			if ($this->GetCurrentPageData("MaxTries") > 0 &&
					$this->GetCurrentPageData("Score") == $this->GetCurrentPageData("MaxScore"))
			{
				$this->SetCurrentPageData("Tries", $this->GetCurrentPageData("MaxTries") + 1);
			}
		}

		if (!empty($this->IsAdaptivePath))
			$this->AddQuestionsToPageMappingInAdaptivePath();

		$this->SendAICC(false);
	}

	function ShuntToTheme($shuntingPageId, $guid, $AllThemes)
	{
		// $guid is the guid of the theme to branch to
		// $allThemes is the list of themes this shunting question can branch to.

		// This function removes all questions from PageData and PageMapping that are
		// not in the selected theme, and adds the questions from the selected theme if they
		// are not in it yet.

		// First remove the selected theme from AllThemes:
		foreach ($AllThemes as $k => $aGuid)
			if ($guid == $aGuid)
			{
				unset($AllThemes[$k]);
				break;
			}

		// Now remove from the mapping and page data all the questions that where in AllThemes
		// This is recursive
		$this->QuestionsToRemove = array();
		$this->GetListOfQuestionsToRemove($this->QuestionsToRemove, $this->RecursiveThemesAndQuestions, $AllThemes, false);

		$_SESSION['QuizzSession']['PageData'] = array_values(array_filter($_SESSION['QuizzSession']['PageData'], array($this, 'RemoveQuestions')));
		$_SESSION['QuizzSession']['PageMapping'] = array_values(array_filter($_SESSION['QuizzSession']['PageMapping'], array($this, 'RemoveQuestions2')));

		if ($this->bDebug)
		{
			echo 'Removed questions :<br>';
			echo '<pre>what : <br>';
			print_r($this->QuestionsToRemove);
			echo '</pre><br>';
		}

		// Now add the questions and folders for $guid, recursively
		$SelectedTheme = $this->FindThemeWithGuid($guid, $this->RecursiveThemesAndQuestions);
		if ($SelectedTheme !== false)
		{
			$NewPageMapping = array();
			$this->InitPageMappingForTheme($SelectedTheme, $NewPageMapping);

			if ($this->bDebug)
			{
				echo 'Added questions :<br>';
				echo '<pre>what : <br>';
				print_r($NewPageMapping);
				echo '</pre><br>';
			}


			$NewMapping = array();
			foreach ($_SESSION['QuizzSession']['PageMapping'] as $k => $oldPageId)
			{
				$NewMapping[] = $oldPageId;

				if ($k == $shuntingPageId)
				{
					foreach ($NewPageMapping as $pageId)
					{
						if (!in_array($pageId, $_SESSION['QuizzSession']['PageMapping']))
							$NewMapping[] = $pageId;
					}
				}
			}

			$_SESSION['QuizzSession']['PageMapping'] = $NewMapping;

			// Update PageData
			$NewPageData = array();

			for ($i = 0; $i < $this->GetPageCount(); $i++)
			{
				$bFound = false;

				foreach ($_SESSION['QuizzSession']['PageData'] as $existingPageData)
					if ($existingPageData['originalId'] == $_SESSION['QuizzSession']['PageMapping'][$i])
					{
						$NewPageData[] = $existingPageData;
						$bFound = true;
						break;
					}

				if (!$bFound)
				{
					$NewPageData[] =
										$this->getInitialPageData($_SESSION['QuizzSession']['PageMapping'][$i],
													$this->GetPageData($i, 'uid'),
													$this->ShowAnswerAfterQuestion == 'true' ? 2 : 0);
				}
			}

			$_SESSION['QuizzSession']['PageData'] = $NewPageData;
		}
	}

	function FindThemeWithGuid($strGuid, &$RootTheme)
	{
		if (!empty($RootTheme['guid']) && $strGuid === $RootTheme['guid'])
			return $RootTheme;

		if (empty($RootTheme['children']))
			return false;

		foreach ($RootTheme['children'] as $subTheme)
		{
			$ret = $this->FindThemeWithGuid($strGuid, $subTheme);
			if ($ret !== false)
				return $ret;
		}

		return false;
	}

	function GetListOfQuestionsToRemove(&$QuestionsToRemove, $folder, $guidsToRemove, $bRemove)
	{
		$bFoundGuid = ($folder['type'] == 'folder' &&
									 in_array($folder['guid'], $guidsToRemove));

		if (!empty($folder['children']))
			foreach ($folder['children'] as $QuestionOrFolder)
				$this->GetListOfQuestionsToRemove($QuestionsToRemove, $QuestionOrFolder, $guidsToRemove, $bRemove || $bFoundGuid);

		if ($folder['type'] == 'question' && $bRemove)
			$QuestionsToRemove[] = $folder['originalId'];
	}

	function RemoveQuestions($question)
	{
		// callback of array_filter for PageData
		return !in_array($question['originalId'], $this->QuestionsToRemove);
	}

	function RemoveQuestions2($question)
	{
		// callback of array_filter for PageMapping
		return !in_array($question, $this->QuestionsToRemove);
	}

	function MoveToNextPage($_RawGetValues)
	{
		$PreviousPageNumber = $this->GetCurrentPageIndex();

		// if direction is 0, then the user has pressed Validate.
		// If it is 1 or -1, he has pressed the navigation buttons,
		if (isset($_RawGetValues["Direction"]))
			$Direction = $_RawGetValues["Direction"];
		else
			$Direction = 0;

		// the user has pressed "Show answers"
		if (isset($_RawGetValues["ShowAnswer"]))
			$ShowAnswers = $_RawGetValues["ShowAnswer"];
		else
			$ShowAnswers = 0;

		if ($this->InCorrection())
		{
			switch ($Direction)
			{
				case -1 :
					return $this->SetCurrentPageIndex($PreviousPageNumber - 1);

				case 0 :
				case 1 :
					return $this->SetCurrentPageIndex($PreviousPageNumber + 1);

				case 2 :
					return $this->SetCurrentPageIndex($_RawGetValues["NavigationSelect"]);

				case 3 : // end the questionnaire
					return $this->SetCurrentPageIndex($this->GetPageCount());

				case 4 :
					return $this->SetCurrentPageIndex($_RawGetValues["NavigationDirectAccess"]);
			}
		}

		if ($PreviousPageNumber == -1 && $this->QuizzNeedPasswd)
		{
			if (stripslashes($_REQUEST['QuizzPasswd']) != $this->QuizzCorrectPasswd)
				return $this->SetCurrentPageIndex(-1);
		}

		$NoMoreTime = false;
		$tries = 0;

		if ($this->IsCurrentPageAQuestion())
		{
			if ($ShowAnswers == 1)
			{
				$this->SetCurrentPageData("ShowAnswers", true);
				$this->SetCurrentPageData("Disabled", true);
			}

			if (!$this->NoTimer && !$this->IsGlobalTimer && $this->GetCurrentPageData("TimeMax") > 0)
				$NoMoreTime = ($this->GetCurrentPageData("TimeMax") <= $this->GetCurrentPageData("Time"));
		}

		if (!$this->NoTimer && $this->IsGlobalTimer)
			$NoMoreTime = ($this->GlobalTimeMax <= $_SESSION['QuizzSession']['GlobalSpentTime']);

		switch ($Direction)
		{
			case -1 :
				$this->SetCurrentPageIndex($PreviousPageNumber - 1);
				break;

			case 0 :
				$waitOnce = false;

				if ($this->IsCurrentPageAQuestion())
				{
					if ($this->GetCurrentPageData("MaxTries") > 0 &&
					    $this->GetCurrentPageData("Score") == $this->GetCurrentPageData("MaxScore") &&
					    $this->GetCurrentPageData("MaxTries") >= $this->GetCurrentPageData("Tries"))
					{
						$waitOnce = true;
					}

					$TriesLeft = $this->GetCurrentPageData("MaxTries") - $this->GetCurrentPageData("Tries");

					if ($TriesLeft <= 0 && $this->GetCurrentPageData("HintCycle") == 1)
						$waitOnce = true;
				}

				if ($ShowAnswers > 0)
					$waitOnce = true;

				if (($this->GetCurrentPageData("MaxTries") == 0 || $TriesLeft < 0 || $NoMoreTime) &&
						!$waitOnce)
					$this->SetCurrentPageIndex($PreviousPageNumber + 1);
				else
					$this->SetCurrentPageIndex($PreviousPageNumber);

				break;

			case 1 :
				$this->SetCurrentPageIndex($PreviousPageNumber + 1);
				break;

			case 2 :
				$this->SetCurrentPageIndex($_RawGetValues["NavigationSelect"]);
				break;

			case 3 :
				$this->SetCurrentPageIndex($this->GetPageCount());
				break;

			case 4 :
				$this->SetCurrentPageIndex($_RawGetValues["NavigationDirectAccess"]);
				break;
		}

		if (!$this->InCorrection() && $NoMoreTime && $this->IsGlobalTimer)
			$this->SetCurrentPageIndex($this->GetPageCount());

		return $this->GetCurrentPageIndex();
	}

	function CalculateTimeString($timeInSec)
	{
		if ($timeInSec < 60)
			return $timeInSec . "&nbsp;" . $GLOBALS['secsString'];

		$Minutes = floor($timeInSec / 60);
		$timeInSec = $timeInSec % 60;

		if ($Minutes < 60)
		{
			if ($timeInSec != 0)
				return $Minutes . "&nbsp;" . $GLOBALS['minsString'] . "&nbsp;" . $timeInSec . "&nbsp;" . $GLOBALS['secsString'];
			else
				return $Minutes . "&nbsp;" . $GLOBALS['minsString'];
		}

		$Hours = floor($Minutes / 60);
		$Minutes = $Minutes % 60;

		return $Hours . ":" . $Minutes . ":" . $timeInSec;
	}

	function GetAICC($field)
	{
		if (isset($_SESSION['QuizzAICCInfo'][$field]) &&
				trim($_SESSION['QuizzAICCInfo'][$field]) != '')
			return $_SESSION['QuizzAICCInfo'][$field];
		else
			return false;
	}

	function SetAICC($field, $value)
	{
		$_SESSION['QuizzAICCInfo'][$field] = $value;
	}

	function IsScorm_Sent()
	{
		if (isset($_SESSION['QuizzSession']['Scorm_Sent']))
			return $_SESSION['QuizzSession']['Scorm_Sent'];
		else
			return false;
	}

	function Scorm_Sent()
	{
		$_SESSION['QuizzSession']['Scorm_Sent'] = true;
	}

	function IsFeedback_Sent()
	{
		if (isset($_SESSION['QuizzSession']['Feedback_Sent']))
			return $_SESSION['QuizzSession']['Feedback_Sent'];
		else
			return false;
	}

	function Feedback_Sent()
	{
		$_SESSION['QuizzSession']['Feedback_Sent'] = true;
	}

	function IsNotificationEmail_Sent()
	{
		if (isset($_SESSION['QuizzSession']['NotificationEmail_Sent']))
			return $_SESSION['QuizzSession']['NotificationEmail_Sent'];
		else
			return false;
	}

	function NotificationEmail_Sent()
	{
		$_SESSION['QuizzSession']['NotificationEmail_Sent'] = true;
	}

	function AICCTimeString($timeInSec)
	{
		$Minutes = floor($timeInSec / 60);
		$timeInSec = $timeInSec % 60;

		$Hours = floor($Minutes / 60);
		$Minutes = $Minutes % 60;

		return $Hours . ":" . $Minutes . ":" . $timeInSec;
	}

	function GetThemeArray($pageIndex = false)
	{
		if ($pageIndex === false)
			$pageIndex = $this->GetCurrentPageIndex();

		$pageIndex = $_SESSION['QuizzSession']['PageMapping'][$pageIndex];

		$ret = array();

		if (isset($this->Pages[$pageIndex]['Theme']))
		{
			for ($i = 0; $i < count($this->Pages[$pageIndex]['Theme']); $i++)
			{
				if (!empty($this->Pages[$pageIndex]['Theme'][$i]))
					$ret[] = $this->Pages[$pageIndex]['Theme'][$i];
			}
		}

		return $ret;
	}

	function GetThemeHTML($pageIndex = false)
	{
		$ThemeArray = $this->GetThemeArray($pageIndex);

		$str = '';

		foreach ($ThemeArray as $aTheme)
		{
			if (!empty($str))
				$str .= ' &gt; ';

			$str .= '<span class="theme">' . $aTheme . '</span>';
		}

		return $str;
	}

	function GetThemeGUID($pageIndex = false)
	{
		if ($pageIndex === false)
			$pageIndex = $this->GetCurrentPageIndex();

		$pageIndex = $_SESSION['QuizzSession']['PageMapping'][$pageIndex];

		if (!empty($this->Pages[$pageIndex]['ThemeGuid']))
			return $this->Pages[$pageIndex]['ThemeGuid'];

		return false;
	}

	// Retrieve a list of all parent guids
	function GetParentGuids($RandomPageIndex)
	{
		$pageIndex = $_SESSION['QuizzSession']['PageMapping'][$RandomPageIndex];

		if (empty($this->Pages[$pageIndex]['ThemeGuid']))
			return array();

		$AncestorGuids = array();

		$CurrentGuid = $this->Pages[$pageIndex]['ThemeGuid'];

		$AncestorGuids[$CurrentGuid] = $CurrentGuid;

		$bFound = true;

		while ($bFound)
		{
			$bFound = false;

			foreach ($this->Themes as $aGuid => $aTheme)
			{
				if (!empty($aTheme['ParentGuid']) && $aGuid == $CurrentGuid)
				{
					$CurrentGuid = $aTheme['ParentGuid'];
					$bFound = true;
					$AncestorGuids[$CurrentGuid] = $CurrentGuid;
					break;
				}
			}
		}

		return $AncestorGuids;
	}

	function GetThemesWithParentGuid($guid)
	{
		$ret = array();
		foreach ($this->Themes as $themeguid => $aTheme)
		{
			if ((empty($guid) && empty($aTheme['ParentGuid'])) ||
					(!empty($guid) &&
					 !empty($aTheme['ParentGuid']) &&
					 $aTheme['ParentGuid'] == $guid))
			{
				$aTheme['children'] = $this->GetThemesWithParentGuid($themeguid);
				$ret[] = $aTheme;
			}
		}

		return $ret;
	}

	function GetThemeRecursive()
	{
		return $this->GetThemesWithParentGuid(false);
	}

	function AddScoresToTheme(&$ScoresByThemes, $aTheme, $ThemeText)
	{
		if (!isset($ScoresByThemes[$aTheme['guid']]))
		{
			$ScoresByThemes[$aTheme['guid']] = array('them'  => $ThemeText,
																							'score' => 0,
																							'max'   => 0);
		}

		if (!empty($aTheme['children']))
			foreach ($aTheme['children'] as $aChildTheme)
				$this->AddScoresToTheme($ScoresByThemes, $aChildTheme, $ThemeText . ' > ' . $aChildTheme['title']);

		foreach ($ScoresByThemes as $guid => $aScore)
		{
			if (empty($this->Themes[$guid]['ParentGuid']))
				continue;

			$ParentGuid = $this->Themes[$guid]['ParentGuid'];

			if ($ParentGuid == $aTheme['guid'])
			{
				$ScoresByThemes[$aTheme['guid']]['score'] += $ScoresByThemes[$guid]['score'];
				$ScoresByThemes[$aTheme['guid']]['max'] += $ScoresByThemes[$guid]['max'];
			}
		}
	}

	function GetHorizontalHistogram($rapport, $maxl, $height)
	{
		if ($rapport > 1)
			$rapport = 1;

		$l1 = round( ($rapport * $maxl) + 1 );
		$l2 = round( $maxl - ($rapport * $maxl) + 1 );

		$res  = '<img src="_images/histo-h_marron.gif" width=' . $l1 .' height=' . $height . '>';
		$res .= '<img src="_images/histo-h_dark.gif" width=' . $l2 .' height=' . $height . '>';

		return $res;
	}

	function getScoreByTheme()
	{
		$ret = array();

		$ScoresByThemes = array();

		// calculate the scores and times
		for($i = 0; $i < $this->GetPageCount(); $i++)
		{
			// Show Score
			if ($this->GetPageData($i, 'MaxScore') == 0)
				$this->SetPageData($i, 'Score', 0);
		}

		// Calculate the scores for each theme
		for($i = 0; $i < $this->GetPageCount(); $i++)
		{
			if ($this->GetPageData($i, 'MaxScore') == 0) // Explanation pages, etc...
				continue;

			// if we are not in a random quizz, add the theme :
			$thisThemeHTML = $this->GetThemeHTML($i);
			$thisThemeGUID = $this->GetThemeGUID($i);

			if (empty($thisThemeGUID))
				continue;

			if (!isset($ScoresByThemes[$thisThemeGUID]))
			{
				$ScoresByThemes[$thisThemeGUID] = array('them'  => strip_tags($thisThemeHTML),
																							 'score' => $this->GetPageData($i, 'Score'),
																							 'max'   => $this->GetPageData($i, 'MaxScore'));
			}
			else
			{
				$ScoresByThemes[$thisThemeGUID]['score'] += $this->GetPageData($i, 'Score');
				$ScoresByThemes[$thisThemeGUID]['max'] += $this->GetPageData($i, 'MaxScore');
			}
		}

		// Add the sub themes scores:
		$recursiveThemes = $this->GetThemeRecursive();
		foreach ($recursiveThemes as $aTheme)
			$this->AddScoresToTheme($ScoresByThemes, $aTheme, $aTheme['title']);

		return $ScoresByThemes;
	}

	function getScoreTable(&$ScoresByThemes,
												 $FinalMaxScore = 0,
												 $NoScore = false)
	{
		$ret = array();

		$totalScore = 0;
		$scoreMax = 0;
		$totalTime = 0;
		$totalTimeMax = 0;
		$oneQuestNotTimeLimited = false;

		// calculate the scores and times
		for($i = 0; $i < $this->GetPageCount(); $i++)
		{
			// Show Score
			if ($this->GetPageData($i, 'MaxScore') != 0)
			{
				$totalScore += $this->GetPageData($i, 'Score');
				$scoreMax   += $this->GetPageData($i, 'MaxScore');
			}

			// Show time
			$totalTime += $this->GetPageData($i, 'Time');

			if ($this->GetPageData($i, 'TimeMax') == 0 || $this->IsGlobalTimer)
			{
				// time is not limited
				$oneQuestNotTimeLimited	= true;
			}
			else
			{
				$totalTimeMax += $this->GetPageData($i, 'TimeMax');
			}
		}

		if ($this->IsGlobalTimer)
		{
			$totalTime = $_SESSION['QuizzSession']['GlobalSpentTime'];
			$totalTimeMax = $this->GlobalTimeMax;
		}
		else if ($oneQuestNotTimeLimited)
			$totalTimeMax = 0;

		$totalTime = round($totalTime);
		$totalTimeMax = round($totalTimeMax);

		if ($scoreMax != 0)
			$ScorePerCent = $totalScore * 100 / $scoreMax;
		else
			$ScorePerCent = 0;

		if ($totalScore < 0)
			$totalScore = 0;

		if ($FinalMaxScore > 0)
		{
			if ($scoreMax != 0)
				$totalScore = round($totalScore * $FinalMaxScore / $scoreMax, 2);
			else
				$totalScore = 0;

			$scoreMax = $FinalMaxScore;
		}
		else
		{
			$totalScore = round($totalScore, 2);
		}

		if (!$NoScore)
		{
			$ret['Total'] = array('Score' => $totalScore,
														'ScoreMax' => $scoreMax,
														'Time' => $totalTime,
														'TimeMax' => $totalTimeMax);

			$ret['Comments'] = array();

			// output main comments
			foreach ($this->ScoreComments as $aComment)
			{
				$fromScore = $aComment['from'];
				$toScore = $aComment['to'];

				if ((($fromScore < $ScorePerCent) && ($ScorePerCent <= $toScore)) ||
						($fromScore == 0 && $ScorePerCent == 0))
				{
					$ret['Comments']['General'] = $aComment['comment'];
					break;
				}
			}

			// output themes comments
			foreach ($this->Themes as $guid => $aTheme)
			{
				if (empty($aTheme['ScoreComments']) || empty($ScoresByThemes[$guid]))
					continue;

				if (empty($ScoresByThemes[$guid]['max']))
					continue;

				if ($ScoresByThemes[$guid]['max'] != 0)
					$ThemeScorePerCent = $ScoresByThemes[$guid]['score'] * 100 / $ScoresByThemes[$guid]['max'];
				else
					$ThemeScorePerCent = 0;

				foreach ($aTheme['ScoreComments'] as $aComment)
				{
					$fromScore = $aComment['from'];
					$toScore = $aComment['to'];

					if ((($fromScore < $ThemeScorePerCent) && ($ThemeScorePerCent <= $toScore)) ||
							($fromScore == 0 && $ThemeScorePerCent == 0))
					{
						$ret['Comments'][$guid] = array('Theme' => $aTheme,
																						'Comment' => $aComment['comment']);
						break;
					}
				}
			}
		} // if the score is calculated

		$QuestionNumber = 1;

		foreach ($_SESSION['QuizzSession']['PageMapping'] as $RandomPageId => $OriginalPageId)
			$ret['ScoreByQuestion'][$OriginalPageId] = $this->GetScoreForQuestion($RandomPageId, $QuestionNumber);

		$counts = $this->GetProfileCounts();
		$ret['Profiles'] = $this->GetProfiles($counts);

		if ($NoScore)
			$totalScore = -1;

		$_SESSION['QuizzSession']['TotalScore'] = $totalScore;
		$_SESSION['QuizzSession']['TotalScoreMax'] = $scoreMax;
		$_SESSION['QuizzSession']['TotalTime'] = $totalTime;
		$_SESSION['QuizzSession']['AICC_Status'] = 'c';

		return $ret;
	}

	function GetProfiles(&$counts)
	{
		$ret = array();

		global $Profiles;

		if (empty($counts))
			return array();

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

		$RadarParams = array();

		foreach ($Profiles as $aProfile)
		{
			if (!empty($counts[$aProfile['guid']]))
				$ratio = $counts[$aProfile['guid']] * 100 / $TotalWeight;
			else
				$ratio = 0;

			$ret[] = array('Label' => $aProfile['label'],
										 'Percent' => $ratio,
										 'Description' => $aProfile['description']);
		}

		return $ret;
	}

	function GetScoreForQuestion($RandomPageIndex, &$QuestionNumber)
	{
		if ($this->GetPageData($RandomPageIndex, 'MaxScore') == 0) // Explanation pages, etc...
			return false;

		// The page has some score

		$strUID = $this->GetPageData($RandomPageIndex, 'uid');

		if (!empty($strUID) && !empty($this->QuestionDefinition[$strUID]['Title']))
			$strQuestionTitle = $this->QuestionDefinition[$strUID]['Title'];
		else
		{
			// NB: this is not translated, but appears here for backward compatibility only.
			// The questions should have a title and if not the title can be set at any time.
			$strQuestionTitle = $GLOBALS['QuestionString'] . '&nbsp;' .  ($QuestionNumber++);
		}

		if ($this->GetPageData($RandomPageIndex, 'TimeMax') == 0 || $this->IsGlobalTimer)
			$timeMax = 'NA';
		else
			$timeMax = round($this->GetPageData($RandomPageIndex, 'TimeMax'));

		return array('Title' => $strQuestionTitle,
								 'OriginalId' => $this->GetPageData($RandomPageIndex, 'originalId'),
								 'UID' => $strUID,
								 'Score' => $this->GetPageData($RandomPageIndex, 'Score'),
								 'ScoreMax' => $this->GetPageData($RandomPageIndex, 'MaxScore'),
								 'Time' => round($this->GetPageData($RandomPageIndex, 'Time')),
								 'TimeMax' => $timeMax);
	}

	function EchoComments(&$ScoreTable)
	{
		if (empty($ScoreTable['Comments']))
			return;

		if (!empty($ScoreTable['Comments']['General']))
		{
			echo '<br><br><table width="100%" border="0"  cellspacing="0" cellpadding="0" class="ScoreComment">';
			echo ' <tr> ';
			echo '  <td class="ScoreComment">'.$ScoreTable['Comments']['General'].'</td>';
			echo ' </tr>';
			echo '</table>';
		}

		foreach ($ScoreTable['Comments'] as $aGuid => $ThemeComment)
		{
			if ($aGuid == 'General')
				continue;

			echo '<br>' . "\n";
			echo '<table width="100%" border="0"  cellspacing="0" cellpadding="0" class="ScoreComment">';
			echo ' <tr> ';
			echo '  <td class="ScoreComment"><b>'.$ThemeComment['Theme']['title'] . '</b><br>' . $ThemeComment['Comment'].'</td>';
			echo ' </tr>';
			echo '</table>';
		}
	}

	function EchoProfiles(&$ScoreTable)
	{
		if (empty($ScoreTable['Profiles']))
			return;

		echo '<div align="center"><table width="100%" border="0" cellspacing="0" cellpadding="2">' . "\n";

		$RadarParams = array();

		foreach ($ScoreTable['Profiles'] as $aProfile)
		{
			$ratio = number_format($aProfile['Percent'], 2);

			$RadarParams[] = 't[]=' . rawurlencode($aProfile['Label']) . '&v[]=' . $ratio . '&m[]=100';

			echo '<tr><td valign="top"><b>'.$aProfile['Label'].'</b></td><td align="right" valign="top"><b>'.$ratio.' %</b></td></tr>' . "\n";
			echo '<tr><td colspan="2" valign="top">'.$aProfile['Description'].'</td></tr>' . "\n";
		}

		echo '</table>' . "\n";

		echo '</div>' . "\n";
	}

	function DisplayScoreForTheme(&$Theme, &$ScoresByThemes, $depth, &$QuestionNumber)
	{
		if (!isset($Theme['ShowSubtotal']))
			$bShowThemeSubTotal = ($Theme['type'] == 'folder');
		else
			$bShowThemeSubTotal = $Theme['ShowSubtotal'];

		if ($bShowThemeSubTotal)
		{
			$strThemeTitle = $this->Themes[$Theme['guid']]['title'];
			echo ' <tr><td class="ScoreHead" colspan="4" style="padding-left: '.(5*$depth).'px">'.$strThemeTitle.'</td></tr>';
		}

		if (!empty($Theme['children']))
		{
			if (!empty($Theme['RandomizeLevel']) &&
					empty($Theme['RandomizeButKeepOrganised']))
			{
				$questionsAtThisLevel = array();
				$this->GetQuestionsForTheme($Theme, $questionsAtThisLevel);

				foreach ($_SESSION['QuizzSession']['PageMapping'] as $RandomPageId => $OriginalPageId)
				{
					if (!in_array($OriginalPageId, $questionsAtThisLevel))
						continue;

					$this->DisplayScoreStringForQuestion($RandomPageId, $QuestionNumber, $depth);
				}
			}
			else
			{
				foreach ($Theme['children'] as $aFolderOrQuestion)
				{
					if ($aFolderOrQuestion['type'] == 'question')
					{
						$RandomPageIndex = $this->TranslatePageNumber($aFolderOrQuestion['originalId']);

						if ($RandomPageIndex === false)
						{
							// the question is not taken in this quizz (cut out by a shunting question or randomization)
							continue;
						}

						$this->DisplayScoreStringForQuestion($RandomPageIndex, $QuestionNumber, $depth);
					}

					if (!empty($aFolderOrQuestion['children']))
						$this->DisplayScoreForTheme($aFolderOrQuestion, $ScoresByThemes, $depth + 1, $QuestionNumber);
				}
			}
		}

		if ($bShowThemeSubTotal)
		{
			echo ' <tr> ';
			echo '  <td class="ScoreSubTotal" style="padding-left: '.(5*$depth).'px"><b>' .$GLOBALS['SubTotalString'].'</b></td>';

			echo '  <td class="ScoreSubTotal"><span dir="ltr"><b>' . round($ScoresByThemes[$Theme['guid']]['score'], 2) . '/' . round($ScoresByThemes[$Theme['guid']]['max'], 2) . '</b></span></td>';

			echo '	<td class="ScoreSubTotal">&nbsp;</td>';
			echo '	<td width="120" class="ScoreSubTotal">&nbsp;</td>';
			echo ' </tr>';
			echo ' <tr> ';
			echo '  <td class="ScoreSubTotal">&nbsp;</td>';
			echo '  <td class="ScoreSubTotal">&nbsp;</td>';
			echo '	<td class="ScoreSubTotal">&nbsp;</td>';
			echo '	<td width="120" class="ScoreSubTotal">&nbsp;</td>';
			echo ' </tr>';
		}
	}

	function DisplayScoreStringForQuestion($RandomPageIndex, &$QuestionNumber, $depth)
	{
		if (!$this->GetPageData($RandomPageIndex, 'MaxScore') == 0) // Explanation pages, etc...
		{
			// The page has some score

			echo "  <tr>\n";

			$strUID = $this->GetPageData($RandomPageIndex, 'uid');

			if (!empty($strUID) && !empty($this->QuestionDefinition[$strUID]['Title']))
				$strQuestionTitle = $this->QuestionDefinition[$strUID]['Title'];
			else
			{
				// NB: this is not translated, but appears here for backward compatibility only.
				// The questions should have a title and if not the title can be set at any time.
				$strQuestionTitle = $GLOBALS['QuestionString'] . '&nbsp;' .  $QuestionNumber;
			}

			$QuestionNumber++;

			if ($this->ShowCorrection && $this->GetPageData($RandomPageIndex, 'Viewed'))
				$strQuestionTitle = '<a class="TOC_Content" href="_ManagerFrame.php?PageNumber='.$this->GetCurrentPageIndex().'&Direction=4&NavigationDirectAccess=' . $RandomPageIndex .'">' . $strQuestionTitle . '</a>';

			echo '    <td class="ScoreQuestionRow" style="padding-left: '.(5*$depth).'px">' .$strQuestionTitle . "</td>\n";

			// Show Score
			echo '    <td class="ScoreQuestionRow"><span dir="ltr">' . round($this->GetPageData($RandomPageIndex, 'Score'), 2) . '/' . round($this->GetPageData($RandomPageIndex, 'MaxScore'), 2) . '</span></td>';

			// Show time
			$time = round($this->GetPageData($RandomPageIndex, 'Time'));

			if ($this->GetPageData($RandomPageIndex, 'TimeMax') == 0 || $this->IsGlobalTimer || $GLOBALS['QuizzManager']->NoTimer)
			{
				// time is not limited
				echo '    <td class="ScoreQuestionRow"><span dir="ltr">' . QuizzManagerBase::CalculateTimeString($time) . '</span></td>';
				echo '    <td width="120" class="ScoreQuestionRow">&nbsp;</td>';
			}
			else
			{
				if ($this->GetPageData($RandomPageIndex, 'TimeMax') != 0)
					$rapport = ($time / $this->GetPageData($RandomPageIndex, 'TimeMax'));

				$imgstr = QuizzManagerBase::GetHorizontalHistogram($rapport, 100, 6);

				echo '    <td class="ScoreQuestionRow"><span dir="ltr">' . QuizzManagerBase::CalculateTimeString($time) . '</span>/<span dir="ltr">' . QuizzManagerBase::CalculateTimeString($this->GetPageData($RandomPageIndex, 'TimeMax')) . '</span></td>';

				echo '    <td width="120" class="ScoreQuestionRow">' . $imgstr . '</td>';
			}

			echo "  </tr>\n";
		}
	}

	function SendScorm()
	{
		if ($this->IsScorm_Sent())
			return;

		$this->Scorm_Sent();

		// SCORM
		echo '<script type="text/javascript" language="JavaScript">' . "\n";
		echo '	window.parent.sendTime();' . "\n";

		if ($_SESSION['QuizzSession']['TotalScore'] != -1)
			echo '	window.parent.sendScore('.str_replace(',', '.', $_SESSION['QuizzSession']['TotalScore']).', 0, '.str_replace(',', '.', $_SESSION['QuizzSession']['TotalScoreMax']).');' . "\n";
		else
			echo '	window.parent.sendScoreMinMax(0, '.str_replace(',', '.', $_SESSION['QuizzSession']['TotalScoreMax']).');' . "\n";

		echo '	window.parent.normalExit();' . "\n";

		echo '</script>' . "\n";
	}


	function SendAICC($bFinal = true, $ScoresByThemes = array())
	{
		if ($this->bDebug)
		{
			echo '<pre>QuizzSession :<br>';
			print_r($_SESSION['QuizzSession']);
			echo '</pre>';
			echo '<pre>QuizzAICCInfo :<br>';
			print_r($_SESSION['QuizzAICCInfo']);
			echo '</pre>';
		}

		if (!$this->GetAICC('aicc_url'))
			return;

		if (!$bFinal && $_SESSION['QuizzSession']['AICC_Status'] == 'c')
			return;

		if ($bFinal &&
				$this->GetAICC('review_mode') == 'true')
		{
			// display the form but don't submit it right away, and allow for the teacher
			// to set the final mark :

			if ($_SESSION['QuizzSession']['AICC_Status'] == 'c')
			{
				echo '<FORM onsubmit="return(SubmitScore(this));" NAME="SendAICCForm" ACTION="' . $this->GetAICC('aicc_url') . '" method="POST" target="resultsframe" ENCTYPE="application/x-www-form-urlencoded">' . "\n";

				echo 'Note finale : <input type="text" name="NewScore" value="'.$_SESSION['QuizzSession']['TotalScore'].'" size="5"> sur '. $_SESSION['QuizzSession']['TotalScoreMax'] . "\n";
				echo '<div align="right">' . "\n";
				echo '	<p><input type="submit" name="submitButtonName" value="Changer la note"></p>' . "\n";
				echo '</div>' . "\n";

				if ($this->GetAICC('ReportId'))
					echo '<INPUT TYPE="hidden" NAME="cmi_ReportId" VALUE="'.$this->GetAICC('ReportId').'">' . "\n";

				echo '<INPUT TYPE="hidden" NAME="command" VALUE="PutParam">' . "\n";
				echo '<INPUT TYPE="hidden" NAME="version" VALUE="2.0">' . "\n";
				echo '<INPUT TYPE="hidden" NAME="session_id" VALUE="'.$this->GetAICC('aicc_sid').'">' . "\n";
				echo '<INPUT TYPE="hidden" NAME="ref_id" VALUE="'.$this->GetAICC('aicc_refid').'">' . "\n";
				echo '<INPUT TYPE="hidden" NAME="aicc_data" VALUE="">' . "\n";
				echo '</FORM>' . "\n";

				echo '<script type="text/javascript" language="JavaScript">' . "\n";

				echo 'function SubmitScore(thisForm)' . "\n";
				echo '{' . "\n";
				echo '	strScore = String(thisForm.NewScore.value);' . "\n";
				echo '	re = /,/g;' . "\n";
				echo '	strScore = strScore.replace(re, ".");	' . "\n";
				echo '	thisForm.aicc_data.value = "%5Bcore%5D%0D%0AScore%3D" + strScore + "%2C' . str_replace(',', '.', $_SESSION['QuizzSession']['TotalScoreMax']) . '%2C0%0D%0A";' . "\n";
				echo '	return true;' . "\n";
				echo '}' . "\n";

				echo '</script>' . "\n";
			}
			else
			{
				echo 'Le questionnaire n\'a pas &eacute;t&eacute; termin&eacute;.';
			}
		}
		else
		{
			// display the (autosubmit) form with all the components on it
			$EasyquizzProApi = $this->HasEasyquizzProAPI();
			$bAnswersReturnedThroughEasyquizzProApi = false;
			$bForceAICC = $this->GetAICC('ForceAICC');

			if ($EasyquizzProApi)
			{
				include_once($EasyquizzProApi);

				if ($bFinal && !empty($ScoresByThemes))
					$strScoreByThemes = serialize($ScoresByThemes);
				else
					$strScoreByThemes = '';

				$ReportId = $this->GetAICC('aicc_refid');

				if (!empty($ReportId))
				{
					if ($_SESSION['QuizzSession']['AICC_Status'] == 'c')
					{
						SaveEasyquizzSession($ReportId,
																 $_SESSION['QuizzSession']['AICC_Status'],
																 str_replace(',', '.', $_SESSION['QuizzSession']['TotalScore']),
																 str_replace(',', '.', $_SESSION['QuizzSession']['TotalScoreMax']),
																 $_SESSION['QuizzSession']['GlobalSpentTime'],
																 serialize($_SESSION['QuizzSession']),
																 $strScoreByThemes);

						$bAnswersReturnedThroughEasyquizzProApi = true;
					}
					else
					{
						SaveEasyquizzSession($ReportId,
																 $_SESSION['QuizzSession']['AICC_Status'],
																 false,
																 false,
																 $_SESSION['QuizzSession']['GlobalSpentTime'],
																 serialize($_SESSION['QuizzSession']),
																 $strScoreByThemes);

						$bAnswersReturnedThroughEasyquizzProApi = true;
					}

					$this->SetAICC('aicc_refid', $ReportId);
				}

				if ($bForceAICC != 'Y')
				{
					// Post an empty form just to update the calling page if present
					echo '<form name="SendAICCForm" action="' . $this->GetAICC('aicc_url') . '" method="POST" target="resultsframe">' . "\n";
					echo '<input type="hidden" name="dummy" value="dummy">' . "\n";
					echo '</form>' . "\n";

					echo '<script type="text/javascript" language="JavaScript">' . "\n";
					echo 'var bUseAjaxRefresh = false;' . "\n";
					echo 'try' . "\n";
					echo '{' . "\n";
					echo '	var myEpistemaAPI = false;' . "\n";
					echo '	if (window.top.EpistemaAPI)' . "\n";
					echo '		myEpistemaAPI = window.top.EpistemaAPI;' . "\n";
					echo '	else if (window.top.opener && window.top.opener.EpistemaAPI)' . "\n";
					echo '		myEpistemaAPI = window.top.opener.EpistemaAPI;' . "\n";
					echo '	if (myEpistemaAPI != false)' . "\n";
					echo '	{' . "\n";
					echo '		myEpistemaAPI.RefreshReport(\''.$ReportId.'\');' . "\n";
					echo '		bUseAjaxRefresh = true;' . "\n";
					echo '	}' . "\n";
					echo '}' . "\n";
					echo 'catch (e)' . "\n";
					echo '{}' . "\n";
					echo 'if (!bUseAjaxRefresh)' . "\n";
					echo '{' . "\n";
					echo '	try' . "\n";
					echo '	{' . "\n";
					echo '		if (window.top.opener && window.top.opener.name != "")' . "\n";
					echo '		{' . "\n";
					echo '			document.SendAICCForm.target = window.top.opener.name;' . "\n";
					echo '			document.SendAICCForm.submit();' . "\n";
					echo '		}' . "\n";
					echo '	}' . "\n";
					echo '	catch (e)' . "\n";
					echo '	{}' . "\n";
					echo '}' . "\n";
					echo '</script>' . "\n";
				}
				else
					$bAnswersReturnedThroughEasyquizzProApi = false;
			}

			if (!$bAnswersReturnedThroughEasyquizzProApi)
			{
			  $RefId = $this->GetAICC('aicc_refid');

				$strAiccReq  = "[core]\r\n";
				$strAiccReq .= "Lesson_Status=".$_SESSION['QuizzSession']['AICC_Status']."\r\n";

				if ($_SESSION['QuizzSession']['AICC_Status'] == 'c')
				{
					$strAiccReq .= "Score=" . str_replace(',', '.', $_SESSION['QuizzSession']['TotalScore']) . ',' . str_replace(',', '.', $_SESSION['QuizzSession']['TotalScoreMax']) . ",0\r\n";
					$strAiccReq .= "Time=" . $this->AICCTimeString($_SESSION['QuizzSession']['TotalTime'])  . "\r\n";
				}

				$strAiccReq .= "[core_lesson]\r\n";
				$strAiccReq .= serialize($_SESSION['QuizzSession']) . "\r\n";

				$strAiccReq .= $this->GetAICC_CMIInteraction();

			  if (!empty($RefId) && !$bForceAICC)
			  {
			  	// We are within epilearn
					echo '<form style="margin: 0; padding: 0" name="SendAICCForm" action="' . $this->GetAICC('aicc_url') . '" method="post" target="resultsframe" enctype="application/x-www-form-urlencoded">' . "\n";
					echo '<input type="hidden" name="command" value="PutParam">' . "\n";
					echo '<input type="hidden" name="version" value="2.0">' . "\n";
					echo '<input type="hidden" name="session_id" value="'.$this->GetAICC('aicc_sid').'">' . "\n";
					echo '<input type="hidden" name="ref_id" value="'.$this->GetAICC('aicc_refid').'">' . "\n";

					if ($bFinal && !empty($ScoresByThemes))
						echo '<input type="hidden" name="scores_them" value="'.urlencode(serialize($ScoresByThemes)).'">' . "\n";

					if ($this->GetAICC('ReportId'))
						echo '<input type="hidden" name="cmi_ReportId" value="'.$this->GetAICC('ReportId').'">' . "\n";

					echo '<input type="hidden" name="aicc_data" value="'.urlencode($strAiccReq).'">' . "\n";
					echo '</form>' . "\n";

					echo '<form style="margin: 0; padding: 0" name="SendAICC_ExitAUForm" action="' . $this->GetAICC('aicc_url') . '" method="post" target="resultsframe">' . "\n";
					echo '<input type="hidden" name="command" value="ExitAU">' . "\n";
					echo '<input type="hidden" name="version" value="2.0">' . "\n";
					echo '<input type="hidden" name="session_id" value="'.$this->GetAICC('aicc_sid').'">' . "\n";
					echo '</form>' . "\n";
			  }
			  else
			  {
			  	// standard AICC
					echo '<form style="margin: 0; padding: 0" name="SendAICCForm" action="' . $this->GetAICC('aicc_url') . '" method="post" target="resultsframe">' . "\n";
					echo '<input type="hidden" name="command" value="PutParam">' . "\n";
					echo '<input type="hidden" name="version" value="2.0">' . "\n";
					echo '<input type="hidden" name="session_id" value="'.$this->GetAICC('aicc_sid').'">' . "\n";
					echo '<textarea style="display:none" name="aicc_data" rows="8" cols="40">'.$strAiccReq.'</textarea>		' . "\n";
					echo '</form>' . "\n";

					echo '<form style="margin: 0; padding: 0" name="SendAICC_ExitAUForm" action="' . $this->GetAICC('aicc_url') . '" method="post" target="resultsframe">' . "\n";
					echo '<input type="hidden" name="command" value="ExitAU">' . "\n";
					echo '<input type="hidden" name="version" value="2.0">' . "\n";
					echo '<input type="hidden" name="session_id" value="'.$this->GetAICC('aicc_sid').'">' . "\n";
					echo '</form>' . "\n";
			  }

			  echo '<script type="text/javascript" language="JavaScript">' . "\n";
			  echo 'try' . "\n";
			  echo '{' . "\n";
			  echo '	if (window.top.opener && window.top.opener.name != "")' . "\n";
			  echo '	{' . "\n";
			  echo '		document.SendAICCForm.target = window.top.opener.name;' . "\n";
		  	echo '		document.SendAICC_ExitAUForm.target = window.top.opener.name;' . "\n";
			  echo '	}' . "\n";
			  echo '}' . "\n";
			  echo 'catch (e)' . "\n";
			  echo '{}' . "\n";
			  echo '' . "\n";
			  echo 'document.SendAICCForm.submit();' . "\n";
			  if ($_SESSION['QuizzSession']['AICC_Status'] == 'c')
			  	echo 'window.setTimeout("document.SendAICC_ExitAUForm.submit();", 500);' . "\n";
			  echo '</script>' . "\n";
		  }
	  }
	}

	function GetAICC_CMIInteraction()
	{
		if ($_SESSION['QuizzSession']['AICC_Status'] != 'c')
			return '';

		global $QuestionDefinition;

		$strAiccReq = '';

		$strAiccReq .= "[cmi]\r\n";
		$strAiccReq .= "_version=3.4\r\n";
		$strAiccReq .= "[cmi.interactions]\r\n";

		if (!file_exists('admin/data.php'))
			return;

		include_once(str_replace('_ressources/inc.manager.php', 'admin/data.php', str_replace('\\', '/', __FILE__)));

		for ($pageNumber = 0; $pageNumber < $this->GetPageCount(); $pageNumber++)
		{
			$answer = '';
			$UID = $this->GetPageData($pageNumber, 'uid');

			$questionData = "";

			foreach ($QuestionDefinition as $aQuestion)
			{
				if ($aQuestion['UID'] == $UID)
				{
					$questionData = $aQuestion;
					break;
				}
			}

			$PageAnswers = $this->GetPageData($pageNumber, 'Answers');

			foreach ($PageAnswers as $input => $answer)
			{
				$answerRow = preg_replace("/[a-zA-Z]/",'',$input); // remove all letters

				if (is_array($answer))
				{
					if (count($answer) > 0)
						$strAnswers = implode(',', $answer);

					$answer = $strAnswers;
				}
			}

			$strAiccReq .= $pageNumber.'.id=' . $UID . "\r\n";

			switch ($questionData['Type'])
			{

				case 'QCM':
					$strAiccReq .= $pageNumber.'.type=Multiple Choice' . "\r\n";
					break;

				case 'QCU':
					$strAiccReq .= $pageNumber.'.type=True/False' . "\r\n";
					break;

				case 'TAT':
					$strAiccReq .= $pageNumber.'.type=Fill in the Blank' . "\r\n";
					break;

				case 'MATCH':
					$strAiccReq .= $pageNumber.'.type=Matching' . "\r\n";
					break;

				case 'SORT':
					$strAiccReq .= $pageNumber.'.type=Sequencing' . "\r\n";
					break;

				case 'TABQCU':
					$strAiccReq .= $pageNumber.'.type=Likert' . "\r\n";
					break;
			}

			if ($questionData['Type'] == 'QCM' || $questionData['Type']== 'QCU')
			{
				$givenAnswers = explode(",", $answer);
				$i = 0;
				$strAiccReq .= $pageNumber.'.student_response=';

				while (isset($givenAnswers[$i]) && !empty($givenAnswers[$i]))
				{
					// Get the answer label
					$answerLabel = $questionData['Answers'][$givenAnswers[$i]];

					$strAiccReq .= $answerLabel;

					if (isset($givenAnswers[$i+1]))
						$strAiccReq .= ';';

					$i++;
				}

				$strAiccReq .=  "\r\n";

				if ($this->GetPageData($pageNumber, 'Score') == $this->GetPageData($pageNumber, 'MaxScore'))
					$strAiccReq .= $pageNumber.'.result=correct' . "\r\n";
				else
					$strAiccReq .= $pageNumber.'.result=wrong' . "\r\n";

				$strAiccReq .= $pageNumber.'.correct_responses.0.pattern='. $questionData['Question'] . "\r\n";

				$nbGoodAnswers = 1;

				if (isset($questionData['Correct_Answers']))
				{
					for ($my_index = 0; $my_index < count($questionData['Correct_Answers']); $my_index++)
					{
						$aValue = &$questionData['Correct_Answers'][$my_index];

						if ($aValue == 'true')
						{
							$strAiccReq .= $pageNumber.'.correct_responses.'. $nbGoodAnswers .'.pattern='. $questionData['Answers'][$my_index] . "\r\n";
							$nbGoodAnswers++;
						}
					}
				}
			}
		}// end for each question

		return $strAiccReq;
	}

	function GetDataForReport($scorm_userid = '', $scorm_username = '', $UserFirstname = '')
	{
		$QuestionsAnswers = array();
		$QuestionsMarks = array();
		$QuestionsUIDs = array();

		$UserId = false;
		$UserName = '';

		for ($pageNumber = 0; $pageNumber < $this->GetPageCount(); $pageNumber++)
		{
			$RealPageNumber = $this->GetPageData($pageNumber, 'originalId');

			$PageAnswers = $this->GetPageData($pageNumber, 'Answers');

			unset($PageData);

			foreach ($PageAnswers as $input => $answer)
			{
				$inputtype = preg_replace("/[0-9]/",'',$input); // remove all numbers
				$answerRow = preg_replace("/[a-zA-Z]/",'',$input); // remove all letters

				$bIsAnswer = true;

				switch ($inputtype)
				{
					case 'T':
						if (!isset($PageData)) $PageData = array();

						$PageData[$answerRow] = stripslashes($answer);
						break;

					case 'QCM':
						if (!isset($PageData)) $PageData = array();

						$PageData['checked'] = $answer;
						break;

					case 'QCU':
						if (!isset($PageData)) $PageData = array();

						$PageData['checked'] = $answer;
						break;

					case 'MORE':
						if (!isset($PageData)) $PageData = array();

						$PageData['more'][$answerRow] = nl2br(stripslashes($answer));
						break;

					case 'TABQCM':
						if (!isset($PageData)) $PageData = array();

						$PageData[$answerRow] = $answer;
						break;

					case 'TABQCU':
						if (!isset($PageData)) $PageData = array();

						$PageData[$answerRow] = $answer;
						break;

					case 'MORETAB':
						if (!isset($PageData)) $PageData = array();

						$PageData['more'] = nl2br(stripslashes($answer));
						break;

					case 'UserName':
						$UserName = nl2br(stripslashes($answer));
						$bIsAnswer = false;
						break;

					case 'UserFirstname':
						$UserFirstname = nl2br(stripslashes($answer));
						$bIsAnswer = false;
						break;

					case 'M':
						if (!isset($PageData)) $PageData = array();

						$PageData[$answerRow] = $answer;
						break;

					case 'DD':
						if (!isset($PageData)) $PageData = array();

						$PageData[$answerRow] = $answer;
						break;

					case 'S':
						if (!isset($PageData)) $PageData = array();

						$PageData[$answerRow] = $answer;
						break;

					case 'F':
						if (!isset($PageData)) $PageData = array();

						$PageData[$answerRow] = $answer;
						break;

					case 'TEXT':
					default:
						$PageData = nl2br(stripslashes($answer));
						break;
				}
			}	// foreach row in the question

			if (!$this->GetPageData($pageNumber, 'credentials'))
			{
				if (isset($PageData)) // if the user has answered
					$QuestionsAnswers[$RealPageNumber] =  $PageData;

				$QuestionsMarks[$RealPageNumber] = array( "sc" => 	$this->GetPageData($pageNumber, 'Score'),
																									"msc" =>	$this->GetPageData($pageNumber, 'MaxScore'),
																									"t" => 		$this->GetPageData($pageNumber, 'Time'),
																									"mt" =>		$this->GetPageData($pageNumber, 'TimeMax'));

				$UID = $this->GetPageData($pageNumber, 'uid');
				$QuestionsUIDs[$UID] = $RealPageNumber;
			}
		} // for each question

		if (!empty($scorm_username))
		{
			$UserName = $scorm_username;
			$UserFirstname = ""; // both name and firstname are in scorm_username
			$UserId = $scorm_userid;
		}

		return array(	"Username" => $UserName,
									"UserFirstname" => $UserFirstname,
									"UserId" => $UserId,
									"who" => $this->GetHostName(),
									"score" => $_SESSION['QuizzSession']['TotalScore'],
									"score_max" => $_SESSION['QuizzSession']['TotalScoreMax'],
									"time_spent" => $_SESSION['QuizzSession']['TotalTime'],
									"when" => time(),
									"answers" => $QuestionsAnswers,
									"scores" => $QuestionsMarks,
									"id_map" => $QuestionsUIDs);
	}

	function WriteAnswersToUserDataFile($scorm_username = "", $scorm_userid = "")
	{
		// Write the answers into userdata.php

		if (!$this->isSurvey)
			return;

		if ($this->IsFeedback_Sent())
			return;

		if (!file_exists('admin/data.php'))
			return;

		$file_handle = @fopen($this->GetUserDataFilePath(), 'a');

		if (!$file_handle)
			echo '<br>' . $GLOBALS['ErrorWritingUserdata'] . '<br>';
		else
		{
			$data = $this->GetDataForReport($scorm_userid, $scorm_username);

			$str = "<? \n".' $QuestionAnswers[] = '.var_export($data, true).'; ?>';

			fwrite($file_handle, $str);
			fclose($file_handle);

			$this->Feedback_Sent();
		}
	}

	/**
	 * displays the page with the quizz, depending on the current page
	 */
	function displayPage()
	{
		$PageName = $this->GetCurrentPageData("filename");
		$this->SetCurrentPageData('Viewed', true);

		// init the timer
		if ($this->IsCurrentPageAQuestion())
		{
			$this->SetCurrentPageData("LoadingTime" , time());

			if ($_SESSION['QuizzSession']['GlobalLoadingTime'] === false)
				$_SESSION['QuizzSession']['GlobalLoadingTime'] = time();
		}

		if ($this->bDebug)
		{
			echo 'Displaying page ' . $this->GetCurrentPageIndex() . '/' . $this->GetPageCount() . ':<br>';

			if ($this->IsCurrentPageAQuestion())
			{
				echo '<pre>Page displayed : '.$PageName.'<br>';
				print_r($_SESSION['QuizzSession']['PageData'][$this->GetCurrentPageIndex()]);
				echo '</pre>';
			}

			echo '<pre>$_GET : <br>';
			print_r($_GET);
			echo '</pre>';
		}

		$GLOBALS['ClassToInstanciate'] = 'QuestionDisplayer';
		include($PageName);

		$UserClassFile = str_replace('inc.manager.php', 'UserQuestionDisplayer.php', str_replace('\\', '/', __FILE__));
		if (file_exists($UserClassFile))
		{
			include_once($UserClassFile);
			$question = new UserQuestionDisplayer();
		}
		else
			$question = new QuestionDisplayer();

		if ($this->IsCurrentPageAQuestion())
		{
			$question->DisplayPage($this->InCorrection() || $this->GetCurrentPageData('ShowAnswers'),
																		 !$this->IsAdaptivePath && $this->GetCurrentPageIndex() == $this->GetPageCount() - 1,
																		 $this->HasAnsweredAllQuestions());
			$this->SetCurrentPageData("ShowAnswers", false);
		}
		else
			$question->DisplayPage(false, false, true);
	}

	function HasAnsweredAllQuestions()
	{
		// Return false if a question has not been answered yet.

		// Note :     we don't test the last question, as for answering this
		//            one you have to go past the javascript box.
		// Solution : we need to replace the javascript with an intermediary page.

		// Note :     Once again we only test questions with a max score.
		//            This is not a hundred percent acurate, as the teacher could
		//            set a question to 0 if he wants to set the score later (open question
		//            for example). This should be fixed somehow.

		for($i = 0; $i < $this->GetPageCount() - 1; $i++)
		{
			if ($this->GetPageData($i, 'MaxScore') > 0 &&
					count($this->GetPageData($i, 'Answers')) == 0)
				return false;
		}

		return true;
	}

	function GetHostName()
	{
		global $HTTP_SERVER_VARS;

		$host = "";
		$IP = "";

		// $this->Get the host:

		if (!empty($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]))
		{
			// if the remote machine is behind a proxy.
			$IP = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
			$host = @gethostbyaddr($IP);
		}
		else
		{
			$IP = $HTTP_SERVER_VARS["REMOTE_ADDR"];
			$host = @gethostbyaddr($IP);
		}
		return $host;
	}

	function GetUserNameForNominativeQuestionnaires(&$credentials)
	{
		for ($pageNumber = 0; $pageNumber < $this->GetPageCount(); $pageNumber++)
		{
			$PageAnswers = $this->GetPageData($pageNumber, 'Answers');

			foreach ($PageAnswers as $input => $answer)
			{
				$inputtype = preg_replace("/[0-9]/",'',$input); // remove all numbers

				if (is_array($answer))
				{
					if (count($answer) > 0)
						$strAnswers = 'array('.implode(',', $answer).')';
					else
						$strAnswers = 'null';

					$answer = $strAnswers;
				}

				switch ($inputtype)
				{
					case 'UserName':
						$credentials['UserName'] = ereg_replace("[\r\n\t]", " ", stripslashes($answer));
						break;

					case 'UserFirstname':
						$credentials['UserFirstname'] = ereg_replace("[\r\n\t]", " ", stripslashes($answer));
						break;
				}
			}

			if (!empty($credentials))
				return true;
		}

		return false;
	}

	function SendNotificationEmail($ScormUserName)
	{
		if (!$this->NotifyByEmail || empty($this->NotifyRecipient))
			return;

		if ($this->IsNotificationEmail_Sent())
			return;

		$UserDisplayName = '';
		$Credentials = array();

		if (!empty($ScormUserName))
			$UserDisplayName = $ScormUserName;
		else if ($this->GetUserNameForNominativeQuestionnaires($Credentials))
			$UserDisplayName = trim(implode(' ', $Credentials));

		$NewEntry = 0;

		$userdataFile = $this->GetUserDataFilePath();

		if (file_exists($userdataFile))
		{
			global $QuestionAnswers;

			include($userdataFile);
			$NewEntry = count($QuestionAnswers) - 1;
		}

		$subject = str_replace('{username}', $UserDisplayName, $this->NotifySubject);
		$subject = str_replace('{quizz_title}', $this->QuizzTitle, $subject);
		$subject = $this->EncodeHeader(str_replace('{date}', date("d/m/Y H:i"), $subject));

		$url = '';

		if(!$this->GetAICC('aicc_url') && $this->isSurvey)
		{
			if ($this->nominativerepports)
				$url = str_replace('_ManagerFrame.php', 'admin/rapportnominatif.php?seeingwho='.$NewEntry, 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']);
			else
				$url = str_replace('_ManagerFrame.php', 'admin/index.php', 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF']);
		}

		$message = str_replace('{username}', $UserDisplayName, $this->NotifyMessage);
		$message = str_replace('{quizz_title}', $this->QuizzTitle, $message);
		$message = str_replace('{report_url}', $url, $message);
		$message = str_replace('{date}', date("d/m/Y H:i"), $message);

		$message = str_replace("\r\n", "\r", $message);
		$message = str_replace("\r", "\n", $message);

		$from = 'Epistema Easyquizz <easyquizz@epistema.com>';
		$to   = $this->NotifyRecipient;

		$headers  = "From: $from\r\n";
		$headers .= "To: $to\r\n";
		$headers .= "Content-type: text/plain; charset=\"UTF-8\"";

		$message = str_replace("\r", "", $message);

		@mail($to, $subject, $message, $headers);

		$this->NotificationEmail_Sent();
	}

	function EncodeHeader($input, $charset = 'UTF-8')
	{
		preg_match_all('/(\w*[\x80-\xFF]+\w*)/', $input, $matches);
		foreach ($matches[1] as $value) {
			$replacement = preg_replace('/([\x80-\xFF])/e', '"=" . strtoupper(dechex(ord("\1")))', $value);
			$input = str_replace($value, '=?' . $charset . '?Q?' . $replacement . '?=', $input);
		}

		return $input;
	}

	function GetUserDataFilePath()
	{
		return str_replace('_ressources/inc.manager.php', 'admin/userdata.php', str_replace('\\', '/', __FILE__));
	}

	function GetAnswersForProfiles()
	{
		// Write the answers into userdata.php

		$QuestionsAnswers = array();

		for ($pageNumber = 0; $pageNumber < $this->GetPageCount(); $pageNumber++)
		{
			$RealPageNumber = $this->GetPageData($pageNumber, 'originalId');

			$PageAnswers = $this->GetPageData($pageNumber, 'Answers');

			unset($PageData);

			foreach ($PageAnswers as $input => $answer)
			{
				$inputtype = preg_replace("/[0-9]/",'',$input); // remove all numbers
				$answerRow = preg_replace("/[a-zA-Z]/",'',$input); // remove all letters

				$bIsAnswer = true;

				switch ($inputtype)
				{
					case 'QCM':
						if (!isset($PageData)) $PageData = array();

						$PageData['checked'] = $answer;
						break;

					case 'QCU':
						if (!isset($PageData)) $PageData = array();

						$PageData['checked'] = $answer;
						break;

					case 'TABQCM':
						if (!isset($PageData)) $PageData = array();

						$PageData[$answerRow] = $answer;
						break;

					case 'TABQCU':
						if (!isset($PageData)) $PageData = array();

						$PageData[$answerRow] = $answer;
						break;
				}
			}	// foreach row in the question

			if (!$this->GetPageData($pageNumber, 'credentials'))
			{
				if (isset($PageData)) // if the user has answered
					$QuestionsAnswers[$RealPageNumber] = $PageData;
			}
		} // for each question

		return $QuestionsAnswers;
	}

	function GetProfileCounts()
	{
		$UserAnswer = $this->GetAnswersForProfiles();

		global $Profiles;
		global $QuestionDefinition;

		include_once(str_replace('_ressources/inc.manager.php', 'admin/data.php', str_replace('\\', '/', __FILE__)));

		$counts = array();

		$i = 1;

		foreach ($QuestionDefinition as $Question)
		{
			if ($Question["Type"] == "SURVEYQCU" ||
					$Question["Type"] == "SURVEYQCM")
			{
				$v = array();

				foreach ($QuestionDefinition[$i - 1]["Answers"] as $QuestionRow)
					$v[] = array("value" => false);

				if (isset($UserAnswer[$i - 1]))
					$ThisAnswer = $UserAnswer[$i - 1];

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
					if (isset($UserAnswer[$i - 1]))
						$ThisAnswer = &$UserAnswer["answers"][$i - 1][$k];

					if (isset($ThisAnswer) && is_array($ThisAnswer))
					{
						foreach ($ThisAnswer as $SubAnswers)
							$AnswersRow[$k]["values"][$SubAnswers] = true;
					}
					else if (isset($ThisAnswer))
					{
						$AnswersRow[$k]["values"][$ThisAnswer] = true;
					}
				}

				foreach ($AnswersRow as $rowindex => $answer)
				{
					if (!isset($QuestionDefinition[$i - 1]["Weights"][$rowindex]))
						continue;

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

		return $counts;
	}

	function GetOptionForPageId($RandomPageIndex, $depth)
	{
		if ($this->GetPageData($RandomPageIndex, 'credentials'))
			return '';

		$strUID = $this->GetPageData($RandomPageIndex, 'uid');

		$bIsQuestionPage = ($this->QuestionDefinition[$strUID]['Type'] == 'EXPLANATION');

		if (!empty($strUID) && !empty($this->QuestionDefinition[$strUID]['Title']))
			$strQuestionTitle = $this->QuestionDefinition[$strUID]['Title'];
		else
		{
			// NB: this is not translated, but appears here for backward compatibility only.
			// The questions should have a title and if not the title can be set at any time.
			$strQuestionTitle = 'Question ' .  ($RandomPageIndex + 1);
		}

		$bViewed = $this->GetPageData($RandomPageIndex, 'Viewed');
		if (!$bIsQuestionPage)
			$bAnswered = $this->HasAnswered($RandomPageIndex);
		else
			$bAnswered = $bViewed;

		$Check = '';

		if (empty($bViewed))
			$style = 'style="color: #000000"';
		else if (!empty($bAnswered))
		{
			$style = 'style="color: #999999"';
			$Check = '&radic;&nbsp;';
		}
		else
			$style = 'style="color: #D3570C"';

		$strQuestionTitle = $this->get_snippet($strQuestionTitle, 50);

		if ($this->GetCurrentPageIndex() == $RandomPageIndex)
			return '<option selected value="'.$RandomPageIndex.'">'. $Check . $strQuestionTitle .'</option>' . "\n";
		else
			return '<option '.$style.' value="'.$RandomPageIndex.'">'. $Check . $strQuestionTitle . '</option>' . "\n";
	}

	function get_snippet($text, $length=64, $tail="...")
	{
		$origtext = strip_tags($text);
		$origtext = str_replace('&nbsp;', ' ', $origtext);
		$origtext = preg_replace("/[\r\n]+/", ' ', $origtext);

		$text = preg_replace("/&[^;]+;/", '_', $origtext);
		$text = trim($text);

		if (strlen($text) < $length)
			return $origtext;

		$origparts = explode(' ', $origtext);
		$parts = explode(' ', $text);

		$currentLenght = 0;
		$FinalParts = array();

		foreach ($parts as $k => $aPart)
		{
			$currentLenght += strlen($aPart);
			$FinalParts[] = $origparts[$k];

			if ($currentLenght >= $length)
				break;
		}

		$FinalParts[] = $tail;

		return implode(' ', $FinalParts);
	}

	function GetOptionsForQuestionOrFolder(&$Theme, $depth = 0)
	{
		$str = '';

		if ($Theme['type'] == 'folder' && !empty($this->Themes[$Theme['guid']]['title']))
		{
			$strThemeTitle = $this->get_snippet($this->Themes[$Theme['guid']]['title'], 50);
			$str .= '<OPTGROUP LABEL="'.str_repeat('&nbsp;', $depth). htmlspecialchars($strThemeTitle).'">' . "\n";
		}

		if (!empty($Theme['children']))
		{
			if (!empty($Theme['RandomizeLevel']) &&
					empty($Theme['RandomizeButKeepOrganised']))
			{
				$questionsAtThisLevel = array();
				$this->GetQuestionsForTheme($Theme, $questionsAtThisLevel);

				foreach ($_SESSION['QuizzSession']['PageMapping'] as $RandomPageId => $OriginalPageId)
				{
					if (!in_array($OriginalPageId, $questionsAtThisLevel))
						continue;

					$str .= $this->GetOptionForPageId($RandomPageId, $depth);
				}
			}
			else if (empty($Theme['RandomizeLevel']))
			{
				foreach ($Theme['children'] as $aFolderOrQuestion)
				{
					if ($aFolderOrQuestion['type'] == 'question')
					{
						$RandomPageIndex = $this->TranslatePageNumber($aFolderOrQuestion['originalId']);

						if ($RandomPageIndex === false)
						{
							// the question is not taken in this quizz (cut out by a shunting question or randomization)
							continue;
						}

						$str .= $this->GetOptionForPageId($RandomPageIndex, $depth);
					}

					if (!empty($aFolderOrQuestion['children']))
						$str .= $this->GetOptionsForQuestionOrFolder($aFolderOrQuestion, $depth + 1);
				}
			}
			else
			{
				$PageIds = array();
				$ChildrenFolders = array();
				$SortedChildrenFolders = array();

				foreach ($Theme['children'] as $aFolderOrQuestion)
				{
					if ($aFolderOrQuestion['type'] == 'question')
					{
						$RandomPageIndex = $this->TranslatePageNumber($aFolderOrQuestion['originalId']);

						if ($RandomPageIndex === false)
						{
							// the question is not taken in this quizz (cut out by a shunting question or randomization)
							continue;
						}

						$PageIds[] = $RandomPageIndex;
					}

					if (!empty($aFolderOrQuestion['children']))
						$ChildrenFolders[$aFolderOrQuestion['guid']] = $aFolderOrQuestion;
				}

				foreach ($_SESSION['QuizzSession']['PageMapping'] as $RandomPageId => $OriginalPageId)
				{
					$AncestorGuids = $this->GetParentGuids($RandomPageId);

					foreach ($ChildrenFolders as $guid => $aFolder)
					{
						if (in_array($guid, $AncestorGuids) && !isset($SortedChildrenFolders[$guid]))
						{
							$SortedChildrenFolders[$guid] = $aFolder;
							break;
						}
					}

					if (!in_array($RandomPageId, $PageIds))
						continue;

					$str .= $this->GetOptionForPageId($RandomPageId, $depth);
				}

				foreach ($ChildrenFolders as $guid => $aFolder)
				{
					if (!isset($SortedChildrenFolders[$guid]))
						$SortedChildrenFolders[$guid] = $aFolder;
				}

				foreach ($SortedChildrenFolders as $aFolder)
					$str .= $this->GetOptionsForQuestionOrFolder($aFolder, $depth + 1);
			}
		}

		if ($Theme['type'] == 'folder' && !empty($this->Themes[$Theme['guid']]['title']))
			$str .= '</OPTGROUP>' . "\n";

		return $str;
	}

	function GetQuestionNavigator()
	{
		$str  = '		<select onfocus="if (!this.originalIndex) this.originalIndex=(this.selectedIndex + 1)" style="font-size: 10px;" onchange="navigate_to_page(this)" name="NavigationSelect" size="1">' . "\n";
		$str .= $this->GetOptionsForQuestionOrFolder($this->RecursiveThemesAndQuestions);

		if (empty($this->HideResultPage))
			$str .= '			<option epiLang="EndTheQuestionnaireNow" value="END">- End the quizz now -</option>' . "\n";

		$str .= '		</select>' . "\n";

		return $str;
	}

	function GetTOCScriptForPageId($RandomPageIndex)
	{
		if ($this->GetPageData($RandomPageIndex, 'credentials'))
			return '';

		$strUID = $this->GetPageData($RandomPageIndex, 'uid');

		if (!empty($strUID) && !empty($this->QuestionDefinition[$strUID]['Title']))
			$strQuestionTitle = $this->QuestionDefinition[$strUID]['Title'];
		else
		{
			// NB: this is not translated, but appears here for backward compatibility only.
			// The questions should have a title and if not the title can be set at any time.
			$strQuestionTitle = 'Question ' .  ($RandomPageIndex + 1);
		}

		return '{name: \''.str_replace("'", "\\'", $strQuestionTitle).'\', type: \''.$this->QuestionDefinition[$strUID]['Type'].'\', index: '.$RandomPageIndex.'}';
	}

	function GetTOCScriptForQuestionOrFolder(&$Theme)
	{
		$str = '';

		if ($Theme['type'] == 'folder' && !empty($this->Themes[$Theme['guid']]['title']))
		{
			$str .= '	{name: \''.str_replace("'", "\\'", $this->Themes[$Theme['guid']]['title']).'\', type: \'folder\', children: new Array(' . "\n";
		}

		if (!empty($Theme['children']))
		{
			$items = array();

			if (!empty($Theme['RandomizeLevel']) &&
					empty($Theme['RandomizeButKeepOrganised']))
			{
				$questionsAtThisLevel = array();
				$this->GetQuestionsForTheme($Theme, $questionsAtThisLevel);

				foreach ($_SESSION['QuizzSession']['PageMapping'] as $RandomPageId => $OriginalPageId)
				{
					if (!in_array($OriginalPageId, $questionsAtThisLevel))
						continue;

					$itemStr = $this->GetTOCScriptForPageId($RandomPageId);
					if (!empty($itemStr))
						$items[] = $itemStr;
				}

				$str .= implode(",\n", $items);
			}
			else if (empty($Theme['RandomizeLevel']))
			{
				foreach ($Theme['children'] as $aFolderOrQuestion)
				{
					if ($aFolderOrQuestion['type'] == 'question')
					{
						$RandomPageIndex = $this->TranslatePageNumber($aFolderOrQuestion['originalId']);

						if ($RandomPageIndex === false)
						{
							// the question is not taken in this quizz (cut out by a shunting question or randomization)
							continue;
						}

						$itemStr = $this->GetTOCScriptForPageId($RandomPageIndex);
						if (!empty($itemStr))
							$items[] = $itemStr;
					}

					if (!empty($aFolderOrQuestion['children']))
						$items[] = $this->GetTOCScriptForQuestionOrFolder($aFolderOrQuestion);
				}

				$str .= implode(",\n", $items);
			}
			else
			{
				$PageIds = array();
				$ChildrenFolders = array();
				$SortedChildrenFolders = array();

				foreach ($Theme['children'] as $aFolderOrQuestion)
				{
					if ($aFolderOrQuestion['type'] == 'question')
					{
						$RandomPageIndex = $this->TranslatePageNumber($aFolderOrQuestion['originalId']);

						if ($RandomPageIndex === false)
						{
							// the question is not taken in this quizz (cut out by a shunting question or randomization)
							continue;
						}

						$PageIds[] = $RandomPageIndex;
					}

					if (!empty($aFolderOrQuestion['children']))
						$ChildrenFolders[$aFolderOrQuestion['guid']] = $aFolderOrQuestion;
				}

				foreach ($_SESSION['QuizzSession']['PageMapping'] as $RandomPageId => $OriginalPageId)
				{
					$AncestorGuids = $this->GetParentGuids($RandomPageId);

					foreach ($ChildrenFolders as $guid => $aFolder)
					{
						if (in_array($guid, $AncestorGuids) && !isset($SortedChildrenFolders[$guid]))
						{
							$SortedChildrenFolders[$guid] = $aFolder;
							break;
						}
					}

					if (!in_array($RandomPageId, $PageIds))
						continue;

					$itemStr = $this->GetTOCScriptForPageId($RandomPageId);
					if (!empty($itemStr))
						$items[] = $itemStr;
				}

				foreach ($ChildrenFolders as $guid => $aFolder)
				{
					if (!isset($SortedChildrenFolders[$guid]))
						$SortedChildrenFolders[$guid] = $aFolder;
				}

				foreach ($SortedChildrenFolders as $aFolder)
					$items[] = $this->GetTOCScriptForQuestionOrFolder($aFolder);

				$str .= implode(",\n", $items);
			}
		}

		if ($Theme['type'] == 'folder' && !empty($this->Themes[$Theme['guid']]['title']))
			$str .= '	 )}';

		return $str;
	}
	/**
	 * returns a script tag with an array containing the TOC (similar to GetQuestionNavigator)
	 */
	function GetTOCScript()
	{
		$str = '';

		$str .= '<script type="text/javascript" language="JavaScript">' . "\n";

		$str .= 'var QuestionnaireTOC = new Array(' . "\n";

		$str .= $this->GetTOCScriptForQuestionOrFolder($this->RecursiveThemesAndQuestions);

		$str .= '	);' . "\n";

		$str .= '</script>' . "\n";

		return $str;
	}

	function HasEasyquizzProAPI()
	{
		if (!empty($_SERVER['InstancePath']))
			$ThisPath = $_SERVER['DOCUMENT_ROOT'] . 'includes/';
		else
			$ThisPath = str_replace('\\', '/', __FILE__);

		while (true)
		{
			$ThisPath = dirname($ThisPath);
			$EasyquizzProApi = $ThisPath . '/includes/interfaces/easyquizz_direct/EasyquizzProAPI.php';

			if (file_exists($EasyquizzProApi))
				return $EasyquizzProApi;

			if (empty($ThisPath) || $ThisPath == dirname($ThisPath))
				return false;
		}

		return false;
	}

	function CanShowNextButton()
	{
		if (empty($this->HideResultPage))
			return true;
		else
			return $this->GetCurrentPageIndex() < $this->GetPageCount() - 1;
	}

	/**
	 * Logs a heartbeat, and return the accurate elapsed
	 * time on the question or on the quizz
	 */
	function Pulse()
	{
		$_SESSION['QuizzSession']['GlobalSpentTime'] = time() - $_SESSION['QuizzSession']['GlobalLoadingTime'];

		$ElapsedTime = $this->GetCurrentPageData("Time") + time() - $this->GetCurrentPageData('LoadingTime');
		$this->SetCurrentPageData("Time", $ElapsedTime);

		$this->SetCurrentPageData("LoadingTime" , time());

		if ($this->IsGlobalTimer)
			$ElapsedTime = time() - $_SESSION['QuizzSession']['GlobalLoadingTime'];

		ob_start();

		$this->SendAICC(false);

		ob_end_clean();

		return $ElapsedTime;
	}
}

?>