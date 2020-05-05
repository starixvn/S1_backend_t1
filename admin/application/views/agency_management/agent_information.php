<?php
/**
 *   filename:   agent_information.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-25
 *   @brief:     view for agent Information
 */

// PHP {{{1
$agent_id = $agent['agent_id'];
$activate_url = site_url('agency_management/activate_agent/' . $agent_id);
$inactivate_url = site_url('agency_management/inactivate_agent/' . $agent_id);
$freeze_url = site_url('agency_management/freeze_agent/' . $agent_id);
$suspend_url = site_url('agency_management/suspend_agent/' . $agent_id);
$edit_url = site_url('agency_management/edit_agent/' . $agent_id);
$sub_list_url = site_url('/agency_management/agent_list?parent_id=' . $agent_id);
//$reset_pass_url = site_url('agency_management/reset_password/' . $agent_id);
$reset_pass_url = site_url('agency_management/reset_random_password/' . $agent_id);
$login_as_url = site_url('agency_management/login_as_agent/' . $agent_id);
$adjust_credit_limit_url = site_url('agency_management/adjust_credit_limit/' . $agent_id);
$adjust_credit_url = site_url('agency_management/adjust_credit/' . $agent_id);
$parent_agent_url = site_url('agency_management/agent_information/' . $parent['agent_id']);
$add_bank_url = site_url('agency_management/add_bank_account/' . $agent_id);

$readonly_url = site_url('agency_management/readonly_account/' . $agent_id);

// PHP }}}1
?>

