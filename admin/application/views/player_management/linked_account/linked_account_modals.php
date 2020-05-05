<!-- EDIT MODAL START-->
<div id="edit-remarks-modal"  class="modal fade bs-example-modal-md"  data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-type="message">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header panel-heading">
                <h3 class="type-message"><?=lang('Edit Remarks')?></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group type-message">
                            <label for="subject"><?=ucwords(lang("username"))?></label>
                            <input type="text" class="form-control input-sm usernameInputTxt" readonly>
                            <input type="hidden" name="linkAccountId" id="linkAccountId" class="form-control" required />
                            <span class="help-block" style="color:#F04124"></span>
                        </div>
                        <div class="form-group">
                            <label class="control-label" ><?=lang("Remarks")?></label>
                            <textarea class="form-control input-sm"  style="resize: none; height: 36px; max-height: 80px;" onkeyup="autogrow(this);" id="remarksTxtArea" rows="3" required></textarea>
                            <span class="help-block" style="color:#F04124"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div style="height:70px;position:relative;">
                    <button type="button" class="btn btn-default"  id="closeBtn" data-dismiss="modal"><?=lang('lang.close');?></button>
                    <button type="button" id="saveRemarksBtn" class="btn btn-primary"><?=lang('Save')?></button>
                    <span id="messageTxt"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!--EDIT MODAL END-->

<!--ADD LINK ACCOUNT MODAL START-->
<div id="add-link-account-modal"  class="modal fade bs-example-modal-md"  data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-type="message">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header panel-heading">
                <h3 class="type-message"><?=lang('Add Linked Account')?></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group type-message">
                            <label for="subject"><?=ucwords(lang("username"))?></label>
                            <select class="js-data-example-ajax" id="addedLinkedAccounts" name="addedLinkedAccounts[]" multiple="multiple" style="width: 100%;"></select>
                            <input type="hidden" name="addLinkAccountId" id="addLinkAccountId" class="form-control" required />

                            <span class="help-block" style="color:#F04124"></span>
                        </div>
                        <div class="form-group">
                            <label class="control-label" ><?=lang("Remarks")?></label>
                            <textarea class="form-control" style="resize: none; height: 36px; max-height: 80px;" onkeyup="autogrow(this);" id="addRemarksTxtArea" rows="3" maxlength="300" required></textarea>
                            <span class="help-block" style="color:#F04124"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div style="height:70px;position:relative;">
                    <button type="button" class="btn btn-default"  id="addCloseBtn" data-dismiss="modal"><?=lang('lang.close');?></button>
                    <button type="button" id="saveAddLinkAccountBtn" class="btn btn-primary"><?=lang('Save')?></button>
                    <span id="addMessageTxt"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!--ADD LINK ACCOUNT MODAL END -->