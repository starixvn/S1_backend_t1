<!-- custom style -->
<style>
	.btn_collapse {
		margin-left: 10px;
	}
</style>
	<!-- START DAFAULT AFFILIATE SHARES -->
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title pull-left">
                    <i class="fa fa-cog"></i> Affilliate Level1 Setup
                </h4>
				<div class="clearfix"></div>
            </div><!-- end panel-heading -->
            <div class="panel-body collapse in" id="affiliate_main_panel_body">
            	<div class="col-md-12">
					<form id="form_affiliate_level0" class="form_affiliate_level" index="0" method="POST" action="<?php echo site_url('/affiliate_management/saveAffilliateLevelSetup/0');?>">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.min.profits')?></div>
										<input type="number" class="form-control user-success" name="min_profits[0]" id="min_profits_0" value="<?=$setting[0]['min_profits']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.max.profits')?></div>
										<input type="number" class="form-control user-success" name="max_profits[0]" id="max_profits_0" value="<?=$setting[0]['max_profits']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.min.activeplayers')?></div>
										<input type="number" class="form-control user-success" name="min_valid_player[0]" id="min_valid_player_0" value="<?=$setting[0]['min_valid_player']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.max.activeplayers')?></div>
										<input type="number" class="form-control user-success" name="max_valid_player[0]" id="max_valid_player_0" value="<?=$setting[0]['max_valid_player']?>" required="">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12 game_provider_tree">
								<input type="hidden" name="selected_game_tree[0]" value="">
								<lablel><strong><?php echo lang('Share on Specific Game Platform'); ?></strong></lablel>
								<fieldset>
									<div class="row">
										<div id="gameTree0" vip_level="0" class="gameTree col-xs-12">
										</div>
									</div>
								</fieldset>
							</div><!-- end col-md-12 -->
						</div>
					    <div class="row">
    						<div class="col-md-12">
								<button type="submit" id="option_1_submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i> <?=lang('sys.vu70');?></button>
							</div>
    					</div>
    				</form>
				</div>

			</div><!-- end panel-body -->
            <!--div class="panel-footer"></div-->
        </div>
    </div>

	<div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title pull-left">
                    <i class="fa fa-cog"></i> Affilliate Level2 Setup
                </h4>
				<div class="clearfix"></div>
            </div><!-- end panel-heading -->

            <div class="panel-body collapse in" id="affiliate_main_panel_body">
            	<div class="col-md-12">
					<form id="form_affiliate_level1" class="form_affiliate_level" index="1" method="POST" action="<?php echo site_url('/affiliate_management/saveAffilliateLevelSetup/1');?>">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.min.profits')?></div>
										<input type="number" class="form-control user-success" name="min_profits[1]" id="min_profits_1" value="<?=$setting[1]['min_profits']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.max.profits')?></div>
										<input type="number" class="form-control user-success" name="max_profits[1]" id="max_profits_1" value="<?=$setting[1]['max_profits']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.min.activeplayers')?></div>
										<input type="number" class="form-control user-success" name="min_valid_player[1]" id="min_valid_player_1" value="<?=$setting[1]['min_valid_player']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.max.activeplayers')?></div>
										<input type="number" class="form-control user-success" name="max_valid_player[1]" id="max_valid_player_1" value="<?=$setting[1]['max_valid_player']?>" required="">
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12 game_provider_tree">
								<input type="hidden" name="selected_game_tree[1]" value="">
								<lablel><strong><?php echo lang('Share on Specific Game Platform'); ?></strong></lablel>
								<fieldset>
									<div class="row">
										<div id="gameTree1" vip_level="1" class="gameTree col-xs-12">
										</div>
									</div>
								</fieldset>
							</div><!-- end col-md-12 -->
						</div>
					    <div class="row">
    						<div class="col-md-12">
								<button type="submit" id="option_2_submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i> <?=lang('sys.vu70');?></button>
							</div>
    					</div>
    				</form>
				</div>

			</div><!-- end panel-body -->
            <!--div class="panel-footer"></div-->
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title pull-left">
                    <i class="fa fa-cog"></i> Affilliate Level3 Setup
                </h4>
				<div class="clearfix"></div>
            </div><!-- end panel-heading -->

            <div class="panel-body collapse in" id="affiliate_main_panel_body">
            	<div class="col-md-12">
					<form id="form_affiliate_level2" class="form_affiliate_level" index="2" method="POST" action="<?php echo site_url('/affiliate_management/saveAffilliateLevelSetup/2');?>">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.min.profits')?></div>
										<input type="number" class="form-control user-success" name="min_profits[2]" id="min_profits_2" value="<?=$setting[2]['min_profits']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.max.profits')?></div>
										<input type="number" class="form-control user-success" name="max_profits[2]" id="max_profits_2" value="<?=$setting[2]['max_profits']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.min.activeplayers')?></div>
										<input type="number" class="form-control user-success" name="min_valid_player[2]" id="min_valid_player_2" value="<?=$setting[2]['min_valid_player']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.max.activeplayers')?></div>
										<input type="number" class="form-control user-success" name="max_valid_player[2]" id="max_valid_player_2" value="<?=$setting[2]['max_valid_player']?>" required="">
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12 game_provider_tree">
								<input type="hidden" name="selected_game_tree[2]" value="">
								<lablel><strong><?php echo lang('Share on Specific Game Platform'); ?></strong></lablel>
								<fieldset>
									<div class="row">
										<div id="gameTree2" vip_level="2" class="gameTree col-xs-12">
										</div>
									</div>
								</fieldset>
							</div><!-- end col-md-12 -->
						</div>
					    <div class="row">
    						<div class="col-md-12">
								<button type="submit" id="option_3_submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i> <?=lang('sys.vu70');?></button>
							</div>
    					</div>
    				</form>
				</div>

			</div><!-- end panel-body -->
            <!--div class="panel-footer"></div-->
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title pull-left">
                    <i class="fa fa-cog"></i> Affilliate Level4 Setup
                </h4>
				<div class="clearfix"></div>
            </div><!-- end panel-heading -->

            <div class="panel-body collapse in" id="affiliate_main_panel_body">
            	<div class="col-md-12">
					<form id="form_affiliate_level3" class="form_affiliate_level" index="3" method="POST" action="<?php echo site_url('/affiliate_management/saveAffilliateLevelSetup/3');?>">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.min.profits')?></div>
										<input type="number" class="form-control user-success" name="min_profits[3]" id="min_profits_3" value="<?=$setting[3]['min_profits']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.max.profits')?></div>
										<input type="number" class="form-control user-success" name="max_profits[3]" id="max_profits_3" value="<?=$setting[3]['max_profits']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.min.activeplayers')?></div>
										<input type="number" class="form-control user-success" name="min_valid_player[3]" id="min_valid_player_3" value="<?=$setting[3]['min_valid_player']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.max.activeplayers')?></div>
										<input type="number" class="form-control user-success" name="max_valid_player[3]" id="max_valid_player_3" value="<?=$setting[3]['max_valid_player']?>" required="">
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12 game_provider_tree">
								<input type="hidden" name="selected_game_tree[3]" value="">
								<lablel><strong><?php echo lang('Share on Specific Game Platform'); ?></strong></lablel>
								<fieldset>
									<div class="row">
										<div id="gameTree3" vip_level="3" class="gameTree col-xs-12">
										</div>
									</div>
								</fieldset>
							</div><!-- end col-md-12 -->
						</div>
					    <div class="row">
    						<div class="col-md-12">
								<button type="submit" id="option_4_submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i> <?=lang('sys.vu70');?></button>
							</div>
    					</div>
    				</form>
				</div>

			</div><!-- end panel-body -->
            <!--div class="panel-footer"></div-->
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title pull-left">
                    <i class="fa fa-cog"></i> Affilliate Level5 Setup
                </h4>
				<div class="clearfix"></div>
            </div><!-- end panel-heading -->

            <div class="panel-body collapse in" id="affiliate_main_panel_body">
            	<div class="col-md-12">
					<form id="form_affiliate_level4" class="form_affiliate_level" index="4" method="POST" action="<?php echo site_url('/affiliate_management/saveAffilliateLevelSetup/4');?>">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.min.profits')?></div>
										<input type="number" class="form-control user-success" name="min_profits[4]" id="min_profits_4" value="<?=$setting[4]['min_profits']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.max.profits')?></div>
										<input type="number" class="form-control user-success" name="max_profits[4]" id="max_profits_4" value="<?=$setting[4]['max_profits']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.min.activeplayers')?></div>
										<input type="number" class="form-control user-success" name="min_valid_player[4]" id="min_valid_player_4" value="<?=$setting[4]['min_valid_player']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.max.activeplayers')?></div>
										<input type="number" class="form-control user-success" name="max_valid_player[4]" id="max_valid_player_4" value="<?=$setting[4]['max_valid_player']?>" required="">
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12 game_provider_tree">
								<input type="hidden" name="selected_game_tree[4]" value="">
								<lablel><strong><?php echo lang('Share on Specific Game Platform'); ?></strong></lablel>
								<fieldset>
									<div class="row">
										<div id="gameTree4" vip_level="4" class="gameTree col-xs-12">
										</div>
									</div>
								</fieldset>
							</div><!-- end col-md-12 -->
						</div>
					    <div class="row">
    						<div class="col-md-12">
								<button type="submit" id="option_5_submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i> <?=lang('sys.vu70');?></button>
							</div>
    					</div>
    				</form>
				</div>

			</div><!-- end panel-body -->
            <!--div class="panel-footer"></div-->
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title pull-left">
                    <i class="fa fa-cog"></i> Affilliate Level6 Setup
                </h4>
				<div class="clearfix"></div>
            </div><!-- end panel-heading -->

            <div class="panel-body collapse in" id="affiliate_main_panel_body">
            	<div class="col-md-12">
					<form id="form_affiliate_level5" class="form_affiliate_level" index="5" method="POST" action="<?php echo site_url('/affiliate_management/saveAffilliateLevelSetup/5');?>">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.min.profits')?></div>
										<input type="number" class="form-control user-success" name="min_profits[5]" id="min_profits_4" value="<?=$setting[5]['min_profits']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.max.profits')?></div>
										<input type="number" class="form-control user-success" name="max_profits[5]" id="max_profits_4" value="<?=$setting[5]['max_profits']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.min.activeplayers')?></div>
										<input type="number" class="form-control user-success" name="min_valid_player[5]" id="min_valid_player_5" value="<?=$setting[5]['min_valid_player']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.max.activeplayers')?></div>
										<input type="number" class="form-control user-success" name="max_valid_player[5]" id="max_valid_player_5" value="<?=$setting[5]['max_valid_player']?>" required="">
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12 game_provider_tree">
								<input type="hidden" name="selected_game_tree[5]" value="">
								<lablel><strong><?php echo lang('Share on Specific Game Platform'); ?></strong></lablel>
								<fieldset>
									<div class="row">
										<div id="gameTree5" vip_level="5" class="gameTree col-xs-12">
										</div>
									</div>
								</fieldset>
							</div><!-- end col-md-12 -->
						</div>
					    <div class="row">
    						<div class="col-md-12">
								<button type="submit" id="option_6_submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i> <?=lang('sys.vu70');?></button>
							</div>
    					</div>
    				</form>
				</div>

			</div><!-- end panel-body -->
            <!--div class="panel-footer"></div-->
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title pull-left">
                    <i class="fa fa-cog"></i> Affilliate Level7 Setup
                </h4>
				<div class="clearfix"></div>
            </div><!-- end panel-heading -->

            <div class="panel-body collapse in" id="affiliate_main_panel_body">
            	<div class="col-md-12">
					<form id="form_affiliate_level6" class="form_affiliate_level" index="6" method="POST" action="<?php echo site_url('/affiliate_management/saveAffilliateLevelSetup/6');?>">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.min.profits')?></div>
										<input type="number" class="form-control user-success" name="min_profits[6]" id="min_profits_4" value="<?=$setting[6]['min_profits']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.max.profits')?></div>
										<input type="number" class="form-control user-success" name="max_profits[6]" id="max_profits_4" value="<?=$setting[6]['max_profits']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.min.activeplayers')?></div>
										<input type="number" class="form-control user-success" name="min_valid_player[6]" id="min_valid_player_6" value="<?=$setting[6]['min_valid_player']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.max.activeplayers')?></div>
										<input type="number" class="form-control user-success" name="max_valid_player[6]" id="max_valid_player_6" value="<?=$setting[6]['max_valid_player']?>" required="">
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12 game_provider_tree">
								<input type="hidden" name="selected_game_tree[6]" value="">
								<lablel><strong><?php echo lang('Share on Specific Game Platform'); ?></strong></lablel>
								<fieldset>
									<div class="row">
										<div id="gameTree6" vip_level="6" class="gameTree col-xs-12">
										</div>
									</div>
								</fieldset>
							</div><!-- end col-md-12 -->
						</div>
					    <div class="row">
    						<div class="col-md-12">
								<button type="submit" id="option_7_submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i> <?=lang('sys.vu70');?></button>
							</div>
    					</div>
    				</form>
				</div>

			</div><!-- end panel-body -->
            <!--div class="panel-footer"></div-->
        </div>
    </div>    

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title pull-left">
                    <i class="fa fa-cog"></i> Affilliate Level8 Setup
                </h4>
				<div class="clearfix"></div>
            </div><!-- end panel-heading -->

            <div class="panel-body collapse in" id="affiliate_main_panel_body">
            	<div class="col-md-12">
					<form id="form_affiliate_level7" class="form_affiliate_level" index="7" method="POST" action="<?php echo site_url('/affiliate_management/saveAffilliateLevelSetup/7');?>">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.min.profits')?></div>
										<input type="number" class="form-control user-success" name="min_profits[7]" id="min_profits_4" value="<?=$setting[7]['min_profits']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.max.profits')?></div>
										<input type="number" class="form-control user-success" name="max_profits[7]" id="max_profits_4" value="<?=$setting[7]['max_profits']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.min.activeplayers')?></div>
										<input type="number" class="form-control user-success" name="min_valid_player[7]" id="min_valid_player_7" value="<?=$setting[7]['min_valid_player']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.max.activeplayers')?></div>
										<input type="number" class="form-control user-success" name="max_valid_player[7]" id="max_valid_player_7" value="<?=$setting[7]['max_valid_player']?>" required="">
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12 game_provider_tree">
								<input type="hidden" name="selected_game_tree[7]" value="">
								<lablel><strong><?php echo lang('Share on Specific Game Platform'); ?></strong></lablel>
								<fieldset>
									<div class="row">
										<div id="gameTree7" vip_level="7" class="gameTree col-xs-12">
										</div>
									</div>
								</fieldset>
							</div><!-- end col-md-12 -->
						</div>
					    <div class="row">
    						<div class="col-md-12">
								<button type="submit" id="option_8_submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i> <?=lang('sys.vu70');?></button>
							</div>
    					</div>
    				</form>
				</div>

			</div><!-- end panel-body -->
            <!--div class="panel-footer"></div-->
        </div>
    </div>

    <div class="col-md-12" style="display: none">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title pull-left">
                    <i class="fa fa-cog"></i> Affilliate Level9 Setup
                </h4>
				<div class="clearfix"></div>
            </div><!-- end panel-heading -->

            <div class="panel-body collapse in" id="affiliate_main_panel_body">
            	<div class="col-md-12">
					<form id="form_affiliate_level8" class="form_affiliate_level" index="8" method="POST" action="<?php echo site_url('/affiliate_management/saveAffilliateLevelSetup/8');?>">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.min.profits')?></div>
										<input type="number" class="form-control user-success" name="min_profits[8]" id="min_profits_4" value="<?=$setting[8]['min_profits']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.max.profits')?></div>
										<input type="number" class="form-control user-success" name="max_profits[8]" id="max_profits_4" value="<?=$setting[8]['max_profits']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.min.activeplayers')?></div>
										<input type="number" class="form-control user-success" name="min_valid_player[8]" id="min_valid_player_8" value="<?=$setting[8]['min_valid_player']?>" required="">
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=lang('aff.sl.max.activeplayers')?></div>
										<input type="number" class="form-control user-success" name="max_valid_player[8]" id="max_valid_player_8" value="<?=$setting[8]['max_valid_player']?>" required="">
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12 game_provider_tree">
								<input type="hidden" name="selected_game_tree[8]" value="">
								<lablel><strong><?php echo lang('Share on Specific Game Platform'); ?></strong></lablel>
								<fieldset>
									<div class="row">
										<div id="gameTree8" vip_level="8" class="gameTree col-xs-12">
										</div>
									</div>
								</fieldset>
							</div><!-- end col-md-12 -->
						</div>
					    <div class="row">
    						<div class="col-md-12">
								<button type="submit" id="option_9_submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i> <?=lang('sys.vu70');?></button>
							</div>
    					</div>
    				</form>
				</div>

			</div><!-- end panel-body -->
            <!--div class="panel-footer"></div-->
        </div>
    </div>            
