<?php
/**
 * Create the admin menu for this plugin
 * @param no-param
 * @return no-return
 */
function WtgLikePostAdminMenu() {
     add_options_page('WTG Like Post', __('WTG Like Post', 'wtg-like-post'), 'activate_plugins', 'WtgLikePostAdminMenu', 'WtgLikePostAdminContent');
}

add_action('admin_menu', 'WtgLikePostAdminMenu');

/**
 * Pluing settings page
 * @param no-param
 * @return no-return
 */
function WtgLikePostAdminContent() {
     // Creating the admin configuration interface
     global $wpdb, $wtg_like_post_db_version;
     
	$excluded_sections = get_option('wtg_like_post_excluded_sections');
	$excluded_categories = get_option('wtg_like_post_excluded_categories');
	
	if (empty($excluded_sections)) {
		$excluded_sections = array();
	}
	
	if (empty($excluded_categories)) {
		$excluded_categories = array();
	}
?>
<div class="wrap">
     <h2><?php echo __('WTG Like Post Options', 'wtg-like-post');?></h2>
     <br class="clear" />

	<div class="metabox-holder has-right-sidebar" id="poststuff">
		<div id="post-body">
			<div id="post-body-content">
				<div id="WtgLikePostOptions" class="postbox">
					<h3><?php echo __('Configuration', 'wtg-like-post'); ?></h3>
					<div class="inside">
						<form method="post" action="options.php">
							<?php settings_fields('wtg_like_post_options'); ?>
							<table class="form-table">
								<tr valign="top">
									<th scope="row"><label for="drop_settings_table_no"><?php _e('Remove plugin settings and table on plugin un-install', 'wtg-like-post'); ?></label></th>
									<td>
										<input type="radio" name="wtg_like_post_drop_settings_table" id="drop_table_yes" value="1" <?php if (1 == get_option('wtg_like_post_drop_settings_table')) { echo 'checked'; } ?> /> <?php echo __('Yes', 'wtg-like-post'); ?>
										<input type="radio" name="wtg_like_post_drop_settings_table" id="drop_table_no" value="0" <?php if ((0 == get_option('wtg_like_post_drop_settings_table')) || ('' == get_option('wtg_like_post_drop_settings_table'))) { echo 'checked'; } ?> /> <?php echo __('No', 'wtg-like-post'); ?>
										<span class="description"><?php _e('Select whether the plugin settings and table will be removed when you uninstall the plugin. Setting this to NO is helpful if you are planning to reuse this in future with old data or upgrade to PRO version.', 'wtg-like-post');?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Voting Period', 'wtg-like-post'); ?></label></th>
									<td>
										<?php
										$voting_period = get_option('wtg_like_post_voting_period');
										?>
										<select name="wtg_like_post_voting_period" id="wtg_like_post_voting_period">
											<option value="0"><?php echo __('Always can vote', 'wtg-like-post'); ?></option>
											<option value="once" <?php if ("once" == $voting_period) echo "selected='selected'"; ?>><?php echo __('Only once', 'wtg-like-post'); ?></option>
											<option value="1" <?php if ("1" == $voting_period) echo "selected='selected'"; ?>><?php echo __('One day', 'wtg-like-post'); ?></option>
											<option value="2" <?php if ("2" == $voting_period) echo "selected='selected'"; ?>><?php echo __('Two days', 'wtg-like-post'); ?></option>
											<option value="3" <?php if ("3" == $voting_period) echo "selected='selected'"; ?>><?php echo __('Three days', 'wtg-like-post'); ?></option>
											<option value="7" <?php if ("7" == $voting_period) echo "selected='selected'"; ?>><?php echo __('One week', 'wtg-like-post'); ?></option>
											<option value="14" <?php if ("14" == $voting_period) echo "selected='selected'"; ?>><?php echo __('Two weeks', 'wtg-like-post'); ?></option>
											<option value="21" <?php if ("21" == $voting_period) echo "selected='selected'"; ?>><?php echo __('Three weeks', 'wtg-like-post'); ?></option>
											<option value="1m" <?php if ("1m" == $voting_period) echo "selected='selected'"; ?>><?php echo __('One month', 'wtg-like-post'); ?></option>
											<option value="2m" <?php if ("2m" == $voting_period) echo "selected='selected'"; ?>><?php echo __('Two months', 'wtg-like-post'); ?></option>
											<option value="3m" <?php if ("3m" == $voting_period) echo "selected='selected'"; ?>><?php echo __('Three months', 'wtg-like-post'); ?></option>
											<option value="6m" <?php if ("6m" == $voting_period) echo "selected='selected'"; ?>><?php echo __('Six Months', 'wtg-like-post'); ?></option>
											<option value="1y" <?php if ("1y" == $voting_period) echo "selected='selected'"; ?>><?php echo __('One Year', 'wtg-like-post'); ?></option>
										</select>
										<span class="description"><?php _e('Select the voting period after which user can vote again.', 'wtg-like-post');?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Voting Style', 'wtg-like-post'); ?></label></th>
									<td>
										<?php
										$voting_style = get_option('wtg_like_post_voting_style');
										?>
										<select name="wtg_like_post_voting_style" id="wtg_like_post_voting_style">
											<option value="style1" <?php if ("style1" == $voting_style) echo "selected='selected'"; ?>><?php echo __('Style1', 'wtg-like-post'); ?></option>
											<option value="style2" <?php if ("style2" == $voting_style) echo "selected='selected'"; ?>><?php echo __('Style2', 'wtg-like-post'); ?></option>
											<option value="style3" <?php if ("style3" == $voting_style) echo "selected='selected'"; ?>><?php echo __('Style3', 'wtg-like-post'); ?></option>
										</select>
										<span class="description"><?php _e('Select the voting style from 3 available options with 3 different sets of images.', 'wtg-like-post'); ?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"<label><?php _e('Vote count displayed', 'wtg-like-post'); ?></label></th>
									<td>
										<input type="radio" name="wtg_like_post_show_vote_count" id="count_yes" value="1"
											<?php echo get_option('wtg_like_post_show_vote_count') == 1 ? 'checked="checked"' : NULL ?>
											/> <?php echo __('Yes', 'wtg-like-post'); ?>
										<input type="radio" name="wtg_like_post_show_vote_count" id="count_no" value="0"
											<?php echo get_option('wtg_like_post_show_vote_count') == 0 ? 'checked="checked"' : NULL ?>
											/> <?php echo __('No', 'wtg-like-post'); ?>
										<span class="description"><?php _e('Select whether you want the vote count to show next to the thumb icons'); ?></span>

									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Login required to vote', 'wtg-like-post'); ?></label></th>
									<td>	
										<input type="radio" name="wtg_like_post_login_required" id="login_yes" value="1" <?php if (1 == get_option('wtg_like_post_login_required')) { echo 'checked'; } ?> /> <?php echo __('Yes', 'wtg-like-post'); ?>
										<input type="radio" name="wtg_like_post_login_required" id="login_no" value="0" <?php if ((0 == get_option('wtg_like_post_login_required')) || ('' == get_option('wtg_like_post_login_required'))) { echo 'checked'; } ?> /> <?php echo __('No', 'wtg-like-post'); ?>
										<span class="description"><?php _e('Select whether only logged in users can vote or not.', 'wtg-like-post');?></span>
									</td>
								</tr>			
								<tr valign="top">
									<th scope="row"><label><?php _e('Login required message', 'wtg-like-post'); ?></label></th>
									<td>	
										<input type="text" size="40" name="wtg_like_post_login_message" id="wtg_like_post_login_message" value="<?php echo get_option('wtg_like_post_login_message'); ?>" />
										<span class="description"><?php _e('Message to show in case login required and user is not logged in.', 'wtg-like-post');?></span>
									</td>
								</tr>			
								<tr valign="top">
									<th scope="row"><label><?php _e('Thank you message', 'wtg-like-post'); ?></label></th>
									<td>	
										<input type="text" size="40" name="wtg_like_post_thank_message" id="wtg_like_post_thank_message" value="<?php echo get_option('wtg_like_post_thank_message'); ?>" />
										<span class="description"><?php _e('Message to show after successful voting.', 'wtg-like-post');?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Already voted message', 'wtg-like-post'); ?></label></th>
									<td>	
										<input type="text" size="40" name="wtg_like_post_voted_message" id="wtg_like_post_voted_message" value="<?php echo get_option('wtg_like_post_voted_message'); ?>" />
										<span class="description"><?php _e('Message to show if user has already voted.', 'wtg-like-post');?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Show on pages', 'wtg-like-post'); ?></label></th>
									<td>	
										<input type="radio" name="wtg_like_post_show_on_pages" id="show_pages_yes" value="1" <?php if (('1' == get_option('wtg_like_post_show_on_pages'))) { echo 'checked'; } ?> /> <?php echo __('Yes', 'wtg-like-post'); ?>
										<input type="radio" name="wtg_like_post_show_on_pages" id="show_pages_no" value="0" <?php if ('0' == get_option('wtg_like_post_show_on_pages') || ('' == get_option('wtg_like_post_show_on_pages'))) { echo 'checked'; } ?> /> <?php echo __('No', 'wtg-like-post'); ?>
										<span class="description"><?php _e('Select yes if you want to show the like option on pages as well.', 'wtg-like-post')?></span>
									</td>
								</tr>	
								<tr valign="top">
									<th scope="row"><label><?php _e('Exclude on selected sections', 'wtg-like-post'); ?></label></th>
									<td>
										<input type="checkbox" name="wtg_like_post_excluded_sections[]" id="wtg_like_post_excluded_home" value="home" <?php if (in_array('home', $excluded_sections)) { echo 'checked'; } ?> /> <?php echo __('Home', 'wtg-like-post'); ?>
										<input type="checkbox" name="wtg_like_post_excluded_sections[]" id="wtg_like_post_excluded_archive" value="archive" <?php if (in_array('archive', $excluded_sections)) { echo 'checked'; } ?> /> <?php echo __('Archive', 'wtg-like-post'); ?>
										<span class="description"><?php _e('Check the sections where you do not want to avail the like/dislike options. This has higher priority than the "Exclude post/page IDs" setting.', 'wtg-like-post');?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Exclude selected categories', 'wtg-like-post'); ?></label></th>
									<td>	
										<select name='wtg_like_post_excluded_categories[]' id='wtg_like_post_excluded_categories' multiple="multiple" size="4" style="height:auto !important;">
											<?php 
											$categories=  get_categories();
											
											foreach ($categories as $category) {
												$selected = (in_array($category->cat_ID, $excluded_categories)) ? 'selected="selected"' : '';
												$option  = '<option value="' . $category->cat_ID . '" ' . $selected . '>';
												$option .= $category->cat_name;
												$option .= ' (' . $category->category_count . ')';
												$option .= '</option>';
												echo $option;
											}
											?>
										</select>
										<span class="description"><?php _e('Select categories where you do not want to show the like option. It has higher priority than "Exclude post/page IDs" setting.', 'wtg-like-post');?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Allow post IDs', 'wtg-like-post'); ?></label></th>
									<td>	
										<input type="text" size="40" name="wtg_like_post_allowed_posts" id="wtg_like_post_allowed_posts" value="<?php _e(get_option('wtg_like_post_allowed_posts')); ?>" />
										<span class="description"><?php _e('Suppose you have a post which belongs to more than one categories and you have excluded one of those categories. So the like/dislike will not be available for that post. Enter comma separated those post ids where you want to show the like/dislike option irrespective of that post category being excluded.', 'wtg-like-post');?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Exclude post/page IDs', 'wtg-like-post'); ?></label></th>
									<td>	
										<input type="text" size="40" name="wtg_like_post_excluded_posts" id="wtg_like_post_excluded_posts" value="<?php _e(get_option('wtg_like_post_excluded_posts')); ?>" />
										<span class="description"><?php _e('Enter comma separated post/page ids where you do not want to show the like option. If Show on pages setting is set to Yes but you have added the page id here, then like option will not be shown for the same page.', 'wtg-like-post');?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Show excluded posts/pages on widget', 'wtg-like-post'); ?></label></th>
									<td>	
										<input type="radio" name="wtg_like_post_show_on_widget" id="show_widget_yes" value="1" <?php if (('1' == get_option('wtg_like_post_show_on_widget')) || ('' == get_option('wtg_like_post_show_on_widget'))) { echo 'checked'; } ?> /> <?php echo __('Yes', 'wtg-like-post'); ?>
										<input type="radio" name="wtg_like_post_show_on_widget" id="show_widget_no" value="0" <?php if ('0' == get_option('wtg_like_post_show_on_widget')) { echo 'checked'; } ?> /> <?php echo __('No', 'wtg-like-post'); ?>
										<span class="description"><?php _e('Select yes if you want to show the excluded posts/pages on widget.', 'wtg-like-post')?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Position Setting', 'wtg-like-post'); ?></label></th>
									<td>	
										<input type="radio" name="wtg_like_post_position" id="position_top" value="top" <?php if (('top' == get_option('wtg_like_post_position')) || ('' == get_option('wtg_like_post_position'))) { echo 'checked'; } ?> /> <?php echo __('Top of Content', 'wtg-like-post'); ?>
										<input type="radio" name="wtg_like_post_position" id="position_bottom" value="bottom" <?php if ('bottom' == get_option('wtg_like_post_position')) { echo 'checked'; } ?> /> <?php echo __('Bottom of Content', 'wtg-like-post'); ?>
										<span class="description"><?php _e('Select the position where you want to show the like options.', 'wtg-like-post')?></span>
									</td>
								</tr>			
								<tr valign="top">
									<th scope="row"><label><?php _e('Alignment Setting', 'wtg-like-post'); ?></label></th>
									<td>	
										<input type="radio" name="wtg_like_post_alignment" id="alignment_left" value="left" <?php if (('left' == get_option('wtg_like_post_alignment')) || ('' == get_option('wtg_like_post_alignment'))) { echo 'checked'; } ?> /> <?php echo __('Left', 'wtg-like-post'); ?>
										<input type="radio" name="wtg_like_post_alignment" id="alignment_right" value="right" <?php if ('right' == get_option('wtg_like_post_alignment')) { echo 'checked'; } ?> /> <?php echo __('Right', 'wtg-like-post'); ?>
										<span class="description"><?php _e('Select the alignment whether to show on left or on right.', 'wtg-like-post')?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Title text for like/unlike images', 'wtg-like-post'); ?></label></th>
									<td>
										<input type="text" name="wtg_like_post_title_text" id="wtg_like_post_title_text" value="<?php echo get_option('wtg_like_post_title_text')?>" />
										<span class="description"><?php echo __('Enter both texts separated by "/" to show when user puts mouse over like/unlike images.', 'wtg-like-post')?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Show dislike option', 'wtg-like-post'); ?></label></th>
									<td>	
										<input type="radio" name="wtg_like_post_show_dislike" id="show_dislike_yes" value="1" <?php if (('1' == get_option('wtg_like_post_show_dislike')) || ('' == get_option('wtg_like_post_show_dislike'))) { echo 'checked'; } ?> /> <?php echo __('Yes', 'wtg-like-post'); ?>
										<input type="radio" name="wtg_like_post_show_dislike" id="show_dislike_no" value="0" <?php if ('0' == get_option('wtg_like_post_show_dislike')) { echo 'checked'; } ?> /> <?php echo __('No', 'wtg-like-post'); ?>
										<span class="description"><?php _e('Select the option whether to show or hide the dislike option.', 'wtg-like-post')?></span>
									</td>
								</tr>	
								<tr valign="top">
									<th scope="row"><label><?php _e('Show +/- symbols', 'wtg-like-post'); ?></label></th>
									<td>	
										<input type="radio" name="wtg_like_post_show_symbols" id="show_symbol_yes" value="1" <?php if (('1' == get_option('wtg_like_post_show_symbols')) || ('' == get_option('wtg_like_post_show_symbols'))) { echo 'checked'; } ?> /> <?php echo __('Yes', 'wtg-like-post'); ?>
										<input type="radio" name="wtg_like_post_show_symbols" id="show_symbol_no" value="0" <?php if ('0' == get_option('wtg_like_post_show_symbols')) { echo 'checked'; } ?> /> <?php echo __('No', 'wtg-like-post'); ?>
										<span class="description"><?php _e('Select the option whether to show or hide the plus or minus symbols before like/unlike count.', 'wtg-like-post')?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"></th>
									<td>
										<input class="button-primary" type="submit" name="Save" value="<?php _e('Save Options', 'wtg-like-post'); ?>" />
										<input class="button-secondary" type="submit" name="Reset" value="<?php _e('Reset Options', 'wtg-like-post'); ?>" onclick="return confirmReset()" />
									</td>
								</tr>
							</table>
						</form>
					</div>
				</div>
			</div>		
		</div>
	
		<script>
		function confirmReset()
		{
			// Check whether user agrees to reset the settings to default or not
			var check = confirm("<?php _e('Are you sure to reset the options to default settings?', 'wtg-like-post')?>");
			
			if (check) {
				// Reset the settings
				document.getElementById('wtg_like_post_voting_period').value = 0;
				document.getElementById('wtg_like_post_voting_style').value = 'style1';
				document.getElementById('login_yes').checked = false;
				document.getElementById('login_no').checked = true;
				document.getElementById('wtg_like_post_login_message').value = 'Please login to vote.';
				document.getElementById('wtg_like_post_thank_message').value = 'Thanks for your vote.';
				document.getElementById('wtg_like_post_voted_message').value = 'You have already voted.';
				document.getElementById('show_pages_yes').checked = false;
				document.getElementById('show_pages_no').checked = true;
				document.getElementById('wtg_like_post_allowed_posts').value = '';
				document.getElementById('wtg_like_post_excluded_posts').value = '';
				document.getElementById('wtg_like_post_excluded_categories').selectedIndex = -1;
				document.getElementById('wtg_like_post_excluded_home').value = '';
				document.getElementById('wtg_like_post_excluded_archive').value = '';
				document.getElementById('show_widget_yes').checked = true;
				document.getElementById('show_widget_no').checked = false;
				document.getElementById('position_top').checked = false;
				document.getElementById('position_bottom').checked = true;
				document.getElementById('alignment_left').checked = true;
				document.getElementById('alignment_right').checked = false;
				document.getElementById('show_symbol_yes').checked = true;
				document.getElementById('show_symbol_no').checked = false;
				document.getElementById('show_dislike_yes').checked = true;
				document.getElementById('show_dislike_no').checked = false;
				document.getElementById('wtg_like_post_title_text').value = 'Like/Unlike';
				
				return true;
			}
			
			return false;
		}
		
		function processAll()
		{
			var cfm = confirm('<?php echo __('Are you sure to reset all the counts present in the database?', 'wtg-like-post')?>');
			
			if (cfm) {
				return true;
			} else {
				return false;
			}
		}
		
		function processSelected()
		{
			var cfm = confirm('<?php echo __('Are you sure to reset selected counts present in the database?', 'wtg-like-post')?>');
			
			if (cfm) {
				return true;
			} else {
				return false;
			}
		}
		</script>
		
		<?php
		if (isset($_POST['resetall'])) {
			$status = $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}wtg_like_post");
			if ($status) {
				echo '<div class="updated" id="message"><p>';
				echo __('All counts have been reset successfully.', 'wtg-like-post');
				echo '</p></div>';
			} else {
				echo '<div class="error" id="error"><p>';
				echo __('All counts could not be reset.', 'wtg-like-post');
				echo '</p></div>';
			}
		}
		if (isset($_POST['resetselected'])) {
			if (count($_POST['post_ids']) > 0) {
                                $post_ids = implode(",", array_map('absint', $_POST['post_ids']));
				$status = $wpdb->query("DELETE FROM {$wpdb->prefix}wtg_like_post WHERE post_id IN ($post_ids)");
				if ($status) {
					echo '<div class="updated" id="message"><p>';
					if ($status > 1) {
						echo $status . ' ' . __('counts have been reset successfully.', 'wtg-like-post');
					} else {
						echo $status . ' ' . __('count has been reset successfully.', 'wtg-like-post');
					}
					echo '</p></div>';
				} else {
					echo '<div class="error" id="error"><p>';
					echo __('Selected counts could not be reset.', 'wtg-like-post');
					echo '</p></div>';
				}
			} else {
				echo '<div class="error" id="error"><p>';
				echo __('Please select posts to reset count.', 'wtg-like-post');
				echo '</p></div>';
			}
		}
		?>
		
		<div class="ui-sortable meta-box-sortables">
			<h2><?php _e('Most Liked Posts', 'wtg-like-post');?></h2>
			<?php
			// Getting the most liked posts
			$query = "SELECT COUNT(post_id) AS total FROM `{$wpdb->prefix}wtg_like_post` L JOIN {$wpdb->prefix}posts P ";
			$query .= "ON L.post_id = P.ID WHERE value > 0";
			$post_count = $wpdb->get_var($query);
	   
			if ($post_count > 0) {
	
				// Pagination script
				$limit = get_option('posts_per_page');
				$current = isset($_GET['paged']) ? max( 1, $_GET['paged'] ) : 1;
				$total_pages = ceil($post_count / $limit);
				$start = $current * $limit - $limit;
				
				$query = "SELECT post_id, SUM(value) AS like_count, SUM(dislike_value) AS dislike_count, post_title FROM `{$wpdb->prefix}wtg_like_post` L JOIN {$wpdb->prefix}posts P ";
                                $query .= $wpdb->prepare("ON L.post_id = P.ID WHERE value > 0 OR dislike_value > 0 GROUP BY post_id ORDER BY like_count DESC, post_title LIMIT %d, %d", $start, $limit);
				$result = $wpdb->get_results($query);
				?>
				<form method="post" action="<?php echo admin_url('options-general.php?page=WtgLikePostAdminMenu'); ?>" name="most_liked_posts_form" id="most_liked_posts_form">
					<div style="float:left">
						<input class="button-secondary" type="submit" name="resetall" id="resetall" onclick="return processAll()" value="<?php echo __('Reset All Counts', 'wtg-like-post')?>" />
						<input class="button-secondary" type="submit" name="resetselected" id="resetselected" onclick="return processSelected()" value="<?php echo __('Reset Selected Counts', 'wtg-like-post')?>" />
					</div>
					<div style="float:right">
						<div class="tablenav top">
							<div class="tablenav-pages">
								<span class="displaying-num"><?php echo $post_count?> <?php echo __('items', 'wtg-like-post'); ?></span>
								<?php
								echo paginate_links(
											array(
												'current' 	=> $current,
												'prev_text'	=> '&laquo; ' . __('Prev', 'wtg-like-post'),
												'next_text'    	=> __('Next', 'wtg-like-post') . ' &raquo;',
												'base' 		=> @add_query_arg('paged','%#%'),
												'format'  	=> '?page=WtgLikePostAdminMenu',
												'total'   	=> $total_pages
											)
								);
								?>
							</div>
						</div>
					</div>
					<?php
					echo '<table cellspacing="0" class="wp-list-table widefat fixed likes">';
					echo '<thead><tr><th class="manage-column column-cb check-column" id="cb" scope="col">';
					echo '<input type="checkbox" id="checkall">';
					echo '</th><th>';
					_e('Post Title', 'wtg-like-post');
					echo '</th><th>';
					_e('Like Count', 'wtg-like-post');
					echo '</th><th>';
					_e('Dislike Count', 'wtg-like-post');
					echo '</th><tr></thead>';
					echo '<tbody class="list:likes" id="the-list">';
					
					foreach ($result as $post) {
						$post_title = stripslashes($post->post_title);
						$permalink = get_permalink($post->post_id);
						$like_count = $post->like_count;
						$dislike_count = $post->dislike_count;
						
						echo '<tr>';
						echo '<th class="check-column" scope="row" align="center"><input type="checkbox" value="' . $post->post_id . '" class="administrator" id="post_id_' . $post->post_id . '" name="post_ids[]"></th>';
						echo '<td><a href="' . $permalink . '" title="' . $post_title . '" target="_blank">' . $post_title . '</a></td>';
						echo '<td>' . $like_count . '</td>';
						echo '<td>' . $dislike_count . '</td>';
						echo '</tr>';
					}
		 
					echo '</tbody></table>';
				?>
				</form>
				<?php
			} else {
				echo '<p>';
				echo __('No posts liked yet.', 'wtg-like-post');
				echo '</p>';
			}
			?>
		</div>
     </div>
