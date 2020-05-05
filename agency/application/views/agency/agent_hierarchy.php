<?php
/**
 *   filename:   agent_hierarchical_tree.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-06-12
 *   @brief:     view for agent hierarchical tree
 */
?>

<div class="content-container">
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
        </div>
        <!-- panel body }}}3 -->
    </div>
    <!-- End of Hierarchical Tree }}}2 -->
</div>

<script>
// Hierarchical Tree {{{2
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
// Hierarchical Tree }}}2
</script>
<?php
// zR to open all folded lines
// vim:ft=php:fdm=marker
// end of agent_hierarchical_tree.php
