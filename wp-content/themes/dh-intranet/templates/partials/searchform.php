<form method="get" class="sidebar-search-form" action="<?php echo home_url('/'); ?>">
  <label for="searchbox"><?php _e('Search for:', 'roots'); ?></label>
  <input id="searchbox" type="search" value="<?php if (is_search()) {echo get_search_query(); } ?>" name="s">
  <button type="submit" class="button"><?php _e('Search', 'roots'); ?></button>
</form>
