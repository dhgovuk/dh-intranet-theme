<article <?php post_class('news-item'); ?>>

  <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
  <time class="published" datetime="<?php echo get_the_time('c'); ?>"><?php echo get_the_time('d F Y'); ?></time>

  <div class="rich-text entry">
    <?php the_excerpt(); ?>
  </div>

</article>