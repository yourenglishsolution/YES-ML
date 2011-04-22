<?php

	PrintHeaders('Export');

	$MoreFieldsCount = array();

	// export the column headers
	$QuestionText = array();
	$QuestionText[] =  '"Date"';
	$QuestionText[] =  '"Host"';

	if ($QuestionDefinition[0]["Type"] == "CREDENTIALS")
	{
		$QuestionText[] = '"Surname"'; // Nom
		$QuestionText[] = '"Firstname"'; // Prenom
	}

	foreach ($QuestionDefinition as $QuestionIndex => $Question)
	{
		if ($Question["Type"] == "CREDENTIALS") continue;

		$QuestionColums = 1;

		if (isset($Question["Answers"]) && is_array($Question["Answers"]))
			$QuestionColums = count($Question["Answers"]);

		if (isset($Question["Columns"]) && is_array($Question["Columns"]))
			$QuestionColums = $QuestionColums * count($Question["Columns"]);

		if (isset($Question["Lefts"]) && is_array($Question["Lefts"]))
			$QuestionColums = count($Question["Lefts"]);

		$QuestionText[] = '"' . PrepareTextToCSVExport($Question["Question"]) . '"';

		// fill the rest of the columns with blank strings
		for ($i = 1; $i < $QuestionColums; $i++)
			$QuestionText[] = '""';

		$MoreFieldsCount[$QuestionIndex] = GetQuestionMoreFieldsCount($QuestionAnswers, $QuestionDefinition, $QuestionIndex);

		for ($i = 0; $i < $MoreFieldsCount[$QuestionIndex]; $i++)
			$QuestionText[] = '""';
	}

	echo implode(';', $QuestionText) . "\n";

	$QuestionText = array();
	$QuestionText[] = '""'; // date
	$QuestionText[] = '""'; // host

	if ($QuestionDefinition[0]["Type"] == "CREDENTIALS")
	{
		$QuestionText[] = '""'; // Nom
		$QuestionText[] = '""'; // Prenom
	}

	foreach ($QuestionDefinition as $QuestionIndex => $Question)
	{
		if ($Question["Type"] == "CREDENTIALS") continue;

		if (isset($Question["Answers"]) && is_array($Question["Answers"]))
		{
			foreach ($Question["Answers"] as $QuestionRow)
			{
				if (isset($Question["Columns"]) && is_array($Question["Columns"]))
				{
					foreach ($Question["Columns"] as $QuestionColumn)
						$QuestionText[] = '"' . PrepareTextToCSVExport($QuestionRow . ' (' .$QuestionColumn . ')') . '"';
				}
				else
					$QuestionText[] = '"' . PrepareTextToCSVExport($QuestionRow) . '"';
			}
		}
		else if (isset($Question["Lefts"]) && is_array($Question["Lefts"]))
		{
			foreach ($Question["Lefts"] as $QuestionRow)
				$QuestionText[] = '"' . PrepareTextToCSVExport($QuestionRow) . '"';
		}
		else
			$QuestionText[] = '""';

		for ($i = 0; $i < $MoreFieldsCount[$QuestionIndex]; $i++)
			$QuestionText[] = '"..."';
	}

	echo implode(';', $QuestionText) . "\n";

	// export the answers
	foreach ($QuestionAnswers as $answer)
	{
		$ThisAnswer = $answer["answers"];

		$QuestionText = array();
		$QuestionText[] = '"'. date("d/m/Y G:i:s",$answer["when"]). '"';
		$QuestionText[] = '"'. PrepareTextToCSVExport($answer["who"]). '"';

		if ($QuestionDefinition[0]["Type"] == "CREDENTIALS")
		{
			$QuestionText[] = '"'. PrepareTextToCSVExport($answer["Username"]). '"'; // Nom
			$QuestionText[] = '"'. PrepareTextToCSVExport($answer["UserFirstname"]). '"'; // Prenom
		}

		foreach ($QuestionDefinition as $QuestionIndex => $Question)
		{
			if ($Question["Type"] == "CREDENTIALS") continue;

			if (isset($Question["Answers"]) && is_array($Question["Answers"]))
			{
				foreach ($Question["Answers"] as $ColumnIndex => $QuestionRow)
				{
					switch ($Question["Type"])
					{
						case "SURVEYQCU" :
						case "QCU"    :
							if (!is_null($ThisAnswer[$QuestionIndex]) &&
									$ThisAnswer[$QuestionIndex]['checked'] == $ColumnIndex)
								$QuestionText[] = '"1"';
							else
								$QuestionText[] = '""';
							break;

						case "SURVEYQCM" :
						case "QCM"    :
							if (!is_null($ThisAnswer[$QuestionIndex]) &&
									is_array($ThisAnswer[$QuestionIndex]['checked']) &&
									in_array($ColumnIndex, $ThisAnswer[$QuestionIndex]['checked']))
								$QuestionText[] = '"1"';
							else
								$QuestionText[] = '""';
							break;

						case "SURVEYTABQCU" :
						case "TABQCU" :
							foreach ($Question["Columns"] as $SubColumnIndex => $QuestionColumn)
							{
								if (!is_null($ThisAnswer[$QuestionIndex]) &&
										$ThisAnswer[$QuestionIndex][$ColumnIndex] == $SubColumnIndex)
									$QuestionText[] = '"1"';
								else
									$QuestionText[] = '""';
							}
							break;

						case "SURVEYTABQCM" :
						case "TABQCM" :
							foreach ($Question["Columns"] as $SubColumnIndex => $QuestionColumn)
							{
								if (!is_null($ThisAnswer[$QuestionIndex]) &&
										is_array($ThisAnswer[$QuestionIndex][$ColumnIndex]) &&
										in_array($SubColumnIndex, $ThisAnswer[$QuestionIndex][$ColumnIndex]))
									$QuestionText[] = '"1"';
								else
									$QuestionText[] = '""';
							}
							break;

						case "TAT"   :
						case "MATCH" :
						case "SORT"  :
						case "FORM"  :
							$QuestionText[] = '"' . PrepareTextToCSVExport($ThisAnswer[$QuestionIndex][$ColumnIndex + 1]) . '"';
							break;

						default:
							if (is_array($ThisAnswer[$QuestionIndex][$ColumnIndex]))
								$QuestionText[] = '"' . PrepareTextToCSVExport(implode(';', $ThisAnswer[$QuestionIndex][$ColumnIndex])) . '"';
							else
								$QuestionText[] = '"' . PrepareTextToCSVExport($ThisAnswer[$QuestionIndex][$ColumnIndex]) . '"';
					}
				}

				if (isset($ThisAnswer[$QuestionIndex]['more']))
				{
					if (is_array($ThisAnswer[$QuestionIndex]['more']))
					{
						foreach ($ThisAnswer[$QuestionIndex]['more'] as $aText)
							$QuestionText[] = '"' . PrepareTextToCSVExport($aText) . '"';
					}
					else
					{
						$QuestionText[] = '"' . PrepareTextToCSVExport($ThisAnswer[$QuestionIndex]['more']) . '"';
					}
				}
				else
				{
					for ($i = 0; $i < $MoreFieldsCount[$QuestionIndex]; $i++)
						$QuestionText[] = '""';
				}
			}
			else
			{
				switch ($Question["Type"])
				{
					case "TEXT"    :
						$QuestionText[] = '"' . PrepareTextToCSVExport($ThisAnswer[$QuestionIndex]) . '"';
						break;

					case 'DRAGDROP':
						foreach ($Question["Lefts"] as $ColumnIndex => $QuestionRow)
							$QuestionText[] = '"' . PrepareTextToCSVExport($ThisAnswer[$QuestionIndex][$ColumnIndex + 1]) . '"';
						break;

					default:
						$QuestionText[] = '"' . PrepareTextToCSVExport(print_r($ThisAnswer[$QuestionIndex], true)) . '"';

				}
			}
		}

		echo implode(';', $QuestionText) . "\n";
	}

	function GetQuestionMoreFieldsCount(&$QuestionAnswers, &$QuestionDefinition, $QuestionIndex)
	{
		$MoreFieldsCount = 0;

		$Question = $QuestionDefinition[$QuestionIndex];

		if ($Question["Type"] == "CREDENTIALS")
			return 0;

		// export the answers
		foreach ($QuestionAnswers as $answer)
		{
			$ThisAnswerMoreFieldsCount = 0;
			$ThisAnswer = $answer["answers"];

			if (isset($ThisAnswer[$QuestionIndex]['more']))
			{
				if (is_array($ThisAnswer[$QuestionIndex]['more']))
				{
					foreach ($ThisAnswer[$QuestionIndex]['more'] as $aText)
						$ThisAnswerMoreFieldsCount++;
				}
				else
				{
					$ThisAnswerMoreFieldsCount++;
				}
			}

			if ($ThisAnswerMoreFieldsCount > $MoreFieldsCount)
				$MoreFieldsCount = $ThisAnswerMoreFieldsCount;
		}

		return $MoreFieldsCount;
	}

	exit();
?>