<!-- HTML {{{1 -->
<input type="hidden" id="agent_id" value="<?=$agent_id?>"/>
<div class="row">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <!-- heading {{{2 -->
            <div class="panel-heading">
                <h4 class="panel-title pull-left custom-pt">
                    <i class="icon-list"></i> &nbsp;<?=lang('Agent Information');?>
                </h4>
                <div class="clearfix"></div>
            </div> <!-- heading }}}2 -->

            <div class="panel-body" id="agent_panel_body">
                <!-- Basic Info {{{2 -->
                <div class="panel panel-primary">
                    <!-- panel heading {{{3 -->
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a href="#basic_info" id="hide_agent_basic_info" class="btn btn-info btn-sm">
                                <i class="glyphicon glyphicon-chevron-up" id="hide_agentbi_up"></i>
                            </a>
                            &nbsp; <?=lang('Basic Information');?>

                            <!-- panel buttons {{{4 -->
                            <div class="pull-right">
                                <?php if($this->utils->isEnabledFeature('enabled_readonly_agency')){?>
                                <a href="<?=$readonly_url?>" class="btn btn-xs btn-info">
                                    <span class="fa-stack">
                                      <i class="fa fa-pencil fa-stack-1x"></i>
                                      <i class="fa fa-ban fa-stack-2x fa-rotate-90"></i>
                                    </span> <?=lang('Readonly Account');?>
                                </a>
                                <?php }?>
                                <?php if ($agent['status'] == 'frozen') {?>
                                <span style="color: red;"><?=lang('The agent is FROZEN!')?></span>
                                <input type='button' value='<?php echo lang('Activate'); ?>'
                                class='btn btn-info btn-sm pull-right'
                                onclick="window.location.href='<?php echo $activate_url; ?>';">
                                <?php } else {?>
                                <?php if ($agent['status'] == 'active') {?>
                                <?php if ($this->permissions->checkPermissions('edit_agent')) {?>
                                <a href="<?=$edit_url?>" class="btn btn-sm btn-default">
                                    <i class="glyphicon glyphicon-edit"></i> <?=lang('lang.edit');?>
                                </a>
                                <?php } ?>
                                <?php if ($this->utils->isEnabledMDB()) { ?>
                                    <a href="<?=site_url('/agency_management/sync_agent_to_mdb/'.$agent_id)?>" class="btn btn-success btn-sm">
                                        <i class="fa fa-refresh"></i> <?=lang('Sync To Currency')?>
                                    </a>
                                <?php } ?>

                                <input type='button' value='<?php echo lang('View Key'); ?>'
                                class='btn btn-info btn-sm'
                                data-toggle="modal" data-target="#keyModals">

                                <input type='button' value='<?php echo lang('Suspend'); ?>'
                                class='btn btn-warning btn-sm'
                                onclick="window.location.href='<?php echo $suspend_url; ?>';">
                                <input type='button' value='<?php echo lang('Freeze'); ?>'
                                class='btn btn-danger btn-sm'
                                onclick="window.location.href='<?php echo $freeze_url; ?>';">
                                <?php } else {?>
                                <span style="color: red;"><?=lang('The agent is SUSPENDED!')?></span>
                                <input type='button' value='<?php echo lang('Activate'); ?>'
                                class='btn btn-info btn-sm'
                                onclick="window.location.href='<?php echo $activate_url; ?>';">
                                <?php } ?>
                                <?php } ?>
                            </div>
                            <!-- panel buttons }}}4 -->
                        </h4>
                    </div> <!-- panel heading }}}3 -->
                    <!-- panel body {{{3 -->
                    <div class="panel-body agent_basic_panel_body" id="agent_basic_info">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered" style="margin-bottom:0;">
                                        <!-- name and Password {{{4 -->
                                        <tr>
                                            <th class="active col-md-2"><?=lang('Agent Username');?></th>
                                            <td class="col-md-4" id="agent-username"><?=$agent['agent_name']?></td>
                                            <th class="active col-md-2">
                                                <span id="_password_label"><?=lang('Password');?></span>
                                            </th>
                                            <td>
                                                <?php if ($agent['status'] == 'active' || $agent['status'] == 'suspended') {?>
                                                <?php if ($this->permissions->checkPermissions('agent_admin_action')) {?>
                                                <a href="javascript:void(0)" class="btn btn-xs btn-primary"
                                                    onclick="reset_agent_password('<?=$agent_id?>')">
                                                    <?=lang('lang.reset');?>
                                                </a>
                                                <?php }?>
                                                <?php if ($this->utils->isEnabledFeature('login_as_agent') && $this->permissions->checkPermissions('login_as_agent')){?>
                                                <a href="<?php echo $login_as_url ?>"
                                                    class="btn btn-primary btn-xs" target="_blank">
                                                    <?=lang('Login as Agent')?>
                                                </a>
                                                <?php }?>
                                                <div class="pull-right reset-password">
                                                    <span id="random_password_span_lbl" hidden><?=lang('system.word10');?></span>
                                                    <span id="random_password_span" class="text-primary"></span>
                                                </div>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <!-- name and Password }}}4 -->
                                        <!-- parent and sub agents {{{4 -->
                                        <tr>
                                            <th class="active col-md-2">
                                                <?php echo lang('Parent Agent'); ?>
                                            </th>
                                            <td>
                                                <?php if (!empty($parent)) {?>
                                                <a href="<?=$parent_agent_url;?>" target="_blank">
                                                    <?php echo $parent['agent_name']; ?>
                                                </a>
                                                <?php }?>
                                            </td>
                                            <th class="active col-md-2"><?=lang('Total Sub Agents');?></th>
                                            <td>
                                                <a href="<?php echo $sub_list_url; ?>">
                                                    <?=count($sub_agents);?>
                                                </a>
                                            </td>
                                        </tr> <!-- parent and sub agents }}}4 -->
                                        <!-- credit Limit and available Credit {{{4 -->
                                        <tr>
                                            <th class="active col-md-2">
                                                <?=lang('Credit Limit');?>
                                            </th>
                                            <td>
                                                <?=$agent['credit_limit']?>
                                                <?php if ($agent['status'] == 'active') {?>
                                                <?php if ($this->permissions->checkPermissions('agent_admin_action')) {?>
                                                <a href="<?=$adjust_credit_limit_url;?>"
                                                    class="btn btn-primary btn-xs">
                                                    <?=lang('Adjust Credit Limit')?>
                                                </a>
                                                <?php } ?>
                                                <?php } ?>
                                            </td>
                                            <th class="active col-md-2">
                                                <?=lang('transaction.credit');?>
                                            </th>
                                            <td>
                                                <?=$agent['available_credit']?>
                                                <?php if ($agent['status'] == 'active') {?>
                                                <?php if ($this->permissions->checkPermissions('agent_admin_action')) {?>
                                                <a href="javascript:void(0)" class="btn btn-primary btn-xs"
                                                    onclick="agent_adjust_credit('<?=$agent_id?>')" >
                                                    <?=lang('Adjust Credit')?>
                                                </a>
                                                <?php } ?>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <!-- credit Limit and available Credit }}}4 -->
                                        <!-- currency and status {{{4 -->
                                        <tr>
                                            <th class="active col-md-2"><?=lang('Currency');?></th>
                                            <td class="col-md-4"><?=$agent['currency']?></td>
                                            <th class="active col-md-2"><?=lang('Status');?></th>
                                            <td class="col-md-4"><?=lang($agent['status'])?></td>
                                        </tr>
                                        <!-- }}}4 -->
                                        <!-- credit Limit and available Credit }}}4 -->
                                        <!-- agent level and rev share {{{4 -->
                                        <tr>
                                            <th class="active col-md-2"><?=lang('Agent Level');?></th>
                                            <td class="col-md-4"><?=$agent['agent_level']?></td>
                                            <?php if ( ! $this->utils->isEnabledFeature('agency_hide_binding_player')): ?>
                                                <?php include __DIR__.'/../includes/agent_binding_player.php'; ?>
                                            <?php endif ?>
<!--                                             <th class="active col-md-2"><?=lang('Rev Share');?></th>
                                            <td class="col-md-4"><?=$agent['rev_share']?></td>
 -->                                        </tr>
                                        <!-- }}}4 -->
                                        <!-- credit Limit and available Credit }}}4 -->
                                        <!-- allowed agent level and level names {{{4 -->
                                        <tr>
                                            <th class="active col-md-2"><?=lang('Max Agent Level');?></th>
                                            <td class="col-md-4"><?=count(explode(',', $agent['agent_level_name'])) - 1; ?></td>
                                            <th class="active col-md-2"><?=lang('Agent Level Names');?></th>
                                            <td class="col-md-4"><?=$agent['agent_level_name']?></td>
                                        </tr>
                                        <!-- }}}4 -->
                                        <!-- credit Limit and available Credit }}}4 -->
                                        <!-- Rolling Comm {{{4 -->
<!--                                         <tr>
                                            <th class="active col-md-2"><?=lang('Rolling Comm');?></th>
                                            <td class="col-md-4"><?=$agent['rolling_comm']?></td>
                                            <th class="active col-md-2"><?=lang('Rolling Comm Basis');?></th>
                                            <td class="col-md-4"><?=$agent['rolling_comm_basis']?></td>
                                        </tr>
 -->
                                        <!-- credit Limit and available Credit }}}4 -->
                                        <!-- child setting {{{4 -->
                                        <tr>
                                            <th class="active col-md-2"><?=lang('Can Have sub-agent');?></th>
                                            <td class="col-md-4"><?=$agent['can_have_sub_agent']? lang('yes') : lang('no')?></td>
                                            <th class="active col-md-2"><?=lang('Can Have Players');?></th>
                                            <td class="col-md-4"><?=$agent['can_have_players']? lang('yes') : lang('no')?></td>
                                        </tr>
                                        <!-- }}}4 -->
                                        <tr>
                                            <th class="active col-md-2"><?=lang('Show Bet Limit Template');?></th>
                                            <td class="col-md-4"><?=$agent['show_bet_limit_template']? lang('yes') : lang('no')?></td>
                                            <th class="active col-md-2"><?=lang('Show Rolling Commission');?></th>
                                            <td class="col-md-4"><?=$agent['show_rolling_commission']? lang('yes') : lang('no')?></td>
                                        </tr>
                                        <!-- credit Limit and available Credit }}}4 -->
                                        <!-- VIP Level and settlement period {{{4 -->
                                        <tr>
                                            <th class="active col-md-2"><?=lang('Default VIP');?></th>
                                            <td class="col-md-4"><?=lang($vip_group_name)?> - <?=lang($vip_level_name)?></td>
                                            <th class="active col-md-2"><?=lang('Settlement Period');?></th>
                                            <td class="col-md-4"><?=lang($agent['settlement_period'])?></td>
                                        </tr>

                                        <?php if($this->config->item('enable_gamegateway_api')) : ?>
                                            <tr>
                                                <th class="active col-md-2"><?=lang('Credential Mode');?></th>
                                                <td class="col-md-4">
                                                    <?php if($agent['live_mode']== 1) : ?>
                                                        Live
                                                    <?php else : ?>
                                                        Staging
                                                    <?php endif; ?>
                                                </td>
                                                <th class="active col-md-2"><?=lang('Player Prefix');?></th>
                                                <td class="col-md-4"><?=lang($agent['player_prefix'])?></td>
                                            </tr>
                                        <?php endif; ?>

                                        <!-- }}}4 -->
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--  modal for adding players for the agent {{{4 -->
                        <div class="modal fade in" id="add_players_modal"
                            tabindex="-1" role="dialog" aria-labelledby="label_add_players_modal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title" id="label_add_players_modal"></h4>
                                    </div>
                                    <div class="modal-body"></div>
                                    <div class="modal-footer"></div>
                                </div>
                            </div>
                        </div> <!--  modal for level name setting }}}4 -->
                    </div>
                    <!-- panel body }}}3 -->
                </div>
                <!-- End of Basic Info }}}2 -->

            <?php include __DIR__.'/../includes/agent_bank_info.php'; ?>

            <?php if ($agent['agent_level'] == 0 || $this->utils->isEnabledFeature('variable_agent_fee_rate')): ?>
                <?=$this->load->view('includes/agent_commission_fees', array('agent' => $agent, 'view_only' => TRUE), TRUE)?>
            <?php endif ?>

            <?php if ($this->utils->isEnabledFeature('agent_tier_comm_pattern')) { ?>
                <?=$this->load->view('includes/game_platform_settings_tier_comm', $game_platform_settings, TRUE)?>
            <?php } else { ?>
                <?=$this->load->view('includes/game_platform_settings', $game_platform_settings, TRUE)?>
            <?php } ?>

        <!-- Tracking Code {{{2 -->
        <a name="agent_tracking_code">
        <div class="panel panel-primary agency_tracking_code">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a name="hide_agent_track_info" href="#personal_info" id="hide_agent_track_info" class="btn btn-info btn-sm">
                        <i class="glyphicon glyphicon-chevron-up" id="hide_agent_atk_up"></i>
                    </a> <?=lang('Agent Tracking Code');?>
                </h4>
            </div>

            <div class="panel-body agent_track_panel_body">
                <form action="<?=site_url('agency_management/edit_tracking_code/' . $agent_id)?>" method="POST" class="form-horizontal">
                    <div class="row">
                        <div class="btn-group col-md-4">
                            <label for="tracking_code" class="control-label" style="text-align:right;"><?=lang('aff.ai40');?> </label>
                            <div>
                                <input type="text" disabled="disabled" name="tracking_code" id="tracking_code" class="form-control <?=$this->utils->isEnabledFeature('agent_tracking_code_numbers_only') ? 'number_only' : ''?>"
                                minlength="4" maxlength="20" value="<?php echo $agent['tracking_code']; ?>" />
                                <?php echo form_error('tracking_code', '<span style="color:#CB3838;">'); ?>
                            </div>
                        </div>
                        <?php if ($this->permissions->checkPermissions('edit_agency_tracking_code')) {?>
                        <div class="btn-group col-md-8" id="random_code_sec" role="group" style="margin-top: 26px;">

                            <?php if ($this->utils->isEnabledFeature('agent_tracking_code_numbers_only')): ?>
                            <a href="javascript:void(0)" style="display: none;" class="btn btn-default hidden-xs btn_update_tracking_code" id="random_code" onclick="randomNumber('8');">
                            <?php else: ?>
                            <a href="javascript:void(0)" style="display: none;" class="btn btn-default hidden-xs btn_update_tracking_code" id="random_code" onclick="randomCode('8');">
                            <?php endif ?>
                                <i class="fa fa-calculator"></i> <?=lang('aff.ai38');?>
                            </a>
                            <input type="submit" style="display: none;"  class="btn btn-info btn_update_tracking_code" value="<?=lang('Save');?>"/>
                            <input type="button" onclick="cancel_update_tracking_code()" style="display: none;"  class="btn btn-danger btn_update_tracking_code" value="<?=lang('Cancel');?>"/>

                            <a href="javascript:void(0)" class="btn btn-default hidden-xs" id="random_code_lock" onclick="unlock_tracking_code();">
                                <?=lang('icon.locked') . "" . lang('system.word56');?>
                              </a>
                        </div>
                        <?php }?>
                        <div class="clearfix"></div>
                    </div>
                </form>

                <div class="row">
                    <div class="col-md-12" style="overflow: auto;">
                        <table class="table table-striped">
                            <thead>
                                <th colspan="2"><?=lang('aff.ai41');?></th>
                            </thead>

                            <tbody>
                                <?php
                                if (!empty($domain_list_for_agent) && !empty($agent['tracking_code'])) {
                                    foreach ($domain_list_for_agent as $domain_value) {
                                        $url  = $this->utils->isEnabledFeature('use_https_for_agent_tracking_links') ? 'https://' : 'http://';
                                        $url .= $domain_value['domain_name'];
                                        $url .= $this->utils->isEnabledFeature('use_new_agent_tracking_link_format') ? '?ag=' : '/ag/'; # OGP-6432
                                        $url .= $agent['tracking_code'];
                                ?>
                                        <tr>
                                            <td><a href="<?=$url?>" target="_blank"><?=$url?></a></td>
                                        </tr>
                                    <?php
                                    }?>
                                <?php } else {?>
                                        <tr>
                                            <td colspan="6" style="text-align:center"><span class="help-block"><?php echo lang('N/A'); ?></span></td>
                                        </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>


            <?php if ($this->permissions->checkPermissions('edit_agency_source_code')) {?>
                <a name="agent_source_code_list">
                <div class="row">
                    <div class="col-md-12" style="overflow: auto;">
                        <a href="javascript:void(0)" class="btn btn-primary btn-xs" onclick="newSourceCode()"><?php echo lang('New Agent Source Code');?></a>
                        <table class="table table-striped">
                            <thead>
                                <th><?php echo lang('Agent Source Code');?></th>
                                <th><?php echo lang('Link Example');?></th>
                                <th><?php echo lang('Action');?></th>
                            </thead>
                            <tbody>
                            <?php
                            if(!empty($agent_source_code_list)){
                                foreach($agent_source_code_list as $source_code){?>
                                <tr>
                                    <td><?php echo $source_code['tracking_source_code']; ?></td>
                                    <td><?php echo !empty($first_domain) ? $first_domain.'/agent/'.$agent['tracking_code'].'/'.$source_code['tracking_source_code'] : ""; ?>
                                    </td>
                                    <td><a href="javascript:void(0)" class="btn btn-primary btn-xs" onclick="editSourceCode(<?php echo $source_code['id'];?>, '<?php echo $source_code['tracking_source_code'];?>')"><?php echo lang('Edit');?></a>
                                    <a href="javascript:void(0)" id="frm_remove_domain" onclick='removeSourceCode(<?php echo $source_code['id'];?>,  "<?php echo $source_code['tracking_source_code']; ?>")' class="btn btn-primary btn-xs"><?php echo lang('Remove'); ?></a>
                                    </td>
                                </tr>
                            <?php }
                            }?>
                            </tbody>
                        </table>
                    </div>
                </div>


            <?php }?>

            <?php if ($this->permissions->checkPermissions('edit_agency_domain')) {?>
                <a name="agent_additional_domain_list">
                <div class="row">
                    <div class="col-md-12" style="overflow: auto;">
                        <a href="javascript:void(0)" class="btn btn-primary btn-xs" onclick="newAdditionalDomain()"><?php echo lang('New Agent Additional Domain');?></a>
                        <table class="table table-striped">
                            <thead>
                                <th><?php echo lang('Agent Domain');?></th>
                                <th><?php echo lang('Action');?></th>
                            </thead>
                            <tbody>
                            <?php
                            if(!empty($agent_additional_domain_list)){
                                foreach($agent_additional_domain_list as $domain){?>
                                <tr>
                                    <td><a href="http://<?php echo $domain['tracking_domain']; ?>" target='_blank'><?php echo $domain['tracking_domain']; ?></a></td>
                                    <td><a href="javascript:void(0)" class="btn btn-primary btn-xs" onclick="editAdditionalDomain(<?php echo $domain['id'];?>, '<?php echo $domain['tracking_domain'];?>')"><?php echo lang('Edit');?></a>
                                    <a href="javascript:void(0)" class="btn btn-primary btn-xs" onclick="removeAdditionalDomain(<?php echo $domain['id'];?>, '<?php echo $domain['tracking_domain'];?>')"><?php echo lang('Remove');?></a> </td>
                                </tr>
                            <?php }
                            }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php }?>

                <div class="row">
                    <div class="col-md-12" style="overflow: auto;">
                        <table class="table table-striped">
                            <thead>
                                <th><?=lang('Sub Agent Link');?></th>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>
                                    <?php if (!empty($agent['tracking_code']) && !empty($agent['sub_link']) && !empty($agent['can_have_sub_agent'])) : ?>
                                        <a href="<?php echo $agent['sub_link']; ?>" target="_blank"><?=$agent['sub_link'];?></a>
                                    <?php else : ?>
                                        <?= lang('N/A'); ?>
                                    <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div> <!--end panel-body-->
        </div> <!--end panel-->
        <!-- End of Tracking Code }}}2 -->

<?php if(!$this->utils->isEnabledFeature('disable_agent_hierarchy')){?>
                <!-- Hierarchical Tree {{{2 -->
                <div class="panel panel-primary">
                    <!-- panel heading {{{3 -->
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a href="#hierarchy" id="hide_agent_hierarchy" class="btn btn-info btn-sm">
                                <i class="glyphicon glyphicon-chevron-up" id="hide_agentbi_up"></i>
                            </a>
                            &nbsp; <?=lang('Agent Hierarchy');?>
                        </h4>
                    </div> <!-- panel heading }}}3 -->

                    <!-- panel body {{{3 -->
                    <div class="panel-body agent_basic_panel_body" id="agent_hierarchy">
                        <!-- agent tree {{{4 -->
                        <div class="row">
                            <div class="col-md-12 agency_hierarchical_tree">
                                <label>
                                    <strong><?php echo lang('Agent Hierarchical Tree'); ?></strong>
                                </label>
                                <fieldset>
                                    <div class="row">
                                        <div id="agent_tree" class="col-xs-12">
                                        </div>
                                    </div>
                                </fieldset>
                            </div><!-- end col-md-12 -->
                        </div> <!-- agent tree }}}4 -->
                        <!--  modal for adding players for the agent {{{4 -->
                        <div class="modal fade in" id="add_players_modal"
                            tabindex="-1" role="dialog" aria-labelledby="label_add_players_modal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title" id="label_add_players_modal"></h4>
                                    </div>
                                    <div class="modal-body"></div>
                                    <div class="modal-footer"></div>
                                </div>
                            </div>
                        </div> <!--  modal for level name setting }}}4 -->
                    </div>
                    <!-- panel body }}}3 -->
                </div>
                <!-- End of Hierarchical Tree }}}2 -->
<?php }?>
            </div>
            <div class="panel-footer"></div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="keyModals" tabindex="-1" role="dialog" aria-labelledby="keyModalsTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle"><?=lang('View Keys')?></h5>
          </div>
          <div class="modal-body">
                <table class="table">
                  <thead class="thead-dark">
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col"><?=lang('Secure Key')?></th>
                      <th scope="col"><?=lang('Sign Key')?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th scope="row"><?=lang('Staging')?></th>
                      <td><?=(empty($agent['staging_secure_key'])? lang('N/A'):$agent['staging_secure_key'])?></td>
                      <td><?=(empty($agent['staging_sign_key'])? lang('N/A'):$agent['staging_sign_key'])?></td>
                    </tr>
                    <tr>
                      <th scope="row"><?=lang('Live')?></th>
                      <td><?=(empty($agent['live_secure_key'])? lang('N/A'):$agent['live_secure_key'])?></td>
                      <td><?=(empty($agent['live_sign_key'])? lang('N/A'):$agent['live_sign_key'])?></td>
                    </tr>
                  </tbody>
                </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
</div>
<!-- HTML }}}1 -->

