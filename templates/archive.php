<?php
/*
  Template Name: Archive
*/
?>

<section class="news-section">
    <?php while (have_posts()) : ?>
    <?php the_post(); ?>
        <?php get_template_part('partials/archive'); ?>
    <?php endwhile; ?>
    <?php get_template_part('partials/pagination'); ?>
</section>

<aside class="sidebar news-section-sidebar">
    <?php get_template_part('partials/sidebar'); ?>
</aside>