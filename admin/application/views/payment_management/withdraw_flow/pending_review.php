<?=$this->load->view("resources/third_party/bootstrap-tagsinput")?>

<td colspan="1">
    <input type="checkbox" checked disabled />
    <label><?=lang('pay.penreview')?></label>
</td>
<td>
    <div id="player-tagged-info">
        <div id="recorded-tag" class="col col-md-10 no-gutter">
            <?=lang('player.tp12')?>
        </div>
        <?php if ($this->permissions->checkPermissions('edit_player_tag')): ?>
            <button style="margin-bottom:2px;" type="button" id="add-player-tag" title="<?=lang('con.plm72')?>" class="btn btn-info btn-xs pull-right"><i class="glyphicon glyphicon-edit"></i><?=lang('con.plm72')?></button>
        <?php endif ?>
    </div>
    <div id="player-tagged-form">
        <?php if ($this->permissions->checkPermissions('edit_player_tag')): ?>
            <span id="success-glyph-tag" class="glyphicon glyphicon-ok text-success"></span>
            <span id="ajax-status-tag"><?=lang('text.loading')?></span>
            <button style="margin-bottom:2px;" type="button" id="save-player-tag" title="<?=lang('player.ui63')?>" class="btn btn-success btn-xs pull-right"><i class="glyphicon glyphicon-floppy-disk"></i><?=lang('player.ui63')?></button>
            <button style="margin-bottom:2px;" type="button" id="cancel-player-tag" title="<?=lang('player.ui65')?>" class="btn btn-danger btn-xs pull-right"><i class="glyphicon glyphicon-remove"></i><?=lang('player.ui65')?></button>
            <input type="hidden" id="tags-options" name="tags-options" sbe-ui-toogle="tagsinput" data-freeInput="false" />
            <select id="tags-list"  name="tags-list" class="form-control input-sm"></select>
        <?php endif ?>
    </div>
</td>