<!-- JS {{{1 -->
<script type="text/javascript">
<?php if(!$this->utils->isEnabledFeature('disable_agent_hierarchy')){?>
    // Hierarchical Tree {{{2
        $('#agent_tree').jstree({
          'core' : {
            'data' : {
              "url" : "<?php echo site_url('/api/get_agent_hierarchical_tree/' . $agent_id); ?>",
              "dataType" : "json" // needed only if you do not supply JSON headers
            }
          },
          "plugins":[
            "search"
          ]
        });
    // Hierarchical Tree }}}2
<?php }?>
    // DataTables {{{2
//getMonthlyEarnings(0);
//getPayments(0);
$(document).ready(function() {

    $(".number_only").keydown(function (e) {
        var code = e.keyCode || e.which;
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(code, [46, 8, 9, 27, 13, 110]) !== -1 ||
            // Allow: Ctrl+A
            ( e.ctrlKey === true) || ( e.metaKey === true) ||
            // Allow: home, end, left, right, down, up
            (code >= 35 && code <= 40)) {
                // let it happen, don't do anything
                return;
            }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (code < 48 || code > 57)) && (code < 96 || code > 105)) {
            e.preventDefault();
        }
    });

    $('#earningsTable').DataTable( {
        "responsive": {
            details: {
                type: 'column'
            }
        },
            "columnDefs": [ {
                className: 'control',
                    orderable: false,
                    targets:   0
            } ],
            "order": [ 1, 'asc' ]
    } );

    // Filters
    $('#yearmonth').on('change', function(){
        var filter = $(this).val();
        $('#earningsTable').DataTable().search(filter).draw();
    });

    /*
    $('#bankTable').DataTable( {
        "responsive": {
            details: {
                type: 'column'
            }
        },
            "columnDefs": [ {
                className: 'control',
                    orderable: false,
                    targets:   0
            } ],
            "order": [ 1, 'asc' ]
    } );

    $('#paymentTable').DataTable( {
        "responsive": {
            details: {
                type: 'column'
            }
        },
            "columnDefs": [ {
                className: 'control',
                    orderable: false,
                    targets:   0
            } ],
            "order": [ 1, 'asc' ]
    } );

    $('.btn_deposit').click(function(){
        window.location.href='<?php echo site_url("/agency_management/agent_deposit/" . $agent_id); ?>';
    });
    $('.btn_withdraw').click(function(){
        window.location.href='<?php echo site_url("/agency_management/agent_withdraw/" . $agent_id); ?>';
    });
    */

    <?php if ($this->permissions->checkPermissions('agent_admin_action') && $this->utils->getConfig('enabled_show_password')) {?>
    $('#_password_label').dblclick(function(){
        alert("<?php echo $hide_password; ?>");
    });
    <?php }?>

} );
    // DataTables }}}2

