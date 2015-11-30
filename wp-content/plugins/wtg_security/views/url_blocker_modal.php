<?php
require_once('../wtg_autoloader.php');
$data = wtg_strip_links::get_lang_data();
$type = isset($_GET['type']) ? $_GET['type'] : 'url';
?>
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title" id="myModalLabel"><?php echo $data[$type]->modal_title ?></h4>
		</div>
		<div class="modal-body">
			<p><?php echo $data[$type]->modal_content ?></p>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" data-dismiss="modal">
				<?php echo $data[$type]->modal_button ?>
			</button>
		</div>
	</div>
</div>
<script type="text/javascript">
	jQuery('#link_modal').on('hidden.bs.modal', function() {
		jQuery(this).removeData('bs.modal');
	});
</script>