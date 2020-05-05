
<!--
<?php
echo json_encode($commonSettings,JSON_PRETTY_PRINT);
?>
 -->
<!-- custom style -->
<style>
	.btn_collapse {
		margin-left: 10px;
	}
    .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px; }
    .toggle.ios .toggle-handle { border-radius: 20px; }
</style>


<!-- START DEFAULT SHARES -->
<div class="row">
    <div class="col-md-6">
    	<div class="panel panel-primary">

            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fa fa-cog"></i> <?=lang('aff.ai101');?>
                </h4>
            </div><!-- end panel-heading -->

            <div class="panel-body collapse in" id="affiliate_main_panel_body">
            	<div class="col-md-12">
					<form id="form_operator" method="POST" action="<?php echo site_url('/affiliate_management/save_common_setup/operator_settings');?>">
						<?=$this->load->view('affiliate_management/setup/operator_settings', array('settings' => $commonSettings), TRUE); ?>
					</form>
				</div>
			</div><!-- end panel-body -->
            <!--div class="panel-footer"></div-->
        </div>
    </div>
<!-- END DEFAULT AFFILIATE SHARES -->

<!-- START DAFAULT AFFILIATE SHARES -->
    <div class="col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title pull-left">
                    <i class="fa fa-cog"></i> <?=lang('aff.sb9');?>
                </h4>
				<div class="clearfix"></div>
            </div><!-- end panel-heading -->

            <div class="panel-body collapse in" id="affiliate_main_panel_body">
            	<div class="col-md-12">
					<form id="form_option_1"  method="POST" action="<?php echo site_url('/affiliate_management/save_common_setup/commission_setup');?>">
						<div class="row">
							<div class="col-md-12">
								<label><b><?=lang('a_header.affiliate') . ' ' . lang('lang.settings');?></b></label>
								<fieldset>
									<br>
									<div class="form-group">
										<div class="input-group">
									      	<div class="input-group-addon"><?php echo lang('Total Active Players'); ?></div>
											<input type="number" class="form-control" name="totalactiveplayer" value="<?php echo $commonSettings['totalactiveplayer']; ?>" required="required" min="0" step="1"/>
									      	<div class="input-group-addon">#</div>
									    </div>
									</div>
								</fieldset>
								<br>
								<div class="form-group">
									<label><b><?php echo lang('Betting Amount');?></b></label>
									<fieldset>
										<br>
										<div class="form-row">

											<div class="form-group col-xs-12">
												<?=lang('earnings.minBetting');?>
											</div>

											<?php foreach ($game as $g) { ?>
							                <div class="form-group col-xs-4">
												<label class="control-label"><?=$g['system_code'];?></label>
												<input type="number" name="provider_betting_amount_<?php echo $g['id'];?>" class="form-control input-sm" value="<?php echo $commonSettings['provider_betting_amount'][$g['id']];?>" min="0"/>
											</div>
											<?php }?>

											<div class="form-group col-xs-12">
												<p class="help-block well well-sm"><?php echo lang('Zero or Empty means ignore this'); ?></p>
											</div>

											<div class="form-group col-xs-12">
												<div class="input-group">
											      	<div class="input-group-addon"><?php echo lang('Minimum Total Betting');?></div>
													<input type="number" class="form-control input-sm" name="minimumBetting" value="<?php echo $commonSettings['minimumBetting']; ?>" min="0"/>
											    </div>
										    </div>
											<?php foreach ($game as $g) { ?>
							                <div class="form-group col-xs-4">
												<label class="control-label">
													<input type="checkbox" name="provider[]" value="<?=$g['id'];?>" <?php echo in_array($g['id'], $commonSettings['provider']) ? "checked='checked'" : "";?>>
													<?=$g['system_code'];?>
												</label>
											</div>
											<?php }?>
									    </div>

									    <?php if ($this->utils->isEnabledFeature('switch_to_ibetg_commission')) { ?>
										<div class="row">
											<div class="col-xs-12">
												<div class="input-group">
											      	<div class="input-group-addon"><?php echo lang('Minimum Total Betting Times');?></div>
													<input type="text" class="form-control input-sm" name="minimumBettingTimes" maxlength="10" value="<?php echo $commonSettings['minimumBettingTimes']; ?>" />
											    </div>
										    </div>
									    </div>
									    <?php } ?>
									    
						            </fieldset>
					            </div>
					            <label><b><?=lang('aff.ai96');?></b></label>
								<fieldset>
									<br>
									<div class="form-group">
										<div class="input-group">
									      	<div class="input-group-addon"><i class="fa fa-money"></i></div>
											<input type="text" class="form-control amount_only" name="minimumDeposit" maxlength="15" value="<?php echo $commonSettings['minimumDeposit']; ?>" />
									    </div>
									</div>
								</fieldset>
								<br>
								<p class="help-block well"><b><?php echo lang('Active Player'); ?></b> 
									<?php if ($this->utils->isEnabledFeature('affiliate_commision_check_deposit_and_bet')) { ?>
									<?=lang('aff.ai103');?>
								<?php }  else { ?>
									<?=lang('aff.ai91');?>
								<?php } ?>
								</p>
							</div><!-- end col-md-6 -->
						</div><!-- end row -->
						<button type="submit" id="option_1_submit" class="btn pull-right <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>"><i class="fa fa-floppy-o"></i> <?=lang('sys.vu70');?></button>
					</form>
				</div>
			</div><!-- end panel-body -->
            <!--div class="panel-footer"></div-->
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title pull-left">
                    <i class="fa fa-cogs"></i> <?=lang('aff.sb10');?>
                </h4>

				<div class="clearfix"></div>
            </div><!-- end panel-heading -->
           <div class="panel-body collapse in" id="sub_affiliate_main_panel_body" >
				<form id="frm_sub_option" method="POST" action="<?php echo site_url('/affiliate_management/save_common_setup/sub_affiliate_settings');?>" >
					<input type="hidden" id="sub_level" name="sub_level" value="<?php echo $commonSettings['sub_level']; ?>">

				<div class="sub-affiliate-options">
	            	<!-- sub option -->
					<div class="col-xs-12" id="btn_group_sub_allowed">
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="row">
					                <div class="col-xs-6">
										<div class="form-group">
											<label for="pt">
												<input type="checkbox" name="manual_open" value="true" <?php echo $commonSettings['manual_open'] ?'checked="checked"' : "";?> <?php if ( ! $this->permissions->checkPermissions('affiliate_admin_action')) echo 'disabled="disabled"' ?>>
												<?=lang('aff.ai94');?>
											</label>
										</div>
									</div>
					                <div class="col-xs-6">
										<div class="form-group">
											<label for="pt">
												<input type="checkbox" name="sub_link" value="true" <?php echo $commonSettings['sub_link'] ?'checked="checked"' : "";?> <?php if ( ! $this->permissions->checkPermissions('affiliate_admin_action')) echo 'disabled="disabled"' ?>>
												<?=lang('aff.ai95');?>
											</label>
										</div>
									</div>
								</div>
							</div>
							<div class="panel-footer"></div>
						</div>
					</div>
				</div>

				<!-- <div class="col-xs-12" id="affiliate_commission">
					<div class="panel panel-default">
						<div class="panel-heading"><?=lang('Affiliate Commission');?></div>
						<table class="table">
                            <thead>
                                <tr>
                                    <th><?=lang('Tier');?></th>
                                    <th><?=lang('Active Player');?></th>
                                    <th><?=lang('Commission %');?></th>
                                    <th><?=lang('USD');?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td><input type="text" class="form-control" placeholder="Active Player"> and Above</td>
                                    <td><input type="text" class="form-control" placeholder="Commission %"></td>
                                    <td><input type="text" class="form-control" placeholder="Minimum Gross Bet"> - <input type="text" class="form-control" placeholder="Maximum Gross Bet"></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td><input type="text" class="form-control" placeholder="Active Player"> and Above</td>
                                    <td><input type="text" class="form-control" placeholder="Commission %"></td>
                                    <td><input type="text" class="form-control" placeholder="Minimum Gross Bet"> - <input type="text" class="form-control" placeholder="Maximum Gross Bet"></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td><input type="text" class="form-control" placeholder="Active Player"> and Above</td>
                                    <td><input type="text" class="form-control" placeholder="Commission %"></td>
                                    <td><input type="text" class="form-control" placeholder="Minimum Gross Bet"> - <input type="text" class="form-control" placeholder="Maximum Gross Bet"></td>
                                </tr>
                            </tbody>
                        </table>
					</div>
				</div> -->

				<div class="sub-affiliate-options">
		            <!-- SUB-OPTION 1 -->
		            <div class="col-md-12">
						<div class="panel panel-default">
							<div class="panel-heading"><?=lang('aff.ai99');?></div>
							<div class="panel-body">
								<div class="row" id="sub_level_container">
									<?php $sub_max = $commonSettings['sub_level']; ?>
									<?php if ($sub_max > 0) { ?>
										<?php $sub_levels = $commonSettings['sub_levels']; ?>
										<?php foreach ($sub_levels as $key => $value) { ?>
											<div class="col-md-6">
												<div class="form-group">
													<div class="input-group">
												      	<div class="input-group-addon"><?=lang('lang.level');?> <?php echo $key + 1; ?>:</div>
														<input type="text" class="form-control amount_only" name="sub_levels[]" id="total_shares"
															value="<?php echo $value; ?>" <?php if ( ! $this->permissions->checkPermissions('affiliate_admin_action')) echo 'disabled="disabled"' ?>/>
												      	<div class="input-group-addon">%</div>
												    </div>
												</div>
											</div>
										<?php } ?>
									<?php } ?>
								</div>
							</div>
							<div class="panel-footer"></div>
						</div>

							<div class="row">
								<div class="col-md-12">
									<br>
									<div class="btn-group pull-right" role="group">
										<button type="submit" id="sub_option_submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-portage' : 'btn-primary'?>" <?php if ( ! $this->permissions->checkPermissions('affiliate_admin_action')) echo 'disabled="disabled"' ?>>
											<i class="fa fa-floppy-o"></i> <?=lang('sys.vu70');?></button>
									</div>
									<div class="clearfix"></div>
								<div>
							</div>
						</div>
					</div>
	    		</form>
            </div><!-- end panel-body -->
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="formula-modal">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?=lang('Affiliate Commission Formula')?></h4>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('Close');?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- END DEFAULT AFFILIATE SHARES -->
<script type="text/javascript">

	(function($){

        $('#gameTree').jstree({
          'core' : {
            'data' : {
              "url" : "<?php echo site_url('/api/get_game_tree_by_pub_affiliate_setting'); ?>",
              "dataType" : "json" // needed only if you do not supply JSON headers
            }
          },
          "input_number":{
            "form_sel": '#form_operator'
          },
          "checkbox":{
            "tie_selection": false,
          },
          "plugins":[
            "search","checkbox","input_number"
          ]
        });

        $('#form_operator').submit(function(e){
          var selected_game=$('#gameTree').jstree('get_checked');
          // if(selected_game.length>0){
            $('#form_operator input[name=selected_game_tree]').val(selected_game.join());
            $('#gameTree').jstree('generate_number_fields');
          // }else{
          // }
        });

        $('input[name="paymentSchedule"]').change(function() {
    		$('#paymentDay').prop('disabled', ! ($(this).val() == 'monthly' && this.checked))
        });

	})(jQuery);

	// $('#sub_option_submit').on('click', function(){
	// 	$('#frm_sub_option').submit();
	// });

	// prevent negative value
	$('input[type="number"]').on('change', function(){
		if($(this).val() < 0) $(this).val(0);
	});


	// START DEFAULT AFFILIATE SHARES JS ===============================================

	// $('#form_option_1 input').on('change', function(){
	// 	// enable save button
	// 	$('#btn_save').prop('disabled', false);
	// 	$('#option_1_submit').prop('disabled', false);
	// 	// get option and enable it
	// 	var option = $(this).closest('.panel').find('.panel-heading').find('.option').prop('checked', 'checked');
	// });

	// $('#form_option_2 input').on('change', function(){
	// 	// enable save button
	// 	$('#btn_save').prop('disabled', false);
	// 	$('#option_2_submit').prop('disabled', false);

	// 	// get option and enable it
	// 	var option = $(this).closest('.panel').find('.panel-heading').find('.option').prop('checked', 'checked');
	// });

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

	// $('#btn_save').on('click', function(){
	// 	// check which option is selected
	// 	var option = $('input[name=option]:checked').val();

	// 	switch(option) {
	// 		case 'option1':
	// 			$('#option_1_submit').trigger('click');
	// 		break;
	// 		case 'option2':
	// 			$('#option_2_submit').trigger('click');
	// 		break;
	// 	}
	// });

	// END DEFAULT AFFILIATE SHARES JS ====================================================

	// START DEFAULT SUB AFFILIATES SHARES JS =============================================

	// var sub_levels = 0;

	// $('#sub_affiliate_main_panel_body input').on('change', function(){
	// 	$('#btn_save_sub').prop("disabled", false);
	// });

	// main option
	// $('#btn_sub_allow').parent().on('click', function(){
	// 	$('#btn_group_sub_allowed').removeClass('hide');
	// 	$('.sub-affiliate-options').removeClass('hide');
	// 	$('#sub_option_submit').prop('disabled', false);
	// });
	// $('#btn_sub_anallow').parent().on('click', function(){
	// 	$('btn_group_sub_allowed label').removeClass('active');
	// 	$('btn_group_sub_allowed input').prop('checked', false);
	// 	$('#btn_group_sub_allowed').addClass('hide');
	// 	$('.sub-affiliate-options').addClass('hide');
	// 	$('#sub_option_submit').prop('disabled', false);

	// 	// $('#sub_option1_body input[type="number"]').prop('disabled', 'disabled');
	// 	// $('#sub_option2_body input[type="number"]').prop('disabled', 'disabled');
	// });

	// sub option
	// $('#btn_sub_all').parent().on('click', function(){
	// 	$('#sub_option1_body input[type="number"]').prop('disabled', false);
	// 	$('#sub_option2_body input[type="number"]').prop('disabled', false);
	// });

	// $('#btn_sub_manual').parent().on('click', function(){
	// 	$('#sub_option1_body input[type="number"]').prop('disabled', false);
	// 	// $('#sub_option2_body input').prop('disabled', 'disabled');
	// });

	// $('#btn_sub_link').parent().on('click', function(){
	// 	// $('#sub_option1_body input').prop('disabled', 'disabled');
	// 	$('#sub_option2_body input[type="number"]').prop('disabled', false);
	// });

	// main setting
	// $('#sub_option1_body input').on('change', function(){
	// 	$('#btn_save_sub').prop('disabled', false);
	// 	$('#frm_sub_option input[type="submit"]').prop('disabled', false);
	// 	$('#frm_sub_option input[type="hidden"]').prop('disabled', false);
	// });
	// $('#sub_option2_body input').on('change', function(){
	// 	$('#btn_save_sub').prop('disabled', false);
	// 	$('#frm_sub_option input[type="submit"]').prop('disabled', false);
	// 	$('#frm_sub_option input[type="hidden"]').prop('disabled', false);
	// });

	// trigger save
	// $('#btn_save_sub').on('click', function(){
	// 	$('#sub_option_submit').trigger('click');
	// });

	// END DEFAULT SUB AFFILIATES SHARES JS ===============================================


	// START OPERATOR SETTINGS ============================================================
	// $('#btn_form_operator').on('click', function(){
	// 	$('#form_operator').submit();
	// });
	// $('#form_operator').on('submit', function(){
		//console.log($(this).serializeArray());
		//return false;
	// });

	function showFormula() {
		$('#formula-modal').modal('show').find('.modal-body').load('/affiliate_management/affiliate_formula');
	}

    function mapData(data, level){
        var that = this;
        that.id = ko.observable(data.id);
        that.level = ko.observable(level);
        that.active_mem = ko.observable(data.active_members);
        that.min_net_rev =  ko.observable(data.min_net_revenue);
        that.max_net_rev =  ko.observable(data.max_net_revenue);
        that.commission_rate =  ko.observable(data.commission_rates);
        that.isExist = ko.observable(true);
        that.isEditing = ko.observable(false);
        that.showEditBtn = ko.observable(true);
        that.showSaveBtn = ko.observable(false);
        that.showDeleteBtn = ko.observable(true);
        that.showCancelBtn = ko.observable(false);
    }

    function ViewModel(){
        var self = this;
        self.dataLists = ko.observableArray();
        self.previous_data = ko.observableArray();

        $.ajax({
            url: '/affiliate_management/get_tier_settings',
            type: "GET",
            contentType: "application/json",
            accept: "application/json",
            success: function(result) {
                if(result == null){
                    result = [];
                }
                var map = $.map(result, function(item, i) {
                    i += 1;
                    return new mapData(item, i)
                });
                self.dataLists(map);
            }
        });

        self.addSetting = function(self){
            var length = self.dataLists().length,
                previous_max_net = length > 0 ? self.dataLists()[length-1].max_net_rev() : '';

            self.dataLists.push({
                id: ko.observable(),
                level: ko.observable(self.dataLists().length + 1),
                active_mem: ko.observable(""),
                min_net_rev: ko.observable(previous_max_net),
                max_net_rev: ko.observable(""),
                commission_rate: ko.observable(""),
                isExist: ko.observable(false),
                showEditBtn: ko.observable(false),
                showSaveBtn: ko.observable(true),
                isEditing: ko.observable(true),
                showDeleteBtn: ko.observable(true),
                showCancelBtn : ko.observable(false)
            });
        };

        self.tdEdit = function(data){
            data.showEditBtn(false);
            data.showSaveBtn(true);
            data.isEditing(true);
            data.showDeleteBtn(false);
            data.showCancelBtn(true);
        };

        self.tdSave = function(data){
            var url = '/affiliate_management/save_tier_setting/';
            if(data.active_mem() == '' || data.min_net_rev() == '' || data.max_net_rev() == '' || data.commission_rate() == ''){
                alert('Please fill up all the fields!');
            }
            else {
                $.ajax({
                    url: data.isExist() == true ? url + data.id() : url,
                    type: "POST",
                    dataType : "json",
                    data: {
                        level: data.level(),
                        active_members: data.active_mem(),
                        min_net_revenue: data.min_net_rev(),
                        max_net_revenue: data.max_net_rev(),
                        commission_rates: data.commission_rate()
                    },
                    error: function (xhr, textStatus, errorThrown) {
//                            data.errorMessage.push( xhr.responseJSON.name );
                        setTimeout(function () {
                        }, 5000);
                    },
                    success: function (result) {
                        data.showEditBtn(true);
                        data.showSaveBtn(false);
                        data.isEditing(false);
                        data.showDeleteBtn(true);
                        data.showCancelBtn(false);
                        setTimeout(function () {
                        }, 5000);
                    }
                });
            }
        };

        self.tdDelete = function(data){
            if (data.isExist() == false) {
                self.dataLists.remove(data);
                return 0;
            }else {
                if (confirm("Are you sure you want to delete this tier?")) {
                    $.ajax({
                        url: '/affiliate_management/delete_tier_setting/' + data.id(),
                        type: "DELETE",
                        contentType: "application/json",
                        accept: "application/json",
                        success: function () {
                            self.dataLists.remove(data);

                            return ko.utils.arrayFilter(self.dataLists(), function(lists) {
                                if(lists.level() > data.level()){
                                    $.ajax({
                                        url: '/affiliate_management/save_tier_setting/' + lists.id(),
                                        type: "POST",
                                        dataType : "json",
                                        data: {
                                            level: parseInt(lists.level()) - 1
                                        },
                                        error: function (xhr, textStatus, errorThrown) {
                                            setTimeout(function () {
                                            }, 5000);
                                        },
                                        success: function (result) {
                                            setTimeout(function () {
                                            }, 5000);
                                        }
                                    });
                                }
                            });
                        }
                    });


                }

            }
        };

        self.tdCancel = function(data){
            data.showEditBtn(true);
            data.showSaveBtn(false);
            data.isEditing(false);
            data.showDeleteBtn(true);
            data.showCancelBtn(false);
        };
    }
    var tierModel = new ViewModel();
    ko.applyBindings(tierModel);

    ko.bindingHandlers.numeric = {
        init: function (element, valueAccessor) {
            $(element).on("keydown", function (event) {
                // Allow: backspace, delete, tab, escape, and enter
                if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 ||
                        // Allow: Ctrl+A
                    (event.keyCode == 65 && event.ctrlKey === true) ||
                        // Allow: . ,
                    (event.keyCode == 188 || event.keyCode == 190 || event.keyCode == 110) ||
                        // Allow: home, end, left, right
                    (event.keyCode >= 35 && event.keyCode <= 39)) {
                    // let it happen, don't do anything
                    return;
                }
                else {
                    // Ensure that it is a number and stop the keypress
                    if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105)) {
                        event.preventDefault();
                    }
                }
            });
        }
    };

    $('#transaction_fee').change(function(){
        transactionFee();
    });

    function transactionFee(){
        if( $('#transaction_fee').is(':checked') ){
            $('#split_transaction_fee').bootstrapToggle('enable');
        }else{
            $('#split_transaction_fee').bootstrapToggle('off').bootstrapToggle('disable');
        }
    }

    $(function(){
        $('#split_transaction_fee').bootstrapToggle('disable');
        transactionFee();
        splitTransaction();

        var defaultLang = 'Are you sure you want to use the default settings?';
        var tierLang = 'Are you sure you want to use the tier settings?';

        $("#default_shares_checkbox").closest("div.toggle").on("click", function () {
            commissionSetting('default_shares_checkbox', tierLang, defaultLang);
        });

        $("#comm_by_tier_checkbox").closest("div.toggle").on("click", function () {
            commissionSetting('comm_by_tier_checkbox', defaultLang, tierLang);
        });

    });

    function commissionSetting(checkboxName, firstLang, secLang){
        if ($('#'+checkboxName).is(':checked')) {
            if (confirm(firstLang)) {
                $('#comm_by_tier_checkbox').bootstrapToggle('on');
                $('#default_shares_checkbox').bootstrapToggle('on');
            }else{
                $('#comm_by_tier_checkbox').bootstrapToggle('off');
                $('#default_shares_checkbox').bootstrapToggle('off');
            }
        } else {
            if (confirm(secLang)) {
                $('#comm_by_tier_checkbox').bootstrapToggle('off');
                $('#default_shares_checkbox').bootstrapToggle('off');
            }else{
                $('#comm_by_tier_checkbox').bootstrapToggle('on');
                $('#default_shares_checkbox').bootstrapToggle('on');
            }
        }
    }

    $('#split_transaction_fee').change(function(){
        splitTransaction();
    });

    function splitTransaction(){
        if( $('#split_transaction_fee').is(':checked') ){
            $('#inp-transaction-fee').prop('disabled', true);
            $('#inp-deposit-fee').prop('disabled', false);
            $('#inp-withdrawal-fee').prop('disabled', false);
        }else{
            $('#inp-transaction-fee').prop('disabled', false);
            $('#inp-deposit-fee').prop('disabled', true);
            $('#inp-withdrawal-fee').prop('disabled', true);
        }
    }

</script>