// others {{{2
$('#sub_option_submit').on('click', function(){
    $('#form_sub_option').submit();
});

// prevent negative value
$('input[type="number"]').on('change', function(){
    if($(this).val() < 0) $(this).val(0);
});


// START DEFAULT SHARES JS ===============================================

$('#form_option_1 input').on('change', function(){
    // enable save button
    $('#btn_save').prop('disabled', false);
    $('#option_1_submit').prop('disabled', false);
    // get option and enable it
    var option = $(this).closest('.panel').find('.panel-heading').find('.option').prop('checked', 'checked');
});

$('#form_option_2 input').on('change', function(){
    // enable save button
    $('#btn_save').prop('disabled', false);
    $('#option_2_submit').prop('disabled', false);

    // get option and enable it
    var option = $(this).closest('.panel').find('.panel-heading').find('.option').prop('checked', 'checked');
});

$('.btn_collapse').on('click', function(){
    // get current state
    var child = $(this).find('i');

    // change ui
    if(child.hasClass('glyphicon-chevron-down')) {
        child.removeClass('glyphicon-chevron-down');
        child.addClass('glyphicon-chevron-up')
    } else {
        child.removeClass('glyphicon-chevron-up');
        child.addClass('glyphicon-chevron-down')
    }
});

$('#btn_save').on('click', function(){
    // check which option is selected
    var option = $('input[name=option]:checked').val();

    switch(option) {
    case 'option1':
        $('#option_1_submit').trigger('click');
        break;
    case 'option2':
        $('#option_2_submit').trigger('click');
        break;
    }
});

