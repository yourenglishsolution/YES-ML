<?

$epitoolsDir = dirname(str_replace('\\', '/', str_replace('_stats.php', '', __FILE__)));

include_once($epitoolsDir.'/_epitools.php');
include_once($epitoolsDir.'/_epiDBTools.php');
include_once($epitoolsDir.'/_epidate.php');

// histogram::setStatsURL('../epitools/stats/');

class histogram
{
	var $distribution;
	var $title;
	var $X_Title;
	var $Y_Title;
	var $max_height;

	function histogram($title, $X_Title, $Y_Title, $max_height = 80)
	{
		$this->distribution = array();
		$this->title = $title;
		$this->X_Title = $X_Title;
		$this->Y_Title = $Y_Title;
		$this->max_height = $max_height;
	}

	function setStatsURL($relativeURL)
	{
		$GLOBALS['Stats']['RelativeURL'] = $relativeURL;
	}

	function display()
	{
		if (!isset($GLOBALS['Stats']['RelativeURL']))
			$GLOBALS['Stats']['RelativeURL'] = '';

		if (count($this->distribution) == 0)
			return;

		echo '<p><b>'.$this->title.'</b></p>' . "\n";
		echo '<table border="0" width="100%" cellspacing="1" cellpadding="1" bgcolor="#a9a9a9">' . "\n";

		echo '	<tr>' . "\n";

		// find the max :
		$maxY = 0;

		foreach ($this->distribution as $y)
			if ($y > $maxY)
				$maxY = $y;

		$width = round(80 / (count($this->distribution))) . '%';

		foreach ($this->distribution as $x => $y)
		{
			$height = (int)(($this->max_height * $y) / $maxY);
			echo '		<td align="center" valign="bottom" bgcolor="white">'
								.$y
								.'<br><img src="'.$GLOBALS['Stats']['RelativeURL'].'stats/chart.gif" width="5" height="'.$height.'" border="0"></td>' . "\n";
		}

		echo '		<td valign="bottom" bgcolor="white">'.$this->Y_Title.'</td>' . "\n";
		echo '	</tr>' . "\n";
		echo '	<tr>' . "\n";

		foreach ($this->distribution as $x => $y)
		{
			echo '		<td width="'.$width.'" valign="bottom" align="center" bgcolor="#cfcfcf">'.($x + 0).'</td>' . "\n";
		}

		echo '		<td width="20%" valign="bottom" bgcolor="white">'.$this->X_Title.'</td>' . "\n";
		echo '	</tr>' . "\n";
		echo '</table>' . "\n";
	}

	// stats
	function implodeParams($params)
	{
		if (empty($params))
			return '';

		$ret = array();

		foreach ($params as $k => $v)
			$ret[] = $k . '=' . urlencode(stripslashes($v));

		return '?' . implode('&', $ret);
	}
}

class daily_histogram extends histogram
{
	var $nb_days;
	var $bUseSQLFormat;
	var $NavigationId;

	function daily_histogram($title, $NavigationId = false, $bUseSQLFormat = false, $max_height = 80, $nb_days = 30)
	{
		$this->histogram($title, '', '', $max_height);
		$this->nb_days = $nb_days;
		$this->bUseSQLFormat = $bUseSQLFormat;
		$this->NavigationId = $NavigationId;
	}

