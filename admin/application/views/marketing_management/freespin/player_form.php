<div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <!-- <i class="icon-bullhorn"></i> --> <?=lang('Add Player');?>
            <a href="<?=site_url('marketing_management/freeroundPlayers/' .$fround_id)?>" class="btn btn-primary pull-right" id="add_promocms_sec" style="color: #fff; margin-top: -4px; margin-right: 5px;" data-original-title="" title="">
                <span id="addPromoCmsGlyhicon; " class="glyphicon glyphicon-arrow-left"></span> 
                    <?=lang('Back to list')?>
                </a>
            <div class="clearfix"></div>
        </h4>
    </div>
    <div class="panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12">
                <form id="frm_player_create" action="<?=site_url('marketing_management/addFreeroundPlayer')?>" method="post">
                    <input type="hidden" name="fround_id" value="<?=$fround_id?>">
                    <div id="promorule_table" class="table-responsive" style="overflow: hidden;">
                        <div class="row">
                            <div class="col-md-12">
                                <h6>
                                    <label for="promoName">
                                        <?=lang('Players')?>: 
                                    </label>
                                </h6>
                                <select id="player_ids" name="player_ids[]" multiple="multiple" class="chosen-select" required="">

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
                                <h6><label for="promoName"><?=lang('Promo Code')?>: </label></h6>
                                <input type="text" id="promo_code" name="promo_code" class="form-control input-sm">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                                <br>
                                <div class="col-md-12">
                                    <div class="col-md-5">
                                    </div>
                                    <div class="col-md-2">
                                        <input for="frm_player_create" type="submit" value="Submit" class="btn btn-primary btn-sm btn-block">
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


<script type="text/javascript">
    $(function(){

        $(".chosen-select").chosen({
            disable_search: true,
        });

    });

</script>

