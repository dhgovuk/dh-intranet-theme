<button class="nav-toggle" id="js-navigation-toggle">Menu</button>
<div class="nav-container group">
  <nav class="navigation" id="js-navigation">
    <?php
      if (has_nav_menu('primary_navigation')) {
        $defaults = array(
          'theme_location' => 'primary_navigation',
          'depth' => 2,
          );
        $primary_navigation = wp_nav_menu($defaults);
      }
    ?>
    <?php $primary_navigation; ?>

    <?php if (class_exists('\\DHIntranet\\IT_Updates')) {
      \DHIntranet\IT_Updates::display_overall_status();
    } ?>

    <div class="user-welcome">
    <ul>
      <?php if (is_user_logged_in()) : ?>
      <li><a href="<?php echo wp_logout_url( home_url() ); ?>">Sign out</a></li>
      <li><a href="<?php echo admin_url('profile.php') ?>">My profile</a></li>
      <?php else : ?>
      <li><a href="<?php echo wp_login_url(); ?>">Sign in</a></li>
      <?php endif; ?>
    </ul>
    </div>

  </nav>
</div>