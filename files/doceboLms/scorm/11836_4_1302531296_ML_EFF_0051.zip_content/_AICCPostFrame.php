<?php 

	if (isset($_POST['PostNow']))
	{	
		include_once("_ressources/inc.manager.php");
		$quizz = new QuizzManagerBase();
	}

	?><html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="Generator" content="Epistema EasyQuizz"><script type="text/javascript" language="JavaScript" src="_scripts/language.js"></script><script type="text/javascript" language="JavaScript" src="_default_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_default_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_lang/en.js"></script><meta http-equiv="imagetoolbar" content="no">
</head>
<body><?php 

	if (isset($_POST['PostNow']))
	{	
		$quizz->SendAICC(false);
	}

	?>
</body><script type="text/javascript" language="JavaScript">
	EpiLangManager.TranslatePage(document) ;
</script></html>
