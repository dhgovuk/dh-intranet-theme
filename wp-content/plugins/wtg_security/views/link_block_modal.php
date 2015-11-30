<?php
require_once('../wtg_autoloader.php');
$data = wtg_strip_links::get_modal_data();
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel"><?php echo $data->modal_title ?></h4>
        </div>
        <div class="modal-body">
            <p><?php echo $data->modal_content ?></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo $data->modal_button ?></button>
        </div>
    </div>
</div>