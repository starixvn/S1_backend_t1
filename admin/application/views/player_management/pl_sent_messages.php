<div class="panel-heading">
    <h4 class="panel-title"><strong>Sent Messages</strong></h4>
</div>

<div class="panel panel-body" id="sent_messages_panel_body">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Created by</th>
                            <th>Description</th>
                            <th>Message category</th>
                            <th>Client type</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Subject</th>
                            <th>Resend</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td colspan="9" style="text-align:center"><span class="help-block">No Records Found</span></td>
                        </tr>
                    </tbody>

                </table>

                <br/><br/>

                <div class="col-md-12 col-offset-0">
                    <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links(); ?> </ul>
                </div>
            </div>
        </div>
    </div>
</div>