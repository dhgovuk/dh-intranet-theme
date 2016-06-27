<section class="single-article">
  <?php the_post(); ?>
  <article <?php post_class('article group'); ?>>
    <header>
      <h1><?php the_title(); ?></h1>
    </header>

    <div class="meta"><?php get_template_part('partials/entry-meta'); ?></div>

    <figure class="featured-img">
      <?php echo the_post_thumbnail('large'); ?>
    </figure>

    <div class="rich-text entry">
      <?php the_content(); ?>
    </div>

  </article>
  <?php comments_template('/partials/comments.php'); ?>
</section>

<aside class="sidebar news-section-sidebar">
    <?php get_template_part('partials/sidebar'); ?>
</aside>