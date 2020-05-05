<?php
$this->CI->load->library(['player_message_library']);

$default_admin_sender_name = $this->CI->player_message_library->getDefaultAdminSenderName();
?>
<link rel="stylesheet" type="text/css" href="<?=$this->CI->utils->cssUrl('select2.min.css')?>" />
<script type="text/javascript" src="<?=$this->CI->utils->jsUrl('select2.full.min.js')?>"></script>

<!-- Include the default theme -->
<link rel="stylesheet" href="<?=$this->CI->utils->thirdpartyUrl('sceditor/2.1.3/minified/themes/default.min.css')?>" />

<!-- Include the editors JS -->
<script src="<?=$this->CI->utils->thirdpartyUrl('sceditor/2.1.3/minified/sceditor.min.js')?>"></script>
<script src="<?=$this->CI->utils->thirdpartyUrl('sceditor/2.1.3/development/plugins/plaintext.js')?>"></script>

<!-- Include the BBCode or XHTML formats -->
<script src="<?=$this->CI->utils->thirdpartyUrl('sceditor/2.1.3/minified/formats/bbcode.js')?>"></script>
<script src="<?=$this->CI->utils->thirdpartyUrl('sceditor/2.1.3/minified/formats/xhtml.js')?>"></script>

<div id="messages_send_messages" class="modal" tabindex="-1" role="dialog">
    <form enctype="multipart/form-data" action="<?=site_url('cs_management/sendBatchMessagePost')?>" method="post" autocomplete="off">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title"><?=lang('lang.send.message')?></h5>
                </div>
                <div class="modal-body">
                    <div class="form-group" >
                        <label class="control-label" for="messages_send_message_username"><?=lang('lang.select.players')?></label>

                        <div class="input-group">
                            <select class="form-control" id="messages_send_message_username" name="messages_send_message_username[]" multiple="true" required></select>
                            <div class="input-group-btn">
                                <button type="button" id="messages_send_message_clear_player_select" title="" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-default'?>" ><fa class="glyphicon glyphicon-remove"></fa><?=lang('lang.clear.selections')?></button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <input type="file" class="form-control" name="messages_send_message_batch_players" id="messages_send_message_batch_players" accept=".csv" required/>
                            <div class="input-group-btn">
                                <button type="button" id="messages_send_message_clear_file_select" title="" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-default'?>" ><fa class="glyphicon glyphicon-remove"></fa><?=lang('lang.clear.selections')?></button>
                            </div>
                        </div>
                        <div class="help-block">
                            <strong><?=lang("csv_required")?></strong>&nbsp;&nbsp;
                            (<a href="<?= '/resources/sample_csv/sample_player_send_message.csv' ?>"><span><?= lang('Download Sample File') ?></span></a>)
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="send-message-sender"><?=lang("cs.sender")?> </label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="send-message-sender" name="message-sender" required >
                            <div class="input-group-btn">
                                <button type="button" class="btn use_default_admin_sender <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>" data-value="<?=$default_admin_sender_name?>"><?=lang('cs.use_default_admin_sender')?></button>
                                <button type="button" class="btn custom_admin_sender <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-default'?>"><?=lang('cs.custom_admin_sender')?></button>
                                <button type="button" class="btn save_to_default_admin_sender <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?=lang('Saving')?>"><?=lang('cs.save_to_default_admin_sender')?></button>
                            </div>
                        </div>
                        <span class="help-block"></span>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="disabled_reply" value="1">
                            <?=lang('cs.disabled_reply')?>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="message-subject"><?=lang("cs.subject")?> </label>
                        <input type="text" class="form-control" id="message-subject" name="message-subject" required >
                        <span class="help-block"></span>
                    </div>

                    <div class="form-group">
                        <label for="message-body" class="control-label" ><?=lang("cu.7")?></label>
                        <textarea class="form-control" id="message-body" rows="3" name="message-body" required></textarea>
                        <span class="help-block"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" data-dismiss="modal"><?=lang('lang.close');?></button>
                    <button type="button" id="messages_send_message_reset_form" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>"><?=lang('lang.reset');?></button>
                    <button type="submit" id="messages_send_message_submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>"><i class="fa fa-comment"></i> <?=lang('Batch Send Internal Message')?></button>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
var sceditor_message_default_options = {
    "style": '<?=$this->CI->utils->thirdpartyUrl('sceditor/2.1.3/minified/themes/content/default.min.css')?>',
    // "format": "bbcode",
    "format": "xhtml",
    "width": "100%",
    "height": "100%",
    "toolbar": 'bold,italic,underline,strike,subscript,superscript|' +
    'left,center,right,justify|size,color,removeformat|' +
    'bulletlist,orderedlist,indent,outdent|' +
    'table|quote|horizontalrule,link,unlink|' +
    'date,time|maximize',
    "resizeEnabled": false,
    "emoticonsEnabled": false,
    "bbcodeTrim": true,
    'enablePasteFiltering': true
};

<?php if ($this->utils->getConfig('internal_message_edit_allow_only_plain_text_when_pasting')) : ?>
    sceditor_message_default_options["plugins"] = "plaintext";
<?php endif; ?>

