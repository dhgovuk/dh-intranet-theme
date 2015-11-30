<article <?php post_class('article'); ?>>

    <header>
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <?php get_template_part('partials/entry-meta'); ?>
    </header>

    <div class="rich-text entry">
        <?php the_excerpt(); ?>
    </div>

</article>