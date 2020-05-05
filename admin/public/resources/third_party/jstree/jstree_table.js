/*
 * Some utils function for jstree and result table interaction
 *
 */

function loadJstreeTable(tree_dom_id, outer_tale_id, summarize_table_id, get_data_url, input_number_form_sel, default_num_value='0', generate_filter_column=new Array(), filter_col_id='', filter_trigger_id='', use_input_number=true) {
    var plugins = ["search", "checkbox"];
    if(use_input_number === true) {
        plugins.push("input_number");
    }

    $(tree_dom_id)
    // listen for event
    .on('loaded.jstree', function (event, data) {
        previewResult(tree_dom_id, outer_tale_id, expand_and_collapse=true, expand_and_collapse_count=2, use_input_number, default_num_value);
        previewResult(tree_dom_id, summarize_table_id, expand_and_collapse=true, expand_and_collapse_count=5, use_input_number, default_num_value);
    })
    .jstree({
        'core' : {
            'data' : {
                "url" : get_data_url,
                "dataType" : "json" // needed only if you do not supply JSON headers
            },
        },
        "input_number":{
            "form_sel": input_number_form_sel
        },
        "checkbox":{
            "tie_selection": false
        },
        "plugins": plugins
    });

    loadFilterColumn(tree_dom_id, get_data_url, generate_filter_column, filter_col_id, filter_trigger_id);

    $('#previewResult').on('click', function(){
        previewResult(tree_dom_id, summarize_table_id, expand_and_collapse=true, expand_and_collapse_count=5, use_input_number, default_num_value);
    });
}

function loadFilterColumn(tree_dom_id, get_data_url='', filter_column=new Array(), filter_col_id, filter_trigger_id) {
    if(!jQuery.isEmptyObject(filter_column)) {
        appendFilterColumnDom(filter_column, filter_col_id, filter_trigger_id);
        bindFilterColumnDomEvent(tree_dom_id, get_data_url, filter_column, filter_col_id, filter_trigger_id, input_number_form_sel);
    }
}

function appendFilterColumnDom(filter_column, filter_col_id, filter_trigger_id) {
    $.each(filter_column, function( key, col_name ) {
        var column_dom =
            '<div class="col-md-3">' +
                '<div class="row">' +
                    '<div class="col-md-6">' +
                        '<strong>' + key + '</strong>' +
                    '</div>' +
                    '<div class="col-md-6">' +
                        '<select class="form-control user-success" name="' + col_name + '">' +
                            '<option value="99" checked>All</option>' +
                            '<option value="1">Yes</option>' +
                            '<option value="0">No</option>' +
                        '</select>' +
                    '</div>' +
                '</div>' +
            '</div>';

        $(filter_col_id).append(column_dom);
        $(filter_trigger_id).show();
    });
}

function bindFilterColumnDomEvent(tree_dom_id, get_data_url, filter_column, filter_col_id, filter_trigger_id, input_number_form_sel) {
    $(filter_trigger_id).click(function() {
        var all_game_data = $(tree_dom_id).jstree(true).get_json('#', {flat:true});
        console.log(all_game_data);

        var filter_query_string = '';
        var filter_arr = {};

        $.each(filter_column, function( key, col_name ) {
            var filter_value = $('select[name=' + col_name).val();
            filter_arr[col_name] = filter_value;
        });

        var filter_query_string = createFilterQueryString(filter_arr);
        // console.log(filter_query_string);

        $(tree_dom_id).jstree("destroy");
        $(tree_dom_id).jstree({
            'core' : {
                'data' : {
                    "url" : get_data_url + filter_query_string,
                    "dataType" : "json" // needed only if you do not supply JSON headers
                }
            },
            "input_number":{
                "form_sel": input_number_form_sel
            },
            "checkbox":{
                "tie_selection": false
            },
            "search": {
                "show_only_matches": true
            },
            "plugins":[
                "search","checkbox","input_number"
            ]
        });
    });
}

