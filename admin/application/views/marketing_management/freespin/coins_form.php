<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <!-- <i class="icon-bullhorn"></i> --> <?=lang('Add Coin');?>
            <a href="<?=site_url('marketing_management/freeroundCoins/' .$fround_id)?>" class="btn btn-primary pull-right" id="add_promocms_sec" style="color: #fff; margin-top: -4px; margin-right: 5px;" data-original-title="" title="">
                <span id="addPromoCmsGlyhicon; " class="glyphicon glyphicon-arrow-left"></span> 
                    <?=lang('Back to list')?>
                </a>
            <div class="clearfix"></div>
        </h4>
    </div>
    <div class="panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12">
                <form id="frm_player_create" action="<?=site_url('marketing_management/addFreeroundCoins')?>" method="post">
                    <input type="hidden" name="fround_id" value="<?=$fround_id?>">
                    <div id="promorule_table" class="table-responsive" style="overflow: hidden;">
                        <div class="row">
                            <div class="col-md-12">
                                <h6>
                                    <label for="promoName">
                                        <?=lang('lang.games')?>: 
                                    </label>
                                </h6>
                                <select id="game_id" name="game_id" class="form-control" required="">
                                    <option value="">-- <?=lang('Select Game')?> --</option>

                                    <?php
                                        if( ! empty( $games ) ){

                                            foreach ($games as $key => $value) {
                                    ?>
                                                <option value="<?=$value['game_code']?>"><?=lang($value['game_name'])?></option>

                                    <?php
                                            }

                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="coin">
                            
                        </div>
                        <br>
                        <div class="row">
                                <br>
                                <div class="col-md-12">
                                    <div class="col-md-5">
                                    </div>
                                    <div class="col-md-2">
                                        <input for="frm_player_create" type="submit" value="<?=lang('lang.submit')?>" class="btn btn-primary btn-sm btn-block">
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

<script type="text/javascript" src="<?=site_url('resources/js/freespin.js')?>"></script>
<script type="text/javascript">
    $(function(){

        FreeSpin.invalidCoin = "<?=lang('Please enter valid coin value')?>";
        FreeSpin.init();

        // $('body').on('submit', '#frm_player_create', function(e){
        //     e.preventDefault();
        //     $.ajax({
        //         url: $('#frm_player_create').attr('action'),
        //         type: 'POST',
        //         data: $('#frm_player_create').serialize(),
        //         success: function(){
        //             console.log('here');
        //         }
        //     });
        // });

    });

</script>

