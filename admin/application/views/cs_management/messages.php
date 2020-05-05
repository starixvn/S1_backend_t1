<?php include VIEWPATH . '/includes/messages.php'; ?>
<link rel="stylesheet" type="text/css" href="<?=$this->utils->thirdpartyUrl('bootstrap-switch/3.3.4/css/bootstrap3/bootstrap-switch.min.css')?>" />
<script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-switch/3.3.4/js/bootstrap-switch.min.js');?>"></script>

<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-search"></i> <?=lang("lang.search")?>
            <span class="pull-right">
                <a data-toggle="collapse" href="#collapseViewMessages" class="btn btn-xs <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info' : 'btn-default'?>"></a>
            </span>
        </h4>
    </div>
    <div id="collapseViewMessages" class="panel-collapse collapse in">
        <div class="panel-body">
            <form class="form-horizontal" id="search-form">
                <div class = "row">
                    <div class="col-md-4">
                        <label class="control-label" for="search_date"><?=lang('report.sum02');?></label>
                        <input id="search_date" class="form-control input-sm dateInput" data-start="#date_from" data-end="#date_to" data-time="true"/>
                        <input type="hidden" id="date_from" name="date_from" value="<?php if (isset($date_from)){ echo $date_from;} ?>" />
                        <input type="hidden" id="date_to" name="date_to"  value="<?php if (isset($date_to)){ echo $date_to;} ?>"/>
                    </div>
                    <div class="col-md-2">
                        <label class="control-label" for="status"><?=lang('cashier.28');?> </label>
                        <select name="status" id="status" class="form-control input-sm">
                            <option value=""><?=lang('All');?></option>
                            <option value="player_new"><?=lang('cs.player.new');?></option>
                            <option value="admin_new"><?=lang('cs.admin.new');?></option>
                            <option value="unprocessed" selected><?=lang('cs.unprocessed');?></option>
                            <option value="processed"><?=lang('cs.processed');?></option>
                            <option value="admin_read"><?=lang('cs.admin.read');?></option>
                            <option value="markclose"><?=lang('cs.markclose');?></option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="playerUsername"><?=lang('Player Name');?> </label>
                        <input type="text" name="playerUsername" id="playerUsername" class="form-control input-sm" value="<?php if (isset($playerUsername)){ echo $playerUsername;} ?>" />
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="adminUserId"><?=lang('Operator');?> </label>
                        <select type="text" name="adminUserId" id="adminUserId" class="form-control input-sm">
                            <option value=""><?=lang('All');?></option>
                            <?php foreach($this->users->getAllAdminUsers() as $entry): ?>
                                <option value="<?=$entry['userId']?>" <?=(($adminUserId == $entry['userId']) ? ' selected="selected"' : '')?>><?=$entry['username']?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label" for="subject"><?=lang('cs.subject');?> </label>
                        <input type="text" name="subject" id="subject" class="form-control input-sm" value="<?php if (isset($subject)){ echo $subject;} ?>" />
                    </div>
                    <div class="col-md-6">
                        <label class="control-label" for="messages"><?=lang('cs.messages');?> </label>
                        <input type="text" name="messages" id="messages" class="form-control input-sm" value="<?php if (isset($messages)){ echo $messages;} ?>" />
                    </div>
                </div>

            </form>
        </div>
        <div class="panel-body text-right">
            <input type="button" id="btnResetFields" value="<?php echo lang('Reset'); ?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-danger'?>">
            <button type="button" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" id="search-message"><?=lang('lang.search');?></button>
        </div>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <div class="pull-right">
            <?php if ($this->CI->permissions->checkPermissions('send_message_sms')) : ?>
            <button type="button" class="btn btn-info btn-xs btn-send-message"><?=lang('lang.send.message')?></button>
            <?php endif; ?>
            <?php if ($this->CI->permissions->checkPermissions('cs_message_settings')) : ?>
            <button type="button" class="btn btn-info btn-xs btn-message-setting"><i class="icon-settings"></i></button>
            <?php endif; ?>
        </div>
        <h4 class="panel-title custom-pt">
            <i class="icon-bubble2"></i> <?=lang('cs.messages');?>
        </h4>
    </div>
    <form action="<?=site_url('cs_management/deleteSelectedMessage')?>" method="post" role="form">
        <div class="panel-body" id="chat_panel_body">
            <div class="">
                <?php if($this->CI->permissions->checkPermissions('delete_messages')) {?>
                    <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="<?=lang('cms.deletesel');?>" onclick="return confirm('<?=lang('sys.sure');?>')">
                        <i class="glyphicon glyphicon-trash" style="color:white;"></i>
                        <?php echo lang('cms.deletesel'); ?>
                    </button>
                <?php } ?>
                <!--Hidden Values for Search-->
                <input type="hidden" id="date_from_delete" name="date_from_delete" value="" />
                <input type="hidden" id="date_to_delete" name="date_to_delete"  value=""/>
                <input type="hidden" id="sender_delete" name="sender_delete"  value=""/>
                <input type="hidden" id="subject_delete" name="subject_delete"  value=""/>
                <input type="hidden" id="messages_delete" name="messages_delete"  value=""/>
            </div>

            <div class="table-responsive">
                <table class="table table-hover" style="width:100%;" id="myTable">
                    <thead>
                    <tr>
                        <th></th>
                        <th style="padding: 8px" class="th_chk_multiple"><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
                        <th><?=lang('cs.sender');?></th>
                        <th><?=lang('con.cs06');?></th>
                        <th><?=lang('Operator');?></th>
                        <th><?=lang('cs.subject');?></th>
                        <th><?=lang('lang.date');?></th>
                        <th><?=lang('New');?></th>
                        <th><?=lang('cs.unread_count.admin');?></th>
                        <th><?=lang('cs.unread_count.player');?></th>
                        <th><?=lang('lang.status');?></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </form>
    <div class="panel-body"></div>
