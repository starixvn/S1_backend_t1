var imgloader = "/resources/images/ajax-loader.gif";

// games menu
$(document).ready(function() {
    var url = document.location.pathname;
    var res = url.split("/");
    // console.log('res: '+res);
    for (i = 0; i < res.length; i++) {
        switch (res[i]) {
        	case '1':
                $("#game_menu_sec_item_link_1").addClass("game_menu_sec_item_active");
                break;

            case '2':
                $("#game_menu_sec_item_link_2").addClass("game_menu_sec_item_active");
                break;

            case '3':
                $("#game_menu_sec_item_link_3").addClass("game_menu_sec_item_active");
                break;

            case '4':
                $("#game_menu_sec_item_link_4").addClass("game_menu_sec_item_active");
                break;

            case '5':
                $("#game_menu_sec_item_link_5").addClass("game_menu_sec_item_active");
                break;

            case '6':
                $("#game_menu_sec_item_link_6").addClass("game_menu_sec_item_active");
                break;

            default:
                break;
        }
    }
});