<?php 
$QuizzTitle = "Word power";
$Passwd = "";

$LangFile = "en.js";
$Encoding = "UTF-8";


$LogoImage = "";


$CopyrightText = "Epistema LMS";
$CopyrightURL = "";

$IsAdaptivePath = false;				

$ScoreComments = array();

$ScoreComments[0]["comment"] = "Oh no!  Surely you'll do better next week!";
$ScoreComments[0]["from"] = 0;
$ScoreComments[0]["to"] = 50;

$ScoreComments[1]["comment"] = "Average - Not too bad, but I'm sure with a little more effort you can do better.";
$ScoreComments[1]["from"] = 50;
$ScoreComments[1]["to"] = 65;

$ScoreComments[2]["comment"] = "Above average - Good job!  Keep up the good work.";
$ScoreComments[2]["from"] = 65;
$ScoreComments[2]["to"] = 85;

$ScoreComments[3]["comment"] = "Wow - What a fantastic job!  I'm really impressed.";
$ScoreComments[3]["from"] = 85;
$ScoreComments[3]["to"] = 100;


$Themes = array();



$Profiles = array();



$QuestionDefinition[] = array(
			"UID" => "LPC0G",
			"QuestionTitle" => "Word of the day",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "0",
	
			"Type" => "EXPLANATION"

);


$QuestionDefinition[] = array(
			"UID" => "JPL24",
			"QuestionTitle" => "Word power",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "0",
	
			"Type" => "EXPLANATION"

);


$QuestionDefinition[] = array(
			"UID" => "33MDA",
			"QuestionTitle" => "Q1",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "4",
	
			"Question" => "<i>Match the words with the correct definitions.</i>",
			"Type" => "MATCH",
			"Answers" => array("patent", "posterity", "newfangled", "feasible"),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "FBP58",
			"QuestionTitle" => "Q2",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "4",
	
			"Question" => "<i>Complete the sentence by choosing the best word to fill in the blank.</i>",
			"Type" => "MATCH",
			"Answers" => array("The university gave the scientist ____ to continue the experiment.", "The team will ____ the possible outcomes of the invention process.", "Hank doesn't have all the materials he needs to build the model so he will ____ and use the materials he has.", "In order for us to ____ that our invention will work, we should run some tests."),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "EP18R",
			"QuestionTitle" => "Q3",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "1",
	
			"Question" => "<i>A ____ is the first written copy of some idea.  Usually this will have to be written again.</i>",

			"Type" => "QCU",

			"Answers" => array("draft", "memo", "email"),
			"Correct_Answers" => array("true", "false", "false"),
			"Comments" => array("", "", ""),
			"Profiles" => array("", "", ""),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "S5PP1",
			"QuestionTitle" => "Q4",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "1",
	
			"Question" => "<i>A fortunate accident that produces a valuable or pleasing result that wasn't originally looked for is called...</i><br>(Hint: This is a good surprise, something totally unexpected which proves to be very good.)",

			"Type" => "QCU",

			"Answers" => array("shocking.", "serendipity.", "hypothesis."),
			"Correct_Answers" => array("false", "true", "false"),
			"Comments" => array("", "", ""),
			"Profiles" => array("", "", ""),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "CWFM3",
			"QuestionTitle" => "Q5",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "5",
	
			"Question" => "<i>Write a word from the list below in the blank by the correct definition. (Note: These words might have different meanings, but for now, think of them in a scientific way.)</i><br><br><b>compare</b><br><b>enumerate</b><br><b>evaluate</b><br><b>interpret</b><br><b>diagram</b><br/>1. a picture, a graph or a type of plan on paper  [<span style=\"border-bottom: 1px black dashed\">diagram</span>]<br><br>2. looking at the differences or similarities between two or more things  [<span style=\"border-bottom: 1px black dashed\">compare</span>]<br><br>3. to number a list of things  [<span style=\"border-bottom: 1px black dashed\">enumerate</span>]<br><br>4. to understand something in a particular way [<span style=\"border-bottom: 1px black dashed\">interpret</span>]<br><br>5. to determine the significance of something  [<span style=\"border-bottom: 1px black dashed\">evaluate</span>]",
			"Type" => "TAT",
			"Answers" => array("", "", "", "", ""),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "47G1R",
			"QuestionTitle" => "The end",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "true",
			"MaxScore" => "0",
	
			"Type" => "EXPLANATION"

);



?>