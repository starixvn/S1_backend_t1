<?php
/**
 *   filename:   settlement.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-05-02
 *   @brief:     view settlement information for agency sub-system
 */

// set display according to configurations
$panelOpenOrNot = $this->config->item('default_open_search_panel') ? '' : 'collapsed';
$panelDisplayMode = $this->config->item('default_open_search_panel') ? '' : 'in';
if(isset($parent_id) && $parent_id > 0) {
    $settlements = $this->agency_model->get_all_settlement($parent_id);
} else {
    $settlements = $this->agency_model->get_all_settlement();
    $parent_id = '';
}

if(isset($settlement_id) && !empty($settlement_id)) {
    $selected = $this->agency_model->get_settlement_by_id($settlement_id);
} else {
    $selected = $settlements[0];
    $settlement_id = $selected['settlement_id'];
}
$this->utils->debug_log('SELECTED_SETTLEMENT', $selected);

$this->load->library(array('agency_library'));
?>
<div class="container">
    <!-- search form {{{1 -->
    <form class="form-horizontal" id="search-form">
        <input type="hidden" id="parent_id" name="parent_id" value="<?php echo $parent_id?>"/>
        <input type="hidden" id="search_on_date" name="search_on_date" value="1"/>
        <input type="hidden" id="only_under_agency" name="only_under_agency" value="1"/>
        <input type="hidden" id="agent_name" name="agent_name" value=""/>
        <input type="hidden" id="period" name="period" value=""/>
        <input type="hidden" id="date_from" name="date_from" value=""/>
        <input type="hidden" id="date_to" name="date_to" value=""/>
        <input type="hidden" id="include_all_downlines" name="include_all_downlines" value="true"/>
        <input type="hidden" id="group_by" name="group_by" value="player.playerId"/>
        <div class="panel panel-primary">
            <!-- panel heading {{{2 -->
            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fa fa-search"></i>
                    <?=lang("Select Invoice")?>
                    <span class="pull-right">
                        <a data-toggle="collapse" href="#collapseAgentList"
                            class="btn btn-info btn-xs <?=$panelOpenOrNot?>">
                        </a>
                    </span>
                </h4>
            </div>
            <!-- panel heading }}}2 -->

            <div id="collapseAgentList" class="panel-collapse collapse <?=$panelDisplayMode?>">
                <!-- panel body {{{2 -->
                <div class="panel-body">
                    <!-- input row {{{3 -->
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <label class="control-label"><?=lang('Select Invoice');?></label>
                            <select name="invoice_select_list" id="invoice_select_list" class="form-control input-sm">
                                <?php foreach($settlements as $rec) { ?>

                                <?php $n = $this->agency_library->create_invoice_name($rec); ?>
                                <?php $v = $this->agency_library->create_invoice_val_json($rec); ?>
                                <?php //$this->utils->debug_log('v', $v); ?>
                                <option value='<?=$v?>'
                                <?=($settlement_id == $rec['settlement_id']) ? 'selected' : ''?> >
                                <?=$n?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div> <!-- input row }}}3 -->
                <!-- button row {{{3 -->
                <div class="col-md-4 col-lg-4 pull-right" style="padding-top: 20px;">
                    <input type="button" value="<?=lang('Send by email');?>"
                    class="btn btn-primary btn-sm" onclick="alert('<?=lang('Send by email')?>')" />

                    <input class="btn btn-sm btn-primary" type="button"
                    value="<?=lang('Send by Skype');?>" onclick="alert('<?=lang('Send by Skype')?>')" />
                </div>
                <div class="col-md-4" style="padding-top: 20px">
                    <input type="button" value="<?=lang('Export in Excel')?>"
                    class="btn btn-success btn-sm export_excel">
                </div> <!-- button row }}}3 -->
                <!--  modal for send settlement invoice {{{4 -->
                <div class="modal fade in" id="send_invoice_modal"
                    tabindex="-1" role="dialog" aria-labelledby="label_send_invoice_modal">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="label_send_invoice_modal"></h4>
                            </div>
                            <div class="modal-body"></div>
                            <div class="modal-footer"></div>
                        </div>
                    </div>
                </div> <!--  modal for level name setting }}}4 -->
            </div>
            <!-- panel body }}}2 -->
        </div>
    </div>
</form> <!-- end of search form }}}1 -->

<!-- panel for settlement table {{{1 -->
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-bullhorn"></i>
            <?=lang('Settlement Report');?>
        </h4>
    </div>
    <div class="panel-body">
        <!-- settlement table {{{2 -->
        <div id="logList" class="table-responsive">
            <table class="table table-striped table-hover table-condensed"
                id="settlement_table" style="width:100%;">
                <thead>
                    <tr>
                        <?php include __DIR__.'/../includes/agency_settlement_table_header.php'; ?>
                    </tr>
                </thead>
                <tbody id="settlement_tbody">
                </tbody>
            </table>
        </div>
        <!--end of settlement table }}}2 -->
    </div>
    <div class="panel-footer"></div>
</div>
<!-- panel for settlement table }}}1 -->
<!-- panel for player info {{{1 -->
<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="icon-users"></i> <?=lang('report.s09')?> </h4>
    </div>
    <div class="panel-body">

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="myTable">
                <thead>
                    <tr>
                        <?php include __DIR__.'/../includes/agency_player_report_table_header.php'; ?>
                    </tr>
                </thead>
                <tbody id="player_tbody">
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel-footer"></div>
</div>
<!-- panel for player info }}}1 -->
<!-- panel for game info {{{1 -->
<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title"><i class="icon-dice"></i> <?=lang('report.s07')?> </h4>
    </div>
    <div class="panel-body">
        <table class="table table-bordered table-hover" id="myTable">
            <thead>
                <tr>
                    <?php include __DIR__.'/../includes/agency_games_report_table_header.php'; ?>
                </tr>
            </thead>
            <tbody id="game_tbody">
            </tbody>
        </table>
    </div>
    <div class="panel-footer"></div>
</div>
<!-- panel for game info }}}1 -->
</div>

<!-- JS code {{{1 -->
<script type="text/javascript">
$(document).ready(function(){
    make_invoice_options_searchable();
    ajax_url = "<?=site_url('agency_management/get_invoice_info_ajax')?>";
    set_invoice_select_action(ajax_url);
    var v = $('#invoice_select_list').val();
    set_hidden_input_fields(v);
    get_invoice_info_ajax(ajax_url, v);

    $('.export_excel').click(function(){
        var d = {'extra_search':$('#search-form').serializeArray(), 'draw':1, 'length':-1, 'start':0};
        var export_url = '<?php echo site_url('export_data/agency_invoice') ?>';
        // utils.safelog(d);
        //$.post(site_url('/export_data/agency_settlement_list'), d, function(data){
        $.post(export_url, d, function(data){
            // utils.safelog(data);

            //create iframe and set link
            if(data && data.success){
                $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
            }else{
                alert('export failed');
            }
        });
    });

});
</script>
<!-- JS code }}}1 -->

<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of settlement.php
