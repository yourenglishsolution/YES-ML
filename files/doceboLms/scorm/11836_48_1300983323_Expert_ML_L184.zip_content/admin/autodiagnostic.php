<?
	session_name(ereg_replace("[^[:alnum:]]", "", dirname(dirname($_SERVER['PHP_SELF']))));
	session_start();

	include_once('data.php');
	
	// affiche un tableau avec des histogrammes linéaires des réponses ventilées par thèmes :
	function AfficheHistogrammes($AfficherLabels = false)
	{
		$ColorDistribution = array();
		$GrandTotal = array();
		foreach ($GLOBALS['Profiles'] as $aProfile)
			$GrandTotal[$aProfile['color']] = 0;
		
		foreach ($_SESSION['QuizzSession']['PageData'] as $k => $UserAnswer)
		{
			if (!isset($UserAnswer['Answers']['QCU']))
				continue;
				
			$ThisAnswer = $UserAnswer['Answers']['QCU'];
	
			$ThisThem = 	$GLOBALS['QuestionDefinition'][$k]['Theme'];
			if ($ThisThem != $CurrentTheme)
			{
				$CurrentTheme = $ThisThem;
				
				if (!isset($ColorDistribution[$CurrentTheme]))
				{
					$ColorDistribution[$ThisThem] = array();
					
					foreach ($GLOBALS['Profiles'] as $aProfile)
						$ColorDistribution[$ThisThem][$aProfile['color']] = 0;
				}
			}
			
			$ThisColorNumber = $GLOBALS['QuestionDefinition'][$k]['Profiles'][$ThisAnswer];
			$ThisProfile = $GLOBALS['Profiles'][$ThisColorNumber];
			
			$ThisColor = $ThisProfile['color'];
	
			$ColorDistribution[$ThisThem][$ThisColor] ++;
			$GrandTotal[$ThisColor]++;
		}
		
		echo '<table width="100%" border="0" cellspacing="0" cellpadding="2">' . "\n";
		
		foreach($ColorDistribution as $k => $aTheme)
		{
			echo '<tr><td width="40%"><b>'.$k.'</b></td><td align="right" width="60%">' . "\n";
			DisplayBar($aTheme, $AfficherLabels);
			echo '</td></tr>' . "\n";
		}
		
		echo '<tr><td>&nbsp;</td><td>&nbsp;<td></tr>' . "\n";
		echo '<tr><td width="40%"><b><u>Total</u></b></td><td align="right" width="60%">' . "\n";

		DisplayBar($GrandTotal, $AfficherLabels);

		echo '</td></tr>' . "\n";
				
		echo '</table>' . "\n";	
	}
	
	function DisplayBar($Colors, $AfficherLabels = false)
	{
		$total = 0;
		foreach ($Colors as $ColorCount)
			$total += $ColorCount;	
	
		if ($total == 0)
		{
			echo 'Pas de r&eacute;ponse';
			return;
		}
		
		echo '<table class="Histogram" width="100%" border="0" cellspacing="0" cellpadding="2"><tr>' . "\n";

		foreach ($GLOBALS['Profiles'] as $aProfile)
		{
			$ColorCount = $Colors[$aProfile['color']];
			$color = $aProfile['color'];
			
			if ($ColorCount == 0)
				continue;

			$percent = round($ColorCount * 100 / $total);

			$luminosity = HTMLColorLuminosity($color);
			
			$strPercent = $percent . '%';
			
			if ($AfficherLabels)
				$strPercent = htmlentities($aProfile['label']) . '&nbsp;: ' . $strPercent;

			if ($luminosity < 0.5)
				$strPercent = '<font color="white">'.$strPercent.'</font>';
			else
				$strPercent = $strPercent;

			echo '<td nowrap align="center" bgcolor="#'.$color.'" width="'.$percent.'%"><b>'.$strPercent. '</b></td>' . "\n";
		}
		echo '</tr></table>' . "\n";	
	}
	
	// affiche un tableau avec trois colonnes : l'intitulé de la question, la réponse,
	// et le commentaire assicié :
	function AfficheCommentaires()
	{
		$CurrentTheme = "No them";

		echo '<table id="Comments" width="100%" border="0" cellspacing="0" cellpadding="2">' . "\n";
		echo '<tr><th><b>QUESTION</b></th><th><b>REPONSE</b></th><th><b>COMMENTAIRE</b></th></tr>' . "\n";

		foreach ($_SESSION['QuizzSession']['PageData'] as $k => $UserAnswer)
		{
			if (!isset($UserAnswer['Answers']['QCU']))
				continue;

			$ThisAnswer = $UserAnswer['Answers']['QCU'];

			$ThisThem = 	$GLOBALS['QuestionDefinition'][$k]['Theme'];
			if ($ThisThem != $CurrentTheme)
			{
				$CurrentTheme = $ThisThem;
				echo '<tr><th colspan="3"><b>'.$ThisThem.'</b></th></tr>		' . "\n";
			}

			$ThisColorNumber = $GLOBALS['QuestionDefinition'][$k]['Profiles'][$ThisAnswer];
			$ThisProfile = $GLOBALS['Profiles'][$ThisColorNumber];

			$ThisColor = $ThisProfile['color'];

			echo '<tr>' . "\n";
			echo '<td valign="top">'.$GLOBALS['QuestionDefinition'][$k]['Question'].'</td>' . "\n";
			echo '<td align="center" valign="top"><font color="#'.$ThisColor.'">'.$GLOBALS['QuestionDefinition'][$k]['Answers'][$ThisAnswer].'</font></td>' . "\n";
			echo '<td valign="top"><font color="#'.$ThisColor.'">'.$GLOBALS['QuestionDefinition'][$k]['Comments'][$ThisAnswer].'</font></td>' . "\n";
			echo '</tr>' . "\n";
		}

		echo '</table>' . "\n";

	}

	// revoie la luminosité d'une couleur HTML
	function HTMLColorLuminosity($color)
	{
		$colorComp = hex2dec($color);

		$R = $colorComp['r'] / 255;
		$G = $colorComp['g'] / 255;
		$B = $colorComp['b'] / 255;

		$Cmax = max($R, $G, $B);
		$Cmin = min($R, $G, $B);

		// calculate luminosity
		$L = ($Cmax + $Cmin) / 2;

		return $L;
	}

	// Converti un code couleur HTML en sa composante RVB
	function hex2dec($hex) 
	{
		$color = str_replace('#', '', $hex);
		$ret = array('r' => hexdec(substr($color, 0, 2)),
								 'g' => hexdec(substr($color, 2, 2)),
								 'b' => hexdec(substr($color, 4, 2)));
	 	return $ret;
	}	
?>