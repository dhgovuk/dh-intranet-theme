<div class="widget">
    <h5 class="root-page">
        <?php
        $ancestors = array_reverse(get_post_ancestors($post));
        $rootParent = isset($ancestors[0]) ? $ancestors[0] : null;
        //top level parent
        echo "<a href='".esc_attr(get_permalink($rootParent))."'>".esc_attr(get_the_title($rootParent)).'</a>';
        ?>
    </h5>

    <?php if (count($ancestors) > 1) : ?>

        <h6>
            <?php echo get_the_title($ancestors[1]);
            ?>
        </h6>

    <?php endif; ?>

    <?php $currentPage = $post->ID;

    $args = array(
        'parent' => isset($ancestors[1]) ? $ancestors[1] : null,
        'post_type' => 'page',
        'sort_column' => 'menu_order',
        'post_status' => 'publish', //auto by get_pages?
    );

    $siblings = get_pages($args);

    echo '<ul>';

    foreach ($siblings as $sibling) {
        // check if there is an alternative short navigation title
        $altNavTitle = get_field('shorter_navigation_title', $sibling->ID);
        $postTitle = $altNavTitle ? $altNavTitle : $sibling->post_title;

        // is this the current page, or if this sibling contains the current page, work out the highlighting

        if (((count($ancestors) === 2) &&
        ((int)$sibling->ID === (int)$currentPage)) ||
        ((count($ancestors) === 3) && ((int)$sibling->ID === (int)$ancestors[2]))) {

            // if this is actually the current page (and not the parent of the current page)

            if ($sibling->ID == $currentPage) {
                echo "<li class='show-children highlight-current'><a href='".esc_attr(get_permalink($sibling->ID))."'>".esc_html($postTitle).'</a>';
            } else {
                echo "<li class='show-children'><a href='".esc_attr(get_permalink($sibling->ID))."'>".esc_html($postTitle).'</a>';
            }

            // only show siblings for current page.

            $args = array(
                'parent' => $sibling->ID,
                'sort_column' => 'menu_order',
            );
            $children = get_pages($args);

            if ($children) {
                echo "<ul class='children'>";
                foreach ($children as $child) {

                    // check if there is an alternative short navigation title

                    $altNavTitle = get_field('shorter_navigation_title', $child->ID);
                    $postTitle = $altNavTitle ? $altNavTitle : $child->post_title;

                    if ((int)$child->ID === (int)$currentPage) {
                        echo "<li class='highlight-current'><a href='".esc_attr(get_permalink($child->ID))."'>".esc_html($postTitle).'</a></li>';
                    } else {
                        echo "<li><a href='".esc_attr(get_permalink($child->ID))."'>".esc_html($postTitle).'</a></li>';
                    }
                }
                echo '</ul>';
            }
            echo '</li>';
        } else {
            echo "<li><a href='".esc_attr(get_permalink($sibling->ID))."'>".esc_html($postTitle).'</a></li>';
        }
    }
    ?>
</div>
