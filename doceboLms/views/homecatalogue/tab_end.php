		<?php
			//Left category
			if ($_GET['r'] !== 'homecatalogue/coursepathCourse') {
				echo '</div>'
				. '</div>'
				. '<div class="yui-b" id="left_categories">'
				. '<ul class="flat-categories">'
				. '<li><a href="' . $std_link . '">' . Lang::t('_ALL_CATEGORIES', 'catalog') . '</a></li>';

				$category = $model->getMajorCategory($std_link);

				foreach ($category as $id_cat => $name)
					echo '<li><a href="' . $std_link . '&amp;id_cat=' . $id_cat . '">' . $name . '</a></li>';

				echo '</ul>'
				. '</div>'
				. '</div>';
			}
		?>
	</div>
	<div class="nofloat">&nbsp;</div>
</div>