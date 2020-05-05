var MessageBox = window.t1t_messagebox = window.MessageBox = (function(){
    var MESSAGEBOX_TYPE_NORMAL = 'normal';
    var MESSAGEBOX_TYPE_PRIMARY = 'primary';
    var MESSAGEBOX_TYPE_SUCCESS = 'success';
    var MESSAGEBOX_TYPE_INFO = 'info';
    var MESSAGEBOX_TYPE_WARNING = 'warning';
    var MESSAGEBOX_TYPE_DANGER = 'danger';

    function MessageBox(options){
        this.options = $.extend({}, {
            'header_class_normal': 'font-weight-bold bg-light text-light',
            'header_class_primary': 'font-weight-bold bg-primary text-primary',
            'header_class_success': 'font-weight-bold bg-success text-success',
            'header_class_info': 'font-weight-bold bg-info text-info',
            'header_class_warning': 'font-weight-bold bg-warning text-warning',
            'header_class_danger': 'font-weight-bold bg-danger text-danger',
            'header_symbol_normal': 'fa-bookmark',
            'header_symbol_primary': 'fa-bullhorn',
            'header_symbol_success': 'fa-check-circle',
            'header_symbol_info': 'fa-info-circle',
            'header_symbol_warning': 'fa-exclamation-circle',
            'header_symbol_danger': 'fa-exclamation-triangle',
            'header_title_normal': 'Hint',
            'header_title_primary': 'Hint',
            'header_title_success': 'Success',
            'header_title_info': 'Info',
            'header_title_warning': 'Warning',
            'header_title_danger': 'Danger',
            'body_class_normal': 'bg-light',
            'body_class_primary': 'bg-light',
            'body_class_success': 'bg-light',
            'body_class_info': 'bg-light',
            'body_class_warning': 'bg-light',
            'body_class_danger': 'bg-light',
            'footer_class_normal': 'font-weight-bold bg-light text-light',
            'footer_class_primary': 'font-weight-bold bg-primary text-primary',
            'footer_class_success': 'font-weight-bold bg-success text-success',
            'footer_class_info': 'font-weight-bold bg-info text-info',
            'footer_class_warning': 'font-weight-bold bg-warning text-warning',
            'footer_class_danger': 'font-weight-bold bg-danger text-danger',
            'close_button_text': 'Close',
            'confirm_button_text': 'Confirm'
        }, options);

        this.container = null;
    }

    MessageBox.prototype.show = function(type, message, title, callback, buttons){
        var self = this;
        
        if($.isEmptyObject(type) || $.isEmptyObject(message)){
            if(typeof callback === "function") callback();

            return false;
        }

        if((type != MESSAGEBOX_TYPE_NORMAL) && (type != MESSAGEBOX_TYPE_PRIMARY) && (type != MESSAGEBOX_TYPE_SUCCESS) && (type != MESSAGEBOX_TYPE_INFO) && (type != MESSAGEBOX_TYPE_WARNING) && (type != MESSAGEBOX_TYPE_DANGER)){
            if(typeof callback === "function") callback();

            return false;
        }

        if(message.length < 1){
            if(typeof callback === "function") callback();

            return false;
        }
        
        var modal_header_title_symbol = self.options.header_symbol_normal;
        var modal_header_title_string = self.options.header_title_normal;
        var modal_header_title_class = self.options.header_class_normal;
        var modal_body_class = self.options.body_class_normal;
        var modal_footer_class = self.options.footer_class_normal;
        switch(type){
            case MESSAGEBOX_TYPE_SUCCESS:
                modal_header_title_symbol = self.options.header_symbol_success;
                modal_header_title_string = self.options.header_title_success;
                modal_header_title_class = self.options.header_class_success;
                modal_body_class = self.options.body_class_success;
                modal_footer_class = self.options.footer_class_success;
            break;
            case MESSAGEBOX_TYPE_INFO:
                modal_header_title_symbol = self.options.header_symbol_info;
                modal_header_title_string = self.options.header_title_info;
                modal_header_title_class = self.options.header_class_info;
                modal_body_class = self.options.body_class_info;
                modal_footer_class = self.options.footer_class_info;
            break;
            case MESSAGEBOX_TYPE_WARNING:
                modal_header_title_symbol = self.options.header_symbol_warning;
                modal_header_title_string = self.options.header_title_warning;
                modal_header_title_class = self.options.header_class_warning;
                modal_body_class = self.options.body_class_warning;
                modal_footer_class = self.options.footer_class_warning;
            break;
            case MESSAGEBOX_TYPE_DANGER:
                modal_header_title_symbol = self.options.header_symbol_danger;
                modal_header_title_string = self.options.header_title_danger;
                modal_header_title_class = self.options.header_class_danger;
                modal_body_class = self.options.body_class_danger;
                modal_footer_class = self.options.footer_class_danger;
            break;
            case MESSAGEBOX_TYPE_PRIMARY:
            default:
                modal_header_title_symbol = self.options.header_symbol_primary;
                modal_header_title_string = self.options.header_title_primary;
                modal_header_title_class = self.options.header_class_primary;
                modal_body_class = self.options.body_class_primary;
                modal_footer_class = self.options.footer_class_primary;
                type = MESSAGEBOX_TYPE_PRIMARY;
                break;

        }

        var modal = $('<div>').attr({
            'class': 't1t-ui modal t1t-message-box'
        }).css({
            'position': 'fixed'
        }).appendTo('body');
        this.container = modal;
        
        var modal_vertical_alignment_helper = $('<div>').attr({
            'class': 'modal-vertical-alignment-helper'
        }).css({
            'display': 'table',
            'width': '100%',
            'height': '100%'
        }).appendTo(modal);
        
        var modal_dialog = $('<div>').attr({
            'class': 'modal-dialog',
            'role': 'document'
        }).css({
            'display': 'table-cell',
            'vertical-align': 'middle',
            'pointer-events': 'none'
        }).appendTo(modal_vertical_alignment_helper);
        
        var modal_content = $('<div>').attr({
            'class': 'modal-content'
        }).css({
            'margin': '0 auto',
            'width': 'inherit',
            'max-width': 'inherit',
            'border-radius': '1em',
            'pointer-events': 'all'
        }).appendTo(modal_dialog);
        
        var modal_header = $('<div>').attr({
            'class': 'modal-header'
        }).css({
            'border-radius': '1em 1em 0 0',
            'height': 'auto',
            'overflow': 'hidden'
        }).appendTo(modal_content).addClass(modal_header_title_class);
        
        var modal_header_title = $('<h4>').attr({
            'class': 'modal-title pull-left'
        }).appendTo(modal_header);
        
        var modal_header_title_content = (modal_header_title_symbol.length) ? '<i class="fa ' + modal_header_title_symbol + '" aria-hidden="true"></i>' : '';
        if(!$.isEmptyObject(title) && (title.length > 0)){
            modal_header_title_content += '&nbsp;<span>' + title + '</span>';
        }else{
            modal_header_title_content += '&nbsp;<span>' + modal_header_title_string + '</span>';
        }
        modal_header_title.html(modal_header_title_content);
        
        var modal_header_close = $('<button>').attr({
            'type': 'button',
            'class': 'close pull-right',
            'arial-label': 'Close'
        }).appendTo(modal_header);
        modal_header_close.html('<span aria-hidden="true">&times;</span>');
        modal_header_close.off('click').on('click', function(){
            self.close();
        });
        
        var modal_body = $('<div>').attr({
            'class': 'modal-body'
        }).appendTo(modal_content);
        modal_body.html('<p>' + message + '</p>');
        modal_body.addClass(modal_body_class);
        
        var modal_footer = $('<div>').attr({
            'class': 'modal-footer'
        }).css({
            'border-radius': '0 0 1em 1em'
        }).appendTo(modal_content).addClass(modal_footer_class);
        
        if((buttons === undefined) || !(buttons instanceof Array)){
            var modal_footer_close = $('<button>').attr({
                'class': 'btn btn-' + type,
                'data-dismiss': 'modal'
            }).appendTo(modal_footer);
            
            modal_footer_close.html(self.options.close_button_text);
            
            modal_footer_close.off('click').on('click', function(){
                self.close();
            });
        }else{
            $.each(buttons, function(idx, button_options){
                if($.isEmptyObject(button_options)){
                    return;
                }
                
                var button = $('<button>');
                
                if(button_options.hasOwnProperty('attr')){
                    button.attr(button_options.attr);
                }
                
                if(button_options.hasOwnProperty('text')){
                    button.html(button_options.text);
                }
                
                button.off('click').on('click', function(){
                    if(button_options.hasOwnProperty('callback') && (typeof button_options['callback'] === 'function')){
                        button_options.callback();
                    }

                    self.close();
                });
                
                button.appendTo(modal_footer);
            });
        }

        modal.off('shown.t1t.ui.modal').on('shown.t1t.ui.modal', function(){
            var instance = modal.data('t1t.ui.modal');

            $(instance._backdrop).addClass('t1t-message-box');
        });

        modal.off('hidden.t1t.ui.modal').on('hidden.t1t.ui.modal', function(){
            if(typeof callback === "function") callback();

            self.remove();
        });
        
        modal.modal({
            backdrop: 'static',
            keyboard: false
        });
        
        return this;
    };

    MessageBox.prototype.close = function(){
        var self = this;
        
        if(self.container == null) return true;
        
        self.container.modal('hide');
        
        return this;
    };

    MessageBox.prototype.remove = function(){
        this.container.remove();
        this.container = null;
        
        return this;
    };
    
    function MessageBoxManager(){
        this.options = {};
    }
    
    MessageBoxManager.prototype.setOptions = function(options){
        this.options = $.extend({}, this.options, options);
        
        return this;
    };

    MessageBoxManager.prototype.show = function(message, title, callback, buttons){
        return (new MessageBox(this.options)).show(MESSAGEBOX_TYPE_NORMAL, message, title, callback, buttons);
    };

    MessageBoxManager.prototype.normal = MessageBoxManager.prototype.show;

    MessageBoxManager.prototype.primary = function(message, title, callback, buttons){
        return (new MessageBox(this.options)).show(MESSAGEBOX_TYPE_PRIMARY, message, title, callback, buttons);
    };

    MessageBoxManager.prototype.success = function(message, title, callback, buttons){
        return (new MessageBox(this.options)).show(MESSAGEBOX_TYPE_SUCCESS, message, title, callback, buttons);
    };

    MessageBoxManager.prototype.info = function(message, title, callback, buttons){
        return (new MessageBox(this.options)).show(MESSAGEBOX_TYPE_INFO, message, title, callback, buttons);
    };

    MessageBoxManager.prototype.warning = function(message, title, callback, buttons){
        return (new MessageBox(this.options)).show(MESSAGEBOX_TYPE_WARNING, message, title, callback, buttons);
    };

    MessageBoxManager.prototype.danger = function(message, title, callback, buttons){
        return (new MessageBox(this.options)).show(MESSAGEBOX_TYPE_DANGER, message, title, callback, buttons);
    };

    MessageBoxManager.prototype.ajax = function(response, callback){
        var message = response.hasOwnProperty('msg') ? response.msg : response.hasOwnProperty('message') ? response.message : 'Unknown';
        switch(response.status){
            case 'failed':
                this.danger(message, null, callback);
                return false;
            break;
            case 'success':
                this.success(message, null, callback);
                return true;
            break;
            default:
                this.info(message, null, callback);
                return true;
            break;
        }
    };

    MessageBoxManager.prototype.confirm = function(message, title, confirm_callback, cancel_callback, type){
        var self = this;

        this[(!!type) ? type : 'info'](message, title, null, [
            {
                'attr': {
                    'class': 'btn btn-primary msgbox-btn-confirm'
                },
                'text': self.options.confirm_button_text,
                'callback': function(){
                    if(typeof confirm_callback === "function") confirm_callback();
                }
            },
            {
                'attr': {
                    'class': 'btn btn-default msgbox-btn-cancel'
                },
                'text': self.options.close_button_text,
                'callback': function(){
                    if(typeof cancel_callback === "function") cancel_callback();
                }
            }
        ]);
    };

    return new MessageBoxManager();
})();