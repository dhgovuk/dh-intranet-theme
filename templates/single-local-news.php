<?php the_post(); ?>
<section class="single-article">
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
</section>

<aside class="sidebar group" role="complementary">
  <?php get_template_part('partials/your-building') ?>
</aside>
