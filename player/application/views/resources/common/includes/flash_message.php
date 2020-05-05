<?php
$flash_messages_result = $this->session->userdata('result');
$flash_messages = $this->session->userdata('message');
$flash_messages = preg_replace( "/\r|\n/", "", $flash_messages);
$this->session->unset_userdata('result');
$this->session->unset_userdata('message');

if(empty($flash_messages_result) && (isset($snackbar) && !empty($snackbar))){
    $flash_messages_result = 'danger';
}
?>
<div id="alert_message"></div>

<!-- Modal -->
<div class="modal fade <?=$this->utils->isEnabledFeature('cashier_custom_error_message') ? 'custom-error-notif' : '';?>" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content panel-<?=$flash_messages_result?>">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><strong><?=lang('alert-' . $flash_messages_result)?></strong></h4>
            </div>
            <div class="modal-body panel-body">
            </div>
            <div class="modal-footer">
                <!--<button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('Close')?></button>-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
$(document).ready(function(){
    var alert_message_list_container = $('<ul>');

<?php if($flash_messages):?>
    alert_message_list_container.append($('<li>').html("<?=addslashes($flash_messages)?>"));
<?php endif ?>
<?php if(isset($snackbar) && !empty($snackbar)): ?>
<?php foreach ($snackbar as $message): ?>
    alert_message_list_container.append($('<li>').html("<?=addslashes($message)?>"));
<?php endforeach ?>
<?php endif ?>
    $('#alertModal').find('.modal-body').append(alert_message_list_container);

<?php if($flash_messages || (isset($snackbar) && !empty($snackbar))):?>
    $('#alertModal').modal('show');
<?php endif ?>
});
</script>

<?php if(!empty($this->session->userdata('result')) || !empty($this->session->userdata('message'))) : ?>
    <script>
        $(function() {
            load_alert_message("<?=$this->session->userdata('result')?>","<?=$this->session->userdata('message')?>");
        });
        <?php
        $this->session->unset_userdata('result');
        $this->session->unset_userdata('message');
        ?>
    </script>
<?php endif; ?>