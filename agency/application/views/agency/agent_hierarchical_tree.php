<?php
/**
 *   filename:   agent_hierarchical_tree.php
 *   author:     William Zhang
 *   e-mail:     william.dev@tripleonetech.net
 *   date:       2016-06-12
 *   @brief:     view for agent hierarchical tree
 */
?>

<div class="panel panel-primary">

    <div class="panel-heading">
    </div>

    <div class="panel-body" id="player_panel_body">
        <div class="container">
            <!-- Hierarchical Tree {{{2 -->
            <!-- agent tree {{{3 -->
            <div class="row">
                <div class="col-md-12 agency_hierarchical_tree">
                    <div class="row">
                        <div id="agent_tree" class="col-xs-12">
                        </div>
                    </div>
                </div><!-- end col-md-12 -->
            </div> <!-- agent tree }}}3 -->
            <!-- End of Hierarchical Tree }}}2 -->
        </div>
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
