<footer class="footer group">

  <h4>Top tasks</h4>

  <ul>
    <?php foreach (get_tasks() as $task) : ?>
        <?php
        $post = $task;
        setup_postdata($post);
        ?>
        <li>
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </li>
        <?php wp_reset_postdata(); ?>
    <?php endforeach ?>
  </ul>

  <div class="footer-message">
    <?php if (is_home()) : ?>
      <p>We're always looking to improve the intranet - <a href="mailto:intranet@dh.gsi.gov.uk">email</a> the DH intranet team if you have any great ideas.</p>
    <?php endif ?>
  </div>

</footer>

<?php wp_footer(); ?>
