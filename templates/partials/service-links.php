<?php if( have_rows('service_links', 'option') ): ?>

<section class="service-links-widget group">
  <h3>Services</h3>

  <div class="service-links group">
  <ul>
    <?php while( have_rows('service_links', 'option') ): the_row();
      $icon = get_sub_field('icon');
      $text = get_sub_field('text');
      $url = get_sub_field('url');
      $blank = get_sub_field('opens_in_new_window');
    ?>
    <li>
        <a href="<?php echo $url; ?>"<?php if ($blank == "Yes"): ?> target="_blank"<?php endif; ?>>
          <div class="service-icon">
            <?php if( $icon ): ?>
              <img src="<?php h()->assetPath('img/icon-'.$icon.'.svg') ?>" alt="">
            <?php else : ?>
              <img src="<?php h()->assetPath('img/icon-default.svg') ?>" alt="">
            <?php endif; ?>
          </div>
          <?php echo $text; ?>
        </a>
    </li>
  <?php endwhile; ?>
  </ul>
  </div>

</section>

<?php endif; ?>
