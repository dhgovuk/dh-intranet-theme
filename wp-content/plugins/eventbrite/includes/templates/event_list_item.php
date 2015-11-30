<div class="row event_item">
	<div class="border-bottom"><a href="<?php echo $wp_link ?>" title="<?php echo $title ?>"><?php echo $title ?></a></div>
	<div class="col-md-6">
		<p class="border-bottom"><strong>Start time:</strong> <?php echo date('j F Y H:i', strtotime($start_date)) ?></p>
		<p class="border-bottom"><strong>End time:</strong> <?php echo date('j F Y H:i', strtotime($end_date))   ?></p>
		<?php echo $venue ? "<p class='border-bottom'><strong>Location:</strong> $venue </p>" : null ?>
		<?php echo $address ? "<p class='border-bottom'><strong>Address:</strong> $address </p>" : null ?>
	</div>
	<div class="col-md-6">
		<p><?php echo $description ?></p>
	</div>
	<a href="<?php echo $wp_link ?>" title="<?php echo $title ?>" class="btn btn-primary" style="float:right">See more</a>
	<!-- <a href="<?php echo $register_link ?>" title="Register for: <?php echo $title ?>" class="btn btn-success" >Register</a> -->
</div>
<hr/>
