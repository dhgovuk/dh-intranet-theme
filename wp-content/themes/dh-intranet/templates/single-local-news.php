<?php the_post(); ?>

<article <?php post_class('article group'); ?>>
  <header>
    <h1><?php echo roots_title(); ?></h1>
    <?php echo the_post_thumbnail(); ?>
  </header>
  <div class="rich-text entry">
    <?php the_content(); ?>
  </div>
</article>

<?php comments_template('/partials/comments.php'); ?>

<aside class="sidebar group" role="complementaryy">
  <?php get_template_part('partials/your-building') ?>
</aside>