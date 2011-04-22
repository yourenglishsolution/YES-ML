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
		

		$this->Pages[$PageId]['uid'] = "60L64";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 0;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "4FFLW";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 0;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "YRN0C";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 6;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "K6IIP";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 0;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "XSGRL";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 1;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "K6OUB";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 1;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "MGV1U";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 1;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "V01DR";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 1;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "AS1YI";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 1;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "W1CZ7";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 0;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->QuestionDefinition = array();

		$this->QuestionDefinition["60L64"] = array(
			"Title" => "Word of the day",	
			"Type" => "EXPLANATION");
		$this->QuestionDefinition["4FFLW"] = array(
			"Title" => "Adverb clauses",	
			"Type" => "EXPLANATION");
		$this->QuestionDefinition["YRN0C"] = array(
			"Title" => "Q1",	
			"Type" => "TAT");
		$this->QuestionDefinition["K6IIP"] = array(
			"Title" => "Instructions",	
			"Type" => "EXPLANATION");
		$this->QuestionDefinition["XSGRL"] = array(
			"Title" => "Q2",
			"Type" => "QCU");
		$this->QuestionDefinition["K6OUB"] = array(
			"Title" => "Q3",
			"Type" => "QCU");
		$this->QuestionDefinition["MGV1U"] = array(
			"Title" => "Q4",
			"Type" => "QCU");
		$this->QuestionDefinition["V01DR"] = array(
			"Title" => "Q5",
			"Type" => "QCU");
		$this->QuestionDefinition["AS1YI"] = array(
			"Title" => "Q6",
			"Type" => "QCU");
		$this->QuestionDefinition["W1CZ7"] = array(
			"Title" => "The end",	
			"Type" => "EXPLANATION");



		$this->GenerationDate = "01.04.2011 17:51:25";

			
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
		
		$this->QuizzTitle = "Adverb clauses";

		
		
		$PageId = 0;
		
		$this->RecursiveThemesAndQuestions = array(
					'type' => 'folder',
					'guid' => false,
						'RandomizeLevel' => false,'RandomizeButKeepOrganised' => false,
					'RandomizeCount' => "10",
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