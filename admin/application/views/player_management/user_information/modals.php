<!-- mainModal Start -->
    <script type="text/javascript">
        function modal(load, title) {
            $('#mainModalLabel').html(title);
            $('#mainModal .modal-body').html('<center><img src="' + imgloader + '"></center>').load(load);
            $('#mainModal').modal('show');
        }
    </script>
    <div class="modal fade in" id="mainModal" tabindex="-1" role="dialog" aria-labelledby="mainModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="mainModalLabel"></h4>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
<!-- mainModal End -->


<!-- simpleModal Start -->
    <script type="text/javascript">
        function success_modal(title, content) {
            var button = '<button class="btn btn-sm btn-scooter" data-dismiss="modal" aria-label="Close"><?=lang('OK')?></button>';

            $('#simpleModal-header').removeClass();
            $('#simpleModal-header').addClass("modal-header modal-header-success");

            showSimpleModal(title, content, button)
        }

        function success_modal_custom_button(title, content, button) {
            $('#simpleModal-header').removeClass();
            $('#simpleModal-header').addClass("modal-header modal-header-success");

            showSimpleModal(title, content, button)
        }

        function error_modal(title, content) {
            var button = '<button class="btn btn-sm btn-scooter" data-dismiss="modal" aria-label="Close"><?=lang('OK')?></button>';

            $('#simpleModal-header').removeClass();
            $('#simpleModal-header').addClass("modal-header modal-header-danger");

            showSimpleModal(title, content, button)
        }

        function confirm_modal(title, content, button) {
            $('#simpleModal-header').removeClass();
            $('#simpleModal-header').addClass("modal-header modal-header-info");

            showSimpleModal(title, content, button)
        }

        function showSimpleModal(title, content, button) {
            $('#simpleModalLabel').html(title);
            $('#simpleModal .modal-body').html(content);
            $('#simpleModal .modal-footer').html(button);
            $('#simpleModal').modal('show');
        }
    </script>
    <div class="modal fade in" id="simpleModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" id="simpleModal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="simpleModalLabel"></h4>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>
<!-- simpleModal End -->


<!-- Basic Info -->
    <div class="modal fade in" id="comm_pref_notes" tabindex="-1" role="dialog" aria-labelledby="label_comm_pref_notes">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="label_comm_pref_notes"><?=lang('Notes')?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <textarea name="comm_pref_notes" id="comm_pref_notes_content" class="form-control input-sm" rows="7" placeholder="<?=lang('Add notes')?>..."></textarea>
                        <span class="help-block text-danger"></span>
                    </div>
                    <button data-value="" data-key="" class="btn btn-scooter pull-right" id="comm_pref_submit"><?=lang('lang.submit')?></button>
                    <div class="clearfix"></div>
                </div>

            </div>
        </div>
    </div>
<!-- Basic Info -->

<!-- Responsible Gaming -->
    <div class="modal fade bs-example-modal-md" id="rsp_cancel_modal" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header panel-heading">
                    <h3 class="rsp_cancel_title"><?=lang('responsible_gaming.manual.canceled')?></h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" ><?=lang("Reason")?></label>
                                <textarea class="form-control rsp_cancel_reason" rows="3" maxlength="300"></textarea>
                                <span class="help-block text-danger"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-linkwater rsp-reset-reason"><?=lang('lang.reset');?></button>
                    <button type="button" class="btn btn-linkwater rsp-cancel-send" data-dismiss="modal"><?=lang('lang.close');?></button>
                    <button type="button" class="btn btn-scooter btn-submit"><?=lang('cu.9')?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-md" id="rsp_expire_modal" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header panel-heading">
                    <h3 class="rsp_expire_title"><?=lang('responsible_gaming.manual.expired')?></h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" ><?=lang("Reason")?></label>
                                <textarea class="form-control rsp_expire_reason" rows="3" maxlength="300"></textarea>
                                <span class="help-block text-danger"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-linkwater rsp-reset-reason"><?=lang('lang.reset');?></button>
                    <button type="button" class="btn btn-linkwater rsp-expire-send" data-dismiss="modal"><?=lang('lang.close');?></button>
                    <button type="button" class="btn btn-scooter btn-submit"><?=lang('cu.9')?></button>
                </div>
            </div>
        </div>
    </div>
<!-- Responsible Gaming -->


<!-- Withdrawal Condition -->
    <div id="conf-modal" class="modal fade bs-example-modal-md" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header panel-heading">
                    <h3 id="myModalLabel"><?=lang('sys.pay.conf.title');?></h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="help-block" id="conf-msg-ask"></div>
                            <div class="form-group">
                                <label class="control-label" id="conf-msg-reason" ></label>
                                <textarea class="form-control" id="reason-to-cancel"rows="3"></textarea>
                                <div class="help-block text-danger"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="conf-cancel-action" class="btn btn-default" data-dismiss="modal"><?=lang('pay.bt.cancel');?></button>
                    <button type="button" id="conf-yes-action" class="btn btn-scooter"><?=lang('pay.bt.yes');?></button>
                </div>
            </div>
        </div>
    </div>
