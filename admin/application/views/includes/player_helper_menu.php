<style type="text/css">
.sbe_player_helper_menu {
    position: fixed;

    display: block;
}
</style>
<script type="text/javascript">
(function(){
    var SBEPlayerHelperMenu = function(){
        this.player_info = null;
        this.target = null;
        this.container = null;
        this.timer = null;
    };

    SBEPlayerHelperMenu.prototype.run = function(player_info, e){
        var self = this;

        self.player_info = player_info;
        self.target = $(e.target);

        self.createMenu(player_info, e.clientX - e.offsetX + $(e.target).width(), e.clientY - e.offsetY);
    };

    SBEPlayerHelperMenu.prototype.checkCloseMenu = function(){
        var self = this;

        this.timer = setTimeout(function(){
            if(self.target.is(':hover') || self.container.is(':hover')){
                self.checkCloseMenu();
                return false;
            }

            self.closeMenu();
        }, 500);
    };

    SBEPlayerHelperMenu.prototype.createMenu = function(player_info, positionX, positionY){
        var self = this;

        var container = self.container = $('<div>');
        container.attr({
            "class": "sbe_player_helper_menu dropdown open"
        });
        container.css({
            "top": positionY,
            "left": positionX
        });
        container.appendTo($('body'));

        var dropdown_menu = $('<ul class="dropdown-menu">');
        dropdown_menu.appendTo(container);

        dropdown_menu.append($('<h6 class="dropdown-header">' + player_info.player_name + '</h6>'));
        dropdown_menu.append($('<li role="separator" class="divider"></li>'));

        $.each(sbe_player_helper_menu_manager.menu_items, function(key, value){
            var dropdown_item_container = $('<li class="dropdown-item"></li>');
            var dropdown_item = $('<a href="javascript: void(0);"></a>');

            dropdown_item.html(value.item_name);

            dropdown_item.on('click', function(){
                self.closeMenu();
                value.callback(self.player_info);
            });

            dropdown_item.appendTo(dropdown_item_container);
            dropdown_item_container.appendTo(dropdown_menu);
        });

        self.target.data('sbe-player-helper-menu-opened', 1);
        self.checkCloseMenu();
    };

    SBEPlayerHelperMenu.prototype.closeMenu = function(){
        var self = this;
        if(!this.container) return this;

        if(!!this.timer){
            clearTimeout(this.timer);
        }

        var container = this.container;
        container.remove();

        this.container = null;

        self.target.data('sbe-player-helper-menu-opened', 0);
    };

    function SBEPlayerHelperMenuManager(){
        this.menu_items = {};
    }

    SBEPlayerHelperMenuManager.prototype.isOpened = function(target){
        return !!$(target).data('sbe-player-helper-menu-opened');
    };

    SBEPlayerHelperMenuManager.prototype.addItem = function(item_key, item_name, callback){
        this.menu_items[item_key] = {
            "item_key": item_key,
            "item_name": item_name,
            "callback": callback
        };
    };

    var sbe_player_helper_menu_manager = window.sbe_player_helper_menu_manager = new SBEPlayerHelperMenuManager();

    $(function(){
        $(document).on('mouseover', '[data-toggle="sbe-player-helper-menu"]', function(e){
            if(sbe_player_helper_menu_manager.isOpened(e.target)){
                return;
            }

            var player_info = {
                "player_id": $(this).data('player-id'),
                "player_name": $(this).data('player-name')
            };

            var sbe_player_helper_menu = new SBEPlayerHelperMenu();

            sbe_player_helper_menu.run(player_info, e);
        });
    });
})();
</script>