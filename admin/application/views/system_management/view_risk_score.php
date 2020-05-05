<div class="panel panel-primary panel_main">
	<div class="panel-heading" id="">
		<h4 class="panel-title"><i class="fa fa-cogs"></i> &nbsp;<?php echo $title; ?>
		<a href="#main_panel" data-toggle="collapse" class="pull-right"><i class="fa fa-caret-down"></i></a>
		</h4>
	</div>
	<div id="main_panel" class="panel-collapse collapse in ">
		<div class="panel-body">
            <ul class="nav nav-tabs">
                <?php foreach($risk_score as $key => $value): ?>
                <li><a data-toggle="tab" href="#risk_score_type_<?=$value['category_name']?>"><?=lang($value['category_description'])?></a></li>
                <?php endforeach; ?>
            </ul>
            <!--<form action="<?=site_url('system_management/update_risk_score_setting')?>" method="post">-->
	            <div class="tab-content">
	                <?php foreach($risk_score as $key => $value): ?>
	                	<div id="risk_score_type_<?=$value['category_name']?>" class="tab-pane fade in">
	                		<pre class="form-control" id="<?=$value['category_name']?>" name="<?=$value['category_name']?>" style="height: 600px"><?php echo "<pre>";print_r($value['rules']) ?></pre>
	                	</div>
	                <?php endforeach; ?>                
	            </div>
	            <div class="panel-footer">
					<input type="submit" class="btn btn_save <?=$this->utils->getConfig('use_new_sbe_color') ? 'btn-scooter' : 'btn-primary'?>" onclick="updateRow();" value="<?php echo lang('Save'); ?>">
				</div>
			<!--</form>-->
		</div>
		
	</div>

</div>

<script type="text/javascript">
	$(document).ready(function () {
        $('#risk_score_setting').addClass('active');

		// Init syntax highlight for JSON string in extra_info
        hljs.initHighlightingOnLoad();
	    $('.nav-tabs a:first').tab('show');
	});

 	// Init ACE editor for JSON
    if($("#R1").length != 0){
        var riskScoreR1 = ace.edit("R1");
        riskScoreR1.setTheme("ace/theme/tomorrow");
        riskScoreR1.session.setMode("ace/mode/json");
        if($("#R1").length != 1){
            riskScoreR1.setValue(JSON.stringify(JSON.parse(riskScoreR1.getValue()), null, 4));
        }
    }
   	

    if($("#R2").length != 0){
        var riskScoreR2 = ace.edit("R2");
        riskScoreR2.setTheme("ace/theme/tomorrow");
        riskScoreR2.session.setMode("ace/mode/json");
        if($("#R2").length != 1){
            riskScoreR2.setValue(JSON.stringify(JSON.parse(riskScoreR2.getValue()), null, 4));
        }
    }
        

    if($("#R3").length != 0){
        var riskScoreR3 = ace.edit("R3");
        riskScoreR3.setTheme("ace/theme/tomorrow");
        riskScoreR3.session.setMode("ace/mode/json");
        if($("#R3").length != 1){
            riskScoreR3.setValue(JSON.stringify(JSON.parse(riskScoreR3.getValue()), null, 4));
        } 
    }
    

    if($("#R4").length != 0){
        var riskScoreR4 = ace.edit("R4");
        riskScoreR4.setTheme("ace/theme/tomorrow");
        riskScoreR4.session.setMode("ace/mode/json");
        if($("#R4").length != 1){
            riskScoreR4.setValue(JSON.stringify(JSON.parse(riskScoreR4.getValue()), null, 4));
        }
    }
        

    if($("#R5").length != 0){
        var riskScoreR5 = ace.edit("R5");
        riskScoreR5.setTheme("ace/theme/tomorrow");
        riskScoreR5.session.setMode("ace/mode/json");
        if($("#R5").length != 1){
            riskScoreR5.setValue(JSON.stringify(JSON.parse(riskScoreR5.getValue()), null, 4));
        }
    }
    

    if($("#R6").length != 0){
        var riskScoreR6 = ace.edit("R6");
        riskScoreR6.setTheme("ace/theme/tomorrow");
        riskScoreR6.session.setMode("ace/mode/json");
        if($("#R6").length != 1){
            riskScoreR6.setValue(JSON.stringify(JSON.parse(riskScoreR6.getValue()), null, 4));
        }
    }
    

    if($("#RC").length != 0){
        var riskScoreRC = ace.edit("RC");
        riskScoreRC.setTheme("ace/theme/tomorrow");
        riskScoreRC.session.setMode("ace/mode/json");
        if($("#RC").length != 1){
        	riskScoreRC.setValue(JSON.stringify(JSON.parse(riskScoreRC.getValue()), null, 4));
    	}
    }

    if($("#R7").length != 0){
        var riskScoreR7 = ace.edit("R7");
        riskScoreR7.setTheme("ace/theme/tomorrow");
        riskScoreR7.session.setMode("ace/mode/json");
        if($("#R7").length != 1){
            riskScoreR7.setValue(JSON.stringify(JSON.parse(riskScoreR7.getValue()), null, 4));
        }
    }

    if($("#R8").length != 0){
        var riskScoreR8 = ace.edit("R8");
        riskScoreR8.setTheme("ace/theme/tomorrow");
        riskScoreR8.session.setMode("ace/mode/json");
        if($("#R8").length != 1){
            riskScoreR8.setValue(JSON.stringify(JSON.parse(riskScoreR8.getValue()), null, 4));
        }
    }

	function updateRow(){

        if($("#R1").length != 0){
            var R1 = riskScoreR1.getValue();
        } else {
            var R1 = [];
        }

        if($("#R2").length != 0){
            var R2 = riskScoreR2.getValue();
        } else {
            var R2 = [];
        }
		
        if($("#R3").length != 0){
            var R3 = riskScoreR3.getValue();
        } else {
            var R3 = [];
        }
		
        if($("#R4").length != 0){
            var R4 = riskScoreR4.getValue();
        } else {
            var R4 = [];
        }

        if($("#R5").length != 0){
            var R5 = riskScoreR5.getValue();
        } else {
            var R5 = [];
        }

        if($("#R6").length != 0){
            var R6 = riskScoreR6.getValue();
        } else {
            var R6 = [];
        }
		
        if($("#RC").length != 0){
            var RC = riskScoreRC.getValue();
        } else {
            var RC = [];
        }

        if($("#R7").length != 0){
            var R7 = riskScoreR7.getValue();
        } else {
            var R7 = [];
        }

        if($("#R8").length != 0){
            var R8 = riskScoreR8.getValue();
        } else {
            var R8 = [];
        }

		if ( ! isJsonString(R1) || ! isJsonString(R2) || ! isJsonString(R3) || ! isJsonString(R4) || ! isJsonString(R5) || ! isJsonString(R6) || ! isJsonString(RC) || ! isJsonString(R7) || ! isJsonString(R8) ) {
            alert('Invalid JSON');
            return false;
        }

		$.post('/system_management/update_risk_score_setting' ,{ 'R1' : R1 , 'R2' : R2 , 'R3' : R3 , 'R4' : R4 , 'R5' : R5 , 'R5' : R5 , 'R6' : R6 , 'RC' : RC , 'R7' : R7, 'R8' : R8} ,function(data){
			location.reload();
        });
	}

	function isJsonString(str) {
        if (str == '') return true;
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }
</script>