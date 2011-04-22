<?php 
		session_name(ereg_replace("[^[:alnum:]]", "", dirname($_SERVER['PHP_SELF'])));
		session_start();
		
		if (!isset($_SESSION['QuizzAICCInfo']))		
			$_SESSION['QuizzAICCInfo'] = array();
			
		$_SESSION['QuizzAICCInfo']['RawData'] = $_REQUEST;
		
?><html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Expressions</title>
<meta name="Generator" content="Epistema EasyQuizz"><script type="text/javascript" language="JavaScript" src="_scripts/language.js"></script><script type="text/javascript" language="JavaScript" src="_default_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_default_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_lang/en.js"></script><script type="text/javascript" language="JavaScript" src="_scripts/scorm.js"></script><meta http-equiv="imagetoolbar" content="no">
</head>
<frameset rows="100%,0,0" cols="*" frameborder="NO" framespacing="0" border="0">
<frame name="mainframe" src="_ManagerFrame.php">
<frame name="resultsframe" src="_ResultsFrame.php">
<frame name="AiccPostFrame" src="_images/transparentpixel.gif">
<noframes>
<body><span lang="fr">G&eacute;n&eacute;r&eacute; avec <b>Epistema <a href="http://www.epistema.com/">Easyquizz</a></b> le 31.03.2011.</span><span lang="en">Built with <b>Epistema <a href="http://www.epistema.com/">Easyquizz</a></b> on 31.03.2011.</span></body>
</noframes>
</frameset><!--Easyquizz Version 2.7.2.0-->
</html>
