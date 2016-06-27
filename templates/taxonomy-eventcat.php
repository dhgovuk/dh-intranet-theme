<?php
$today = date('Y-m-d');
$term = get_queried_object()->slug;
$args = array(
    'post_type' => 'event',
    'meta_key' => 'start_date',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => 'start_date',
            'value' => $today,
            'compare' => '>=',
        ),
    ),
    'tax_query' => array(
              array(
                'taxonomy' => 'eventcat',
                'field'    => 'slug',
                'terms'    => $term,
        ),
    ),
);
$events = new WP_Query($args);
?>
<section class="events-section group">

    <div class="events-section-content">
        <header>
            <h1><?php echo get_queried_object()->name; ?></h1>
            <?php if (get_queried_object()->description !== '') : ?>
            <div class="rich-text entry">
                <p><?php echo get_queried_object()->description; ?></p>
            </div>
            <?php endif; ?>
        </header>
        <aside class="sidebar events-section-sidebar">
            <?php get_template_part('partials/service-links'); ?>
        </aside>
    </div>

    <article class="event-category">
        <?php while ($events->have_posts()) : ?>
        <?php $events->the_post(); ?>
            <?php get_template_part('partials/events/event-item'); ?>
        <?php endwhile; ?>
    </article>

</section>


