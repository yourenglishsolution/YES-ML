// ========================= Copyright Epistema 2002 ========================
//
// Question.js
//
// Base class for all questions script
//
// ==========================================================================

function DisableTextHandler() { this.blur(); }
function DisableButtonHandler() { return false; }
function DisableRadioHandler() { return false; }

// -----------------------------------------------------------------------
// Question is the base class for all questions
// -----------------------------------------------------------------------
function Question(Name, FormQ)
{
	this.enable = true;
	this.weight = 1;
	this.DisabledScore = 0;

	this.DontAllowSubmitIfEmpty = false;

	if (FormQ)
		this.formQ = FormQ;

	if (!this.formQ)
		this.formQ = null;

	this.name = Name || "";

	this.Hints = "";
	this.AnswerText = "";

	// -----------------------------------------------------------------------
	// Reset le formulaire
	// -----------------------------------------------------------------------
	this.Reset = function()
	{
		if( this.enable	== false)
			return;

		this.formQ.reset();
	};

	// -----------------------------------------------------------------------
	// Affiche une fenêtre PopUp avec l'hint. Rajoute le footer et le header
	// s'il y a lieu.
	// -----------------------------------------------------------------------
	this.ShowHint = function(bInCorrection, Header, Footer)
	{
		var DialogText = "";

		if (bInCorrection && this.Hints.length == 0) return false;

		if (String(Header).length > 0)
			DialogText += '<b>' + Header + '</b><br/>';

		if (this.Hints.length > 0)
		  DialogText += this.Hints;

		if (DialogText.length > 0)
		{
			if (document.getElementById && document.getElementById("Comment"))
			{
				DialogText = "<table width=\"100%\" class=\"Comment\" ><tr><td class=\"Comment\">"
									 + DialogText;
									 + "</td></tr></table>"

				document.getElementById("Comment").innerHTML = DialogText;
			}
			else
			{
				if (String(Footer).length > 0)
					DialogText += '<br><br>' + Footer;

				var replaceregexp = /<br>/gi;
				DialogText = DialogText.replace(replaceregexp, "\n");
				alert(DialogText);
			}

			return true;
		}

		return false;
	};

	// -----------------------------------------------------------------------
	// Fonctions pour désactiver les différents éléments d'un formulaire
	// -----------------------------------------------------------------------
	this.disableObject = function(obj)
	{
		// don't disable the navigation:
		if (obj.name == 'NavigationSelect')
			return;

		obj.disabled = true;

		switch(obj.type)
		{
			case "password":
			case "text":
			case "textarea":
				obj.onfocus = DisableTextHandler;
				obj.blur();
				break;

			case "button":
			case "checkbox":
			case "reset":
			case "submit":
				obj.onclick = DisableButtonHandler;
				break;

			case "radio":
				obj.onmousedown = DisableRadioHandler;
				break;

			case "hidden":
				break;

			default:
				//alert("Don't know how to disable"+obj.type);
				break;
		}
	};


	this.DisableForm = function()
	{
		this.enable = false;

		for(var i = 0; i < this.formQ.elements.length; i++)
			this.disableObject(this.formQ.elements[i]);
	};

	// because controls are not posted if they are disabled, we re-enable them
	// before submitting the form...
	this.ReEnableFormBeforeSubmit = function()
	{
		for(var i = 0; i < this.formQ.elements.length; i++)
			if (this.formQ.elements[i].tagName != 'OBJECT')
				this.formQ.elements[i].disabled = false;
	};

	this.submit = function()
	{
		if (this.enable && !this.canSubmit())
			return;

		this.ReEnableFormBeforeSubmit();
		this.formQ.submit();
	};


	this.canSubmit = function()
	{
		// Check that the user can submit the question
		return true;
	};

	this.ShowAnswers = function()
	{
	};

	this.GetScore = function()
	{
		return 0;
	};

	this.Deserialize = function(str)
	{
	};

	this.Serialize = function()
	{
		return "";
	};

	this.Disable = function()
	{
		this.DisableForm();
	};

	this.GetScoreMax = function()
	{
		return parseFloat(this.weight);
	};

	this.ShowScore = function(CorrectString, HalfString)
	{
		if (document.getElementById)
		{
			str = '';
			if (this.GetScore() == 0)
				str = '';
			else if (this.GetScore() < this.GetScoreMax())
				str = HalfString + '<br>';
			else
				str = CorrectString + '<br>';

			str += 'Score : ' + MyRound(this.GetScore()) + "/" + this.GetScoreMax();

			document.getElementById("score").innerHTML = str;
		}
	};
}

Event.observe(window, 'load', function(){
	var allTextAreas = document.getElementsByTagName("TEXTAREA");
	if (allTextAreas.length > 0)
	{
		var maxTextAreaLength = Math.floor((2083 - window.location.href.length - 100) / allTextAreas.length);

		// http://support.microsoft.com/?scid=kb%3Ben-us%3B208427
		Event.observe(document.body, 'keyup', function(event) {

			var elt = Event.element(event);

			if ('TEXTAREA' == elt.tagName)
			{
				if (escape(elt.value).length > maxTextAreaLength)
				{
					var theNewLength = unescape(escape(elt.value).substring(0, maxTextAreaLength)).length;
					elt.value = elt.value.substring(0, theNewLength);
				}
			}
		});
	}

});
