<?php $playerId = $player['playerId']; ?>
<?php if($this->utils->isEnabledFeature('add_suspended_status')):?>
    <div class="panel panel-primary">
        <div class="panel-footer">
            <form action="#" method="post">
                <center>
                    <div class="form-group">
                        <p>
                            <?= lang('system.word98') ?> <strong><?= strtoupper($player['username']) ?></strong>
                        </p>
                        <p>
                            <?= lang('system.word99') ?> :
                            <select name="optBlockingReason" id="optBlockingReason" data-flg="x" required>
                                <option value=""><?=lang('Select block reason')?></option>
                                <?php if(!empty($tags)) : ?>
                                    <?php foreach ($tags as $key => $value) : ?>
                                        <option value="<?=$value['tagId']?>" data-description="<?=$value['tagDescription']?>" ><?=$value['tagName']?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </p>
                        <h4><strong><span class="glyphicon glyphicon-info-sign"></span> <span id="spnTagDescription"></span></strong></h4>
                    </div>
                    <br>

                    <button class="btn btn-danger" type="button" id="iBlock">
                        <span class="glyphicon glyphicon-lock"></span> <?=lang('Block')?>
                    </button>
                    <button class="btn btn-danger" type="button" id="iSuspend">
                        <span class="glyphicon glyphicon-lock"></span> <?=lang('Suspended')?>
                    </button>
                    <div class="clearfix"></div>
                </center>
            </form>
        </div>
    </div>
<?php else:?>
    <div class="panel panel-primary">
        <div class="panel-footer">
            <form action="/player_management/blockPlayer/<?=$playerId?>" method="post">
                <center>
                    <div class="form-group">
                        <p>
                            <?= lang('system.word98') ?> <strong><?= strtoupper($player['username']) ?></strong>
                        </p>
                        <?php if(!empty($tags)) : ?>
                            <p>
                                <?= lang('system.word99') ?> :
                                <select name="optBlockingReason" id="optBlockingReason" data-flg="x" required>
                                    <option value=""><?=lang('Select block reason')?></option>
                                    <?php foreach ($tags as $key => $value) : ?>
                                        <option value="<?=$value['tagId']?>" data-description="<?=$value['tagDescription']?>" ><?=$value['tagName']?></option>
                                    <?php endforeach; ?>
                                </select>
                            </p>
                            <h4><strong><span class="glyphicon glyphicon-info-sign"></span> <span id="spnTagDescription"></span></strong></h4>
                        <?php endif; ?>
                    </div>
                    <br>

                    <button class="btn btn-danger" type="submit">
                        <span class="glyphicon glyphicon-lock"></span> <?=lang('Block')?>
                    </button>
                    <div class="clearfix"></div>
                </center>
            </form>
        </div>
    </div>
<?php endif;?>
<script type="text/javascript">

    $('#optBlockingReason').change(function(){
        var description = $(this).find(":selected").data("description");
        $('#spnTagDescription').text(description);
    });

    <?php if($this->utils->isEnabledFeature('add_suspended_status')): ?>
        var playerId =<?=$playerId?>;

        $('#iBlock').click(function(){
            var voptBlockingReason = $('#optBlockingReason').val();
            if(voptBlockingReason == ""){
                alert("<?=lang('Block Reason is required')?>");
                return;
            }
            $.ajax({
                'url' : '/player_management/changePlayserStatus/'+playerId+'/true',
                'data': {optBlockingReason: voptBlockingReason,playerStatus:"Block"},
                'type' : 'POST',
                'cache' : false,
                'dataType' : "json"
            }).done(
                function(data){
                    location.reload();
                }
            );
        });

        $('#iSuspend').click(function(){
            var voptBlockingReason = $('#optBlockingReason').val();
            if(voptBlockingReason == ""){
                alert("<?=lang('Block Reason is required')?>");
                return;
            }
            $.ajax({
                'url' : '/player_management/changePlayserStatus/'+playerId+'/true',
                'data': {optBlockingReason: voptBlockingReason,playerStatus:"Suspended"},
                'type' : 'POST',
                'cache' : false,
                'dataType' : "json"
            }).done(
                function(data){
                    location.reload();
                }
            );
        });
    <?php endif;?>
</script>