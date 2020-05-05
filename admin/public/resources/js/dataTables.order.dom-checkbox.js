$.fn.dataTable.ext.order['dom-checkbox'] = function(settings, col){
    return this.api().column(col, {order: 'index'}).nodes().map(function(td, i){
        return $('input', td).prop('checked') ? '0' : '1';
    });
};