// END DEFAULT SHARES JS ====================================================

// START DEFAULT SUB SHARES JS =============================================
var sub_level_shares = 0;

$('#sub_agent_main_panel_body input').on('change', function(){
    $('#btn_save_sub').prop("disabled", false);
});

$('#sub_level').on('change', function(){
    var levels = $(this).val();
    var label = $('#sub_level_label');
    var container = $('#sub_level_container');
    container.html("");

    var tpl_singular = $('#single_sub_level').html();
    var tpl_multiple = $('#multi_sub_level').html();

    if(levels == 0) {
        label.addClass('hide');

        $('#btn_save_sub_1').prop('disabled', 'disabled');
        $('#sub_option2_body input[type="submit"]').prop('disabled', false);
    } else if(levels == 1) {
        label.removeClass('hide');
        // get template
        var html = tpl_singular;

        // append single row template
        container.html(html);
    } else if(levels > 1) {
        label.removeClass('hide');

        // append multiple template
        for(i=sub_level_shares; i<levels; i++) {
            var html = Mustache.render(tpl_multiple, {counter: i + 1});
            container.append(html);
        }
    }
});

// main option
$('#btn_sub_allow').parent().on('click', function(){
    $('#btn_group_sub_allowed').removeClass('hide');
    $('.sub-agent-options').removeClass('hide');
});
$('#btn_sub_anallow').parent().on('click', function(){
    $('btn_group_sub_allowed label').removeClass('active');
    $('btn_group_sub_allowed input').prop('checked', false);
    $('#btn_group_sub_allowed').addClass('hide');
    $('.sub-agent-options').addClass('hide');

    $('#sub_option1_body input[type="number"]').prop('disabled', 'disabled');
    $('#sub_option2_body input[type="number"]').prop('disabled', 'disabled');
});

