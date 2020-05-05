(function(){
    function T1T_Announcement(){
        this.name = 'announcement';
    }

    T1T_Announcement.prototype.renderAnnouncement= function(){
        var self = this;

        //setup announcement url
        var url = variables.urls.player + "/pub/announcement";

        var element = $('._public_news');

        if(element.length <= 0){
            return;
        }

        switch(element.prop('tagName').toLowerCase()){
            case 'div':
                if(utils.parseInt(element.data('enable-marquee'), 1) !== 0){
                    utils.addJS(utils.getPlayerCmsUrl('/resources/third_party/jquery-marquee/1.5.0/jquery.marquee.js'), false, function(){
                        window.$('._public_news .marquee').marquee({
                            //speed in milliseconds of the marquee
                            //duration: 15000,
                            speed: 60,
                            //gap in pixels between the tickers
                            gap: 50,
                            //time in milliseconds before the marquee will start animating
                            delayBeforeStart: 0,
                            //'left' or 'right'
                            direction: 'left',
                            //true or false - should the marquee be duplicated to show an effect of continues flow
                            duplicated: false // Do not enable this, because it will affects the speed.
                        });

                        window.$('._public_news .marquee').on("mouseover mouseout", function(e){
                            window.$(this).marquee("toggle");
                        });
                    });
                }

                var container = $('<div class="marquee">');
                container.appendTo(element);
                container.on('click', function(){
                    self.show_announcement();
                });

                $.each(variables.announcement, function(key, entry){
                    $('<span class="entry">').html('<span class="title"><b>'
                        + utils.strap_match_tags(entry['title'])
                        + '</b></span><span class="content">'
                        + utils.strap_match_tags(entry['content'])
                        + '</span>').appendTo(container);
                });
                break;
            case 'iframe':
            default:
                element.attr('src', url);

                var announcement_option = utils.parseInt(variables.announcement_option, 2);
                var auto_popup_announcements_on_the_first_visit = variables.auto_popup_announcements_on_the_first_visit;
                var auto_popup_announcement = cookies.get('auto_popup_announcement');
                if(announcement_option === 2 && !!auto_popup_announcements_on_the_first_visit){
                    if(!auto_popup_announcement){
                        $(function(){
                            self.showAnnouncementModal();
                            cookies.set('auto_popup_announcement', 1, {
                                "domain": '.' + variables.main_host
                            });
                        });
                    }
                }
                break;
        }
    };

    T1T_Announcement.prototype.show_announcement = function(){
        var announcement_option = utils.parseInt(variables.announcement_option, 2);

        if(announcement_option === 1){
            window.open(utils.getSystemUrl('player', '/pub/announcement_popup'), "_blank", "toolbar=yes, scrollbars=yes, resizable=no, top=100, left=10, width=600, height=450");
        }else{
            this.showAnnouncementModal();
        }
    };

    T1T_Announcement.prototype.showAnnouncementModal = function(){
        var iframe_container = null;
        if($('.player_announcement_popup_container').length <= 0){
            iframe_container = $('<div class="t1t-ui modal player_announcement_popup_container">\n' +
                '    <div class="modal-dialog">\n' +
                '        <div class="modal-content">\n' +
                '            <div class="modal-heading">\n' +
                '                <h4 class="modal-title">' + variables.langText.header_Announcements + '</h4>\n' +
                '                <button type="button" class="close">&times;</button>\n' +
                '            </div>\n' +
                '            <div class="modal-body"></div>\n' +
                '        </div>\n' +
                '    </div>\n' +
                '</div>');
            iframe_container.appendTo($('body'));
        }else{
            iframe_container = $('.player_announcement_popup_container');
        }

        iframe_container.on('show.t1t.ui.modal', function(){
            var url = variables.urls.player + "/pub/announcement_popup_list/nav_list/" + utils.getISO639STwoLetterLanguage(variables.currentLang);
            $('.modal-body', iframe_container).append($('<iframe>').attr('src', url));
        });

        iframe_container.on('hidden.t1t.ui.modal', function(){
            iframe_container.remove();
        });

        $('.close', iframe_container).on('click', function(){
            iframe_container.modal('hide');
        });

        iframe_container.modal({
            backdrop: 'static',
            keyboard: false
        });
    };

    var announcement = new T1T_Announcement();
    smartbackend.addAddons(announcement.name, smartbackend);

    smartbackend.on('run.t1t.smartbackend', function(){
        announcement.renderAnnouncement();
    });

    utils.registerMessageEvent('announcement_popup', function(jsonData) {
        announcement.showAnnouncementModal();
    });

    renderUI.renderAnnouncement = $.proxy(announcement.renderAnnouncement, announcement);
    renderUI.showAnnouncementModal = $.proxy(announcement.showAnnouncementModal, announcement);

    return announcement;
})();