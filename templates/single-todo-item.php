<?php the_post(); ?>

<article <?php post_class('article group'); ?>>
  <header>
    <h1><?php echo roots_title(); ?></h1>
    <?php echo the_post_thumbnail(); ?>
  </header>

  <div class="rich-text entry">
    <?php the_content(); ?>
  </div>

  <?php if (get_field('related_links')) { ?>
  <div class="related-links">
    <h5>Related Links</h5>
      <ul>
      <?php while (has_sub_field('related_links')) { ?>
        <li><a href="<?php the_sub_field('link');?>" class="button"><?php the_sub_field('link_name');?></a></li>
      <?php } ?>
      </ul>
  </div>
  <?php } ?>
</article>

<?php comments_template('/partials/comments.php'); ?>

<aside class="sidebar group" role="complementary">
<?php get_template_part('partials/todo-details') ?>
<?php get_template_part('partials/todo-list') ?>
</aside>

