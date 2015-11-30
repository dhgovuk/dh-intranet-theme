<?php
$query1 = new WP_Query('category_name=emergency');

while ($query1->have_posts()) {
  $showMsg = true;
  $query1->the_post();
  // build a unique cookie name for each emergency message so new or update messages are always displayed
  $postHash = hash('md5', get_the_title().$post->post_modified);
  $cookieName = 'eMsg-'.$postHash;
?>

<div class="emergency-message" style="display:none;" data-post-cookie="<?php echo $cookieName; ?>">
  <?php echo '<h3>'.esc_html(get_the_title()).'</h3>'; ?>
  <a href="#" class="button button-close" data-post-hash="<?php echo $postHash; ?>">Close</a>
</div>

<?php wp_reset_postdata(); } ?>