<form class="form-horizontal" action="<?=BASEURL . 'affiliate_management/searchStatistics'?>" method="POST">
    <div class="panel panel-primary
              " style="margin-bottom:10px;">
        <div class="panel-heading">
            <h4 class="panel-title">
                <i class="icon-sort-amount-desc" id="hide_main_up"></i> <?=lang('lang.sort');?>
                <a href="#main" 
              id="hide_main" class="btn btn-default btn-sm pull-right">
                    <i class="glyphicon glyphicon-chevron-up" id="hide_main_up"></i>
                </a>
            </h4>
        </div>

        <div class="panel-body main_panel_body" id="main_panel_body" style="padding-bottom:0;">
            <div class="form-group">
                <?php
$period = $this->session->userdata('period');
$start_date = $this->session->userdata('start_date');
$end_date = $this->session->userdata('end_date');
$date_range_value = $this->session->userdata('date_range_value');

$username = $this->session->userdata('username');
$type_date = $this->session->userdata('type_date');
?>
                <div class="col-md-2 col-lg-2">
                    <label for="period" class="control-label" style="font-size:12px;"><?=lang('player.ap06');?>:</label>
                    <select class="form-control input-sm" name="period" id="period" onchange="checkPeriod(this)">
                        <option value=""><?=lang('aff.as13');?></option>
                        <option value="daily" <?=($period == 'daily') ? 'selected' : ''?> ><?=lang('aff.as14');?></option>
                        <option value="weekly" <?=($period == 'weekly') ? 'selected' : ''?> ><?=lang('aff.as15');?></option>
                        <option value="monthly" <?=($period == 'monthly') ? 'selected' : ''?> ><?=lang('aff.as16');?></option>
                        <option value="yearly" <?=($period == 'yearly') ? 'selected' : ''?> ><?=lang('aff.as17');?></option>
                    </select>
                    <?php echo form_error('period', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                </div>
                <div class="col-md-2 col-lg-2">
                    <label for="username" class="control-label" style="font-size:12px;"><?=lang('aff.as03');?>:</label>
                    <input type="text" name="username" class="form-control input-sm" value="<?=($username != null) ? $username : ''?>"/>
                </div>
                <div class="col-md-4 col-lg-4">
                    <label for="start_date" class="control-label" style="font-size:12px;"><?=lang('lang.date');?>: </label>
                    <div style="border:1px solid #E8E8E8;border-radius:5px;padding:0 10px 2px 10px;">
                        <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                            <input type="radio" name="type_date" class="type_date" value="registration date" <?=($period != null && $type_date == 'registration date') ? 'checked' : ''?> <?=($period == null) ? 'disabled' : ''?>/> <?=lang('aff.ap04');?>
                        </label>
                        <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                            <input type="radio" name="type_date" class="type_date" value="login date" <?=($period != null && $type_date == 'login date') ? 'checked' : ''?> <?=($period == null) ? 'disabled' : ''?>/>  <?=lang('aff.ap05');?>
                        </label>
                        <label class="radio-inline" style="padding-top:4px;margin-left:3px;">
                            <input type="radio" name="type_date" class="type_date" value="report date" <?=($period != null && $type_date == null || $type_date == 'report date') ? 'checked' : ''?> <?=($period == null) ? 'disabled' : ''?>/>  <?=lang('aff.ap07');?>
                        </label>
                    </div>
                </div>
                <div class="col-md-3 col-lg-3" id="reportrange" <?=($period == null || $period == 'today') ? 'style="display: none;"' : ''?>>
                    <label class="control-label" for="period"><?=lang('report.sum02');?></label>
                    <input type="text" class="form-control input-sm dateInput" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd"/>
                    <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart" value="<?=$start_date == '' ? '' : $start_date;?>" />
                    <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd" value="<?=$end_date == '' ? '' : $end_date;?>" />
                    <!-- <label for="start_date" class="control-label" style="font-size:12px;"><?=lang('aff.vb08');?>: </label>
                    <input type="date" name="start_date" id="start_date" class="form-control input-sm" <?=($this->session->userdata('start_date') == null) ? 'disabled' : ''?> value="<?=(!empty($this->session->userdata('start_date'))) ? $this->session->userdata('start_date') : ''?>">
                    <?php echo form_error('start_date', '<span class="help-block" style="color:#ff6666;">', '</span>');?> -->
                </div>
                <!-- div class="col-md-2 col-lg-2">
                    <label for="end_date" class="control-label" style="font-size:12px;"><?=lang('aff.vb09');?>: </label>
                    <input type="date" name="end_date" id="end_date" class="form-control input-sm" <?=($this->session->userdata('end_date') == null) ? 'disabled' : ''?> value="<?=(!empty($this->session->userdata('end_date'))) ? $this->session->userdata('end_date') : ''?>">
                    <?php echo form_error('end_date', '<span class="help-block" style="color:#ff6666;">', '</span>');?>
                    <span class="help-block" style="color:#ff6666;display:none;" id="mdate"></span>
                </div> -->
            </div>
            <center>
                <input type="submit" value="<?=lang('aff.as22');?>" id="search_main"class="btn btn-info btn-sm" >
            </center>
        </div>
    </div>
</form>
<!--end of main-->

<!-- display statistics -->
<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title pull-left"><i class="glyphicon glyphicon-list-alt"></i> <?=lang('aff.as01');?> </h4>
        <div class="clearfix"></div>
    </div>

    <div class="panel panel-body" id="details_panel_body">
        <!-- <div class="table-responsive" id="statisticsList" style="overflow:auto"> -->
            <table class="table table-striped table-hover" id="statisticsTable" style="width:100%">
                <thead>
                    <tr>
                        <th></th>
                        <th class="input-sm"><?=lang('aff.as02');?></th>
                        <th class="input-sm"><?=lang('aff.as18');?></th>
                        <th class="input-sm"><?="PT " . lang('aff.as06');?></th>
                        <th class="input-sm"><?="AG " . lang('aff.as06');?></th>
                        <th class="input-sm"><?=lang('aff.as06');?></th>
                        <th class="input-sm"><?="PT " . lang('aff.as07');?></th>
                        <th class="input-sm"><?="AG " . lang('aff.as07');?></th>
                        <th class="input-sm"><?=lang('aff.as07');?></th>
                        <th class="input-sm"><?="PT " . lang('aff.as09');?></th>
                        <th class="input-sm"><?="AG " . lang('aff.as09');?></th>
                        <th class="input-sm"><?=lang('aff.as09');?></th>
                        <th class="input-sm"><?=lang('aff.as12');?></th>
                        <th class="input-sm"><?=lang('aff.as08');?></th>
                    </tr>
                </thead>

                <tbody>
                    <?php
if (!empty($statistics)) {
	foreach ($statistics as $row) {
		$date = date('Y-m-d', strtotime($row['date']));
		?>
                                    <tr>
                                        <td></td>
                                        <td class="input-sm"><a href="<?=BASEURL . 'affiliate_management/viewAffiliateStatisticsToday/' . urlencode($date)?>"><?=$date?></a></td>  <!-- onclick="viewAffiliateStatisticsToday('<?=date('Y-m-d', strtotime($row['date']))?>');" -->
                                        <td class="input-sm"><?=$row['total_affiliates']?></td>
                                        <td class="input-sm"><?=$row['pt_bet']?></td>
                                        <td class="input-sm"><?=$row['ag_bet']?></td>
                                        <td class="input-sm"><?=$row['total_bet']?></td>
                                        <td class="input-sm"><?=$row['pt_win']?></td>
                                        <td class="input-sm"><?=$row['ag_win']?></td>
                                        <td class="input-sm"><?=$row['total_win']?></td>
                                        <td class="input-sm"><?=$row['pt_loss']?></td>
                                        <td class="input-sm"><?=$row['ag_loss']?></td>
                                        <td class="input-sm"><?=$row['total_loss']?></td>
                                        <td class="input-sm"><?=$row['total_bonus']?></td>
                                        <td class="input-sm"><?=$row['total_net_gaming']?></td>
                                    </tr>
                    <?php }
}
?>
                </tbody>
            </table>
        <!-- </div> -->
    </div>
</div>
<!-- end of display statistics -->

<script type="text/javascript">
    $(document).ready(function() {
        $('#statisticsTable').DataTable( {
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
    } );
</script>