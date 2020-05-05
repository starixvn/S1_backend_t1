<div class="row">
    <div class="col-md-12">
        <div class="panel panel-og">
            <table class="table table-hover table-striped table-bordered">
                <tbody>
                    <?php //var_dump($deposits);
if (!empty($promo)) {
	?>
                        <?php foreach ($promo as $row) {?>
                            <tr>
                                <td>
                                    <a href="<?=BASEURL . 'iframe_module/viewPromoDetails/' . $row['promoCmsSettingId']?>">
                                        <img class="media-object" src="<?=PROMOIMAGEPATH . $row['promoThumbnail']?>" width='210' height='147'>
                                    </a>
                                </td>
                                <td><?=$row['promoName']?></td>
                                <td><?=$row['promoDescription']?></td>
                                <td>
                                    <a class="btn btn-sm btn-og" href="<?=BASEURL . 'iframe_module/viewPromoDetails/' . $row['promoCmsSettingId']?>"><?=lang('tool.02');?></a>
                                </td>
                            </tr>
                        <?php }
	?>
                    <?php } else {?>
                            <tr>
                                <td colspan="6" style="text-align:center"><span class="help-block"><?=lang('cashier.32');?></span></td>
                            </tr>
                    <?php }
?>
                </tbody>
            </table>

            <br/>

            <div class="col-md-12 col-offset-0">
                <ul class="pagination pagination-sm" style="margin: 0; padding: 0;"> <?php echo $this->pagination->create_links();?> </ul>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
        $('#myTable').DataTable({
            "responsive": {
                details: {
                    type: 'column'
                }
            },
            "columnDefs": [ {
                className: 'control',
                orderable: false,
                targets:   0
            } ],
            "order": [ 1, 'asc' ]
        });
    });
</script>