<div class="row">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title pull-left"><strong><?= lang('player.ap01'); ?></strong></h4>
                <a href="<?= BASEURL . 'affiliate_management/viewAffiliates'?>" class="btn btn-default btn-sm pull-right"><span class="glyphicon glyphicon-remove"></span></a>
                <div class="clearfix"></div>
            </div>

            <div class="panel panel-body" id="signupinfo_panel_body">
                <form class="form-horizontal" action="<?= BASEURL . 'affiliate_management/actionType'?>" method="post" role="form">
                    <div class="col-md-7">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped table-responsive" id="myTable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th><?= lang('player.01'); ?></th>
                                        <th><?= lang('player.07'); ?></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach($affiliates as $row) { ?>
                                        <tr>
                                            <td></td>
                                            <td><?= $row['username']?></td>
                                            <td><?= date('Y-m-d', strtotime($row['createdOn']))?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr/>
                    <div class="col-md-5">
                        <div class="pull-right">
                            <select name="action_type" id="action_type" class="form-control input-sm" onchange="showDivs(this);">
                                <option value="locked"><?= lang('aff.aa02'); ?></option>
                                <option value="tag"><?= lang('aff.aa03'); ?></option>
                            </select>
                            <?php echo form_error('action_type', '<span class="help-block" style="color:#ff6666;">', '</span>'); ?>
                            <input type="hidden" name="affiliates" value="<?= $affiliate_ids ?>">
                        </div>
                        <div class="clearfix"></div>
                    
                        <label><?= lang('aff.aa06'); ?></label>

                        <div class="well" style="overflow: auto">
                            <div id="block_lock">
                                <div class="form-group">
                                    <label class="control-label col-md-4"><?= lang('aff.aa07'); ?>: </label>
                                    <div class="col-md-4">
                                        <input type="submit" class="btn btn-info btn-sm" value="<?= lang('con.i07'); ?>">
                                    </div>
                                </div>
                            </div>

                            <div id="tag" style="display: none;">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label for="tag" class="control-label"><?= lang('aff.aa08'); ?>: </label>
                                        <select id="tags" name="tags" class="form-control input-sm" onchange="showDescription(this)">
                                            <option value="">-<?= lang('aff.aa09'); ?>-</option>
                                            <?php foreach ($tags as $tag) { ?>
                                                <option value="<?= $tag['tagId']?>"><?= $tag['tagName']?></option>
                                            <?php } ?>

                                            <?php if($page == 'blacklist') { ?>
                                                <!-- <option value="Others">Others</option> -->
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-12" style="padding-top:10px;">
                                        <input type="submit" class="btn btn-info btn-sm" value="<?= lang('aff.aa11'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="col-md-7" id="player_details" style="display: none;">

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#myTable').DataTable( {
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
