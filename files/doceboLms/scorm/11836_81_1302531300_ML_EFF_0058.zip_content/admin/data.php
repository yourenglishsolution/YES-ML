<?php 
$QuizzTitle = "Review";
$Passwd = "";

$LangFile = "en.js";
$Encoding = "UTF-8";


$LogoImage = "";


$CopyrightText = "Epistema LMS";
$CopyrightURL = "";

$IsAdaptivePath = false;				

$ScoreComments = array();


$Themes = array();



$Profiles = array();



$QuestionDefinition[] = array(
			"UID" => "PSLTJ",
			"QuestionTitle" => "Word of the day",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "0",
	
			"Type" => "EXPLANATION"

);


$QuestionDefinition[] = array(
			"UID" => "8SHDS",
			"QuestionTitle" => "Review",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "0",
	
			"Type" => "EXPLANATION"

);


$QuestionDefinition[] = array(
			"UID" => "Z6VBK",
			"QuestionTitle" => "Q1",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "2",
	
			"Question" => "<i>Which sentences use the word 'indulge' correctly?</i>",

			"Type" => "QCM",

			"Answers" => array("The tired girl indulged her cravings and bought some chocolate.", "We will indulge the dinner.", "Tom indulged his wife's wishes and took her on vacation.", "The indulged treat was too rich for the old man.", "We will indulge the business deal this afternoon."),
			"Correct_Answers" => array("true", "false", "true", "false", "false"),
			"Comments" => array("", "", "", "", ""),
			"Profiles" => array("", "", "", "", ""),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "VW56N",
			"QuestionTitle" => "Q2",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "4",
	
			"Question" => "<i>Choose as many answers as apply.</i><br><br>Columbus day is celebrated in...",

			"Type" => "QCM",

			"Answers" => array("... Spain.", "... North America.", "... South America.", "... Alaska.", "... Australia."),
			"Correct_Answers" => array("true", "true", "true", "true", "false"),
			"Comments" => array("", "", "", "", ""),
			"Profiles" => array("", "", "", "", ""),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "JBGOL",
			"QuestionTitle" => "Q3",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "1",
	
			"Question" => "<i>Christopher Columbus landed on the Bahamas in...</i>",

			"Type" => "QCU",

			"Answers" => array("... 1490.", "... 1492.", "... 1494."),
			"Correct_Answers" => array("false", "true", "false"),
			"Comments" => array("", "", ""),
			"Profiles" => array("", "", ""),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "JA3IR",
			"QuestionTitle" => "Q4",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "3",
	
			"Question" => "<i>Choose the best answer.</i><br><i></i>",
			"Type" => "MATCH",
			"Answers" => array("patent", "draft", "serendipity"),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "JBIOJ",
			"QuestionTitle" => "The end",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "true",
			"MaxScore" => "0",
	
			"Type" => "EXPLANATION"

);



?>