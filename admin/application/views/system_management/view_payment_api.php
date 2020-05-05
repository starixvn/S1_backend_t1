<div class="row" id="user-container">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <div class="panel-heading custom-ph">
                <h3 class="panel-title custom-pt" >
                    <i class="icon-list"></i>
                    <?=lang('sys.pay.paneltitle');?>
                </h3>
            </div>
            <div class="panel-body" id="list_panel_body">
                <form autocomplete="on" id="my_form">
                    <div class="row">
                          <div class="btn-action col-md-12">
                            <div class="btn-action">
                                <button type="button" value="" id="add-row" name="btnSubmit" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>">
                                    <i class="glyphicon glyphicon-plus" style="color:white;"  data-placement="bottom" ></i>
                                    <?=lang('sys.pay.add.button');?>
                                </button>&nbsp;
                                <button type="button" value="" id="delete-items" name="btnSubmit" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-chestnutrose' : 'btn-danger'?>">
                                    <i class="glyphicon glyphicon-trash" style="color:white;"  data-placement="bottom" ></i>
                                    <?=lang('sys.pay.delete.gameapi');?>
                                </button>&nbsp;
                            </div>
                          </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-condensed" style="width:100%;" id="my_table" >
                            <thead>
                                <tr>
                                    <th style="min-width: 70px;"><?=lang('sys.pay.action');?></th>
                                    <th><?=lang('sys.pay.systemid');?></th>
                                    <th><?=lang('sys.pay.systemname');?></th>
                                    <th><?=lang('sys.pay.status');?></th>
                                    <th><?=lang('sys.pay.note');?></th>
                                    <th><?=lang('sys.pay.lastsyncdt');?></th>
                                    <th><?=lang('sys.pay.lastsyncid');?></th>
                                    <th><?=lang('sys.pay.lastsyncdet');?></th>
                                    <th><?=lang('sys.pay.systemtype');?></th>
                                    <th><?=lang('sys.pay.liveurl');?></th>
                                    <th><?=lang('sys.pay.sandboxurl');?></th>
                                    <th><?=lang('sys.pay.livekey');?></th>
                                    <th><?=lang('sys.pay.livesecret');?></th>
                                    <th><?=lang('sys.pay.sandboxkey');?></th>
                                    <th><?=lang('sys.pay.sandboxsecret');?></th>
                                    <th><?=lang('sys.pay.livemode');?></th>
                                    <th><?=lang('sys.pay.secondurl');?></th>
                                    <th><?=lang('sys.pay.sandboxacct');?></th>
                                    <th><?=lang('sys.pay.liveacct');?></th>
                                    <th><?=lang('sys.pay.systemcode');?></th>
                                    <th><?=lang('sys.pay.classname');?></th>
                                    <th><?=lang('sys.pay.localpath');?></th>
                                    <th><?=lang('sys.pay.manager');?></th>
                                    <th style="max-width: 300px;"><?=lang('sys.pay.extrainfo');?></th>
                                    <th style="max-width: 300px;"><?=lang('sys.pay.sandboxextrainfo');?></th>
                                    <th><?=lang('pay.createdon');?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($paymentApis as $row): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" title="<?=lang('sys.gd21');?>" class="delete_id_selector" id="id_selector-<?=$row['id']?>" name="id_selector"/>
                                            <?php if ($row['go_live'] == 0): ?>
                                                <a href="#" data-toggle="tooltip" title="Go live" class="go_live" id="go_live-<?=$row['id']?>">
                                                    <span class="glyphicon glyphicon-eye-close"></span>
                                                </a>
                                            <?php else: ?>
                                                <?php if ($can_view_secret): ?>
                                                    <a href="#" data-toggle="tooltip" title="View Secret Information" class="view_secret" id="view_secret-<?=$row['id']?>" >
                                                        <span class="glyphicon glyphicon-eye-open"></span>
                                                    </a>
                                                <?php endif;?>
                                                <a href="#" data-toggle="tooltip" title="Edit Secret Information" class="edit_secret" id="edit_secret-<?=$row['id']?>" >
                                                    <span class="glyphicon glyphicon-lock"></span>
                                                </a>
                                            <?php endif;?>
                                            <a href="#" data-toggle="tooltip" title="<?=lang('sys.gd23');?>" class="edit-row" id="edit_row-<?=$row['id']?>" >
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>
                                            <?php if ($row['status'] == 1): ?>
                                                <a href="#" data-toggle="tooltip" title="Deactivate Payment API" class="disable_row" id="disable_row-<?=$row['id']?>" >
                                                     <span class="glyphicon glyphicon-remove-circle"></span>
                                                </a>
                                            <?php else: ?>
                                                <a href="#" data-toggle="tooltip" title="Activate Payment API" class="able_row" id="able_row-<?=$row['id']?>">
                                                    <span class="glyphicon glyphicon-ok-sign primary"></span>
                                                </a>
                                            <?php endif;?>
                                        </td>
                                        <td><?=$row['id']?></td>
                                        <td><?=($row['system_name'] != "") ? htmlspecialchars($row['system_name']) : "-"?></td>
                                        <td>
                                            <?php if ($row['status'] == 1): ?>
                                                <span class="glyphicon glyphicon-ok text-success"></span>
                                            <?php else: ?>
                                                <span class="glyphicon glyphicon-remove text-danger"></span>
                                            <?php endif;?>
                                        </td>
                                        <td><?=($row['note'] != "") ? $row['note'] : "-"?></td>
                                        <td><?=($row['last_sync_datetime'] == "" || $row['last_sync_datetime'] == "0000-00-00 00:00:00") ? "-" : htmlspecialchars($row['last_sync_datetime'])?></td>
                                        <td><?=($row['last_sync_id'] != "") ? htmlspecialchars($row['last_sync_id']) : "-"?></td>
                                        <td><?=($row['last_sync_details'] != "") ? htmlspecialchars($row['last_sync_details']) : "-"?></td>
                                        <td><?=($row['system_type'] == 2) ? lang('sys.payment.api') : "-"?></td>
                                        <td><?=($row['live_url'] != "") ? htmlspecialchars($row['live_url']) : "-"?></td>
                                        <td><?=($row['sandbox_url'] != "") ? htmlspecialchars($row['sandbox_url']) : "-"?></td>
                                        <td><?=($row['live_key'] != "") ? htmlspecialchars($row['live_key']) : "-"?></td>
                                        <td><?=($row['live_secret'] != "") ? htmlspecialchars($row['live_secret']) : "-"?></td>
                                        <td><?=($row['sandbox_key'] != "") ? htmlspecialchars($row['sandbox_key']) : "-"?></td>
                                        <td><?=($row['sandbox_secret'] != "") ? htmlspecialchars($row['sandbox_secret']) : "-"?></td>
                                        <td>
                                            <?php if ($row['live_mode'] == 1): ?>
                                                <span class="glyphicon glyphicon-ok text-success"></span>
                                            <?php else: ?>
                                                <span class="glyphicon glyphicon-remove text-danger"></span>
                                            <?php endif;?>
                                        </td>
                                        <td><?=($row['second_url'] != "") ? htmlspecialchars($row['second_url']) : "-"?></td>
                                        <td><?=($row['sandbox_account'] != "") ? htmlspecialchars($row['sandbox_account']) : "-"?></td>
                                        <td><?=($row['live_account'] != "") ? htmlspecialchars($row['live_account']) : "-"?></td>
                                        <td><?=($row['system_code'] != "") ? htmlspecialchars($row['system_code']) : "-"?></td>
                                        <td><?=($row['class_name'] != "") ? htmlspecialchars($row['class_name']) : "-"?></td>
                                        <td><?=($row['local_path'] != "") ? htmlspecialchars($row['local_path']) : "-"?></td>
                                        <td><?=($row['manager'] != "") ? htmlspecialchars($row['manager']) : "-"?></td>
                                        <td style="max-width: 300px;"><?=($row['extra_info']) ? "<pre style='word-break: normal; font-size: 0.875em'><code class=\"JSON\">" . htmlspecialchars($row['extra_info']) . "</code></pre>" : "-"?></td>
                                        <td style="max-width: 300px;"><?=($row['sandbox_extra_info']) ? "<pre style='word-break: normal; font-size: 0.875em'><code class=\"JSON\">" . htmlspecialchars($row['sandbox_extra_info']) . "</code></pre>" : "-"?></td>
                                        <td><?=($row['created_on'] == null) ? lang('N/A') : $row['created_on']?></td>
                                    </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="panel-footer"></div>
        </div>
    </div>

    <!---------------FORM start---------------->
    <div class="col-md-5" id="add-edit-form" style="display: none;">
        <div class="panel panel-info <?=$this->utils->getConfig('use_new_sbe_color') ? 'panel-primary' : ''?>">
            <div class="panel-heading <?=$this->utils->getConfig('use_new_sbe_color') ? 'custom-ph' : ''?>">
                <h4 class="panel-title pull-left" >
                    <i class="icon-pencil"></i> <span id="add-edit-panel-title"></span>
                </h4>
                <a href="#close" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info btn-xs' : 'btn-primary btn-sm'?>" id="closeForm" >
                    <span class="glyphicon glyphicon-remove"></span>
                </a>
                <div class="clearfix"></div>
            </div>
            <div class="panel panel-body" id="details_panel_body">
                <p class="bg-warning" id="error-msg" style="padding:10px;display:none;color:#D9534F"></p>
                <form method="post" role="form" >
                    <div class="form-group">
                        <label for="system_name"><?=lang('sys.pay.systemid');?></label>
                        <input type="hidden" id="status" name="status" />
                        <input type="hidden" id="gaId" name="gaId" />
                        <select id="newId" name="newId" class="form-control">
                            <?php foreach ($api_types as $api_type_name => $api_type): ?>
                                <optgroup label="<?=$api_type_name?>" id="<?=$api_type['id']?>">
                                    <?php foreach ($api_type['list'] as $key => $value): ?>
                                        <option value="<?=$value?>"><?=$key?></option>
                                    <?php endforeach?>
                                </optgroup>
                            <?php endforeach?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="system_name"><?=lang('sys.ga.systemname');?></label>
                        <input type="text" value="" class="form-control" id="system_name" name="system_name" data-tool="ddd" required/>
                    </div>
                    <div class="form-group">
                        <label for="note"><?=lang('sys.ga.note');?></label>
                        <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="last_sync_datetime"><?=lang('sys.ga.lastsyncdt');?></label>
                        <input type="text" id="last_sync_datetime" name="last_sync_datetime"  data-time="true" value="" class="form-control input-sm dateInput" />
                    </div>
                    <div class="form-group">
                        <label for="last_sync_id"><?=lang('sys.ga.lastsyncid');?></label>
                        <input type="text" id="last_sync_id" name="last_sync_id" value="" class="form-control" />
                    </div>

                    <div class="form-group">
                        <label for="last_sync_details"><?=lang('sys.ga.lastsyncdet');?></label>
                        <textarea class="form-control" id="last_sync_details" name="last_sync_details"rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="system_type"><?=lang('sys.ga.systemtype');?></label>
                        <select id="system_type"  name="system_type" class="form-control input-sm"></select>
                    </div>
                    <div class="form-group">
                        <label for="category"><?=lang('sys.ga.category');?></label>
                        <input type="text"  id="category"  name="category"   value=""   class="form-control input-sm" />
                    </div>
                    <div class="form-group">
                        <label for="amount_float"><?=lang('sys.ga.amount_float');?></label>
                        <select id="amount_float"  name="amount_float" class="form-control input-sm">
                            <option value="0">0(Integer)</option>
                            <option value="1">0.1</option>
                            <option value="2" selected="selected">0.01</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="live_url"><?=lang('sys.ga.liveurl');?></label>
                        <input type="text"  id="live_url"  name="live_url"   value=""   class="form-control input-sm" />
                    </div>
                    <div class="form-group">
                        <label for="sandbox_url"><?=lang('sys.ga.sandboxurl');?></label>
                        <input type="text"  id="sandbox_url"  name="sandbox_url"    value=""   class="form-control input-sm" />
                    </div>
                    <div class="form-group">
                        <label for="live_key"><?=lang('sys.ga.livekey');?></label>
                        <textarea class="form-control" id="live_key"  name="live_key" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="live_secret"><?=lang('sys.ga.livesecret');?></label>
                        <textarea class="form-control" id="live_secret"  name="live_secret"  rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="sandbox_key"><?=lang('sys.ga.sandboxkey');?></label>
                        <textarea class="form-control" id="sandbox_key"  name="sandbox_key"  rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="sandbox_secret"><?=lang('sys.ga.sandboxsecret');?></label>
                        <textarea class="form-control" id="sandbox_secret"  name="sandbox_secret" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="live_mode"><?=lang('sys.ga.livemode');?></label>
                        <input type="checkbox"  id="live_mode"  name="live_mode"  value="1" style="width: auto; height: auto" class="form-control input-sm" />
                    </div>
                    <div class="form-group">
                        <label for="second_url"><?=lang('sys.ga.secondurl');?></label>
                        <input type="text"  id="second_url"  name="second_url"   value=""   class="form-control input-sm" />
                    </div>
                    <div class="form-group">
                        <label for="sandbox_account"><?=lang('sys.ga.sandboxacct');?></label>
                        <input type="text"  id="sandbox_account"  name="sandbox_account"   value=""   class="form-control input-sm" />
                    </div>
                    <div class="form-group">
                        <label for="live_account"><?=lang('sys.ga.liveacct');?></label>
                        <input type="text"  id="live_account"  name="live_account"   value=""   class="form-control input-sm" />
                    </div>
                    <div class="form-group">
                        <label for="system_code"><?=lang('sys.ga.systemcode');?></label>
                        <input type="text"  id="system_code"  name="system_code"   value=""   class="form-control input-sm" />
                    </div>
                    <div class="form-group">
                        <label for="class_name"><?=lang('sys.ga.classname');?></label>
                        <input type="text"  id="class_name"  name="class_name"   value=""   class="form-control input-sm" />
                    </div>
                    <div class="form-group">
                        <label for="local_path"><?=lang('sys.ga.localpath');?></label>
                        <input type="text"  id="local_path"  name="local_path"   value=""   class="form-control input-sm" />
                    </div>
                    <div class="form-group">
                        <label for="manager"><?=lang('sys.ga.manager');?></label>
                        <input type="text"  id="manager"  name="manager"   value=""   class="form-control input-sm" />
                    </div>
                    <div class="form-group">
                        <label for="extra_info"><?=lang('sys.pay.extrainfo');?></label>
                        <pre class="form-control" id="extra_info" name="extra_info" style="height: 150px"></pre>
                    </div>
                    <div class="form-group">
                        <label for="sandbox_extra_info"><?=lang('sys.pay.sandboxextrainfo');?></label>
                        <pre class="form-control" id="sandbox_extra_info" name="sandbox_extra_info" style="height: 150px"></pre>
                    </div>
                    <div class="form-group">
                        <label for="allow_deposit_withdraw"><?=lang('This API can be used for')?>:</label>
                        <select id="allow_deposit_withdraw" name="allow_deposit_withdraw">
                            <option value="1" selected><?=lang('Deposit')?></option>
                            <option value="2"><?=lang('Withdraw')?></option>
                            <option value="3"><?=lang('Deposit and Withdraw')?></option>
                        </select>
                    </div>

                    <!----------NOTE: BUTTON TITLE WILL BE UPDATE THROUG JAVASCTRIPT---------------->
                    <button id="add-update-button"  type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>"></button>
                </form>
            </div>
        </div>
    </div>
    <!---------------FORM end---------------->
