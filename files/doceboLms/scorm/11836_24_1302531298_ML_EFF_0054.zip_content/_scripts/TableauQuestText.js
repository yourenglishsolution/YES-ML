// ========================= Copyright Epistema 2002 ========================
//
// TableauQuestText.js
//
// Script for QT type questions
//
// ==========================================================================


// -----------------------------------------------------------------------
// Constructeur de TableauQuestText (Open)
// Name : (string) le nom du quizz
// FormQ : (Formulaire) le formulaire des questions
// -----------------------------------------------------------------------
function TableauQuestText(Name, FormQ, NumbOfQuest)
{
	this.name = Name;
	this.formQ = FormQ;
	this.numberOfQuest = NumbOfQuest;

	this.RightAnswer = new Array();
	this.CaseSensitive = false;
	this.AccentSensitive = true;

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
		if (!this.DontAllowSubmitIfEmpty)
			return true;

		var allInputs = this.formQ.getElementsByTagName("TEXTAREA");

		for(var c = 0; c < allInputs.length; c++)
		{
			if (this.jsTrim(allInputs[c].value) == '')
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

		var allInputs = this.formQ.getElementsByTagName("TEXTAREA");

		for(var i = 0; i < allInputs.length; i++)
		{
			var answer = allInputs[i].value;
			answer = answer.replace(/[\r\n]/g,'').replace(/^[\s]+/g,'').replace(/[\s]+$/g, '');

			var rightanswer = this.RightAnswer[i];
			rightanswer = rightanswer.replace(/<br>/g, '').replace(/^[\s]+/g,'').replace(/[\s]+$/g, '');

			if (!this.CaseSensitive)
			{
				answer = answer.toLowerCase();
				rightanswer = rightanswer.toLowerCase();
			}

			if (!this.AccentSensitive)
			{
				answer = this.RemoveAccents(answer);
				rightanswer = this.RemoveAccents(rightanswer);
			}

			if (answer == rightanswer)
				score++;
		}

		return score * (this.GetScoreMax() / this.GetNoneWeightedScoreMax());
	};

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

	this.GetNoneWeightedScoreMax = function()
	{
		return 	this.numberOfQuest;
	};

	// -----------------------------------------------------------------------
	// Affiche les bonnes rÃ©sponses du quizz
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
			var allInputs = this.formQ.getElementsByTagName("TEXTAREA");

			for(var i = 0; i < allInputs.length; i++)
			{
				allInputs[i].value = this.RightAnswer[i];
			}
		}
	};

	// -----------------------------------------------------------------------
	// Serialize a form
	//
	// result -> "<%text1%><%texte2%>"
	// -----------------------------------------------------------------------
	this.Serialize = function()
	{
		var str="";
		var allInputs = this.formQ.getElementsByTagName("TEXTAREA");

		for(var i = 0; i < allInputs.length; i++)
		{
			str += "<%"+allInputs[i].value+"%>";
		}
		return str;
	};

	this.Deserialize = function(str)
	{
		var idx = 0;
		var pos1=0,pos2=0;
		var allInputs = this.formQ.getElementsByTagName("TEXTAREA");

		for(var i = 0; i < allInputs.length; i++)
		{
			pos1 = pos2;
			pos1 = str.indexOf("<%",pos1);
			pos2 = str.indexOf("%>",pos2);

			allInputs[i].value = str.substring(pos1+2,pos2);
			pos2 += 2;
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

		var allInputs = this.formQ.getElementsByTagName("TEXTAREA");

		for(var i = 0; i < allInputs.length; i++)
			this.disableObject(allInputs[i]);
	};
}

TableauQuestText.prototype = new Question;
