// ========================= Copyright Epistema 2005 ========================
//
// OpenPopupImage.js
//
// Opens an image centered
//
//
// ==========================================================================


function showPopupImage(image, width, height)
{
	var url = image;

	if (url.indexOf('_images/') == -1)
		url = '_images/' + url;

	var name = 'PopupWindow';

	// make place for the scrollbars
	width = Number(width) + 30;
	height = Number(height) + 40;

	if (width == 30) width = 500;
	if (height == 40) height = 300;

	var params = 'status=no,toolbar=no,menubar=no,location=no,resizable=yes,scrollbars=no';
	var win = null;

	if (window.showModelessDialog)
	{
		var winParms = "dialogWidth:" + width + "px;dialogHeight:" + height + "px;help:no";

		if (params)
		{
			params = params.replace(/=/ig, ":");
			params = params.replace(/,/ig, ";");

			winParms += ";" + params;
		}

		win = window.showModelessDialog(url, window, winParms);
	}
	else
	{
		var left = Math.floor( (screen.width - width) / 2);
		var top = Math.floor( (screen.height - height) / 2);
		var winParms = "top=" + top + ",left=" + left + ",height=" + height + ",width=" + width;

		if (params)
			winParms += "," + params;

		win = window.open(url, name, winParms);

		if (parseInt(navigator.appVersion) >= 4)
			win.window.focus();
	}

	var re;
	re = /swf$/i;

	if (!url.match(re))
	{
		if (win)
			win.setTimeout("window.document.onclick=function() { window.close(); };", 500);
	}
}