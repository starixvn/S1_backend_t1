<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            <i class="icon-diamond"></i><b> <?= lang($data[0]['groupName']);?> <?=lang('player.lvl');?></b>
            <div class="pull-right">
                <a href="<?=BASEURL . 'vipsetting_management/increaseVipGroupLevel/' . $vipSettingId?>" class="btn  btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-default' ?>">
                    <span class="glyphicon glyphicon-plus"></span> <?=lang('player.incgrplvlcnt');?>
                </a>
                <a href="<?=BASEURL . 'vipsetting_management/decreaseVipGroupLevel/' . $vipSettingId?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-default' ?>">
                    <span class="glyphicon glyphicon-minus"></span> <?=lang('player.decgrplvlcnt');?>
                </a>
                <a href="<?=BASEURL . 'vipsetting_management/vipGroupSettingList'?>" class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-primary' : 'btn-default' ?>" id="add_news">
                    <span class="glyphicon glyphicon-remove"></span>
                </a>
            </div>
        </h3>

        <div class="clearfix"></div>
    </div>
    <div class="panel panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12">
                    <div id="tag_table">
                        <table class="table table-striped table-hover" id="my_table" style="margin: 0px 0 0 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <!-- <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th> -->
                                    <th><?=lang('player.viplvl');?></th>
                                    <th><?=lang('player.lvlname');?></th>
                                    <th><?=lang('player.mindep');?></th>
                                    <th><?=lang('player.maxdep');?></th>
                                    <th><?=lang('player.maxdailywith');?></th>
                                    <!-- <th><?=lang('player.allowgame');?></th> -->
                                    <th><?=lang('lang.action');?></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
if (!empty($data)) {
	foreach ($data as $data) {
		$cnt = 0;
		?>
                                                <tr>
                                                    <td></td>
                                                    <!-- <td><input type="checkbox" class="checkWhite" id="<?=$data['vipSettingId']?>" name="vipgroup[]" value="<?=$data['vipSettingId']?>" onclick="uncheckAll(this.id)"/></td> -->
                                                    <td><?=$data['vipLevel'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $data['vipLevel']?></td>
                                                    <td><?=$data['vipLevelName'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : lang($data['vipLevelName'])?></td>
                                                    <td><?=$data['minDeposit'] == '' ? '<i class="help-block"><?= lang("lang.norecyet"); ?><i/>' : $data['minDeposit']?></td>
                                                    <td><?=$data['maxDeposit'] == '' ? '<i class="help-block"><?= lang("player.nomindep"); ?><i/>' : $data['maxDeposit']?></td>
                                                    <td><?=$data['dailyMaxWithdrawal'] == '' ? '<i class="help-block"><?= lang("lang.nopointreq"); ?><i/>' : $data['dailyMaxWithdrawal']?></td>
                                                    <!-- <td>
                                                        <?php
//var_dump($data['vipsettingcashbackgamerule']);exit();
		if (!empty($data['vipsettingcashbackgamerule'])) {
			if ($data['vipsettingcashbackgamerule']) {
				foreach ($data['vipsettingcashbackgamerule'] as $key) {
					echo '<i>' . lang("cms.gameprovider") . ':</i> ' . $key['game'];
					echo '<br/>';
					echo '<i>' . lang("player.cashbck") . '(%):</i> ' . $key['percentage'] . '%';
					echo '<br/>';
					echo '<i>Max ' . lang("player.cashbck") . ':</i> ' . $key['maxBonus'];
					echo '<br/>';
					echo '<br/>';
				}
			}
		} else {
			echo '<i><?= lang("player.noallowgameset"); ?></i>';
		}
		?>
                                                    </td> -->
                                                    <td>
                                                        <div class="actionVipGroup">
                                                            <a href="<?=BASEURL . 'vipsetting_management/editVipGroupLevel/' . $data['vipsettingcashbackruleId']?>">
                                                                <button class="btn btn-sm <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-info' ?>"><?=lang('player.editset');?></button>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                <?php $cnt++;}
} else {
	?>

                                    <!-- <tr>
                                        <td colspan="6" style="text-align:center"><span class="help-block"><?=lang('lang.norec');?></span></td>
                                    </tr> -->
                                <?php }
?>
                            </tbody>
                        </table>

                        <!-- <div class="col-md-12 col-offset-0">
                            <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links();?> </ul>
                        </div> -->
                    </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#my_table').DataTable({
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            "dom":"<'panel-body' <'pull-right'f><'pull-right progress-container'>l>t<'panel-body'<'pull-right'p>i>",
            buttons: [
    {
      extend: 'colvis',
      postfixButtons: [ 'colvisRestore' ]
    }
    ],
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 1, 'asc' ]
        });
    });
</script>