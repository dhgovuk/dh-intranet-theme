<?php get_template_part('partials/head'); ?>
<body <?php body_class(); ?>>

  <!--[if lt IE 7]>
  <div class="alert">
      <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.', 'roots'); ?>
  </div>
  <![endif]-->

  <?php do_action('get_header'); // does nothing - no header.php in root to load - needed for hooks? ?>
  <?php get_template_part('partials/header'); ?>

<main class="main group" role="main">

  <?php if (get_field('emergency_message', 'option')): ?>
    <div class="emergency-message"><?php the_field('emergency_message', 'option'); ?></div>
  <?php endif; ?>

  <?php include roots_template_path() ?>

</main>

  <?php get_template_part('partials/footer'); ?>

</body>
</html>