	function display()
	{
		if (!isset($GLOBALS['Stats']['RelativeURL']))
			$GLOBALS['Stats']['RelativeURL'] = '';

		if ($this->bUseSQLFormat)
			$format = "Y-n-j";
		else
			$format = "y-m-d";

		$width = round(100 / $this->nb_days) . '%';

		$AddDays = 0;
		if ($this->NavigationId !== false)
		{
			if (isset($_GET['navigate_'.$this->NavigationId]))
				$AddDays = $_GET['navigate_'.$this->NavigationId];
		}

		ksort($this->distribution, SORT_NUMERIC);

		$ReferenceTime = time();

		$ReferenceTime += $AddDays * 86400;

		$first_day = mktime(0, 0, 0, date("m", $ReferenceTime), date("d", $ReferenceTime) - $this->nb_days, date("Y", $ReferenceTime));
		$newDate = new EpiDate($first_day);
		$last_day = mktime(0, 0, 0, date("m", $ReferenceTime), date("d", $ReferenceTime) + 1, date("Y", $ReferenceTime));

		echo '<p><a name="'.$this->NavigationId.'"></a><b>'.$this->title.'</b></p>' . "\n";
		echo '<table border="0" width="100%" cellspacing="0" cellpadding="3"><tr><td>' . "\n";
		echo '<table border="0" width="100%" cellspacing="1" cellpadding="1" bgcolor="#a9a9a9">' . "\n";
		echo '	<tr>' . "\n";

		$Months = array();


		$CurrentMonth = array('name' => $newDate->format("%B %Y")/*strftime("%B %Y", $first_day)*/,
													'count'	=> 0,
													'number' => date("m", $first_day));

		for ($i = 1; $i <= $this->nb_days; $i++)
		{
			$day_to_display = mktime(0, 0, 0, date("m", $ReferenceTime), date("d", $ReferenceTime) - ($this->nb_days - $i), date("Y", $ReferenceTime));
			$newDate = new EpiDate($day_to_display);
			if ($CurrentMonth['name'] == $newDate->format("%B %Y")/*strftime("%B %Y", $day_to_display)*/)
			{
				$CurrentMonth['count'] ++;
			}
			else
			{
				$Months[] = $CurrentMonth;
				$CurrentMonth = array('name' => $newDate->format("%B %Y")/*strftime("%B %Y", $day_to_display)*/,
															'count'	=> 1,
															'number' => date("m", $day_to_display));
			}
		}

		$Months[] = $CurrentMonth;
		foreach ($Months as $aMonth)
		{
			if ($aMonth['number'] % 2)
				$color = '#cfcfcf';
			else
				$color = '#bababa';

			echo '		<td colspan="'.$aMonth['count'].'" align="center" bgcolor="'.$color.'">'.$aMonth['name'].'</td>' . "\n";
		}

		echo '	</tr>' . "\n";

		echo '	<tr>' . "\n";

		$FirstMonth = date("m", $first_day);
		for ($i = 1; $i <= $this->nb_days; $i++)
		{
			$day_to_display = mktime(0, 0, 0, date("m", $ReferenceTime), date("d", $ReferenceTime) - ($this->nb_days - $i), date("Y", $ReferenceTime));

			if (date("m", $day_to_display) % 2)
				$color = '#cfcfcf';
			else
				$color = '#bababa';

			$weekday = date("w", $day_to_display);
			if ($weekday == 0 || $weekday == 6)
				$color = '#f5f5f5';

			echo '		<td width="'.$width.'" align="center"  bgcolor="'.$color.'">'.date("d", $day_to_display).'</td>' . "\n";
		}

		echo '	</tr>' . "\n";
		echo '	<tr>' . "\n";

		// find the max/min :
		$cnx_max = 0;

		foreach ($this->distribution as $NbCnx)
			if ($NbCnx > $cnx_max)
				$cnx_max = $NbCnx;

		for ($i = 1; $i <= $this->nb_days; $i++)
		{
			$day_to_display = mktime(0, 0, 0, date("m", $ReferenceTime), date("d", $ReferenceTime) - ($this->nb_days - $i), date("Y", $ReferenceTime));

			if (isset($this->distribution[date($format, $day_to_display)]))
				$NbCnx = $this->distribution[date($format, $day_to_display)];
			else
				$NbCnx = 0;

			if ($cnx_max == 0)
			{
				echo '		<td align="center" height="'.($this->max_height + 15).'" valign="bottom" bgcolor="white">0</td>' . "\n";
			}
			else
			{
				$height = (int)(($this->max_height * $NbCnx) / $cnx_max);
				if ($height == 0)
					echo '		<td height="'.($this->max_height + 15).'" align="center" valign="bottom" bgcolor="white">'.($NbCnx + 0).'</td>' . "\n";
				else
					echo '		<td height="'.($this->max_height + 15).'" align="center" valign="bottom" bgcolor="white">'.($NbCnx + 0).'<br><img src="'.$GLOBALS['Stats']['RelativeURL'].'stats/chart.gif" width="5" height="'.$height.'" border="0"></td>' . "\n";
			}
		}

		echo '	</tr>' . "\n";

		echo '</table>' . "\n";


		echo '</td></tr>' . "\n";

		if ($this->NavigationId !== false)
		{
			echo '<tr><td align="center">' . "\n";
			$params = $_GET;

			$BackDays = (int)($AddDays - ($this->nb_days * 3 / 4));
			$params['navigate_'.$this->NavigationId] = $BackDays;
			$strParams = $_SERVER['PHP_SELF'] . $this->implodeParams($params) . '#' .$this->NavigationId;
			echo '<a href="'.$strParams.'"><img src="'.$GLOBALS['Stats']['RelativeURL'].'stats/left.gif" width="24" height="24" border="0"></a>&nbsp;' . "\n";

			$params['navigate_'.$this->NavigationId] = '0';
			$strParams = $_SERVER['PHP_SELF'] . $this->implodeParams($params). '#' .$this->NavigationId;
			echo '<a href="'.$strParams.'"><img src="'.$GLOBALS['Stats']['RelativeURL'].'stats/home.gif" width="24" height="24" border="0"></a>&nbsp;' . "\n";

			$ForwardDays = (int)($AddDays + ($this->nb_days * 3 / 4));
			$params['navigate_'.$this->NavigationId] = $ForwardDays;
			$strParams = $_SERVER['PHP_SELF'] . $this->implodeParams($params). '#' .$this->NavigationId;
			echo '<a href="'.$strParams.'"><img src="'.$GLOBALS['Stats']['RelativeURL'].'stats/right.gif" width="24" height="24" border="0"></a></td>' . "\n";

			echo '</tr>' . "\n";
		}

		echo '</table>' . "\n";

	}

