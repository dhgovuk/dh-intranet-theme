<?php

	/* 
		This file displays the admin form.
		It is broken down into two parts. The first form allows you to add categories to the news aggregation page.
		The second lets you reorder how they are displayed.
	*/


	/* Checks to see if a category has already been added to the options data */
	function checkForCat($cat,$opts) {

		$result = false;

		if (!empty($opts)) {

			if (in_array($cat,$opts)) {
				$result = true;
			}
		}

		return $result;

	}

	/* Get all the categories data */
	$args = array('hierarchical'=>1,'hide_empty'=>0);
	$cats = get_categories($args);

	$news_title_length = newsGetTitleLength();

?>
<link rel="stylesheet" href="<?php echo plugins_url();?>/dh-news-aggregation/css/style.css" />
<div class="wrap">
<?php echo (!empty($message)?'<p>'.$message.'</p>':'') ?>
	<div style="background:white; width: 600px; border:dotted; border-width: 1px; padding:3px; padding-left:10px">
		<h1>News Aggregation Control Panel</h1>
		<br>
		<form method="POST" action="" class="">
			<table style="width:500px; border-spacing:0px">
				<tbody>
					<tr>
						<th style="text-align: left; width:60%">Category</th>
						<th style="text-align: left;">Display</th>
						<th style="text-align: left;">Up</th>
						<th style="text-align: left;">Down</th>
					</tr>
					<?php foreach ($opt as $i => $o) { ?>
						<tr class="category_container">
							<td><label for="<?php echo str_replace(' ','_',get_the_category_by_ID($o)); ?>"><?php echo get_the_category_by_ID($o); ?></label></td>
							<td><input type="checkbox" id="<?php echo str_replace(' ','_',get_the_category_by_ID($o)); ?>" value="<?php echo $o; ?>" name="cats[]" CHECKED /></td>
							<td><a class="na_upRow" href="#" alt="Move up" style="text-decoration: none">˄</a></td>
							<td><a class="na_downRow" href="#" alt="Move down" style="text-decoration: none">˅</a></td>
						</tr>
					<?php } ?>
					<?php foreach ($cats as $c) { ?>
						<?php if(!checkForCat($c->cat_ID,$opt)){ ?>
							<tr class="category_container">
								<td><label for="<?php echo str_replace(' ','_',$c->name); ?>"><?php echo $c->name; ?></label></td>
								<td><input type="checkbox" id="<?php echo str_replace(' ','_',$c->name); ?>" value="<?php echo $c->cat_ID; ?>" name="cats[]" /></td>
								<td><a class="na_upRow" href="#" alt="Move up" style="text-decoration: none">˄</a></td>
								<td><a class="na_downRow" href="#" alt="Move down" style="text-decoration: none">˅</a></td>
							</tr>
						<?php } ?>
					<?php } ?>
				</tbody>
			</table>
			<br><br>
			Maximum title length in characters: <input name="news_title_length" type="text" value="<?php echo $news_title_length; ?>" maxlength="3" size="5">
			<br><br>
			<input type="submit" class="button-primary" name="submit" value="Save Changes"/>
			<br><br>
		</form>
	</div>
	<script src="<?php echo plugins_url();?>/dh-news-aggregation/js/control.js"></script>
</div>