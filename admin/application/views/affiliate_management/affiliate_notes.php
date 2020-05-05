<div>
    <ul class="list-group">
        <?php
        if(!empty($notes)){
            foreach ($notes as $note){ ?>
            <li class="list-group-item" style="box-sizing:border-box; border-style:none; height:150px;">
                <?php if (false): # $note['userId'] == $user_id?>
                    <button type="button" class="close" onclick="return remove_player_note(<?=$note['noteId']?>, <?=$affiliate_id?>)">
                        <span>×</span>
                    </button>
                <?php endif ?>

                <div class="row">
                    <div class="col-md-2">
                        <span class="text-muted pull-right"><?=$note['username']?></span>
                    </div>
                    <div class="col-md-9">
                        <span class="text-muted"><?=$note['createdOn']?></span>
                    </div>
                    <div class="col-md-12" style="box-sizing:border-box; padding-top:8px; heignt:100px; max-height:160px; word-break: break-all;">
                        <?=($note['notes'])?>
                    </div>
                </div>
            </li>
        <?php
            }
        } ?>
    </ul>

    <form action="/affiliate_management/add_affiliate_notes/<?=$affiliate_id?>" method="post" onsubmit="return add_affiliate_notes(this, <?=$affiliate_id?>)">
        <div class="form-group col-md-12">
            <textarea name="notes" class="form-control input-sm" rows="5" placeholder="<?=lang('Add remarks')?>..." style="resize: none; max-height: 180px;" onkeyup="autogrow(this); limitTextArea(this,240,'#add-remarks-error-text');" onkeypress="limitTextArea(this,240,'#add-remarks-error-text');" onpaste="limitTextArea(this,240,'#add-remarks-error-text');" required></textarea>
        </div>
        <div class="row">
            <div class="col-lg-6 text-left remarks-error-text" id="add-remarks-error-text" style="margin: -5px 18px 0 16px;">
                <p style="color:white;"><?=lang('Maximum characters')?></p>
            </div>
            <div class="col-lg-5 text-right">
                <button class="btn btn-primary pull-right btn-sm"  style="width:90px; padding:8px; margin-top:-3px;"><?=lang('Add Remarks')?></button>
            </div>
        </div>
    </form>
</div>

<script>
    function limitTextArea(field,lenMax,idName){
        if (field.value.length > lenMax){
            if(idName == "#edit-remarks-error-text"){
                $('#edit-remarks-error-text p').attr("style","color:red");
            }else{
                $('#add-remarks-error-text p').attr("style","color:red");
            }
            field.value = field.value.substring(0, lenMax);
        }else{
            if(idName == "#edit-remarks-error-text"){
                $('#edit-remarks-error-text p').attr("style","color:white");
            }else{
                $('#add-remarks-error-text p').attr("style","color:white");
            }
        }
    }
</script>