<!-- END DEFAULT AFFILIATE SHARES -->
<script type="text/javascript">

	(function($){
		$('.gameTree').each(function(index){
			var vip_level = $(this).attr('vip_level');
	        $('#gameTree' + vip_level).jstree({
	          	'core' : {
	            	'data' : {
	              		"url" : "<?php echo site_url('/api/get_game_tree_by_affilliate_Level2/'); ?>/" + vip_level,
	              		"dataType" : "json" // needed only if you do not supply JSON headers
	            	}
	          	},
	          	"input_number":{
	            	"form_sel": '#form_affiliate_level' + vip_level
	          	},
	          	"checkbox":{
	            	"tie_selection": false,
	          	},
          		"plugins":[
            		"search","checkbox","input_number"
          		]
	        });
		});


        $('.form_affiliate_level').submit(function(e){
        	var index = $(this).attr('index');
       		var selected_game = $('#form_affiliate_level' + index + ' #gameTree' + index).jstree('get_checked');
       		if (selected_game.length > 0) {
	       		$('#form_affiliate_level' + index + ' input[name="selected_game_tree[' + index + ']"]').val(selected_game.join());
		       	$('#form_affiliate_level' + index + ' #gameTree' + index).jstree('generate_number_fields');
		    } else {
	            BootstrapDialog.alert("<?php echo lang('Please choose one game at least'); ?>");
	            e.preventDefault();
		    }
       	});
	})(jQuery);

	// prevent negative value
	$('input[type="number"]').on('change', function(){
		if($(this).val() < 0) $(this).val(0);
	});


	// START DEFAULT AFFILIATE SHARES JS ===============================================

	$('.btn_collapse').on('click', function(){
		// get current state
		var child = $(this).find('i');

		// change ui
		if(child.hasClass('glyphicon-chevron-down')) {
		   child.removeClass('glyphicon-chevron-down');
		   child.addClass('glyphicon-chevron-up')
		} else {
		   child.removeClass('glyphicon-chevron-up');
		   child.addClass('glyphicon-chevron-down')
		}
	});

</script>