</div>
<?php
}

// For adding metabox for posts/pages
add_action('admin_menu', 'WtgLikePostAddMetaBox');

/**
 * Metabox for for like post
 * @param no-param
 * @return no-return
 */
function WtgLikePostAddMetaBox() {
	// Add the meta box for posts/pages
     add_meta_box('wtg-like-post-meta-box', __('WTG Like Post Exclude Option', 'wtg-like-post'), 'WtgLikePostShowMetaBox', 'post', 'side', 'high');
     add_meta_box('wtg-like-post-meta-box', __('WTG Like Post Exclude Option', 'wtg-like-post'), 'WtgLikePostShowMetaBox', 'page', 'side', 'high');
}

/**
 * Callback function to show fields in meta box
 * @param no-param
 * @return string
 */
function WtgLikePostShowMetaBox() {
     global $post;

     // Use nonce for verification
     echo '<input type="hidden" name="wtg_like_post_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

     // Get whether current post is excluded or not
	$excluded_posts = explode(',', get_option('wtg_like_post_excluded_posts'));
	if (in_array($post->ID, $excluded_posts)) {
		$checked = 'checked="checked"';
	} else {
		$checked = '';
	}

     echo '<p>';    
     echo '<label for="wtg_exclude_post"><input type="checkbox" name="wtg_exclude_post" id="wtg_exclude_post" value="1" ', $checked, ' /> ';
	echo __('Check to disable like/unlike functionality', 'wtg-like-post');
     echo '</label>';
     echo '</p>';
}

