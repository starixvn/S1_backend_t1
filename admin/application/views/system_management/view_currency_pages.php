<table class="table table-striped table-hover table-condensed" id="my_table">
    <thead>
        <tr>
            <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)"/></th>
            <th>Currency code</th>
            <th>Currency name</th>
            <th>Updated on</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php if(!empty($currency)) { ?>
            <?php foreach($currency as $row) { ?>
                <tr>
                    <td><input type="checkbox" class="checkWhite" id="<?= $row['currencyId']?>" name="currency[]" value="<?= $row['currencyId']?>" onclick="uncheckAll(this.id)"/></td>
                    <td><?= $row['currencyCode'] ?></td>
                    <td><?= $row['currencyName'] ?></td>
                    <td><?= $row['updatedOn'] ?></td>
                    <td>
                        <?php if(!empty($active_currency)) { ?>
                            <?= $row['status'] == 1 ? "Inactive" : "Active" ?>
                        <?php } else { ?>
                            <a href="<?= BASEURL . 'user_management/changeCurrencyStatus/' . $row['currencyId']?>" class="btn btn-warning btn-xs">Set this to Active</a>
                        <?php } ?>
                    </td>
                    <td>
                        <a href="#editCurrency">
                            <span class="glyphicon glyphicon-edit" data-toggle="tooltip" title="<?= lang('system.word89'); ?>"  data-placement="top" onclick="UserManagementProcess.getCurrencyDetails(<?= $row['currencyId'] ?>)">
                            </span>
                        </a>

                        <a href="<?= BASEURL . 'user_management/deleteCurrency/'.$row['currencyId'] ?>">
                            <span class="glyphicon glyphicon-trash" data-toggle="tooltip" title="<?= lang('system.word90'); ?>"  data-placement="top">
                            </span>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
                <tr>
                    <td colspan="6" style="text-align:center"><?= lang('lang.norec'); ?>
                    </td>
                </tr>
        <?php } ?>
    </tbody>
</table>

<br/>

<div class="col-md-12 col-offset-0">
    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
</div>