// Record & Compare

if (typeof lockApplets == "undefined")
	lockApplets = false;

if (typeof checkSubmit == "undefined")
	checkSubmit = false;

var rncs = 0;
var rnc_filled = 0;
var page = 0;

function LockApplets(i)
{
	if (!lockApplets) return;

	var allApplets = $$('.recordAndCompareApplet')
		allApplets.each(function(anApplet) {
			if(anApplet.name != i)
				anApplet.Lock();
		});	
}

function UnlockApplets()
{
	if (!lockApplets) return;

	var allApplets = $$('.recordAndCompareApplet')
		allApplets.each(function(anApplet) {
			anApplet.Unlock();
		});		
}


function RnCRecord()
{
	rnc_filled++;
}

function playRnC(i)
{
	if (window.parent.played[i] == null || window.parent.played[i] == false)
		window.parent.played['length']++;
	window.parent.played[i] = true;	
}


function isValid(i)
{
	return (window.parent.buffer[i] && window.parent.buffer[i] == "Y");
}


function addBuffer(i)
{
	window.parent.buffer[i] = 'Y';
	if (window.myPageName)
		window.parent.rnccaption[i] = myPageName;
	else
		window.parent.rnccaption[i] = window.document.title;
}


function ProcessElementForRecordAndCompare(el)
{
	if (el.childNodes.length == 0)
		return;

	for (var childindex = el.childNodes.length - 1; childindex >= 0; childindex--)
	{
		var child = el.childNodes[childindex];

		if (child.nodeType == 1 && child.nodeName == 'SELECT')
		{
			// Do nothing - do not parse the inside of a select
		}
		else if (child.nodeType == 1) // element node
			ProcessElementForRecordAndCompare(child);
		else if (child.nodeType == 3) // text node
		{
			var str = child.nodeValue.escapeHTML();
			var regexp = new RegExp("\\[\\[rnc:([^\\]]*)\\]\\]", 'gi');

			while ((match = regexp.exec(str)) != null)
			{
				var s = match[0];

				if (match[1] != null)
					var mode = match[1];
				else
					var mode = "";

				var rnc_index = 'page'+page+'_'+rncs;
				if (mode == "record_save" && window.parent.buffer[rnc_index] != null)
					rnc_filled++;

				if (!IsInCorrection)
				{
					str = str.replace(s,	   '<span class="record_and_compare" id="'+rnc_index+'"></span>'
								  +'<!--[if IE]>'
								  +'<object classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93" width="200" height="20" name="'+rnc_index+'" class="recordAndCompareApplet">'
								  +'	<param name="java_code"      value="audioapplet.AudioFrame.class" />'
								  +'	<param name="java_codebase"  value="_ressources/_recordandcompare/" />'
								  +'	<param name="java_archive"   value="audiocomponent.jar" />'
								  +'	<param name="java_mayscript" value="true" />'
								  +'	<param name="type"           value="application/x-java-applet;version=1.4" />'
								  +'<![endif]-->'
								  +'<!--[if !IE]> -->'
								  +'<object '
								  +'	name="'+rnc_index+'"'
								  +'	classid="java:audioapplet.AudioFrame.class"'
								  +'        type="application/x-java-applet;version=1.4"'
								  +'        archive="audiocomponent.jar"'
								  +'        height="20"'
								  +'        width="200"'
								  +'        class="recordAndCompareApplet"'
								  +'        mayscript="true">'
								  +'	<param name="mayscript" value="true" />'
								  +'	<param name="archive"   value="audiocomponent.jar" />'
								  +'	<param name="codebase"  value="_ressources/_recordandcompare/" />'
								  +'<!--<![endif]-->'
								  +'	<param name="mode" value="'+mode+'"/>'
								  +'	<param name="record_and_compare_id" value="'+rnc_index+'" />'
								  +'</object>');
				}
				else
				{
					str = str.replace(s, '');
				}

				rncs++;
			}

			if (child.ownerDocument.createRange)
			{
				var range = child.ownerDocument.createRange();
				range.selectNodeContents(child);
				child.parentNode.replaceChild(
					range.createContextualFragment(str), child);
			}
			else
			{
				child.nodeValue = '';
				var temp = document.createElement("SPAN");
				temp = child.parentNode.insertBefore(temp, child);
				str = str.replace(/^ /, '&nbsp;');
				str = str.replace(/ $/, '&nbsp;');
				temp.outerHTML = str;
			}
		}
	}
}

function textareasToDivs(el)
{
	var textareas = $A(el.getElementsByTagName('textarea'));

	textareas.each(function(elt)
		{
		  if (elt.name != '')
			  return;

			var textareaClassName = elt.classNames();

			var sPreserve = elt.value;
			var oNewNode = $(document.createElement("DIV"));

			var par = elt.parentNode;
			par.replaceChild(oNewNode, elt);

			oNewNode.innerHTML = sPreserve;
			oNewNode.addClassName(textareaClassName);
		}
	);
}

function createBufferObject()
{
	if (window.parent.buffer == null)
		window.parent.buffer = new Object();

	if (window.parent.rnccaption == null)
		window.parent.rnccaption = new Object();

	if (window.parent.played == null)
		window.parent.played = new Object();

	window.parent.played['length'] = 0;
}


