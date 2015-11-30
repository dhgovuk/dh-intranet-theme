<article <?php post_class('news-sticky group'); ?>>
  <figure>
    <?php echo the_post_thumbnail('fullsize'); ?>
  </figure>

  <div class="article-content">

    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
    <time class="published" datetime="<?php echo get_the_time('c'); ?>"><?php echo get_the_time('d F Y'); ?></time>

    <div class="rich-text entry">
      <?php the_excerpt(); ?>
    </div>

  </div>
</article>