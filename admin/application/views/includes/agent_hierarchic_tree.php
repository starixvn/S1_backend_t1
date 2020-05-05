                <!-- Hierarchical Tree {{{2 -->
                <div class="panel panel-primary">
                    <!-- panel heading {{{3 -->
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a href="#hierarchy" id="hide_agent_hierarchy" class="btn btn-info btn-sm">
                                <i class="glyphicon glyphicon-chevron-up" id="hide_agentbi_up"></i>
                            </a>
                            &nbsp; <?=lang('Agent Hierarchy');?>
                        </h4>
                    </div> <!-- panel heading }}}3 -->

                    <!-- panel body {{{3 -->
                    <div class="panel-body agent_basic_panel_body" id="agent_hierarchy">
                        <!-- agent tree {{{4 -->
                        <div class="row">
                            <div class="col-md-12 agency_hierarchical_tree">
                                <label>
                                    <strong><?php echo lang('Agent Hierarchical Tree'); ?></strong>
                                </label>
                                <fieldset>
                                    <div class="row">
                                        <div id="agent_tree" class="col-xs-12">
                                        </div>
                                    </div>
                                </fieldset>
                            </div><!-- end col-md-12 -->
                        </div> <!-- agent tree }}}4 -->
                        <!--  modal for adding players for the agent {{{4 -->
                        <div class="modal fade in" id="add_players_modal"
                            tabindex="-1" role="dialog" aria-labelledby="label_add_players_modal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title" id="label_add_players_modal"></h4>
                                    </div>
                                    <div class="modal-body"></div>
                                    <div class="modal-footer"></div>
                                </div>
                            </div>
                        </div> <!--  modal for level name setting }}}4 -->
                    </div>
                    <!-- panel body }}}3 -->
                </div>
                <!-- End of Hierarchical Tree }}}2 -->

<script type="text/javascript">

    $(function(){
        $('#agent_tree').jstree({
          'core' : {
            'data' : {
              "url" : "<?php echo site_url('/api/get_agent_hierarchical_tree/' . $agent_id); ?>",
              "dataType" : "json" // needed only if you do not supply JSON headers
            }
          },
          "plugins":[
            "search"
          ]
        });
    });

</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agent_hierarchical_tree.php
