<div data-file-info="ajax_ui_personal.php" data-datatable-selector="#personal-table">
    <div class="clearfix">
        <table id="personal-table" class="table table-bordered">
            <thead>
                <tr>
                    <th><?=lang('player.uper01');?></th>
                    <th><?=lang('player.uper02');?></th>
                    <th><?=lang('cms.updatedby');?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($personal_history)): ?>
                    <?php foreach ($personal_history as $value): ?>
                        <tr>
                            <td><?=$value['createdOn']?></td>
                            <td><?=$value['changes']?></td>
                            <td><?=$value['operator']?></td>
                        </tr>
                    <?php endforeach?>
                <?php endif?>
            </tbody>
        </table>
    </div>
</div>


<script type="text/javascript">
    function personalHistory() {
        $('#personal-table').DataTable({
            dom: "<'row'<'col-md-12'<'pull-right'f><'pull-right progress-container'>l<'dt-information-summary2 text-info pull-left' i>>><'table-responsive't><'row'<'col-md-12'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>>",
            autoWidth: false,
            order: [ 0, 'desc' ]
        });

        ATTACH_DATATABLE_BAR_LOADER.init('personal-table');
    }
</script>