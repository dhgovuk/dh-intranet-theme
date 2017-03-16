<?php /* Template Name: Policy Kit Index */ ?>
<?php the_post(); ?>

<header>
<h1><?php echo roots_title(); ?></h1>
</header>

<article class="rich-text entry">
    <?php the_content(); ?>
</article>

<div class="policy-kit">
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

    $grandchildren = get_children(array(
        'post_type' => 'any',
        'post_parent' => $child->ID,
        'post_status' => 'publish',
        'orderby' => 'menu_order',
        'order' => 'asc', ));
?>
    <article class="policy-document">
        <h3><a href="<?php esc_attr(the_permalink($child_link)); ?>"><?php echo $child->post_title; ?> </a>
            <span class="url">[<?php esc_attr(the_permalink($child_link)); ?>]</span>
        </h3>

        <ul class="child-documents">
        <?php if ($grandchildren) : foreach ($grandchildren as $grandchild) :
            $grandchildpage_id = $grandchild->ID;
            $grandchildPost = get_post($grandchildpage_id);
        ?>
            <li>
                <a href="<?php esc_attr(the_permalink($grandchildpage_id)); ?>"> &mdash; <?php echo esc_html($grandchild->post_title); ?></a><span class="url">[<?php esc_attr(the_permalink($grandchildpage_id)); ?>]</span>
                <?php
                $greatgrandchildren = get_children(array(
                    'post_type' => 'any',
                    'post_parent' => $grandchild->ID,
                    'post_status' => 'publish',
                    'orderby' => 'menu_order',
                    'order' => 'asc', ));
                ?>

                <?php if ($greatgrandchildren) : ?>
                    <ul>
                    <?php foreach ($greatgrandchildren as $greatgrandchild) :
                        $greatgrandchildpage_id = $greatgrandchild->ID;
                        $greatgrandchildPost = get_post($greatgrandchildpage_id);
                    ?>
                        <li>
                            <a href="<?php esc_attr(the_permalink($greatgrandchildpage_id)); ?>"> &mdash; &mdash; <?php echo esc_html($greatgrandchild->post_title); ?></a><span class="url">[<?php esc_attr(the_permalink($greatgrandchildpage_id)); ?>]</span>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

            </li>
        <?php endforeach; endif; ?>
        </ul>
    </article>

<?php endforeach; endif; ?>
</div>
