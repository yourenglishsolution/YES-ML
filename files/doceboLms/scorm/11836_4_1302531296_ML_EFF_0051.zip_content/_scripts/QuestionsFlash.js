// ========================= Copyright Epistema 2002 ========================
//
// Flash.js
//
// Script for Flash questions
//
// ==========================================================================

var globalScore = 0;
var globalScoreMax = 0;
var globalSerializeData = '';

var GotScore = false;
var GotScoreMax = false;
var GotSerializeData = false;

var iTimerID = window.setTimeout("GetFlashScoreMax()", 500);

function GetFlashScoreMax()
{
	var theMovie = getQuestionFlash();

	if (theMovie && !GotScoreMax)
		CallFlashFunction("getScoreMax");

	if (!GotScoreMax)
		iTimerID = window.setTimeout("GetFlashScoreMax()", 500);
}

function getQuestionFlash()
{
  return window.document.FlashMovie;
}

function CallFlashFunction(strFunction)
{
	var theMovie = getQuestionFlash();

	if (!theMovie)
		return false;

	try
	{
		theMovie.SetVariable("ecouteur." + strFunction, true);
	}
	catch (e)
	{}

	return true;
}

function CallFlashFunctionWithParam(strFunction, strParam)
{
	var theMovie = getQuestionFlash();

	if (!theMovie)
		return false;

	try
	{
		theMovie.SetVariable(strFunction + "Param1", strParam);
		theMovie.SetVariable("ecouteur." + strFunction, true);
	}
	catch (e)
	{}

	return true;
}

// Callbacks :

function FlashMovie_DoFSCommand(command, args)
{
	if (command == "getScoreReturnValue")
	{
		globalScore = Number(args);
		GotScore = true;
	}
	else if (command == "getScoreMaxReturnValue")
	{
		globalScoreMax = Number(args);
		GotScoreMax = true;
	}
	else if (command == "showAnswersReturnValue")
	{
		if (document.getElementById("CorrectionPHP"))
			document.getElementById("CorrectionPHP").innerHTML = args;
		else if (document.getElementById("Correction"))
			document.getElementById("Correction").innerHTML = args;
	}
	else if (command == "serializeReturnValue")
	{
		globalSerializeData = args;
		GotSerializeData = true;
	}
}

// -----------------------------------------------------------------------
// Constructeur de Question_Flash
// Name : (chaine) le nom du quizz
// FormQ : (Formulaire) le formulaire des questions
// -----------------------------------------------------------------------
function Question_Flash(Name, FormQ)
{
	this.name = Name;
	this.formQ = FormQ;

	this.canSubmit = function()
	{
		// set the hidden inputs with the flash data
		var Flash_Data =			document.getElementById('Flash_DataId');
		if (Flash_Data)				Flash_Data.value = this.Serialize();
		var Flash_Score = 		document.getElementById('Flash_ScoreId');
		if (Flash_Score)			Flash_Score.value = this.GetScore();
		var Flash_ScoreMax =	document.getElementById('Flash_ScoreMaxId');
		if (Flash_ScoreMax)		Flash_ScoreMax.value = this.GetScoreMax();

		// Check that the min and max answers are checked
		if (!this.DontAllowSubmitIfEmpty)
			return true;

		// not implemented

		return true;
	};

	// -----------------------------------------------------------------------
	// Renvoie le score
	// -----------------------------------------------------------------------
	this.GetScore = function()
	{
		if( this.enable	== false)
			return this.DisabledScore;

		return Number(globalScore);
	};

	this.GetScoreMax = function()
	{
		return Number(globalScoreMax);
	};


	// -----------------------------------------------------------------------
	// Affiche les bonnes réponses du quizz
	// -----------------------------------------------------------------------
	this.ShowAnswers = function()
	{
		// disable quizz (save the current score)
		this.Disable();

		CallFlashFunction("showAnswers");
	};

	// -----------------------------------------------------------------------
	// Serialize a form
	// word1,word2,word3,,lastword
	// -----------------------------------------------------------------------
	this.Serialize = function()
	{
		return globalSerializeData;
	};

	this.Deserialize = function(str)
	{
		return CallFlashFunctionWithParam("deserialize", str);
	};

	this.Disable = function()
	{
		if( this.enable	== false)
			return;

		this.DisabledScore = this.GetScore();

		this.DisableForm();
	};

	this.DisableForm = function()
	{
		return CallFlashFunction("disable");
	};
}

Question_Flash.prototype = new Question;
