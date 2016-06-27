<?php /* Template Name: Single Event */ ?>

<?php the_post(); ?>
<section class="event-single">
  <article <?php post_class('article group'); ?>>
    <?php
      $args = array(
        'post_type'   => 'event',
        'posts_per_page' => 1,
        'p' => get_the_ID(),
      );
      $the_query = new WP_Query($args);
    ?>

    <?php if( $the_query->have_posts() ): ?>
      <?php while( $the_query->have_posts() ) : $the_query->the_post(); ?>

      <header>
        <h1><?php the_title(); ?></h1>
      </header>

      <?php
        $venue = get_post_meta( get_the_ID(), 'venue' , true );
        $city = get_post_meta( get_the_ID(), 'city' , true );
        $postal_code = get_post_meta( get_the_ID(), 'postal_code' , true );
        $region = get_post_meta( get_the_ID(), 'region' , true );
        $start_date = get_post_meta( get_the_ID(), 'start_date', true);
        $end_date = get_post_meta( get_the_ID(), 'end_date' , true );
        $eventbrite_public_uri = get_post_meta( get_the_ID(), 'eventbrite-sync_public-uri' , true );
      ?>

      <div class="event-meta group">
        <ul class="when-where">
            <?php if ($venue) : ?>
            <li itemprop="location" itemscope itemtype="http://schema.org/Place">
              <?php h()->svgIcon('locationpin') ?>
              <?php echo esc_html($venue); ?></li>
            <?php endif; ?>
            <?php if ($start_date) : ?>
            <li itemprop="startDate" content="2016-04-21T20:00">
              <?php h()->svgIcon('calendar') ?>
              <?php echo esc_html(date('l jS M Y', strtotime($start_date))); ?></li>
            <?php endif; ?>
            <?php if ($start_date) : ?>
            <li itemprop="startDate" content="2016-04-21T20:00">
              <?php h()->svgIcon('clock1') ?>
              Starts at <?php echo esc_html(date('g:iA', strtotime($start_date))); ?></li>
            <?php endif; ?>
            <?php if ($end_date) : ?>
              <li itemprop="endDate" content="2016-04-21T20:00">
              <?php h()->svgIcon('clock2') ?>
              Ends at <?php echo esc_html(date('g:iA', strtotime($end_date))); ?></li>
            <?php endif; ?>
          </ul>

          <div class="event-actions">
            <?php if ($eventbrite_public_uri !== '') : ?>
              <a href="<?php echo esc_html($eventbrite_public_uri); ?>" class="button">Register for this event</a>
            <?php endif; ?>
            <a href="<?php echo site_url('/upcoming-events/') ?>" class="jump-back"><?php h()->svgIcon('back') ?> Back to the list</a>
          </div>
      </div>

      <div class="rich-text entry">
        <?php the_content(); ?>
      </div>

      <?php endwhile; ?>
    <?php endif; ?>

  </article>
</section>

<aside class="sidebar group" role="complementary">
  <?php get_template_part('partials/service-links') ?>
  <?php get_template_part('partials/events/events-sidebar'); ?>
</aside>
