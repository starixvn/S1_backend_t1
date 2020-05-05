
<?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
<form id="_export_excel_queue_form" class="hidden" method="POST" target="_blank">
<input name='json_search' type="hidden">
</form>
<?php }?>

<script type="text/javascript">

<?php
$export_report_permission= isset($export_report_permission) ? $export_report_permission : false;
?>

    function initDataTable(mainTable, searchForm, searchUrl, exportUrl, orderIdx, orderAscDesc, disableSort, textRight){

        return $(mainTable).DataTable( {
            dom: "<'panel-body' <'pull-right'B> <'pull-right progress-container'r>l>t<'panel-body'<'pull-right'p>i>",
        	// "responsive": {
         //        details: {
         //            type: 'column'
         //        }
         //    },
            "order": [ orderIdx, orderAscDesc ],
			buttons:[
				{
                extend: 'colvis',
                className: 'btn-sm',
                postfixButtons: [ 'colvisRestore' ]
            	}
                <?php if ($export_report_permission) {?>
                ,{
                    text: '<?php echo lang("lang.export_excel"); ?>',
                    className:'btn btn-sm btn-primary',
                    action: function ( e, dt, node, config ) {
                        var d = {'extra_search':$(searchForm).serializeArray(), 'draw':1, 'length':-1, 'start':0};

                        <?php if($this->utils->isEnabledFeature('export_excel_on_queue')){?>
                            $("#_export_excel_queue_form").attr('action', exportUrl);
                            $("#_export_excel_queue_form [name=json_search]").val(JSON.stringify(d));
                            $("#_export_excel_queue_form").submit();
                        <?php }else{?>
                            // utils.safelog(d);
                            $.post(exportUrl, d, function(data){
                                // utils.safelog(data);

                                //create iframe and set link
                                if(data && data.success){
                                    $('body').append('<iframe src="'+data.link+'" frameborder="0" scrolling="no" style="border:0px;width:0px;height:0px"></iframe>');
                                }else{
                                    alert('export failed');
                                }
                            }).fail(function(){
                                alert('export failed');
                            });
                        <?php }?>
                    }
                }
                <?php } ?>

    		],
            columnDefs: [
                { sortable: false, targets: disableSort },
                { className: 'text-right', targets: textRight }
            ],
            processing: true,
            serverSide: true,
            searching: false,
            ajax: function (data, callback, settings) {
                data.extra_search = $(searchForm).serializeArray();
                $.post(searchUrl, data, function(data) {
                    callback(data);
                },'json');
            },
			"initComplete": function(settings, json) {
			    // $(".buttons-colvis").addClass('btn-sm');
			}

        } );

    }

</script>