function previewResult(tree_dom_id, target_table, expand_and_collapse=true, expand_and_collapse_count=5, input_number=false, default_num_value='0', first_level_prefix="gp", second_level_prefix="gt", third_level_prefix="gd") {
    var selected_flat_data = $(tree_dom_id).jstree('get_checked', true);
    // console.log(selected_flat_data);

    var all_game_flat_data = $(tree_dom_id).jstree(true).get_json('#', {flat:true});
    // console.log(all_game_flat_data);
    // var all_game_data = $(tree_dom_id).jstree(true).get_json('#', {flat:false});
    // console.log(all_game_data);

    var result_selected_tree_data = new Array();

    if(selected_flat_data.length > 0) {
        if(input_number == true) {
            var input_select_number = $(tree_dom_id).jstree('generate_number_fields');
            // console.log(input_select_number);

            selected_flat_data = appendSelectNumberToTreeData(input_select_number, default_num_value, selected_flat_data, first_level_prefix, second_level_prefix, third_level_prefix);
        }

        // console.log(selected_flat_data);

        var result_selected_tree_data = flatDataToTreeData(all_game_flat_data, selected_flat_data, input_number);
        // console.log(result_selected_tree_data);

        appendTreeDataToTable(result_selected_tree_data, target_table, expand_and_collapse, expand_and_collapse_count);  //apeend tree data to target table
    }
}

function flatDataToTreeData(all_flat_data, selected_flat_data, input_number=false) {
    var result_tree_data = new Array();
    if(selected_flat_data.length > 0) {
        // get the first level
        selected_flat_data.map(function(each_data_details) {
            if(result_tree_data.length == 0) {
                var parameter_list = [each_data_details.parents[1], getGameTextByItemId(all_flat_data, each_data_details.parents[1], each_data_details.text), '#', new Array()];

                if(input_number == true) {
                    var level_percentage = setLevelPercentage(each_data_details.percentage_level, 1);
                    parameter_list.push(input_number, level_percentage[0], level_percentage[1]);
                    parameter_list[1] = parameter_list[1] + '<span class=percentage_source_'+ level_percentage[1] + '>( ' + level_percentage[0] + ' % )</span>';
                }
                var inside_arr = createObjContent.apply(this, parameter_list);
                result_tree_data.push(inside_arr);
            }
            else {
                var selected_game_tree_focus = result_tree_data.filter(function(gp) {
                    return gp.id == each_data_details.parents[1];
                });

                if( (each_data_details.parents.length == 3) && (selected_game_tree_focus.length == 0) ) {
                    var parameter_list = [each_data_details.parents[1], getGameTextByItemId(all_flat_data, each_data_details.parents[1], each_data_details.text), '#', new Array()];
                    if(input_number == true) {
                        var level_percentage = setLevelPercentage(each_data_details.percentage_level, 1);
                        parameter_list.push(input_number, level_percentage[0], level_percentage[1]);
                        parameter_list[1] = parameter_list[1] + '<span class=percentage_source_'+ level_percentage[1] + '>( ' + level_percentage[0] + ' % )</span>';
                    }
                    var inside_arr = createObjContent.apply(this, parameter_list);
                    result_tree_data.push(inside_arr);
                }
            }

        });

        // get the second level
        var level2_tree = new Array();
        selected_flat_data.map(function(each_data_details) {
            if(each_data_details.parent != '#') {   // this flat data item is not first level like gp_2, need to parse it.
                if(level2_tree.length == 0) {
                    var parameter_list = [each_data_details.parent, getGameTextByItemId(all_flat_data, each_data_details.parent, each_data_details.text), each_data_details.parents[1], new Array()];
                    if(input_number == true) {
                        var level_percentage = setLevelPercentage(each_data_details.percentage_level, 2);
                        parameter_list.push(input_number, level_percentage[0], level_percentage[1]);
                        parameter_list[1] = parameter_list[1] + '<span class=percentage_source_'+ level_percentage[1]+ '>( ' + level_percentage[0] + ' % )</span>';
                    }
                    var inside_arr = createObjContent.apply(this, parameter_list);

                    var parameter_list = [each_data_details.id, each_data_details.text, each_data_details.parent, "none"];
                    if(input_number == true) {
                        var level_percentage = setLevelPercentage(each_data_details.percentage_level, 3);
                        parameter_list.push(input_number, level_percentage[0], level_percentage[1]);
                        parameter_list[1] = parameter_list[1] + '<span class=percentage_source_'+ level_percentage[1]+ '>( ' + level_percentage[0] + ' % )</span>';
                    }
                    var inside_game_arr = createObjContent.apply(this, parameter_list);

                    inside_arr.children.push(inside_game_arr);
                    level2_tree.push(inside_arr);
                }
                else {
                    var level2_tree_focus = level2_tree.filter(function(gt) {
                        return gt.id == each_data_details.parent;
                    });

                    if(level2_tree_focus.length == 0) {
                        var parameter_list = [each_data_details.parent, getGameTextByItemId(all_flat_data, each_data_details.parent, each_data_details.text), each_data_details.parents[1], new Array()];
                        if(input_number == true) {
                            var level_percentage = setLevelPercentage(each_data_details.percentage_level, 2);
                            parameter_list.push(input_number, level_percentage[0], level_percentage[1]);
                            parameter_list[1] = parameter_list[1] + '<span class=percentage_source_'+ level_percentage[1]+ '>( ' + level_percentage[0] + ' % )</span>';
                        }
                        var inside_arr = createObjContent.apply(this, parameter_list);

                        var parameter_list = [each_data_details.id, each_data_details.text, each_data_details.parent, "none"];
                        if(input_number == true) {
                            var level_percentage = setLevelPercentage(each_data_details.percentage_level, 3);
                            parameter_list.push(input_number, level_percentage[0], level_percentage[1]);
                            parameter_list[1] = parameter_list[1] + '<span class=percentage_source_'+ level_percentage[1]+ '>( ' + level_percentage[0] + ' % )</span>';
                        }
                        var inside_game_arr = createObjContent.apply(this, parameter_list);

                        inside_arr.children.push(inside_game_arr);
                        level2_tree.push(inside_arr);
                    }
                    else {
                        var parameter_list = [each_data_details.id, each_data_details.text, each_data_details.parent, "none"];
                        if(input_number == true) {
                            var level_percentage = setLevelPercentage(each_data_details.percentage_level, 3);
                            parameter_list.push(input_number, level_percentage[0], level_percentage[1]);
                            parameter_list[1] = parameter_list[1] + '<span class=percentage_source_'+ level_percentage[1]+ '>( ' + level_percentage[0] + ' % )</span>';
                        }
                        var inside_game_arr = createObjContent.apply(this, parameter_list);
                        level2_tree_focus[0].children.push(inside_game_arr);
                    }
                }
            }
        });

        // console.log(level2_tree);
        // console.log(result_tree_data);

        level2_tree.map(function(gt) {
            result_tree_data.map(function (gp) {
                if(gt.parent == gp.id) {
                    gp.children.push(gt);
                }
            });
        });
    }

    result_tree_data = result_tree_data.filter(function(item) {
        return (item.id != "#") && (typeof item.id !== 'undefined');
    });
    // console.log(result_tree_data);
    return result_tree_data;
}

