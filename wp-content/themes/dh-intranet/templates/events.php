<?php
/*
  Template Name: Events
*/
?>

<h1>Events</h1>

<?php while (have_posts()) : ?>
    <?php the_post(); ?>
    <?php the_title(); ?>
<?php endwhile; ?>