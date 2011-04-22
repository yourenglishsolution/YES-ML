<?php 
$QuizzTitle = "Noun clauses";
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
			"UID" => "77SZ5",
			"QuestionTitle" => "Word of day",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "0",
	
			"Type" => "EXPLANATION"

);


$QuestionDefinition[] = array(
			"UID" => "0AAEZ",
			"QuestionTitle" => "Noun clauses",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "0",
	
			"Type" => "EXPLANATION"

);


$QuestionDefinition[] = array(
			"UID" => "742QC",
			"QuestionTitle" => "Q1",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "1",
	
			"Question" => "<i>Choose the best word to complete the following sentence. (Hint:  You are looking for the correct word to start the noun clause.)</i><br><br>Now she understands ____ must be done this week.",

			"Type" => "QCU",

			"Answers" => array("what", "that", "who"),
			"Correct_Answers" => array("true", "false", "false"),
			"Comments" => array("", "", ""),
			"Profiles" => array("", "", ""),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "K0OK0",
			"QuestionTitle" => "Q2",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "1",
	
			"Question" => "<i>Choose the best word to complete the following sentence.</i><br><br>____ you are looking at now is a painting by Monet.",

			"Type" => "QCU",

			"Answers" => array("That", "Who", "What"),
			"Correct_Answers" => array("false", "false", "true"),
			"Comments" => array("", "", ""),
			"Profiles" => array("", "", ""),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "5A6AV",
			"QuestionTitle" => "Q3",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "1",
	
			"Question" => "<i>Choose the best word to complete the following sentence.</i><br><br>Jake is the one ____ is working on the project in Salt Lake City.",

			"Type" => "QCU",

			"Answers" => array("that", "which", "who"),
			"Correct_Answers" => array("false", "false", "true"),
			"Comments" => array("", "", ""),
			"Profiles" => array("", "", ""),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "RE41V",
			"QuestionTitle" => "Q4",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "1",
	
			"Question" => "<i>Choose the best word to complete the following sentence.</i><br><br>Can you tell me ____ you know about the historical significance of this building?",

			"Type" => "QCU",

			"Answers" => array("that", "what", "which"),
			"Correct_Answers" => array("false", "true", "false"),
			"Comments" => array("", "", ""),
			"Profiles" => array("", "", ""),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "P2BSA",
			"QuestionTitle" => "Q5",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "5",
	
			"Question" => "<i>Are the words in bold a complete noun clause?</i>",

			"Type" => "TABQCU",

			"Answers" => array("I agree <b>that the teacher</b> was a little mean today.", "<b>What is</b> that noise?", "Do you know <b>which restaurant they are going to for dinner</b>?", "I reminded her <b>that she had a dentist appointment today at noon</b>.", "<b>That Jake is jealous of Billy</b> is obvious to everyone."),
			"Columns" => array("complete noun clause", "not a noun clause"),
			"Correct_Answers" => array(array("false", "true"),
								array("false", "true"),
								array("true", "false"),
								array("true", "false"),
								array("true", "false")),			

			"HasComments" => false,

			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "DI4BG",
			"QuestionTitle" => "The end",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "true",
			"MaxScore" => "0",
	
			"Type" => "EXPLANATION"

);



?>