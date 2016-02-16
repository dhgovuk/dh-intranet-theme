<div class="search-results-list">

    <header class="group">
        <h1>Search results for <?php the_search_query(); ?></h1>

        <?php didyoumean() ?>

    <div class="search-filters">

        <div class="count">
            <p><?php echo esc_html(sprintf(_n('%d result', '%d results', $wp_query->found_posts), $wp_query->found_posts)) ?></p>
        </div>

        <div class="search-filters">
            <form action="/" method="GET">
                <input type="hidden" name="s" value="<?php the_search_query() ?>">
                <div class="form-group">
                    <label class="checkbox-group">
                        <input class="js-exclude-news" type="checkbox" name="exclude-news" value="yes" <?php echo (isset($_GET['exclude-news']) && $_GET['exclude-news'] === 'yes') ? 'checked' : '' ?>>
                        Exclude News stories from search
                    </label>
                </form>
            </div>
        </div>

    </header>

    <ul>
        <?php while (have_posts()) : ?>
            <?php the_post(); ?>
            <li>
                <article <?php post_class('article'); ?>>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="breadcrumbs">
                        <?php \DHIntranet\Pages::the_breadcrumbs(); ?>
                    </div>
                    <div class="rich-text entry-summary">
                        <?php the_excerpt(); ?>
                    </div>
                </article>
            </li>
        <?php endwhile ?>
    </ul>

    <?php get_template_part('partials/pagination'); ?>

</div>
