<style type="text/css">
  #container_game_tree .disabled{
    color: #cccccc;
    pointer-events: none;
  }
</style>
<div class="panel panel-primary">
    <div class="panel-heading">

        <h4 class="panel-title pull-left"><i class="icon-gift"></i> <?=lang('player.curcashbckperset');?></h4>
             <div class="clearfix"></div>
    </div>
    <?php
    $cb_rule_id = null;
    if(isset($common_cashback_rule->id)) $cb_rule_id = $common_cashback_rule->id;
    ?>
    <form id="cashback_settings_form" method="post" action="<?=site_url('marketing_management/saveCommonCashbackSetting/' . $cb_rule_id)?>">
         <input type="hidden" name="enabled_edit_game_tree" id="enabled_edit_game_tree" value="false">
         <div class="form-group" style="margin:20px;">
            <fieldset style="padding:20px">
              <legend><h4><?=lang('Common Cashback Setting Form');?></h4></legend>
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-3">
                      <?php echo lang('Min Bet Amount'); ?>
                      <input type="text" class="form-control number_only" maxlength="10" name="min_bet_amount" value="<?php echo isset($common_cashback_rule->min_bet_amount) ? $common_cashback_rule->min_bet_amount : '' ?>" required>
                  </div>
                  <div class="col-md-3">
                      <?php echo lang('Max Bet Amount'); ?>
                      <input type="text" class="form-control number_only"  maxlength="10" name="max_bet_amount" value="<?php echo isset($common_cashback_rule->max_bet_amount) ? $common_cashback_rule->max_bet_amount : '' ?>" required>
                  </div>
                  <div class="col-md-3">
                      <?php echo lang('Percentage (Default percentage if no game selected)'); ?> <i class="fa fa-exclamation-circle" data-toggle="tooltip" title="<?=lang("Will apply to all checked items without the specific percentage setting.")?>" data-container="body"></i>
                      <input type="text" class="form-control number_only" maxlength="5" name="default_percentage" value="<?php echo isset($common_cashback_rule->default_percentage) ? $common_cashback_rule->default_percentage : '' ?>" required>
                  </div>

                </div>
              </div>
            </fieldset>
          </div>

           <!-- Allowed Game Type Tree Start -->
            <div class="row">
              <div class="col-md-12">
                <div class="col-md-6">
                   <div class="form-group" id="container_game_tree" style="margin-left:5px;margin-right:5px;">
                    <input type="hidden" name="selected_game_tree" value="">
                    <input type="hidden" name="selected_game_tree_count" value="">
                    <fieldset style="padding:20px">
                      <legend><h4><?=lang('Select Common Cashback Game Rules (Optional)');?></h4></legend>

                      <div class="row" style="padding-bottom:15px;">
                        <div class="col-md-4" style="padding-left:20px;">
                          <input type="button" id="btn_edit_game_tree" disabled="disabled" class="disabled form-control input-sm btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary' ?>" value="<?=lang('Edit')?>">
                        </div>
                        <div class="col-md-4" style="padding-left:20px;">
                          <input type="text" id="searchTree" class="form-control input-sm" disabled="disabled" placeholder="<?=lang('Search Game List')?>">
                        </div>
                      </div>
                      <div class="row" style="padding-bottom:15px;">
                        <div class="col-md-8" style="padding-left:20px;">
                          <?=lang('Click Edit button to modify Cashback GameList')?>
                        </div>
                      </div>

                      <div id="gameTree" class="disabled col-md-12">
                      </div>
                    </fieldset>
                  </div>
                </div>
              </div>
            </div>
            <!-- Allowed Game Type Tree End -->
          <div class="row">
              <div class="col-md-12">
                  <div class="col-md-3" style="margin:20px;">
                      <br/>

                      <input type="submit" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-success' ?>" id="saveCashbackGameRuleSetting" value="<?=lang('player.saveset');?>">
                      <a href="<?php echo site_url('/marketing_management/cashbackPayoutSetting'); ?>" class="btn <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-linkwater' : 'btn-default' ?>"><?=lang('Cancel');?></a>
                  </div>
              </div>
          </div>
    </div>
</form>
<script>

  $('#btn_edit_game_tree').click(function(){
    //load again

    //remove disabled and set flag
    $('#gameTree').removeAttr('disabled').removeClass('disabled');
    $('#gameTree input').removeAttr('disabled').removeClass('disabled');
    $('#searchTree').removeAttr('disabled').removeClass('disabled');
    $('#enabled_edit_game_tree').val('true');
  });
  /*
   * Git Issue: #170 - start
   * Modified datetime: 2016-10-26 05:30:00
   * Modification Description: Added Game List in Tree
   */
  $('#gameTree').on('ready.jstree', function(ev, data){
        //enable edit button
        $('#btn_edit_game_tree').removeAttr('disabled').removeClass('disabled');
        $('#gameTree input').attr('disabled', 'disabled').addClass('disabled');
      }).jstree({
    'core' : {
      'data' : {
        "url" : "<?php echo site_url('/api/get_cashback_game_rule/' . $this->uri->segment(3)); ?>",
        "dataType" : "json" // needed only if you do not supply JSON headers
      }
    },
    "input_number":{
        "form_sel": '#cashback_settings_form'
    },
    "checkbox":{
      "tie_selection": false,
    },
    "plugins":[
      "search","checkbox","input_number"
    ]
  });

  $("#cashback_settings_form").submit(function(e){
    if(!isChrome()){
      alert("<?=lang('Sorry, cannot use other browser to save settings.')?> <?=lang('Please ONLY use Chrome Browser, version should be more than 69, otherwise settings will be lost.')?>");
      e.preventDefault();
      return false;
    }
    if($("#enabled_edit_game_tree").val()=='true'){
      checkGameIfSelected();
    }else{
      if(!confirm('<?=lang("You didn\'t add or delete any games. Sure about this?") ?>')) {
          e.preventDefault();
          return false;
      }
    }
  });

  function checkGameIfSelected(){
    var selected_game=$('#gameTree').jstree('get_checked');
    if(selected_game.length>0){
      $('#cashback_settings_form input[name=selected_game_tree]').val(selected_game.join());
      $('#gameTree').jstree('generate_number_fields');
    }
  }
</script>