// sub option
$('#btn_sub_all').parent().on('click', function(){
    $('#sub_option1_body input[type="number"]').prop('disabled', false);
    $('#sub_option2_body input[type="number"]').prop('disabled', false);
});

$('#btn_sub_manual').parent().on('click', function(){
    $('#sub_option1_body input[type="number"]').prop('disabled', false);
    $('#sub_option2_body input').prop('disabled', 'disabled');
});

$('#btn_sub_link').parent().on('click', function(){
    $('#sub_option1_body input').prop('disabled', 'disabled');
    $('#sub_option2_body input[type="number"]').prop('disabled', false);
});

// main setting
$('#sub_option1_body input').on('change', function(){
    $('#btn_save_sub').prop('disabled', false);
    $('#form_sub_option input[type="submit"]').prop('disabled', false);
    $('#form_sub_option input[type="hidden"]').prop('disabled', false);
});
$('#sub_option2_body input').on('change', function(){
    $('#btn_save_sub').prop('disabled', false);
    $('#form_sub_option input[type="submit"]').prop('disabled', false);
    $('#form_sub_option input[type="hidden"]').prop('disabled', false);
});

// trigger save
$('#btn_save_sub').on('click', function(){
    $('#sub_option_submit').trigger('click');
});
// others }}}2

