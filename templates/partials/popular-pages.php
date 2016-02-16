<?php if( have_rows('popular_pages', 'option') ): ?>

<h3>Popular pages</h3>

  <section class="popular-pages group">

    <ul>
      <?php while( have_rows('popular_pages', 'option') ): the_row();
        $text = get_sub_field('text');
        $url = get_sub_field('url');
      ?>
      <li>
          <a href="<?php echo $url; ?>">
            <?php echo $text; ?>
          </a>
      </li>
    <?php endwhile; ?>
    </ul>

  </section>

<?php endif; ?>