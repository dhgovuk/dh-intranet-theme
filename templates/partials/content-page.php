<?php the_post(); ?>
<article <?php post_class('article group'); ?>>

    <header>
        <h1><?php echo roots_title(); ?></h1>
        <?php echo the_post_thumbnail(); ?>
    </header>

    <div class="breadcrumbs">
        <?php \DHIntranet\Pages::the_breadcrumbs(); ?>
    </div>

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
