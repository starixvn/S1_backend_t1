<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title pull-left">
            <i class="icon-stats-bars2"></i> 
            <?=lang('Set Allowed Level Names');?> 
        </h4>
        <!--
        <a href="#close" class="btn btn-default btn-sm pull-right" id="chat_history" onclick="closeDetails()">
            <span class="glyphicon glyphicon-remove"></span>
        </a> -->
        <div class="clearfix"></div>
    </div>
    <div class="panel panel-body" id="details_panel_body">
        <div class="col-md-12">
            <table class="table table-striped table-hover table-responsive" id="myTable">
                <thead>
                    <tr>
                        <th class="col-md-2"><?=lang('Level');?></th>
                        <th class="col-md-6"><?=lang('Level Name');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i = 0; $i < $level_count; $i++) {?>
                    <tr>
                        <td class="col-md-2">
                            <b><?=$i+1?></b>
                        </td>
                        <td class="col-md-6">
                            <?php $level_name = 'level_name_'.$i; ?>
                            <input type="text" id="<?=$level_name?>" 
                            name="allowed_level_names[<?=$i?>]" class="form-control input-sm" 
                            placeholder=' <?=lang('Enter Level Name');?>'
                            value=""/>

                            <span class="errors"></span>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td></td>
                        <td class="col-md-1">
                            <input type="button" class="btn btn-sm btn-info" 
                            onclick="show_level_names()"
                            value="<?=lang('lang.save');?>" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script language="javascript">
$(":text").keyup(function(){
    var re=/^[a-zA-Z0-9]*$/;//只允许输入数字和大小写字母
    //var re=/^\w{12}$/;//只允许输入数字和大小写字母
    if(!re.test(this.value)){
        //alert("请勿输入非法字符");
        this.value=this.value.substr(0,this.value.length-1);//将最后输入的字符去除
        var message = 'Illegal input';
        this.next().css({"color":"red"}).html(message);
    } else {
        this.next().html('');
    }
}
</script>
