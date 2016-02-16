<?php /* Template Name: News */ ?>
<?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

query_posts([
    'post_type' => 'post',
    'paged' => $paged,
]);
?>

<header>
    <h1><?php _e('News Archive', 'roots'); ?></h1>
</header>

<section class="news-section">
    <?php while (have_posts()) : ?>
        <?php the_post() ?>
        <article class="news-story entry group">

            <figure>
                <div class="meta">
                    <span><?php comments_popup_link('0', '1', '%'); ?></span>
                </div>
                <?php if (has_post_thumbnail()) {the_post_thumbnail('category-thumb'); } ?>
            </figure>

            <div class="article-content">
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <time class="published" datetime="<?php echo get_the_time('c'); ?>">
                    <?php echo get_the_time('d F Y'); ?>
                </time>
                <div class="rich-text entry-summary">
                    <p><?php echo get_the_excerpt(); ?></p>
                </div>
            </div>

        </article>
    <?php endwhile; ?>
</section>

<aside class="_sidebar news-section-sidebar">
    <?php get_template_part('partials/sidebar'); ?>
</aside>

<?php get_template_part('partials/pagination'); ?>
