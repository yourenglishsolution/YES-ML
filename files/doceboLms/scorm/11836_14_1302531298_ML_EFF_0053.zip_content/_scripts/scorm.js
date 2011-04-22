// SCORM functions

// local variable definitions used for finding the API
var apiHandle = null;
var API = null;
var findAPITries = 0;

// local variable used to keep from calling LMSFinish more than once
var finishCalled = 0;

var StartTime = new Date();

/*******************************************************************************
**
** Function findAPI(win)
** Inputs: win - a Window Object
** Return: If an API object is found, it's returned; otherwise, null is returned
**
** Description:
** This function looks for an object named API in parent and opener windows
**
*******************************************************************************/
function findAPI(win)
{
	while ((win.API == null) &&
				 (win.parent != null) &&
				 (win.parent != win))
	{
		findAPITries++;

		// Note: 7 is an arbitrary number, but should be more than sufficient
		if (findAPITries > 7)
		{
//			alert("Error finding API -- too deeply nested.");
			return null;
		}

		win = win.parent;
	}

	return win.API;
}

/*******************************************************************************
**
** Function getAPI()
** Inputs: none
** Return: If an API object is found, it's returned; otherwise, null is returned
**
** Description:
** This function looks for an object named API, first in the current window's
** frame hierarchy and then, if necessary, in the current window's opener window
** hierarchy (if there is an opener window).
**
*******************************************************************************/
function getAPI()
{
	var theAPI = null;

	try
	{
		theAPI = findAPI(window);
		if ((theAPI == null) &&
				(window.opener != null) &&
				(typeof(window.opener) != "undefined"))
		{
			theAPI = findAPI(window.opener);
		}
	}
	catch (e)
	{}

	if (theAPI == null)
	{
//		alert("Unable to find an API adapter");
	}

	return theAPI
}

/******************************************************************************
**
** Function getAPIHandle()
** Inputs: None
** Return: value contained by APIHandle
**
** Description:
** Returns the handle to API object if it was previously set;
** otherwise, it returns null
**
*******************************************************************************/
function getAPIHandle()
{
	if (apiHandle == null)
	{
		apiHandle = getAPI();
	}

	return apiHandle;
}

/**********************************************************************
**
** Function: doLMSInitialize()
** Inputs: None
** Return: CMIBoolean true if the initialization was successful or
** CMIBoolean false if the initialization failed.
**
** Description:
** Initialize communication with LMS by calling the LMSInitialize
** function, which will be implemented by the LMS.
**
**********************************************************************/
function doLMSInitialize()
{
	var api = getAPIHandle();
	if (api == null)
	{
//		alert( "Unable to locate the LMS's API Implementation.\n" +	"LMSInitialize was not successful.");
		return "false";
	}

	var result = api.LMSInitialize("");
	if (result.toString() != "true")
	{
		// may want to do some error handling
	}

	return result.toString();
}

/**********************************************************************
**
** Function doLMSFinish()
** Inputs: None
** Return: CMIBoolean true if successful
** CMIBoolean false if failed.
**
** Description:
** Close communication with LMS by calling the LMSFinish
** function, which will be implemented by the LMS
**
**********************************************************************/
function doLMSFinish()
{
	var api = getAPIHandle();
	if (api == null)
	{
//		alert( "Unable to locate the LMS's API Implementation.\n" + "LMSFinish was not successful.");
		return "false";
	}
	else
	{
		// call LMSFinish only if it was not previously called
		if ( ! finishCalled )
		{
			finishCalled = 1;
			// call the LMSFinish function that should be implemented by the API
			var result = api.LMSFinish("");

			if (result.toString() != "true")
			{
				// may want to do some error handling
			}
		}
	}
	return result.toString();
}

/*******************************************************************************
**
** Function doLMSGetValue(name)
** Inputs: name - string representing the cmi data model defined category or
** element (e.g. cmi.core.student_id)
** Return: The value presently assigned by the LMS to the cmi data model
** element defined by the element or category identified by the name
** input value.
**
** Description:
** Wraps the call to the LMS LMSGetValue method
**
*******************************************************************************/
function doLMSGetValue(name)
{
	var api = getAPIHandle();

	if (api == null)
		return "";

	var value = api.LMSGetValue(name);
	var errCode = api.LMSGetLastError().toString();

	if (errCode == "0")
		return value.toString();

	// may want to do some error handling
	return "";
}

/*******************************************************************************
**
** Function doLMSSetValue(name, value)
** Inputs: name -string representing the data model defined category or element
** value -the value that the named element or category will be assigned
** Return: CMIBoolean true if successful
** CMIBoolean false if failed.
**
** Description:
** Wraps the call to the LMS LMSSetValue function
**
*******************************************************************************/
function doLMSSetValue(name, value)
{
	var api = getAPIHandle();
	if (api == null)
		return;

	var result = api.LMSSetValue(name, value);

	if (result.toString() != "true")
	{
		// may want to do some error handling
	}

	api.LMSCommit("");

	return;
}

/**********************************************************************
**
** Function normalExit()
** Inputs: None
** Return: None
**
** Description:
** Makes the appropriate calls for a normal exit calling LMSFinish and
** setting cmi.core.exit to "" for a normal exit
**
**********************************************************************/
function normalExit()
{
	// do not call a set after finish was called
	if ( ! finishCalled )
	{
		doLMSSetValue("cmi.core.exit", "");
	}

	doLMSFinish();
}

function sendTime()
{
	var time = new Date(new Date().getTime() - StartTime.getTime());
	var hh = time.getUTCHours();
	var mm = time.getUTCMinutes();
	var ss = time.getUTCSeconds();

	if (hh < 10) hh = '0' + hh;
	if (mm < 10) mm = '0' + mm;
	if (ss < 10) ss = '0' + ss;

	var TimeString = hh + ":" + mm + ":" + ss;

	doLMSSetValue("cmi.core.session_time", TimeString);
}

function sendScore(score, min, max)
{
	// NB : toString is necessary to keep float values from being rounded.
	doLMSSetValue("cmi.core.score.raw", score.toString());
	doLMSSetValue("cmi.core.score.min", min.toString());
	doLMSSetValue("cmi.core.score.max", max.toString());

	doLMSSetValue("cmi.core.lesson_status","completed");
}

function sendScoreMinMax(min, max)
{
	doLMSSetValue("cmi.core.score.min", min.toString());
	doLMSSetValue("cmi.core.score.max", max.toString());
}

/**********************************************************************
**
** Function abnormalExit()
** Inputs: None
** Return: None
**
** Description:
** Makes the appropriate calls for an abnormal exit calling LMSFinish
** and setting cmi.core.exit to suspend
**
**********************************************************************/
function abnormalExit()
{
	// do not call a set after finish was called
	if ( ! finishCalled )
	{
		doLMSSetValue( "cmi.core.exit", "suspend" );
	}

	doLMSFinish();
}


getAPI();
doLMSInitialize();
