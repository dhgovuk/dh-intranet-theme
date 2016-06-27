<header class="header" role="banner">
  <div class="row">

    <div class="logo group">
      <a href="/" title="<?php _e('Go back to the homepage', 'roots'); ?>">
        <img src="<?php h()->assetPath('img/logo.png') ?>" alt="Department of Health logo">
      </a>
      <h1><a href="/" title="<?php _e('Go back to the homepage', 'roots'); ?>">Department of Health</a></h1>
    </div>

    <?php get_template_part('partials/searchform'); ?>

    <?php get_template_part('partials/navigation'); ?>

  </div>
</header>
