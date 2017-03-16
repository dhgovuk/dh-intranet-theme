<?php

// Custom post search results
// store the post type from the URL string
$post_type = $_GET['post_type'];
// check to see if there was a post type in the
// URL string and if a results template for that
// post type actually exists
if ( isset( $post_type ) && locate_template( 'search-' . $post_type . '.php' ) ) :
    // if so, load that template
    get_template_part( 'search', $post_type );

else :

?>

<div class="search-results-list">

    <header class="group">
        <h1>Search results for <?php the_search_query(); ?></h1>

        <?php didyoumean() ?>
    </header>

    <aside class="search-filters search-sidebar">
        <form action="/" method="GET">
            <input type="hidden" name="s" value="<?php the_search_query() ?>">
            <p class="filter-by">Filter by:</p>
            <div class="form-group">
                <label class="checkbox-group">
                    <input class="js-exclude-news" type="checkbox" name="exclude-news" value="yes" <?php echo (isset($_GET['exclude-news']) && $_GET['exclude-news'] === 'yes') ? 'checked' : '' ?>>
                    Exclude News stories from search
                </label>
            </div>
        </form>
    </aside>


    <div class="returned-results">

            <p class="count"><?php echo esc_html(sprintf(_n('%d result found', '%d results found', $wp_query->found_posts), $wp_query->found_posts)) ?></p>


        <?php while (have_posts()) : ?>
            <?php the_post(); ?>
            <article <?php post_class('article'); ?>>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div class="breadcrumbs">
                    <?php \DHIntranet\Pages::the_breadcrumbs(); ?>
                </div>

                    <?php
                    $postid = get_the_ID();
                    $taxonomyid = get_post_taxonomies($postid);
                    $term_list =  wp_get_post_terms($postid, $taxonomyid, array(
                        "fields" => "all"
                    ));
                    $tags = [];
                    ?>
                    <?php
                        foreach($term_list as $term) {
                            if (strpos($term->name, '@dh.gsi.gov.uk') === FALSE) {
                                $tags[] = '<a href="/category/' .$term->slug. '">' .$term->name. '</a>';
                            };
                        };
                    ?>
                    <?php if (!empty($tags) && get_post_type() == 'post') : ?>
                    <div class="terms">
                        <p>News:
                            <?php echo implode(', ', $tags); ?>
                        </p>
                    </div>
                    <?php endif; ?>

                <div class="rich-text entry-summary">
                    <?php the_excerpt(); ?>
                </div>
            </article>
        <?php endwhile ?>

    </div>

    <?php get_template_part('partials/pagination'); ?>

</div>
<?php endif; ?>
