// ========================= Copyright Epistema 2002 ========================
//
// JSQuizzCommon.js
//
// Common core script
//
// ==========================================================================


// IE don't know undefined
if (document.all) {
	// IE
	var undefined = null;
}

function RoundToNdp(X, N)
{
	var T = Number('1e' + N);
	return Math.round(X*T)/T;
}

function MyRound(X)
{
	if(X < 1)
	{
		return RoundToNdp(X,2);
	} else if(X < 5) {
		return RoundToNdp(X,1);
	} else {
		return RoundToNdp(X,0);
	}
}

function accent(str)
{
	if (str == "&egrave;") return String.fromCharCode(232);
	if (str == "&eacute;") return String.fromCharCode(233);
	if (str == "&agrave;") return String.fromCharCode(224);
}

// =======================================================================
// Serialize/Deserialize Form
// =======================================================================
function SerializeForm(Sform) {
	var str = "";

	for(var i = 0; i < Sform.elements.length; i++) {
		switch ( Sform.elements[i].type ) {
			case "password":
			case "text":
			case "textarea":
				// Remove all "<%" "%>
				while( Sform.elements[i].value.indexOf("%>") != -1 || Sform.elements[i].value.indexOf("<%") != -1)
					Sform.elements[i].value = Sform.elements[i].value.replace(/<%/g,"").replace(/%>/g,"");
				str += "<%" + Sform.elements[i].value + "%>";
				break;
			case "checkbox":
			case "radio":
				str += Sform.elements[i].checked ? "1" : "0";
				break;
			default:
				break;
		}
	}

	return str;
}

// -----------------------------------------------------------------------

function DeserializeForm(Sform, str, idx) {
// Démarre la deserialisation à idx, cela permet d'enchainer
// les déserialisations au cas où l'on a plusieurs formulaires.

	if(idx == undefined) {
		idx = 0;
	}

	for(var i = 0; i < Sform.elements.length; i++) {
		switch ( Sform.elements[i].type ) {
			case "password":
			case "text":
			case "textarea":
				var endText = str.indexOf("%>",idx);
				Sform.elements[i].value = str.substring(idx+2,endText);
				idx = endText + 2;
				break;
			case "checkbox":
			case "radio":
				Sform.elements[i].checked = ( str.charAt(idx) == "1") ? true : false;
				idx++;
				break;
			default:
				break;
		}
	}

	return idx;
}

// -----------------------------------------------------------------------
// Get a parameter from a url
// return null if the parameter doesn't exist
// -----------------------------------------------------------------------
function GetParameter(url,param) {
	param += "=";

	var pos1 = url.indexOf(param);
	// check if the parameter exist
	if( pos1 == -1 )
		return null;
	else
		pos1 += param.length;

	var pos2 = url.indexOf("&",pos1);

	if(pos2 == -1) {
		// we want the last parameter
		return url.substr(pos1);
	} else {
		return url.substring(pos1,pos2);
	}
}

// ----------------

function GetQuizz_Init() {
	var url = window.parent.location.toString();
	return unescape( GetParameter( url, "quizz_init") );
}

// =======================================================================
// QuizzManager
// =======================================================================

// Types :
function ScoreData()
{
	this.value = 0;
	this.max = 0;

	this.ToCookie = function()
	{
	  var ret = "";

	  ret += this.value + "|" + this.max;

	  return ret;
	};

	this.FromCookie = function(cookieVal)
	{
		var strCookieVal = String(cookieVal);
	  var values = strCookieVal.split("|");
  	this.value = Number(values[0]);
  	this.max = Number(values[1]);
	};
}

function TimeData()
{
	this.value = 0; // Elapsed time on the question
	this.max = 0;

	this.ToCookie = function()
	{
	  var ret = "";

	  ret += this.value + "|" + this.max;
	  return ret;
	};

	this.FromCookie = function(cookieVal)
	{
		var strCookieVal = String(cookieVal);
	  var values = strCookieVal.split("|");
  	this.value = Number(values[0]);
  	this.max = Number(values[1]);
	};
}