</div>

<div id="messages_settings" class="modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <form enctype="multipart/form-data" action="javascript: void(0);" class="form-horizontal" method="post" autocomplete="off">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title"><?=lang('lang.settings')?></h5>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs">
                        <li class="nav-item" role="presentation"><a href="#messages-settings-pane-default" role="tab" data-toggle="tab"><?=lang('lang.settings')?></a></li>
                        <?php if($this->utils->isEnabledFeature('enable_player_message_request_form')): ?>
                        <li class="nav-item" role="presentation"><a href="#messages-settings-pane-request-form" role="tab" data-toggle="tab"><?=lang('message.request_form')?></a></li>
                        <?php endif ?>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane" id="messages-settings-pane-default">
                            <div class="form-group">
                                <label class="control-label" for="admin_send_message_length_limit"><?=lang("cs.admin_send_message_length_limit")?> </label>
                                <input type="number" class="form-control" id="admin_send_message_length_limit" name="admin_send_message_length_limit" value="<?=$this->player_message_library->getDefaultAdminSendMessageLengthLimit()?>" max="4096">
                                <span class="help-block"></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="player_send_message_length_limit"><?=lang("cs.player_send_message_length_limit")?> </label>
                                <input type="number" class="form-control" id="player_send_message_length_limit" name="player_send_message_length_limit" value="<?=$this->player_message_library->getDefaultPlayerSendMessageLengthLimit()?>" max="4096">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <?php if($this->utils->isEnabledFeature('enable_player_message_request_form')): ?>
                        <div class="tab-pane" id="messages-settings-pane-request-form">
                            <div class="form-group">
                                <label class="control-label" for="player_message_request_form_enable_floating_button"><?=lang('player.message.request_form.enable_floating_button')?></label>
                                <input type="hidden" name="player_message_request_form[enable_floating_button]" value="0">
                                <input type="checkbox" id="player_message_request_form_enable_floating_button" name="player_message_request_form[enable_floating_button]" <?=($player_message_request_form_settings['enable_floating_button']) ? 'checked="checked"' : ''?>>
                            </div>
                            <fieldset class="form-group">
                                <legend><?=lang('player.message.request_form.floating_button')?></legend>
                                <div class="form-group">
                                    <div class="col col-md-4">
                                        <label class="control-label" for="player_message_request_form_enable_for_guest"><?=lang('player.message.request_form.enable_for_guest')?></label>
                                    </div>
                                    <div class="col col-md-8">
                                        <input type="hidden" name="player_message_request_form[enable_for_guest]" value="0">
                                        <input type="checkbox" id="player_message_request_form_enable_for_guest" name="player_message_request_form[enable_for_guest]" <?=($player_message_request_form_settings['enable_for_guest']) ? 'checked="checked"' : ''?>>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col col-md-4">
                                        <label class="control-label" for="player_message_request_form_enable_for_player"><?=lang('player.message.request_form.enable_for_player')?></label>
                                    </div>
                                    <div class="col col-md-8">
                                        <input type="hidden" name="player_message_request_form[enable_for_player]" value="0">
                                        <input type="checkbox" id="player_message_request_form_enable_for_player" name="player_message_request_form[enable_for_player]" <?=($player_message_request_form_settings['enable_for_player']) ? 'checked="checked"' : ''?>>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="form-group">
                                <legend><?=lang('player.message.request_form.field_setting')?></legend>
                                <div class="form-group">
                                    <div class="col col-md-4">
                                        <label class="control-label"><?=sprintf(lang('player.message.request_form.real_name'), lang('First Name'))?></label>
                                    </div>
                                    <div class="col col-md-4">
                                        <input type="hidden" name="player_message_request_form[real_name_enable]" value="0">
                                        <input type="checkbox" name="player_message_request_form[real_name_enable]" <?=($player_message_request_form_settings['real_name_enable']) ? 'checked="checked"' : ''?> value="1" data-on-text="<?=lang('lang.show')?>" data-off-text="<?=lang('lang.hide')?>">
                                    </div>
                                    <div class="col col-md-4">
                                        <input type="hidden" name="player_message_request_form[real_name_required]" value="0">
                                        <input type="checkbox" name="player_message_request_form[real_name_required]" <?=($player_message_request_form_settings['real_name_required']) ? 'checked="checked"' : ''?> value="1" data-on-text="<?=lang('lang.required')?>" data-off-text="<?=lang('lang.optional')?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col col-md-4">
                                        <label class="control-label"><?=sprintf(lang('player.message.request_form.username'), lang('Username'))?></label>
                                    </div>
                                    <div class="col col-md-4">
                                        <input type="hidden" name="player_message_request_form[username_enable]" value="0">
                                        <input type="checkbox" name="player_message_request_form[username_enable]" <?=($player_message_request_form_settings['username_enable']) ? 'checked="checked"' : ''?> value="1" data-on-text="<?=lang('lang.show')?>" data-off-text="<?=lang('lang.hide')?>">
                                    </div>
                                    <div class="col col-md-4">
                                        <input type="hidden" name="player_message_request_form[username_required]" value="0">
                                        <input type="checkbox" name="player_message_request_form[username_required]" <?=($player_message_request_form_settings['username_required']) ? 'checked="checked"' : ''?> value="1" data-on-text="<?=lang('lang.required')?>" data-off-text="<?=lang('lang.optional')?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col col-md-4">
                                        <label class="control-label"><?=lang('player.message.request_form.contact_method')?></label>
                                    </div>
                                    <div class="col col-md-4">
                                        <input type="hidden" name="player_message_request_form[contact_method_enable]" value="0">
                                        <input type="checkbox" name="player_message_request_form[contact_method_enable]" <?=($player_message_request_form_settings['contact_method_enable']) ? 'checked="checked"' : ''?> value="1" data-on-text="<?=lang('lang.show')?>" data-off-text="<?=lang('lang.hide')?>">
                                    </div>
                                    <div class="col col-md-4">
                                        <input type="hidden" name="player_message_request_form[contact_method_required]" value="0">
                                        <input type="checkbox" name="player_message_request_form[contact_method_required]" <?=($player_message_request_form_settings['contact_method_required']) ? 'checked="checked"' : ''?> value="1" data-on-text="<?=lang('lang.required')?>" data-off-text="<?=lang('lang.optional')?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col col-md-4">
                                        <label class="control-label"><?=lang('player.message.request_form.contact_method')?></label>
                                    </div>
                                    <div class="col col-md-4">
                                        <select name="player_message_request_form[contact_method]" id="player_message_request_form_contact_method">
                                            <option value="mobile_phone" <?=($player_message_request_form_settings['contact_method'] == 'mobile_phone') ? 'selected="selected"' : ''?>><?=sprintf(lang('player.message.request_form.contact_method.mobile_phone'), lang('Contact Number'))?></option>
                                            <option value="email" <?=($player_message_request_form_settings['contact_method'] == 'email') ? 'selected="selected"' : ''?>><?=sprintf(lang('player.message.request_form.contact_method.email'), lang('Email'))?></option>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="form-group">
                                <label class="control-label" for="player_message_request_form_window_title"><?=lang('player.message.request_form.window_title')?></label>
                                <input type="text" class="form-control" id="player_message_request_form_window_title" name="player_message_request_form[window_title]" value="<?=$player_message_request_form_settings['window_title']?>">
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="player_message_request_form_footer_notice"><?=lang('player.message.request_form.footer_notice')?></label>
                                <input type="text" class="form-control" id="player_message_request_form_footer_notice" name="player_message_request_form[footer_notice]" value="<?=$player_message_request_form_settings['footer_notice']?>">
                            </div>
                        </div>
                        <?php endif ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>" data-dismiss="modal"><?=lang('lang.close')?></button>
                    <button type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>"><?=lang('lang.save')?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="messages_view_message" class="modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <input type="hidden" name="message_id">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">
                    <span class="message_subject"></span>
                </h5>
            </div>
            <div class="modal-body">
                <div class="message_details">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-markAsClose <?=($this->permissions->checkPermissions('mark_as_closed_message')) ? '' : 'hidden'?>"><?=lang('cs.markclose');?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('lang.close');?></button>
                <button type="button" class="btn btn-primary btn-reply"><i class="fa fa-comment"></i> <?=lang('cs.reply')?></button>
            </div>
        </div>
    </div>
