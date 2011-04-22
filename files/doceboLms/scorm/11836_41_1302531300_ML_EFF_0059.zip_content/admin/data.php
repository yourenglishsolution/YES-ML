<?php 
$QuizzTitle = "Animal hybrids";
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
			"UID" => "V5C8Q",
			"QuestionTitle" => "Word of the day",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "0",
	
			"Type" => "EXPLANATION"

);


$QuestionDefinition[] = array(
			"UID" => "L2UC7",
			"QuestionTitle" => "Animal hybrids",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "0",
	
			"Type" => "EXPLANATION"

);


$QuestionDefinition[] = array(
			"UID" => "20ZJV",
			"QuestionTitle" => "Q1",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "1",
	
			"Question" => "<i>If a male poodle and a female labrador mate, their offspring would be called a...</i>",

			"Type" => "QCU",

			"Answers" => array("... poolador", "... labrapoodle", "... labradoodle."),
			"Correct_Answers" => array("false", "false", "true"),
			"Comments" => array("", "", ""),
			"Profiles" => array("", "", ""),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "STRJ5",
			"QuestionTitle" => "Q2",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "7",
	
			"Question" => "<i>Are the animals purebred (from the same species) a hybrid (from two or more species)?</i>",

			"Type" => "TABQCU",

			"Answers" => array("cockapoo", "zebra", "grolar", "zorse", "donkey", "jaguar", "cama"),
			"Columns" => array("purebred", "hybrid"),
			"Correct_Answers" => array(array("false", "true"),
								array("true", "false"),
								array("false", "true"),
								array("false", "true"),
								array("true", "false"),
								array("true", "false"),
								array("false", "true")),			

			"HasComments" => false,

			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "38WAI",
			"QuestionTitle" => "Q3",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "8",
	
			"Question" => "<i>Complete the sentences by writing the missing word in the blanks.</i><br><br><b>hinny</b><br><b>hump</b><br><b>tigon</b><br><b>ears</b><br><b>wolphin</b><br><b>mutt</b><br><b>liger</b><br><b>mule</b><br/>1. A [<span style=\"border-bottom: 1px black dashed\">mutt</span>] is a dog whose parentage includes more than one breed.<br><br>2. The offspring of a female donkey and a male horse is called a [<span style=\"border-bottom: 1px black dashed\">hinny</span>].<br><br>3. The [<span style=\"border-bottom: 1px black dashed\">mule</span>] is the offspring of a male donkey and a female horse.<br><br>4. The offspring of a male lion and a female tiger is called a [<span style=\"border-bottom: 1px black dashed\">liger</span>] and is much larger than either the tiger or lion.<br><br>5. The [<span style=\"border-bottom: 1px black dashed\">tigon</span>] is the offspring of a male tiger and female lion and is quite a bit smaller than either of its parents.<br><br>6. A [<span style=\"border-bottom: 1px black dashed\">wolphin</span>] is the offspring of a dolphin and a type of whale.<br><br>7. A Cama usually has the short [<span style=\"border-bottom: 1px black dashed\">ears</span>] and long tail of a camel, the cloven hooves of a llama but no [<span style=\"border-bottom: 1px black dashed\">hump</span>].",
			"Type" => "TAT",
			"Answers" => array("", "", "", "", "", "", "", ""),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "TC3YE",
			"QuestionTitle" => "Q4",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "false",
			"MaxScore" => "1",
	
			"Question" => "<i>Cama are the offspring from a camel and llama but in order to get this result, the female has to be...</i>",

			"Type" => "QCU",

			"Answers" => array("... large.", "... artificially inseminated.", "... very young."),
			"Correct_Answers" => array("false", "true", "false"),
			"Comments" => array("", "", ""),
			"Profiles" => array("", "", ""),
			"Notions" => array()

);


$QuestionDefinition[] = array(
			"UID" => "JLYES",
			"QuestionTitle" => "The end",
			"Theme" => "",
			"ThemeGUID" => "",
			"IsLastQuestionOfTheme" => "true",
			"MaxScore" => "0",
	
			"Type" => "EXPLANATION"

);



?>