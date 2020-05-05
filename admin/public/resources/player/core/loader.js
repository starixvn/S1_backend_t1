/**
 * @author Elvis Chen<elvis.php.tw@tripleonetech.net>
 * @param object window
 * @returns void
 */
var Loader = window.t1t_loader = window.Loader = window.loader = window.Loading = (function(window){
    var LOADER_OPACITY = (window.hasOwnProperty('LOADER_OPACITY')) ? window['LOADER_OPACITY'] : "0.8";
    
    function Loader(){
        this.callback = false;

        this.container = null;
        this.loader_container = null;
        
        this.interval = null;
        this.interval_sn = 0;
    }
    
    Loader.prototype.fetch_container = function(selector){
        var container;
        if(selector !== undefined){
            container = $(selector);
            if(container.length <= 0){
                container = $((selector.indexOf('.') === 0) ? selector : 'body');
            }
            return (container.length <= 0) ? false : container;
        }else{
            return false;
        }
    };
    
    Loader.prototype.init_objects = function(){
        var container, show_text, callback;
        
        switch(arguments.length){
            case 3:
                container = this.fetch_container(arguments[0]);
                if(container === false){
                    container = $('body');
                }
                show_text = arguments[1];
                callback = (typeof arguments[2] === 'function') ? arguments[2] : false;
                break;
            case 2:
                if(typeof arguments[1] === 'function'){
                    container = this.fetch_container(arguments[0]);
                    if(container === false){
                        container = $('body');
                        show_text = arguments[0];
                    }else{
                        show_text = false;
                    }
                    callback = arguments[1];
                }else{
                    container = this.fetch_container(arguments[0]);
                    if(container === false){
                        container = $('body');
                        show_text = arguments[1];
                    }else{
                        show_text = arguments[1];
                    }
                    callback = false;
                }
                break;
            case 1:
                if(typeof arguments[0] === 'function'){
                    container = $('body');
                    show_text = false;
                    callback = arguments[0];
                }else{
                    container = this.fetch_container(arguments[0]);
                    if(container === false){
                        container = $('body');
                        show_text = arguments[0];
                    }else{
                        show_text = false;
                    }
                    callback = false;
                }
                break;
            default:
                container = $('body');
                show_text = false;
                callback = false;
        }
        
        if(show_text !== false){
            show_text = (window.hasOwnProperty('Language') && Language.hasOwnProperty('cmsLang') && Language.hasOwnProperty(cmsLang) && (typeof Language.cmsLang === "object")) ? Language.cmsLang[show_text] : show_text;
        }
        
        return {
            "container": container,
            "show_text": show_text,
            "callback": callback
        };
    };
    
    Loader.prototype.init_loader_container = function(){
        var loader = $('<div class="loader">').hide();
        
        return loader;
    };
    
    Loader.prototype.init_loader_content = function(show_text){
        var self = this;
        
        var loader_vertical_helper = $('<div class="loader_vertical_helper"></div>');
        
        var loader_content = $('<div class="loader_content"></div>');
        loader_content.appendTo(loader_vertical_helper);
        
        var loader_animation = $('<div class="loader_animation"></div>');
        loader_animation.appendTo(loader_content);
        
        var loader_text = $('<div class="loader_text"></div>');
        
        if(show_text){
            loader_text.text(show_text);
            this.interval = setInterval(function(){
                self.interval_sn++;

                loader_text.text(show_text + '.'.repeat(parseInt(self.interval_sn % 4)));
            }, 300);
        }
        loader_text.appendTo(loader_content);
        
        return loader_vertical_helper;
    };
    
    Loader.prototype.init = function(){
        var args = this.init_objects.apply(this, Array.prototype.slice.call(arguments));
        
        var loader_container = this.init_loader_container();
        
        var loader_content = this.init_loader_content(args.show_text);
        loader_content.appendTo(loader_container);
        
        if(args.container.is('body')){
            loader_container.css({
                "position": "fixed"
            });
            loader_container.on('click', function(){
                event.stopPropagation();
                
                return false;
            });
        }else{
            loader_container.css({
                "position": "absolute"
            });

            if(args.container.parent().css('position') === 'relative'){
                args.container.addClass('loader_parent_absolute');
            }else{
                if(args.container.css('position') == 'static'){
                    args.container.addClass('loader_parent_relative');
                }
            }
        }
        loader_container.appendTo(args.container);

        this.container = args.container;
        this.loader_container = loader_container;
        this.callback = args.callback;
        
        return loader_container;
    };
    
    Loader.prototype.show = function(){
        var self = this;
        
        this.loader_container.fadeIn(300, function(){
            if(self.callback){
                self.callback(self);
            }
        });
        
        return this;
    };
    
    Loader.prototype.hide = function(callback){
        var self = this;
        
        if(this.interval){
            clearInterval(this.interval);
        }
        
        this.loader_container.fadeOut(300, function(){
            self.container.removeClass('loader_parent_absolute').removeClass('loader_parent_relative');

            if(typeof callback === "function"){
                callback();
            }
            
            self.loader_container.remove();
        });
        
        return this;
    };
    
    function LoaderManagement(){
        this.loaders = new Array();
    }
    
    LoaderManagement.prototype.show = function(){
        var loader = new Loader();
        loader.init.apply(loader, Array.prototype.slice.call(arguments));
        
        this.loaders.push(loader);
        
        return loader.show();
    };
    
    LoaderManagement.prototype.hide = function(callback){
        if(this.loaders.length <= 0){
            return;
        }
        
        var loader = this.loaders.pop();
        
        return loader.hide(callback);
    };
    
    return new LoaderManagement();
})(window);