</div>

<div id="messages_reply_message" class="modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <form enctype="multipart/form-data" action="javascript: void(0);" method="post" autocomplete="off">
            <input type="hidden" name="message_id">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title">
                        <span class="message_subject"></span>
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="message-sender"><?=lang("cs.sender")?> </label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="message-sender" name="message-sender" required >
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default use_default_admin_sender" data-value="<?=$default_admin_sender_name?>"><?=lang('cs.use_default_admin_sender')?></button>
                                <button type="button" class="btn btn-default custom_admin_sender"><?=lang('cs.custom_admin_sender')?></button>
                                <button type="button" class="btn btn-default save_to_default_admin_sender" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?=lang('Saving')?>"><?=lang('cs.save_to_default_admin_sender')?></button>
                            </div>
                        </div>
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group message-content">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=lang('lang.close')?></button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-comment"></i> <?=lang('cs.reply')?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    var setting_modal = $('#messages_settings');
    var view_modal = $('#messages_view_message');
    var reply_modal = $('#messages_reply_message');

    $("[type='checkbox']", setting_modal).bootstrapSwitch();

    $(document).ready(function(){
        var dataTable = $('#myTable').DataTable({
            "responsive":
            {
                details: {
                    type: 'column'
                }
            },
            autoWidth: false,
            searching: false,
            "dom":"<'panel-body' <'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                  extend: 'colvis',
                  postfixButtons: [ 'colvisRestore' ]
                }
            ],

            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            },
            {
               orderable: false,
                targets:   [1, 2, 3, 4, 5, 7, 8, 9]
            } ],

            "order": [ 6, 'desc' ],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {
				data.extra_search = $('#search-form').serializeArray();

                $.post(base_url + "cs_management/getMessagesFilter", data, function(data) {
                    callback(data);
                },'json');
            }
        });

         //---Set Value for hidden data for search deletion upon document ready
        $('input[name="date_from_delete"]').val($('input[name="date_from"]').val());
        $('input[name="date_to_delete"]').val($('input[name="date_to"]').val());
        $('input[name="sender_delete"]').val($('input[name="sender"]').val());
        $('input[name="subject_delete"]').val($('input[name="subject"]').val());
        $('input[name="messages_delete"]').val($('input[name="messages"]').val());

        $('#search-message').click( function() {
			$('input[name="messagesdetails"]').val('');

             //---Set Value for hidden data for search deletion upon search click
            $('input[name="date_from_delete"]').val($('input[name="date_from"]').val());
            $('input[name="date_to_delete"]').val($('input[name="date_to"]').val());
            $('input[name="sender_delete"]').val($('input[name="sender"]').val());
            $('input[name="subject_delete"]').val($('input[name="subject"]').val());
            $('input[name="messages_delete"]').val($('input[name="messages"]').val());
            dataTable.ajax.reload();
        });

        $('.btn_close, .close').click( function() {
            dataTable.ajax.reload();
        });
        var dateFrom = $("#date_from").val();
        var dateTo = $("#date_to").val();

        $('#btnResetFields').click(function(){
            $("#date_from").val(dateFrom);
            $("#date_to").val(dateTo);
            $("#messages").val("");
            $("#sender").val("");
            $("#subject").val("");
            $("#status").val("player_new");
            $("#search_date").val(dateFrom+' to '+dateTo);
        });
    });

    function checkAll(id) {
        var list = document.getElementsByClassName(id);
        var all = document.getElementById(id);

        if (all.checked) {
            for (i = 0; i < list.length; i++) {
                list[i].checked = 1;
            }
            $("#delete_form").attr("onsubmit","return confirmDelete();");

        } else {
            for (i = 0; i < list.length; i++) {
                list[i].checked = 0;
            }

            $("#delete_form").attr("onsubmit","");
        }
    }

    function uncheckAll(id) {
        var list = document.getElementById(id).dataset.checkedAllFor;
        var all = document.getElementById(list);

        var item = document.getElementById(id);
        var allitems = document.getElementsByClassName(list);
        var cnt = 0;

        if (item.checked) {
            for (i = 0; i < allitems.length; i++) {
                if (allitems[i].checked) {
                    cnt++;
                }
            }

            if (cnt == allitems.length) {
                all.checked = 1;
            }
        } else {
            all.checked = 0;
        }
    }

    reply_modal.on('show.bs.modal', function(){
        view_modal.modal('hide');
    });

    setting_modal.on('show.bs.modal', function(){
        $('.nav-tabs a:first', $(this)).tab('show');
    });

    $('.btn-message-setting').on('click', function(){
        setting_modal.modal('show');
    });

    $('form', setting_modal).on('submit', function(){
        var form = $(this);

        BootstrapDialog.show({
            closable: false,
            title: "<?=lang('lang.settings')?>",
            onshow: function(dialog) {
                setting_modal.modal('hide');
                var close_btn = dialog.getButton('close');
                close_btn.spin();

                $.ajax({
                    "contentType": "application/x-www-form-urlencoded; charset=UTF-8",
                    "url": "<?=site_url('cs_management/messageSetting')?>",
                    "type": "POST",
                    "data": form.serialize(),
                    "success": function(result){
                        dialog.close();

                        if(result['status'] === 'success'){
                            BootstrapDialog.show({
                                "type": BootstrapDialog.TYPE_SUCCESS,
                                "message": result['message'],
                                "onhide": function(){
                                    window.location.reload(true);
                                }
                            });
                        }else{
                            BootstrapDialog.show({
                                "type": BootstrapDialog.TYPE_DANGER,
                                "message": result['message'],
                                "onhide": function(){
                                    window.location.reload(true);
                                }
                            });
                        }
                    },
                    "error": function(){
                        dialog.close();
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

    $('.btn-send-message').on('click', function(){
        sbe_messages_send_message(null, null, function(){
            $('#status').val('unprocessed');
            $('#search-message').trigger('click');
        });
    });

    $('.btn-reply').on('click', function(){
        var message_id = $('[name="message_id"]', reply_modal).val();

        reply_modal.modal('show');

        $('.use_default_admin_sender', reply_modal).trigger('click');

        var textarea = $('<textarea class="form-control" required></textarea>');
        $('.message-content', reply_modal).empty().append(textarea).show();

        sceditor.create(textarea[0], sceditor_message_default_options);
        var sceditor_instance = window.sceditor_instance = sceditor.instance(textarea[0]);

        $('form', reply_modal).off('submit').on('submit', function(){
            var message_sender = $('[name="message-sender"]', reply_modal).val();
            var message_html = $(sceditor_instance.getBody()).html();
            var message_text = $(sceditor_instance.getBody()).text();


            if(!message_text.length){
                sceditor_instance.focus();
                return false;
            }

            send_reply_message(message_id, message_sender, message_html, function(){
                message_reply_message(message_id);

                $('#search-message').trigger('click');
            });

            return false;
        });
    });

    $('.btn-markAsClose').on('click', function(){
        var message_id = $('[name="message_id"]', view_modal).val();

        BootstrapDialog.show({
            closable: false,
            title: $.trim($('.modal-title', view_modal).html()),
            message: "<?=lang('cs.confirm_mark_as_close')?>",
            onshow: function(dialog) {
                view_modal.modal('hide');
            },
            buttons: [{
                id: 'close',
                label: '<?=lang('lang.close')?>',
                action: function(dialogRef){
                    dialogRef.close();
                }
            },{
                id: 'confirm',
                cssClass: 'btn-primary',
                label: '<?=lang('Confirmation')?>',
                action: function(dialogRef){
                    var $button = this;
                    $button.spin();
                    $.ajax({
                        "contentType": "application/x-www-form-urlencoded; charset=UTF-8",
                        "url": "<?=site_url('cs_management/markAsClose')?>/" + message_id,
                        "type": "GET",
                        "success": function(result){
                            dialogRef.close();
                            $('#search-message').trigger('click');

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
                        },
                        "error": function(){
                            dialog.close();
                        }
                    });
                }
            }]
        });
    });

    function message_reply_message(message_id){
        $('.message_details', view_modal).empty();
        $('.message_details', view_modal).html('<img src="' + imgloader + '" />');

        view_modal.modal('show');

        $.ajax({
            "url": "/cs_management/viewChatDetails/" + message_id,
            "success": function(result){
                if(result.status === "success"){
                    render_reply_message(result.data);
                }else{

                }
            },
            "error": function(){
            }
        });
    }

    function render_reply_message(data){
        console.log(data);
        $('[name="message_id"]', view_modal).val(data.topic.messageId);
        $('[name="message_id"]', reply_modal).val(data.topic.messageId);

        $('.message_subject', view_modal).html('<?=lang('cs.subject')?>: &nbsp;' + data.topic['subject']);
        $('.message_subject', reply_modal).html('<?=lang('cs.subject')?>: &nbsp;' + data.topic['subject']);

        $('.message_details', view_modal).empty();
        $.each(data.messages, function(idx, message_content){
            var entry = $('<div class="message-entry">');
            entry.attr('data-message-detail-id', message_content['messageDetailsId']);

            var author = $('<div class="message-author">').appendTo(entry);
            if(message_content['flag'] === 'player'){
                entry.addClass('message-author-player');
                author.html(message_content['sender']);
            }else{
                entry.addClass('message-author-admin').addClass('sceditor-support');
                author.html(message_content['sender']);
            }

            var datetime = $('<div class="message-datetime">').appendTo(entry);
            datetime.html(message_content['date']);

            var detail = $('<div class="message-detail">').appendTo(entry);
            detail.html(message_content['detail']);

            entry.appendTo($('.message_details', view_modal));
        });

        $('.message_details', view_modal).scrollTop($('.message_details', view_modal)[0].scrollHeight);

        if(data.flags.is_disabled_reply || data.flags.is_deleted){
            $('.btn-reply').hide();
            $('.btn-markAsClose').hide();
        }else{
            $('.btn-reply').show();
            $('.btn-markAsClose').show();
        }

        if(data.topic.message_type == "<?=Internal_message::MESSAGE_TYPE_REQUEST_FORM?>"){
            $('.btn-reply').hide();
        }
    }

    function send_reply_message(message_id, sender, content, callback){
        BootstrapDialog.show({
            closable: false,
            title: $.trim($('.modal-title', view_modal).text()),
            onshow: function(dialog) {
                reply_modal.modal('hide');
                var close_btn = dialog.getButton('close');
                close_btn.spin();

                $.ajax({
                    "contentType": "application/x-www-form-urlencoded; charset=UTF-8",
                    "url": "<?=site_url('cs_management/reply')?>/" + message_id,
                    "type": "POST",
                    "data": {
                        "message-sender": sender,
                        "message": content
                    },
                    "success": function(result){
                        dialog.close();

                        if(result['status'] === 'success'){
                            BootstrapDialog.show({
                                "type": BootstrapDialog.TYPE_SUCCESS,
                                "message": result['message'],
                                "onhide": function(){
                                    if(typeof callback === "function") callback(true);
                                }
                            });
                        }else{
                            BootstrapDialog.show({
                                "type": BootstrapDialog.TYPE_DANGER,
                                "message": result['message'],
                                "onhide": function(){
                                    if(typeof callback === "function") callback(false);
                                }
                            });
                        }
                    },
                    "error": function(){
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
    }

</script>
