<div class="breadcrumbs policy-kit" typeof="BreadcrumbList" vocab="http://schema.org/">
    <?php if (function_exists('bcn_display')) {
    echo '<ul>';
    bcn_display();
    echo '</ul>';
}?>
</div>

<section class="section-single-policy-kit">
  <?php the_post(); ?>
  <article <?php post_class('article group'); ?>>
    <header>
      <h1><?php the_title(); ?></h1>
      <?php get_template_part('partials/entry-meta'); ?>
    </header>

    <figure class="featured-img">
      <?php echo the_post_thumbnail('large'); ?>
    </figure>

    <div class="rich-text entry">
      <?php the_content(); ?>
    </div>

    <?php get_template_part('partials/policy-kit-children'); ?>

  </article>
  <?php comments_template('/partials/comments.php'); ?>
</section>

<aside class="sidebar policy-kit-sidebar">
    <?php get_template_part('partials/sidebar-policy-kit'); ?>
    <?php dynamic_sidebar('policy-kit-sidebar'); ?>
</aside>
