<button type="submit" class="btn btn-danger btn-sm">
    <i class="glyphicon glyphicon-trash"></i> Delete selected
</button>

<hr/>

<table class="table table-striped table-hover table-responsive" id="my_table">
    <thead>
        <tr>
            <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
            <th><?= lang('aff.vb26'); ?></th>
            <th><?= lang('aff.vb27'); ?></th>
            <th><?= lang('aff.vb28'); ?></th>
            <th><?= lang('aff.vb29'); ?></th>
            <th><?= lang('aff.vb30'); ?></th>
            <th><?= lang('aff.vb31'); ?></th>
            <th><?= lang('aff.vb32'); ?></th>
        </tr>
    </thead>

    <tbody>
        <?php
                if(!empty($banner)) {
                    foreach($banner as $row) {
        ?>
                        <tr>
                            <td><input type="checkbox" class="checkWhite" id="<?= $row['bannerId']?>" name="banner[]" value="<?= $row['bannerId']?>" onclick="uncheckAll(this.id)"/></td>
                            <td><?= date("Y-m-d", strtotime($row['createdOn'])) ?></td>
                            <td><?= $row['bannerName'] ?></td>
                            <td><?= $row['width'] . " x " . $row['height'] ?></td>
                            <td><?= $row['language'] ?></td>
                            <td><a href="#" onclick="window.open('<?= $row['bannerURL'] ?>','_blank', 'width=<?= $row['width'] ?>,height=<?= $row['height'] ?>,scrollbars=yes,status=yes,resizable=no,screenx=0,screeny=0')"><img src="<?= $row['bannerURL'] ?>" style="width=100px; height: 40px;"/></a></td>
                            <td><?= ($row['status'] == 0) ? 'Active':'Inactive' ?></td>
                            <td>
                                <a href="#editbanner">
                                    <span class="glyphicon glyphicon-edit" data-toggle="tooltip" title="<?= lang('tool.am08'); ?>"  data-placement="top" onclick="AffiliateManagementProcess.getBannerDetails(<?= $row['bannerId'] ?>)">
                                    </span>
                                </a>
                                <a href="<?= BASEURL . 'affiliate_management/deleteBanner/' . $row['bannerId'] ?>">
                                    <span class="glyphicon glyphicon-trash" data-toggle="tooltip" title="<?= lang('tool.am09'); ?>"  data-placement="top">
                                    </span>
                                </a>

                                <?php if($row['status'] == 0) { ?>
                                    <a href="#deactivate" onclick="deactivateBanner('<?= $row['bannerId'] ?>', '<?= $row['bannerName'] ?>')">
                                        <span class="glyphicon glyphicon-remove-circle" data-toggle="tooltip" title="<?= lang('tool.am10'); ?>"  data-placement="top">
                                        </span>
                                    </a>
                                <?php } else { ?>
                                    <a href="#activate" onclick="activateBanner('<?= $row['bannerId'] ?>', '<?= $row['bannerName'] ?>')">
                                        <span class="glyphicon glyphicon-ok-sign" data-toggle="tooltip" title="<?= lang('tool.am11'); ?>"  data-placement="top">
                                        </span>
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
        <?php       }
                } else {
         ?>

            <tr>
                <td colspan="8" style="text-align:center"><span class="help-block"><?= lang('lang.norec'); ?></span></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<br/>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>