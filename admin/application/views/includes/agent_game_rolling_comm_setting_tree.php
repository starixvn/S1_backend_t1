<?php
/**
 *   filename:   agent_game_rolling_comm_setting_tree.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-08-02
 *   @brief:     jstree for games rolling comm setting
 */
?>

<div class="panel panel-primary">
    <!-- panel heading {{{3 -->
    <div class="panel-heading">
        <h4 class="panel-title">
            <a href="#hierarchy" id="hide_agent_game_rolling_comm_setting" class="btn btn-info btn-sm">
                <i class="glyphicon glyphicon-chevron-up" id="hide_agentbi_up"></i>
            </a>
            &nbsp; <?=lang('Game Rolling Comm Setting');?>
        </h4>
    </div> <!-- panel heading }}}3 -->
    <div class="panel-body agent_basic_panel_body" id="agent_game_rolling_comm_setting">
        <!-- fieldset games_rolling_comm_setting {{{3 -->
        <div class="col-md-12">
            <form method="POST" id="rolling_comm_setting_form"
                action="<?=site_url('agency/process_game_rolling_comm_setting/'.$agent_id)?>" accept-charset="utf-8">
                <label for="rolling_comm_setting">
                    <?=lang('Game Rolling Comm Setting') . ' (' . lang('default game rolling comm') . ' = ' .'0.00)';?>
                </label>
                <div class="form-group" style="margin-left:5px;margin-right:5px;">
                    <input type="hidden" name="selected_game_tree" value="">
                    <fieldset style="padding:20px">
                        <div id="gameTree" class="col-md-12"> </div>
                    </fieldset>
                </div>
                <!-- button row {{{3 -->
                <div class="row">
                    <div class="col-md-5 col-lg-5" style="padding: 10px;">
                    </div>
                    <div class="col-md-6 col-lg-6" style="padding: 10px;">
                        <input type="submit" id="submit" class="btn btn-sm btn-primary agent-oper" value="<?=lang('Save');?>" />
                    </div>
                </div>
                <!-- button row }}}3 -->
            </form> <!-- end of form rolling_comm }}}1 -->
        </div> <!-- fieldset games_rolling_comm_setting }}}3 -->
    </div>
</div>
<script>
$(document).ready(function(){
    $('#gameTree').jstree({
        'core' : {
            'data' : {
                "url" : "<?php echo site_url('/api/get_game_tree_by_agent/' . $agent_id); ?>",
                    "dataType" : "json" // needed only if you do not supply JSON headers
            }
        },
        "input_number":{
            "form_sel": '#rolling_comm_setting_form'
        },
        "checkbox":{
            "tie_selection": false,
        },
        "plugins":[
            "search","checkbox","input_number"
        ]
    });

});
</script>

<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agent_game_rolling_comm_setting_tree.php
