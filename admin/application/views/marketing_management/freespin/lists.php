<div class="panel panel-primary">
    
        <div class="panel-heading">
            <h4 class="panel-title">
                <i class="fa fa-search"></i><?=lang('lang.search')?><span class="pull-right">
                    <a data-toggle="collapse" href="#collapsePlayerList" class="btn btn-info btn-xs"></a>
                </span>
            </h4>
        </div>
        <form id="search-form" action="<?=site_url('marketing_management/freeround')?>" method="GET">
            <div id="collapsePlayerList" class="panel-collapse ">
                <div class="panel-body">
                    
                    
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="wallet_order" class="control-label"><?=lang('State')?></label>
                                <select name="state" class="form-control">
                                    <option value="">--- <?=lang('All States')?> ---</option>
                                    <option value="ACTIVE" <?=(isset($_GET['state']) && $_GET['state'] == "ACTIVE") ? 'selected="selected"' : ''?>><?=lang('ACTIVE')?></option>
                                    <option value="EXPIRED" <?=(isset($_GET['state']) && $_GET['state'] == "EXPIRED") ? 'selected="selected"' : ''?>><?=lang('EXPIRED')?></option>
                                    <option value="FINISHED" <?=(isset($_GET['state']) && $_GET['state'] == "FINISHED") ? 'selected="selected"' : ''?>><?=lang('FINISHED')?></option>
                                    <option value="CANCELLED" <?=(isset($_GET['state']) && $_GET['state'] == "CANCELLED") ? 'selected="selected"' : ''?>><?=lang('CANCELLED')?></option>
                                </select>
                            </div>
                        </div>

                    

                </div>
            </div>
            <div class="panel-footer text-center">
                <input value="<?=lang('lang.reset')?>" class="btn btn-default btn-sm" onclick="window.location='/marketing_management/freeround';" type="button">
                <input form="search-form" value="<?=lang('lang.search')?>" class="btn btn-primary btn-sm" type="submit">
            </div>
        </form>
</div>


<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <!-- <i class="icon-bullhorn"></i> --> <?=lang('Free Round Package');?>
            <a href="<?=site_url('marketing_management/addFreeround')?>" class="btn btn-primary pull-right" id="add_promocms_sec" style="color: #fff; margin-top: -4px" data-original-title="" title="">
                <span id="addPromoCmsGlyhicon" class="glyphicon glyphicon-plus-sign"></span> 
                    <?=lang('Create Free Round Package')?>
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
                                <th class="tableHeaderFont"><?=lang('Name');?></th>
                                <th class="tableHeaderFont"><?=lang('Operator');?></th>
                                <!-- <th class="tableHeaderFont"><?=lang('lang.games');?></th> -->
                                <th class="tableHeaderFont"><?=lang('Coins');?></th>
                                <th class="tableHeaderFont"><?=lang('Player');?></th>
                                <th class="tableHeaderFont"><?=lang('Limit Per Player');?></th>
                                <th class="tableHeaderFont"><?=lang('Total Amount Spent');?></th>
                                <!-- <th class="tableHeaderFont"><?=lang('Promo Code');?></th> -->
                                <!-- <th class="tableHeaderFont"><?=lang('Open For All');?></th> -->
                                <th class="tableHeaderFont"><?=lang('Start Date');?></th>
                                <th class="tableHeaderFont"><?=lang('End Date');?></th>
                                <!-- <th class="tableHeaderFont"><?=lang('Relative Duration');?></th> -->
                                <th class="tableHeaderFont"><?=lang('Package State');?></th>
                                <th class="tableHeaderFont">&nbsp;</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                                if( ! empty( $freespin ) ){
                                    foreach ($freespin as $key => $value) {
                            ?>
                                        <tr>
                                            <td><?=$value->name?></td>
                                            <td><?=($value->operator_id == 0) ? 'Master Licensee ['.$value->operator_id.']' : ''?></td>
                                        <!--     <td>
                                                <?=lang('Games')?>
                                            </td> -->
                                            <td>
                                                <a href="<?=site_url('marketing_management/freeroundCoins/' . $value->fround_id)?>">
                                                    <?=lang('Info')?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="<?=site_url('marketing_management/freeroundPlayers/' . $value->fround_id)?>"><?=$value->n_players?></a>
                                            </td>
                                            <td><?=$value->limit_per_player?></td>
                                            <td><?=$value->total_spent?></td>
                                            <!-- <td><?=$value->name?></td> -->
                                            <!-- <td><?=$value->name?></td> -->
                                            <td><?=$value->start_date?></td>
                                            <td><?=$value->end_date?></td>
                                            <!-- <td><?=(isset($value->duration_relative)) ? $value->duration_relative : '0'?> day(s)</td> -->
                                            <td><?=$value->state?></td>
                                            <td>
                                                <?php
                                                    if( $value->state <> "EXPIRED" ){
                                                ?>
                                                        <a href="javascript:void(0)" id="freeroundCancel_<?=$value->fround_id?>" data-toggle="modal" data-target="#cancel_dialog" data-freeroundId="<?=$value->fround_id?>" class="<?=($value->state == 'CANCELLED') ? 'hide' : ''?>"><?=lang('system.word49')?></a>

                                                        <a href="javascript:void(0)" id="freeroundActivate_<?=$value->fround_id?>" onclick="return activateFreeround(<?=$value->fround_id?>)" class="<?=($value->state == 'ACTIVE') ? 'hide' : ''?>"><?=lang('lang.activate')?></a>
                                                <?php
                                                    }
                                                ?>
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

<div class="modal fade " id="cancel_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 40%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php echo lang('Freeround Cancel');?></h4>
      </div>
      <div class="modal-body">
            <!-- add static site -->
            <div class="row">
                <div class="col-md-12">
                    <div class="well"  id="add_freeround_form">
                        <form action="<?=site_url('api/cancelFreeRound')?>" method="post">
                            <input type="hidden" id="fround_id" name="fround_id">
                            <div class="row">
                                <div class="col-md-12">
                                    <h6><label for="promoName"><?=lang('Reason')?>: </label></h6>
                                    <textarea id="reason" name="reason" class="form-control input-sm" required=""></textarea>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                    <br>
                                    <div class="col-md-12">
                                        <div class="col-md-5">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="submit" value="<?=lang('lang.submit')?>" class="btn btn-primary btn-sm btn-block">
                                        </div>
                                        <div class="col-md-5">
                                        </div>
                                    </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
                <!-- end of add static site -->
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#myTable').DataTable();
        $('a[id^="freeroundCancel_"]').on('click', function(e){
            e.preventDefault();
            $('#fround_id').val($(this).data('freeroundid'));
        });
    } );

    function activateFreeround( id ){

        var r = confirm("<?=lang('sys.ga.conf.able.msg')?>");
        if (r == false) return

        $.get("<?=site_url('api/freeRoundActivate')?>/" + id, function(){

            window.location.reload();

        });

        

    }
</script>
