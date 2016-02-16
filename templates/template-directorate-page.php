<?php /* Template Name: Directorate page */ ?>
<?php the_post(); ?>

<article>

  <header>
    <h1><?php echo roots_title(); ?></h1>
  </header>

  <div class="breadcrumbs">
    <?php \DHIntranet\Pages::the_breadcrumbs(); ?>
  </div>

  <div class="rich-text entry">
    <?php the_content(); ?>
  </div>

</article>

<?php
// get featured post as set by meta box in directorate page
$featuredPost = get_field('directorate-featured-post');

if ((int)get_query_var('paged') === 0) {
  ?>
  <div class="director rich-text">
    <?php
    $directorPost = get_field('director');
    ?>

    <figure>
      <?php
      if (is_object($directorPost) && has_post_thumbnail($directorPost->ID)) {
        echo get_the_post_thumbnail($directorPost->ID, 'category-thumb');
      }
      ?>
    </figure>
    <div class="article-content">
      <h2><?php echo is_object($directorPost) ? $directorPost->post_title : '' ?></h2>
      <p><?php echo is_object($directorPost) ? $directorPost->post_excerpt : '' ?></p>
    </div>
  </div>

  <div class="directorate">
    <?php
    $keyPersonnelPosts = get_field('key-personnel');
    if (is_array($keyPersonnelPosts)) {
      foreach ($keyPersonnelPosts as $keyPersonnelPost) {
        ?>
        <ul>
          <li>
            <article class="rich-text entry group">

              <figure>
                <div class="meta">
                  <span><?php comments_popup_link('0', '1', '%');
                  ?></span>
                </div>
                <?php
                if (has_post_thumbnail()) {
                  the_post_thumbnail('category-thumb');
                }
                ?>
              </figure>

              <div class="article-content">
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <time class="published"datetime="<?php echo get_the_time('c'); ?>"><?php echo get_the_time('d F Y'); ?></time>
                <div class="entry-summary"><p><?php the_little_excerpt(200) ?></p></div>
              </div>
            </article>
          </li>
        </ul>

        <?php
        }
      }
    ?>
  </div>

  <?php
    if ($featuredPost) {
    //set up global post data.
      global $post;
      $post = $featuredPost;
      setup_postdata($post);
    ?>

    <ul>
      <li>
        <article class="rich-text entry group">

          <figure>
            <div class="meta">
              <span><?php comments_popup_link('0', '1', '%'); ?></span> </div>
            <?php
            if (has_post_thumbnail()) {
              the_post_thumbnail('category-thumb');
            }
            ?>
          </figure>

          <div class="article-content">
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <time class="published"datetime="<?php echo get_the_time('c'); ?>"><?php echo get_the_time('d F Y'); ?></time>
            <div class="entry-summary"><p><?php the_little_excerpt(200) ?></p></div> </div>
        </article>
      </li>
    </ul>

  <?php
    wp_reset_postdata();
    }
  ?>
  <h2 class="directorate-articles-title">Directorate Articles</h2>
  <?php
    }
  ?>

<?php

$wtg_posts_per_page = 10;

//get latest posts includes in the associated directorate category
$associatedCategory = get_field('directorate-category');

$current_page = get_query_var('paged') ? get_query_var('paged') : 1;

$args = array(
  'post_type' => 'post',
  'posts_per_page' => $wtg_posts_per_page,
  'orderby' => 'date',
  'order' => 'DESC',
  'cat' => $associatedCategory,
  'post__not_in' => is_object($featuredPost) ? array($featuredPost->ID) : array(),
  'paged' => get_query_var('page'),
  'offset' => ($current_page - 1) * $wtg_posts_per_page,
  );
$query = new WP_Query($args);

?>
<div class="directorate-article">

  <?php while ($query->have_posts()) : ?>
  <?php $query->the_post() ?>
  <article class="rich-text entry group">

    <figure>
      <div class="meta">
        <span><?php comments_popup_link('0', '1', '%'); ?></span>
      </div>
      <?php
      if (has_post_thumbnail()) {
        the_post_thumbnail('category-thumb');
      }
      ?>
    </figure>

    <div class="article-content">
      <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
      <time class="published"datetime="<?php echo get_the_time('c'); ?>"><?php echo get_the_time('d F Y'); ?></time>
      <div class="entry-summary"><p><?php the_little_excerpt(200) ?></p></div>
    </div>
  </article>
  <?php endwhile ?>

<?php wp_reset_postdata() ?>

</div>

<?php get_template_part('partials/pagination'); ?>
