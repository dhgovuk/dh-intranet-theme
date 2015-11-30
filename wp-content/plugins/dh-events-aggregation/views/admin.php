<br><br>
<div style="background:white; width: 600px; border:dotted; border-width: 1px; padding:3px; padding-left:10px">
	<h1>Events Aggregation Control Panel</h1>
	<br>
	<form method="POST" action="" class="event_categories">
		<table style="width:500px; border-spacing:0px">
			<tbody>
				<tr>
					<th style="text-align: left; width:60%">Category</th>
					<th style="text-align: left;">Display</th>
					<th style="text-align: left;">Hide</th>
					<th style="text-align: left;">Up</th>
					<th style="text-align: left;">Down</th>
				</tr>
				<?php
					// All categories and their events.
					$event_categories = Event_Categories::get_all_categories();

					// Categories that have been set to be displayed/hidden.
					$category_states = Event_Categories::get_category_states();

					// Length for truncating the event titles.
					$event_title_length = Event_Categories::get_event_title_length();

					// Length for truncating the event descriptions.
					$event_description_length = Event_Categories::get_event_description_length();

					foreach($category_states as $cat_id => $state) {
						if(isset($event_categories[$cat_id]) || $cat_id == 'uncategorized')	{
							echo "<tr class=\"category_container\">";
							if($cat_id == 'uncategorized')
								echo "<td>Uncategorized</td>";
							else
								echo "<td>$event_categories[$cat_id]</td>";

							echo '<td><input type="radio" name="categories[' . $cat_id . ']" value="display" ' . ($state == 'display' ? 'checked' : '') . '></td>';
							echo '<td><input type="radio" name="categories[' . $cat_id . ']" value="hide" ' . ($state == 'hide' ? 'checked' : '') . '></td>';
							echo '<td class="up"><a class="up" href="#" alt="Move up" style="text-decoration: none">˄</a></td>';
							echo '<td class="down"><a class="down" href="#" alt="Move down" style="text-decoration: none">˅</a></td>';
							echo '</tr>';
						}
					}
				?>
			</tbody>
		</table>
		<br><br>
		Maximum title length in characters: <input name="event_title_length" type="text" value="<?php echo $event_title_length; ?>" maxlength="3" size="5">
		<br><br>
		Maximum description length in characters: <input name="event_description_length" type="text" value="<?php echo $event_description_length; ?>" maxlength="3" size="5">
		<br><br>
		<input type="submit" class="button-primary" name="save_changes" value="Save Changes">
	</form>
	<br>
	<i>Note: To create or delete a category, use the Events Category submenu from the Events menu.</i>
</div>