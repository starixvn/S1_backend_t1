<div class="row">
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h4 class="panel-title pull-left"><i class="glyphicon glyphicon-picture"></i> <?=lang('player.sd09')?> </h4>
				<a href="<?= BASEURL . 'player_management/FriendReferralLevelSetup'?>" class="btn btn-primary btn-sm pull-right" id="banner_settings"><span class="glyphicon glyphicon-remove"></span></a>
				<div class="clearfix"></div>
			</div>

			<div class="panel panel-body" id="banner_panel_body">
				<form method="POST" id="friend_referral_level" action="<?= BASEURL . 'player_management/saveFriendReferralLevel'?>" accept-charset="utf-8" enctype="multipart/form-data">
					<input type="hidden" name="id" value="<?=(!empty($setting))?$setting["id"]:""?>">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon" style=""><?=lang('player.frl01')?>:</div>
									<input class="form-control" type="number" name="min_betting" id="min_betting" min="0" value="<?=(!empty($setting))?$setting['min_betting']:0;?>" required=""/>
									<label style="color: red; font-size: 12px;"><?php echo form_error('min_betting'); ?></label>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon" style=""><?=lang('player.frl02')?>:</div>
									<input class="form-control" type="number" name="max_betting" id="max_betting" min="0" value="<?=(!empty($setting))?$setting['max_betting']:0;?>" required=""/>
									<label style="color: red; font-size: 12px;"><?php echo form_error('max_betting'); ?></label>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon" style=""><?=lang('player.frl03')?>:</div>
									<input class="form-control" type="number" name="min_volid_player" id="min_volid_player" min="0" value="<?=(!empty($setting))?$setting['min_volid_player']:0;?>" required=""/>
									<label style="color: red; font-size: 12px;"><?php echo form_error('min_volid_player'); ?></label>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon" style=""><?=lang('player.frl04')?>:</div>
									<input class="form-control" type="number" name="max_volid_player" id="max_volid_player" min="0" value="<?=(!empty($setting))?$setting['max_volid_player']:0;?>" required=""/>
									<label style="color: red; font-size: 12px;"><?php echo form_error('max_volid_player'); ?></label>
								</div>
							</div>
						</div>
						<div class="col-md-12">
								<input type="hidden" name="selected_game_tree" value="">
								<lablel><strong><?php echo lang('Share on Specific Game Platform'); ?></strong></lablel>
								<fieldset>
									<div class="row">
										<div id="gameTree" class="gameTree col-xs-12">
										</div>
									</div>
								</fieldset>
						</div>
					</div>
					<div class="row">
						<div class="col-md-5 col-md-offset-0"></div>
						<div class="col-md-2 col-md-offset-0">
							<input type="submit" name="submit" id="submit" class="form-control btn btn-primary" value="<?=lang('save')?>">
						</div>
						<div class="col-md-5 col-md-offset-0"></div>
					</div>
				</form>
			</div>

			<div class="panel-footer">

			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
	    $('#gameTree').jstree({
	      	'core' : {
	        	'data' : <?=isset($selected_game_tree)? $selected_game_tree : '[]';?>
	      	},
	      	"input_number":{
	        	"form_sel": '#friend_referral_level'
	      	},
	      	"checkbox":{
	        	"tie_selection": false,
	      	},
        	"plugins":[
            	"search","checkbox","input_number"
          	]
	    });

        $('form').submit(function(e){
       		var selected_game = $('#gameTree').jstree('get_checked');
       		if (selected_game.length > 0) {
	       		$('input[name="selected_game_tree"]').val(selected_game.join());
		       	$('#gameTree').jstree('generate_number_fields');
		    } else {
	            BootstrapDialog.alert("<?php echo lang('Please choose one game at least'); ?>");
	            e.preventDefault();
		    }
       	});

		// prevent negative value
		$('input[type="number"]').on('change', function(){
			if($(this).val() < 0) $(this).val(0);
		});
	});
</script>