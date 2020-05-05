<style type="text/css">
    table.game-history-content {
        text-align: center;
    }
    table.game-history-content th {
        text-align: center;
    }
    table.game-history-content>thead>tr>th {
        border-bottom: 1px solid #000;
    }
    .box-border-bottom {
        border-bottom: 1px #000 solid;
    }
    table.game-history-content tbody tr td.vcenter {
        vertical-align: middle!important;
    }
    table.game-history-content tbody tr td.game-code-list {
        background: #e6f3fa;
        border-bottom: 1px #000 solid;
    }
    .game-b-b {
        border-bottom: 1px #000 solid;
    }
    @media (min-width: 768px) {
        .modal-xl {
            width: 90%;
        }
    }
</style>
<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4 class="panel-title"><?=lang('Batch Update Active Games')?>
                <?php
                    if (empty($Count) != true) {
                        $aGamePlatform = array_unique(array_column($Updated_Games, 'Game_Platform'));
                        echo "<b>[" . $aGamePlatform[0] . "]</b>";
                    }
                ?> 
                </h4>
            </div>
            <div class="panel-body">
                <?php if (!empty($message)): ?>
                    <div class="text-danger">
                        <?=$message?>
                    </div>
                <?php endif ?>

                <h4 class="text-info"><strong><?=lang('Total')?>: <span class="pull-right"><?=$Count;?></span></strong></h4>

                <div href="#total_updated_games" data-toggle="collapse" style=""><span><?=lang('Updated Games')?>: </span> <a href="#total_updated_games" data-toggle="collapse"><?=empty($Count)?0:$Count?></a>
                    <ul id="total_updated_games" class="collpase collapse">
                        <div style="height: <?=($Count > 0 ? $Count * 20 : 20) . 'px'?>; overflow-y: scroll;">
                            <?php if (empty($Count)): ?>
                                <li><?=lang('No updated Game')?></li>
                            <?php else: ?>
                                <?php foreach ($Updated_Games as $Updated_Game) {?>
                                    <?php $aGame = (array)$Updated_Game;?>
                                    <li><?php echo $aGame['English_Name']; ?> [<?php echo $aGame['Game_Code']; ?>]</li>
                                <?php } ?>
                            <?php endif ?>
                        </div>
                    </ul>
                </div>
           </div>
       </div>
   </div>
</div>
<script type="text/javascript" src="<?=site_url().'resources/datatables/dataTables.buttons.min.js'?>"></script>
<script type="text/javascript" src="<?=site_url().'resources/datatables/jszip.min.js'?>"></script>
<script type="text/javascript" src="<?=site_url().'resources/datatables/buttons.html5.min.js'?>"></script>
<script type="text/javascript">
    $("#collapseSubmenuGameDescription").addClass("in");
    $("a#view_game_description").addClass("active");
    $("a#viewGameListSettings").addClass("active");
    $("#myTable").DataTable({
         dom: "<'panel-body' <'pull-right'B><'pull-right'f><'pull-right progress-container'>l>" +
            "<'dt-information-summary1 text-info pull-left' i>t<'text-center'r>" +
            "<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        buttons: [
            {
                extend: 'csvHtml5',  // csvHtml5 , copyHtml5, excelHtml5
                exportOptions: {
                    columns: ':visible'
                },
                className:'btn btn-sm btn-primary',
                text: '<?=lang('CSV Export')?>',
                filename:  '<?=lang('Active Player Report')?>'
            }
        ]
    });
</script>
