<?php /* Template Name: Fullwidth Page*/ ?>
<?php the_post(); ?>

<article <?php post_class('group'); ?>>

  <header>
    <h2><?php echo roots_title(); ?></h2>
  </header>

  <div class="rich-text entry">
    <?php the_content(); ?>
  </div>

</article>