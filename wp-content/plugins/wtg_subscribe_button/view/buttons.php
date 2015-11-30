<link rel="stylesheet" href="<?php echo plugins_url();?>/wtg_subscribe_button/css/style.css" />
<span class="hidden-print">Subscribe <ul class="subscriptionButtons">
	<?php
	foreach ($cats as $c) {
		?>
			<li>
				<?php 

					$nl = unserialize($newsletters['meta_value']);

					if (!is_array($nl)) {
						$nl = array();
					}

					if (!in_array($c->cat_ID, $nl)) { 

				?>
				<a href="<?php echo post_permalink($post->id) ?>?cSbs=<?php echo $c->cat_ID; ?>&s=1">
					Subscribe to... <?php echo $c->cat_name; ?>
				</a>
				<?php } else { ?>
				<a href="<?php echo post_permalink($post->id) ?>?cSbs=<?php echo $c->cat_ID; ?>&s=0">
					Unsubscribe from... <?php echo $c->cat_name; ?>
				</a>
				<?php } ?>
			</li>
		<?php 
	}
	?>
</ul></span>