	function InitDistributionWithRandomValues($MaxValue = 15)
	{
		if ($this->bUseSQLFormat)
			$format = "Y-n-j";
		else
			$format = "y-m-d";

		$this->distribution = array();
		$first_day = mktime(0, 0, 0, date("m"), date("d") - $this->nb_days, date("Y"));
		$last_day = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));

		for ($i = 1; $i <= $this->nb_days; $i++)
		{
			$day_to_display = mktime(0, 0, 0, date("m"), date("d") - ($this->nb_days - $i), date("Y"));

			$this->distribution[date($format, $day_to_display)] = rand(0, $MaxValue);
		}

		return $this->distribution;
	}

	function InitDistributionWithMySQLRows(&$Rows, $date_field)
	{
		if ($this->bUseSQLFormat)
			$format = "Y-n-j";
		else
			$format = "y-m-d";

		$this->distribution = array();

		$AddDays = 0;
		if ($this->NavigationId !== false)
		{
			if (isset($_GET['navigate_'.$this->NavigationId]))
				$AddDays = $_GET['navigate_'.$this->NavigationId];
		}

		$ReferenceTime = time();

		$ReferenceTime += $AddDays * 86400;

		$first_day = mktime(0, 0, 0, date("m", $ReferenceTime), date("d", $ReferenceTime) - $this->nb_days, date("Y", $ReferenceTime));
		$last_day = mktime(0, 0, 0, date("m", $ReferenceTime), date("d", $ReferenceTime) + 1, date("Y", $ReferenceTime));

	  foreach ($Rows as $aRow)
	  {
	    if ($aRow[$date_field] != '')
	    {
	    	$CxnTime = EpiDBTools::SQLToTimeStamp($aRow[$date_field]);

	    	if ($CxnTime > $first_day && $CxnTime < $last_day)
	    	{
	    		if (!isset($this->distribution[date($format, $CxnTime)]))
	    			$this->distribution[date($format, $CxnTime)] = 0;

	    		$this->distribution[date($format, $CxnTime)]++;
	    	}
	    }
	  }
	}
}

class monthly_histogram extends histogram
{
	var $bUseSQLFormat;
	var $nb_months_before;
	var $nb_months_after;

	function monthly_histogram($title, $bUseSQLFormat = false, $max_height = 80,
														 $nb_months_before = 12, $nb_months_after=0)
	{
		$this->histogram($title, '', '', $max_height);

		$this->nb_months_before = $nb_months_before;
		$this->nb_months_after = $nb_months_after;

		$this->bUseSQLFormat = $bUseSQLFormat;
	}

