<!----------------EDIT-FORM Game Details start---------------->
    <div class="col-md-5" id="edit_game_description_details" style="display: none;">

        <div class="panel panel-info">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="icon-pencil"></i> <span id="add-edit-panel-title"></span>
                    <a href="#close" class="btn btn-primary btn-sm pull-right panel-button" id="closeDetails" ><span class="glyphicon glyphicon-remove"></span></a>
                </h4>

                <div class="clearfix"></div>
            </div>
            <div class="panel panel-body" id="details_panel_body">

                <form   method="post" role="form" id="game-description-form">
                    <div class="form-group">
                        <input type="hidden" id="gd_id" name="gd_id" >
                        <label for="game"><?=lang('sys.gd5');?></label>
                        <select name="game_platform_id" id="game_platform_id" class="form-control input-sm"></select>
                    </div>
                    <div class="form-group">
                        <label for="game"><?=lang('sys.gd6');?></label>
                        <select name="game_type_id" id="game_type_id" class="form-control input-sm"></select>
                    </div>
                    <div class="form-group " id="game_name_label">
                        <label for="game_name" style="margin-right: 20px"><?=lang('sys.gd8');?></label>
                        <input type="checkbox" id="game_name_use_json" name="game_name_use_json" value="1" /> <label for="game_name_use_json"><?=lang('Use JSON');?></label>
                        <input type="text" value="" class="form-control" id="game_name" name="game_name">
                        <pre class="form-control" id="game_name_editor" name="game_name_editor" style="height: 150px"></pre>
                        <!-- <select name="game_name" id="game_name" class="form-control input-sm"></select> -->
                    </div>

                    <div class="form-group">
                        <label for="game_code"><?=lang('sys.gd9');?></label>
                        <input type="text"  value="" class="form-control"  id="game_code" name="game_code">
                    </div>
                    <div class="form-group">
                        <label for="progressive"><?=lang('lang.external.game.id');?></label>
                        <input type="text" value="" class="form-control" id="external_game_id" name="external_game_id">
                    </div>
                    <div class="form-group">
                        <label for="progressive"><?=lang('lang.english.name');?></label>
                        <input type="text" value="" class="form-control" id="english_name" name="english_name">
                    </div>
                    <div class="form-group">
                        <label for="progressive"><?=lang('sys.gd10');?></label>
                        <input type="text" value="" class="form-control" id="progressive" name="progressive">
                    </div>

                    <!-- <div class="form-group">
                        <label for="dlc_enabled"><?=lang('sys.gd12');?></label>
                        <input type="checkbox" id="dlc_enabled" name="dlc_enabled"/>
                    </div>
                    <div class="form-group">
                        <label for="flash_enabled"><?=lang('sys.gd13');?></label>
                        <input type="checkbox"   id="flash_enabled" name="flash_enabled"/>
                    </div>
                    <div class="form-group">
                        <label for="offline_enabled"><?=lang('sys.gd14');?></label>
                        <input type="checkbox" id="offline_enabled" name="offline_enabled"/>
                    </div>
                    <div class="form-group">
                        <label for="mobile_enabled"><?=lang('sys.gd15');?></label>
                        <input type="checkbox" id="mobile_enabled" name="mobile_enabled" />
                    </div> -->

                    <div class="form-group">
                        <label for="note"><?=lang('sys.gd11');?></label>
                        <textarea class="form-control"id="note"  maxlength="1000" name="note"rows="5"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="status"><?=lang('sys.gd16');?></label>
                        <input type="checkbox"  id="status" name="status"/>
                    </div>
                    <div class="form-group">
                        <label for="flag_show_in_site"><?=lang('sys.gd17');?></label>
                        <input type="checkbox"  id="flag_show_in_site" name="flag_show_in_site"/>
                    </div>
                    <div class="form-group">
                        <label for="no_cash_back"><?=lang('sys.gd18');?></label>
                        <input type="text" value="" maxlength="1" class="form-control" id="no_cash_back" name="no_cash_back">
                    </div>
                    <div class="form-group">
                        <label for="void_bet"><?=lang('sys.gd19');?></label>
                        <input type="text" value="" class="form-control" id="void_bet" name="void_bet">
                    </div>
                    <div class="form-group">
                        <label for="game_order"><?=lang('sys.gd20');?></label>
                        <input type="number" value="" class="form-control" id="game_order" name="game_order">
                    </div>
                    <!----------NOTE: BUTTON TITLE WILL BE UPDATE THROUG JAVASCTRIPT---------------->
                    <button id="add-update-button"  type="submit" class="btn btn-info"></button>

                </form>


            </div>
        </div>


    </div>
    <!---------------EDIT FORM Game Details end---------------->
