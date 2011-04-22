// ========================= Copyright Epistema 2002 ========================
//
// Bar.js
//
// Progress bar
//
// ==========================================================================

// =======================================================================
// CountDown
// =======================================================================
function CountDown(Name)
{
	this.name = Name;
	this.timeLeft = 0;
	this.timeTotal = 0;

	this.Decrement = function()
	{
		if( this.OnTick != null)
			this.OnTick();

		this.timeLeft = Math.round ( (this.timeLeft - 0.5) * 100 )/100;

		if(this.timeLeft >= 0)
		{
			this.timer = setTimeout(this.name+".Decrement()", 500);
		}
		else
		{
			clearTimeout(this.timer);
			this.timeLeft = 0;

		  if( this.OnTimer != null)
				this.OnTimer();
		}
	};

	this.Start = function(TimeLeft, TimeMaxInSec, NumbOfTick)
	{
		// IE don't know undefined
		if (document.all) {
			// IE
			var undefined = null;
		}

		if(NumbOfTick == undefined)
			NumbOfTick = TimeLeft;

		this.periode = (TimeLeft / NumbOfTick) * 1000;
		this.timeLeft = TimeLeft;
		this.timeTotal = TimeMaxInSec;

		this.timer = setTimeout(this.name+".Decrement()", 1);
	};

	this.Stop = function()
	{
		clearTimeout(this.timer);
	};

	this.GetElapsedTime = function()
	{
	  return this.timeTotal - this.timeLeft;
	};

	// overridable
	this.OnTimer = function()
	{
		alert("Time expired");
	};

	this.OnTick = function()
	{
		alert("tick " + this.timeLeft + "s");
	};
}

// -----------------------------------------------------------------------
// Name : Le nom de la barre
// Width : Largeur
// Height : Hauteur
// Max : Valeur maximale que pourra prendre la barre
// ImgPrefix : Le prefix de l'image avec le sous répertoire
// ImgExt : Extension des images (.gif, .jpg, .png ...)
// NbImg : le nombre d'image disponible
// -----------------------------------------------------------------------
function Bar(Name, Width, Height, Max, imageOn, imageOff, imageHalfOn, imageHalfOff, isGlobal, strTimerFinished)
{
	this.name = Name;
	this.width = Width;
	this.height = Height;
	this.max = Max;
	this.strTimerFinished = strTimerFinished;

	this.isGlobal = isGlobal;

	this.imageOn = imageOn;
	this.imageOff = imageOff;
	this.imageHalfOn = imageHalfOn;
	this.imageHalfOff = imageHalfOff;

	this.CalculateTimeString = function(timeInSec)
	{
		if (timeInSec < 60)
		{
			return timeInSec + " " + EpiLang.sec;
		}

		var Minutes = Math.floor(timeInSec / 60);
		timeInSec = timeInSec % 60;

		if (Minutes < 60)
			return '<span dir="ltr">' + Minutes + ":" + timeInSec + '</span>';

		var Hours = Math.floor(Minutes / 60);
		Minutes = Minutes % 60;

		return '<span dir="ltr">' + Hours + ":" + Minutes + ":" + timeInSec + '</span>';
	};

	this.CalcRealPos = function(pos)
	{
		realpos = Math.round ( (this.width / this.max) * pos );

		if ( realpos > this.width )
			realpos = this.width;

		if( realpos <= 0 )
			realpos = 1;

		return (realpos < 10) ? ("0" + realpos) : ("" + realpos);
	};

	this.Build = function(HTMLBlock, pos, strGlobalTime, strQuestionTime)
	{
		var realpos = this.CalcRealPos(pos);

		var imageOn = this.imageOn;
		var imageOff = this.imageOff;

		if (pos == Math.round(this.max/2))
		{
			imageOn = this.imageHalfOn;
			imageOff = this.imageHalfOff;
		}

		str =  '<table class="timerbar" id="ProgressBarDiv'+this.name+'" border="0" cellspacing="0" cellpadding="0">';

		str += ' <tr>';

		if (this.isGlobal)
			str += '  <td nowrap class="timerbar">' + strGlobalTime + '</td>';
		else
			str += '  <td nowrap class="timerbar">' + strQuestionTime + '</td>';

		str += ' </tr>';


		str += ' <tr>';
		str += '  <td nowrap class="timerbar" id="ProgressBarText'+this.name+'"></td>';
		str += ' </tr>';
		str += ' <tr>';
		str += '  <td width="' + (this.width + 20) + '" class="timerbar"><img src="' + imageOn +'" name="ImgBarOn'+this.name+'" width="'+realpos+'" height="'+this.height+'"><img src="' + imageOff +'" name="ImgBarOff'+this.name+'" width="'+(this.width-realpos)+'" height="'+this.height+'"></td>';
		str += ' </tr>';
		str += '</table>';

		if (HTMLBlock)
			HTMLBlock.innerHTML = str;

		this.barOn = eval("document.ImgBarOn"+this.name);
		this.barOff = eval("document.ImgBarOff"+this.name);
	};

	this.SetText = function(str)
	{
		var id = "ProgressBarText"+this.name;

		if (document.getElementById)
		{
			x = document.getElementById(id);
			x.innerHTML = '';
			x.innerHTML = str;
		}
		else if (document.all)
		{
			x = document.all[id];
			x.innerHTML = str;
		}
	};

	this.SetMax = function(newMax)
	{
	  this.max = Number(newMax);

		this.SetText(this.CalculateTimeString(newMax));
	};

	this.SetPosition = function(pos)
	{
		// Calculate real position
		var realpos = this.CalcRealPos(pos);

		var imageOn = this.imageOn;
		var imageOff = this.imageOff;

		if (pos == Math.round(this.max/2))
		{
			imageOn = this.imageHalfOn;
			imageOff = this.imageHalfOff;
		}

		if (this.barOn.src != imageOn) this.barOn.src = imageOn;
		if (this.barOff.src != imageOff) this.barOff.src = imageOff;

		this.barOn.width = realpos;
		this.barOff.width = this.width - realpos;

		if (pos == 0)
		{
		  this.SetText(this.CalculateTimeString(this.max) + "<br>" + this.strTimerFinished);
			this.barOn.width = 0;
			this.barOff.width = 0;
		}
		else
		{
			  this.SetText(this.CalculateTimeString(Math.round(pos)) + "/" + this.CalculateTimeString(this.max));
		}
	};

	this.SetVisible = function(bVisible)
	{
		// Show/Hide functions for pointer objects
		if (document.getElementById)
		{
			if (bVisible)
			{
				document.getElementById("ProgressBarText"+this.name).style.visibility = "visible";
				document.getElementById("ProgressBarDiv"+this.name).style.visibility = "visible";
				document.getElementById("timerBloc").style.visibility = "visible";
			}
			else
			{
			  document.getElementById("ProgressBarText"+this.name).style.visibility = "hidden";
			  document.getElementById("ProgressBarDiv"+this.name).style.visibility = "hidden";
			  document.getElementById("timerBloc").style.visibility = "hidden";
			}
		}
	};
}