<?php 
$QuizzTitle = "Expressions";
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
			"UID" => "QWAMF",
			"QuestionTitle" => "Word of the day",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "0",
	
			"Type" => "EXPLANATION"

);


$QuestionDefinition[] = array(
			"UID" => "UGXTQ",
			"QuestionTitle" => "Expressions",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "0",
	
			"Type" => "EXPLANATION"

);


$QuestionDefinition[] = array(
			"UID" => "UIGG0",
			"QuestionTitle" => "Q1",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "4",
	
			"Question" => "<i>Complete the expressions with the missing words.</i>",
			"Type" => "MATCH",
			"Answers" => array("Sunday ____", "____ home", "____ driver", "____ by the seat of one's pants"),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "WHDZU",
			"QuestionTitle" => "Q2",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "4",
	
			"Question" => "<i>Match the expression with the sentence it could be used with or could replace.</i>",
			"Type" => "MATCH",
			"Answers" => array("go fly a kite", "ride the tide", "drive home", "pigs might fly"),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "EM1VK",
			"QuestionTitle" => "Q3",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "4",
	
			"Question" => "<i>The following sentences use the expressions you just learned but a word is missing.  Write the word in the blank.</i><br/>1. Ralph always tells people how to do things.  He is a [<span style=\"border-bottom: 1px black dashed\">backseat</span>] driver.<br><br>2. My husband is a terrible [<span style=\"border-bottom: 1px black dashed\">Sunday</span>] driver.  He drives so slowly!<br><br>3. Jill always sounds like she just fell off the [<span style=\"border-bottom: 1px black dashed\">turnip</span>] truck.<br><br>4. We really don't have any plans. We'll probably just fly by the [<span style=\"border-bottom: 1px black dashed\">seat</span>] of our pants.",
			"Type" => "TAT",
			"Answers" => array("", "", "", ""),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "MZ3NY",
			"QuestionTitle" => "The end",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "true",
			"MaxScore" => "0",
	
			"Type" => "EXPLANATION"

);



?>