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
		

		$this->Pages[$PageId]['uid'] = "V5C8Q";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 0;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "L2UC7";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 0;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "20ZJV";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 1;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "STRJ5";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 7;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "38WAI";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 8;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "TC3YE";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 1;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "JLYES";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 0;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->QuestionDefinition = array();

		$this->QuestionDefinition["V5C8Q"] = array(
			"Title" => "Word of the day",	
			"Type" => "EXPLANATION");
		$this->QuestionDefinition["L2UC7"] = array(
			"Title" => "Animal hybrids",	
			"Type" => "EXPLANATION");
		$this->QuestionDefinition["20ZJV"] = array(
			"Title" => "Q1",
			"Type" => "QCU");
		$this->QuestionDefinition["STRJ5"] = array(
			"Title" => "Q2",
			"Type" => "TABQCU");
		$this->QuestionDefinition["38WAI"] = array(
			"Title" => "Q3",	
			"Type" => "TAT");
		$this->QuestionDefinition["TC3YE"] = array(
			"Title" => "Q4",
			"Type" => "QCU");
		$this->QuestionDefinition["JLYES"] = array(
			"Title" => "The end",	
			"Type" => "EXPLANATION");



		$this->GenerationDate = "01.04.2011 17:54:48";

			
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
		
		$this->QuizzTitle = "Animal hybrids";

		
		
		$PageId = 0;
		
		$this->RecursiveThemesAndQuestions = array(
					'type' => 'folder',
					'guid' => false,
						'RandomizeLevel' => false,'RandomizeButKeepOrganised' => false,
					'RandomizeCount' => "7",
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