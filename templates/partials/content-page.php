<div class="breadcrumbs" typeof="BreadcrumbList" vocab="http://schema.org/">
    <?php if (function_exists('bcn_display')) {
    echo '<ul>';
    bcn_display();
    echo '</ul>';
}?>
</div>

<?php the_post(); ?>
<article <?php post_class('article group'); ?>>

    <header>
        <h1><?php echo roots_title(); ?></h1>
        <?php echo the_post_thumbnail(); ?>
        <?php get_template_part('partials/entry-meta'); ?>
    </header>

    <div class="rich-text entry">
        <?php the_content(); ?>
    </div>

    <?php if (get_field('related_links')) : ?>
        <div class="related-links">
            <h5>Related Links</h5>
            <ul>
                <?php while (has_sub_field('related_links')) : ?>
                    <li><a href="<?php the_sub_field('link');?>" class="button"><?php the_sub_field('link_name'); ?></a></li>
                <?php endwhile ?>
            </ul>
        </div>
    <?php endif ?>

    <?php
    if (class_exists('\\Dxw\\ContentFeedback\\Registrar')) {
        $registrar = \Dxw\ContentFeedback\Registrar::getInstance();
        $registrar->di['Dxw\\ContentFeedback\\Feedback']->form(get_the_id());
    }
    ?>

    <?php comments_template('/partials/comments.php'); ?>



</article>

<aside class="sidebar group" role="complementary">
    <?php get_template_part('partials/service-links') ?>
    <?php get_template_part('partials/page-details') ?>
</aside>
