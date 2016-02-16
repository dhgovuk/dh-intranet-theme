<?php if( have_rows('service_links', 'option') ): ?>

<h3>Services</h3>

<section class="service-links group">

  <ul>
    <?php while( have_rows('service_links', 'option') ): the_row();
      $icon = get_sub_field('icon');
      $text = get_sub_field('text');
      $url = get_sub_field('url');
    ?>
    <li>
        <a href="<?php echo $url; ?>">

          <div class="service-icon">
            <?php if( $icon ): ?>
              <img src="<?php echo get_template_directory_uri(); ?>/../assets/img/icon-<?php echo $icon; ?>.svg" alt="">
            <?php else : ?>
              <img src="<?php echo get_template_directory_uri(); ?>/../assets/img/icon-default.svg" alt="">
            <?php endif; ?>
          </div>
          <?php echo $text; ?>
        </a>
    </li>
  <?php endwhile; ?>
  </ul>

</section>

<?php endif; ?>