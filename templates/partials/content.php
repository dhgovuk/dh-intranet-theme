<div class="breadcrumbs" typeof="BreadcrumbList" vocab="http://schema.org/">
    <?php if (function_exists('bcn_display')) {
    echo '<ul>';
    bcn_display();
    echo '</ul>';
}?>
</div>

<article <?php post_class('article'); ?>>

    <header>
        <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
        <?php get_template_part('partials/entry-meta'); ?>
    </header>

    <div class="rich-text entry">
        <?php the_excerpt(); ?>
    </div>

</article>
