<?php if (get_field('key-points') || get_field('policy-get-support-text') || get_field('policy-resources-text') || get_field('policy-casestudy')) :  ?>
<?php # this needs wrapping around the whole template do this at the end of the sprint otherwise it's hard to test ?>
<?php endif; ?>

<article <?php post_class('policy-kit-article'); ?>>

    <div class="inner">

        <a href="#panel-<?php echo $post->ID ?>" class="js-more-panel-toggle more-panel-toggle">
            <div class="summary">
                <header>
                    <h2><?php the_title(); ?></h2>

                    <div class="taxonomy group">
                        <?php
                            $term_list = wp_get_post_terms($post->ID, 'policyguidance', array("fields" => "all"));
                            foreach($term_list as $term_single) : ?>
                            <span class="term <?php echo $term_single->slug; ?>"><?php echo $term_single->name; ?></span>
                        <?php endforeach ?>
                    </div>
                    <img src="<?php h()->assetPath('img/icon-close.svg') ?>" class="icon-close">
                </header>
            </div>
        </a>

        <div class="js-more-panel more-panel" id="panel-<?php echo $post->ID ?>">

            <div class="tabs">

                <?php $tabuid = $post->ID; ?>

                <ul class="tabs-navigation policy-tabs-navigation">
                    <?php if(get_field('policy-summary')) : ?>
                        <li><a href="#tab-one-<?php echo $tabuid; ?>" aria-controls="tab-one" aria-expanded="true">Summary</a></li>
                    <?php endif; ?>
                    <?php if(get_field('policy-get-support')) : ?>
                        <li><a href="#tab-two-<?php echo $tabuid; ?>" aria-controls="tab-two" aria-expanded="true">Get support</a></li>
                    <?php endif; ?>
                    <?php if(get_field('policy-casestudy')) : ?>
                        <li><a href="#tab-three-<?php echo $tabuid; ?>" aria-controls="tab-three" aria-expanded="true">Case studies</a></li>
                    <?php endif; ?>
                </ul>

                <?php if(get_field('policy-summary')) : ?>
                <div class="tab rich-text" id="tab-one-<?php echo $tabuid; ?>" aria-hidden="false">
                    <?php the_field('policy-summary') ?>
                </div>
                <?php endif; ?>

                <?php if(get_field('policy-get-support')) : ?>
                <div class="tab rich-text" id="tab-two-<?php echo $tabuid; ?>" aria-hidden="false">
                    <?php the_field('policy-get-support') ?>
                </div>
                <?php endif; ?>

                <?php if(get_field('policy-casestudy')) : ?>
                <div class="tab rich-text" id="tab-three-<?php echo $tabuid; ?>" aria-hidden="false">
                    <?php the_field('policy-casestudy') ?>
                </div>
                <?php endif; ?>

            </div>
            <div class="buttons"><a href="<?php the_permalink(); ?>" class="button">More details</a></div>
        </div>
    </div>

</article>
