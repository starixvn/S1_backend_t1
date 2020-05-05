
<div class="panel panel-primary">

    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="icon-bubbles3"></i>
            <?= lang('cs.messagehistory'); ?>
        </h4>
    </div>

    <div class="panel-body">
        <div class="text-left">
            <div class="form-inline">
                <input type="text" id="reportrange" class="form-control input-sm dateInput inline" data-start="#dateRangeValueStart" data-end="#dateRangeValueEnd" data-time="true"/>
                <input type="hidden" id="dateRangeValueStart" name="dateRangeValueStart"/>
                <input type="hidden" id="dateRangeValueEnd" name="dateRangeValueEnd"/>
                <input type="button" class="btn btn-primary btn-sm" id="btn-submit" value="<?=lang('lang.search');?>"/>
            </div>
        </div>
        <hr/>
        <table class="table table-hover" id="myTable">

            <thead>
                <tr>
                    <th><?= lang('lang.date'); ?></th>
                    <th><?= lang('cs.subject'); ?></th>
                    <th><?= lang('cs.session'); ?></th>
                    <th><?= lang('cs.recipient'); ?></th>
                    <th><?= lang('cs.sender'); ?></th>
                    <th><?= lang('lang.action'); ?></th>
                </tr>
            </thead><!-- 

            <tbody>
                <?php if(!empty($chat_history)) { ?>
                    <?php foreach($chat_history as $chat_history) { ?>
                        <tr>
                            <td><?= $chat_history['date']?></td>
                            <td><?= $chat_history['subject']?></td>
                            <td><?= $chat_history['session']?></td>
                            <td><?= $chat_history['sender']?></td>
                            <td><?= $chat_history['recepient']?></td>
                            <td>
                                <a href="javascript:void(0);" data-toggle="tooltip" title="<?= lang('tool.cs01'); ?>" onclick="modal('/cs_management/viewChatHistoryDetails/<?=$chat_history["messageId"]?>','<?=lang('cs.messagedetail')?>')"><span class="glyphicon glyphicon-zoom-in"></span></a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
 -->
        </table>
    </div>

    <div class="panel-footer"></div>

</div>

<div class="modal fade" id="mainModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="mainModalLabel"></h4>
            </div>
            <div class="modal-body"></div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">

    $(document).ready(function(){

         $('#myTable').DataTable({
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            "dom":"<'panel-body' <'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
            {
              extend: 'colvis',
              postfixButtons: [ 'colvisRestore' ]
          }],

         "order": [ 0, 'desc' ]
    });

    });

    function modal(load, title) {
      var target = $('#mainModal .modal-body');
      $('#mainModalLabel').html(title);
      target.html('<center><img src="' + imgloader + '"></center>').delay(1000).load(load);
      $('#mainModal').modal('show');
    }

</script>
