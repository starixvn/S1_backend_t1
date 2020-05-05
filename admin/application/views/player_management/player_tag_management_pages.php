<table class="table table-striped table-hover table-responsive" id="my_table">
    <thead>
        <tr>
            <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
            <th><?= lang('player.tm02'); ?></th>
            <th><?= lang('player.tm04'); ?></th>
            <th><?= lang('cms.createdby'); ?></th>
            <th><?= lang('lang.action'); ?></th>
        </tr>
    </thead>

    <tbody>
        <?php
                if(!empty($tags)) {
                    foreach($tags as $row) {
        ?>
                        <tr>
                            <td><input type="checkbox" class="checkWhite" id="<?= $row['tagId']?>" name="tag[]" value="<?= $row['tagId']?>" onclick="uncheckAll(this.id)"/></td>
                            <td><?= $row['tagName'] ?></td>
                            <td><?= $row['tagDescription'] ? $row['tagDescription'] : lang('player.tm06') ?></td>
                            <td><?= $row['username'] ?></td>
                            <td>
                                <a href="#editTag">
                                    <span class="glyphicon glyphicon-edit" data-toggle="tooltip" title="<?= lang('player.tm07'); ?>"  data-placement="top" onclick="PlayerManagementProcess.getTagDetails(<?= $row['tagId'] ?>)">
                                    </span>
                                </a>
                                <a href="<?= BASEURL . 'Player_management/deleteTag/'.$row['tagId'] ?>">
                                    <span class="glyphicon glyphicon-trash" data-toggle="tooltip" title="<?= lang('player.tm08'); ?>"  data-placement="top">
                                    </span>
                                </a>
                            </td>
                        </tr>
        <?php       }
                } else {
         ?>

            <tr>
                <td colspan="5" style="text-align:center"><span class="help-block"><?= lang('lang.norec'); ?></span></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<br/>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>