add_action('save_post', 'WtgLikePostSaveData');

/**
 * Save data from meta box
 * @param no-param
 * @return string
 */
function WtgLikePostSaveData($post_id) {
     // Verify nonce
     if (isset($_POST['wtg_like_post_meta_box_nonce']) &&
		 ! wp_verify_nonce($_POST['wtg_like_post_meta_box_nonce'], basename(__FILE__))) {
          return $post_id;
     }
    
     // Check autosave
     if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
          return $post_id;
     }
    
     // Check permissions
     if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
          if (!current_user_can('edit_page', $post_id)) {
               return $post_id;
          }
     } elseif (!current_user_can('edit_post', $post_id)) {
          return $post_id;
     }

	// Initialise the excluded posts array
	$excluded_posts = array();
	
	// Check whether this post/page is to be excluded
	$exclude_post = isset($_POST['wtg_exclude_post']) ? $_POST['wtg_exclude_post'] : FALSE;
	
	// Get old excluded posts/pages
	if (strlen(get_option('wtg_like_post_excluded_posts')) > 0) {
		$excluded_posts = explode(',', get_option('wtg_like_post_excluded_posts'));
	}
	
	if ($exclude_post == 1 && !in_array($_POST['ID'], $excluded_posts)) {
		// Add this post/page id to the excluded list
		$excluded_posts[] = $_POST['ID'];
		
		if (!empty($excluded_posts)) {
			// Since there are already excluded posts/pages, add this as a comma separated value
			update_option('wtg_like_post_excluded_posts', implode(',', $excluded_posts));
		} else {
			// Since there is no old excluded post/page, add this directly
			update_option('wtg_like_post_excluded_posts', $_POST['ID']);
		}
	} else if (!$exclude_post) {
		// Check whether this id is already in the excluded list or not
		$key = isset($_POST['ID']) ? array_search($_POST['ID'], $excluded_posts) : FALSE;
		
		if ($key !== false) {
			// Since this is already in the list, so exluded this
			unset($excluded_posts[$key]);
			
			// Update the excluded posts list
			update_option('wtg_like_post_excluded_posts', implode(',', $excluded_posts));
		}
	}
}

/**
 * Additional links on plugins page
 * 
 * @param array
 * @param string
 * @return array
 */
function WtgLikePostSetPluginMeta( $links, $file ) {
	if ( strpos( $file, 'wtg-like-post/wtg_like_post.php' ) !== false ) {
		$new_links = array(
						'<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=support@webtechideas.com&item_name=WTG%20Like%20Post&return=http://www.webtechideas.com/thanks/" target="_blank">' . __( 'Donate', 'wtg-like-post' ) . '</a>',
						'<a href="http://www.webtechideas.com/product/wtg-like-post-pro/" target="_blank">' . __( 'PRO Version', 'wtg-like-post' ) . '</a>',
						'<a href="http://support.webtechideas.com/forums/forum/wtg-like-post-pro/" target="_blank">' . __( 'PRO Support Forum', 'wtg-like-post' ) . '</a>',
					);
		
		$links = array_merge( $links, $new_links );
	}
	
	return $links;
}

// add_filter( 'plugin_row_meta', 'WtgLikePostSetPluginMeta', 10, 2 );
