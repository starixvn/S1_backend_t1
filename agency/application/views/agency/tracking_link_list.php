<div class="panel panel-primary agency_tracking_code">
    <div class="panel-heading">
        <h4 class="panel-title">
            <?=lang('Agent Tracking Code');?>
        </h4>
    </div>

    <div class="panel-body agent_track_panel_body">
        <div class="row">
            <div class="col-md-12">
                <form id="update_agent_tracking_code" action="<?=site_url($controller_name . '/edit_tracking_code/' . $agent_id)?>" method="POST" class="form-inline" onsubmit="return submit_update_tracking_code(this);">
                    <div class="form-group">
                        <label for="tracking_code" class="control-label" style="text-align:right;"><?=lang('Tracking Code');?> </label>
                        <div class="input-group">
                            <input type="text" readonly="readonly" name="tracking_code" id="tracking_code" class="form-control input-sm disabled <?=$this->utils->isEnabledFeature('agent_tracking_code_numbers_only') ? 'number_only' : ''?>"
                                   minlength="4" maxlength="20" value="<?=$agent['tracking_code']; ?>"/>
                            <?=form_error('tracking_code', '<span style="color:#CB3838;">'); ?>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-default btn-sm btn_update_tracking_code" style="display: none;" id="random_code"><i class="fa fa-calculator"></i> <?=lang('aff.ai38');?></button>
                            <input type="submit" class="btn btn-info btn_update_tracking_code" style="display: none;" value="<?=lang('Save');?>"/>
                            <input type="button" class="btn btn-danger btn-sm btn_update_tracking_code" style="display: none;" onclick="cancel_update_tracking_code()" value="<?=lang('Cancel');?>"/>

                            <a href="javascript:void(0)" class="btn btn-default btn-sm" id="random_code_lock" onclick="unlock_tracking_code();">
                                <?=lang('icon.locked') . "" . lang('system.word56');?>
                            </a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </form>
            </div>
        </div>

        <hr />

        <div class="row">
            <?php //old tracking link ?>
            <?php if(!$enabled_wechat_links_on_agency){ ?>
            <div class="col-md-12" style="overflow: auto;">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="col-md-10"><?=lang('aff.ai41');?></th>
                            <th class="col-md-2"><?=lang('Action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($agent_domains) && !empty($agent['tracking_code'])): ?>
                    <?php
                        foreach($agent_domains as $key => $domain_name) {
                            $url  = $this->utils->isEnabledFeature('use_https_for_agent_tracking_links') ? 'https://' : 'http://';
                            $url .= $domain_name;
                            $url .= $this->utils->isEnabledFeature('use_new_agent_tracking_link_format') ? '?ag=' : '/ag/'; # OGP-6432
                            $url .= $agent['tracking_code'];
                    ?>
                            <tr>
                                <td><a href="<?=$url?>" target="_blank"><?=$url?></a></td>
                                <td>
                                    <a href="javascript:void(0)" class="btn btn-primary btn-xs btn-copy" data-clipboard-text="<?=$url?>"><?=lang('Copy')?></a>
                                </td>
                            </tr>
                    <?php } ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" style="text-align:center"><span class="help-block"><?php echo lang('N/A'); ?></span></td>
                        </tr>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12" style="overflow: auto;">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="col-md-10"><?=lang('Sub Agent Link');?></th>
                            <th class="col-md-2"><?=lang('Action'); ?></th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if(!empty($agent['tracking_code']) && !empty($agent['sub_link'])): ?>
                        <tr>
                            <td><a href="<?=$agent['sub_link']?>" target="_blank"><?=$agent['sub_link'];?></a></td>
                            <td>
                                <a href="javascript:void(0)" class="btn btn-primary btn-xs btn-copy" data-clipboard-text="<?=$agent['sub_link']?>"><?=lang('Copy')?></a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="2"><?php echo lang('N/A'); ?></td>
                        </tr>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
            <?php }?>
        </div>

        <div class="row agent_source_code_list_container">
            <div class="col-md-12" style="overflow: auto;">
                <a href="javascript:void(0)" class="btn btn-primary btn-sm pull-right" onclick="newSourceCode()"><?=lang('New Agent Source Code'); ?></a>
                <table class="table table-striped agent_source_code_list">
                    <thead>
                        <tr>
                            <th class="col-md-2"><?=lang('Agent Source Code'); ?></th>
                            <th class="col-md-1"><?=lang('Bonus Group'); ?></th>
                            <th class="col-md-1"><?=lang('agency.tracking_link.rebate_rate'); ?></th>
                            <th class="col-md-1"><?=lang('Type'); ?></th>
                            <th class="col-md-3"><?=lang('Link Example'); ?></th>
                            <th class="col-md-2"><?=lang('Action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($agent_source_code_list)): ?>
                        <?php foreach($agent_source_code_list as $source_code): ?>
                            <?php $url = (!empty($first_domain)) ? $tracking_link_protocol . $first_domain . AGENT_TRACKING_BASE_URL . $agent['tracking_code'] . '/' . $source_code['tracking_source_code'] : ""; ?>
                            <tr>
                                <td><?=$source_code['tracking_source_code']?></td>
                                <td><?=empty($source_code['bonus_rate']) ? lang('N/A') : $source_code['bonus_rate']?></td>
                                <td><?=empty($source_code['rebate_rate']) ? lang('N/A') : $source_code['rebate_rate']?></td>
                                <td><?=$source_code['player_type']?></td>
                                <td><a href="<?=$url?>" target="_blank"><?=$url?></a></td>
                                <td>
                                    <a href="javascript:void(0)" class="btn btn-primary btn-xs" onclick="generate_source_code_shorturl(<?=$source_code['id']; ?>, '<?=$agent['tracking_code']; ?>', '<?=$tracking_link_protocol?>', '<?=urlencode($source_code['shorturl'])?>');"><?=lang('Shorturl')?></a>
                                    <a href="javascript:void(0)" class="btn btn-primary btn-xs btn-copy" data-clipboard-text=""><?=lang('Copy')?></a>
                                    <a href="javascript:void(0)" class="btn btn-primary btn-xs" onclick="editSourceCode(<?=$source_code['id']; ?>, '<?=$source_code['bonus_rate']?>', '<?=$source_code['player_type']?>', '<?=$source_code['tracking_source_code']?>', '<?=$source_code['rebate_rate']?>')"><?=lang('Edit'); ?></a>
                                    <a href="javascript:void(0)" class="btn btn-primary btn-xs" onclick="removeSourceCode(<?=$source_code['id']; ?>, '<?=$source_code['bonus_rate']?>', '<?=$source_code['player_type']?>', '<?=$source_code['tracking_source_code']?>', '<?=$source_code['rebate_rate']?>')"><?=lang('Remove'); ?></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if(!$this->utils->isEnabledFeature('hidden_domain_tracking')): ?>
        <div class="row">
            <div class="col-md-12" style="overflow: auto;">
                <a href="javascript:void(0)" class="btn btn-primary" onclick="newAdditionalDomain()"><?=lang('New Agent Additional Domain'); ?></a>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="col-md-3"><?php echo lang('Agent Domain');?></th>
                            <th class="col-md-6"><?=lang('Link Example'); ?></th>
                            <th class="col-md-3"><?php echo lang('Action');?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($agent_additional_domain_list)): ?>
                    <?php foreach($agent_additional_domain_list as $domain): ?>
                    <?php $url = $tracking_link_protocol . $domain['tracking_domain']; ?>
                    <tr>
                        <td><?=$domain['tracking_domain']?></td>
                        <td><a href="<?=$url?>" target='_blank'><?=$url?></a></td>
                        <td>
                            <a href="javascript:void(0)" class="btn btn-primary btn-xs btn-copy" data-clipboard-text="<?=$url?>"><?=lang('Copy')?></a>
                            <a href="javascript:void(0)" class="btn btn-primary btn-xs" onclick="editAdditionalDomain(<?=$domain['id'];?>, '<?=$domain['tracking_domain'];?>')"><?=lang('Edit');?></a>
                            <a href="javascript:void(0)" class="btn btn-primary btn-xs" onclick="removeAdditionalDomain(<?=$domain['id'];?>, '<?=$domain['tracking_domain'];?>')"><?=lang('Remove');?></a>
                        </td>
                    </tr>
                    <?php endforeach ?>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div> <!--end panel-body-->
</div> <!--end panel-->

<div class="templates hidden">
    <div class="template tpl_tracking_source_code_link">
        <?=$tracking_link_protocol . '{DOMAIN}' . AGENT_TRACKING_BASE_URL . $agent['tracking_code'] . '/{CODE}'?>
    </div>
    <div class="template tpl_datatable_toolbar">
        <div class="form-group">
            <label class="control-label"><?=lang('Domain List')?></label>
            <select class="form-control">
                <?php foreach($domain_list_for_agent as $key => $domain_value): ?>
                    <option value="<?=$domain_value['domain_name']?>"><?=$domain_value['domain_name']?></option>
                <?php endforeach ?>
            </select>
        </div>
    </div>
    <div class="template tpl_generate_source_code_shorturl">
        <div class="form-group">
            <label class="control-label"><?=lang('Shorturl'); ?></label>
            <input type="text" class="form-control agency_source_code_shorturl_field" readonly="readonly" />
        </div>
    </div>
    <div class="template tpl_new_source_code">
        <form method="POST" class="frm_new_source_code" action="/<?=$controller_name?>/new_source_code/<?=$agent_id?>">
            <?php if($enabled_wechat_links_on_agency){ ?>
            <div class="form-group">
                <label class="control-label"><?=lang('lang.bonus.agency'); ?></label>
                <select name="bonus_rate" class="form-control agency_bonus_rate_field">
                    <?php foreach($agency_tracking_link_bonus_rate_list as $value): ?>
                    <option value="<?=$value?>"><?=$value?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="form-group">
                <label class="control-label"><?=lang('agency.tracking_link.rebate_rate')?></label>
                <input type="number" name="rebate_rate" class="form-control agency_rebate_rate_field" value="" step="0.0001" min="0" />
            </div>
            <div class="form-group">
                <label class="control-label"><?=lang('Type'); ?></label>
                <select name="player_type" class="form-control agency_player_type_field">
                    <option value="<?=AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_PLAYER?>"><?=lang('Player')?></option>
                    <option value="<?=AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_AGENT?>"><?=lang('Agent')?></option>
                </select>
            </div>
            <?php }else{?>
            <?php //old tracking link ?>
                <input type="hidden" name="bonus_rate" value="0">
                <input type="hidden" name="rebate_rate" value="0">
            <input type="hidden" name="player_type" value="<?=AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_PLAYER?>">
            <?php }?>
            <div class="form-group">
                <label class="control-label"><?=lang('New Agent Source Code')?></label>
                <input type="text" name="sourceCode" class="form-control agency_source_code_field" value="" />
            </div>
        </form>
    </div>
    <div class="template tpl_edit_source_code">
        <form method="POST" class="frm_edit_source_code" action="/<?=$controller_name?>/change_source_code/<?=$agent_id?>">
            <?php if($enabled_wechat_links_on_agency){ ?>
            <div class="form-group">
                <label class="control-label"><?=lang('lang.bonus.agency'); ?></label>
                <select name="bonus_rate" class="form-control agency_bonus_rate_field">
                    <?php foreach($agency_tracking_link_bonus_rate_list as $value): ?>
                        <option value="<?=$value?>"><?=$value?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="form-group">
                <label class="control-label"><?=lang('agency.tracking_link.rebate_rate')?></label>
                <input type="number" name="rebate_rate" class="form-control agency_rebate_rate_field" value="" step="0.0001" min="0" />
            </div>
            <div class="form-group">
                <label class="control-label"><?=lang('Type'); ?></label>
                <select name="player_type" class="form-control agency_player_type_field">
                    <option value="<?=AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_PLAYER?>"><?=lang('Player')?></option>
                    <option value="<?=AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_AGENT?>"><?=lang('Agent')?></option>
                </select>
            </div>
            <?php }else{?>
            <input type="hidden" name="bonus_rate" value="0">
            <input type="hidden" name="rebate_rate" value="0">
            <input type="hidden" name="player_type" value="<?=AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_PLAYER?>">
            <?php }?>
            <div class="form-group">
                <label class="control-label"><?=lang('Agent Source Code')?></label>
                <input type="text" name="sourceCode" class="form-control agency_source_code_field" value="" />
            </div>
        </form>
    </div>
    <div class="template tpl_remove_source_code">
        <form method="POST" class="frm_remove_source_code" action="/<?=$controller_name?>/remove_source_code/<?=$agent_id?>">
            <div class="form-group">
                <label class="control-label"><?=lang('lang.bonus.agency'); ?></label>
                <input type="number" name="bonus_rate" class="form-control agency_bonus_rate_field" step="any" min="0" value="" />
            </div>
            <div class="form-group">
                <label class="control-label"><?=lang('agency.tracking_link.rebate_rate')?></label>
                <input type="number" name="rebate_rate" class="form-control agency_rebate_rate_field" value="" step="0.0001" min="0" />
            </div>
            <div class="form-group">
                <label class="control-label"><?=lang('Type'); ?></label>
                <select name="player_type" class="form-control agency_player_type_field">
                    <option value="<?=AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_PLAYER?>"><?=lang('Player')?></option>
                    <option value="<?=AGENCY_TRACKING_SOURCE_CODE_PLAYER_TYPE_AGENT?>"><?=lang('Agent')?></option>
                </select>
            </div>
            <div class="form-group">
                <label class="control-label"><?=lang('Agent Source Code')?></label>
                <input type="text" name="sourceCode" class="form-control agency_source_code_field" value="" />
            </div>
        </form>
    </div>
    <div class="template tpl_new_add_domain">
        <form method="POST" class="frm_new_add_domain" action="/<?=$controller_name?>/new_additional_agent_domain/<?=$agent_id?>">
            <?=lang('New Agent Additional Domain')?>: <input type="text" name="agent_domain" class="form-control agency_domain_field" value="">
        </form>
    </div>
    <div class="template tpl_edit_add_domain">
        <form method="POST" class="frm_edit_add_domain" action="/<?=$controller_name?>/change_additional_agent_domain/<?=$agent_id?>">
            <?=lang('Agent Additional Domain')?>: <input type="text" name="agent_domain" class="form-control agency_domain_field" value="">
        </form>
    </div>
    <div class="template tpl_remove_add_domain">
        <form method="POST" class="frm_remove_add_domain" action="/<?=$controller_name?>/remove_additional_agent_domain/<?=$agent_id?>">
            <?=lang('Agent Additional Domain')?>: <input type="text" disabled="disabled" class="form-control agency_domain_field" value="">
        </form>
    </div>
</div>

<script type="text/javascript">
    var controller_name = "<?=$controller_name?>";
</script>