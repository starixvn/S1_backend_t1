<script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-html5sortable/1.0.0/jquery.sortable.min.js');?>"></script>
<link rel="stylesheet" type="text/css" href="<?=$this->utils->thirdpartyUrl('bootstrap-switch/3.3.4/css/bootstrap3/bootstrap-switch.min.css')?>" />
<script type="text/javascript" src="<?=$this->utils->thirdpartyUrl('bootstrap-switch/3.3.4/js/bootstrap-switch.min.js');?>"></script>
<div class="panel panel-primary panel_main">
	<div class="panel-heading" id="">
		<h4 class="panel-title"><i class="fa fa-cogs"></i> &nbsp;<?php echo $title; ?>
            <?php if($this->utils->getConfig('use_new_sbe_color')){?>
                <span class="pull-right">
                    <a data-toggle="collapse" href="#main_panel" class="btn btn-info btn-xs" aria-expanded="true"></a>
                </span>
            <?php }else{?>
		        <a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
            <?php }?>
		</h4>
	</div>
	<div id="main_panel" class="panel-collapse collapse in ">
		<div class="panel-body">
            <table class="table table-sortable">
                <thead>
                    <tr>
                        <td class="col col-md-2">#</td>
                        <td class="col col-md-4"><?=lang('Wallet')?></td>
                        <td class="col col-md-3"><?=lang('Enabled on Desktop')?></td>
                        <td class="col col-md-3"><?=lang('Enabled on Mobile')?></td>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($game_wallet_settings as $wallet_id => $game_wallet_setting_entry):
                    if(!isset($game_wallet_setting_entry['wallet_name'])) continue;
                    ?>
                    <tr data-wallet-id="<?=$wallet_id?>">
                        <td><span class="glyphicon glyphicon-move"></span></td>
                        <td><?=$game_wallet_setting_entry['wallet_name']?></td>
                        <td><input type="checkbox" name="enabled_on_desktop" <?=($game_wallet_setting_entry['enabled_on_desktop']) ? 'checked="checked"' : ''?> /></td>
                        <td><input type="checkbox" name="enabled_on_mobile" <?=($game_wallet_setting_entry['enabled_on_mobile']) ? 'checked="checked"' : ''?> /></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
		</div>
		<div class="panel-footer">
			<input type="submit" class="btn btn_save <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>" value="<?php echo lang('Save'); ?>">
		</div>
	</div>
</div>

<script type="text/javascript">
$(function(){
    $("[type='checkbox']").bootstrapSwitch();

    $('.table-sortable tbody').sortable({
        handle: 'span.glyphicon-move'
    });

    $('.btn_save').on('click', function(){
        var game_wallet_settings = {};

        var sort = 0;
        $('.table-sortable tbody tr').each(function(){
            sort++;

            var wallet_id = $(this).data('wallet-id');
            var game_wallet_setting = {};
            game_wallet_setting['sort'] = sort;
            game_wallet_setting['enabled_on_desktop'] = !!($('[name="enabled_on_desktop"]', this).is(':checked'));
            game_wallet_setting['enabled_on_mobile'] = !!($('[name="enabled_on_mobile"]', this).is(':checked'));

            game_wallet_settings[wallet_id] = game_wallet_setting;
        });

        var data = {
            "game_wallet_settings": JSON.stringify(game_wallet_settings)
        };

        BootstrapDialog.show({
            message: 'I send ajax request!',
            onshow: function(dialogRef){
                dialogRef.enableButtons(false);
                dialogRef.setClosable(false);
                dialogRef.getModalBody().html('');

                var $btnClose = dialogRef.getButton('btn-close');
                $btnClose.spin();

                $.ajax({
                    "contentType": "application/x-www-form-urlencoded; charset=UTF-8",
                    "url": "/system_management/save_game_wallet_settings",
                    "type": "POST",
                    "data": data,
                    "success": function(data){
                        $btnClose.stopSpin();
                        dialogRef.enableButtons(true);
                        dialogRef.setClosable(true);
                        dialogRef.getModalBody().html(data.message);
                    },
                    "error": function(){
                        $btnClose.stopSpin();
                        dialogRef.enableButtons(true);
                        dialogRef.setClosable(true);
                    }
                });
            },
            onhide: function(){
                window.location.reload(true);
            },
            buttons: [{
                id: 'btn-close',
                icon: 'glyphicon glyphicon-send',
                label: 'Close',
                cssClass: 'btn-primary',
                action: function(dialogRef){
                    dialogRef.close();
                }
            }]
        });
    });
});
</script>