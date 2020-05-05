
<div class="row" id="user-container">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <div class="panel-heading custom-ph">
                <h3 class="panel-title custom-pt" > <i class="icon-list"></i> <?=lang('sys.ga.paneltitle');?>
                   <!--  <a href="#edit-column" id="edit_column" role="button" data-toggle="modal" class="btn btn-sm btn-default pull-right">
                        <span class="glyphicon glyphicon-list"></span> <?=lang('sys.ga.edit.column');?>
                    </a> -->


                </h3>
            </div>

            <div class="panel-body" id="list_panel_body">
                <form  autocomplete="on" id="my_form">
                    <div class="row">
                      <div class="btn-action col-md-12">
                          <button type="button" value="" id="add-row" name="btnSubmit" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>">
                              <i class="glyphicon glyphicon-plus" style="color:white;"  data-placement="bottom" ></i>
                              <?=lang('sys.ga.add.button');?>
                          </button>&nbsp;
                          <!--<button type="button" value="" id="delete-items" name="btnSubmit" class="btn btn-danger btn-sm">
                              <i class="glyphicon glyphicon-trash" style="color:white;"  data-placement="bottom" ></i>
                              <?=lang('sys.ga.delete.gameapi');?>
                          </button>-->&nbsp;
                      </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-condensed" style="width:100%;" id="my_table" >

                            <thead>
                                <tr>
                                    <th><?=lang('sys.ga.action');?></th>
                                    <th><?=lang('sys.ga.systemid')?></th>
                                    <?php echo ($this->session->userdata('system_name_gapi')) ? "<th>" . lang('sys.ga.systemname') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('status_gapi')) ? "<th>" . lang('sys.ga.status') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('note_gapi')) ? "<th>" . lang('sys.ga.note') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('last_sync_datetime_gapi')) ? "<th>" . lang('sys.ga.lastsyncdt') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('last_sync_id_gapi')) ? "<th>" . lang('sys.ga.lastsyncid') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('last_sync_details_gapi')) ? "<th>" . lang('sys.ga.lastsyncdet') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('system_type_gapi')) ? "<th>" . lang('sys.ga.systemtype') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('live_url_gapi')) ? "<th>" . lang('sys.ga.liveurl') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('sandbox_url_gapi')) ? "<th>" . lang('sys.ga.sandboxurl') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('live_key_gapi')) ? "<th>" . lang('sys.ga.livekey') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('live_secret_gapi')) ? "<th>" . lang('sys.ga.livesecret') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('sandbox_key_gapi')) ? "<th>" . lang('sys.ga.sandboxkey') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('sandbox_secret_gapi')) ? "<th>" . lang('sys.ga.sandboxsecret') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('live_mode_gapi')) ? "<th>" . lang('sys.ga.livemode') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('second_url_gapi')) ? "<th>" . lang('sys.ga.secondurl') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('sandbox_account_gapi')) ? "<th>" . lang('sys.ga.sandboxacct') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('live_account_gapi')) ? "<th>" . lang('sys.ga.liveacct') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('system_code_gapi')) ? "<th>" . lang('sys.ga.systemcode') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('class_name_gapi')) ? "<th>" . lang('sys.ga.classname') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('local_path_gapi')) ? "<th>" . lang('sys.ga.localpath') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('manager_gapi')) ? "<th>" . lang('sys.ga.manager') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('game_platform_rate')) ? "<th>" . lang('sys.gd7') . " " . lang('sys.rate') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('extra_info')) ? "<th>" . lang('sys.ga.extrainfo') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('sandbox_extra_info')) ? "<th>" . lang('sys.ga.sandboxextrainfo') . " </th>" : '' ?>
                                    <?php echo ($this->session->userdata('created_on')) ? "<th>" . lang('pay.createdon') . " </th>" : '' ?>
                                </tr>
                            </thead>
                            <tbody>

                <?php foreach ($gameApis as $row): ?>
                    <tr class="<?=$row['maintenance_mode'] == 1 ? 'danger' : ($row['pause_sync'] == 1 ? 'warning' : '') ; ?>">
                    <td style="white-space: nowrap">
                      <input type="checkbox" title="<?=lang('sys.gd21');?>" class="delete_id_selector" id="id_selector-<?=$row['id']?>" name="id_selector"/>
                      <a href="#" data-toggle="tooltip" title="<?=lang('sys.gd23');?>" class="edit-row" id="edit_row-<?=$row['id']?>" ><span class="glyphicon glyphicon-edit"></span></a>
                      <?php if ($row['status'] == 1): ?>
                        <a href="#"  data-toggle="tooltip" title="<?php echo lang('Block');?>" class="disable_row" id="disable_row-<?=$row['id']?>" >
                         <span class="glyphicon glyphicon-remove-circle"></span>
                        </a>
                      <?php else: ?>
                        <a href="#" data-toggle="tooltip" title="<?php echo lang('Unblock'); ?>" class="able_row" id="able_row-<?=$row['id']?>">
                          <span class="glyphicon glyphicon-ok-sign primary"></span>
                        </a>
                      <?php endif;?>
                      <a href="javascript:void(0)" data-toggle="tooltip" title="<?=lang('sys.ga.synclog');?>" class="sync-log"
                        id="sync-log-<?=$row['id']?>" data-platform-id="<?=$row['id']?>">
                        <span class="glyphicon glyphicon-refresh"></span>
                      </a>
                        <?php if($this->permissions->checkPermissions('game_api_maintenance')){
                            if ($row['maintenance_mode'] == 0): ?>
                                <a href="#"  data-toggle="tooltip" title="<?=lang('Start Maintenance');?>" class="maintenance_row" id="maintenance_row-<?=$row['id']?>" >
                                    <span class="glyphicon glyphicon-cog"></span>
                                </a>
                            <?php else: ?>
                            <a href="#" data-toggle="tooltip" title="<?=lang('Finish Maintenance'); ?>" class="active_row" id="active_row-<?=$row['id']?>">
                                <span class="glyphicon glyphicon-ok-circle"></span>
                            </a>

                        <?php endif; }?>
                        <?php if($row['pause_sync'] == 0): ?>
                            <a href="#"  data-toggle="tooltip" title="<?=lang('Pause Syncing');?>" class="pause_sync_row <?=$row['maintenance_mode'] == 1 ? 'hidden' : ''; ?>" id="pause_sync_row-<?=$row['id']?>" >
                                <span class="glyphicon glyphicon-pause"></span>
                            </a>
                        <?php else: ?>
                            <a href="#" data-toggle="tooltip" title="<?=lang('Revert To Syncing'); ?>" class="revert_sync_row <?=$row['maintenance_mode'] == 1 ? 'hidden' : ''; ?>" id="revert_sync_row-<?=$row['id']?>">
                                <span class="glyphicon glyphicon-play"></span>
                            </a>
                        <?php endif; ?>
                    </td>
                      <td><?=$row['id'] != '' ? $row['id'] : '-'?></td>
                    <?php if ($this->session->userdata('system_name_gapi') == 'checked'): ?>
                      <td><?=($row['system_name'] != "") ? htmlspecialchars($row['system_name']) : "-"?></td>
                    <?php endif;?>

                    <?php if ($this->session->userdata('status_gapi') == 'checked'): ?>
                     <td>
                    <?php if ($row['status'] == 1): ?>
                        <span class="glyphicon glyphicon-ok text-success"></span>
                    <?php else: ?>
                        <span class="glyphicon glyphicon-remove text-danger"></span>
                    <?php endif;?>
                    </td>
                   <?php endif;?>

                    <?php if ($this->session->userdata('note_gapi') == 'checked'): ?>
                      <td><?=($row['note'] != "") ? htmlspecialchars($row['note']) : "-"?></td>
                    <?php endif;?>

                    <?php if ($this->session->userdata('last_sync_datetime_gapi') == 'checked'): ?>
                      <td><?=($row['last_sync_datetime'] == "" || $row['last_sync_datetime'] == "0000-00-00 00:00:00") ? "-" : $row['last_sync_datetime']?></td>
                    <?php endif;?>

                    <?php if ($this->session->userdata('last_sync_id_gapi') == 'checked'): ?>
                      <td><?=($row['last_sync_id'] != "") ? htmlspecialchars($row['last_sync_id']) : "-"?></td>
                    <?php endif;?>

                    <?php if ($this->session->userdata('last_sync_details_gapi') == 'checked'): ?>
                      <td><?=($row['last_sync_details'] != "") ? htmlspecialchars($row['last_sync_details']) : "-"?></td>
                    <?php endif;?>

                     <?php if ($this->session->userdata('system_type_gapi') == 'checked'): ?>
                     <td>
                     <?=($row['system_type'] == 1) ? lang('sys.game.api') : "-"?>
                    </td>
                   <?php endif;?>

                   <?php if ($this->session->userdata('live_url_gapi') == 'checked'): ?>
                     <td><div style="width: 150px; overflow: hidden; text-overflow:ellipsis;" title="<?=($row['live_url'] != "") ? htmlspecialchars($row['live_url']) : "-"?>">
                        <?=($row['live_url'] != "") ? htmlspecialchars($row['live_url']) : "-"?>
                      </div></td>
                   <?php endif;?>

                   <?php if ($this->session->userdata('sandbox_url_gapi') == 'checked'): ?>
                     <td><div style="width: 150px; overflow: hidden; text-overflow:ellipsis;" title="<?=($row['sandbox_url'] != "") ? htmlspecialchars($row['sandbox_url']) : "-"?>">
                        <?=($row['sandbox_url'] != "") ? htmlspecialchars($row['sandbox_url']) : "-"?>
                      </div></td>
                   <?php endif;?>

                   <?php if ($this->session->userdata('live_key_gapi') == 'checked'): ?>
                     <td><div style="width: 100px; overflow: hidden; text-overflow:ellipsis;" title="<?=($row['live_key'] != "") ? htmlspecialchars($row['live_key']) : "-"?>">
                        <?=($row['live_key'] != "") ? htmlspecialchars($row['live_key']) : "-"?>
                      </div></td>
                   <?php endif;?>

                   <?php if ($this->session->userdata('live_secret_gapi') == 'checked'): ?>
                     <td><div style="width: 100px; overflow: hidden; text-overflow:ellipsis;" title="<?=($row['live_secret'] != "") ? htmlspecialchars($row['live_secret']) : "-"?>">
                        <?=($row['live_secret'] != "") ? htmlspecialchars($row['live_secret']) : "-"?>
                      </div></td>
                   <?php endif;?>

                   <?php if ($this->session->userdata('sandbox_key_gapi') == 'checked'): ?>
                     <td><div style="width: 100px; overflow: hidden; text-overflow:ellipsis;" title="<?=($row['sandbox_key'] != "") ? htmlspecialchars($row['sandbox_key']) : "-"?>">
                        <?=($row['sandbox_key'] != "") ? htmlspecialchars($row['sandbox_key']) : "-"?>
                      </div></td>
                   <?php endif;?>
                   <?php if ($this->session->userdata('sandbox_secret_gapi') == 'checked'): ?>
                     <td><div style="width: 100px; overflow: hidden; text-overflow:ellipsis;" title="<?=($row['sandbox_secret'] != "") ? htmlspecialchars($row['sandbox_secret']) : "-"?>">
                        <?=($row['sandbox_secret'] != "") ? htmlspecialchars($row['sandbox_secret']) : "-"?>
                      </div></td>
                   <?php endif;?>

                   <?php if ($this->session->userdata('live_mode_gapi') == 'checked'): ?>
                     <td>
                        <?php if ($row['live_mode'] == 1): ?>
                            <span class="glyphicon glyphicon-ok text-success"></span>
                        <?php else: ?>
                            <span class="glyphicon glyphicon-remove text-danger"></span>
                        <?php endif;?>
                     </td>
                   <?php endif;?>

                   <?php if ($this->session->userdata('second_url_gapi') == 'checked'): ?>
                     <td><?=($row['second_url'] != "") ? htmlspecialchars($row['second_url']) : "-"?></td>
                   <?php endif;?>

                   <?php if ($this->session->userdata('sandbox_account_gapi') == 'checked'): ?>
                     <td><?=($row['sandbox_account'] != "") ? htmlspecialchars($row['sandbox_account']) : "-"?></td>
                   <?php endif;?>

                   <?php if ($this->session->userdata('live_account_gapi') == 'checked'): ?>
                     <td><?=($row['live_account'] != "") ? htmlspecialchars($row['live_account']) : "-"?></td>
                   <?php endif;?>

                   <?php if ($this->session->userdata('system_code_gapi') == 'checked'): ?>
                     <td><?=($row['system_code'] != "") ? htmlspecialchars($row['system_code']) : "-"?></td>
                   <?php endif;?>

                    <?php if ($this->session->userdata('class_name_gapi') == 'checked'): ?>
                     <td><?=($row['class_name'] != "") ? htmlspecialchars($row['class_name']) : "-"?></td>
                   <?php endif;?>

                   <?php if ($this->session->userdata('local_path_gapi') == 'checked'): ?>
                     <td><?=($row['local_path'] != "") ? htmlspecialchars($row['local_path']) : "-"?></td>
                   <?php endif;?>

                   <?php if ($this->session->userdata('manager_gapi') == 'checked'): ?>
                     <td><?=($row['manager'] != "") ? htmlspecialchars($row['manager']) : "-"?></td>
                   <?php endif;?>

                   <?php if ($this->session->userdata('game_platform_rate') == 'checked'): ?>
                     <td><?=($row['game_platform_rate'] != "") ? htmlspecialchars($row['game_platform_rate']) : "-"?></td>
                   <?php endif;?>

                   <?php if ($this->session->userdata('extra_info') == 'checked'): ?>
                     <td><?=($row['extra_info'] != "") ? "<pre style='word-break: normal; font-size: 0.875em'><code class=\"JSON\">" . htmlspecialchars($row['extra_info']) . "</code></pre>" : "-"?></td>
                   <?php endif;?>

                   <?php if ($this->session->userdata('sandbox_extra_info') == 'checked'): ?>
                     <td><?=($row['sandbox_extra_info'] != "") ? "<pre style='word-break: normal; font-size: 0.875em'><code class=\"JSON\">" . htmlspecialchars($row['sandbox_extra_info']) . "</code></pre>" : "-"?></td>
                   <?php endif;?>
                    <?php if ($this->session->userdata('created_on') == 'checked'): ?>
                     <td><?=($row['created_on'] != "") ? "<pre style='word-break: normal; font-size: 0.875em'><code class=\"JSON\">" . htmlspecialchars($row['created_on']) . "</code></pre>" : "-"?></td>
                   <?php endif;?>
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
            <h4 class="panel-title pull-left"  ><i class="icon-pencil"></i><span id="add-edit-panel-title"></span>  </h4>
            <a href="#close" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-info btn-xs' : 'btn-primary btn-sm'?>" id="closeForm" ><span class="glyphicon glyphicon-remove"></span></a>
            <div class="clearfix"></div>
        </div>
        <div class="panel panel-body" id="details_panel_body">
            <p class="bg-warning" id="error-msg" style="padding:10px;display:none;color:#D9534F"></p>
            <form   method="post" role="form" >
                <input type="hidden" id="status" name="status"    value="" />
                <div class="form-group">
                    <label for="system_name"><?=lang('sys.pay.systemid');?></label>
                    <input type="hidden" id="gaId" name="gaId">
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
                    <input type="text" value="" class="form-control" id="system_name" name="system_name" required>
                </div>
                <div class="form-group">
                    <label for="note"><?=lang('sys.ga.note');?></label>
                    <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="last_sync_datetime"><?=lang('sys.ga.lastsyncdt');?></label>
                    <input  type="text" id="last_sync_datetime" name="last_sync_datetime"  data-time="true"   value=""   class="form-control input-sm dateInput" />
                </div>
                <div class="form-group">
                    <label for="last_sync_id"><?=lang('sys.ga.lastsyncid');?></label>
                    <input type="text" id="last_sync_id" name="last_sync_id"    value=""   class="form-control" />
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
                    <label for="game_platform_rate"><?=lang('sys.gd7') . " " . lang('sys.rate');?></label>
                    <input type="text"  id="game_platform_rate"  name="game_platform_rate"   value=""   class="form-control input-sm" />
                </div>
                <div class="form-group">
                    <label for="extra_info"><?=lang('sys.ga.extrainfo');?></label>
                    <pre class="form-control" id="extra_info" name="extra_info" style="height: 150px"></pre>
                </div>
                <div class="form-group">
                    <label for="sandbox_extra_info"><?=lang('sys.ga.sandboxextrainfo');?></label>
                    <pre class="form-control" id="sandbox_extra_info" name="sandbox_extra_info" style="height: 150px"></pre>
                </div>



                <!----------NOTE: BUTTON TITLE WILL BE UPDATE THROUG JAVASCTRIPT---------------->
                <button id="add-update-button"  type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info'?>"></button>

            </form>


        </div>
    </div>


