<div class="panel panel-default" style="border-radius: 0; border-top: none; margin-bottom: 0;">
    <div class="panel-body">
        <div class="text-right">
            <div class="form-inline">
                <input type="text" id="reportrange" class="form-control input-sm dateInput inline" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true"/>
                <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart"/>
                <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd"/>
                <input type="button" class="btn btn-primary btn-sm" id="btn-submit" value="<?=lang('lang.searchby');?>"/>
            </div>
        </div>
        <hr/>
        <table id="transaction-table" class="table table-striped table-hover table-condensed">
            <thead>
                <tr>
                    <th><?php echo lang('player.ut01');?></th>
                    <th><?php echo lang('player.ut02');?></th>
                    <th><?php echo lang('player.ut03');?></th>
                    <th><?php echo lang('player.ut04');?></th>
                    <th><?php echo lang('player.ut05');?></th>
                    <th><?php echo lang('player.ut06');?></th>
                    <th><?php echo lang('player.ut07');?></th>
                    <th><?php echo lang('player.ut08');?></th>
                    <th><?php echo lang('cms.promoCat');?></th>
                    <th><?php echo lang('pay.totalbal');?></th>
                    <th><?php echo lang('player.ut10');?></th>
                    <th><?php echo lang('player.ut11');?></th>
                    <th><?php echo lang('player.ut12');?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>