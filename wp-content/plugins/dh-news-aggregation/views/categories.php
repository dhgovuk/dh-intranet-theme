<?php

/*
	This file displays the relevant category headers. 
	Then initiates an Ajax call to pull in the post data. 
	See js/cats.js
	Some styling is controlled via the style sheet...
	css/style.css
*/

$newsletterCategories = dh_subscriptions::getInstance()->get_newsletter_categories();
$userCategoies = dh_subscriptions::getInstance()->get_user_feed_categories();

?>
<link rel="stylesheet" href="<?php echo plugins_url();?>/dh-news-aggregation/css/style.css" />
<div class="wrap hide-in-search-results">
	<div class="dhCats">
	<?php

		$cats = get_option($name,false);

		if (!empty($cats)) {
			$cats = json_decode($cats);

			foreach ($cats as $c) {
				// echo '<pre>';
				// print_r($c);
				// print_r(get_the_category_by_ID($c));
				// echo '</pre>';
	?>

        <div class="row articles">
            <div id="news_cat_<?php echo $c ?>" data-header="#news_cat_<?php echo $c ?>_header">
                <h2><a href="<?php echo get_category_link($c); ?>" title="<?php echo get_the_category_by_ID($c); ?>"><?php echo get_the_category_by_ID($c); ?></a><?php dh_category_subscribe_checkbox($c) ?></h2>
            </div>
            <?php wtg_category_slider("#news_cat_{$c}", $c) ?>
        </div>
		<div class="catRow">
			<a class="button pull-right btn last-child" href="<?php echo get_category_link($c); ?>" title="More News... <?php echo get_the_category_by_ID($c); ?>">More News <span class="glyphicon glyphicon-chevron-right last-child"></span></a>
		</div>
	<?php

			}

		}

	?>
	</div>
</div>