function setLevelPercentage(percentage_level_arr, level) {
    var result_percentage = '';
    var result_percentage_source = '0';
    $.each(percentage_level_arr, function( level_num, p_value ) {
        // console.log("level_num: "+ level_num+', p_value: '+ p_value);
        if( (level_num <= level) && (p_value.trim().length != 0) )  {
            result_percentage = p_value;
            result_percentage_source = level_num;
        }
    });
    return [result_percentage, result_percentage_source];
}

/*
 * Parameters utils
 * 1. tree_data: structured tree data
 * 2. target_table: display the assigned tree data to this table
 */
function appendTreeDataToTable(tree_data, target_table, expand_and_collapse=false, expand_and_collapse_count=5, prefix=hashCode(target_table)) {
    $(target_table).empty();

    tree_data.map(function (gp) {
        var gp_fieldset =
            '<fieldset id="' + prefix + '_' + gp.id + '" style="padding:20px;margin-bottom: 5px;">' +
                '<legend><h5><strong>' + gp.text + '</strong></h5></legend>' +
            '</field>' +
            '<div class="table-responsive">' +
                '<div>' +
                    '<div class="clearfix"></div>' +
                    '<table class="table table-bordered table-hover" id="'+ gp.id + '-table">' +
                        '<tbody id="' + prefix + '_' + gp.id + '-tbody">' +

                        '</tbody>' +
                    '</table>' +
                '</div>' +
            '</div>';

        $(target_table).append(gp_fieldset);

        gp.children.map(function(gt) {
            var each_table_row =
                '<tr>' +
                    '<td class="td-short" id="' + prefix + '_' + gt.id + '"><h6><strong>' + gt.text + '</strong></h6></td>' +
                    '<td class="td-long" id="' + prefix + '_' + gt.id + '-gd"></td>' +
                '</tr>';

            $('#' + prefix + '_' + gp.id + '-tbody').append(each_table_row);

            var gd_str = '';
            var count = 0;
            gd_id = prefix + '_' + gt.id + '-gd';
            gd_dom_id = '#' + gd_id;

            gt.children.map(function(gd) {
                if(expand_and_collapse == false) {	//not using expand_and_collapse list
                    gd_str = gd_str + '<p>' + gd.text + '</p>';
                }
                else {
                    if(count > expand_and_collapse_count) {
                        gd_str = gd_str + '<p class="content-hide">' + gd.text + '</p>';
                    }
                    else if(count == expand_and_collapse_count) {
                        gd_str = gd_str +
                            '<p> <a id="' + gd_id + '-expand_and_collapse" href="#" onclick="return expandAll(event, this.id)">Expand All...</a></p>' +
                            '<p class="content-hide">' + gd.text + '</p>';
                    }
                    else {
                        gd_str = gd_str + '<p>' + gd.text + '</p>';
                    }
                    count++;
                }
            });

            $(gd_dom_id).append(gd_str);
        });
    });
}

