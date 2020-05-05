<table id="statisticsTable" class="table table-bordered table-hover table-responsive dataTable" style="width:100%">
    <thead>
        <tr>
            <th><?=lang('aff.as03');?></th>
            <th><?=lang('aff.as04');?></th>
            <th><?=lang('a_header.affiliate');?> <?=lang('lang.level');?></th>
            <th><?=lang('aff.as23');?></th>
            <th><?=lang('aff.as24');?></th>
            <th><?=lang('aff.as06');?></th>
            <th><?=lang('aff.as07');?></th>
            <th><?=lang('aff.as09');?></th>
            <th><?=lang('aff.as12');?></th>
            <th><?=lang('aff.as08');?></th>
            <th><?=lang('aff.as10');?></th>
            <th><?=lang('aff.as11');?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($statistics)) { ?>
            <?php foreach ($statistics as $row) { ?>
            <tr>
                <td><a><?=$row['username']?></a></td>
                <td><?=$row['realname']?></td>
                <td>
                    <?php if(count($row['levels']) > 1) { ?>
                        <a data-toggle="tooltip" data-placement="top" data-html="true"
                            title="<?php $counter=0; foreach($row['levels'] as $r){
                                $counter ++;
                                echo 'level'.$counter.' : ';
                                // echo $r['affiliateId'] . '<br>'; // debug
                                echo $r['firstname'].' '.$r['lastname'].'<br>';
                                }; ?>">
                            <?=count($row['levels']);?>
                        </a>
                    <?php } else { echo count($row['levels']); } ?>
                </td>
                <td>
                    <?php if(count($row['subaffiliates'])) { ?>
                        <a data-toggle="tooltip" data-placement="top" data-html="true"
                            title="<?php foreach($row['subaffiliates'] as $r){
                                // echo $r['affiliateId'] . ' : '; // debug
                                echo $r['realname'].'<br>';
                                }; ?>">
                            <?=count($row['subaffiliates']);?>
                        </a>
                    <?php } else { echo count($row['subaffiliates']); } ?>
                </td>
                <td>
                    <?php if(count($row['players'])) { ?>
                        <a data-toggle="tooltip" data-placement="top" data-html="true"
                            title="<?php foreach($row['players'] as $r){
                                // echo $r['playerId'].' : '; // debug
                                echo $r['realname'].'<br>';
                                }; ?>">
                            <?=count($row['players']);?>
                        </a>
                    <?php } else { echo count($row['players']); } ?>
                </td>
                <td><?=$row['bets'];?></td>
                <td><?=$row['win'];?></td>
                <td><?=$row['loss'];?></td>
                <td><?php if($row['bonus'] > 0) echo '<a>'.$row['bonus']; else echo $row['bonus']; ?></td>
                <td><?php if($row['income'] > 0) echo '<a>'.$row['income']; else echo $row['income']; ?></td>
                <td><?=$row['location']?></td>
                <td><?=$row['ip_address']?></td>
            </tr>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>

<script type="text/javascript">
    $(document).ready(function() {
        var dataTable = $('#statisticsTable').DataTable({
            autoWidth: false,
            stateSave: true,
            dom: "<'panel-body'<'pull-right'B><'pull-right'f>l>t<'text-center'r><'panel-body'<'pull-right'p>i>",
            buttons: [
                {
                    extend: 'colvis',
                    postfixButtons: [ 'colvisRestore' ]
                }
            ],
            "order": [[ 0, 'asc' ]]
        });

        // Filters
        $('.select').on('change', function(){
            var filter = $(this).val();
            $('#statisticsTable').DataTable().search(filter).draw();
        });
    } );
</script>