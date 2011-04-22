/*
 * EpiLang
 * Copyright (C) Epistema
 *
 * File Name: language.js
 * 	Defines the EpiLang object that is used for language
 * 	operations.
 *
 */

var EpiLangManager = new Object() ;
var EpiLang =
	{
		EpiLangDir : "ltr"
	};

var EpiLangJS =
	{
	};

EpiLangManager.TranslateTags = function( targetDocument, tag, propertyToSet ) //propertyToSet: value or innerHTML
{
	var elementArray = targetDocument.getElementsByTagName(tag) ;

	for (var i = 0; i < elementArray.length; i++)
	{
		var sKey = elementArray[i].getAttribute('epiLang') ;

		if (sKey)
		{
			var s = EpiLang[sKey];

			if (s)
			{
				if (propertyToSet == 'value')
					elementArray[i].value = s;
				else if (propertyToSet == 'innerHTML')
					elementArray[i].innerHTML = s;
			}
		}
	}
}

EpiLangManager.TranslatePage = function(targetDocument)
{
	this.TranslateTags(targetDocument, 'INPUT',  'value');
	this.TranslateTags(targetDocument, 'SPAN',   'innerHTML');
	this.TranslateTags(targetDocument, 'LABEL',  'innerHTML');
	this.TranslateTags(targetDocument, 'OPTION', 'innerHTML');

	if (targetDocument.body.dir != EpiLang.EpiLangDir)
		targetDocument.body.dir = EpiLang.EpiLangDir;
}

