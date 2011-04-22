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
			"UID" => "LA2WO",
			"QuestionTitle" => "Word of the day",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "0",
	
			"Type" => "EXPLANATION"

);


$QuestionDefinition[] = array(
			"UID" => "NXBKK",
			"QuestionTitle" => "Review",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "0",
	
			"Type" => "EXPLANATION"

);


$QuestionDefinition[] = array(
			"UID" => "NEH3G",
			"QuestionTitle" => "Q1",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "4",
	
			"Question" => "<i>Complete the expressions with the missing words.</i>",
			"Type" => "MATCH",
			"Answers" => array("talk is ____", "talk out the back of your ____", "all talk and no ____", "talking to a brick ____"),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "DU6Q0",
			"QuestionTitle" => "Q2",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "3",
	
			"Question" => "<i>Match the word to the correct definition.</i>",
			"Type" => "MATCH",
			"Answers" => array("haste", "indeed", "simmer"),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "GT8OP",
			"QuestionTitle" => "Q3",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "4",
	
			"Question" => "<i>Match the expressions with the correct definitions.</i>",
			"Type" => "MATCH",
			"Answers" => array("talk shop", "pep talk", "talk turkey", "talk of the town"),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "CDYXF",
			"QuestionTitle" => "Q4",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "5",
	
			"Question" => "<i>Are the statements true or false?</i>",

			"Type" => "TABQCU",

			"Answers" => array("George Washington was the second president of the United States.", "George Washington was in the military.", "George Washington was born in Washington.", "North Carolina's state slogan is the 'First in Flight'.", "California has the largest land-locked harbor."),
			"Columns" => array("true", "false"),
			"Correct_Answers" => array(array("false", "true"),
								array("true", "false"),
								array("false", "true"),
								array("true", "false"),
								array("true", "false")),			

			"HasComments" => false,

			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "3RTBS",
			"QuestionTitle" => "The end",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "true",
			"MaxScore" => "0",
	
			"Type" => "EXPLANATION"

);



?>