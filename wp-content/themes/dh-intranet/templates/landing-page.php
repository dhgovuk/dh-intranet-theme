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
   if (get_children('post_type=any&post_parent='.$page_id.'&post_status=publish')) {
      $children = get_children(array('post_type' => 'any',
        'post_parent' => $page_id,
        'post_status' => 'publish',
        'orderby' => 'menu_order',
        'order' => 'asc', ));
      $even = true;
      foreach ($children as $child) {
        ?>

        <section class="child-pages group">
          <h3><?php echo $child->post_title; ?></h3>
          <ul>
            <?php
            $grandchildren = get_children(array('post_type' => 'any',
              'post_parent' => $child->ID,
              'post_status' => 'publish',
              'orderby' => 'menu_order',
              'order' => 'asc', ));

            if ($grandchildren) {
              foreach ($grandchildren as $grandchild) {
                $grandchildpage_id = $grandchild->ID;
                $grandchildPost = get_post($grandchildpage_id);
                echo '<li><a href="'.esc_attr(get_permalink($grandchildpage_id)).'">'.esc_html($grandchild->post_title).'</a></li>';
            }
        }
        ?>
    </ul>
</section>
    <?php
            }
        }
    ?>
</div>