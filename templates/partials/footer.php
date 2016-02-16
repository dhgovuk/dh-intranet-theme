<footer class="footer group">
  <div class="row">

  <nav class="footer-navigation group">
    <?php
      if (has_nav_menu('footer_navigation')) {
        $defaults = array(
          'theme_location' => 'footer_navigation',
          'depth' => 1,
        );
        $footer_navigation = wp_nav_menu($defaults);
      }
    ?>
    <?php $footer_navigation; ?>
  </nav>

    <div class="footer-message">
      <?php if (is_home()) : ?>
        <p>We're always looking to improve the intranet - <a href="mailto:intranet@dh.gsi.gov.uk">email</a> the DH intranet team if you have any great ideas.</p>
      <?php endif ?>
    </div>

  </div>

</footer>

<?php wp_footer(); ?>
