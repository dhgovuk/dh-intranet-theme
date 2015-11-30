<?php
require_once('../wtg_autoloader.php');
$data = wtg_strip_links::get_lang_data();
$type = $_GET['type'];
?>
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h1 class="modal-title" id="myModalLabel"><?php echo $data['mailto']->modal_title ?></h1>
		</div>
		<div class="modal-body">
			<p><?php echo $data['mailto']->modal_content ?></p>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-primary" data-dismiss="modal">
				<?php echo $data['mailto']->modal_cancel ?>
			</button>
			<a type="button" class="btn btn-danger" href="<?php echo $_GET['mailto'] ?>">
				<?php echo $data['mailto']->modal_button ?>
			</a>
		</div>
	</div>
</div>