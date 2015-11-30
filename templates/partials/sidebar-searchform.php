<form method="get" class="sidebar-search-form" action="<?php echo home_url('/'); ?>">
  <label for="searchbox" class="hide"><?php _e('Search for:', 'roots'); ?></label>
  <input id="searchbox" class="search-box" type="search" value="<?php if (is_search()) {echo get_search_query(); } ?>" name="s" placeholder="Search">
</form>
