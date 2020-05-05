<div class="container">
    <div class="col-md-12">
        <div class="panel panel-og">
            <div class="panel-heading">
                <h4 class="panel-title pull-left"><?=lang('reg.68');?></h4>
                <div class="clearfix"></div>
            </div>

            <div class="panel panel-body" id="add_player_panel_body">

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <center> <?=lang('reg.52');?> </center>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading panel-title">
                                <h2 class="media-heading"><strong><?=lang('reg.54');?></strong></h2>
                            </div>
                            <div class="panel-body">
                                <form method="post" action="<?=BASEURL . 'player_center/resendEmail';?>">
                                    <input type="hidden" value="<?=$player['playerId'];?>" name="playerId" />
                                    <p>
                                        <?=lang('reg.64');?> <a href="#"><?=$player['email']?></a>.<br />
                                        <?=lang('reg.65');?> <?=lang('reg.70');?>

                                        <?=lang('reg.66');?>:
                                        <input type="submit" class="btn btn-hotel btn-sm" value="<?=lang('reg.67');?>" />
                                        <br />

                                        <?=lang('reg.customer_welcome');?>

                                        <a href="<?php echo site_url('player_center/iframe_viewCashier') ?>" class="btn btn-hotel btn-sm"><?=lang('button.go_home')?></a>
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>