$(function(){
    var modal = $('#messages_send_messages');
    var form = $("#messages_send_messages form");
    var playerInput  = $("input[name='messages_send_message_batch_players']", modal);
    var playerSelect = $("#messages_send_message_username", modal);
    var sceditor_instance = null;

    $('.modal').on('show.bs.modal', function () {
        $("#messages_send_message_reset_form").trigger('click');

        var sc_container = $('#message-body')[0];

        sceditor.create(sc_container, sceditor_message_default_options);
        sceditor_instance = sceditor.instance(sc_container);

    });

    playerInput.change(function(){
        var v = $(this).val();
        if(v != ''){
            playerSelect.removeAttr("required");

            var res = v.split('.').pop();

            if(res != 'csv'){
                playerInput.val('');
                alert('<?=lang('notify.invalid.file')?>');

                return false;
            }
        }else{
            playerSelect.removeAttr("required");
        }
    });

    playerSelect.change(function(){
        if(!!parseInt($(this).val())){
            playerInput.removeAttr("required");
        }else{
            playerInput.attr("required");
        }
    });

    playerSelect.select2({
        ajax: {
            url: '<?php echo site_url('player_management/getPlayerUsernames') ?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                // Query paramters will be ?search=[term]&page=[page]
                return {
                    q: params.term,
                    page: params.page
                };
            },
            //placeholder: "Username",
            allowClear: true,
            tags: true,
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup; },
        minimumInputLength: 1,
        templateResult: function(opt) {
            if (opt.loading) {
                return opt.text;
            } else{
                return opt.username;
            }
        },
        templateSelection: function (opt) {
            return opt.username || opt.text;
        }
    });

    $("#messages_send_message_clear_player_select", modal).click(function() {
        playerSelect.empty();
        playerSelect.val('').trigger("change");
    });

    $("#messages_send_message_clear_file_select", modal).click(function() {
        playerInput.val('').trigger("change");
    });

    $("#messages_send_message_reset_form", modal).click(function(){
        playerInput.val("");
        $("#messages_send_message_clear_player_select").trigger('click');
        $("#messages_send_message_clear_file_select").trigger('click');

        $('.use_default_admin_sender', modal).trigger('click');
        $('#message-subject').val("");
        $('#message-subject').next('span').html('');
        $('#message-body').val("");
        $('#message-body').next('span').html('');

        try {
            sceditor.instance($('#message-body')[0]).destroy();
        } catch(e){

        }
    });

    $('.use_default_admin_sender').on('click', function(){
        $(this).removeClass('active').addClass('active');

        $(this).parent().parent().find('[name="message-sender"]').attr('readonly', 'readonly');
        $(this).parent().parent().find('[name="message-sender"]').val($(this).data('value'));

        $(this).parent().find('.custom_admin_sender').removeClass('active');
    });

    $('.custom_admin_sender').on('click', function(){
        $(this).removeClass('active').addClass('active');

        $(this).parent().parent().find('[name="message-sender"]').removeAttr('readonly');

        $(this).parent().find('.use_default_admin_sender').removeClass('active');
    });

    $('.save_to_default_admin_sender').on('click', function(){
        if($(this).parent().find('.use_default_admin_sender').hasClass('active')){
            return false;
        }

        var $this = $(this);
        $this.button('loading');

        $.ajax({
            "contentType": "application/x-www-form-urlencoded; charset=UTF-8",
            "url": "<?=site_url('cs_management/saveMessageDefaultAdminSenderName')?>",
            "type": "POST",
            "data": {
                "default_message_admin_sender_name": $this.parent().parent().find('[name="message-sender"]').val()
            },
            "success": function(data){
                $this.button('reset');

                $('.use_default_admin_sender').data('value', $this.parent().parent().find('[name="message-sender"]').val());
            },
            "error": function(){
            }
        });
    });

    form.submit(function(e) {
        var self = this;

        var message_html = $(sceditor_instance.getBody()).html();
        var message_text = $(sceditor_instance.getBody()).text();

        if(!message_text.length){
            sceditor_instance.focus();
            return false;
        }

        $('#message-body').val(message_html);

        e.preventDefault();
        var formData = new FormData(this);

        console.log(formData);

        BootstrapDialog.show({
            closable: false,
            title: $.trim($('.modal-title', modal).html()),
            onshow: function(dialog) {
                modal.modal('hide');
                var close_btn = dialog.getButton('close');
                close_btn.spin();

                $.ajax({
                    cache: false,
                    contentType: false,
                    processData: false,
                    url: $(self).attr('action'),
                    type: 'POST',
                    data: formData,
                    success: function (result) {
                        dialog.close();

                        var event = $.Event('done.t1t-sbe.messages.send');
                        form.trigger(event, result);

                        if(event.isDefaultPrevented()) return;

                        if(result['status'] === 'success'){
                            BootstrapDialog.show({
                                "type": BootstrapDialog.TYPE_SUCCESS,
                                "message": result['message']
                            });
                        }else{
                            BootstrapDialog.show({
                                "type": BootstrapDialog.TYPE_DANGER,
                                "message": result['message']
                            });
                        }
                    }
                });
            },
            buttons: [{
                id: 'close',
                label: 'Close',
                autospin: true,
                action: function(dialogRef){
                    dialogRef.close();
                }
            }]
        });
    });

    window.sbe_messages_send_message = function(){
        var callback = null;

        modal.modal('show');

        if(typeof arguments[0] === "object" && !!arguments[0]){
            $.each(arguments[0], function(player_id, player_name){
                playerSelect.append($('<option>').attr('value', player_id).text(player_name).prop('selected', true)).trigger('change');
            });
        }else{
            if(!!arguments[0]){
                playerSelect.append($('<option>').attr('value', arguments[0]).text(arguments[1]).prop('selected', true)).trigger('change');
            }
            callback = arguments[2];
        }

        if(typeof callback === 'function'){
            form.off('done.t1t-sbe.messages.send').on('done.t1t-sbe.messages.send', function(){
                callback.call(form, Array.prototype.slice.call(arguments));
            })
        }
    };

    if(typeof sbe_player_helper_menu_manager === "object"){
        sbe_player_helper_menu_manager.addItem("send_message", "<?=lang('lang.send.message')?>", function(player_info){
            sbe_messages_send_message(player_info.player_id, player_info.player_name);
        });
    }
});
</script>