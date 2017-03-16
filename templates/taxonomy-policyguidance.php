<section class="policy-kit-archive">
<?php
$term = get_queried_object()->slug;
$tax = get_queried_object()->taxonomy;
$args = array(
    'post_type' => 'policy-kit',
    'order' => 'ASC',
    'posts_per_page' => -1,
    'tax_query' => array(
              array(
                'taxonomy' => $tax,
                'field'    => 'slug',
                'terms'    => $term,
        ),
    ),
);
$cats = new WP_Query($args);
?>
    <header>
        <h1><?php echo get_queried_object()->name; ?></h1>
        <?php if (get_queried_object()->description !== '') : ?>
            <div class="rich-text intro">
                <p><?php echo get_queried_object()->description; ?></p>
            </div>
        <?php endif; ?>
    </header>

    <div class="gridrow">

    <?php while ($cats->have_posts()) : ?>
    <?php $cats->the_post(); ?>
        <?php get_template_part('partials/policy-kit-article'); ?>
    <?php endwhile; ?>

    </div>

</section>

<aside class="sidebar policy-kit-sidebar">
    <?php get_template_part('partials/sidebar-policy-kit'); ?>
    <?php dynamic_sidebar('policy-kit-sidebar'); ?>
</aside>
