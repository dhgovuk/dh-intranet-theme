<?php
$parent = $post->ID;
$args = array(
    'post_parent' => $parent,
    'post_type'   => 'policy-kit',
    'post_status' => 'publish',
    'posts_per_page' => -1,
);
$children = new WP_Query( $args );
?>

<?php if ($children->have_posts() ) : ?>
    <div class="children">
        <header>
            <h4>Related pages</h4>
        </header>
        <ul>
            <?php while ($children->have_posts() ) : $children->the_post(); ?>
            <li>
                <a href="<?php the_permalink(); ?>">
                    <h5><?php the_title($child_id); ?></h5>
                    <span class="view-details">View details</span>
                </a>
            </li>
            <?php endwhile; ?>
        </ul>
    </div>
<?php endif; ?>
