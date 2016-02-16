<?php if ($wp_query->max_num_pages > 1) : ?>

  <?php \DHIntranet\Pages::pagination(); ?>

<?php endif; ?>
