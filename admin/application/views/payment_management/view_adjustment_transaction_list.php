<div class="row">
    <div class="col-md-12">
        <!-- Sort Option -->
        <form class="form-horizontal" id="form-filter" method="post">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary
              " style="margin-bottom:10px;">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <i class="icon-sort-amount-desc" id="hide_main_up"></i> <?=lang("report.p01");?>
                                <a href="#personal" 
              class="btn btn-default btn-sm hide_sortby pull-right">
                                    <i class="glyphicon glyphicon-chevron-up hide_sortby_up"></i>
                                </a>
                            </h4>
                        </div>

                        <div class="panel-body sortby_panel_body main_panel_body" style="padding-bottom:0;">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <fieldset style="padding:0px 10px 10px 15px;">
                                    <legend>
                                        <label class="control-label"><?=lang('player.ut02')?></label>
                                    </legend>

                                        <div class="col-md-1 col-md-offset-0">
                                            <div class="checkbox checkbox-info checkbox-circle">
                                              <input id="checkall" value="checkall" class="checkall" type="checkbox" onclick="checkAll(this.id)" checked>
                                              <label for="checkbox8">
                                                <?=lang('player.ui02')?>
                                              </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox checkbox-info checkbox-circle">
                                              <input id="" name="transaction_type" value="1" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                                              <label for="checkbox8">
                                                <?=lang('transaction.transaction.type.1')?>
                                              </label>
                                              <br/>
                                              <input id="" name="transaction_type" value="2" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                                              <label for="checkbox8">
                                                <?=lang('transaction.transaction.type.2')?>
                                              </label>
                                              <br/>
                                              <input id="" name="transaction_type" value="3" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                                              <label for="checkbox8">
                                                <?=lang('transaction.transaction.type.3')?>
                                              </label>
                                              <br/>
                                              <input id="" name="transaction_type" value="4" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                                              <label for="checkbox8">
                                                <?=lang('transaction.transaction.type.4')?>
                                              </label>
                                              <br/>
                                              <input id="" name="transaction_type" value="5" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                                              <label for="checkbox8">
                                                <?=lang('transaction.transaction.type.5')?>
                                              </label>
                                              <br/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox checkbox-info checkbox-circle">
                                              <input id="" name="transaction_type" value="6" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                                              <label for="checkbox8">
                                                <?=lang('transaction.transaction.type.6')?>
                                              </label>
                                              <br/>
                                              <input id="" name="transaction_type" value="7" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                                              <label for="checkbox8">
                                                <?=lang('transaction.transaction.type.7')?>
                                              </label>
                                              <br/>
                                              <input id="" name="transaction_type" value="8" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                                              <label for="checkbox8">
                                                <?=lang('transaction.transaction.type.8')?>
                                              </label>
                                              <br/>
                                              <input id="" name="transaction_type" value="9" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                                              <label for="checkbox8">
                                                <?=lang('transaction.transaction.type.9')?>
                                              </label>
                                              <br/>
                                              <input id="" name="transaction_type" value="10" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                                              <label for="checkbox8">
                                                <?=lang('transaction.transaction.type.10')?>
                                              </label>
                                              <br/>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="checkbox checkbox-info checkbox-circle">
                                              <input id="" name="transaction_type" value="11" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                                              <label for="checkbox8">
                                                <?=lang('transaction.transaction.type.11')?>
                                              </label>
                                              <br/>
                                              <input id="" name="transaction_type" value="12" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                                              <label for="checkbox8">
                                                <?=lang('transaction.transaction.type.12')?>
                                              </label>
                                              <br/>
                                              <input id="" name="transaction_type" value="13" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                                              <label for="checkbox8">
                                                <?=lang('transaction.transaction.type.13')?>
                                              </label>
                                              <br/>
                                              <input id="" name="transaction_type" value="14" class="checkall" type="checkbox" onclick="checkPlayerInfo(this.value);" checked>
                                              <label for="checkbox8">
                                                <?=lang('transaction.transaction.type.14')?>
                                              </label>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label"><?=lang('player.ut03')?></label>
                                    <div class="form-inline">
                                        <select class="form-control input-sm" name="from_type">
                                            <option value=""><?=lang('lang.selectall')?></option>
                                            <option value="1" <?php echo set_select('from_type', 1, isset($input['from_type']) && $input['from_type'] == 1)?>><?php echo lang('transaction.from.to.type.1')?></option>
                                            <option value="2" <?php echo set_select('from_type', 2, isset($input['from_type']) && $input['from_type'] == 2)?>><?php echo lang('transaction.from.to.type.2')?></option>
                                            <option value="3" <?php echo set_select('from_type', 3, isset($input['from_type']) && $input['from_type'] == 3)?>><?php echo lang('transaction.from.to.type.3')?></option>
                                        </select>
                                        <input type="text" name="fromUsername" class="form-control input-sm" placeholder="Username"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label"><?=lang('player.ut04')?></label>
                                    <div class="form-inline">
                                        <select class="form-control input-sm" name="to_type">
                                            <option value=""><?=lang('lang.selectall')?></option>
                                            <option value="1" <?php echo set_select('to_type', 1, isset($input['to_type']) && $input['to_type'] == 1)?>><?php echo lang('transaction.from.to.type.1')?></option>
                                            <option value="2" <?php echo set_select('to_type', 2, isset($input['to_type']) && $input['to_type'] == 2)?>><?php echo lang('transaction.from.to.type.2')?></option>
                                            <option value="3" <?php echo set_select('to_type', 3, isset($input['to_type']) && $input['to_type'] == 3)?>><?php echo lang('transaction.from.to.type.3')?></option>
                                        </select>
                                        <input type="text" name="toUsername" class="form-control input-sm" placeholder="Username"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label"><?=lang('player.ut05')?> &gt;= </label>
                                    <input type="number" class="form-control input-sm" name="amountStart" value="<?php echo isset($input['amountStart']) ? $input['amountStart'] : '';?>"/>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label"><?=lang('player.ut05')?> &lt;= </label>
                                    <input type="number" class="form-control input-sm" name="amountEnd" value="<?php echo isset($input['amountEnd']) ? $input['amountEnd'] : '';?>"/>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label"><?=lang('player.ut09')?></label>
                                    <select class="form-control input-sm" name="status">
                                        <option value=""><?=lang('lang.selectall')?></option>
                                        <option value="1" <?php echo set_select('status', 1, isset($input['status']) && $input['status'] == 1)?>><?php echo lang('transaction.status.1')?></option>
                                        <option value="2" <?php echo set_select('status', 2, isset($input['status']) && $input['status'] == 2)?>><?php echo lang('transaction.status.2')?></option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label"><?=lang('player.ut10')?></label>
                                    <select class="form-control input-sm" name="flag">
                                        <option value=""><?=lang('lang.selectall')?></option>
                                        <option value="1" <?php echo set_select('flag', 1, isset($input['flag']) && $input['flag'] == 1)?>><?php echo lang('transaction.flag.1')?></option>
                                        <option value="2" <?php echo set_select('flag', 2, isset($input['flag']) && $input['flag'] == 2)?>><?php echo lang('transaction.flag.2')?></option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label"><?=lang('pay.transperd')?></label>
                                    <input id="reportrange" class="form-control input-sm dateInput" data-time="true" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd"/>
                                    <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart" value="<?=$this->session->userdata('dateRangeValueEnd')?>"/>
                                    <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd" value="<?=$this->session->userdata('dateRangeValueEnd')?>"/>
                                </div>

                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <input class="btn btn-default btn-sm" id="btn-reset" type="reset" value="<?=lang('lang.reset');?>">
                            <input class="btn btn-sm btn-primary" type="submit" value="<?=lang("lang.search");?>" />
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end of Sort Information-->

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title"><i class="fa fa-credit-card"></i> <?=lang('pay.transactions')?></h4>
            </div>
            <table id="transaction-table" class="table table-bordered table-hover" cellspacing="0" width="100%">
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
                        <th><?php echo lang('player.ut09');?></th>
                        <th><?php echo lang('player.ut10');?></th>
                        <th><?php echo lang('player.ut11');?></th>
                        <th><?php echo lang('player.ut12');?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th colspan="12" style="text-align: right;">
                            <div class="row" id="summary"></div>
                        </th>
                    </tr>
                </tfoot>
            </table>
            <div class="panel-body">

            </div>
            <div class="panel-footer"></div>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        var dataTable = $('#transaction-table').DataTable({

            autoWidth: false,
            searching: false,
            dom: "<'panel-body'<'pull-right'B>l>t<'text-center'r><'panel-body'<'pull-right'p>i>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ]
                }
            ],
            columnDefs: [
                { className: 'text-right', targets: [ 4,5,6 ] },
                { visible: false, targets: [ 7,9,10,11 ] }
            ],
            order: [[0, 'desc']],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {

                data.extra_search = $('#form-filter').serializeArray();

                $.post(base_url + "api/adjustment_history", data, function(data) {

                    if (data.summary) {
                        $('#summary').html('');
                        $.each(data.summary, function(key, value) {
                            $('#summary').append('<div class="col-xs-11"><?=lang('system.word66')?> '+key+':</div><div class="col-xs-1">'+value+'</div>');
                        });
                    }

                    callback(data);
                },'json');

            },
        });

        $('#form-filter').on('submit', function(e) {
            e.preventDefault();
            dataTable.ajax.reload();
        });

        $('#btn-reset').on('click', function(e) {
            e.preventDefault();
            document.getElementById('form-filter').reset();
            dataTable.ajax.reload();
        });

    });

    function checkAll(id) {
        var list = document.getElementsByClassName(id);
        var all = document.getElementById(id);

        if (all.checked) {
            for (i = 0; i < list.length; i++) {
                list[i].checked = 1;
            }
        } else {
            all.checked;

            for (i = 0; i < list.length; i++) {
                list[i].checked = 0;
            }
        }
    }
</script>