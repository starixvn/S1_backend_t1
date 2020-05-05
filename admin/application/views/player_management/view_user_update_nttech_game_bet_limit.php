<script src="https://cdn.jsdelivr.net/npm/vue"></script>
<div class="row" id="bet-container">
    <div class="" id="toggleView" style="width: 50%;margin: 0 auto;">
        <div class="panel panel-primary" id="app-platform">
            <div class="panel-heading custom-ph">
                <h3 class="panel-title custom-pt">
                    <i class="icon-list"></i>
                    <?= $system_code ?> <?= lang('Bet Setting') ?>
                </h3>
            </div>
            <ul class="list-group" style="width:100%;">
                <li class="list-group-item"><?php echo lang('Username'); ?> : <b><?= $username ?></b> </li>
            </ul>
            <div class="panel-body">
                <div class="form-group">
                    <select v-model="selected" class="" name="bet_platform" id="bet_platform">
                        <option v-for="option in options" v-bind:value="option.value">
                            {{ option.text }}
                        </option>
                    </select>

                </div>
                <div class="form-group">
                    <component v-bind:is="selected">
                    </component>
                </div>
            </div>
            <div class="panel-footer">
                <button type="submit" class="btn btn-primary update_bet_setting" v-bind:disabled="isDisabled" v-model="selected" >Submit</button>
            </div>
        </div>
    </div>
</div>


<script type="text/x-template" id="by-id">
    <h4>Limit Setting(Min - Max)<h4>
    <div v-for="bet in betidList">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" v-bind:value="bet.value" name="limit_setting" v-model="bet.selected">
            <label class="form-check-label">
            {{bet.text}}
            </label>
        </div>
    </div>

    ---------------------------------
    <h4>Selected Settings</h4>
    <div>
        <ul>
            <li v-for="bet in selectedBets">
                <span>Player/Banker: {{ bet.text }}<span><br>
                <span>BetLimitIDs: {{ bet.value }}<span><br>
            </li>
        </ul>
    </div>
    
</script>

<script type="text/x-template" id="by-value">
    <h4>By Value Component<h4>
    <div class="row" id = by_component_value>
        <div class="col-md-4">
            <div class="form-group">
                <label for="maxbet">Max Bet</label>
                <input type="text" class="form-control" id="maxbet" name="maxbet" placeholder="Max Bet" maxlength="12">
            </div>
            <div class="form-group">
                <label for="minbet">Min Bet</label>
                <input type="text" class="form-control" id="minbet" name="minbet" placeholder="Min Bet" maxlength="12">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="mindraw">Min Draw</label>
                <input type="text" class="form-control" id="mindraw" name="mindraw" placeholder="Min Draw" maxlength="12">
            </div>
            <div class="form-group">
                <label for="matchlimit">Match Limit</label>
                <input type="text" class="form-control" id="matchlimit" name="matchlimit" placeholder="Match Limit" maxlength="12">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="maxdraw">Max Draw</label>
                <input type="text" class="form-control" id="maxdraw" name="maxdraw" placeholder="Max Draw" maxlength="12">
            </div>
        </div>
    </div>
</script>

<script type="text/x-template" id="bet-item">
    <h1>bet item</h1>
</script>

<script>
    Vue.component('by-id', {
    template: '#by-id',
        data () {
            return {
                betidList: [
                    { id: 0, text: '10-2,000', value: 260901, selected: false },
                    { id: 1, text: '20-5,000', value: 260902, selected: false },
                    { id: 2, text: '50-10,000', value: 260903, selected: false },
                    { id: 3, text: '100-20,000', value: 260904, selected: false },
                    { id: 3, text: '200-50,000', value: 260905, selected: false },
                    { id: 3, text: '500-100,000', value: 260906, selected: false },
                    { id: 3, text: '50-3,000', value: 260907, selected: false }
                ]
            }
        },
        computed: {
            selectedBets: function () {
                return this.betidList.filter(el => el.selected === true)
            }
        }
    })

    Vue.component('bet-item', {
        template: '#bet-item'
    })

    Vue.component('by-value', {
        template: '#by-value'
    })

    new Vue({
        el: '#app-platform',    
        data: {
            selected: '',
            options: [
                { id: 0, text: 'Choose Platform Here', value: '' },
                { id: 1, text: 'SEXYBCRT', value: 'by-id' },
                { id: 2, text: 'VENUS', value: 'by-id' },
                { id: 3, text: 'SV388', value: 'by-value' }
            ]
        },
        computed: {
            isDisabled: function(){
                return !this.selected;
            }
        }
    })

    $(document).on("click",".update_bet_setting",function(){
        var setting = [];
        var amount = [];
        $.each($("input[name='limit_setting']:checked"), function(){
            setting.push(parseInt($(this).val()));
        });

        $.each($("#by_component_value :input"), function(){
            amount.push($(this).val());
        });
        var update_by = $.trim($("select[name='bet_platform']").val());
        var selected = $.trim($("#bet_platform option:selected").text());
        var player_id = "<?php echo $player_id; ?>";
        var game_platform_id = "<?php echo $game_platform_id; ?>";
        var username = "<?php echo $username; ?>";
        var url = "/async/update_player_nttech_bet_setting";
        var r_data = JSON.stringify({
            update_by:update_by,
            selected:selected,
            amount:amount,
            setting:setting,
            username:username,
            game_platform_id:game_platform_id
        })
        $.ajax({
            type: "POST",
            url: url,
            dataType: 'json',
            data: {
                data:r_data,
            },
            success: function(data){
                if(data.success == true){
                    alert("<?php echo lang('Update success!'); ?>");
                    location.reload();
                } else {
                    alert("<?php echo lang('Try again!'); ?>");
                }
            }
        });
    });

    $(document).on("keypress","#maxbet,#minbet,#mindraw,#matchlimit,#maxdraw",function(e){
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            //display error message
            if (!$('#number_alert').length){
                var message = '<?php echo lang('Input number Only'); ?>';
                alert(message);    
            }
            return false;
        }
    });

</script>