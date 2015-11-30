<?php the_post() ?>
<article <?php post_class('group'); ?>>

  <header>
    <h1><?php echo roots_title(); ?></h1>
    <?php echo the_post_thumbnail(); ?>
  </header>

  <div class="rich-text entry">
    <?php
    the_content();

    // Also get any included posts as sections.
    $rows = get_field('policy_article_includes2');
        if ($rows) {
            foreach ($rows as $row) {
                // Only display published posts.
                if ($row->post_status === 'publish') {
                    $thisPostId = $row->ID;
                    echo '<hr>';
                    echo '<h2>'.esc_html(get_the_title($thisPostId)).'</h2>';

                    //check if this post is a policy step
                    if ($row->post_type === 'policy-step') {
                        $detailsContent = get_field('policy-details', $thisPostId);
                        $detailsContent = str_replace('<div>', '[div]', $detailsContent);
                        $detailsContent = str_replace('</div>', '[/div]', $detailsContent);
                        $detailsContent = apply_filters('the_content', $detailsContent);
                        echo "$detailsContent";
                        $caseStudyContent = get_field('policy-casestudy', $thisPostId);
                        if ($caseStudyContent) {
                            $caseStudyContent = str_replace('<div>', '[div]', $caseStudyContent);
                            $caseStudyContent = str_replace('</div>', '[/div]', $caseStudyContent);
                            $caseStudyContent = apply_filters('the_content', $caseStudyContent);
                            echo '<div class="button">Show case study</div>';
                            echo '<div class="show-casestudy-box">'.$caseStudyContent.'</div>';
                        }
                    } else {
                        //Regular Page - get the post content
                        $postContent = get_post_field('post_content', $thisPostId);
                        //show any in post <div> tags because there screwing up our formatting
                        $postContent = str_replace('<div>', '[div]', $postContent);
                        $postContent = str_replace('</div>', '[/div]', $postContent);
                        //apply filters so we get voting thumbs added to the content.
                        $postContent = apply_filters('the_content', $postContent);
                        echo $postContent;
                    }
                }
            }
        } else {
            //echo "no included posts";
        }
    ?>
  </div>
</article>

<aside class="sidebar group" role="complementary">
  <?php get_template_part('partials/page-details') ?>
</aside>