// tracking code {{{2
function unlock_tracking_code(){
    $('.btn_update_tracking_code').show();
    $('#random_code_lock').hide();
    $('#tracking_code').prop("disabled",false);

    $.ajax({
        'url' : '/agency_management/log_unlock_trackingcode',
        'type' : 'GET',
        'success' : function(data) {
                        // console.log("success");
                    }
    },'json');
}

function cancel_update_tracking_code(){

    $('.btn_update_tracking_code').hide();
    $('#random_code_lock').show();
    $('#tracking_code').prop("disabled",true);

}

function randomCode(len)
{
    var text = '';

    var charset = "abcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < len; i++ ) {
        text += charset.charAt(Math.floor(Math.random() * charset.length));
    }

    $('#tracking_code').val(text);
}


function randomNumber(len)
{
    var text = '';

    var charset = "0123456789";

    for( var i=0; i < len; i++ ) {
        text += charset.charAt(Math.floor(Math.random() * charset.length));
    }

    $('#tracking_code').val(text);
}

<?php if ($this->permissions->checkPermissions('edit_agency_domain')) {?>

function editAdditionalDomain(agentTrackingId, agent_domain){
    BootstrapDialog.show({
        title: '<?php echo lang('Agent Additional Domain'); ?>',
        message: '<form method="POST" class="frm_edit_add_domain_'+agentTrackingId+'" action="<?php echo site_url("/agency_management/change_additional_agent_domain/".$agent_id); ?>/'
            +agentTrackingId+'"><?php echo lang("Agent Additional Domain"); ?>: <input type="text" name="agent_domain" class="form-control" value="'+agent_domain+'"></form>',
        spinicon: 'fa fa-spinner fa-spin',
        buttons: [{
            icon: 'fa fa-save',
            label: '<?php echo lang('Save'); ?>',
            cssClass: 'btn-primary',
            autospin: true,
            action: function(dialogRef){
                dialogRef.enableButtons(false);
                dialogRef.setClosable(false);
                // utils.safelog(dialogRef);

                var frm=dialogRef.getModalBody().find('.frm_edit_add_domain_'+agentTrackingId);
                frm.submit();
                // dialogRef.getModalBody().html('Dialog closes in 5 seconds.');
                // setTimeout(function(){
                //     dialogRef.close();
                // }, 5000);
            }
        }, {
            label: '<?php echo lang('Close'); ?>',
            action: function(dialogRef){
                dialogRef.close();
            }
        }]
    });

}

function removeAdditionalDomain(agentTrackingId, agent_domain){
    BootstrapDialog.show({
        title: '<?php echo lang('Agent Additional Domain'); ?>',
        message: '<form method="POST" class="frm_remove_add_domain_'+agentTrackingId+'" action="<?php echo site_url("/agency_management/remove_additional_agent_domain/".$agent_id); ?>/'
            +agentTrackingId+'"><?php echo lang("Agent Additional Domain"); ?>: <input type="text" class="form-control" disabled="disabled" value="'+agent_domain+'"></form>',
        spinicon: 'fa fa-spinner fa-spin',
        buttons: [{
            icon: 'fa fa-save',
            label: '<?php echo lang('Remove'); ?>',
            cssClass: 'btn-danger',
            autospin: true,
            action: function(dialogRef){
                dialogRef.enableButtons(false);
                dialogRef.setClosable(false);
                // utils.safelog(dialogRef);

                var frm=dialogRef.getModalBody().find('.frm_remove_add_domain_'+agentTrackingId);
                frm.submit();
                // dialogRef.getModalBody().html('Dialog closes in 5 seconds.');
                // setTimeout(function(){
                //     dialogRef.close();
                // }, 5000);
            }
        }, {
            label: '<?php echo lang('Close'); ?>',
            action: function(dialogRef){
                dialogRef.close();
            }
        }]
    });

}

function newAdditionalDomain(){
    BootstrapDialog.show({
        title: '<?php echo lang('Agent Additional Domain'); ?>',
        message: '<form method="POST" class="frm_new_add_domain" action="<?php echo site_url("/agency_management/new_additional_agent_domain/".$agent_id); ?>"><?php echo lang("New Agent Additional Domain"); ?>: <input type="text" name="agent_domain" class="form-control" value=""></form>',
        spinicon: 'fa fa-spinner fa-spin',
        buttons: [{
            icon: 'fa fa-save',
            label: '<?php echo lang('Save'); ?>',
            cssClass: 'btn-primary',
            autospin: true,
            action: function(dialogRef){
                dialogRef.enableButtons(false);
                dialogRef.setClosable(false);
                // utils.safelog(dialogRef);

                var frm=dialogRef.getModalBody().find('.frm_new_add_domain');
                frm.submit();
                // dialogRef.getModalBody().html('Dialog closes in 5 seconds.');
                // setTimeout(function(){
                //     dialogRef.close();
                // }, 5000);
            }
        }, {
            label: '<?php echo lang('Close'); ?>',
            action: function(dialogRef){
                dialogRef.close();
            }
        }]
    });

}

<?php } ?>

