<!-- custom style -->
<style>
	.btn_collapse {
		margin-left: 10px;
	}
</style>
<?php
	$level2type = (isset($setting['level2type'])? $setting['level2type'] : 2);
?>
	<!-- START DAFAULT AFFILIATE SHARES -->
<form id="form_sub_affiliate_level" class="form_sub_affiliate_level" index="0" method="POST" action="<?php echo site_url('/affiliate_management/saveAffilliateSubLevelSetup');?>">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title pull-left">
                    <i class="fa fa-cog"></i> Affilliate Sub Level Setup
                </h4>
				<div class="clearfix"></div>
            </div><!-- end panel-heading -->
            <div class="panel-body collapse in" id="affiliate_main_panel_body">
            	<div class="col-md-12">
					<br>
           			<label><strong>Level Master</strong></label>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<div class="input-group">
									<input class="form-control" name="levelmaster" type="number" min="0" value="<?=(isset($setting['levelmaster']) ? $setting['levelmaster'] : 0); ?>"/>
							      	<div class="input-group-addon">%</div>
								</div>
							</div>
						</div>
					</div>
				</div>
            	<div class="col-md-12">
					<br>
           			<label><strong>Level 1</strong></label>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<div class="input-group">
									<input class="form-control" name="level1rate" type="number" min="0" value="<?=(isset($setting['level1rate']) ? $setting['level1rate'] : 0); ?>"/>
							      	<div class="input-group-addon">%</div>
								</div>
							</div>
						</div>
					</div>
				</div>
            	<div class="col-md-12">
					<br>
           			<label><strong>Level 2</strong></label>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<div class="input-group">
									<input type="radio" name="level2type" value="1" <?=((int)$level2type == 1 ? 'checked="checked"' : '');?>/> By Game Type
							      	<input type="radio" name="level2type" value="2" <?=((int)$level2type == 2 ? 'checked="checked"' : '');?>/> By Single Rate
								</div>
							</div>
						</div>
					</div>
					<div class="row" id="level2gamerate" style="">
						<div class="col-md-3">
							<div class="form-group">
								<div class="input-group">
									<input class="form-control" name="level2Rate" type="number" min="0" value="<?=(isset($setting['level2Rate']) ? $setting['level2Rate'] : 0); ?>"/>
							      	<div class="input-group-addon">%</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row" id="level2gametree" style="display: none">
						<div class="col-md-12">
							<input type="hidden" name="selected_game_tree" value="">
							<fieldset>
								<div class="row">
									<div id="gameTree" class="col-xs-12">
									</div>
								</div>
							</fieldset>
						</div><!-- end col-md-12 -->
					</div>
				</div>
			</div><!-- end panel-body -->
		    <div class="row">
		    	<div class="col-md-5"></div>
				<div class="col-md-2">
					<button type="submit" id="option_1_submit" class="btn btn-primary pull-right"><i class="fa fa-floppy-o"></i> <?=lang('sys.vu70');?></button>
				</div>
		    	<div class="col-md-5"></div>
    		</div>
            <!--div class="panel-footer"></div-->
        </div>
    </div>
</form>
    
<!-- END DEFAULT AFFILIATE SHARES -->
<script type="text/javascript">

	(function($){
	    $('#gameTree').jstree({
	      	'core' : {
	        	'data' : {
	        		url: '/api/get_game_tree_by_affilliate_sub_Level2',
	        		type: 'json'
	        	}
	      	},
	      	"input_number":{
	        	"form_sel": '#form_sub_affiliate_level'
	      	},
	      	"checkbox":{
	        	"tie_selection": false,
	      	},
        	"plugins":[
            	"search","checkbox","input_number"
          	]
	    });

        $('#form_sub_affiliate_level').submit(function(e){
       		var selected_game = $('#gameTree').jstree('get_checked');
       		if (selected_game.length > 0) {
	       		$('input[name="selected_game_tree"]').val(selected_game.join());
		       	$('#gameTree').jstree('generate_number_fields');
		    } else {
	            BootstrapDialog.alert("<?php echo lang('Please choose one game at least'); ?>");
	            e.preventDefault();
		    }
       	});

       	$('input[name="level2type"]').on('change', function(e){
       		switch(parseInt($(this).val())) {
       			case 1 :
       				$('#level2gamerate').hide();
       				$('#level2gametree').show();
       				break;
       			case 2 :
       				$('#level2gamerate').show();
       				$('#level2gametree').hide();
       				break;
       		}
       	});
       	<?php
       	switch($level2type) {
       		case 1 :
       	?>
		$('#level2gamerate').hide();
		$('#level2gametree').show();
       	<?php
       			break;
       		case 2 :
       	?>
		$('#level2gamerate').show();
		$('#level2gametree').hide();
       	<?php
       			break;
       	}
       	?>
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
