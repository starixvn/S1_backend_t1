<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title pull-left">
            <i class="glyphicon glyphicon-list-alt"></i>
            <?=lang('cms.currentPromoCancellationSetup');?>: <b><?=$cancelsetup['value'] == 0 ? lang('cms.manual') : lang('cms.auto')?></b></h4>
        <div class="clearfix"></div>
    </div>
    <div class="panel panel-body" id="details_panel_body">
        <div class="row">
        <div class="col-md-12">
        <table class="table">
            <tr>
                <td><b><?=lang('cms.manual');?></b>
                </td>
                <td><?=lang('cms.manualDesc');?>
                </td>
            </tr>
            <tr>
                <td><b><?=lang('cms.auto');?></b>
                </td>
                <td><?=lang('cms.autoDesc');?>
                </td>
            </tr>
        </table>
        <hr/>
        </div>
        <form action="<?=BASEURL . 'marketing_management/setupPromoCancellation'?>" method="post" role="form">

                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-3">
                            <label><?=lang('cms.promocancelSetup');?>:</label>

                            <select class="form-control" name="setup" id="setup" required>
                                <option value="">-- <?=lang('cms.selectSetup');?> --</option>
                                <option value="0"><?=lang('cms.manual');?></option>
                                <option value="1"><?=lang('cms.auto');?></option>
                            </select>

                        </div>

                    </div>
                </div>

                <div class="row">
                    <br/>
                    <div class="col-md-12">
                        <div class="col-md-3">
                            <button class="btn btn-sm btn-primary"><?=lang('player.saveset');?></button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
