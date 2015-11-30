<div class="your-building widget">
  <h4>In your building <?php location_selector(); ?> </h4>
  <div id="your-building-ajax-load" data-action="<?php echo esc_attr(admin_url('admin-ajax.php')) ?>">
    <?php get_location_items(); ?>
  </div>
</div>
