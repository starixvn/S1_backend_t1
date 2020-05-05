<style>
*{
    outline:;
}
    /* .player_note_list-group {
        height: 50%;
        overflow: scroll;
        overflow-x: hidden;
    } */
    .player-remarks-modal .vertical-alignment-helper {
        display:table;
        height: 100%;
        width: 100%;
        pointer-events:none; /* This makes sure that we can still click outside of the modal to close it */
    }
    .player-remarks-modal .vertical-align-center {
        /* To center vertically */
        display: table-cell;
        vertical-align: middle;
        pointer-events:none;
    }
    .player-remarks-modal .modal-content {
        /* Bootstrap sets the size of the modal in the modal-dialog class, we need to inherit it */
        max-width:inherit; /* For Bootstrap 4 - to avoid the modal window stretching full width */
        height:inherit;
        pointer-events: all;
    }
    .dot-btn{
        margin-left: -15px;
        background-color: transparent;
    }
    .player-remarks-modal{
        position: absolute;
        top: -66px;
        right: -21px;
        width: 600px;
    }
    #edit-player-remarks-modal{
        z-index: 1090;
    }
    #delete-player-remarks-modal{
        z-index: 1090;
    }
    .note_list-item .btn-group{
        display: none;
        height:12px;
    }
    .note_list-item:hover .btn-group{
        display: block;
    }
    .note_list-item:hover{
        background-color: #f0f0f0;
    }
    .remarks-error-text p{
        font-size: 8px;
    }
    .note_list-item{
        box-sizing:border-box;
        padding-bottom:90px;
        border-style:none;
    }
    .old_note{
        border-style: none;
    }
    .note-content{
        box-sizing:border-box;
        padding-top:8px;
        height:100px;
        max-height:160px;
        max-width:505px;
        word-break: break-all;
    }
</style>
<div>
    <ul class="player_note_list-group">
        <?php
        if(!empty($notes)):
            foreach ($notes as $note): ?>
            <li class="btn-control list-group-item note_list-item">
                <input class="old_note" type="hidden" value="<?=$note['notes']?>"/>
                <div class="row">
                    <div class="col-md-2">
                        <span class="text-muted pull-right"><?=$note['username']?></span>
                    </div>
                    <div class="col-md-9">
                        <span class="text-muted">
                            <?php
                                if(strtotime($note['updatedOn']) > strtotime($note['createdOn'])) {
                                    echo $note['updatedOn'] . ' (Edited)';
                                }else{
                                    echo $note['createdOn'];
                                }
                            ?>
                        </span>
                    </div>
                    <?php if ($this->permissions->checkPermissions('control_edit_delete_note')){  ?>
                    <div class="btn-group col-md-1">
                        <button class="dot-btn btn glyphicon glyphicon-option-horizontal dropdown-toggle drop_notes" type="button" data-toggle="dropdown">
                        </button>
                        <ul id="dot-menu" class="dropdown-menu" role="menu">
                            <!-- Edit Remarks 小視窗 -->
                            <li><a href="#" id="edit-remark-modal" onclick="return get_noteid_Info(this, <?=$note['noteId']?>, 'edit-remark'); return false;"><span><?=lang('lang.edit')?></span></a></li>
                            <!-- Delete Remarks 小視窗 -->
                            <li><a href="#" id="delete-remark-modal" onclick="return get_noteid_Info(this, <?=$note['noteId']?>, 'delete-remark'); return false;"><span><?=lang('lang.delete')?></span></a></li>
                        </ul>
                    </div>
                    <?php } ?>
                </div>
                <div class="col-md-12 note-content">
                    <?=$note['notes']?>
                </div>

                <!-- Edit Remarks 小視窗 -->
                <form action="" method="post" onsubmit="return edit_player_note(this, <?=$note['noteId']?>, <?=$player_id?>, 'save')">
                    <div class="player-remarks-modal hide remark-modal" id="edit-player-remarks-modal" tabindex="-1" role="dialog">
                        <div class="vertical-alignment-helper">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close">
                                        <span class="close-btn" onclick="closebtn_isclicked(this); return edit_player_note(this, <?=$note['noteId']?>, <?=$player_id?>, 'cancel');">×</span>
                                    </button>
                                    <h4 class="modal-title" id="label_edit_player_remarks"><?=lang('Edit Remark')?></h4>
                                </div>
                                <div class="modal-body">
                                    <span class="display_note">
                                        <textarea name="new_notes" class="form-control input-sm m-b-10" style="max-width: 100%;min-width: 100%;height: 155px; resize: none;" onkeyup="limitTextArea(this,240,'#edit-remarks-error-text');" onkeypress="limitTextArea(this,240,'#edit-remarks-error-text');" onpaste="limitTextArea(this,240,'#edit-remarks-error-text');"><?=$note['notes']?></textarea>
                                    </span>
                                    <div class="row">
                                        <div class="col-lg-7 text-left remarks-error-text" id="edit-remarks-error-text">
                                            <p style="color:white;"><?=lang('Maximum characters')?></p>
                                        </div>
                                        <div class="col-lg-5 text-right">
                                            <button type="button" id="edit-remarks-cancel-btn" class="btn btn-default btn-sm cancel_notes" onclick="return edit_player_note(this, <?=$note['noteId']?>, <?=$player_id?>, 'cancel')">
                                                <span><?=lang('lang.cancel')?></span>
                                            </button>
                                            <button type="submit" class="btn btn-primary btn-sm save_notes">
                                                <span><?=lang('lang.save')?></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="" id="backdrop"></div>

                <!-- Delete Remarks 小視窗 -->
                <div class="player-remarks-modal hide remark-modal" id="delete-player-remarks-modal" tabindex="-1" role="dialog">
                    <div class="vertical-alignment-helper">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close">
                                    <span class="close-btn" onclick="closebtn_isclicked(this)">×</span>
                                </button>
                                <h4 class="modal-title" id="label_delete_player_remarks"><?=lang('Delete Remark')?></h4>
                            </div>
                            <div class="modal-body">
                                <div class="m-b-25 m-t-15">
                                    <p style="color: #6f6f6f; font-size: 14px;"><?=lang('Are you sure you want to delete this remark?')?></p>
                                </div>
                                <div class="text-right">
                                    <button type="button" id="delete-remarks-cancel-btn" class="btn btn-default btn-sm" onclick="return get_noteid_Info(this, <?=$note['noteId']?>, 'cancel-delete');"><?=lang('lang.cancel')?></button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="return remove_player_note(<?=$note['noteId']?>, <?=$player_id?>);"><?=lang('lang.delete')?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="" id="backdrop"></div>
            </li>
        <?php
            endforeach;
        endif; ?>
    </ul>

    <form action="/player_management/add_player_notes/<?=$player_id?>" method="post" onsubmit="return add_player_notes(this, <?=$player_id?>)">
        <div class="form-group col-md-12">
            <textarea name="notes" class="form-control input-sm" rows="5" placeholder="<?=lang('Add remarks')?>..." style="resize: none; max-height: 180px;" onkeyup="autogrow(this); limitTextArea(this,240,'#add-remarks-error-text');" onkeypress="limitTextArea(this,240,'#add-remarks-error-text');" onpaste="limitTextArea(this,240,'#add-remarks-error-text');" required></textarea>
        </div>
        <div class="row">
            <div class="col-lg-6 text-left remarks-error-text" id="add-remarks-error-text" style="margin: -5px 18px 0 16px;">
                <p style="color:white;"><?=lang('Maximum characters')?></p>
            </div>
            <div class="col-lg-5 text-right">
                <button id="preview-send-btn" class="btn btn-primary pull-right btn-sm" style="width:90px; padding:8px; margin-top:-3px;"><?=lang('Add Remarks')?></button>
            </div>
        </div>
    </form>
