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
		

		$this->Pages[$PageId]['uid'] = "LPC0G";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 0;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "JPL24";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 0;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "33MDA";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 4;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "FBP58";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 4;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "EP18R";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 1;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "S5PP1";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 1;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "CWFM3";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 5;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->Pages[$PageId]['uid'] = "47G1R";
		$this->Pages[$PageId]['filename'] = 'Page'.($PageId+1).'.php';
		$this->Pages[$PageId]['MaxScore'] = 0;
		$this->Pages[$PageId]['TimeMax'] = 0;
		$this->Pages[$PageId]['MaxTries'] = 0;
		
		$this->Pages[$PageId]['credentials'] = false;
		$this->Pages[$PageId]['Theme'] = explode('|', "");
		
		$PageId++;
	 

		$this->QuestionDefinition = array();

		$this->QuestionDefinition["LPC0G"] = array(
			"Title" => "Word of the day",	
			"Type" => "EXPLANATION");
		$this->QuestionDefinition["JPL24"] = array(
			"Title" => "Word power",	
			"Type" => "EXPLANATION");
		$this->QuestionDefinition["33MDA"] = array(
			"Title" => "Q1",	
			"Type" => "MATCH");
		$this->QuestionDefinition["FBP58"] = array(
			"Title" => "Q2",	
			"Type" => "MATCH");
		$this->QuestionDefinition["EP18R"] = array(
			"Title" => "Q3",
			"Type" => "QCU");
		$this->QuestionDefinition["S5PP1"] = array(
			"Title" => "Q4",
			"Type" => "QCU");
		$this->QuestionDefinition["CWFM3"] = array(
			"Title" => "Q5",	
			"Type" => "TAT");
		$this->QuestionDefinition["47G1R"] = array(
			"Title" => "The end",	
			"Type" => "EXPLANATION");



		$this->GenerationDate = "01.04.2011 17:52:52";

			
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
		
		$this->QuizzTitle = "Word power";

		
		$this->ScoreComments[0]['comment'] = "Oh no!  Surely you'll do better next week!";
		$this->ScoreComments[0]['from'] = 0;
		$this->ScoreComments[0]['to'] = 50;
		
		$this->ScoreComments[1]['comment'] = "Average - Not too bad, but I'm sure with a little more effort you can do better.";
		$this->ScoreComments[1]['from'] = 50;
		$this->ScoreComments[1]['to'] = 65;
		
		$this->ScoreComments[2]['comment'] = "Above average - Good job!  Keep up the good work.";
		$this->ScoreComments[2]['from'] = 65;
		$this->ScoreComments[2]['to'] = 85;
		
		$this->ScoreComments[3]['comment'] = "Wow - What a fantastic job!  I'm really impressed.";
		$this->ScoreComments[3]['from'] = 85;
		$this->ScoreComments[3]['to'] = 100;
		
		
		$PageId = 0;
		
		$this->RecursiveThemesAndQuestions = array(
					'type' => 'folder',
					'guid' => false,
						'RandomizeLevel' => false,'RandomizeButKeepOrganised' => false,
					'RandomizeCount' => "8",
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