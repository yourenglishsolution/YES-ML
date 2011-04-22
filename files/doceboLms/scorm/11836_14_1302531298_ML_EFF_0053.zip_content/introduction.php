<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><?php 	
	class QuestionDisplayer extends QuestionDisplayerBase
	{
		function QuestionDisplayer()
		{
			$this->QuestionDisplayerBase();
			$this->QuestionType = 'introduction';
		}

		function DisplayPage()
		{
?><html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Review</title>
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Droid+Sans|Droid+Serif|Droid+Sans+Mono">
<link rel="stylesheet" type="text/css" href="_ressources/reset.css" media="all">
<link rel="stylesheet" type="text/css" href="_ressources/style.css" media="all"><script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script><script src="_ressources/javascripts/jquery.formalize.js"></script><link rel="stylesheet" href="_images/default_css.css" type="text/css"><script type="text/javascript" language="JavaScript" src="_scripts/prototype.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/language.js"></script><script type="text/javascript" language="JavaScript" src="_default_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_default_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/OpenPopupImage.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/InlineMedia.js"></script><script type="text/javascript" language="JavaScript">
		
		var AvailableMedia = new Array();
		
		AvailableMedia[0] = "B1_W11_D3_refund1.mp3";
		
		AvailableMedia[1] = "B1_W11_D3_refund2.mp3";
		
		AvailableMedia[2] = "B1_W11_D4_yacht1.mp3";
		
		AvailableMedia[3] = "B1_W11_D4_yacht2.mp3";
		
		AvailableMedia[4] = "B1_W11_D5_indulge1.mp3";
		
		AvailableMedia[5] = "B1_W11_D5_indulge2.mp3";
		
		AvailableMedia[6] = "B1_W12_D1_recollection1.mp3";
		
		AvailableMedia[7] = "B1_W12_D1_recollection2.mp3";
		

		var AvailableReducedImages = new Array();
		</script><meta http-equiv="imagetoolbar" content="no">
</head>
<body dir="ltr">
<form name="MyQuizzForm" action="_ManagerFrame.php" method="get"><input type="hidden" name="PageNumber" value="-1"><input type="hidden" name="Direction" value="1"><div id="core">
</div>
<div id="cntrls">
<div id="cntrls_c">
<div id="cntrls_dcrs"></div>
<div id="cntrls_btns">
<div id="btn_start"><a class="Start" id="Start" href="javascript:document.forms[0].submit();"><img src="_images/img_start.png" align="middle" border="0"></a></div>
</div>
</div>
</div>
<div id="intro">
<div class="intro_title">Review</div>
<p class="txt"><span class="description ProcessInlineImages" id="description">It's time for a little review.  Are you ready to impress us with your knowledge?  Let's start off with a Word of the Day, and then move straight on to the quiz.</span></p>
</div><script type="text/javascript" language="JavaScript">
bDontScrollDraggables = true;
</script></form><script type="text/javascript" language="JavaScript">
	EpiLangManager.TranslatePage(document) ;
</script></body>
</html><?php 
		} // DisplayPage
	}; // class
	
	$PageDisplay = new QuestionDisplayer();
	
?>
