<?php
/*
  Template Name: Archive
*/
?>

<header>
    <h1><?php echo roots_title(); ?></h1>
</header>

<?php while (have_posts()) : ?>
    <?php the_post(); ?>
    <?php get_template_part('partials/archive'); ?>
<?php endwhile; ?>