</div>

<div id="conf-modal" class="modal fade bs-example-modal-md" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel"><?=lang('sys.pay.conf.title');?></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="help-block" id="conf-msg">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?>" id="cancel-action"data-dismiss="modal"><?=lang('pay.bt.cancel');?></button>
                <button type="button" id="confirm-action" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>"><?=lang('pay.bt.yes');?></button>
            </div>
        </div>
    </div>
</div>

<div id="view-secret-modal" class="modal fade bs-example-modal-md" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="view-secret-header"><?=lang('View Secret Information');?></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="input-group" id="otp-aria">
                            <input type="hidden" name="api_id" id="view-secret-api-id" />
                            <input type="text" id="otp_code" name="otp_code" class="form-control" placeholder="<?=lang('Enter OTP code to view the content')?>" />
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" id="submit-otp-btn"><?=lang('Submit')?></button>
                            </span>
                        </div>
                        <div id="view-secret-content"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('Done');?></button>
            </div>
        </div>
    </div>
</div>

<div id="edit-secret-modal" class="modal fade bs-example-modal-md" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="edit-secret-header"><?=lang('Edit Secret Information');?></h3>
            </div>
            <form method="POST" role="form" action="/payment_api/updateSecretInfo">
	            <div class="modal-body">
	                <div class="row">
	                    <div class="col-md-12">
                            <input type="hidden" name="api_id" id="edit-secret-api-id">
                            <div id="edit-secret-form"></div>
                            <br>
	                    </div>
	                </div>
	            </div>
	            <div class="modal-footer">
	                <button type="submit" class="btn btn-info"><?=lang("Save")?></button>
	                <button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('Cancel');?></button>
	            </div>
            </form>
        </div>
    </div>
