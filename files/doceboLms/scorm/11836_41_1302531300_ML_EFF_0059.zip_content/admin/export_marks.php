<?php

	PrintHeaders('Export');

	// export the column headers
	$QuestionText = array();
	$QuestionText[] =  '"Date"';
	$QuestionText[] =  '"Host"';

	if ($QuestionDefinition[0]["Type"] == "CREDENTIALS")
	{
		$QuestionText[] = '"Surname"'; // Nom
		$QuestionText[] = '"Firstname"'; // Prenom
	}

	$QuestionText[] = '"Total score"';
	$QuestionText[] = '"Max score"';
	$QuestionText[] = '"Time spent"';

	$i = 1;
	$ThemeGUID = "";
	$current_theme = "";

	foreach ($QuestionDefinition as $Question)
	{
		if ($Question["Type"] != "CREDENTIALS" &&
				$Question["Type"] != "EXPLANATION")
		{
			if ($ThemeGUID != $Question["ThemeGUID"])
			{
				$ThemeGUID = $Question["ThemeGUID"];

				if (isset($Themes[$ThemeGUID]["title"]))
					$current_theme = $Themes[$ThemeGUID]["title"];
				else
					$current_theme = '';

				$QuestionText[] = '"' . PrepareTextToCSVExport($Question["Theme"]) . '"';
				$QuestionText[] = '""';
			}

			$QuestionText[] = '"' . PrepareTextToCSVExport($Question["Question"]) . '"';
			$QuestionText[] = '""';
		}

		$i++;
	}

	echo implode(';', $QuestionText) . "\n";

	// export the column sub-headers
	$QuestionText = array();
	$QuestionText[] =  '""';
	$QuestionText[] =  '""';

	if ($QuestionDefinition[0]["Type"] == "CREDENTIALS")
	{
		$QuestionText[] = '""'; // Nom
		$QuestionText[] = '""'; // Prenom
	}

	$QuestionText[] = '""';
	$QuestionText[] = '""';
	$QuestionText[] = '""';

	$i = 1;
	$ThemeGUID = "";
	$current_theme = "";

	foreach ($QuestionDefinition as $Question)
	{
		if ($Question["Type"] != "CREDENTIALS" &&
				$Question["Type"] != "EXPLANATION")
		{
			if ($ThemeGUID != $Question["ThemeGUID"])
			{
				$ThemeGUID = $Question["ThemeGUID"];

				if (isset($Themes[$ThemeGUID]["title"]))
					$current_theme = $Themes[$ThemeGUID]["title"];
				else
					$current_theme = '';

				$QuestionText[] = '"Score"';
				$QuestionText[] = '"Max score"';
			}

			$QuestionText[] = '"Score"';
			$QuestionText[] = '"Max score"';
		}

		$i++;
	}

	echo implode(';', $QuestionText) . "\n";

	// export the answers
	foreach ($QuestionAnswers as $UserAnswer)
	{
		$ThisAnswer = $UserAnswer["answers"];
		$ThisScores = $UserAnswer["scores"];

		$QuestionText = array();
		$QuestionText[] = '"'. date("d/m/Y G:i:s",$UserAnswer["when"]). '"';
		$QuestionText[] = '"'. PrepareTextToCSVExport($UserAnswer["who"]). '"';

		if ($QuestionDefinition[0]["Type"] == "CREDENTIALS")
		{
			$QuestionText[] = '"'. PrepareTextToCSVExport($UserAnswer["Username"]) . '"'; // Nom
			$QuestionText[] = '"'. PrepareTextToCSVExport($UserAnswer["UserFirstname"]) . '"'; // Prenom
		}

		$QuestionText[] = number_format($UserAnswer["score"], 2, ',', '');
		$QuestionText[] = number_format($UserAnswer["score_max"], 2, ',', '');
		$QuestionText[] = $UserAnswer["time_spent"];

		$i = 1;
		$ThemeGUID = "";
		$current_theme = "";
		$sub_score = array();
		$sub_score_max = array();

		foreach ($QuestionDefinition as $Question)
		{
			if ($Question["Type"] != "CREDENTIALS" &&
					$Question["Type"] != "EXPLANATION")
			{
				if ($ThemeGUID != $Question["ThemeGUID"])
				{
					$ThemeGUID = $Question["ThemeGUID"];

					if (isset($Themes[$ThemeGUID]["title"]))
						$current_theme = $Themes[$ThemeGUID]["title"];
					else
						$current_theme = '';

					$sub_score[$ThemeGUID] == 0;
					$sub_score_max[$ThemeGUID] == 0;
				}

				$sub_score[$ThemeGUID] += $UserAnswer["scores"][$i - 1]['sc'];
				$sub_score_max[$ThemeGUID] += $UserAnswer["scores"][$i - 1]['msc'];
			}

			$i++;
		}

		$i = 1;
		$ThemeGUID = "";
		$current_theme = "";

		foreach ($QuestionDefinition as $Question)
		{
			$ThisAnswer = GetAnswersForQuestion($i, $Question, $UserAnswer);

			if ($Question["Type"] != "CREDENTIALS" &&
					$Question["Type"] != "EXPLANATION")
			{
				if ($ThemeGUID != $Question["ThemeGUID"])
				{
					$ThemeGUID = $Question["ThemeGUID"];

					if (isset($Themes[$ThemeGUID]["title"]))
						$current_theme = $Themes[$ThemeGUID]["title"];
					else
						$current_theme = '';

					$QuestionText[] = number_format($sub_score[$ThemeGUID], 2, ',', '');
					$QuestionText[] = number_format($sub_score_max[$ThemeGUID], 2, ',', '');
				}

				$QuestionText[] = number_format($UserAnswer["scores"][$i - 1]['sc'], 2, ',', '');
				$QuestionText[] = number_format($UserAnswer["scores"][$i - 1]['msc'], 2, ',', '');
			}

			$i++;
		}

		echo implode(';', $QuestionText) . "\n";
	}


	function GetAnswersForQuestion($i, $questionDefinition, &$answer)
	{
		if (isset($answer['id_map']) && !empty($questionDefinition['UID']))
		{
			if (isset($answer["answers"][$answer['id_map'][$questionDefinition['UID']]]))
				return $answer["answers"][$answer['id_map'][$questionDefinition['UID']]];
			else
				return false;
		}

		if (isset($answer["answers"][$i - 1]))
			return $answer["answers"][$i - 1];
		else
			return false;
	}


	exit();
?>