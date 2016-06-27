<div class="search-form">
  <form method="get" action="<?php echo home_url('/'); ?>">
    <label for="search-input"><?php _e('Search the site', 'roots'); ?></label>
    <input id="search-input" type="search" value="<?php if (is_search()) {echo get_search_query(); } ?>" name="s">
    <button type="submit" class="button"><img src="<?php h()->assetPath('img/icon-search.svg') ?>"><span><?php _e('Search', 'roots'); ?></span></button>
  </form>
</div>
