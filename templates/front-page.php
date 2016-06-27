<div class="front-page-main group">

    <?php get_template_part('partials/campaign-tabs') ?>

    <div class="news-stories">

        <header>
            <h2><a href="/news/">News</a></h2>
        </header>

        <article class="sticky-news entry group">
        <?php
        // use WP_Query rather than query_posts
        $args = array(
            'post_type' => 'post',
            'showposts' => 6,
            'category_name' => 'news',
            'post_status' => 'publish',
            );
        $query1 = new WP_Query($args);
        ?>
        <?php if ($query1->have_posts()) : $query1->the_post() ?>
            <figure>
                <div class="meta">
                    <span><?php comments_popup_link('0', '1', '%'); ?></span>
                </div>
                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a>
            </figure>
            <div class="rich-text entry">
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <?php the_excerpt(); ?>
            </div>
            <?php endif; ?>
        </article>

        <?php while ($query1->have_posts()) : $query1->the_post(); ?>
            <article class="entry group">

                <figure>
                    <div class="meta">
                        <span><?php comments_popup_link('0', '1', '%'); ?></span>
                    </div>
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a>
                </figure>

                <div class="rich-text entry">
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <?php the_excerpt(); ?>
                </div>

            </article>
        <?php endwhile; ?>

    <div class="cta">
        <a href="/news/" class="button">More news</a>
    </div>

        <?php wp_reset_postdata(); ?>


    </div>

    <div class="other-news-stories">

        <?php $staff_blog_cat_id = get_category_by_slug('blogs')->cat_ID ?>

        <header>
            <h2><a href="/category/blogs">Blogs</a></h2>
        </header>

            <?php
                $staff_blog_id = get_category_by_slug('blogs')->term_id;
                $query1 = new WP_Query('showposts=3&cat='.$staff_blog_id);
                while ($query1->have_posts()) : $query1->the_post();
            ?>
            <article class="entry group">

                <figure>
                    <div class="meta">
                        <span><?php comments_popup_link('0', '1', '%'); ?></span>
                    </div>
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a>
                </figure>

                <div class="rich-text entry">
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <?php the_excerpt(); ?>
                </div>

            </article>
            <?php
                endwhile;
            ?>

        <div class="cta sep">
            <a href="/category/blogs" class="button">More blogs</a>
        </div>

        <?php $dh_life_cat_id = get_category_by_slug('dh-life')->cat_ID ?>

        <header>
            <h2><a href="/category/dh-life">DHLife magazine</a></h2>
        </header>

            <?php
                $dh_life_id = get_category_by_slug('dh-life')->term_id;
                $query1 = new WP_Query('showposts=3&cat='.$dh_life_id);
                while ($query1->have_posts()) : $query1->the_post();
            ?>
            <article class="entry group">

                <figure>
                    <div class="meta">
                        <span><?php comments_popup_link('0', '1', '%'); ?></span>
                    </div>
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a>
                </figure>

                <div class="rich-text entry">
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <?php the_excerpt(); ?>
                </div>

            </article>
            <?php
                endwhile;
            ?>

        <div class="cta">
            <a href="/category/dh-life" class="button">More DHLife</a>
        </div>

    </div>

</div>

<aside class="sidebar group" role="complementary">
    <?php get_template_part('partials/service-links') ?>
    <?php get_template_part('partials/popular-pages') ?>
    <?php //get_template_part('partials/todo-list') ?>
    <?php //get_template_part('partials/your-building') ?>
    <?php //get_template_part('partials/twitter') ?>
    <?php get_template_part('partials/events/events-sidebar') ?>
</aside>