	function display()
	{
		if (!isset($GLOBALS['Stats']['RelativeURL']))
			$GLOBALS['Stats']['RelativeURL'] = '';

		if ($this->bUseSQLFormat)
			$format = "Y-n";
		else
			$format = "y-m";

		setlocale (LC_TIME, 'fr_FR', 'french');

		// convert a daily distribution to a monthly distribution
		$MonthlyDistribution = array();

		foreach ($this->distribution as $k => $NbCnx)
		{
			$parts = explode('-', $k);

			if (!isset($MonthlyDistribution[$parts[0] . '-' . $parts[1]]))
				$MonthlyDistribution[$parts[0] . '-' . $parts[1]] = 0;

			$MonthlyDistribution[$parts[0] . '-' . $parts[1]] += $NbCnx;
		}

		echo '<p><b>'.$this->title.'</b></p>' . "\n";
		echo '<table border="0" width="100%" cellspacing="1" cellpadding="1" bgcolor="#a9a9a9">' . "\n";
		echo '	<tr>' . "\n";

		$width = 100 / ($this->nb_months_before + $this->nb_months_after);

		for ($i = 1; $i <= $this->nb_months_before; $i++)
		{
			$day_to_display = mktime(0, 0, 0, date("m") - ($this->nb_months_before - $i), 1, date("Y"));
			$newDate = new EpiDate($day_to_display);
			$color = '#cfcfcf';
			echo '		<td width="'.$width.'%" nowrap align="center" bgcolor="'.$color.'">'.$newDate->format("%b %y")/*strftime("%b %y", $day_to_display)*/.'</td>' . "\n";
		}

		for ($i = 1; $i <= $this->nb_months_after; $i++)
		{
			$day_to_display = mktime(0, 0, 0, date("m") + $i, 1, date("Y"));
			$newDate = new EpiDate($day_to_display);
			$color = '#cfcfcf';
			echo '		<td width="'.$width.'%" nowrap align="center" bgcolor="'.$color.'">'.$newDate->format("%b %y")/*strftime("%b %y", $day_to_display)*/.'</td>' . "\n";
		}

		echo '	</tr>' . "\n";
		echo '	<tr>' . "\n";

		// find the max/min :
		$cnx_max = 0;

		for ($i = 1; $i <= $this->nb_months_before; $i++)
		{
			$day_to_display = mktime(0, 0, 0, date("m") - ($this->nb_months_before - $i), 1, date("Y"));

			if (isset($MonthlyDistribution[date($format, $day_to_display)]))
				$NbCnx = $MonthlyDistribution[date($format, $day_to_display)];
			else
				$NbCnx = 0;

			if ($NbCnx > $cnx_max)
				$cnx_max = $NbCnx;
		}

		for ($i = 1; $i <= $this->nb_months_after; $i++)
		{
			$day_to_display = mktime(0, 0, 0, date("m") + $i, 1, date("Y"));

			if (!isset($MonthlyDistribution[date("y-m", $day_to_display)]))
				$MonthlyDistribution[date("y-m", $day_to_display)] = 0;

			$NbCnx = $MonthlyDistribution[date("y-m", $day_to_display)];
			if ($NbCnx > $cnx_max)
				$cnx_max = $NbCnx;
		}

		for ($i = 1; $i <= $this->nb_months_before; $i++)
		{
			$day_to_display = mktime(0, 0, 0, date("m") - ($this->nb_months_before - $i), 1, date("Y"));

			if (isset($MonthlyDistribution[date($format, $day_to_display)]))
				$NbCnx = $MonthlyDistribution[date($format, $day_to_display)];
			else
				$NbCnx = 0;

			if ($cnx_max == 0)
			{
				echo '		<td valign="bottom" bgcolor="white">&nbsp;</td>' . "\n";
			}
			else
			{
				$height = (int)(($this->max_height * $NbCnx) / $cnx_max);
				echo '		<td align="center" valign="bottom" bgcolor="white"><img src="'.$GLOBALS['Stats']['RelativeURL'].'stats/chart.gif" width="5" height="'.$height.'" border="0"></td>' . "\n";
			}
		}

		for ($i = 1; $i <= $this->nb_months_after; $i++)
		{
			$day_to_display = mktime(0, 0, 0, date("m") + $i, 1, date("Y"));

			if (isset($MonthlyDistribution[date($format, $day_to_display)]))
				$NbCnx = $MonthlyDistribution[date($format, $day_to_display)];
			else
				$NbCnx = 0;

			if ($cnx_max == 0)
				echo '		<td valign="bottom" bgcolor="white">&nbsp;</td>' . "\n";
			else
			{
				$height = (int)(($this->max_height * $NbCnx) / $cnx_max);
				echo '		<td align="center" valign="bottom" bgcolor="white"><img src="'.$GLOBALS['Stats']['RelativeURL'].'stats/chart.gif" width="5" height="'.$height.'" border="0"></td>' . "\n";
			}
		}

		echo '	</tr>' . "\n";
		echo '	<tr>' . "\n";

		for ($i = 1; $i <= $this->nb_months_before; $i++)
		{
			$day_to_display = mktime(0, 0, 0, date("m") - ($this->nb_months_before - $i), 1, date("Y"));

			if (isset($MonthlyDistribution[date($format, $day_to_display)]))
				$NbCnx = $MonthlyDistribution[date($format, $day_to_display)];
			else
				$NbCnx = 0;

			echo '		<td valign="bottom" align="center" bgcolor="white">'.($NbCnx + 0).'</td>' . "\n";
		}

		for ($i = 1; $i <= $this->nb_months_after; $i++)
		{
			$day_to_display = mktime(0, 0, 0, date("m") + $i, 1, date("Y"));

			if (isset($MonthlyDistribution[date($format, $day_to_display)]))
				$NbCnx = $MonthlyDistribution[date($format, $day_to_display)];
			else
				$NbCnx = 0;

			echo '		<td valign="bottom" align="center" bgcolor="white">'.($NbCnx + 0).'</td>' . "\n";
		}

		echo '	</tr>' . "\n";
		echo '</table>' . "\n";
	}
}

?>