</div>

<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
    <form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
        <input name='json_search' type="hidden">
    </form>
<?php }?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#viewPaymentApi").addClass('active');

        var dataTable = $('#my_table').DataTable({
            autoWidth: false,
            <?php if($this->utils->isEnabledFeature('column_visibility_report')){ ?>
                stateSave: true,
            <?php } else { ?>
                stateSave: false,
            <?php } ?>
            dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ],
                    className: '<?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : ''?>',
                },
                <?php if( $this->permissions->checkPermissions('export_payment_api') ){ ?>
                    {
                        text: "<?php echo lang('CSV Export'); ?>",
                        className:'btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>',
                        action: function ( e, dt, node, config ) {
                            var d = {};
                            $.post(site_url('/export_data/paymentAPI'), d, function(data){

                                //create iframe and set link
                                if(data && data.success){
                                    $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                                }else{
                                    alert('export failed');
                                }
                            });
                        }
                    }
                <?php } ?>
            ],
            columnDefs: [
                { sortable: false, targets: [ 0 ] },
                { visible: false, targets: [4,5,6,7,8,10,11,12,13,14,16,17,21,22,24,25] }
            ],
            order: [[ 1, 'asc' ]],
            drawCallback: function () {
                if ( dataTable.rows( { selected: true } ).indexes().length === 0 ) {
                    dataTable.buttons().disable();
                }
                else {
                    dataTable.buttons().enable();
                }
            }
        });

        // Limit the options of the ID dropdown in the new API dialog
        $('#system_type').change(function() {
            var system_type_id = $(this).val();
            $('#newId optgroup').hide();
            $('#newId optgroup#' + system_type_id).show();
        });

        // Init syntax highlight for JSON string in extra_info
        hljs.initHighlightingOnLoad();

        // Init ACE editor for JSON
        var extraInfoEditor = ace.edit("extra_info");
            extraInfoEditor.setTheme("ace/theme/tomorrow");
            extraInfoEditor.session.setMode("ace/mode/json");
        var sandboxExtraInfoEditor = ace.edit("sandbox_extra_info");
            sandboxExtraInfoEditor.setTheme("ace/theme/tomorrow");
            sandboxExtraInfoEditor.session.setMode("ace/mode/json");
        var GET_EDIT_ROW_URL = '<?php echo site_url('payment_api/editPaymentApi') ?>/',
            ADD_ROW_URL = '<?php echo site_url('payment_api/addPaymentApi') ?>/',
            UPDATE_ROW_URL = '<?php echo site_url('payment_api/updatePaymentApi') ?>/',
            DELETE_ROW_URL = '<?php echo site_url('payment_api/deletePaymentApi') ?>/',
            DISABLE_ABLE_ROW_URL = '<?php echo site_url('payment_api/disableAblePaymentApi') ?>/',
            REFRESH_PAGE_URL = '<?php echo site_url('payment_api/viewPaymentApi') ?>/',
            GET_SYSTEM_TYPES_URL = '<?php echo site_url('payment_api/getSystemTypes') ?>/';
            PRELOAD_API_URL = '<?php echo site_url('game_api/getApiDetail') ?>/';
            GET_DECRYPTED_SECRECT_URL = '<?php echo site_url('payment_api/getDecryptedSecretInfo') ?>/';
            EDIT_SECRECT_URL = '<?php echo site_url('payment_api/editSecretInfo') ?>/';
            GOLIVE_URL = '<?php echo site_url('payment_api/encryptPaymentApi/') ?>/';

        var currentMode,
            forDeleteIds = Array(),
            addUpdateUrl,
            currAbleDisableId,
            goLiveId;

        var LANG = {
            ADD_PANEL_TITLE : "<?=lang('sys.pay.add.paneltitle');?>",
            EDIT_PANEL_TITLE : "<?=lang('sys.pay.edit.paneltitle');?>",
            ADD_BUTTON_TITLE : "<i class='fa fa-check'></i> <?=lang('sys.pay.add.button');?>",
            UPDATE_BUTTON_TITLE : "<i class='fa fa-check'></i> <?=lang('sys.pay.update.button');?>",
            DELETE_CONFIRM_MESSAGE : "<?=lang('sys.pay.conf.del.msg');?>",
            UPDATE_CONFIRM_MESSAGE :"<?=lang('sys.pay.conf.update.msg');?>",
            ADD_CONFIRM_MESSAGE :"<?=lang('sys.pay.conf.add.msg');?>",
            DISABLE_CONFIRM_MESSAGE :"<?=lang('sys.pay.conf.disable.msg');?>",
            ABLE_CONFIRM_MESSAGE:"<?=lang('sys.pay.conf.able.msg');?>",
            GOLIVE_CONFIRM_MESSAGE:"<?=lang('Are you sure you finish development?');?>",
            EDIT : "<?=lang('sys.pay.edit.buttontitle');?>",
            EDIT_COLUMN : "<?=lang('pay.bt.edit.column');?>",
            DELETE_ITEMS : "<?=lang('sys.pay.delete.items');?>",
            ADD_GAME_DESC : "<?=lang('sys.pay.add.gameapi');?>"
        };

        var formatJSON = function(jsonEditor) {
            var jsonStr = jsonEditor.getValue();

            try{
                var jsonObj = JSON.parse(jsonStr);
                jsonStr = JSON.stringify(jsonObj, null, 4);
            } catch(e) {}

            jsonEditor.setValue(jsonStr);
        }

        var preloadApiDetail = function(){
            // preload only when adding new API
            if(currentMode != 'add'){
                return;
            }
            $.ajax({
                url : PRELOAD_API_URL + $(this).val(),
                type : 'GET',
                dataType : "json",
                cache : false,
            }).done(function (data) {
                $("#system_name").val(data.system_name);
                $("#note").val(data.note);
                $("#last_sync_datetime").val(data.last_sync_datetime);
                $("#last_sync_id").val(data.last_sync_id);
                $("#last_sync_details").val(data.last_sync_details);
                $("#system_type").val(data.system_type);
                $('#category').val(data.category);
                $('#amount_float').val(data.amount_float);
                $("#live_url").val(data.live_url);
                $("#sandbox_url").val(data.sandbox_url);
                $("#live_key").val(data.live_key);
                $("#live_secret").val(data.live_secret);
                $("#sandbox_key").val(data.sandbox_key);
                $("#sandbox_secret").val(data.sandbox_secret);
                $("#live_mode").prop('checked', data.live_mode ? 1 : 0);
                $("#second_url").val(data.second_url);
                $("#sandbox_account").val(data.sandbox_account);
                $("#live_account").val(data.live_account);
                $("#system_code").val(data.system_code);
                $("#status").val(data.status);
                $("#class_name").val(data.class_name);
                $("#local_path").val(data.local_path);
                $("#manager").val(data.manager);
                $("#game_platform_rate").val(data.game_platform_rate);

                extraInfoEditor.setValue(data.extra_info);
                sandboxExtraInfoEditor.setValue(data.sandbox_extra_info);

                formatJSON(extraInfoEditor);
                formatJSON(sandboxExtraInfoEditor);

                $("#allow_deposit_withdraw").val(data.allow_deposit_withdraw);
            });
        };

        //Confirmation Go Live
        $(".go_live").click(function () {
            var id = $(this).attr("id").split('-')[1];
            goLiveId = id;
            if(currentMode != "edit" && currentMode != "add"){
                currentMode = "goLive";
                confirmationMessage("goLive");
            }
        });

        $(".view_secret").on('click', function () {
            var id = $(this).attr("id").split('-')[1];
            $('#otp-aria').show();
            $('#otp_code').val('');
            $('#view-secret-content').hide();

            $('#view-secret-modal').modal('show');
            $('#view-secret-api-id').val(id);
        });

        $(".edit_secret").on('click', function () {
            var edit_secret_id = $(this).attr("id").split('-')[1];
            $('#edit-secret-api-id').val(edit_secret_id);

            $.ajax({
                url : EDIT_SECRECT_URL,
                type : 'POST',
                data : { id : edit_secret_id },
                dataType : "json",
                cache : false,
            }).done(function (data) {
                var display = '';
                if (data.status == "success") {
                    if (typeof data.secret.general !== 'undefined') {
                        $.each(data.secret.general, function(index, value){
                            display += '<label class="control-label">' + index + '</label><textarea name="' + index + '" class="form-control">' +value +'</textarea>';
                        })
                    }
                    if (typeof data.secret.extra_info !== 'undefined') {
                        $.each(data.secret.extra_info, function(index, value){
                            display += '<label class="control-label">[Extra Info] ' + index + '</label><textarea name="extra_info-' + index + '" class="form-control">' +value +'</textarea>';
                        })
                    }
                    if (typeof data.secret.sandbox_extra_info !== 'undefined') {
                        $.each(data.secret.sandbox_extra_info, function(index, value){
                            display += '<label class="control-label">[Sandbox Extra Info] ' + index + '</label><textarea name="sandbox_extra_info-' + index + '" class="form-control">' +value +'</textarea>';
                        })
                    }
                } else if (data.status == "error") {
                    display = '<div class="help-block">'+data.msg+'</div>';
                } else if (data.status == "failed") {
                    display = '<div class="help-block">'+data.msg+'</div>';
                }

                $('#edit-secret-form').html(display);
                $('#edit-secret-content').show();
            }).fail(function (jqXHR, textStatus) {
                window.location.href = REFRESH_PAGE_URL;
            });


            $('#edit-secret-modal').modal('show');
        });

        $("#submit-otp-btn").on('click', function () {
            var view_id = $('#view-secret-api-id').val();
            var otp_code = $('#otp_code').val();

            var data = {
                id : view_id,
                code: otp_code
            };

            $.ajax({
                url : GET_DECRYPTED_SECRECT_URL,
                type : 'POST',
                data : data,
                dataType : "json",
                cache : false,
            }).done(function (data) {
                $('#otp_code').val('');
                var display = '';
                if (data.status == "success") {
                    $('#otp-aria').hide();
                    if (typeof data.secret.general !== 'undefined') {
                        $.each(data.secret.general, function(index, value){
                            display += '<label class="control-label">' + index + '</label><textarea class="form-control" rows="1" readonly>' +value +'</textarea>';
                        })
                    }
                    if (typeof data.secret.extra_info !== 'undefined') {
                        $.each(data.secret.extra_info, function(index, value){
                            display += '<label class="control-label">[Extra Info] ' + index + '</label><textarea class="form-control" rows="1" readonly>' +value +'</textarea>';
                        })
                    }
                    if (typeof data.secret.sandbox_extra_info !== 'undefined') {
                        $.each(data.secret.sandbox_extra_info, function(index, value){
                            display += '<label class="control-label">[Sandbox Extra Info] ' + index + '</label><textarea class="form-control" rows="1" readonly>' +value +'</textarea>';
                        })
                    }
                } else if (data.status == "error") {
                    display = '<small class="text-danger">'+data.msg+'</small>';
                } else if (data.status == "failed") {
                    display = '<small class="text-danger">'+data.msg+'</small>';
                }

                $('#view-secret-content').html(display);
                $('#view-secret-content').show();
            }).fail(function (jqXHR, textStatus) {
                window.location.href = REFRESH_PAGE_URL;
            });
        });

        $("#newId").change(preloadApiDetail);

        /*View add form*/
        $("#add-row").on('click', function () {
            currentMode = 'add';
            getAllSystemTypes();
            resetAllFields();
            disableDeleteButton();
            resetTableCheckoxes();
            addPanelNamesAndButtons("add");
            addEditFormPanelOpen();
            fixHtml5Datepicker();
        });

        /*Open Row Details */
        $(".edit-row").on('click', function () {
            currentMode = 'edit';
            var aTagId = $(this).attr('id'),
            id = aTagId.split('-')[1];
            editRow(id);
            disableDeleteButton();
            resetTableCheckoxes();
            addPanelNamesAndButtons("edit");
            addEditFormPanelOpen();
            fixHtml5Datepicker();
        });

        //Selections for delete
        $(".delete_id_selector").click(function () {
            var id = $(this).attr("id").split('-')[1];
            currentMode = "delete";
            forDeleteIds.push(id);
        });

        //Cancels confirmation
        $("#cancel-action").click(function () {
            $("#add-update-button").prop('disabled', false);
            resetTableCheckoxes();
        });

        //Confirmation Delete
        $("#delete-items").click(function () {
            confirmationMessage("delete");
        });

        //Confirmation Disable
        $(".disable_row").click(function () {
            var id = $(this).attr("id").split('-')[1];
            currAbleDisableId = id;
            if(currentMode != "edit" && currentMode != "add"){
                currentMode = "disable";
                confirmationMessage("disable");;
            }
        });

        //Confirmation Able
        $(".able_row").click(function () {
            var id = $(this).attr("id").split('-')[1];
            currAbleDisableId = id;
            if(currentMode != "edit" && currentMode != "add"){
                currentMode = "able";
                confirmationMessage("able");
            }
        });

        /*Close Form */
        $("#closeForm").on('click', function () {
            closeForm();
        });

        /*Submits the For (Add or Update)*/
        $("#add-update-button").on('click', function () {
            $(this).prop('disabled', true);
            if (currentMode === "add") {
                addUpdateUrl = ADD_ROW_URL;
                confirmationMessage("add");
            }
            if (currentMode === "edit") {
                addUpdateUrl = UPDATE_ROW_URL;
                confirmationMessage("update");
            }
            return false;
        });

        //Agreed to Confirmation
        $("#confirm-action").click(function () {
            if (currentMode === "delete") {
                deleteRows();
            }
            if (currentMode === "edit" || currentMode === "add" ) {
                addUpdateRow();
            }
            if (currentMode === "disable" || currentMode === "able" ) {
                ableDisableRow();
            }
            if (currentMode === "goLive") {
                window.location.href = GOLIVE_URL + "/" + goLiveId;
            }
        });

        function confirmationMessage(action){
            switch (action) {
                case "add":
                    if (currentMode == "add") {
                        showConfModal();
                        $('#conf-msg').html(LANG.ADD_CONFIRM_MESSAGE )
                    }
                    break;
                case "update":
                    if (currentMode == "edit") {
                        showConfModal();
                        $('#conf-msg').html(LANG.UPDATE_CONFIRM_MESSAGE );
                    }
                    break;
                case "delete":
                    if (currentMode == "delete") {
                        showConfModal();
                        var count = forDeleteIds.length;
                        $('#conf-msg').html(LANG.DELETE_CONFIRM_MESSAGE + "<b>" + count + " items ?</b>");
                    }
                    break;
                case "disable":
                    if (currentMode == "disable") {
                        showConfModal();
                        $('#conf-msg').html(LANG.DISABLE_CONFIRM_MESSAGE);
                    }
                    break;
                case "able":
                    if (currentMode == "able") {
                        showConfModal();
                        $('#conf-msg').html(LANG.ABLE_CONFIRM_MESSAGE);
                    }
                    break;
                case "goLive" :
                    if (currentMode == "goLive") {
                        showConfModal();
                        $('#conf-msg').html(LANG.GOLIVE_CONFIRM_MESSAGE);
                    }
                    break;
            }
        }

        function hideConfModal(){
             $("html, body").animate({ scrollTop: 0 }, "slow");
             $('#conf-modal').modal('hide');
         }

        function showConfModal(){
            $('#conf-modal').modal('show');
        }

        function ableDisableRow(){
            var status;
            if (currentMode === "disable") {
                status = "0" ;
            }
            if (currentMode === "able") {
                status = "1" ;
            }

            var data = {
                id : currAbleDisableId,
                status:status
            };

            $.ajax({
                url : DISABLE_ABLE_ROW_URL,
                type : 'POST',
                data : data,
                dataType : "json",
                cache : false,
            }).done(function (data) {
                if (data.status == "success") {
                    window.location.href = REFRESH_PAGE_URL;
                }
                if (data.status == "error") {
                    window.location.href = REFRESH_PAGE_URL;
                }
                if (data.status == "failed") {
                    window.location.href = REFRESH_PAGE_URL;
                }
            }).fail(function (jqXHR, textStatus) {
                window.location.href = REFRESH_PAGE_URL;
            });
        }

        function deleteRows(){
            var data = {
                forDeletes : forDeleteIds
            };

            $.ajax({
                url : DELETE_ROW_URL,
                type : 'POST',
                data : data,
                dataType : "json",
            }).done(function (data) {
                if (data.status == "success") {
                    window.location.href = REFRESH_PAGE_URL;
                } else {
                    window.location.href = REFRESH_PAGE_URL;
                }
            }).fail(function (jqXHR, textStatus) {
                window.location.href = REFRESH_PAGE_URL;
            });
        }

        function addUpdateRow(){
            var data = {
                id : $("#gaId").val(),
                new_id : $("#newId").val(),
                system_name : $("#system_name").val(),
                note : $("#note").val(),
                last_sync_datetime : $("#last_sync_datetime").val(),
                last_sync_id : $("#last_sync_id").val(),
                last_sync_details : $("#last_sync_details").val(),
                system_type : $("#system_type").val(),
                category : $('#category').val(),
                amount_float : $('#amount_float').val(),
                live_url : $("#live_url").val(),
                sandbox_url : $("#sandbox_url").val(),
                live_key : $("#live_key").val(),
                live_secret : $("#live_secret").val(),
                sandbox_key : $("#sandbox_key").val(),
                sandbox_secret : $("#sandbox_secret").val(),
                live_mode : $("#live_mode").prop('checked') ? 1 : 0,
                second_url : $("#second_url").val(),
                sandbox_account : $("#sandbox_account").val(),
                live_account : $("#live_account").val(),
                system_code : $("#system_code").val(),
                status : $("#status").val(),
                class_name : $("#class_name").val(),
                local_path : $("#local_path").val(),
                manager : $("#manager").val(),
                extra_info : extraInfoEditor.getValue(),
                sandbox_extra_info : sandboxExtraInfoEditor.getValue(),
                allow_deposit_withdraw : $("#allow_deposit_withdraw").val()
            };

            $.ajax({
                url : addUpdateUrl,
                type : 'POST',
                data : data,
                dataType : "json",
                cache : false,
            }).done(function (data) {
                if (data.status == "success") {
                     window.location.href = REFRESH_PAGE_URL;
                }
                if (data.status == "error") {
                    $("#add-update-button").prop('disabled', false);
                    hideConfModal();
                    showValidationError(data.msg);
                }
                if (data.status == "failed") {
                    window.location.href = REFRESH_PAGE_URL;
                }
            }).fail(function (jqXHR, textStatus) {
                 window.location.href = REFRESH_PAGE_URL;
            });
        }

        function deleteRows(){
            var data = {
                forDeletes : forDeleteIds
            };
            $.ajax({
                url : DELETE_ROW_URL,
                type : 'POST',
                data : data,
                dataType : "json",
            }).done(function (data) {
                if (data.status == "success") {
                    window.location.href = REFRESH_PAGE_URL;
                } else {
                    window.location.href = REFRESH_PAGE_URL;
                }
            }).fail(function (jqXHR, textStatus) {
                window.location.href = REFRESH_PAGE_URL;
            });
        }

        /*Gets SytemTypes*/
        function getAllSystemTypes() {
            $.ajax({
                url : GET_SYSTEM_TYPES_URL,
                type : 'GET',
                dataType : "json",
            }).done(function (data) {
                removeOptions();
                var sytemTypes= data.data.sytemTypes,
                sytemTypesLength = sytemTypes.length;
                for (var i = 0; i < sytemTypesLength; i++) {
                    if(i==1){
                        $('#system_type').append('<option value="' + sytemTypes[i].id+ '" selected>' + sytemTypes[i].system_type+ '</option>');
                    }else{
                        $('#system_type').append('<option value="' + sytemTypes[i].id+ '">' + sytemTypes[i].system_type+ '</option>');
                    }
                }
                $('#system_type').trigger('change');
            }).fail(function (jqXHR, textStatus) {
                window.location.href = REFRESH_PAGE_URL;
            });
        }

        function resetAllFields(){
            $("#gaId").val("");
            $("#newId").val(""),
            $("#system_name").val("");
            $("#note").val("");
            $("#last_sync_datetime").val("");
            $("#last_sync_id").val("");
            $("#last_sync_details").val("");
            $("#system_type").val("");
            $('#category').val("");
            $('#amount_float').val(2);
            $("#live_url").val("");
            $("#sandbox_url").val("");
            $("#live_key").val("");
            $("#live_secret").val("");
            $("#sandbox_key").val("");
            $("#sandbox_secret").val("");
            $("#live_mode").prop('checked', false);
            $("#second_url").val("");
            $("#sandbox_account").val("");
            $("#live_account").val("");
            $("#system_code").val("");
            $("#status").val("");
            $("#class_name").val("");
            $("#local_path").val("");
            $("#manager").val("");
            extraInfoEditor.setValue("");
        }

        /*Gets Payment Description row for editing*/
        function editRow(rowId) {
            $.ajax({
                url : GET_EDIT_ROW_URL + rowId,
                type : 'GET',
                dataType : "json",
            }).done(function (data) {
                removeOptions();

                var sytemTypes= data.data.sytemTypes,
                    paymentApi = data.data.paymentApi,
                    sytemTypesLength = sytemTypes.length;
                for (var i = 0; i < sytemTypesLength; i++) {
                    if(paymentApi.system_type == sytemTypes[i].id){
                        $('#system_type').append('<option value="' + sytemTypes[i].id+ '" selected>' + sytemTypes[i].system_type+ '</option>');
                    }else{
                        $('#system_type').append('<option value="' + sytemTypes[i].id+ '" >' + sytemTypes[i].system_type+ '</option>');
                    }
                }

                $("#gaId").val(paymentApi.id);
                $("#newId").val(paymentApi.id);
                $("#system_name").val(paymentApi.system_name);
                $("#note").val(paymentApi.note);
                $("#last_sync_datetime").val(paymentApi.last_sync_datetime);
                $("#last_sync_id").val(paymentApi.last_sync_id);
                $("#last_sync_details").val(paymentApi.last_sync_details);
                $("#system_type").val(paymentApi.system_type);
                $('#category').val(paymentApi.category);
                $('#amount_float').val(paymentApi.amount_float);
                $("#live_url").val(paymentApi.live_url);
                $("#sandbox_url").val(paymentApi.sandbox_url);
                $("#live_key").val(paymentApi.live_key);
                $("#live_secret").val(paymentApi.live_secret);
                $("#sandbox_key").val(paymentApi.sandbox_key);
                $("#sandbox_secret").val(paymentApi.sandbox_secret);
                $("#live_mode").prop('checked', paymentApi.live_mode == 1);
                $("#second_url").val(paymentApi.second_url);
                $("#sandbox_account").val(paymentApi.sandbox_account);
                $("#live_account").val(paymentApi.live_account);
                $("#system_code").val(paymentApi.system_code);
                $("#status").val(paymentApi.status);
                $("#class_name").val(paymentApi.class_name);
                $("#local_path").val(paymentApi.local_path);
                $("#manager").val(paymentApi.manager);

                if(paymentApi.go_live == 1) {
                    $.each(data.data.secret_list, function(index, value){
                        $("#" + value).attr('readonly', true);
                    })
                } else {
                    $("#details_panel_body textarea").attr('readonly', false);
                    $("#details_panel_body input").attr('readonly', false);
                }

                extraInfoEditor.setValue(paymentApi.extra_info);
                sandboxExtraInfoEditor.setValue(paymentApi.sandbox_extra_info);
                formatJSON(extraInfoEditor);
                formatJSON(sandboxExtraInfoEditor);
                $("#allow_deposit_withdraw").val(paymentApi.allow_deposit_withdraw);
            }).fail(function (jqXHR, textStatus) {
                window.location.href = REFRESH_PAGE_URL;
            });
        }

        function showValidationError(msg) {
            $("#error-msg").stop(true, true).fadeIn().html(msg).fadeOut(10000, function () {
                $(this).html("");
            });
        }

        //Note: not working in css so i fixed this using javascript
        function fixHtml5Datepicker() {
            $('.ws-datetime-local').css({
                width : "50%"
            });
        }

        function removeOptions() {
            $('#system_type').html("");
        }

        function resetTableCheckoxes() {
            forDeleteIds = Array();
            $(".delete_id_selector").prop('checked', false);
        }

        function disableDeleteButton() {
            $("#delete-items").prop('disabled', true);
            $(".delete_id_selector").prop('disabled', true);
        }

        function acivateDeleteButton() {
            $("#delete-items").prop('disabled', false);
            $(".delete_id_selector").prop('disabled', false);
        }

        function addPanelNamesAndButtons(type) {
            switch (type) {
                case "add":
                $("#add-edit-panel-title").html(LANG.ADD_PANEL_TITLE);
                $("#add-update-button").html(LANG.ADD_BUTTON_TITLE);
                break;

                case "edit":
                $("#add-edit-panel-title").html(LANG.EDIT_PANEL_TITLE);
                $("#add-update-button").html(LANG.UPDATE_BUTTON_TITLE);
                break;
            }
        }

        /*Toggles panels for editing*/
        function addEditFormPanelOpen() {
            $('#toggleView').removeClass('col-md-12');
            $('#toggleView').addClass('col-md-7');

            $('#add-edit-form').show();

            if ($('#toggleView').hasClass('col-md-5')) {
                $('table#myTable td#visible').hide();
                $('table#myTable th#visible').hide();
            } else {
                $('table#myTable td#visible').show();
                $('table#myTable th#visible').show();
            }
        }

        function closeForm() {
            acivateDeleteButton();
            //reset Current Mode for Disabling row
            currentMode="";
            $('#toggleView').removeClass('col-md-7');
            $('#toggleView').addClass('col-md-12');

            if ($('#toggleView').hasClass('col-md-7')) {
                $('table#myTable td#visible').hide();
                $('table#myTable th#visible').hide();
            } else {
                $('table#myTable td#visible').show();
                $('table#myTable th#visible').show();
            }
            $('#add-edit-form').hide();
        }

        //tooltips
        $('body').tooltip({
            selector : '[data-toggle="tooltip"]',
            placement : "bottom"
        });

        $(".delete_id_selector").tooltip({
            placement : "right",
            title : LANG.EDIT,
        });

        $("#edit_column").tooltip({
            placement : "left",
            title : LANG.EDIT_COLUMN,
        });

        $("#delete-items").tooltip({
            placement : "right",
            title : LANG.DELETE_ITEMS,
        });

        $("#add-row").tooltip({
            placement : "right",
            title : LANG.ADD_GAME_DESC,
        });
    }); //end document ready.
</script>