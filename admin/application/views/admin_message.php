
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">

            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fa fa-search"></i> <?=lang("lang.search")?>
                    <span class="pull-right">
                        <a data-toggle="collapse" href="#collapseViewMessages" class="btn btn-default btn-xs "></a>
                    </span>

                </h4>
            </div>

            <div id="collapseViewMessages" class="panel-collapse collapse in">
                <div class="panel-body">
                    <form class="form-horizontal" id="search-form">
                        <div class = "row">
                            <div class="col-md-8">
                                <input type="text" name="id" id="id" class="form-control input-sm" style="display:none" value="<?php echo $this->uri->segment('3');?>" />
                                <label class="control-label" for="search_date"><?=lang('report.sum02');?></label>
                                <input id="search_date" class="form-control input-sm dateInput" data-start="#date_from" data-end="#date_to" data-time="true"/>
                                <input type="hidden" id="date_from" name="date_from" value="" />
                                <input type="hidden" id="date_to" name="date_to"  value=""/>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label" for="sender"><?=lang('cs.sender');?> </label>
                                <input type="text" name="sender" id="sender" class="form-control input-sm" value="" />
                            </div>
                        </div>
                        <div class = "row">
                            <div class="col-md-8">
                                <label class="control-label" for="messages"><?=lang('cs.messages');?> </label>
                                <input type="text" name="messages" id="messages" class="form-control input-sm" value="" />
                            </div>
                        </div>

                    </form>
                </div>
                <div class="panel-footer text-right">
                    <button type="button" class="btn btn-primary btn-sm" id="search-message"><?=lang('lang.search');?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12" id="toggleView">
        <div class="panel panel-primary">
            <div class="panel-heading custom-ph">
                <h4 class="panel-title custom-pt">
                    <i class="icon-bubble2"></i> <?=lang('cs.messages');?>
                </h4>
            </div>

            <div class="panel-body" id="chat_panel_body">

                <div class="table-responsive">
                    <table class="table table-hover" style="width:100%;" id="myTable">
                        <thead>
                            <tr>
                                <th></th>
                                <th><?=lang('cs.sender');?></th>
                                <th><?=lang('cs.messages');?></th>
                                <th><?=lang('lang.date');?></th>
                                <th><?=lang('lang.action');?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="panel-footer"></div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"><?=lang('cs.messagedetail');?></h4>
          </div>
          <div class="modal-body">
            <div class="col-md-12" id="cs_details" style="display:none;"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var dataTable = $('#myTable').DataTable({
            "responsive":
            {
                details: {
                    type: 'column'
                }
            },
            autoWidth: false,
            searching: false,
            "dom":"<'panel-body' <'pull-right'f><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
            buttons: [
                {
                  extend: 'colvis',
                  postfixButtons: [ 'colvisRestore' ]
                }
            ],
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 4, 'desc' ],

            // SERVER-SIDE PROCESSING
            processing: true,
            serverSide: true,
            ajax: function (data, callback, settings) {

                data.extra_search = $('#search-form').serializeArray();

                $.post(base_url + "home/getAdminMessages", data, function(data) {
                    callback(data);
                },'json');

            }

        });


        $('#search-message').click( function() {
        	$('#id').val('');
            dataTable.ajax.reload();
        });
    });
    function viewChatDetails(id){
    	$.post(base_url + "home/view_messages_by_id/"+id, function(data) {
            $('.modal-body').html(data);
        });
    }
</script>