function QuestionData()
{
	// a few data that we save for each question.

	this.nbTries = 0; // remaining tries for this question

	this.ToCookie = function()
	{
	  return this.nbTries;
	};

	this.FromCookie = function(cookieVal)
	{
  	this.nbTries = Number(cookieVal);
	};
}


function PageData()
{
	this.Name = 0;
	this.Index = 0;
	this.Index = new Array();
	this.Theme = new Array();
}


function ScoreComment()
{
	this.comment = "";
	this.from = 0;
	this.to = 100;
}

// -----------------------------------------------------------------------

function randomCompare(a, b){
	var n = ( Math.random() - Math.random() ) * 10;
	return n ? n : -1;
}

function CreatBarreHisto(ImgOn, ImgOff, rapport, maxl, height) {
	var res ="";
	l1 = Math.round( (rapport * maxl) + 1 );
	l2 = Math.round( maxl - (rapport * maxl) + 1 );

	res += '<img src="' + ImgOn + '" width=' + l1 +' height=' + height + '>';
	res += '<img src="' + ImgOff + '" width=' + l2 +' height=' + height + '>';

	return res;
}

// -----------------------------------------------------------------------
//
//           -----=====~~~~~{ Object QuizzManager }~~~~~=====-----
//
// -----------------------------------------------------------------------

