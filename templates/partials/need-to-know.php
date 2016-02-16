<div class="campaign-tabs group">

    <?php if( have_rows('campaign_tabs', 'option') ): ?>

        <ul class="campaign-tabs-navigation">
            <?php
                $linkcount = 1;
                while( have_rows('campaign_tabs', 'option') ): the_row();
                $title = get_sub_field('title');
            ?>
              <li>
                <a href="#tab-<?php echo $linkcount; ?>" aria-controls="tab-<?php echo $linkcount; ?>" aria-expanded=""><?php echo $title; ?></a>
              </li>
            <?php
                $linkcount++;
                endwhile;
            ?>
        </ul>

        <?php
            $tabcount = 1;
            while( have_rows('campaign_tabs', 'option') ): the_row();
            $title = get_sub_field('title');
            $image = get_sub_field('image');
            $content = get_sub_field('content');
            $cta = get_sub_field('call_to_action');
        ?>
        <div class="tab group" id="tab-<?php echo $tabcount; ?>" aria-hidden="">

            <div class="image">
                <img src="<?php echo $image; ?>" alt="<?php echo $title; ?>">
            </div>

            <div class="rich-text">
                <h2><a href="<?php echo $cta; ?>"><?php echo $title; ?></a></h2>
                <p><?php echo strip_tags($content, '<a>'); ?>
                <a href="<?php echo $cta; ?>" class="continues"> Continues</a></p>
            </div>

        </div>
        <?php
            $tabcount++;
            endwhile;
        ?>
    <?php endif; ?>
    </div>