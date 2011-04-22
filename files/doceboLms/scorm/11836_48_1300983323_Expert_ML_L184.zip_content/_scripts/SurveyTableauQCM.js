// ========================= Copyright Epistema 2002 ========================
//
// SurveyTableauQCM.js
//
// Script for QCM type questions
//
// ==========================================================================

function SurveyTableauQCM(Name, FormQ, NumRows, NumCols, inQCUMode)
{
	this.name = Name;
	this.formQ = FormQ;

	this.NumRows = NumRows;
	this.NumCols = NumCols;

	this.isQCU = inQCUMode;

	this.ColsMinChecked = new Array();
	this.ColsMaxChecked = new Array();

	this.canSubmit = function()
	{
		// Check that the min and max answers are checked

		var allInputs = this.formQ.getElementsByTagName("INPUT");
		var allCheckBoxes = new Array();

		var j = 0;
		for(var i = 0; i < allInputs.length; i++)
			if (allInputs[i].type == "radio" || allInputs[i].type == "checkbox")
				allCheckBoxes[j++] = allInputs[i];

		for(var c = 0; c < this.NumCols; c++)
		{
			var TotalChecked = 0;

			for(var r = 0; r < this.NumRows; r++)
			{
				if (allCheckBoxes[r * this.NumCols + c].checked)
					TotalChecked ++;
			}

			if (TotalChecked < this.ColsMinChecked[c] ||
					TotalChecked > this.ColsMaxChecked[c])
			{
				if (this.NumCols <= 1)
				{
					var msg = EpiLangJS.YouMustCheckedAtLeastXboxes;
					msg = msg.replace("%1", this.ColsMinChecked[c]);
					msg = msg.replace("%2", this.ColsMaxChecked[c]);
				}
				else
				{
					var msg = EpiLangJS.YouMustCheckedAtLeastXboxesInColN;
					msg = msg.replace("%1", this.ColsMinChecked[c]);
					msg = msg.replace("%2", 1 + c);
					msg = msg.replace("%3", this.ColsMaxChecked[c]);
				}

				alert(msg);
				return false;
			}
		}

		return true;
	};

	this.submit = function()
	{
		if (!this.canSubmit())
			return;

		this.ReEnableFormBeforeSubmit();
		this.formQ.submit();
	};

	this.GetScore = function()
	{
		return 0;
	};

	this.ShowAnswers = function()
	{
		// disable quizz (save the current score)
		this.Disable();
	};

	this.Serialize = function()
	{
		var str="";

		var j = 0;
		for(var i = 0; i < allInputs.length; i++)
			if (allInputs[i].type == "radio" || allInputs[i].type == "checkbox")
				allCheckBoxes[j++] = allInputs[i];

		for(var j = 0; j < allCheckBoxes.length; j++)
			str += (allCheckBoxes[j].checked ? "1" : "0");

		return str;
	};

	this.Deserialize = function(str)
	{
		var strSavedQuestion = String(str);

		for(var i = 0; i < allInputs.length; i++)
			if (allInputs[i].type == "radio" || allInputs[i].type == "checkbox")
				allCheckBoxes[j++] = allInputs[i];

		for(var j = 0; j < allCheckBoxes.length; j++)
			allCheckBoxes[j].checked = strSavedQuestion.charAt(j) == '1';
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

		var allInputs = this.formQ.getElementsByTagName("INPUT");
		var allCheckBoxes = new Array();

		var j = 0;
		for(var i = 0; i < allInputs.length; i++)
			if (allInputs[i].type == "radio" ||
					allInputs[i].type == "checkbox")
				allCheckBoxes[j++] = allInputs[i];

		for(var j = 0; j < allCheckBoxes.length; j++)
			this.disableObject(allCheckBoxes[j]);
	};
}

SurveyTableauQCM.prototype = new Question;


// ----------------------------------------
// Add an effect on the AnswerRows
// ----------------------------------------

Event.observe(window, 'load', initAnswerTable);

var CheckboxClickProcessed = false;
var AnswerRowEventHandler = Class.create();
AnswerRowEventHandler.prototype = {
	initialize: function (elt)
		{
			this.elt = elt;
			if (elt.tagName == 'TR')
			{
				this.elt.onmouseover = this.outlineTR.bindAsEventListener(this);
				this.elt.onmouseout = this.unOutlineTR.bindAsEventListener(this);
				this.elt.onclick = this.selectTR.bindAsEventListener(this);
			}

			if (elt.tagName == 'TD')
				this.elt.onclick = this.selectTR.bindAsEventListener(this);

			if (elt.tagName == 'INPUT')
				this.elt.onclick = this.checkInputAndStop;
		},

	checkInputAndStop: function (evt)
		{
			CheckboxClickProcessed = true;
		},

	selectTR: function (evt)
		{
			var elt = this.elt;

			if (CheckboxClickProcessed)
			{
				if (elt.tagName != 'TD')
					CheckboxClickProcessed = false;

				return;
			}

			var inputs = $A(elt.getElementsByTagName('input'));

			if (inputs.length > 1)
				return; // This is a matrix

			inputs.each(function(input)
				{
					if (!input.disabled)
					{
						if(input.checked && input.type == 'checkbox')
							input.checked = false;
						else
							input.checked = true;
					}

					if (elt.tagName == 'TD')
						CheckboxClickProcessed = true;
				});
		},

	outlineTR: function ()
		{
			var init = true;
			var inputs = $A(this.elt.getElementsByTagName('input'));
			inputs.each(function(input)
				{
					if (input.disabled)
						init = false;
				});
			if (init)
				$(this.elt).addClassName('row_hover');
		},

	unOutlineTR: function ()
		{
			var init = true;
			var inputs = $A(this.elt.getElementsByTagName('input'));
			inputs.each(function(input)
				{
					if (input.disabled)
						init = false;
				});
			if (init)
				$(this.elt).removeClassName('row_hover');
		}
}

function initAnswerTable()
{
	if (IsInCorrection)
		return;

	var answerTable = $('Question_answers');
	var TRs = $A(answerTable.rows);

	TRs.each(
		function (elt)
		{
			var watcher = new AnswerRowEventHandler(elt);
			var inputs = $A(elt.getElementsByTagName('input'));
			inputs.each(function(input)
				{
					var watcher = new AnswerRowEventHandler(input);
				});

			var cells = $A(elt.cells);
			cells.each(function(input)
				{
					var watcher = new AnswerRowEventHandler(input);
				});
		}
	);
}
