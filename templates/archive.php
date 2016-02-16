<?php
/*
  Template Name: Archive
*/
?>

<header>
    <h1><?php echo roots_title(); ?></h1>
</header>

<section class="news-section">
  <?php while (have_posts()) : ?>
      <?php the_post(); ?>
      <?php get_template_part('partials/archive'); ?>
  <?php endwhile; ?>
</section>

<aside class="sidebar news-section-sidebar">
    <?php get_template_part('partials/sidebar'); ?>
</aside>

<?php get_template_part('partials/pagination'); ?>