function showResults()
{

	var result_div = document.getElementById('recordandcompare_results');
	if (result_div == null) return;

	if (window.parent.buffer == null) return;

	if (window.parent.buffer.length == 0)
		return;

	var str = '';

	str += "<p>Listen to your recordings.</p>";
	var i=1;
	str += '<table><tr><td valign="top">';
	str += '<select onchange="displayRnC(this, $(\'rnc_div\'))">';
	str += '<option value="">Choose one of your recordings</option>';

	var prev_page = '';
	var j = 1;
	for (index in window.parent.buffer)
	{
		if (prev_page != window.parent.rnccaption[index])
		{
			str += '<option value="" disabled>'+window.parent.rnccaption[index]+'</option>';
			prev_page = window.parent.rnccaption[index];
			j = 1;
		}

		str += '<option value="'+index+'">&nbsp;&nbsp;Recording '+j+'</option>';
		i++;
		j++;
	}
	str += '</select></td><td>';

	str += '<div id="rnc_div"></div></td></tr></table>';

	if (i > 1)
		result_div.innerHTML = str;
}

function displayRnC(el, div)
{
	if (el.value != '')
	{
		var index = el.value;	
						
		var str =  '<!--[if IE]>'
			  +'<object classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93" width="200" height="20" name="'+index+'" class="recordAndCompareApplet">'
			  +'	<param name="java_code"      value="audioapplet.AudioFrame.class" />'
			  +'	<param name="java_codebase"  value="_ressources/_recordandcompare/" />'
			  +'	<param name="java_archive"   value="audiocomponent.jar" />'
			  +'	<param name="java_mayscript" value="true" />'
			  +'	<param name="type"           value="application/x-java-applet;version=1.4" />'
			  +'<![endif]-->'
			  +'<!--[if !IE]> -->'
			  +'<object '
			  +'	name="'+index+'"'
			  +'	classid="java:audioapplet.AudioFrame.class"'
			  +'        type="application/x-java-applet;version=1.4"'
			  +'        archive="audiocomponent.jar"'
			  +'        height="20"'
			  +'        width="200"'
			  +'        class="recordAndCompareApplet"'
			  +'        mayscript="true">'
			  +'	<param name="mayscript" value="true" />'
			  +'	<param name="archive"   value="audiocomponent.jar" />'
			  +'	<param name="codebase"  value="_ressources/_recordandcompare/" />'
			  +'<!--<![endif]-->'
			  +'	<param name="mode" value="play_only"/>'
			  +'	<param name="record_and_compare_id" value="'+index+'" />'
			  +'</object>';						
	}
	else
	{
		var str = '';
	}
	div.innerHTML = str;
}


function RnCCanSubmit()
{
	if (!checkSubmit) return true;

	var checkboxes = $$('input[type="checkbox"]');
	if (checkboxes.length > 0) /* QCM */
	{
		for (index in checkboxes)
		{
			var chbx = checkboxes[index];
			if (chbx.checked)
			{
					var tr = chbx.up('tr', 0);
					var tmp_rncs = tr.getElementsByClassName('record_and_compare');
					if (tmp_rncs.length > 0)
					{
						for (rnc_i in tmp_rncs)
						{
							var tmp_rnc_id = tmp_rncs[rnc_i].id;
							if (tmp_rnc_id && !window.parent.played[tmp_rnc_id])
								return false;
						}
					}
			}
		}
		return true;
	}
	else
	{
		var radios = $$('input[type="radio"]');
		if (radios.length > 0) /* QCU */
		{
			for (index in radios)
			{
				var rad = Element.extend(radios[index]);
				if (rad.checked)
				{
					var tr = rad.up('tr', 0);
					var tmp_rncs = tr.getElementsByClassName('record_and_compare');
					if (tmp_rncs.length > 0)
					{
						for (rnc_i in tmp_rncs)
						{
							var tmp_rnc_id = tmp_rncs[rnc_i].id;
							if (tmp_rnc_id && !window.parent.played[tmp_rnc_id])
								return false;
						}
					}
				}
			}
			var ind = 0;
			return true;
		}
		else /* other cases */
		{
			return rncs == window.parent.played['length'];
		}
	}
	return true;
}

Event.observe(window, 'load', function() {
	var MyManagerFrame = window.parent.frames['managerframe'];
	if(MyManagerFrame != null)
	{
		if (MyManagerFrame.QuizzManag != null)
			page = MyManagerFrame.QuizzManag.GetPageNumber();
	}
	else // PHP version
		if (document.getElementsByName('PageNumber') != null)
			page = document.getElementsByName('PageNumber')[0].value;

	createBufferObject();

	$$('.ProcessInlineImages').each(ProcessElementForRecordAndCompare);
	$$('.ProcessInlineImages').each(textareasToDivs);

	showResults();

	if (rncs > 0 && !IsInCorrection)
	{
		var old_submit = myQuizz.submit;
		myQuizz.submit = function()
		{
			if (RnCCanSubmit())
				old_submit.call(myQuizz);

		}
	}
});

