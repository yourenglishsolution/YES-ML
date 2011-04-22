// ========================= Copyright Epistema 2002 ========================
//
// TexteATrous.js
//
// Script for TAT type questions
//
// ==========================================================================

function TexteATrous(Name, FormQ, NumbOfMissW, AnswersAsList)
{
	this.name = Name;
	this.formQ = FormQ;
	this.numbOfMissW = NumbOfMissW;

	this.RightAnswer = new Array(); // of arrays
	this.FalseAnswer = new Array();

	this.CaseSensitive = false;
	this.AccentSensitive = false;

	this.AnswersAsList = AnswersAsList;

	this.jsTrim = function(inChaine)
	{
		var idxDebut = 0 ;
		var idxFin = inChaine.length ;
		while ((idxDebut < idxFin) && (inChaine.charAt(idxDebut) == ' '))
			idxDebut ++ ;
		if (idxDebut >= idxFin)
			return "" ;
		do
			idxFin -- ;
		while ((idxFin > idxDebut) && (inChaine.charAt(idxFin) == ' ')) ;
		return inChaine.substr(idxDebut, idxFin - idxDebut + 1) ;
	};

	this.canSubmit = function()
	{
		// Check that the min and max answers are checked
		if (!this.DontAllowSubmitIfEmpty)
			return true;

		var allInputs = this.GetInputs();

		for(var i = 0; i < allInputs.length; i++)
		{
			if (this.AnswersAsList)
			{
				if (allInputs[i].selectedIndex == 0)
				{
					alert(EpiLangJS.YouMustAnswerTheQuestion);
					return false;
				}
			}
			else
			{
				if (this.jsTrim(allInputs[i].value).length == 0)
				{
					alert(EpiLangJS.YouMustAnswerTheQuestion);
					return false;
				}
			}
		}

		return true;
	};

	this.GetInputs = function()
	{
		var allInputs = new Array();
		var j = 0;

		if (this.AnswersAsList)
		{
			var anyInputs = this.formQ.getElementsByTagName("SELECT");

			for(var i = 0; i < anyInputs.length; i++)
				if (anyInputs[i].name != 'NavigationSelect')
					allInputs[j++] = anyInputs[i];
		}
		else
		{
			var anyInputs = this.formQ.getElementsByTagName("INPUT");

			for(var i = 0; i < anyInputs.length; i++)
				if (anyInputs[i].type == "text")
					allInputs[j++] = anyInputs[i];

			if (j == 0) // textareas
			{
				anyInputs = this.formQ.getElementsByTagName("TEXTAREA");

				for(var i = 0; i < anyInputs.length; i++)
					allInputs[j++] = anyInputs[i];
			}
		}

		return allInputs;
	}

	this.RemoveAccents = function(str)
	{
		var accents =   "ÀàÁáÂâÃãÄäåÇçÈèÉéÊêËëÌìÍíÎîÏïñÒòÓóÔôÕõÖöÙùÚúÛûÜü";
		var noaccents = "AaAaAaAaAaaCcEeEeEeEeIiIiIiIinOoOoOoOoOoUuUuUuUu";

		var AccentArray = accents.split('');
		var noAccentArray = noaccents.split('');

		for(var i=0; i<AccentArray.length; i++)
			str = str.replace(AccentArray[i], noaccents[i]);

		return str;
	};

	// -----------------------------------------------------------------------
	// Renvoie le score
	// -----------------------------------------------------------------------
	this.GetScore = function()
	{
		if( this.enable	== false)
			return this.DisabledScore;

		var score = 0;

		var allInputs = this.GetInputs();

		for(var i = 0; i < allInputs.length; i++)
		{
			var word = "";

			if (this.AnswersAsList)
				word = allInputs[i].options[allInputs[i].selectedIndex].text;
			else
				word = allInputs[i].value;

			word = word.replace(/[\r\n]/g,'').replace(/^[\s]+/g,'').replace(/[\s]+$/g, '');

			var rightWords = this.RightAnswer[i];

			if (!this.CaseSensitive)
				word = word.toLowerCase();

			if (!this.AccentSensitive)
				word = this.RemoveAccents(word);

			for (var wordindex = 0; wordindex < rightWords.length; wordindex++)
			{
				var rightword = rightWords[wordindex];

				if (!this.CaseSensitive)
					rightword = rightword.toLowerCase();

				if (!this.AccentSensitive)
					rightword = this.RemoveAccents(rightword);

				// trim:
				rightword = rightword.replace(/^[\s]+/g,'').replace(/[\s]+$/g, '');

				if (word == rightword)
				{
					score++;
					break;
				}
			}
		}

		return score * ( this.GetScoreMax() / this.GetNoneWeightedScoreMax() );
	};

	this.GetNoneWeightedScoreMax = function()
	{
		return this.numbOfMissW;
	};

	// -----------------------------------------------------------------------
	// Affiche les bonnes réponses du quizz
	// -----------------------------------------------------------------------
	this.ShowAnswers = function()
	{
		// disable quizz (save the current score)
		this.Disable();

		var allInputs = this.GetInputs();

		if (document.getElementById && document.getElementById("Correction"))
		{
			document.getElementById("Correction").innerHTML = this.AnswerText;
		}
		else
		{
			for(var i = 0; i < allInputs.length; i++)
			{
				var rightword = '';
				var rightWords = this.RightAnswer[i];

				if (rightWords.length > 0)
					rightword = rightWords[0];

				if (this.AnswersAsList)
					allInputs[i].options[allInputs[i].selectedIndex].text = rightword;
				else
					allInputs[i].value = rightword;
			}
		}
	};

	// -----------------------------------------------------------------------
	// Serialize a form
	// word1,word2,word3,,lastword
	// -----------------------------------------------------------------------
	this.Serialize = function()
	{
		var str = "";
		var allInputs = this.GetInputs();
		var parts = new Array();

		for(var i = 0; i < this.numbOfMissW; i++)
		{
			if (this.AnswersAsList)
				parts[i] = allInputs[i].selectedIndex;
			else
				parts[i] = allInputs[i].value;
		}

		if (this.AnswersAsList)
			str = parts.join('@EasyquizzSeparator@');
		else
			str = parts.join('@EasyquizzSeparator@');

		return str;
	};

	this.Deserialize = function(str)
	{
		var storedQuestion = String(str);

		if (storedQuestion == '')
			return;

		var indexArray = storedQuestion.split("@EasyquizzSeparator@");
		var allInputs = this.GetInputs();

		if (this.numbOfMissW != indexArray.length) return;

		for (var i=0; i < indexArray.length; i++)
		{
			if (this.AnswersAsList)
				allInputs[i].selectedIndex = indexArray[i];
			else
				allInputs[i].value = indexArray[i];
		}
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
}

TexteATrous.prototype = new Question;
