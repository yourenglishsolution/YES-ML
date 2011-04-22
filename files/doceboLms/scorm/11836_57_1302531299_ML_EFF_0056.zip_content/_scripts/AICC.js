// ========================= Copyright Epistema 2002 ========================
//
// AICC
//
// Returns the AICC information to the LMS
//
// ==========================================================================


// -----------------------------------------------------------------------
// Get a parameter from a url
// return null if the parameter doesn't exist
// -----------------------------------------------------------------------
function GetParameter(url,param)
{
	param += "=";

	var pos1 = String(url).toLowerCase().indexOf(String(param).toLowerCase());

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

function GetAICC_URL() {
	var url = unescape( window.parent.location.toString() );
	return GetParameter( url, "aicc_url");
}

function GetAICC_SID() {
	var url = unescape( window.parent.location.toString() );
	return GetParameter( url, "aicc_sid");
}

function GetAICC_REFID() {
	var url = unescape( window.parent.location.toString() );
	return GetParameter( url, "aicc_refid");
}

function CheckScore(score, max, min) {
	if( score >= 0 && (score < min) || (score > max) ) {
		alert("_AiccPutParamRequest_SetScore : invalid score <score="+score+", min="+min+", max="+max+">");
		return false;
	}

	return true;
}

function IntTo2DigitString(a)
{
  if (a < 10) return "0"+a;
  return a;
}

// =======================================================================
// Class AiccPutParamRequest
// =======================================================================

function AiccPutParamRequest(maxScore)
{
	this.LessonStatus = "";
	this.Score = "";
	this.Time = "";
	this.MaxScore = maxScore;

	// Internal variables
	this._score = 0;
	this._min = 0;
	this._max = 0;

	this._time = 0;

	// Methods
	this.SetLessonStatus = function ( strStatus )
	{
		// TODO : check parameter
		this.LessonStatus = strStatus;
	};

	// -----------------------------------------------------------------------
	// SetScore(5,10,0) -> "5,10,0"
	// SetScore(15,50) -> "5,50,0"
	// SetScore(15) -> "15,100,0"
	// -----------------------------------------------------------------------
	this.SetScore = function(score, max, min)
	{
		// IE don't know undefined
		if (document.all) {
			// IE
			var undefined = null;
		}

		if(max == undefined)
			max = 100;

		if(min == undefined)
			min = 0;

		CheckScore(score,max,min);

		if (this.MaxScore > 0)
		{
			if (max > 0)
				score = score * this.MaxScore / max;
			else
				score = 0;

			max = this.MaxScore;
		}

		this._score = score;
		this._min = min;
		this._max = max;

		this.Score = (MyRound(score) + "," + max + "," + min);
	};

	this.AddScore = function(score, max, min)
	{
		// IE don't know undefined
		if (document.all) {
			// IE
			var undefined = null;
		}

		if(max == undefined)
			max = 100;

		if(min == undefined)
			min = 0;

		CheckScore(score,max,min);

		this._score += score;

		if( min < this._min)
			this._min = min;

		this._max += max;

		this.Score = (MyRound(this._score) + "," + this._max + "," + this._min);
	};

	// -----------------------------------------------------------------------
	// time est du type Date()
	// Attention : ne tient compte que du format HH:MM:SS et non [HH]HH:MM:SS
	// -----------------------------------------------------------------------
	this.SetTime = function(time)
	{
		var hh = time.getUTCHours();
		var min = time.getUTCMinutes();
		var ss = time.getUTCSeconds();

		this._time = hh * 3600 + min * 60 + ss;

		this.Time = IntTo2DigitString(hh) + ":" + IntTo2DigitString(min) +
			":" + IntTo2DigitString(ss);
	};

	this.AddTime = function(time)
	{
		var timeObj = new Date(Number(time));

		this._time += ( timeObj.getUTCHours() * 3600
									+ timeObj.getUTCMinutes() * 60
									+ timeObj.getUTCSeconds() );

		var t_hh = Math.floor( this._time / 3600 );
		var t_min = Math.floor( ( this._time - (t_hh * 3600) ) / 60 );
		var t_ss = this._time - (t_hh * 3600) - (t_min * 60);

		this.Time = IntTo2DigitString(t_hh) + ":" + IntTo2DigitString(t_min) +
			":" + IntTo2DigitString(t_ss);
	};

	this.Send = function()
	{
		// reset the score to maxscore:
		this.SetScore(this._score, this._max, this._min);

		var strAiccReq = "";
		strAiccReq += "[core]\r\n";
		strAiccReq += ( "Lesson_Status=" + this.LessonStatus + "\r\n" );
		strAiccReq += ( "Score=" + this.Score + "\r\n" );
		strAiccReq += ( "Time=" + this.Time + "\r\n" );

		document.SendAICCForm.aicc_data.value = strAiccReq;

		document.SendAICCForm.session_id.value = GetAICC_SID();
		document.SendAICCForm.ref_id.value = GetAICC_REFID();

		document.SendAICC_ExitAUForm.session_id.value = GetAICC_SID();

		try
		{
			if (window.top.opener && window.top.opener.name == "SyfSCORM")
				return; // If the opener is syfadis, dont send any AICC

			if (window.top.opener && window.top.opener.name == "LMSWindow")
			{
				document.SendAICCForm.target = window.top.opener.name;
				document.SendAICC_ExitAUForm.target = window.top.opener.name;
			}
		}
		catch (e)
		{}

		document.SendAICCForm.submit();

		if (this.LessonStatus == 'c')
			window.setTimeout("document.SendAICC_ExitAUForm.submit();", 500);
	};

	this.SendCoreLesson = function(strSerializedData)
	{
		var strAiccReq = "";
		strAiccReq += "[core_lesson]\r\n";
		strAiccReq += ( strSerializedData + "\r\n" );

		document.SendAICCForm.aicc_data.value = strAiccReq;

		document.SendAICCForm.session_id.value = GetAICC_SID();
		document.SendAICCForm.ref_id.value = GetAICC_REFID();

		document.SendAICC_ExitAUForm.session_id.value = GetAICC_SID();

		try
		{
			if (window.top.opener && window.top.opener.name == "SyfSCORM")
				return; // If the opener is syfadis, dont send any AICC

			if (window.top.opener && window.top.opener.name == "LMSWindow")
			{
				document.SendAICCForm.target = window.top.opener.name;
				document.SendAICC_ExitAUForm.target = window.top.opener.name;
			}
		}
		catch (e)
		{}

		document.SendAICCForm.submit();

		if (this.LessonStatus == 'c')
			window.setTimeout("document.SendAICC_ExitAUForm.submit();", 500);
	};
}
