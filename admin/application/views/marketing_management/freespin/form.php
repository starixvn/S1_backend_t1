<style type="text/css">
    .ui-datepicker-header.ui-widget-header.ui-helper-clearfix.ui-corner-all{
        background: #006687;
        color: #fff;
    }
</style>
<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <!-- <i class="icon-bullhorn"></i> --> <?=lang('Free Round Package');?>
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
                <form id="frm_freespin_create" action="<?=site_url('api/set_coin')?>" method="post">
                    <div id="promorule_table" class="table-responsive" style="overflow: hidden;">
                        
                        <div class="row">
                            <div class="col-md-12">
                                <h6><label for="promoName"><?=lang('Package Name')?>: </label></h6>
                                <input type="text" id="package_name" name="name" class="form-control input-sm" required="">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <h6><label for="promoName"><?=lang('Player Limit')?>: </label></h6>
                                <input type="text" id="player_limit" name="max_players" class="form-control input-sm">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <h6><label for="promoName"><?=lang('Promo code')?>: </label></h6>
                                <input type="text" id="promo_code" name="promo_code" class="form-control input-sm">
                            </div>
                        </div>
                        <!-- <br>
                        <div class="row">
                            <div class="col-md-12">
                                <h6><label for="promoName"><?=lang('Operator')?>: </label></h6>
                                <input type="text" id="operator" name="operator" class="form-control input-sm" required="">
                            </div>
                        </div> -->
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <h6>
                                    <label for="promoName">
                                        <?=lang('lang.games')?>: 
                                        <input type="checkbox" class="chosen-toggle select"> <?=lang('Select All')?> 
                                    </label>
                                </h6>
                                <select id="games" name="games[]" multiple="" class="form-control chosen-select">
                                    <!-- <option value=""><?=lang('lang.select')?></option> -->
                                    <?php

                                        if( ! empty( $games ) ){

                                            foreach ($games as $key => $value) {
                                    ?>
                                                <option value="<?=$value['game_code']?>" ><?=lang($value['game_name'])?></option>
                                    <?php
                                            }

                                        }

                                    ?>

                                    
                                </select>
                            </div>
                        </div>

                        <div class="coin"></div>

                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <h6><label for="promoName"><?=lang('Player')?>: </label></h6>
                                <select id="player_ids" name="player_ids[]" multiple="multiple" class="chosen-select">

                                    <?php
                                        if( ! empty($players) ){
                                            foreach ($players as $key => $value) {
                                    ?>
                                                <option value="<?=$value['username']?>"><?=$value['username']?></option>
                                    <?php
                                            }
                                        }
                                    ?>
                                    
                                </select>
                            </div>
                        </div>


                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <h6><label for="promoName"><?=lang('Limit Per Player')?>: </label></h6>
                                <input type="text" id="limit_per_player" name="limit_per_player" class="form-control input-sm" required="">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <h6><label for="promoName"><?=lang('Start Date')?>: </label></h6>
                                <input type="text" id="start_date" name="start_date" class="form-control input-sm datepicker" required="" style="display: inline;" required="">
                                <!-- <input type="text" id="start_time" name="start_time" class="form-control input-sm" style="display: inline;" required=""> -->
                                <span>
                                    <input type="checkbox" name="start_in_five_min" id="start_in_five_min"> <label><?=lang('Start In 5 Minutes')?></label>
                                </span>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <h6><label for="promoName"><?=lang('End Date')?>: </label></h6>
                                <input type="text" id="end_date" name="end_date" class="form-control input-sm datepicker" required="" style="display: inline;" required="">
                                <!-- <input type="text" id="end_time" name="end_time" class="form-control input-sm" style="display: inline;" required=""> -->
                                <span>
                                    <!-- <input type="checkbox" name="has_end_date" id="has_end_date"> <label><?=lang('Has End Date')?></label> -->
                                </span>
                            </div>
                        </div>
                        <!-- <br>
                        <div class="row">
                            <div class="col-md-12">
                                <h6><label for="promoName"><?=lang('Relative Duration (in days)')?>: </label></h6>
                                <input type="text" id="relative_duration" name="relative_duration" class="form-control input-sm" required="">
                            </div>
                        </div> -->
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

                    </div>
                </form>
            </div>
        </div>
    </div><div class="panel-footer"></div>
</div>
<a href="#" class="h_dialog_trigger hide" data-toggle="modal" data-target="#packageDetails">sdas</a>
<div class="modal fade " id="packageDetails" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 50%">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?php echo lang('Create Package Details');?></h4>
      </div>
      <div class="modal-body">
            <!-- add static site -->
            <div class="row">
                <div class="col-md-12">
                    <div class="well"  id="add_freeround_form">
                        
                    </div>
                </div>
            </div>
                <!-- end of add static site -->
      </div>
    </div>
  </div>
</div>

<style type="text/css">
    input#start_date, input#end_date, input#start_time, input#end_time {
        width: 255px;
    }
</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="<?=site_url('resources/css/bootstrap-datetimepicker.css')?>">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="<?=site_url('resources/js/jquery.maskedinput.js')?>"></script>
<script type="text/javascript" src="<?=site_url('resources/js/bootstrap-datetimepicker.min.js')?>"></script>
<script type="text/javascript" src="<?=site_url('resources/js/freespin.js')?>"></script>

<script type="text/javascript">
    $(function(){
        FreeSpin.invalidCoin = "<?=lang('Please enter valid coin value')?>";
        FreeSpin.init();

        // $('.datepicker').daterangepicker({
        //     singleDatePicker: true,
        //     showDropdowns: true,
        //     minDate: new Date
        // });

        $(".datepicker").datetimepicker({
            format: "yyyy-mm-dd hh:ii",
        });

        // $('#start_time, #end_time').mask('00:00:00', {placeholder:"hh:mm:ss"});
         $('#frm_freespin_create').on('submit', function(e){
            e.preventDefault();
            submitForm();
        });
    });

    function submitForm(){


        var games = $('#games').val();

        games = games.join(',');

        $.ajax({
            url: $('#frm_freespin_create').attr('action'),
            type: 'POST',
            data: $('#frm_freespin_create').serialize(),
            success: function(data){
                $('#add_freeround_form').html(data);
                $('.h_dialog_trigger').trigger('click');
            }
        });

    }

</script>

