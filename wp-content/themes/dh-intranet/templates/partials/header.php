<div class="top-navigation-bar group">

  <nav class="top-navigation" role="navigation">
    <?php //suppress all wrappers around the navigation <li> items
    $defaults = array(
      'theme_location' => 'top_nav',
      'menu' => '',
      'container' => '',
      'container_class' => '',
      'container_id' => '',
      'menu_class' => '',
      'menu_id' => '',
      'echo' => 0,
      'fallback_cb' => 'wp_page_menu',
      'before' => '',
      'after' => '',
      'link_before' => '',
      'link_after' => '',
      'items_wrap' => '%3$s',
      'depth' => 0,
      'walker' => '',
      );
    $internalLinks = wp_nav_menu($defaults); ?>
    <?php if (is_ip_whitelisted()) : ?>
      <ul>
        <?php echo $internalLinks; ?>
        <?php if (class_exists('\\DHIntranet\\IT_Updates')) {\DHIntranet\IT_Updates::display_overall_status(); } ?>
      </ul>
    <?php else : ?>
      <ul>
        <?php if (class_exists('\\DHIntranet\\IT_Updates')) {\DHIntranet\IT_Updates::display_overall_status(); } ?>
      </ul>
    <?php endif ?>
  </nav>

</div>

