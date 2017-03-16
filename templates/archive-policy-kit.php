<section class="policy-kit-archive">

    <header>
        <h1>PolicyKit</h1>

        <div class="rich-text intro">
            <?php the_field('policy-kit-content', 'option') ?>
        </div>
    </header>

    <div class="policy-kit-search-form">
      <form method="get" action="<?php echo home_url('/'); ?>">
        <input type="hidden" name="post_type" value="policy-kit">
        <label for="search-input"><?php _e('Search PolicyKit', 'roots'); ?></label>
        <input id="search-input" type="search" value="<?php if (is_search()) {echo get_search_query(); } ?>" name="s">
            <button type="submit" class="button"><img src="<?php h()->assetPath('img/icon-search.svg') ?>"><span><?php _e('Search', 'roots'); ?></span></button>
      </form>
    </div>

    <div class="gridrow">

        <?php
            $args = array(
                'post_type' => 'policy-kit',
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'post_parent' => 0,
                'nopaging' => true,
             );
             $policykit = new WP_Query( $args );
             while ($policykit->have_posts()) : $policykit->the_post();
        ?>

            <?php get_template_part('partials/policy-kit-article'); ?>

        <?php endwhile; ?>

    </div>

</section>

<aside class="sidebar policy-kit-sidebar">
    <?php get_template_part('partials/sidebar-policy-kit'); ?>
    <?php dynamic_sidebar('policy-kit-sidebar'); ?>
</aside>
