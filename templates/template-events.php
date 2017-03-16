<?php /* Template Name: Events Listing */ ?>
<?php the_post(); ?>

<?php
$args = array(
    'taxonomy' => 'eventcat',
    'hide_empty' => 1,
    'fields' => 'all',
);
$categories = get_terms($args);

$cats = [];

foreach ($categories as $cat) {
    $today = date('Y-m-d');
    $args = array(
        'post_type' => 'event',
        'posts_per_page' => 6,
        'meta_key' => 'start_date',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => 'start_date',
                'value' => $today,
                'compare' => '>='
            ),
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'eventcat',
                'field'    => 'term_id',
                'terms'    => $cat->term_id,
            ),
        ),
    );
    $event_items = new WP_Query($args);

    $cats[] = [
        'category' => $cat,
        'query' => $event_items,
    ];
}
?>

<section class="events-section group">
    <div class="events-section-content">
        <header>
            <h1>Upcoming Events</h1>

            <div class="rich-text entry">
                <?php the_content(); ?>
            </div>

            <div class="event-categories">
                <h4>Jump to</h4>
                <ul>
                    <?php foreach ($cats as $cat) : ?>
                        <?php if ($cat['query']->have_posts()): ?>
                            <li><a href="<?php echo esc_url('#'.$cat['category']->slug) ?>"><?php echo esc_html($cat['category']->name) ?></a></li>
                        <?php endif ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </header>
        <aside class="sidebar events-section-sidebar">
            <?php get_template_part('partials/service-links'); ?>
        </aside>
    </div>

    <?php foreach ($cats as $cat) : ?>
        <?php if ($cat['query']->have_posts()): ?>
            <article class="event-category">

                <header>
                    <h2 id="<?php echo esc_attr($cat['category']->slug) ?>"><a href="<?php echo esc_url('/event-category/'.$cat['category']->slug) ?>"><?php echo esc_html($cat['category']->name) ?></a></h2>

                    <a href="<?php echo esc_url('/event-category/'.$cat['category']->slug) ?>" class="button button--small">View all</a>
                </header>



                <?php while ($cat['query']->have_posts()) : ?>
                    <?php $cat['query']->the_post(); ?>
                    <?php get_template_part('partials/events/event-item'); ?>
                <?php endwhile; ?>
            </article>
        <?php endif; ?>

    <?php endforeach; ?>

</section>