</div>

<script>
    function limitTextArea(field,lenMax,idName){
        if (field.value.length > lenMax){
            if(idName == "#edit-remarks-error-text"){
                $('#edit-remarks-error-text p').attr("style","color:red");
            }else{
                $('#add-remarks-error-text p').attr("style","color:red");
            }
            field.value = field.value.substring(0, lenMax);
        }else{
            if(idName == "#edit-remarks-error-text"){
                $('#edit-remarks-error-text p').attr("style","color:white");
            }else{
                $('#add-remarks-error-text p').attr("style","color:white");
            }
        }
    }

    function edit_player_note(self, note_id, player_id, action = 'save') {
        var note_list    = $(self).closest('.note_list-item');
        var old_note     = $(note_list).find('.old_note').val();
        var display_note = $(note_list).find('.display_note');
        var edit_remark_modal = $(note_list).find('#edit-player-remarks-modal');

        if (action == 'save'){ //Save
            var url = '/player_management/edit_player_notes/' + note_id + '/' + player_id;
            var params = $(self).serializeArray();
            $.post(url, params, function(data) {
                if (data.success) {
                    refresh_modal('player_notes', "/player_management/player_notes/" + player_id, 'Player Notes', true);
                }
            });
        }

        if (action == 'cancel'){ //Cancel
            $(display_note).empty().append('<textarea name="new_notes" class="form-control input-sm m-b-10" style="max-width: 100%;min-width: 100%;height: 155px; resize: none;" onkeyup="limitTextArea(this,240,'+"'"+'#edit-remarks-error-text'+"'"+');" onkeypress="limitTextArea(this,240,'+"'"+'#edit-remarks-error-text'+"'"+');" onpaste="limitTextArea(this,240,'+"'"+'#edit-remarks-error-text'+"'"+');">' + old_note + '</textarea>');
            $(edit_remark_modal).addClass('hide');
            $('#backdrop').removeClass("modal-backdrop in");
        }

        return false;
    }

    function remove_player_note(note_id, player_id) {
        var url = '/player_management/remove_player_note/' + note_id + '/' + player_id;
        $.getJSON(url, function(data) {
            if (data.success) {
                refresh_modal('player_notes', "/player_management/player_notes/" + player_id, 'Player Notes', true);
            }
        });

        return false;
    }

    function closebtn_isclicked(self) {
        var note_list   = $(self).closest('.note_list-item');
        var error_modal = $(note_list).find(".remark-modal");

        $(error_modal).addClass('hide');
        $('#backdrop').removeClass("modal-backdrop in");
    }


    function get_noteid_Info(self, note_id, active = 'edit-remark'){
        var url = '/player_management/get_noteid_Info/' + note_id;
        var note_list = $(self).closest('.note_list-item');
        var edit_remarks_modal = $(note_list).find('#edit-player-remarks-modal');
        var delete_remarks_modal = $(note_list).find('#delete-player-remarks-modal');

        if(active == 'edit-remark'){
            var modal = edit_remarks_modal.removeClass("hide");
            var modal_backdrop = $('#backdrop').addClass("modal-backdrop in");
        }

        if(active == 'delete-remark'){
            var modal = delete_remarks_modal.removeClass("hide");
            var modal_backdrop = $('#backdrop').addClass("modal-backdrop in");
        }

        if(active == 'cancel-delete'){
            var modal = delete_remarks_modal.addClass("hide");
            var modal_backdrop = $('#backdrop').removeClass("modal-backdrop in");
        }

        $.getJSON(url, function(data) {
            if (data.success) {
                modal;
                modal_backdrop;
            }
        });

        return false;
    }
</script>