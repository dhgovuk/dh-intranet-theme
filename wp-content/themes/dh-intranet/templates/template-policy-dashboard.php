<?php /* Template Name: Policy Dashboard */ ?>

<header>
    <h1><?php echo roots_title(); ?></h1>
</header>

<?php while (have_posts()) : ?>
    <?php the_post(); ?>
    <article class="rich-text entry">
      <?php the_content(); ?>
  </article>
<?php endwhile; ?>

<form class="policy-dashboard">
  <div class="form-group">
    <label for="policy-stage-select">Get started: What steps do I take during the</label>

        <?php
        //get list of policy stages for drop down
        $terms = get_terms('policystage', array('hide_empty' => false));

        // add the terms meta data (order) to it's object
        foreach ($terms as $term) {
            $acfOrder = get_field('policy-stages-order', $term).'<br>';
            $term->acfOrder = $acfOrder;
        }

        // sort those objects by there order value
        usort($terms, function ($a, $b) {
            return strcmp($a->acfOrder, $b->acfOrder);
        });

        // display them in the drop down list
        if (!empty($terms) && !is_wp_error($terms)) {
            echo "<select id='policy-stage-select'>";

            foreach ($terms as $term) {
                echo "<option value='".$term->slug."'>".$term->name.'</option>';
            }
            echo '</select>';
        }
        ?>

        <p class="dummy-label">phase of my policy project?</p>
        <button type="submit" class="button" id="showsteps">Show steps</button>
    </div>
</form>

<div id="show-recomended-link" class="show-recomended-link">
    <a href="#" onclick="return false" class="policy-step-more" id="show-recommended-steps">Show recommended steps</a>
</div>

<?php
//list all steps, hidden to begin with.
//run the query for each term in the priority taxonomy so we get them grouped in order.
$policyPriorityTerms = get_terms('policypriority', array('hide_empty' => false));

    foreach ($policyPriorityTerms as $policyPriorityTerm) {
        $policyPrioritySlug = $policyPriorityTerm->slug;
        $policyPriorityName = $policyPriorityTerm->name;

        //not actaully using these div wrappers but keeping them in for the time being.
        echo "<div id='".esc_attr($policyPrioritySlug)."-container'>";

        $args = array(
            'post_type' => 'policy-step',
            'tax_query' => array(
                array(
                    'taxonomy' => 'policypriority',
                    'field' => 'slug',
                    'terms' => $policyPrioritySlug,
                    ),
                ),
            'posts_per_page' => -1,
            'orderby' => 'menu_order',
            'order' => 'asc',
        );
        $loop = new WP_Query($args);

        while ($loop->have_posts()) {
            $loop->the_post();
            //Policy Stages - should only ever be one term returned.
            $policyStageTerms = get_the_terms($post->ID, 'policystage');
            $policyStageSlugs = '';
            if ((string)$policyStageTerms !== '') {
                foreach ($policyStageTerms as $policyStageTerm) {
                $policyStageSlugs = $policyStageSlugs.' '.$policyStageTerm->slug;
                }
            }
?>

<div class='policy-step <?php echo $policyStageSlugs.'-'.$policyPrioritySlug; ?>'>

    <div class='init-step-box StepID<?php echo $post->ID; ?>' data='StepID<?php echo $post->ID; ?>'>
        <p class='policy-priority style-<?php echo $policyPrioritySlug; ?>'><?php echo "$policyPriorityName"; ?></p>

        <h2><?php echo esc_html(get_the_title()) ?></h2>

        <a href="#" class="policy-step-more" onclick="return false;">More</a>
    </div>

    <div class="expanded-step-box StepID<?php echo $post->ID; ?>">

        <div class="rich-text entry">
            <?php echo the_content(); ?>
        </div>

        <div class="tabs">

            <ul class="policy-tabs-navigation">
                <li><a href="#details<?php echo $post->ID; ?>" class="selected" aria-controls="tab-one" aria-expanded="true">Details</a></li>

                <li><a href="#getsupport<?php echo $post->ID; ?>" aria-controls="tab-two" aria-expanded="false">Get support</a></li>
                <?php $policyCasestudy = get_field('policy-casestudy'); ?>

                <?php if ((string)$policyCasestudy !== '') : ?>
                <li><a href="#casestudy<?php echo $post->ID; ?>" aria-controls="tab-three" aria-expanded="false">Case study</a></li>
                <?php endif ?>
            </ul>

            <div class="tab" id="details<?php echo $post->ID; ?>" aria-hidden="false">
                <div class="rich-text entry">
                    <?php echo apply_filters('the_content', get_field('policy-details')); ?>
                </div>
            </div>

            <div class="tab" id="getsupport<?php echo $post->ID; ?>" aria-hidden="true">
                <div class="rich-text entry">
                    <?php echo apply_filters('the_content', get_field('policy-get-support-text')); ?>
                </div>
            </div>

            <?php if ((string)$policyCasestudy !== '') : ?>
            <div class="tab" id="casestudy<?php echo $post->ID; ?>" aria-hidden="true">
                <div class="rich-text entry">
                    <?php echo apply_filters('the_content', get_field('policy-casestudy')); ?>
                </div>
            </div>
            <?php endif ?>
        </div>

    </div>
</div>

<?php } wp_reset_postdata(); ?>
<?php } ?>
