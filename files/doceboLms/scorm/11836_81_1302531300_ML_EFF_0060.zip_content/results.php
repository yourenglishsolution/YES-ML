<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><?php 
	$GLOBALS['authorEmail'] = "";

 

	class QuestionDisplayer extends QuestionDisplayerBase
	{
		function QuestionDisplayer()
		{
			$this->QuestionDisplayerBase();
			$this->QuestionType = 'result_page';
		}

		function DisplayPage()
		{
?><html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><span epiLang="ResultsString"></span></title>
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Droid+Sans|Droid+Serif|Droid+Sans+Mono">
<link rel="stylesheet" type="text/css" href="_ressources/reset.css" media="all">
<link rel="stylesheet" type="text/css" href="_ressources/style.css" media="all"><script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script><script src="_ressources/javascripts/jquery.formalize.js"></script><link rel="stylesheet" href="_images/default_css.css" type="text/css"><script type="text/javascript" language="JavaScript" src="_scripts/prototype.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/language.js"></script><script type="text/javascript" language="JavaScript" src="_default_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_default_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/OpenPopupImage.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/InlineMedia.js"></script><script type="text/javascript" language="JavaScript">
		
		var AvailableMedia = new Array();
		
		AvailableMedia[0] = "B1_W12_D5_sew1.mp3";
		
		AvailableMedia[1] = "B1_W12_D5_sew2.mp3";
		
		AvailableMedia[2] = "B1_W6_D3_pond1.mp3";
		
		AvailableMedia[3] = "B1_W6_D3_pond2.mp3";
		

		var AvailableReducedImages = new Array();
		</script><meta http-equiv="imagetoolbar" content="no">
</head>
<body dir="ltr"><script type="text/javascript" language="JavaScript">
  // ***** Record and Compare configuration *****

  // Lock applets
  var lockApplets = true;

  // Submit page only when all recordings have been recorded and listened
  var checkSubmit = false;

  // ********************************************
</script><div id="core">
</div>
<div id="cntrls">
<div id="cntrls_c">
<div id="cntrls_dcrs"></div>
<div id="cntrls_btns"><a id="see_corrections_button" class="see_corrections_button" href="javascript:document.navigationform.submit();"><img src="_images/img_answers.png" align="middle" border="0"></a></div>
</div>
</div>
<div id="rslt">
<p><?php 
	$this->EchoResultDataAndSendAICC(true, 100, false, false, true, false);	
?><br></p>
<div id="recordandcompare_results"></div>
</div><script type="text/javascript" language="JavaScript">
bDontScrollDraggables = true;
</script><script type="text/javascript" language="JavaScript">
	EpiLangManager.TranslatePage(document) ;
</script></body>
</html><?php 
		} // DisplayPage
	}; // class
	
	$PageDisplay = new QuestionDisplayer();
	
?>