<script type="text/javascript">
    //////*******************PLAYER TAGGING START**************************************

    var PLAYER_TAGGING = (function () {
        var playerTaggedInfo = $('#player-tagged-info'),
            playerTaggedForm = $('#player-tagged-form'),
            addTag = $('#add-player-tag'),
            playerTaggedInput = $('#tags-options'),
            tagList = $('#tags-list'),
            save = $("#save-player-tag"),
            cancel = $("#cancel-player-tag"),
            recordedTag = $("#recorded-tag"),
            ajaxStatus = $("#ajax-status-tag"),
            GET_ALLTAGS_URL = '<?php echo site_url('payment_management/getAllTagsForPendingReview') ?>/',
            PENDING_REQUEST_TAG_URL = '<?php echo site_url('payment_management/pendingRequestTags') ?>',
            successGlyph = $("#success-glyph-tag"),
            tag_list = <?=($tag_list) ? json_encode($tag_list) : "{}" ?>,
            chosenTag = <?=($taggedStatus) ? json_encode($taggedStatus) : "[]" ?>,
            not_tagged_text = "<?=lang('player.tp12')?>",
            has_playerTagged = "<?=($taggedStatus) ? 0 : 1?>";

        LANG = {
            SELECT_TAG: "<?=lang('player.ui72')?>",
            NO_TAG: "<?=lang('player.ui73')?>"
        };

        addTag.tooltip({
            placement: "top"

        });

        cancel.tooltip({
            placement: "top"

        });

        save.tooltip({
            placement: "top"

        });

        addTag.click(function () {
            getAllTags();
            showAjaxStatus();
            hideSuccessGlyph();
        });

        cancel.click(function () {
            hideAjaxStatus();
            closeEditForm();
            disableSaveButton();
        });

        save.click(function () {
            addUpdateTag();
            disableCancelButton();
        });

        tagList.on('change', function () {
            var option = $(this).find('option:selected');
            playerTaggedInput.tagsinput('add', {id: tagList.val(), text: option.text(), color: option.data('color')});
            ableSaveButton();
        });

        playerTaggedInput.on('itemRemoved', function(){
            ableSaveButton();
        });

        //Initial settings
        closeEditForm();
        hideAjaxStatus();
        disableSaveButton();
        hideSuccessGlyph();
        renderPlayerTagged();

        function getAllTags() {
            $.ajax({
                url: GET_ALLTAGS_URL,
                type: 'GET',
                dataType: "json"
            }).done(function (data) {
                removeOptions();
                playerTaggedInput.tagsinput('removeAll');

                appendSelectPlaceholder();
                var tags = data.tags,
                    tagStatus = data.tagStatus,
                    tagsLength = tags.length;

                for (var i = 0; i < tagsLength; i++) {
                    tagList.append('<option value="' + tags[i].tagId + '" data-color="' + tags[i].tagColor + '">' + tags[i].tagName + '</option>');
                    if(tagStatus && ($.inArray(tags[i].tagId, tagStatus) >= 0)){
                        playerTaggedInput.tagsinput('add', {id: tags[i].tagId, text: tags[i].tagName, color: tags[i].tagColor});
                    }
                }

                hideAjaxStatus();
                showEditForm();
            }).fail(function (jqXHR, textStatus) {
                if (jqXHR.status >= 300 && jqXHR.status < 500) {
                    location.reload();
                } else {
                    alert(textStatus);
                }
            });
        }

        function addUpdateTag() {
            disableSaveButton();

            var select_items = playerTaggedInput.tagsinput('items');
            var tagIds = [];

            $.each(select_items, function(id, item){
                tagIds.push(item['id']);
            });
            var data = {
                tagId: tagIds
            };

            $.ajax({
                url: PENDING_REQUEST_TAG_URL,
                type: 'POST',
                data: data,
                dataType: "json",
                cache: false
            }).done(function (data) {
                if (data.status == "success") {
                    closeEditForm();
                    ableCancelButton();
                    showSuccessGlyph();
                    renderPlayerTagged(data.tagStatus);
                }else{
                    ableSaveButton();
                    ableCancelButton();
                    alert("<?=lang('text.error')?>");

                }

            }).fail(function (jqXHR, textStatus) {
                /*Note: this is for session timeout,if the session is out because this is ajax, eventually it will go to log in page*/
                if (jqXHR.status >= 300 && jqXHR.status < 500) {
                    location.reload();
                } else {
                    alert(textStatus);
                }
            });
        }

        function showSuccessGlyph() {
            successGlyph.show();
        }

        function hideSuccessGlyph() {
            successGlyph.hide();
        }

        function removeEditButton() {
            addTag.remove();
        }

        function disableCancelButton() {
            cancel.prop('disabled', true);
        }

        function ableCancelButton() {
            cancel.prop('disabled', false);
        }

        function disableSaveButton() {
            save.prop('disabled', true);
        }

        function ableSaveButton() {
            save.prop('disabled', false);
        }

        function showAjaxStatus() {
            ajaxStatus.show();
        }

        function hideAjaxStatus() {
            ajaxStatus.hide();
        }

        function showEditForm() {
            addTag.hide();
            recordedTag.hide();

            tagList.show();
            playerTaggedInput.show();
            save.show();
            cancel.show();

            playerTaggedInfo.hide();
            playerTaggedForm.show();
        }

        function closeEditForm() {
            tagList.hide()
            playerTaggedInput.hide();
            save.hide();
            cancel.hide();

            recordedTag.show();
            addTag.show();

            playerTaggedInfo.show();
            playerTaggedForm.hide();
        }

        function removeOptions() {
            tagList.html("");
        }

        function appendSelectPlaceholder() {
            tagList.append('<option value="" selected disabled>' + LANG.SELECT_TAG + '</option>');
        }

        function renderPlayerTagged(tagStatus){
            if(tagStatus === undefined){
                tagStatus = chosenTag;
            }

            if(!$.isArray(tagStatus)){
                recordedTag.html(not_tagged_text);
                return;
            }

            recordedTag.html('');
            $.each(tagStatus, function(idx, tagId){

                $.each(tag_list, function(idx2, tagEntry){
                    // console.log(tagEntry,tagId);
                    if(tagEntry['tagId'] !== tagId){
                        return;
                    }

                    var span = $('<span>').addClass('tag label label-info');
                    span.text(tagEntry['tagName']).css('background-color', tagEntry['tagColor']);

                    var a = $('<a>').addClass('tag tag-component').attr('href', '<?=$this->utils->getSystemUrl('admin')?>/player_management/taggedlist?tag=' + tagId + '&search_reg_date=false');
                    recordedTag.append(a.append(span));
                });
            });
        }
    }());
//////*******************PLAYER TAGGING END**************************************
</script>