<div class="modal fade" tabindex="-1" data-backdrop="static" role="dialog" aria-hidden="true" id="bal_info_dialog">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <table class="bal_details table table-striped">
          <thead><th><?php echo lang('Wallet'); ?></th><th><?php echo lang('Before'); ?></th><th><?php echo lang('After'); ?></th></thead>
          <tbody></tbody>
        </table>
        <pre class='hide'>
          <code class='json'>
          </code>
        </pre>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
//for transactions
function addBalDetail(tbody,name, bAmount, aAmount){
    var bVal=0;
    var aVal=0;
    bVal=parseFloat(bAmount);
    if(bVal && !isNaN(bVal)){
      bVal=bVal.toFixed(2);
    }else{
      bVal=bAmount;
    }
    aVal=parseFloat(aAmount);
    if(aVal && !isNaN(aVal)){
      aVal=aVal.toFixed(2);
    }else{
      aVal=aAmount;
    }
    var css='';
    if(bVal!=aVal){
      css='class="danger"';
    }
    tbody.append('<tr '+css+' ><td>'+name+'</td><td>'+bVal+'</td><td>'+aVal+'</td></tr>');

}
function popupBalInfo(transId){
  var input=$('#bal_info_'+transId).text();
  var output=$("#bal_info_dialog .modal-body pre code");
  var tbody=$('table.bal_details tbody');
  tbody.html('');
  if(input.trim()!=''){
    var balDetails=JSON.parse(input);
    var beforeBalDetails=balDetails['before'];
    var afterBalDetails=balDetails['after'];
    //add main wallet
    addBalDetail(tbody,'Main',beforeBalDetails['main_wallet'],afterBalDetails['main_wallet']);
    //add frozen
    addBalDetail(tbody,'Frozen',beforeBalDetails['frozen'],afterBalDetails['frozen']);

    $(beforeBalDetails['sub_wallet']).each(function(index,item){
      addBalDetail(tbody,item['game'],item['totalBalanceAmount'],afterBalDetails['sub_wallet'][index]['totalBalanceAmount']);
    })
    //add total_balance
    addBalDetail(tbody,'Total',beforeBalDetails['total_balance'],afterBalDetails['total_balance']);

    var node = JSON.stringify( balDetails , null, 2);
    output.html(node);
  }
  // $("#bal_info_dialog .modal-body pre code").html($('#bal_info_'+transId).html());
  $('pre code').each(function(i, block) {
    hljs.highlightBlock(block);
  });

  $("#bal_info_dialog").modal('show');
}
</script>