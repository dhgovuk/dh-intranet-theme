<?php
  $venue = get_post_meta( get_the_ID(), 'venue' , true );
  $city = get_post_meta( get_the_ID(), 'city' , true );
  $postal_code = get_post_meta( get_the_ID(), 'postal_code' , true );
  $region = get_post_meta( get_the_ID(), 'region' , true );
  $start_date = get_post_meta( get_the_ID(), 'start_date', true);
  $end_date = get_post_meta( get_the_ID(), 'end_date' , true );
?>

<div class="event-item group" itemscope itemtype="http://schema.org/Event">

    <div class="event-summary group">
        <div class="event-date" itemprop="startDate" content="<?php echo $start_date; ?>">
          <span class="day"><?php echo esc_html(date('d', strtotime($start_date))); ?></span>
          <span class="month"><?php echo esc_html(date('M', strtotime($start_date))); ?></span>
        </div>

        <div class="event-name"><h3 itemprop="name"><a itemprop="url" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3></div>
    </div>

    <div class="event-details">
        <ul class="event-meta">
            <?php if ($venue) : ?>
            <li itemprop="location" itemscope itemtype="http://schema.org/Place">
            <?php h()->svgIcon('locationpin') ?>
            <?php echo esc_html($venue); ?></li>
            <?php endif; ?>

            <?php if ($start_date) : ?>
            <li itemprop="startDate" content="<?php echo $start_date; ?>">
            <?php h()->svgIcon('calendar') ?>
            <?php echo esc_html(date('l jS F Y', strtotime($start_date))); ?> @ <?php echo esc_html(date('g:iA', strtotime($start_date))); ?></li>
            <?php endif; ?>
        </ul>
        <div itemprop="description" class="rich-text entry"><?php the_excerpt(); ?></div>

    </div>

    <a itemprop="url" href="<?php the_permalink(); ?>" class="button button--small">View details</a>

</div>
