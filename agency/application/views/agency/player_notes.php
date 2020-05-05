<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-sticky-notes-o"></i> <?=lang('Player Notes');?>
        </h4>
        <div class="clearfix"></div>
    </div>
    <ul class="list-group">
        <?php
        if(!empty($notes)){
            foreach ($notes as $note){ ?>
            <li class="list-group-item">
                <?php //if (false): # $note['userId'] == $user_id?>
                    <button type="button" class="close" onclick="return remove_player_note(<?=$note['noteId']?>, <?=$player_id?>)">
                        <span>×</span>
                    </button>
                <?php //endif ?>
                <p><?=nl2br($note['notes'])?></p>
                <br>
                <span class="text-muted"><?=$note['createdOn']?></span>
            </li>
        <?php
            }
        }
        ?>
    </ul>
    <div class="panel-footer">
        <form action="/agency/add_player_notes/<?=$player_id?>" method="post" onsubmit="return add_player_notes(this, <?=$player_id?>)">
            <div class="form-group">
                <textarea name="notes" class="form-control input-sm" rows="5" placeholder="<?=lang('Add Notes')?>..."></textarea>
            </div>
            <button class="btn btn-primary pull-right"><?=lang('Add Notes')?></button>
            <div class="clearfix"></div>
        </form>
    </div>
</div>