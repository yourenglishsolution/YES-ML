<?php 

$GLOBALS['PageString'] = '<span epiLang="PageString"></span>';
$GLOBALS['ScoreString'] = '<span epiLang="ScoreString"></span>';
$GLOBALS['TimeString'] = '<span epiLang="TimeString"></span>';
$GLOBALS['TotalScoreString'] = '<span epiLang="TotalScoreString"></span>';
$GLOBALS['TotalTimeString'] = '<span epiLang="TotalTimeString"></span>';
$GLOBALS['QuestionString'] = '<span epiLang="QuestionString"></span>';
$GLOBALS['TotalString'] = '<span epiLang="TotalString"></span>';
$GLOBALS['SubTotalString'] = '<span epiLang="SubTotalString"></span>';
$GLOBALS['ErrorWritingUserdata'] = '<span epiLang="ErrorWritingUserdata"></span>';
$GLOBALS['minsString'] = '<span epiLang="min"></span>';
$GLOBALS['secsString'] = '<span epiLang="sec"></span>';

$GLOBALS['LangDir'] = 'ltr';

include_once("_ressources/inc.manager.php");

class QuizzManager extends QuizzManagerBase
{
	function QuizzManager()
	{
		$this->QuizzManagerBase();
			
		// Constructor
		$PageId = 0;
		

		$this->Pages[$PageId]['uid'] = "QWAMF";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 0;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "UGXTQ";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 0;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "UIGG0";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 4;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "WHDZU";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 4;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "EM1VK";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 4;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "MZ3NY";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 0;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->QuestionDefinition = array();

		$this->QuestionDefinition["QWAMF"] = array(
			"Title" => "Word of the day",	
			"Type" => "EXPLANATION");
		$this->QuestionDefinition["UGXTQ"] = array(
			"Title" => "Expressions",	
			"Type" => "EXPLANATION");
		$this->QuestionDefinition["UIGG0"] = array(
			"Title" => "Q1",	
			"Type" => "MATCH");
		$this->QuestionDefinition["WHDZU"] = array(
			"Title" => "Q2",	
			"Type" => "MATCH");
		$this->QuestionDefinition["EM1VK"] = array(
			"Title" => "Q3",	
			"Type" => "TAT");
		$this->QuestionDefinition["MZ3NY"] = array(
			"Title" => "The end",	
			"Type" => "EXPLANATION");



		$this->GenerationDate = "01.04.2011 17:53:21";

			
		$this->AllowBack = true;
			
		$this->ShowCorrection = true;
			
		$this->isSurvey = true;
			
		$this->nominativerepports = false;
			
		$this->QuizzNeedPasswd = false;
		$this->QuizzCorrectPasswd = "";
			
		$this->NotifyByEmail = false;
		$this->NotifyRecipient = "";
		$this->NotifySubject = "";
		$this->NotifyMessage = "";
		$this->HideResultPage = false;
		$this->UseHeartBeat = false;
		$this->IsAdaptivePath = false;				
		$this->AdaptivePathQuestionsPerStage = "3";
		$this->AdaptivePathStagesPerQuestionnaire = "5";
		$this->AdaptivePathFirstThemeGuid = "";
		$this->AdaptiveQuestionnaireBalancedPicking = false;				
		
		$this->QuizzTitle = "Expressions";

		
		
		$PageId = 0;
		
		$this->RecursiveThemesAndQuestions = array(
					'type' => 'folder',
					'guid' => false,
						'RandomizeLevel' => false,'RandomizeButKeepOrganised' => false,
					'RandomizeCount' => "6",
					'ShowSubtotal' => false,
					'children' => array(
			array('type' => 'question',
						'originalId' => $PageId++,
						'credentials' => false,
						'children' => array()),
		
			array('type' => 'question',
						'originalId' => $PageId++,
						'credentials' => false,
						'children' => array()),
		
			array('type' => 'question',
						'originalId' => $PageId++,
						'credentials' => false,
						'children' => array()),
		
			array('type' => 'question',
						'originalId' => $PageId++,
						'credentials' => false,
						'children' => array()),
		
			array('type' => 'question',
						'originalId' => $PageId++,
						'credentials' => false,
						'children' => array()),
		
			array('type' => 'question',
						'originalId' => $PageId++,
						'credentials' => false,
						'children' => array())));

		
		
		$this->IsGlobalTimer = false;
		$this->GlobalTimeMax = 0;
		$this->NoTimer = false;
		
			$this->ShowIntroductionPage = true; // introduction
		 
		$this->ShowAnswerAfterQuestion = 'incorrectionmodeonly';

		$this->LoadPersistentData();
		
		if (false) // debug
		{
			unset($_SESSION['QuizzSession']['PageMapping']);
			$this->InitPageMapping();
			echo '<pre>';
			foreach ($_SESSION['QuizzSession']['PageMapping'] as $newId)
				echo $newId . ' - ' . (isset($this->Pages[$newId]['ThemeGuid']) ? $this->Pages[$newId]['ThemeGuid'] : 'root') . '<br>';

			echo '</pre><br/>';
		}		
	}
};

	
$GLOBALS['QuizzManager'] = new QuizzManager();

if (!empty($_POST['HeartBeat']))
{
	// Log a heartbeat pulsation
	$ElapsedTime = $GLOBALS['QuizzManager']->Pulse();

	$output = '{"Return": "Acknowledged", "ElapsedTime": '.$ElapsedTime.'}';

	header("X-JSON: ($output)");

	exit();
}

if (isset($_GET['PageNumber']))
{
	$CurrentPage = $GLOBALS['QuizzManager']->TranslatePageNumber($_GET['PageNumber']);

	// If PreviousPageNumber is not the expected page number from the session,
	// the user has probably pressed backspace (booo). We do nothing.
	if ($CurrentPage == $GLOBALS['QuizzManager']->GetCurrentPageIndex())
	{
		$GLOBALS['QuizzManager']->UninitPage($_GET);
		$GLOBALS['QuizzManager']->MoveToNextPage($_GET);
	}
}

if (empty($GLOBALS['NoDisplay']))
	$GLOBALS['QuizzManager']->displayPage();

?>