<?php

/* author : Polo */
class Module_Pages extends LmsModule {
	
	function loadBody()
	{
		switch($GLOBALS['op'])
		{
			case 'nbCourse':
				$result = array();
				$user = Docebo::user();
				$result['todo'] = $user->getCourseCount(0);
				$result['done'] = $user->getCourseCount(1);
				$content = json_encode($result);
				break;
			
			case 'pagingCourse':
				$result = array();
				$courseType = (isset($_GET['type']) ? $_GET['type'] : 'inprogress');
				
				$user = Docebo::user();
				$count = $user->getCourseCount(($courseType == 'inprogress' ? 0 : 1));
				$result['pagesCount'] = ceil($count / 5);
				
				$content = json_encode($result);
				break;
			
			default:
				ob_start();
				require($GLOBALS['where_lms'].'/views/pages/'.$GLOBALS['op'].'.php');
				$content = ob_get_contents();
				ob_end_clean();
				break;
		}
		
		$GLOBALS['page']->addStart('', 'content');
		$GLOBALS['page']->add($content, 'content');
		$GLOBALS['page']->addEnd('', 'content');
	}
}

?>