<!-- Withdrawal Condition -->


<!-- Transfer Condition -->
    <!--WALLET INFO MODAL START-->
    <div class="modal fade bs-example-modal-md" id="wallet_info_modal" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header panel-heading wallet_info_header">
                    <h3 class="wallet_info_title"><?=lang('lang.details')?></h3>
                </div>
                <div class="modal-body wallet_info_body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" class="transfer_condition_id">
                            <input type="hidden" class="wallet_info">
                            <table class="table table-hover table-bordered table-condensed wallet_info_table">
                                <thead>
                                    <tr>
                                        <th><?=lang('Wallet');?></th>
                                    </tr>
                                </thead>
                                <tbody class="wallet_body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer wallet_info_footer">
                    <button type="button" class="btn btn-default close_wallet_info" data-dismiss="modal"><?=lang('lang.close');?></button>
                </div>
            </div>
        </div>
    </div>
    <!--WALLET INFO MODAL END-->

    <!--TRANSFER CONDITION CANCEL MODAL START-->
    <div class="modal fade bs-example-modal-md" id="cancel_tc_modal" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header panel-heading">
                    <h3 class="tc_cancel_title"><?=lang('sys.pay.conf.title')?></h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="help-block tc-conf-msg-ask"></div>
                            <div class="form-group">
                                <label class="control-label" ><?=lang("pay.reason")?></label>
                                <textarea class="form-control" id="tc_cancel_reason" rows="3" maxlength="300"></textarea>
                                <span class="help-block text-danger"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" >
                    <button type="button" class="btn btn-default tc-reset-reason"><?=lang('lang.reset');?></button>
                    <button type="button" class="btn btn-default tc-cancel-send" data-dismiss="modal"><?=lang('lang.close');?></button>
                    <button type="button" class="btn btn-scooter btn-submit" id="transfer_condition-submit_btn"><?=lang('cu.9')?></button>
                </div>
            </div>
        </div>
    </div>
    <!--TRANSFER CONDITION CANCEL MODAL END-->
<!-- Transfer Condition -->


<!-- Player's Log - Duplicate Account List -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('#all_tag_list').on('change', function(){
                if( $(this).val() == "" ){
                    $('#addTagModal-submit-btn').attr('disabled', 'disabled');
                } else {
                    $('#addTagModal-submit-btn').removeAttr('disabled');
                }
            });
        });
    </script>
    <div class="modal fade" id="addTagModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width: 25%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?=lang('sys.batchAddTag')?></h4>
                </div>
                <form id="add_batch_tags" action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="subject_player_id" value="" />
                        <input type="hidden" name="playerIDs" value="" />

                        <div class="row">
                            <div class="col-md-12">
                                <div class="well"  id="add_staticsites_form">
                                    <select class="form-control" name="tagId" id="all_tag_list">
                                        <option value="" selected="selected"><?=lang('player.ui72')?></option>
                                        <option value="0"><?=lang('player.tp12')?></option>
                                        <?php if(is_array($tags)): ?>
                                            <?php foreach( $tags as $idx => $val ): ?>
                                                <option value="<?=$val['tagId']?>"><?=$val['tagName']?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                       <div class="form-group">
                            <button class="btn btn-sm btn-default" data-dismiss="modal" aria-label="Close"><?=lang('Cancel')?></button>
                            <button onclick="saveTags();" id="addTagModal-submit-btn" class="btn btn-sm btn-scooter" disabled="disabled"><?=lang("lang.save")?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addLinkAcctModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width: 25%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?=lang('sys.linkAcct')?></h4>
                </div>
                <form id="link_batch_accts" action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="linkAcctsplayerIDs" value="" />
                        <span id="linkAcctsplayerUsernames" style="word-wrap: break-word;"></span>
                        <input type="hidden" name="linkAcctsplayerUserId" value="" />
                        <br/>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-default" data-dismiss="modal" aria-label="Close"><?=lang('Cancel')?></button>
                        <button onclick="saveLinkAccounts();" class="btn btn-sm btn-scooter"><?=lang("lang.save")?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- Player's Log - Duplicate Account List -->

<!-- Linked Account Edit Remarks Modal Start -->
<?php $this->load->view('player_management/linked_account/linked_account_modals'); ?>
<!-- Linked Account Edit Remarks Modal End -->


<?php include VIEWPATH . '/includes/messages.php'; ?>
<?php include VIEWPATH . '/includes/sms.php'; ?>
<?php include VIEWPATH . '/includes/popup_promorules_info.php';?>
<?php include VIEWPATH . "/includes/big_wallet_details.php";?>
<?php include VIEWPATH . "/report_management/duplicate_account_modal.php";?>
