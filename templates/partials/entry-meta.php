<div class="meta">
    <?php if (is_page()) : ?>
        <p class="author"><?php echo __('Contact:', 'roots'); ?><a href="mailto:<?php echo get_field('page_owner_email')?>" rel="author"> <span itemprop="name"><?php echo get_field('page_owner_email')?> </span></a></p>
    <?php elseif (get_post_type() === 'policy-kit' && get_field('page_owner') !== '') : ?>
        <p class="author"><?php echo __('Contact:', 'roots'); ?><a href="mailto:<?php echo get_field('page_owner_email')?>" rel="author"> <span itemprop="name"><?php echo get_field('page_owner')?> </span></a></p>
    <?php else : ?>
        <p class="author"><?php echo __('Contact:', 'roots'); ?><span itemprop="name"> <?php the_author(); ?> </span></p>
    <?php endif; ?>
</div>
