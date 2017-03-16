<?php /* Template Name: Landing page */ ?>
<?php the_post(); ?>

<header>
    <h1><?php echo roots_title(); ?></h1>
</header>

<article class="rich-text entry">
    <?php the_content(); ?>
</article>

<div class="landing-pages">
<?php
    $page_id = $post->ID;
    if (get_children('post_type=any&post_parent='.$page_id.'&post_status=publish')) :

        $children = get_children(array(
            'post_type' => 'any',
            'post_parent' => $page_id,
            'post_status' => 'publish',
            'orderby' => 'menu_order',
            'order' => 'asc', ));
        $even = true;

        foreach ($children as $child) :
            $child_link = get_post($child->ID);
?>

            <section class="child-pages group">

                <?php
                $grandchildren = get_children(array(
                    'post_type' => 'any',
                    'post_parent' => $child->ID,
                    'post_status' => 'publish',
                    'orderby' => 'menu_order',
                    'order' => 'asc', ));
                ?>

                <?php if (count($grandchildren) > 0) : ?>
                    <h2 id="heading-<?php echo esc_attr($child->post_name) ?>"><?php echo $child->post_title;
                    ?></h2>
                <?php else : ?>
                    <h2><a href="<?php echo esc_attr(get_permalink($child_link));
                    ?>"><?php echo $child->post_title;
                    ?> </a></h2>
                <?php endif ?>

                <ul>
                <?php if ($grandchildren) : ?>
                        <?php foreach ($grandchildren as $grandchild) :
                            $grandchildpage_id = $grandchild->ID;
                            $grandchildPost = get_post($grandchildpage_id); ?>

                    <li><a href="<?php echo esc_attr(get_permalink($grandchildpage_id)); ?>">
                        <h3><?php echo esc_html($grandchild->post_title); ?></h3>
                    </a></li>

                        <?php endforeach; ?>
                <?php endif; ?>
                </ul>

            </section>

<?php
            endforeach;
        endif;
?>
</div>
