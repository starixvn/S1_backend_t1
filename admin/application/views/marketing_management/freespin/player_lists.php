
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <!-- <i class="icon-bullhorn"></i> --> <?=lang('Players');?>
            
            <a href="<?=site_url('marketing_management/addFreeroundPlayer/' . $fround_id)?>" class="btn btn-primary pull-right" id="add_promocms_sec" style="color: #fff; margin-top: -4px" data-original-title="" title="">
                <span id="addPromoCmsGlyhicon" class="glyphicon glyphicon-plus-sign"></span> 
                    <?=lang('Create player')?>
                </a>

            <a href="<?=site_url('marketing_management/freeround')?>" class="btn btn-primary pull-right" id="add_promocms_sec" style="color: #fff; margin-top: -4px; margin-right: 5px;" data-original-title="" title="">
                <span id="addPromoCmsGlyhicon; " class="glyphicon glyphicon-arrow-left"></span> 
                    <?=lang('Back to list')?>
                </a>

            <div class="clearfix"></div>
        </h4>
    </div>
    <div class="panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12">
                    
                <div id="promorule_table" class="table-responsive" style="overflow: hidden;">
                    <table class="table table-bordered table-hover dataTable" id="myTable" style="width:100%;">

                        <thead>
                            <tr>
                                <th class="tableHeaderFont"><?=lang('Player Id');?></th>
                                <th class="tableHeaderFont"><?=lang('Player State');?></th>
                                <th class="tableHeaderFont"><?=lang('Amount Spent');?></th>
                                <th class="tableHeaderFont">&nbsp;</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                                if( ! empty( $players ) ){
                                    foreach ($players as $key => $value) {
                            ?>
                                        <tr>
                                            <td><?=$value->player_id?></td>
                                            <td><?=$value->state?></td>
                                            <td><?=$value->amount_spent?></td>
                                            <td>

                                                <a href="javascript:void(0)" id="freeroundActivate_<?=$fround_id?>" onclick="return removePlayer('<?=$value->player_id?>')">Remove</a>

                                            </td>
                                        </tr>
                            <?php
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div><div class="panel-footer"></div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#myTable').DataTable();
    } );

    function removePlayer( player_id ){

        var r = confirm("<?=lang('sys.gt4')?>");
        if (r == false) return

       $.ajax({
            url: "<?=site_url('marketing_management/removeFreeroundPlayer')?>",
            type: 'POST',
            data: {
                fround_id: "<?=$fround_id?>",
                player_id: player_id
            },
            success: function(){
                window.location.reload();
            }
       });

    }
</script>
