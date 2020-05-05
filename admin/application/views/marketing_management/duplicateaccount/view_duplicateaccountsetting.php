 <div class="panel panel-primary">
    <div class="panel-heading custom-ph">
        <h4 class="panel-title custom-pt">
            <i class="icon-bullhorn"></i> <?= lang('cms.duplicatePlayerAccountSetting'); ?>
            
        </h4>
    </div>
    <div class="panel-body" id="details_panel_body">
        <div class="row">
            <div class="col-md-12" style="text-align:left;">
                <form>
                <table class="table tablepress table-condensed" style="width:50%">
                    <th><input type="checkbox" id="checkWhite" onclick="checkAll(this.id)" checked /></th>
                    <th>Function</th>
                    <th>Rate</th>
                    <tr>
                        <td>
                            <input type="checkbox" onclick="checkProperty(this.id)" name="functionOptionCbx[]" value="0" id="firstName" class="checkWhite firstName" checked>
                        </td>
                        <td >
                            First Name
                        </td>
                        <td>
                            <input type="number" name="firstName" id="firstName_txt" class="form-control input-sm numbers_only rate" min="1" max="5" value="5">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" onclick="checkProperty(this.id)" name="functionOptionCbx[]" value="1" id="lastName" class="checkWhite lastName" checked>
                        </td>
                        <td> 
                            Last Name
                        </td>
                        <td>
                            <input type="number" name="lastName" id="lastName_txt" class="form-control input-sm numbers_only rate" min="1" max="5" value="5">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" onclick="checkProperty(this.id)" name="functionOptionCbx[]" value="2" id="email" class="checkWhite email" checked>
                        </td>
                        <td>
                            Email
                        </td>
                        <td>
                            <input type="number" name="email" id="email_txt" class="form-control input-sm numbers_only rate" min="1" max="5" value="5">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" onclick="checkProperty(this.id)" name="functionOptionCbx[]" value="3" id="password" class="checkWhite password" checked>
                        </td>
                        <td>
                            Password
                        </td>
                        <td>
                            <input type="number" name="password" id="password_txt" class="form-control input-sm numbers_only rate" min="1" max="5" value="5">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" onclick="checkProperty(this.id)" name="functionOptionCbx[]" value="4" id="secretQuestion" class="checkWhite secretQuestion" checked>
                        </td>
                        <td>
                            Secret question
                        </td>
                        <td>
                            <input type="number" name="secretQuestion" id="secretQuestion_txt" class="form-control input-sm numbers_only rate" min="1" max="5" value="5">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" onclick="checkProperty(this.id)" name="functionOptionCbx[]" value="5" id="secretAnswer" class="checkWhite secretAnswer" checked>
                        </td>
                        <td>
                            Secret answer
                        </td>
                        <td>
                            <input type="number" name="secretAnswer" id="secretAnswer_txt" class="form-control input-sm numbers_only rate" min="1" max="5" value="5">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" onclick="checkProperty(this.id)" name="functionOptionCbx[]" value="6" id="lastLoginIp" class="checkWhite lastLoginIp" checked>
                        </td>
                        <td>
                            Last Login IP
                        </td>
                        <td>
                            <input type="number" name="lastLoginIp" id="lastLoginIp_txt" class="form-control input-sm numbers_only rate" min="1" max="5" value="5">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" onclick="checkProperty(this.id)" name="functionOptionCbx[]" value="7" id="lastLoginTime" class="checkWhite lastLoginTime" checked>
                        </td>
                        <td>
                            Last Login Time
                        </td>
                        <td>
                            <input type="number" name="lastLoginTime" id="lastLoginTime_txt" class="form-control input-sm numbers_only rate" min="1" max="5" value="5">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" onclick="checkProperty(this.id)" name="functionOptionCbx[]" value="8" id="lastLogoutTime" class="checkWhite lastLogoutTime" checked>
                        </td>
                        <td>
                            Last Logout Time
                        </td>
                        <td>
                            <input type="number" name="lastLogoutTime" id="lastLogoutTime_txt" class="form-control input-sm numbers_only rate" min="1" max="5" value="5">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" onclick="checkProperty(this.id)" name="functionOptionCbx[]" value="9" id="dateRegister" class="checkWhite dateRegister" checked>
                        </td>
                        <td>
                            Date Register
                        </td>
                        <td>
                            <input type="number" name="dateRegister" id="dateRegister_txt" class="form-control input-sm numbers_only rate" min="1" max="5" value="5">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" onclick="checkProperty(this.id)" name="functionOptionCbx[]" value="10" id="playerBankAcctName" class="checkWhite playerBankAcctName" checked>
                        </td>
                        <td>
                            Player Bank Account Name
                        </td>
                        <td>
                            <input type="number" name="playerBankAcctName" id="playerBankAcctName_txt" class="form-control input-sm numbers_only rate" min="1" max="5" value="5">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" onclick="checkProperty(this.id)" name="functionOptionCbx[]" value="11" id="playerBankAcctNo" class="checkWhite playerBankAcctNo" checked>
                        </td>
                        <td>
                            Player Bank Account Number
                        </td>
                        <td>
                            <input type="number" name="playerBankAcctNo" id="playerBankAcctNo_txt" class="form-control input-sm numbers_only rate" min="1" max="5" value="5">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" onclick="checkProperty(this.id)" name="functionOptionCbx[]" value="12" id="playerBankName" class="checkWhite playerBankName" checked>
                        </td>
                        <td>
                            Player Bank Name
                        </td>
                        <td>
                            <input type="number" name="playerBankName" id="playerBankName_txt" class="form-control input-sm numbers_only rate" min="1" max="5" value="5">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" onclick="checkProperty(this.id)" name="functionOptionCbx[]" value="13" id="playerBankBranch" class="checkWhite playerBankBranch" checked>
                        </td>
                        <td>
                            Player Bank Branch
                        </td>
                        <td>
                            <input type="number" name="playerBankBranch" id="playerBankBranch_txt" class="form-control input-sm numbers_only rate" min="1" max="5" value="5">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" onclick="checkProperty(this.id)" name="functionOptionCbx[]" value="14" id="playerBankAddress" class="checkWhite playerBankAddress" checked>
                        </td>
                        <td>
                            Player Bank Address
                        </td>
                        <td>
                            <input type="number" name="playerBankAddress" id="playerBankAddress_txt" class="form-control input-sm numbers_only rate" min="1" max="5" value="5">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align:center">
                            <br/>
                            <button class="btn btn-primary">Save Setting</button>
                        </td>
                    </tr>
                </table>  
                </form>  
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var flag1 = true; var flag2 = true; var flag3 = true; var flag4 = true;
    var flag5 = true; var flag6 = true; var flag7 = true; var flag8 = true;
    var flag9 = true; var flag10 = true; var flag11 = true; var flag12 = true;
    var flag13 = true; var flag14 = true; var flag15 = true; 
    //for multi select
    function checkAll(id) {
        var list = document.getElementsByClassName(id);
        var all = document.getElementById(id);

        if (all.checked) {
            $(".rate").attr("disabled",false);
            $(".rate").val("5");
            flag1 = true;flag2 = true;flag3 = true;flag4 = true;flag5 = true;
            flag6 = true;flag7 = true;flag8 = true;flag9 = true;flag10 = true;
            flag11 = true;flag12 = true;flag13 = true;flag14 = true;flag15 = true;
            for (i = 0; i < list.length; i++) {
                list[i].checked = 1;
            }
        } else {
            all.checked;
            $(".rate").attr("disabled",true);
            $(".rate").val("");
            
            flag1 = false;flag2 = false;flag3 = false;flag4 = false;flag5 = false;
            flag6 = false;flag7 = false;flag8 = false;flag9 = false;flag10 = false;
            flag11 = false;flag12 = false;flag13 = false;flag14 = false;flag15 = false;
            for (i = 0; i < list.length; i++) {
                list[i].checked = 0;
            }
        }
    }
    
    function checkProperty(property){
        var item = document.getElementById(property);
        //property+"_txt".disabled = true;
        //console.log("item"+item);
        //console.log("item"+$("#"+property+"_txt").attr("disabled",true));
        switch(property){
            case 'firstName':
                if(flag1){
                    flag1 = false;
                    $("#"+property+"_txt").attr("disabled",true);
                    $("#"+property+"_txt").val("");
                 }else{
                    flag1 = true;
                    $("#"+property+"_txt").attr("disabled",false);
                    $("#"+property+"_txt").val("5");
                 }
            break;
            case 'lastName':
                if(flag2){
                    flag2 = false;
                    $("#"+property+"_txt").attr("disabled",true);
                    $("#"+property+"_txt").val("");
                 }else{
                    flag2 = true;
                    $("#"+property+"_txt").attr("disabled",false);
                    $("#"+property+"_txt").val("5");
                 }
            break;
            case 'email':
                if(flag3){
                    flag3 = false;
                    $("#"+property+"_txt").attr("disabled",true);
                    $("#"+property+"_txt").val("");
                 }else{
                    flag3 = true;
                    $("#"+property+"_txt").attr("disabled",false);
                    $("#"+property+"_txt").val("5");
                 }
            break;
            case 'password':
                if(flag4){
                    flag4 = false;
                    $("#"+property+"_txt").attr("disabled",true);
                    $("#"+property+"_txt").val("");
                 }else{
                    flag4 = true;
                    $("#"+property+"_txt").attr("disabled",false);
                    $("#"+property+"_txt").val("5");
                 }
            break;
            case 'secretQuestion':
                if(flag5){
                    flag5 = false;
                    $("#"+property+"_txt").attr("disabled",true);
                    $("#"+property+"_txt").val("");
                 }else{
                    flag5 = true;
                    $("#"+property+"_txt").attr("disabled",false);
                    $("#"+property+"_txt").val("5");
                 }
            break;
            case 'secretAnswer':
                if(flag6){
                    flag6 = false;
                    $("#"+property+"_txt").attr("disabled",true);
                    $("#"+property+"_txt").val("");
                 }else{
                    flag6 = true;
                    $("#"+property+"_txt").attr("disabled",false);
                    $("#"+property+"_txt").val("5");
                 }
            break;
            case 'lastLoginIp':
                if(flag7){
                    flag7 = false;
                    $("#"+property+"_txt").attr("disabled",true);
                    $("#"+property+"_txt").val("");
                 }else{
                    flag7 = true;
                    $("#"+property+"_txt").attr("disabled",false);
                    $("#"+property+"_txt").val("5");
                 }
            break;
            case 'lastLoginTime':
                if(flag8){
                    flag8 = false;
                    $("#"+property+"_txt").attr("disabled",true);
                    $("#"+property+"_txt").val("");
                 }else{
                    flag8 = true;
                    $("#"+property+"_txt").attr("disabled",false);
                    $("#"+property+"_txt").val("5");
                 }
            break;
            case 'lastLogoutTime':
                if(flag9){
                    flag9 = false;
                    $("#"+property+"_txt").attr("disabled",true);
                    $("#"+property+"_txt").val("");
                 }else{
                    flag9 = true;
                    $("#"+property+"_txt").attr("disabled",false);
                    $("#"+property+"_txt").val("5");
                 }
            break;
            case 'dateRegister':
                if(flag10){
                    flag10 = false;
                    $("#"+property+"_txt").attr("disabled",true);
                    $("#"+property+"_txt").val("");
                 }else{
                    flag10 = true;
                    $("#"+property+"_txt").attr("disabled",false);
                    $("#"+property+"_txt").val("5");
                 }
            break;
            case 'playerBankAcctName':
                if(flag11){
                    flag11 = false;
                    $("#"+property+"_txt").attr("disabled",true);
                    $("#"+property+"_txt").val("");
                 }else{
                    flag11 = true;
                    $("#"+property+"_txt").attr("disabled",false);
                    $("#"+property+"_txt").val("5");
                 }
            break;
            case 'playerBankAcctNo':
                if(flag12){
                    flag12 = false;
                    $("#"+property+"_txt").attr("disabled",true);
                    $("#"+property+"_txt").val("");
                 }else{
                    flag12 = true;
                    $("#"+property+"_txt").attr("disabled",false);
                    $("#"+property+"_txt").val("5");
                 }
            break;
            case 'playerBankName':
                if(flag13){
                    flag13 = false;
                    $("#"+property+"_txt").attr("disabled",true);
                    $("#"+property+"_txt").val("");
                 }else{
                    flag13 = true;
                    $("#"+property+"_txt").attr("disabled",false);
                    $("#"+property+"_txt").val("5");
                 }
            break;
            case 'playerBankBranch':
                if(flag14){
                    flag14 = false;
                    $("#"+property+"_txt").attr("disabled",true);
                    $("#"+property+"_txt").val("");
                 }else{
                    flag14 = true;
                    $("#"+property+"_txt").attr("disabled",false);
                    $("#"+property+"_txt").val("5");
                 }
            break;
            case 'playerBankAddress':
                if(flag15){
                    flag15 = false;
                    $("#"+property+"_txt").attr("disabled",true);
                    $("#"+property+"_txt").val("");
                 }else{
                    flag15 = true;
                    $("#"+property+"_txt").attr("disabled",false);
                    $("#"+property+"_txt").val("5");
                 }
            break;
        }
        
        // }else{
        //     $("#"+property+"_txt").attr("disabled",false);
        //     $("#"+property+"_txt").val("5");   
        // }
        // switch(property){
        //     case '0':
        //         if($(".firstName").attr("disabled",true)){
        //             $(".firstName").attr("disabled",false);
        //             $("#firstName").val("5");    
        //         }else{
        //             $(".firstName").attr("disabled",true);
        //             $("#firstName").val("");
        //         }
                
        //     break;
        //     case '1':
        //     break;
        //     case '2':
        //     break;
        //     case '3':
        //     break;
        //     case '4':
        //     break;
        //     case '5':
        //     break;
        //     case '6':
        //     break;
        //     case '7':
        //     break;
        //     case '8':
        //     break;
        //     case '9':
        //     break;
        //     case '10':
        //     break;
        //     case '11':
        //     break;
        //     case '12':
        //     break;
        //     case '13':
        //     break;
        //     case '14':
        //     break;
        // }
    }
</script>