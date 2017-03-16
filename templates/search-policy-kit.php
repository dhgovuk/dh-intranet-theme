<section class="policy-kit-archive">

    <header class="group">
        <h1>PolicyKit search</h1>

        <div class="rich-text intro">
            <p>We found <?php echo esc_html(sprintf(_n('%d result', '%d results', $wp_query->found_posts), $wp_query->found_posts)) ?> for "<?php the_search_query(); ?>"</p>
        </div>

    </header>

    <div class="gridrow">
        <?php while (have_posts()) : ?>
            <?php the_post(); ?>
            <?php get_template_part('partials/policy-kit-article'); ?>
        <?php endwhile ?>
    </div>

    <?php get_template_part('partials/pagination'); ?>

</section>

<aside class="sidebar policy-kit-sidebar">
    <?php dynamic_sidebar('policy-steps-sidebar'); ?>
</aside>
