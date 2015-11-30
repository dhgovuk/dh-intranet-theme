<div class="front-page-search">
    <form class="search-form">
        <label for="frontpagesearch">Search...</label>
        <input type="search" value="" name="s" id="frontpagesearch">
        <button type="submit" class="button">Search</button>
    </form>

    <div class="top-tasks group">
        <h4><a href="#" id="js-top-tasks-toggle"><span>Show</span> Top Tasks</a></h4>
        <ul>
            <?php foreach (get_tasks() as $task) : ?>
            <?php
                $post = $task;
                setup_postdata($post);
            ?>
            <li>
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </li>
            <?php wp_reset_postdata(); ?>
            <?php endforeach ?>
        </ul>
    </div>
</div>

<div class="front-page-main group">

    <div class="news-stories">

        <h2 class="hide">News stories</h2>

        <article class="homepage-story-sticky entry">
            <?php
            // use WP_Query rather than query_posts
            $args = array(
                'post_type' => 'post',
                'showposts' => 3,
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
                <p><?php echo wp_trim_words(get_the_excerpt(), 20, sprintf('<a href="%s">...more</a>', get_the_permalink())) ?></p>
            </div>
            <?php endif; ?>
        </article>

        <div class="non-sticky-news">
        <?php while ($query1->have_posts()) : $query1->the_post(); ?>
            <article class="homepage-story entry group">

                <figure>
                    <div class="meta">
                        <span><?php comments_popup_link('0', '1', '%'); ?></span>
                    </div>
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a>
                </figure>

                <div class="rich-text entry">
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <p><?php echo wp_trim_words(get_the_excerpt(), 20, sprintf('<a href="%s">...more</a>', get_the_permalink())) ?></p>
                </div>

            </article>
        <?php endwhile; ?>
        </div>
<?php wp_reset_postdata(); ?>

    <div class="cta">
        <a href="/category/news/" class="button">More news</a>
    </div>

</div>

<div class="dh-life-stories">

    <?php $dh_life_cat_id = get_category_by_slug('dh-life')->cat_ID ?>

    <header>
        <h2><a href="/category/dh-life">DHLife magazine</a></h2>
    </header>

    <ul>
        <?php
            $dh_life_id = get_category_by_slug('dh-life')->term_id;
            $query1 = new WP_Query('showposts=6&cat='.$dh_life_id);
            while ($query1->have_posts()) : $query1->the_post();
        ?>
        <li>
            <article class="dh-life-story">
                <figure>
                    <div class="meta">
                        <span><?php comments_popup_link('0', '1', '%'); ?></span>
                    </div>
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('large'); ?></a>
                </figure>
                    <h3><a href="<?php the_permalink();
                    ?>"><?php echo wtg_truncate_string(the_title('', '', false), 50);
                    ?></a></h3>
            </article>
        </li>
        <?php
            endwhile;
        ?>
    </ul>

    <div class="cta">
        <a href="/category/dh-life" class="button">More DHLife</a>
    </div>

</div>

    <div class="events-stories">
        <?php dynamic_sidebar('homepage_events'); ?>
    </div>

</div>


<aside class="sidebar group" role="complementary">
    <?php get_template_part('partials/todo-list') ?>
    <?php get_template_part('partials/your-building') ?>
    <?php get_template_part('partials/twitter') ?>
</aside>

