<style>
	.it-updates-admin {
		padding: 30px;
		margin: 30px 0;
		background: #fff;
		border:1px solid #ccc;
		width: 70%;
	}
	.it-updates-admin table {
		border-collapse: collapse;
		margin: 0 0 30px 0;
	}
	.it-updates-admin input[type=text] {
		width: 100%;
	}
	.it-updates-admin label, .it-updates-admin textarea {
		display: block;
		width: 100%;
		margin: 0 0 15px 0;
	}
	.green {
		text-align: center;
		background: #a7cd45;
	}
	.amber {
		text-align: center;
		background: #ffd32f;
	}
	.red {
		text-align: center;
		background: #eb2040;
	}
	.up, .down, .delete {
		text-align: center;
		display: block;
	}
</style>

<?php
	$updates_list = \DHIntranet\IT_Updates::get_updates();
//	\DHIntranet\IT_Updates::save_updates($updates_list);
?>

<div class="it-updates-admin">
	<h1>IT Updates Control Panel</h1>
	<form method="POST" action="">
		<table>
			<tbody>
			<tr>
				<th style="width:55%"></th>
				<th style="width:5%"></th>
				<th style="width:5%"></th>
				<th style="width:5%"></th>
				<th style="width:10%"></th>
				<th style="width:10%"></th>
				<th style="width:10%"></th>
			</tr>
			<?php
				$position = 0;
                                if (isset($updates_list['statuses']) && is_array($updates_list['statuses'])) {
				foreach($updates_list['statuses'] as $key => $update)
				{
//					if(stristr($key, 'position'))
					{
						echo "<tr class=\"status-container\" position=\"".esc_attr($position)."\">";
						echo "<td class=\"text\"><input type=\"text\" size=\"35\" name=\"statuses[".esc_attr($position)."][system_name]\" value=\"".esc_attr($update['system_name'])."\"></td>";
						echo "<td class=\"green\"><input type=\"radio\" name=\"statuses[".esc_attr($position)."][status]\" value=\"green\"" . ($update['status'] == 'green' ? 'checked' : '')  . "></td>";
						echo "<td class=\"amber\"><input type=\"radio\" name=\"statuses[".esc_attr($position)."][status]\" value=\"amber\"" . ($update['status'] == 'amber' ? 'checked' : '')  . "></td>";
						echo "<td class=\"red\"><input type=\"radio\" name=\"statuses[".esc_attr($position)."][status]\" value=\"red\"" . ($update['status'] == 'red' ? 'checked' : '') . "></td>";
						echo '<td><a class="up" href="#" alt="Move up">Move up</a></td>';
						echo '<td><a class="down" href="#" alt="Move down">Move down</a></td>';
						echo '<td><a class="delete" href="#" alt="Delete">Delete</a></td>';
						echo '</tr>';
					}
					$position++;
				}
                                }
			?>
			<tr position="overall" class="overall-status">
				<?php
					echo '<td class="text"><input type="text" size="35" value="Overall Status" disabled></td>';
					echo "<td class=\"green\"><input type=\"radio\" name=\"overall[status]\" value=\"green\"" . ($updates_list['overall_status']['status'] == 'green' ? 'checked' : '')  . "></td>";
					echo "<td class=\"amber\"><input type=\"radio\" name=\"overall[status]\" value=\"amber\"" . ($updates_list['overall_status']['status'] == 'amber' ? 'checked' : '')  . "></td>";
					echo "<td class=\"red\"><input type=\"radio\" name=\"overall[status]\" value=\"red\"" . ($updates_list['overall_status']['status'] == 'red' ? 'checked' : '') . "></td>";
				?>
				</tr>
			</tr>
			</tbody>
		</table>
		<div class="form-group">
			<label for="overall_status_message"> Custom message </label>
			<textarea rows="12" name="overall[message]"><?php echo esc_html($updates_list['overall_status']['message']); ?></textarea>
			<input type="button" class="button-primary add-new-status" name="add_new_status" value="Add New Status">&nbsp;
			<input type="submit" class="button-primary" name="save_changes" value="Save Changes">
		</div>
	</form>
</div>