</div>
<!---------------FORM end---------------->




<div id="conf-modal"  class="modal fade bs-example-modal-md"  data-backdrop="static"
data-keyboard="false"  tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
<div class="modal-dialog modal-md">
    <div class="modal-content">


        <div class="modal-header panel-heading">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel" ><?=lang('sys.ga.conf.title');?></h3>
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
            <button type="button" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default'?> " id="cancel-delete" data-dismiss="modal"><?=lang('pay.bt.cancel');?></button>
            <button type="button" id="confirm-action" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?> "><?=lang('pay.bt.yes');?></button>
        </div>
    </div>
</div>
</div>

<!--MODAL for edit column-->
<!-- <div id="edit-column" class="modal fade" tabindex="-1"  role="dialog" aria-labelledby="modal_column" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel"><?=lang('sys.vu57');?></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="help-block">
                            <?=lang('sys.vu58');?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <form action="<?=BASEURL . 'game_api/postChangeColumns'?>" method="post" role="form" id="modal_column_form">
                        <div class="col-md-7 col-md-offset-1">


                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="system_name"  <?=($this->session->userdata('system_name_gapi')) ? 'checked' : ''?>  ><?=lang('sys.ga.systemname');?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="note"  <?=($this->session->userdata('note_gapi')) ? 'checked' : ''?>   > <?=lang('sys.ga.note');?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="last_sync_datetime"  <?=($this->session->userdata('last_sync_datetime_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.lastsyncdt');?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="last_sync_id"  <?=($this->session->userdata('last_sync_id_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.lastsyncid');?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="last_sync_details"  <?=($this->session->userdata('last_sync_details_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.lastsyncdet');?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="system_type"  <?=($this->session->userdata('system_type_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.systemtype');?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="live_url"  <?=($this->session->userdata('live_url_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.liveurl');?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="sandbox_url"  <?=($this->session->userdata('sandbox_url_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.sandboxurl');?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="live_key"  <?=($this->session->userdata('live_key_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.livekey');?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="live_secret"  <?=($this->session->userdata('live_secret_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.livesecret');?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="sandbox_key"  <?=($this->session->userdata('sandbox_key_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.sandboxkey');?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="sandbox_secret"  <?=($this->session->userdata('sandbox_secret_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.sandboxsecret');?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="live_mode"  <?=($this->session->userdata('live_mode_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.livemode');?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="second_url"  <?=($this->session->userdata('second_url_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.secondurl');?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="sandbox_account"  <?=($this->session->userdata('sandbox_account_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.sandboxacct');?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="live_account"  <?=($this->session->userdata('live_account_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.liveacct');?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="system_code"  <?=($this->session->userdata('system_code_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.systemcode');?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="status"  <?=($this->session->userdata('status_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.status');?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="class_name"  <?=($this->session->userdata('class_name_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.classname');?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="local_path"  <?=($this->session->userdata('local_path_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.localpath');?>
                                </label>
                            </div>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="manager"  <?=($this->session->userdata('manager_gapi')) ? 'checked' : ''?>   ><?=lang('sys.ga.manager');?>
                                </label>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal"  aria-hidden="true"><?=lang('sys.vu69');?></button>
                    <button class="btn btn-info" id="save_changes"  type="submit"name="save_changes"><?=lang('sys.gd31');?></button>
                </div>
            </form>
        </div>
    </div> -->
    <script type="text/javascript">


        $(document).ready(function () {

            $('body').tooltip({
                selector : '[data-toggle="tooltip"]',
                placement : "bottom"
            });

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

           $('#my_table').DataTable({
            searching: true,
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
                }
            ],
           columnDefs: [
                { sortable: false, targets: [ 0 ] }
           ],
            order: [[1, 'desc']]

        });


            var GET_EDIT_ROW_URL = '<?php echo site_url('game_api/editGameApi') ?>/',
            ADD_ROW_URL = '<?php echo site_url('game_api/addGameApi') ?>/',
            UPDATE_ROW_URL = '<?php echo site_url('game_api/updateGameApi') ?>/',
            DELETE_ROW_URL = '<?php echo site_url('game_api/deleteGameApi') ?>/',
            DISABLE_ABLE_ROW_URL = '<?php echo site_url('game_api/disableAbleGameApi') ?>/',
            MAINTENANCE_MODE_ROW_URL = '<?php echo site_url('game_api/gameMaintenanceMode') ?>/',
            PAUSE_SYNC_ROW_URL = '<?=site_url('game_api/gamePauseSync') ?>/',
            REFRESH_PAGE_URL = '<?php echo site_url('game_api/viewGameApi') ?>/',
            GET_SYSTEM_TYPES_URL = '<?php echo site_url('game_api/getSystemTypes') ?>/',
            SYNC_GAME_LOG_URL = '<?php echo site_url('game_api/syncGameLog') ?>/';
						PRELOAD_API_URL = '<?php echo site_url('game_api/getApiDetail') ?>/';

            var currentMode,
            forDeleteIds = Array(),
            addUpdateUrl;

            var LANG = {
                ADD_PANEL_TITLE : "<?=lang('sys.ga.add.paneltitle');?>",
                EDIT_PANEL_TITLE : "<?=lang('sys.ga.edit.paneltitle');?>",
                ADD_BUTTON_TITLE : "<i class='fa fa-check'></i> <?=lang('sys.ga.add.button');?>",
                UPDATE_BUTTON_TITLE : "<i class='fa fa-check'></i> <?=lang('sys.ga.update.button');?>",
                DELETE_CONFIRM_MESSAGE : "<?=lang('sys.ga.conf.del.msg');?>",
                UPDATE_CONFIRM_MESSAGE :"<?=lang('sys.ga.conf.update.msg');?>",
                ADD_CONFIRM_MESSAGE :"<?=lang('sys.ga.conf.add.msg');?>",
                DISABLE_CONFIRM_MESSAGE :"<?=lang('sys.ga.conf.disable.msg');?>",
                ABLE_CONFIRM_MESSAGE :"<?=lang('sys.ga.conf.able.msg');?>",
                EDIT : "<?=lang('sys.ga.edit.buttontitle');?>",
                EDIT_COLUMN : "<?=lang('pay.bt.edit.column');?>",
                DELETE_ITEMS : "<?=lang('sys.ga.delete.items');?>",
                ADD_GAME_DESC : "<?=lang('sys.ga.add.paneltitle');?>",
                GAME_MAINTENANCE : "<?=lang('sys.game.maintenance');?>",
                GAME_PAUSE_SYNCING : "<?=lang('sys.pause.syncing');?>",
                GAME_REVERT_SYNCING : "<?=lang('sys.revert.syncing');?>"
            };

      var preloadApiDetail = function(){
		// preload only when adding new API
		if(currentMode != 'add'){
		  return;
		}
		$.ajax({
		  url : PRELOAD_API_URL + $(this).val(),
		  type : 'GET',
		  dataType : "json",
		  cache : false
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
        $("#created_on").val(data.created_on);

			  extraInfoEditor.setValue(JSON.stringify(JSON.parse(data.extra_info), null, 4));
			  sandboxExtraInfoEditor.setValue(JSON.stringify(JSON.parse(data.sandbox_extra_info), null, 4));

			  if(extraInfoEditor.getValue() == 'null') {
					extraInfoEditor.setValue('');
			  }
			  if(sandboxExtraInfoEditor.getValue() == 'null') {
					sandboxExtraInfoEditor.setValue('');
				}
			});
	  };

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

      $(".sync-log").click(function(){
        var platformId = $(this).data('platform-id');
        var timeFrom = prompt("<?php echo lang('sys.ga.synclogfrom'); ?>");
        var timeTo = prompt("<?php echo lang('sys.ga.synclogto'); ?>");
        var username = prompt("<?php echo lang('sys.ga.syncloguser'); ?>");
        var postData = {
          platformId : platformId,
          timeFrom : timeFrom,
          timeTo : timeTo,
          username : username
        };

        $.ajax({
          url : SYNC_GAME_LOG_URL,
          type : 'POST',
          data : postData,
          dataType : "json",
          cache : false
        }).done(function (data) {
          if(data.success == 'fail'){
            alert('Failed to submit sync request. Reason: ' + data.msg);
          }
          else if(data.success == 'success'){
            alert('Sync request completed.');
            location.reload();
          }
          else{
            alert('The request failed: ' + data.success);
          }
        });
      });

    //Selections for delete
    $(".delete_id_selector").click(function () {
      var id = $(this).attr("id").split('-')[1];
      currentMode = "delete";
      forDeleteIds.push(id);
    });

    //Cancels confirmation
    $("#cancel-delete").click(function () {
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
       // saveData(url);
       return false;
     });

      //Agreed to Confirmation
    $("#confirm-action").click(function () {

     // if (currentMode === "delete") {
     //   deleteRows();
     // }

     if (currentMode === "edit" || currentMode === "add" ) {
      addUpdateRow();
    }

     if (currentMode === "disable" || currentMode === "able" ) {
       ableDisableRow();
     }

    if (currentMode === "maintenance" || currentMode === "active" ) {
        setMaintenanceMode();
    }

    if (currentMode === "pause_sync" || currentMode === "revert_sync" ) {
        pauseSyncing();
    }
       $(this).prop('disabled', true);
       $("#cancel-delete").prop('disabled', true);

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

        // case "delete":
        // if (currentMode == "delete") {
        //   showConfModal();
        //   var count = forDeleteIds.length;
        //   $('#conf-msg').html(LANG.DELETE_CONFIRM_MESSAGE + "<b>" + count + " items ?</b>");
        // }

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

          case "maintenance":
              if (currentMode == "maintenance") {
                  showConfModal();
                  $('#conf-msg').html(LANG.GAME_MAINTENANCE);
              }
              break;

          case "active":
              if (currentMode == "active") {
                  showConfModal();
                  $('#conf-msg').html(LANG.ABLE_CONFIRM_MESSAGE);
              }
              break;

          case "pause_sync":
              if (currentMode == "pause_sync") {
                  showConfModal();
                  $('#conf-msg').html(LANG.GAME_PAUSE_SYNCING);
              }
              break;

          case "revert_sync":
              if (currentMode == "revert_sync") {
                  showConfModal();
                  $('#conf-msg').html(LANG.GAME_REVERT_SYNCING);
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
      cache : false
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
      dataType : "json"
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

      if ( ! isJsonString(extraInfoEditor.getValue()) || ! isJsonString(sandboxExtraInfoEditor.getValue())) {
        $("#add-update-button").prop('disabled', false);
        hideConfModal();
        showValidationError('Invalid JSON');
        return false;
      }

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
      game_platform_rate:$("#game_platform_rate").val(),
      extra_info : extraInfoEditor.getValue(),
      sandbox_extra_info : sandboxExtraInfoEditor.getValue()
    };


    $.ajax({
      url : addUpdateUrl,
      type : 'POST',
      data : data,
      dataType : "json",
      cache : false
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

  function isJsonString(str) {
    if (str == '') return true;
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

     function deleteRows(){

    var data = {
      forDeletes : forDeleteIds
    };
    $.ajax({
      url : DELETE_ROW_URL,
      type : 'POST',
      data : data,
      dataType : "json"
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
      dataType : "json"
    }).done(function (data) {
      // console.log(data)
      removeOptions();
      var sytemTypes= data.data.sytemTypes,
      sytemTypesLength = sytemTypes.length;
      for (var i = 0; i < sytemTypesLength; i++) {
        if(i==0){
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
    $("#newId").val("");
    $("#system_name").val("");
    $("#note").val("");
    $("#last_sync_datetime").val("");
    $("#last_sync_id").val("");
    $("#last_sync_details").val("");
    $("#system_type").val("").trigger('change');
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

  /*Gets Game Description row for editing*/
  function editRow(rowId) {

    $.ajax({
      url : GET_EDIT_ROW_URL + rowId,
      type : 'GET',
      dataType : "json"
    }).done(function (data) {

     removeOptions();
     var sytemTypes= data.data.sytemTypes,
     gameApi = data.data.gameApi,
     sytemTypesLength = sytemTypes.length;
     for (var i = 0; i < sytemTypesLength; i++) {
      if(gameApi.system_type == sytemTypes[i].id){
         $('#system_type').append('<option value="' + sytemTypes[i].id+ '" selected>' + sytemTypes[i].system_type+ '</option>');
       }else{
         $('#system_type').append('<option value="' + sytemTypes[i].id+ '" >' + sytemTypes[i].system_type+ '</option>');
       }

    }

    $("#gaId").val(gameApi.id);
    $("#newId").val(gameApi.id);
    $("#system_name").val(gameApi.system_name);
    $("#note").val(gameApi.note);
    $("#last_sync_datetime").val(gameApi.last_sync_datetime);
    $("#last_sync_id").val(gameApi.last_sync_id);
    $("#last_sync_details").val(gameApi.last_sync_details);
    $("#system_type").val(gameApi.system_type).trigger('change');
    $('#category').val(gameApi.category);
    $('#amount_float').val(gameApi.amount_float);
    $("#live_url").val(gameApi.live_url);
    $("#sandbox_url").val(gameApi.sandbox_url);
    $("#live_key").val(gameApi.live_key);
    $("#live_secret").val(gameApi.live_secret);
    $("#sandbox_key").val(gameApi.sandbox_key);
    $("#sandbox_secret").val(gameApi.sandbox_secret);
    $("#live_mode").prop('checked', gameApi.live_mode == 1);
    $("#second_url").val(gameApi.second_url);
    $("#sandbox_account").val(gameApi.sandbox_account);
    $("#live_account").val(gameApi.live_account);
    $("#system_code").val(gameApi.system_code);
    $("#status").val(gameApi.status);
    $("#class_name").val(gameApi.class_name);
    $("#local_path").val(gameApi.local_path);
    $("#manager").val(gameApi.manager);
    $("#game_platform_rate").val(gameApi.game_platform_rate);
    var jsonObj; // Check to prevent parsing of invlaid JSON string, just output as it is
    try{ jsonObj = eval("("+gameApi.extra_info+")"); } catch (e) {}
    extraInfoEditor.setValue((gameApi.extra_info != null && typeof jsonObj !== 'undefined') ? JSON.stringify(jsonObj, null, 4) : gameApi.extra_info);
    try{ jsonObj = eval("("+gameApi.sandbox_extra_info+")"); } catch (e) {}
    sandboxExtraInfoEditor.setValue((gameApi.sandbox_extra_info != null && typeof jsonObj !== 'undefined') ? JSON.stringify(jsonObj, null, 4) : gameApi.sandbox_extra_info);

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
          title : LANG.EDIT
        });

        $("#edit_column").tooltip({
          placement : "left",
          title : LANG.EDIT_COLUMN
        });
        $("#delete-items").tooltip({
          placement : "right",
          title : LANG.DELETE_ITEMS
        });
        $("#add-row").tooltip({
          placement : "right",
          title : LANG.ADD_GAME_DESC
        });

            $(".maintenance_row").click(function () {
                var id = $(this).attr("id").split('-')[1];
                currAbleDisableId = id;
                currentMode = "maintenance";
                confirmationMessage("maintenance");
            });

            $(".active_row").click(function () {
                var id = $(this).attr("id").split('-')[1];
                currAbleDisableId = id;
                currentMode = "active";
                confirmationMessage("active");
            });

            $(".pause_sync_row").click(function () {
                var id = $(this).attr("id").split('-')[1];
                currAbleDisableId = id;
                currentMode = "pause_sync";
                confirmationMessage("pause_sync");
            });

            $(".revert_sync_row").click(function () {
                var id = $(this).attr("id").split('-')[1];
                currAbleDisableId = id;
                currentMode = "revert_sync";
                confirmationMessage("revert_sync");
            });

            function setMaintenanceMode(){
                var maintenance_mode = currentMode == 'maintenance' ? '1' : '0';
                var data = {
                    id : currAbleDisableId,
                    maintenance_mode:maintenance_mode
                };

                $.ajax({
                    url : MAINTENANCE_MODE_ROW_URL,
                    type : 'POST',
                    data : data,
                    dataType : "json",
                    cache : false
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

            function pauseSyncing(){
                var pause_sync = currentMode == 'pause_sync' ? '1' : '0';
                var data = {
                    id : currAbleDisableId,
                    pause_sync: pause_sync
                };

                $.ajax({
                    url : PAUSE_SYNC_ROW_URL,
                    type : 'POST',
                    data : data,
                    dataType : "json",
                    cache : false
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


}); //end document ready.




</script>