function createObjContent(id, text, parent, children, input_number=false, percentage='', percentage_source='') {
    var inside_arr = {};
    inside_arr.id = id;
    inside_arr.text = text;
    inside_arr.parent = parent;
    if(input_number==true) {
        inside_arr.percentage = percentage;
        inside_arr.percentage_source = percentage_source;
    }
    if(children != 'none') {
        inside_arr.children = children;
    }

    return inside_arr;
}

function getGameTextByItemId(json_flat_data, item_id, item_default_text) {
    if((item_id == '#') || (typeof item_id === 'undefined')) {
        return item_default_text;
    }
    var item = json_flat_data.filter(function(element) {
        return element.id == item_id;
    });

    return item[0].text;
}

function hashCode(string) {
    var a = 1, c = 0, h, o;
    if (string) {
        a = 0;

        for (h = string.length - 1; h >= 0; h--) {
            o = string.charCodeAt(h);
            a = (a<<6&268435455) + o + (o<<14);
            c = a & 266338304;
            a = c!==0?a^c>>21:a;
        }
    }
    return String(a);
}

function expandAll(event, expand_id) {
	event.preventDefault();
	var gd_id = expand_id.split('-')[0];

	$('#' + gd_id + '-gd .content-hide').attr('class', 'content-show');
	$('#' + expand_id).attr('onclick', 'return collapseAll(event, this.id)');
	$('#' + expand_id).text('Collapse All...');
}

function collapseAll(event, collapse_id) {
	event.preventDefault();
	var gd_id = collapse_id.split('-')[0];

	$('#' + gd_id + '-gd .content-show').attr('class', 'content-hide');
	$('#' + collapse_id).attr('onclick', 'return expandAll(event, this.id)');
	$('#' + collapse_id).text('Expand All...');
}

function createFilterQueryString(source_arr) {
    var result_str = '';
    $.each(source_arr, function(key, value) {
        if(value == '0' || value == '1') {
            result_str = result_str + '&' + key + '=' + value;
        }
        if(result_str.length > 0) {
            result_str = result_str.replace(result_str.charAt(0), '?');
        }
    });
    return result_str;
}

function appendSelectNumberToTreeData(input_select_number, default_num_value, selected_flat_data, first_level_prefix, second_level_prefix, third_level_prefix) {
    var first_level_selected_percentage = {};  //get each first_level item percentage
    var second_level_selected_percentage = {}; //get each second_level item percentage
    var third_level_selected_percentage = {};  //get each third_level item percentage

    $.each(input_select_number, function( select_id, value ) {
        if(value.length != 0) {
            var new_key = select_id.replace('per_', '');
            if(!new_key.includes(second_level_prefix)) {
                first_level_selected_percentage[new_key] = value;
            }
            else if(new_key.includes(third_level_prefix)) {
                third_level_selected_percentage[new_key] = value;
            }
            else {
                second_level_selected_percentage[new_key] = value;
            }
        }
    });
    // console.log(first_level_selected_percentage);
    // console.log(second_level_selected_percentage);
    // console.log(third_level_selected_percentage);

    selected_flat_data.map(function(each_data_details) {
        var percentage_content = new Array();
        percentage_content.push(default_num_value);

        var first_level_flag = false;
        $.each(first_level_selected_percentage, function( first_level_id, p_value ) {
            if(each_data_details.parents[1] == first_level_id) {
                percentage_content.push(p_value);
                first_level_flag = true;
            }
        });
        if(first_level_flag == false) percentage_content.push("");

        var second_level_flag = false;
        $.each(second_level_selected_percentage, function( second_level_id, p_value ) {
            if(each_data_details.parent == second_level_id) {
                percentage_content.push(p_value);
                second_level_flag = true;
            }
        });
        if(second_level_flag == false) percentage_content.push("");

        var third_level_flag = false;
        $.each(third_level_selected_percentage, function( third_level_id, p_value ) {
            if(each_data_details.id == third_level_id) {
                percentage_content.push(p_value);
                third_level_flag = true;
            }
        });
        if(third_level_flag == false) percentage_content.push("");

        each_data_details.percentage_level = percentage_content;
    });

    return selected_flat_data;
}