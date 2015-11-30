<?php get_template_part('partials/head'); ?>
<body <?php body_class(); ?>>

    <!--[if lt IE 7]>
    <div class="alert">
        <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'roots'); ?>
    </div>
    <![endif]-->

    <?php
    if (has_nav_menu('primary_navigation')) {
      $defaults = array(
        'theme_location' => 'primary_navigation',
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
      $primary_navigation = wp_nav_menu($defaults);

      /* build appropriate link server side in php */
      if (is_user_logged_in()) {
        if (is_user_logged_in() && (current_user_can('edit_pages') || is_super_admin())) {
          $primary_navigation .= '<li class="navspacer"><a href="'.admin_url().'">Editor suite</a></li>';
      } else {
        $primary_navigation .= '<li class="navspacer"><a href="'.admin_url().'/profile.php">My profile</a></li>';
      }
      $primary_navigation .= '<li><a href="'.get_bloginfo('url').'/my-subscriptions/">My subscriptions</a></li>';
    } else {
      $primary_navigation .= '<li class="navspacer"><a href="'.get_bloginfo('url').'/sign-up">Register</a></li>';
      $primary_navigation .= '<li><a href="'.wp_login_url().'">Sign in</a></li>';
    }
  }
  ?>

  <header class="header header-sidebar" role="banner">
    <div class="nav-toggle" id="js-navigation-toggle">Menu</div>
    <div class="logo">
      <a href="/" title="<?php _e('Go back to the homepage', 'roots'); ?>">
        <img src="<?php echo get_template_directory_uri(); ?>/../assets/img/department-of-health-logo.png" alt="Department of Health logo">
      </a>
      <h1><a href="/" title="<?php _e('Go back to the homepage', 'roots'); ?>">Department of Health</a></h1>
    </div>

    <nav class="navigation navigation-sidebar" id="js-navigation" role="navigation">
      <ul class="nav nav-sidebar">
        <?php if (!is_home()) : ?>
        <li><?php get_template_part('partials/sidebar-searchform'); ?></li>
      <?php endif; ?>
      <?php echo $primary_navigation; ?>
    </ul>
  </nav>

</header>

<main class="main group" role="main">

  <?php do_action('get_header'); // does nothing - no header.php in root to load - needed for hooks? ?>
  <?php get_template_part('partials/header'); ?>
  <?php get_template_part('partials/emergency-message'); ?>
  <?php include roots_template_path(); ?>

</main>

  <?php get_template_part('partials/footer'); ?>

</body>
</html>