function QuizzManager()
{
	this.hwin = null;

	this.tabScore = new Array();
	this.tabTime = new Array();
	this.tabQuestionData = new Array();
	this.tabAnswers = new Array();

	this.tabScoreComments = new Array();

	// number of questions to take in random mode
	this.RandomTakeN = 0;

	this.index = -1;  // current page - 0 is the first page of the quizz

	this.correction = false;
	this.GlobalSpentTime = 0;

	this.InitPageNeeded = false;
	this.bLoaded = false;

	// The following values are always valid, they are
	// hardcoded in _ManagerFrame

	this.IsGlobalTimer = false;
	this.GlobalTimeMax = 0;
	this.FinalMaxScore = 0;
	this.IsNoTimer = false;
	this.QuizzFrame = null;
	this.AllowBack = false;
	this.AllowNegativeQuestions = true;

	this.tabPage = new Array();
	this.endpage = "";
	this.imagetab = new Array();
	// 0 : fleche haut (ex: quiz_ok.gif)
	// 1 : fleche milieu (ex: quiz_moyen.gif)
	// 2 : fleche bas (ex: quiz_beurk.gif)
	// 3 : bar on (ex: histo-h_blue.gif)
	// 4 : bar off (ex: histo-h_dark.gif)

	// ----------------------------------------------------------- //

	// functions :

	this.SetCookie = function(sName, sValue)
	{
		document.cookie = sName + "=" + escape(sValue);
	};

	// Retrieve the value of the cookie with the specified name.
	this.GetCookie = function(sName)
	{
		// cookies are separated by semicolons
		var aCookie = document.cookie.split("; ");
		for (var i=0; i < aCookie.length; i++)
		{
			// a name/value pair (a crumb) is separated by an equal sign
			var aCrumb = aCookie[i].split("=");
			if (sName == aCrumb[0])
				return unescape(aCrumb[1]);
		}

		// a cookie with the requested name does not exist
		return null;
	};

	// This function checks the existence of a cookie for this quizz
	// If the cookie is non existent, then we create one
	// If the cookie is existent, there has been a refresh done, so we
	// get back the runtime values of the quizz that would have been
	// saved during the unload
	this.InitManager = function()
	{
		var SavedIndex;

		SavedIndex = this.GetCookie("CurrentPage");

		if (SavedIndex != null)
		{
			// There's a cookie present, but we check the current page
			// in QuizzFrame. If it is the introduction,
			// then we remove the cookie, as it's supposedly a new session.
			if (this.QuizzFrame != null)
			{
				var pageName = String(this.QuizzFrame.location);
				var lastPosSlash = pageName.lastIndexOf("/");
				pageName = pageName.substring(lastPosSlash + 1);

				if (pageName == "introduction.htm")
				{
					this.SetCookie("expires", "Thu, 01-Jan-70 00:00:01 GMT");
					this.bLoaded = true;
					return;
				}
			}

			this.index = Number(SavedIndex);

			this.ResetPageOrder(this.GetCookie("RandomOrder"));

			var TimeCookie = String(this.GetCookie("Times"));
			var Times = TimeCookie.split("!");
			for (var i=0; i < Times.length; i++)
		  {
				this.tabTime[i].FromCookie(Times[i]);
			}

			var ScoreCookie = String(this.GetCookie("Scores"));
			var Scores = ScoreCookie.split("!");
			for (var j=0; j < Scores.length; j++)
		  {
				this.tabScore[j].FromCookie(Scores[j]);
			}

			var QuestionDataCookie = String(this.GetCookie("QuestionData"));
			var QuestionDatas = QuestionDataCookie.split("!");
			for (var k=0; k < QuestionDatas.length; k++)
		  {
				this.tabQuestionData[k].FromCookie(QuestionDatas[k]);
			}

			this.correction = (this.GetCookie("Correction") == "true");

			this.GlobalSpentTime = Number(this.GetCookie("GlobalSpentTime"));

			var pageName = String(this.QuizzFrame.location);
			var lastPosSlash = pageName.lastIndexOf("/");
			pageName = pageName.substring(lastPosSlash + 1);

	  	var newPageName = this.GetCurrentLink();

	  	if (newPageName != pageName)
			  this.QuizzFrame.location = newPageName;

			if (this.InitPageNeeded)
				this.QuizzFrame.InitPage();
		}

		this.bLoaded = true;
	};

	// This function saves the runtime values of the quizz into a cookie
	this.UnloadManager = function()
	{
		this.SetCookie("CurrentPage", this.index);

		this.SetCookie("RandomOrder", this.SavePageOrder());

		this.SetCookie("Correction", this.correction);
		this.SetCookie("GlobalSpentTime", this.GlobalSpentTime);

		var Times = "";
		for (var i = 0; i < this.tabTime.length; i++)
		{
			if (i > 0)
				Times += "!";

			Times += this.tabTime[i].ToCookie();
		}

		this.SetCookie("Times", Times);

		var Scores = "";
		for (var j = 0; j < this.tabScore.length; j++)
		{
			if (j > 0)
				Scores += "!";

			Scores += this.tabScore[j].ToCookie();
		}

		this.SetCookie("Scores", Scores);

		var QuestionDatas = "";
		for (var k = 0; k < this.tabQuestionData.length; k++)
		{
			if (k > 0)
				QuestionDatas += "!";

			QuestionDatas += this.tabQuestionData[k].ToCookie();
		}

		this.SetCookie("QuestionData", QuestionDatas);
	};

	this.SetGlobalTimer = function(TimeValue)
	{
	  this.IsGlobalTimer = true;
	  this.GlobalTimeMax = Number(TimeValue);
	};

	this.SetMaxScore = function(MaxScoreValue)
	{
	  this.FinalMaxScore = Number(MaxScoreValue);
	};

	this.SetUseNoTimer = function()
	{
		this.IsNoTimer = true;
	};

	this.Randomize = function(TakeNQuestion)
	{
		this.RandomTakeN = TakeNQuestion;

		// randomize this.pages array
		this.tabPage.sort(randomCompare);
	};

	this.Loaded = function()
	{
		if (! this.bLoaded )
		  this.InitPageNeeded = true;

		return this.bLoaded;
	};

	this.LoadingPage = function(pageURL)
	{
    var newIndex = 0;
    var pageName = String(pageURL);
    var lastPosSlash = pageName.lastIndexOf("/");
	  pageName = pageName.substring(lastPosSlash + 1);

		if ("introduction.htm" == pageName)
		{
			newIndex = -1;
		}
		else
	  {
			for (var i = 0; i < this.tabPage.length; i++)
			{
				if (this.tabPage[i].Name == pageName)
				{
					newIndex = i;
					break;
				}
			}
		}

	  // accept the page if we move on or if we
	  // specified the quizz can go back
	  if (this.AllowBack || newIndex >= this.index)
			this.index = newIndex;
		else
		{
		  alert(str.strItIsNotAllowedToGoBack);
		  this.QuizzFrame.location = this.GetCurrentLink();
		}
	};

	this.GetPageNumber = function()
	{
	  return this.index + 1;
	};

	this.GetLimit = function()
	{
		var limit = this.tabPage.length;

		if(this.RandomTakeN > 0 && this.RandomTakeN < limit)
		{
			limit = this.RandomTakeN;
		}

		return limit;
	};

	this.GetCurrentLink = function()
	{
		var l = "";

		if( this.index < this.GetLimit()) {
			l = this.tabPage[this.index].Name;
		} else {
			l = this.endpage;
		}

		return l;
	};

	this.GetThemeHTML = function(pageIndex)
	{
		if (pageIndex == undefined)
			pageIndex = this.index;

		var str = "";

		if (pageIndex < this.GetLimit())
		{
			for (var i = 0; i < this.tabPage[pageIndex].Theme.length; i++)
			{
				if (this.tabPage[pageIndex].Theme[i] != '')
				{
					if (str != "")
						 str += " &gt; ";

					str += '<span class="theme">' + this.tabPage[pageIndex].Theme[i] + "</span>";
				}
			}
		}

		return str;
	};

	this.MoveToNextPage = function()
	{
		if (this.index >= (this.GetLimit() - 1) && this.AllowBack)
		{
			if (this.correction ||
					confirm(this.strYouHaveReachTheLastPage))
			{
				this.index = this.index + 1;
				this.QuizzFrame.location = this.GetCurrentLink();
			}
		}
		else
		{
			this.index = this.index + 1;
			this.QuizzFrame.location = this.GetCurrentLink();
		}
	};

	this.MoveToPreviousPage = function()
	{
		this.index = this.index - 1;
		this.QuizzFrame.location = this.GetCurrentLink();
	};

	this.MoveToScorePage = function()
	{
		alert(this.strTheQuizzIsFinished);

		this.index = this.GetLimit();
		this.QuizzFrame.location = this.GetCurrentLink();
	};

	this.SetScoreEntry = function(Score, Max)
	{
		if (Score < 0 && !this.AllowNegativeQuestions)
			Score = 0;

		this.tabScore[this.index].value = Score;
		this.tabScore[this.index].max = Max;
	};

	this.SetTimeEntry = function(Time, MaxTime)
	{
    this.tabTime[this.index].max = Number(MaxTime);

		if (this.IsGlobalTimer)
		{
			this.tabTime[this.index].value = Number(Time) - this.GlobalSpentTime + this.tabTime[this.index].value;
		  this.GlobalSpentTime = Number(Time);
		}
		else
			this.tabTime[this.index].value = Number(Time);
	};

	this.SetQuestionDataEntry = function(nbTries)
	{
		this.tabQuestionData[this.index].nbTries = nbTries;
	};

	this.SetAnswerEntry = function(str)
	{
	  this.tabAnswers[this.index] = String(str);
	}

	this.GetAnswerEntry = function()
	{
		if (this.tabAnswers.length > this.index)
		{
		  return this.tabAnswers[this.index];
		}
		else return "";
	}

	this.getSpentTime = function()
	{
		if (this.IsGlobalTimer)
		  return Number(this.GlobalSpentTime);

    return this.tabTime[this.index].value;
	};

	this.getNbTries = function()
	{
	  return this.tabQuestionData[this.index].nbTries;
	};

	this.SavePageOrder = function()
	{
		// returns a string containing the current page
		// order, in order to save it in a cookie

		var str = '';

		for (var i = 0; i < this.tabPage.length; i++)
		{
			if (i > 0)
				str += '|';

			str += this.tabPage[i].Index;
		}

		return str;
	};

	this.ResetPageOrder = function(SavedOrder)
	{
		var strSavedOrder = String(SavedOrder);

		if (strSavedOrder.length == 0) return;

		var NewOrder = strSavedOrder.split("|");

		if (NewOrder.length == 0) return;

		// Backup the current array in a temp var
		var TempTabPage = new Array();

		for (var i=0; i < this.tabPage.length; i++)
			TempTabPage[i] = new PageData();

		for (var i=0; i < this.tabPage.length; i++)
		{
			TempTabPage[this.tabPage[i].Index].Name = this.tabPage[i].Name;
			TempTabPage[this.tabPage[i].Index].Index = this.tabPage[i].Index;
		}

		this.tabPage = new Array();

		for (var i=0; i < NewOrder.length; i++)
		{
			this.tabPage[i] = new PageData();
			this.tabPage[i].Name = TempTabPage[NewOrder[i]].Name;
			this.tabPage[i].Index = TempTabPage[NewOrder[i]].Index;
		}

		var newstr = '';
		for (var i=0; i < this.tabPage.length; i++)
		{
			if (i > 0)
				newstr += '|';

			newstr += this.tabPage[i].Index;
		}
	};

	this.SetPage = function(PageNumber, Name, ScoreMax, Theme)
	{
		this.tabPage[PageNumber] = new PageData();
		this.tabPage[PageNumber].Name = Name;
		this.tabPage[PageNumber].Index = PageNumber;

		if (Theme != "")
			this.tabPage[PageNumber].Theme = String(Theme).split("|");

		this.tabScore[PageNumber] = new ScoreData();
		this.tabScore[PageNumber].max = ScoreMax;
		this.tabScore[PageNumber].value = 0;

		this.tabTime[PageNumber] = new TimeData();
		this.tabTime[PageNumber].value = 0;
		this.tabTime[PageNumber].max = 0;

		this.tabQuestionData[PageNumber] = new QuestionData();
		this.tabQuestionData[PageNumber].nbTries = 0;
	};

	this.seeCorrection = function()
	{
		this.correction = true;
		this.index = -1;
	};

	this.CalculateTimeString = function(timeInSec)
	{
		if (timeInSec < 60)
			return timeInSec + " " + EpiLang.sec;

		var Minutes = Math.floor(timeInSec / 60);
		timeInSec = timeInSec % 60;

		if (Minutes < 60)
		{
			if (timeInSec != 0)
				return Minutes + " " + EpiLang.min + " " + timeInSec + " " + EpiLang.sec;
			else
				return Minutes + " " + EpiLang.min;
		}

		var Hours = Math.floor(Minutes / 60);
		Minutes = Minutes % 60;

		return Hours + ":" + Minutes + ":" + timeInSec + "";
	};

	this.Show = function(PageString,
											 ScoreString,
											 TimeString,
											 TotalScoreString,
											 TotalTimeString,
											 QuestionString)
	{
		var str = "";
		var totalScore = 0;
		var scoreMax = 0;
		var totalTime = 0;
		var totalTimeMax = 0;
		var oneQuestNotTimeLimited = false;

		str += '<table width="100%" border="0"  cellspacing="0" cellpadding="0" class="ScoreTable">';
		str += ' <tr> ';
		str += '  <td class="ScoreHead">'+QuestionString+'</td>';
		str += '  <td class="ScoreHead">'+ScoreString+'</td>';
		str += '  <td class="ScoreHead" colspan="2">'+TimeString+'</td>';
		str += ' </tr>';

		var CurrentThemeHTML = "";

		var QuestionCount = 1;

		for(var i = 0; i < this.GetLimit(); i++)
		{
			if(this.tabScore[i] == undefined)
				continue;

			if (this.tabScore[i].max == 0)
				continue;

			if (this.RandomTakeN == 0)
			{
				// if we are not in a random quizz, add the theme :
				var thisThemeHTML = this.GetThemeHTML(i);

				if (thisThemeHTML != CurrentThemeHTML)
				{
					CurrentThemeHTML = thisThemeHTML;
					if (thisThemeHTML != '')
					{
						str += ' <tr> ';
						str += '  <td class="ScoreHead" colspan="4">'+thisThemeHTML+'</td>';
						str += ' </tr>';
					}
				}
			}

			str += '  <tr>\n';
			str += '    <td class="ScoreQuestionRow">' + QuestionString + ' ' + (QuestionCount) + '</td>\n';

			// Show Score
			totalScore += this.tabScore[i].value;
			scoreMax += this.tabScore[i].max;

			str += '    <td class="ScoreQuestionRow"><span dir="ltr">' + MyRound(this.tabScore[i].value) + '/' + MyRound(this.tabScore[i].max) + '</span></td>';

			// Show time
			time = Math.round( this.tabTime[i].value );
			totalTime += this.tabTime[i].value;

			if (this.tabTime[i].max == 0 || this.IsGlobalTimer)
			{
				// time is not limited
				oneQuestNotTimeLimited	= true;
				str += '    <td class="ScoreQuestionRow"><span dir="ltr">' + this.CalculateTimeString(time) + '</span></td>';
				str += '    <td width="120" class="ScoreQuestionRow">&nbsp;</td>';
			}
			else
			{
				str += '    <td class="ScoreQuestionRow"><span dir="ltr">' + this.CalculateTimeString(time) + '</span>/<span dir="ltr">' + this.CalculateTimeString(this.tabTime[i].max) + '</span></td>';

				rapport = (time / this.tabTime[i].max);
				totalTimeMax += this.tabTime[i].max;
				imgstr = CreatBarreHisto(this.imagetab[3], this.imagetab[4], rapport, 60, 6);
				str += '    <td width="120" class="ScoreQuestionRow">' + imgstr + '</td>';
			}

			str += '  </tr>\n';
			QuestionCount++;
		}

		if (this.IsGlobalTimer)
		{
			totalTime = this.GlobalSpentTime;
			totalTimeMax = this.GlobalTimeMax;
		}

		totalTime = Math.round(totalTime);
		totalTimeMax = Math.round(totalTimeMax);

		var ScoreString = "";
		if (this.FinalMaxScore > 0)
		{
			ScoreString = MyRound(totalScore * this.FinalMaxScore / scoreMax);
			scoreMax = this.FinalMaxScore;
		}
		else
		{
			ScoreString = MyRound(totalScore);
		}

		str += ' <tr> ';
		str += '  <td class="ScoreTotal">Total</td>';

		str += '  <td class="ScoreTotal"><span dir="ltr">' + MyRound(ScoreString) + '/' + MyRound(scoreMax) + '</span></td>';

		if(oneQuestNotTimeLimited && !this.IsGlobalTimer)
		{
			str += '<td class="ScoreTotal"><span dir="ltr">' + this.CalculateTimeString(totalTime) + '</span></td>';
			str += '<td width="120" class="ScoreTotal">&nbsp;</td>';
		}
		else
		{
			str += '<td class="ScoreTotal"><span dir="ltr">' + this.CalculateTimeString(totalTime) + '</span>/<span dir="ltr">' + this.CalculateTimeString(totalTimeMax) + '</span></td>';

			str += '<td width="120" class="ScoreTotal">' + CreatBarreHisto(this.imagetab[3], this.imagetab[4], totalTime / totalTimeMax, 100, 8) + '</td>';
		}

		str += ' </tr>';
		str += '</table>';

		return str;
	};

	this.ShowScoreComments = function()
	{
		var totalScore = 0;
		var scoreMax = 0;

		for(var i = 0; i < this.GetLimit(); i++)
		{
			if(this.tabScore[i] != undefined)
			{
				totalScore += this.tabScore[i].value;
				scoreMax += this.tabScore[i].max;
			}
		}

		var ScorePerCent = MyRound(totalScore * 100 / scoreMax);

		for (var j = 0; j < this.tabScoreComments.length; j++)
		{
			var fromScore = this.tabScoreComments[j].from;
			var toScore = this.tabScoreComments[j].to;

			if (((fromScore < ScorePerCent) && (ScorePerCent <= toScore)) ||
					(fromScore == 0 && ScorePerCent == 0))
			{
				var str = "";

				str += '<br><br><table width="100%" border="0"  cellspacing="0" cellpadding="0" class="ScoreComment">';
				str += ' <tr> ';
				str += '  <td class="ScoreComment">'+this.tabScoreComments[j].comment+'</td>';
				str += ' </tr>';
				str += '</table>';

				return str;
			}
		}

		return "";
	};

	this.AddScoreComment = function(commentIndex, comment, from, to)
	{
		this.tabScoreComments[commentIndex] = new ScoreComment();
		this.tabScoreComments[commentIndex].comment = comment;
		this.tabScoreComments[commentIndex].from = from;
		this.tabScoreComments[commentIndex].to = to;
	};
}

// -----------------------------------------------------------------------