<div class="search-form">
  <form method="get" action="<?php echo home_url('/'); ?>">
    <label for="search-input"><?php _e('Search the site', 'roots'); ?></label>
    <input id="search-input" type="search" value="<?php if (is_search()) {echo get_search_query(); } ?>" name="s">
    <button type="submit" class="button"><img src="<?php echo get_template_directory_uri(); ?>/../assets/img/icon-search.svg"><span><?php _e('Search', 'roots'); ?></span></button>
  </form>
</div>
