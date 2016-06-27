<?php
  $venue = get_post_meta( get_the_ID(), 'venue' , true );
  $city = get_post_meta( get_the_ID(), 'city' , true );
  $postal_code = get_post_meta( get_the_ID(), 'postal_code' , true );
  $region = get_post_meta( get_the_ID(), 'region' , true );
  $start_date = get_post_meta( get_the_ID(), 'start_date', true);
  $end_date = get_post_meta( get_the_ID(), 'end_date' , true );
?>

<div class="event-item event-widget-item" itemscope itemtype="http://schema.org/Event">
  <a itemprop="url" href="<?php the_permalink(); ?>" class="group">

    <div class="event-date" itemprop="startDate" content="<?php echo $start_date; ?>">
      <span class="day"><?php echo esc_html(date('d', strtotime($start_date))); ?></span>
      <span class="month"><?php echo esc_html(date('M', strtotime($start_date))); ?></span>
    </div>

    <div class="event-name">
      <h4 itemprop="name"><?php the_title(); ?></h4>
    </div>

    <?php if ($venue) : ?>
    <ul class="event-meta">
      <li itemprop="location" itemscope itemtype="http://schema.org/Place">
      <?php h()->svgIcon('locationpin') ?> <?php echo esc_html($venue); ?> </li>
    </ul>
  <?php endif; ?>
  </a>
</div>
