<section class="news-section dh-life-section">
    <header>
        <h1><?php echo roots_title(); ?></h1>
    </header>
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

<?php get_template_part('partials/pagination'); ?>
