<?php

class UserQuestionDisplayer extends QuestionDisplayer
{
	function UserQuestionDisplayer()
	{
		$this->QuestionDisplayer();
	}

	function DisplayPage($bInCorrection, $bLastQuestion, $HasAnsweredAllQuestions)
	{
		parent::DisplayPage($bInCorrection, $bLastQuestion, $HasAnsweredAllQuestions);


		$strUID = $GLOBALS['QuizzManager']->GetPageData($GLOBALS['QuizzManager']->GetCurrentPageIndex(), 'uid');

		if (!empty($strUID) && !empty($GLOBALS['QuizzManager']->QuestionDefinition[$strUID]['Title']))
			$strQuestionTitle = $GLOBALS['QuizzManager']->QuestionDefinition[$strUID]['Title'];
		else
		{
			// NB: this is not translated, but appears here for backward compatibility only.
			// The questions should have a title and if not the title can be set at any time.
			$strQuestionTitle = 'Question ' .  ($GLOBALS['QuizzManager']->GetCurrentPageIndex() + 1);
		}

		echo '<script type="text/javascript" language="JavaScript">' . "\n";
		echo '<!--' . "\n";
		echo '		var myPageName = "'.str_replace('"', '\\"', addslashes($strQuestionTitle)).'";' . "\n";
		echo '//-->' . "\n";
		echo '</script>' . "\n";
	}
}


?>