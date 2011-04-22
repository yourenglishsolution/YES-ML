// ========================= Copyright Epistema 2002 ========================
//
// DragDrop.js
//
// Script for drag and drop type questions
//
//
// ==========================================================================

// -----------------------------------------------------------------------
// Constructeur de Question_DragDrop
// Name : (chaine) le nom du quizz
// FormQ : (Formulaire) le formulaire des questions
// -----------------------------------------------------------------------
function Question_DragDrop(Name, FormQ, Scoring)
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

		for(var i = 0; i < this.RightAnswer.length; i++)
		{
			if (document.getElementById('DDId'+(i+1)).value == '')
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

		var score = 0;
		var maxScore = this.RightAnswer.length;

		if (maxScore == 0)
			return 0;

		var bQuestionIsAnswered = false;
		var allInputs = this.GetInputs();

		for(var i = 0; i < allInputs.length; i++)
		{
			if (allInputs[i].value.length == 0)
				continue;

			bQuestionIsAnswered = true;

			if (allInputs[i].value == this.RightAnswer[i])
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
	// Affiche les bonnes résponses du quizz
	// -----------------------------------------------------------------------
	this.ShowAnswers = function()
	{
		// disable quizz (save the current score)
		this.Disable();

		if (document.getElementById && document.getElementById("Correction"))
			document.getElementById("Correction").innerHTML = this.AnswerText;
	};

	// -----------------------------------------------------------------------
	// Serialize a form
	// word1,word2,word3,lastword
	// -----------------------------------------------------------------------
	this.Serialize = function()
	{
		var str = "";
		var allInputs = this.GetInputs();

		if(allInputs.length > 0)
			str += allInputs[0].value;

		for(var i = 1; i < allInputs.length; i++)
			str += "," + allInputs[i].value;

		return str;
	};

	this.Deserialize = function(str)
	{
		var storedQuestion = String(str);
		var indexArray = storedQuestion.split(",");
		var allInputs = this.GetInputs();

		if (this.RightAnswer.length != indexArray.length) return;

		for (var i=0; i < indexArray.length; i++)
			allInputs[i].value = indexArray[i];
	};

	this.Disable = function()
	{
		if (this.enable	== false)
			return;

		this.DisabledScore = this.GetScore();

		this.DisableForm();
	};

	this.DisableForm = function()
	{
		this.enable = false;

		for (var d_i = 0; d_i < dd.elements.length; d_i++)
		{
			var d_o = dd.elements[d_i];
			d_o.setDraggable(false);
		}
	};

	this.GetInputs = function()
	{
		var allInputs = new Array();
		var j = 0;

		var anyInputs = this.formQ.getElementsByTagName("INPUT");

		for(var i = 0; i < anyInputs.length; i++)
			if (anyInputs[i].name.substr(0, 2) == 'DD')
				allInputs[j++] = anyInputs[i];

		return allInputs;
	}

	// -----------------------------------------------------------------------
	// Reset le formulaire
	// -----------------------------------------------------------------------
	this.Reset = function()
	{
		if( this.enable	== false)
			return;

		var allInputs = this.GetInputs();

		for(var i = 0; i < allInputs.length; i++)
			allInputs[i].value = '';

		var theBasket = $('Basket');
		$$('.draggableTable').each(function(aTable){
				theBasket.appendChild(aTable);
			});

		this.formQ.reset();
	};
}

Question_DragDrop.prototype = new Question;
