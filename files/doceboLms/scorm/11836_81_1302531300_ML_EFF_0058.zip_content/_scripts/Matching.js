// ========================= Copyright Epistema 2002 ========================
//
// Matching.js
//
// Script for matching type questions
//
//
// ==========================================================================

// -----------------------------------------------------------------------
// Constructeur de Question_Matching
// Name : (chaine) le nom du quizz
// FormQ : (Formulaire) le formulaire des questions
// -----------------------------------------------------------------------
function Question_Matching(Name, FormQ, Scoring)
{
	this.name = Name;
	this.formQ = FormQ;
	this.scoring = Scoring;

	this.RightAnswer = new Array();

	this.canSubmit = function()
	{
		// Check that the min and max answers are checked
		if (!this.DontAllowSubmitIfEmpty)
			return true;

		var allSelects = this.GetInputs();

		for(var i = 0; i < allSelects.length; i++)
		{
			if (allSelects[i].selectedIndex == 0)
			{
				alert(EpiLangJS.YouMustAnswerTheQuestion);
				return false;
			}
		}

		return true;
	};


	// -----------------------------------------------------------------------
	// Renvoie le score
	// -----------------------------------------------------------------------
	this.GetScore = function()
	{
		if( this.enable	== false)
			return this.DisabledScore;

		var allSelects = this.GetInputs();

		var score = 0;
		var maxScore = this.RightAnswer.length;

		if (maxScore == 0)
			return 0;

		var bQuestionIsAnswered = false;

		for(var i = 0; i < allSelects.length; i++)
		{
			if (allSelects[i].selectedIndex == 0)
				continue;

			bQuestionIsAnswered = true;

			var word = allSelects[i].options[allSelects[i].selectedIndex].text.toLowerCase();
			word = word.replace(/^[\s]+/g,'').replace(/[\s]+$/g, '');

			var rightword = this.RightAnswer[i].toLowerCase();
			rightword = rightword.replace(/^[\s]+/g,'').replace(/[\s]+$/g, '');

			if (word == rightword)
				score++;
			else
				score--;
		}

		if (!bQuestionIsAnswered)
			return 0;

		if (this.scoring == "AllRight")
			return (score == maxScore) ? this.GetScoreMax() : -1 * this.GetScoreMax();
		else
			return (score * this.GetScoreMax()) / maxScore;
	};

	// -----------------------------------------------------------------------
	// Display the correct answers
	// -----------------------------------------------------------------------
	this.ShowAnswers = function()
	{
		// disable quizz (save the current score)
		this.Disable();

		if (document.getElementById && document.getElementById("Correction"))
		{
			document.getElementById("Correction").innerHTML = this.AnswerText;
		}
		else
		{
			var allSelects = this.GetInputs();

			for(var i = 0; i < allSelects.length; i++)
				if (allSelects[i].name != 'NavigationSelect')
					allSelects[i].options[allSelects[i].selectedIndex].text = this.RightAnswer[i];
		}
	};

	// -----------------------------------------------------------------------
	// Serialize a form
	// word1,word2,word3,,lastword
	// -----------------------------------------------------------------------
	this.Serialize = function()
	{
		var str = "";

		var allSelects = this.GetInputs();

		if(this.RightAnswer.length > 0)
			str += (allSelects[0].selectedIndex);

		for(var i = 1; i < allSelects.length; i++)
			str += ("," + allSelects[i].selectedIndex );

		return str;
	};

	this.Deserialize = function(str)
	{
		var storedQuestion = String(str);
		var indexArray = storedQuestion.split(",");

		if (this.RightAnswer.length != indexArray.length) return;

		var allSelects = this.GetInputs();

		for(var i = 0; i < allSelects.length; i++)
			allSelects[i].selectedIndex = indexArray[i];
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
		this.enable = false;

		var allInputs = this.GetInputs();

		for(var i = 0; i < allInputs.length; i++)
			this.disableObject(allInputs[i]);
	};


	this.GetInputs = function()
	{
		var allInputs = new Array();
		var j = 0;

		var anyInputs = this.formQ.getElementsByTagName("SELECT");

		for(var i = 0; i < anyInputs.length; i++)
			if (anyInputs[i].name != 'NavigationSelect')
				allInputs[j++] = anyInputs[i];

		return allInputs;
	}
}

Question_Matching.prototype = new Question;
