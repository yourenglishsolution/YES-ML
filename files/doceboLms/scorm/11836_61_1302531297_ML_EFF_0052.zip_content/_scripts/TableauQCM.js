// ========================= Copyright Epistema 2002 ========================
//
// TableauQCM.js
//
// Script for QCM type questions
//
// ==========================================================================

// -----------------------------------------------------------------------
// Constructeur de QCM
// Name : (chaine) le nom du quizz
// FormQ : (Formulaire) le formulaire des questions
// NumbOfQuest : (int) le nombre de questions
// Scoring : (chaine) le type de scoring "AllRight"|"OneRightOnePoint"|"UserDefined"
// -----------------------------------------------------------------------
function TableauQCM(Name, FormQ, NumbOfQuest, Scoring, inQCUMode)
{
	this.name = Name;
	this.formQ = FormQ;

	this.numberOfQuest = NumbOfQuest; // For regular QCU/QCM, this is 1 (row)

	this.scoring = Scoring;
	this.isQCU = inQCUMode;

	this.RightAnswer = new Array();
	this.AnswersWeight = new Array();

	this.canSubmit = function()
	{
		// Check that the min and max answers are checked
		if (!this.DontAllowSubmitIfEmpty)
			return true;

		var allInputs = this.formQ.getElementsByTagName("INPUT");
		var allCheckBoxes = new Array();

		var j = 0;
		for(var i = 0; i < allInputs.length; i++)
			if (allInputs[i].type == "radio" || allInputs[i].type == "checkbox")
				allCheckBoxes[j++] = allInputs[i];

		this.NumRows = this.numberOfQuest;
		this.NumCols = allCheckBoxes.length / this.numberOfQuest;

		if (this.isQCU)
		{
			for(var r = 0; r < this.NumRows; r++)
			{
				var bAnswered = false;
				for(var c = 0; c < this.NumCols; c++)
				{
					if (allCheckBoxes[r * this.NumCols + c].checked)
					{
						bAnswered = true;
						break;
					}
				}

				if (!bAnswered)
				{
					alert(EpiLangJS.YouMustAnswerTheQuestion);
					return false;
				}
			}
		}
		else
		{
			var bAnswered = false;

			for(var r = 0; r < this.NumRows; r++)
			{
				for(var c = 0; c < this.NumCols; c++)
				{
					if (allCheckBoxes[r * this.NumCols + c].checked)
					{
						bAnswered = true;
						break;
					}
				}

				if (bAnswered) break;
			}

			// Keep one checked answer as good enough
			if (!bAnswered)
			{
				alert(EpiLangJS.YouMustAnswerTheQuestion);
				return false;
			}
		}

		return true;
	};


	this.GetScore = function()
	{
		if( this.enable	== false)
			return this.DisabledScore;

		var score = 0;

		if (this.isQCU)
		{
			if (this.scoring == "UserDefined") // only for QCM/QCU (no tabQCM/tabQCU)
			{
				var MaxRowWeight = 0;

				for(var i = 0; i < this.numberOfQuest; i++)
				{
					var NbsRightAnswer = 0;

					for(var j = 0; j < this.formQ.elements["Q"+i].length; j++)
					{
						if (this.formQ.elements["Q"+i][j].checked == true)
							NbsRightAnswer += this.AnswersWeight[i][j];

						if (MaxRowWeight <= this.AnswersWeight[i][j])
							MaxRowWeight = this.AnswersWeight[i][j];
					}

					if (MaxRowWeight != 0)
						score += (NbsRightAnswer / MaxRowWeight);
				}

				if (this.numberOfQuest == 0)
					score = 0;
				else
					score = score / this.numberOfQuest;
			}
			else
			{
				for(var i = 0; i < this.numberOfQuest; i++)
				{
					var NbsRightAnswer = 0;

					for(var j = 0; j < this.formQ.elements["Q"+i].length; j++)
					{
						if (this.formQ.elements["Q"+i][j].checked == true &&
								this.RightAnswer[i][j] == true)
							NbsRightAnswer++;
					}

					score += NbsRightAnswer;
				}

				if (this.numberOfQuest == 0)
					score = 0;
				else
					score = score / this.numberOfQuest;
			}
		}
		else
		{

			if (this.scoring == "AllRight")
			{
				score = 1;

				for(var i = 0; i < this.numberOfQuest; i++)
				{
					for(var j = 0; j < this.formQ.elements["Q"+i].length; j++)
					{
						if ( this.formQ.elements["Q"+i][j].checked !=	this.RightAnswer[i][j])
						{
							return 0;
						}
					}
				}
			}
			else if (this.scoring == "UserDefined") // only for QCM/QCU (no tabQCM/tabQCU)
			{
				var MaxRowWeight = 0;

				for(var i = 0; i < this.numberOfQuest; i++)
				{
					var NbsRightAnswer = 0;

					for(var j = 0; j < this.formQ.elements["Q"+i].length; j++)
					{
						if (this.formQ.elements["Q"+i][j].checked == true)
							NbsRightAnswer += this.AnswersWeight[i][j];

						if (this.AnswersWeight[i][j] > 0)
							MaxRowWeight += this.AnswersWeight[i][j];
					}

					if (MaxRowWeight != 0)
						score += (NbsRightAnswer / MaxRowWeight);
				}

				if (this.numberOfQuest == 0)
					score = 0;
				else
					score = score / this.numberOfQuest;
			}
			else
			{
				var NbsMaxRightAnswer = 0;

				for(var i = 0; i < this.numberOfQuest; i++)
				{
					var NbsRightAnswer = 0;

					for(var j = 0; j < this.formQ.elements["Q"+i].length; j++)
					{
						if (this.RightAnswer[i][j] == true)
							NbsMaxRightAnswer ++;

						if (this.formQ.elements["Q"+i][j].checked == true)
							if (this.RightAnswer[i][j] == true)
								NbsRightAnswer++;
							else
								NbsRightAnswer--;
					}

					score += NbsRightAnswer;
				}

				if (score < 0) return 0;

				if (NbsMaxRightAnswer == 0)
					score = 0;
				else
					score = score / NbsMaxRightAnswer;
			}
		}

		return score * this.GetScoreMax();
	};

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
			for(var i = 0; i < this.numberOfQuest; i++) {
				for(var j = 0; j < this.formQ.elements["Q"+i].length; j++)
				{
					this.formQ.elements["Q"+i][j].checked = this.RightAnswer[i][j];
				}
			}
		}
	};

	// -----------------------------------------------------------------------
	// Serialize a form
	//
	// - x -
	// - x x
	// - - x
	//
	// result -> "010011001"
	// -----------------------------------------------------------------------
	this.Serialize = function()
	{
		var str="";

		var allInputs = this.formQ.getElementsByTagName("INPUT");
		var allCheckBoxes = new Array();

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

		var allInputs = this.formQ.getElementsByTagName("INPUT");
		var allCheckBoxes = new Array();

		var j = 0;
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
			if (allInputs[i].type == "radio" ||				// QCU
					allInputs[i].type == "checkbox" || 		// QCM
					allInputs[i].type == "text")					// More text
				allCheckBoxes[j++] = allInputs[i];

		for(var j = 0; j < allCheckBoxes.length; j++)
			this.disableObject(allCheckBoxes[j]);
	};
}

TableauQCM.prototype = new Question;


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