<?php if ($this->permissions->checkPermissions('edit_agency_source_code')) {?>

function editSourceCode(agentTrackingId, sourceCode){
    BootstrapDialog.show({
        title: '<?php echo lang('Agent Source Code'); ?>',
        message: '<form method="POST" class="frm_edit_source_code_'+agentTrackingId+'" action="<?php echo site_url("/agency_management/change_source_code/".$agent_id); ?>/'
            +agentTrackingId+'"><?php echo lang("Agent Source Code"); ?>: <input type="text" name="sourceCode" class="form-control" value="'+sourceCode+'"></form>',
        spinicon: 'fa fa-spinner fa-spin',
        buttons: [{
            icon: 'fa fa-save',
            label: '<?php echo lang('Save'); ?>',
            cssClass: 'btn-primary',
            autospin: true,
            action: function(dialogRef){
                dialogRef.enableButtons(false);
                dialogRef.setClosable(false);
                // utils.safelog(dialogRef);

                var frm=dialogRef.getModalBody().find('.frm_edit_source_code_'+agentTrackingId);
                frm.submit();
                // dialogRef.getModalBody().html('Dialog closes in 5 seconds.');
                // setTimeout(function(){
                //     dialogRef.close();
                // }, 5000);
            }
        }, {
            label: '<?php echo lang('Close'); ?>',
            action: function(dialogRef){
                dialogRef.close();
            }
        }]
    });

}

function newSourceCode(){
    BootstrapDialog.show({
        title: '<?php echo lang('Agent Source Code'); ?>',
        message: '<form method="POST" class="frm_new_source_code" action="<?php echo site_url("/agency_management/new_source_code/".$agent_id); ?>"><?php echo lang("New Agent Source Code"); ?>: <input type="text" name="sourceCode" class="form-control" value=""></form>',
        spinicon: 'fa fa-spinner fa-spin',
        buttons: [{
            icon: 'fa fa-save',
            label: '<?php echo lang('Save'); ?>',
            cssClass: 'btn-primary',
            autospin: true,
            action: function(dialogRef){
                dialogRef.enableButtons(false);
                dialogRef.setClosable(false);
                // utils.safelog(dialogRef);

                var frm=dialogRef.getModalBody().find('.frm_new_source_code');
                frm.submit();
                // dialogRef.getModalBody().html('Dialog closes in 5 seconds.');
                // setTimeout(function(){
                //     dialogRef.close();
                // }, 5000);
            }
        }, {
            label: '<?php echo lang('Close'); ?>',
            action: function(dialogRef){
                dialogRef.close();
            }
        }]
    });

}
function removeSourceCode(agentTrackingId, sourceCode){
    BootstrapDialog.show({
        title: '<?php echo lang('Agent Source Code'); ?>',
        message: '<form method="POST" class="frm_remove_source_code" action="<?php echo site_url("/agency_management/remove_source_code/".$agent_id); ?>/'+agentTrackingId+'"><?php echo lang("Agent Source Code"); ?>: <input type="text" disabled="disabled" class="form-control" value="'+sourceCode+'"></form>',
        spinicon: 'fa fa-spinner fa-spin',
        buttons: [{
            icon: 'fa fa-save',
            label: '<?php echo lang('Remove'); ?>',
            cssClass: 'btn-danger',
            autospin: true,
            action: function(dialogRef){
                dialogRef.enableButtons(false);
                dialogRef.setClosable(false);
                // utils.safelog(dialogRef);

                var frm=dialogRef.getModalBody().find('.frm_remove_source_code');
                frm.submit();
                // dialogRef.getModalBody().html('Dialog closes in 5 seconds.');
                // setTimeout(function(){
                //     dialogRef.close();
                // }, 5000);
            }
        }, {
            label: '<?php echo lang('Close'); ?>',
            action: function(dialogRef){
                dialogRef.close();
            }
        }]
    });

}
<?php }?>
    // tracking code }}}2

</script>
<!-- JS }}}1 -->
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agent_information.php
