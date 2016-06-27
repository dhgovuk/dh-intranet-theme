<section class="events-widget group">
  <h3>Upcoming Events</h3>
  <?php
  $today = date('Y-m-d');
  $args = array(
    'post_type'   => 'event',
    'posts_per_page' => 3,
    'meta_key' => 'start_date',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => 'start_date',
            'value' => $today,
            'compare' => '>=',
        ),
    ),
  );
  $the_query = new WP_Query($args);
  ?>
  <?php if( $the_query->have_posts() ): ?>
    <?php while( $the_query->have_posts() ) : $the_query->the_post(); ?>

      <?php get_template_part('partials/events/event-widget-item'); ?>

    <?php endwhile; ?>
  <?php endif; ?>

  <a href="<?php echo site_url('/upcoming-events/') ?>" class="button">More events</a>
</section>
