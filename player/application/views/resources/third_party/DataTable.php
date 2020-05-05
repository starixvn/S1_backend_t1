<?php
$datatable_options = [
    'pagingType' => 'ellipses', // require ellipses.js
    'pageLength' => 10
];

if($this->utils->is_mobile()){
    $datatable_options['pagingType'] = "listbox";
    $datatable_options['pageLength'] = 5;
}

$datatable_i18n_lang = '';
switch($this->language_function->getCurrentLanguage()){
    case Language_function::INT_LANG_ENGLISH:
        $datatable_i18n_lang = 'English.lang';
    break;
    case Language_function::INT_LANG_CHINESE:
        $datatable_i18n_lang = 'Chinese.lang';
    break;
    case Language_function::INT_LANG_INDONESIAN:
        $datatable_i18n_lang = 'Indonesian.lang';
    break;
    case Language_function::INT_LANG_VIETNAMESE:
        $datatable_i18n_lang = 'Vietnamese.lang';
    break;
    case Language_function::INT_LANG_KOREAN:
        $datatable_i18n_lang = 'Korean.lang';
    break;
    case Language_function::INT_LANG_THAI:
        $datatable_i18n_lang = 'Thai.lang';
    break;
    default:
        $datatable_i18n_lang = 'Chinese.lang';
    break;
}

?>
<link rel="stylesheet" href="<?=$this->utils->getPlayerCmsUrl('/resources/datatables/1.10.15/media/css/jquery.dataTables.min.css');?>" />
<link rel="stylesheet" href="<?=$this->utils->getPlayerCmsUrl('/resources/datatables/1.10.15/extensions/Responsive/css/responsive.dataTables.min.css');?>" />
<link rel="stylesheet" href="<?=$this->utils->getPlayerCmsUrl('/resources/datatables/1.10.15/extensions/Responsive/css/responsive.bootstrap.min.css');?>" />

<script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl('/resources/datatables/1.10.15/media/js/jquery.dataTables.min.js');?>"></script>
<script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl('/resources/datatables/1.10.15/media/js/dataTables.bootstrap.min.js');?>"></script>
<script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl('/resources/datatables/1.10.15/extensions/Responsive/js/dataTables.responsive.min.js');?>"></script>
<script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl('/resources/datatables/1.10.15/extensions/Responsive/js/responsive.bootstrap.min.js');?>"></script>
<script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl('/resources/datatables/1.10.15/plugins/pagination/ellipses.js');?>"></script>
<script type="text/javascript" src="<?=$this->utils->getPlayerCmsUrl('/resources/datatables/1.10.15/plugins/pagination/select.js');?>"></script>


<script type="text/javascript">
var dataTable_options = {
    dom: "<'dt-row'>" +
            "<'dt-row'<'dt-table-row'tr>>" +
            "<'dt-row'<'dt-info-row'i><'dt-paginate-row'p>>",
    oClasses: {
        "sPageFirst": "paginate_button_first",
        "sPagePrevious": "paginate_button_previous",
        "sPageNext": "paginate_button_next",
        "sPageLast": "paginate_button_last"
    },
    pagingType: "<?=$datatable_options['pagingType']?>",
    pageLength: parseInt("<?=$datatable_options['pageLength']?>"),
    iShowPages: 3, // only for the ellipses paging type
    lengthMenu: [ 5, 10, 25, 50, 100 ],
    searching: false,

    autoWidth: true,
    responsive: {
        details: {
            type: 'column',
            target: 'tr',
            renderer: function(api, rowIdx, columns){
                // $.map(api.rows('.parent')[0], function(rowId){
                //     $(api.row(rowId).node()).trigger('click');
                // });

                var head = '<tr>' +
                    '<th><?=lang('sys.dasItem')?></th>' +
                    '<th><?=lang('sys.description')?></th>' +
                '</tr>';
                var data = $.map(columns, function(col, i) {
                    return col.hidden ?
                        '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                            '<td>' +
                                '<div style="word-wrap:break-word;word-break:break-all;white-space: pre-wrap" >' +
                                    col.title + ':' +
                                '</div>' +
                            '</td> ' +
                            '<td style="width:80%;">' +
                                '<div style="word-wrap:break-word;word-break:break-all;white-space: pre-wrap" >' +
                                    col.data +
                                '</div>' +
                            '</td>' +
                        '</tr>' : '';
                }).join('');

                return data ? $('<table>').attr('width','100%').append(head).append(data) : false;
            }
        }
    },

    processing: true,
    serverSide: true,
    fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        // $(nRow).removeClass('odd');
        // $(nRow).removeClass('even');
    },
    fnDrawCallback: function(oSettings){
        $(this).find('.sorting_hidden').removeClass('sorting_asc').removeClass('sorting_desc');
    },
    footerCallback: function(tfoot, data, start, end, display){
        this.api().responsive.recalc();
    },
    language: {
        url: '<?=$this->utils->getSystemUrl("player")?>/resources/datatables/1.10.15/plugins/i18n/<?=$datatable_i18n_lang?>',
    }
};

$.fn.dataTable.defaults = $.extend({}, $.fn.dataTable.defaults, dataTable_options);
</script>