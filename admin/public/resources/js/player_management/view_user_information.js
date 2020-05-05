
var changeableTable = changeableTable||{};

changeableTable.initial = function(theOptions){
    var _this = this;
    _this.initD4CBO();
    _this.appendedClassStr = 'appended-into';
    _this.changeable_table$El = _this.getChangeableTable$El();
    if( typeof(theOptions) !== 'undefined'){
        _this = $.extend(true, _this, theOptions);
    }

}; // EOF changeableTable.initial()

/**
 * HTML script append into target element.
 *
 * If has appended element than ignore action.
 * The appended element will has the appended Class, "this.appendedClassStr".
 * @param {string} outerHtml The HTML script.
 * @param {string} targetSelectStr The selector string for appended to.
 * @param {string} checkSelectorStr The selector string for detect appended element.
 */
changeableTable.htmlAppendInto = function(outerHtml, targetSelectStr, checkSelectorStr){
    var _this = this;
    if($(checkSelectorStr).length == 0){
        $(targetSelectStr).append(outerHtml);
        $(checkSelectorStr).addClass(_this.appendedClassStr);
    }
    $(checkSelectorStr+ ':not(.'+ _this.appendedClassStr+ ')').remove();
}; // EOF htmlAppendInto

/**
 * Move source element,".modal" append into target element, and add class appended-into
 * @param sourceSelectStr {string} The selector string, usuallu with ".modal", ex: '[id="duplicateAccountModal"].modal'.
 * @param targetSelectStr {string} The selector string, will append in the element, ex: "footer".
 *
 */
changeableTable.appendInto = function(sourceSelectStr, targetSelectStr){
    var _this = this;
    if($(targetSelectStr+ ' '+ sourceSelectStr).length == 0){
        var outerHtml = _this.outerHtml(sourceSelectStr);
        $(targetSelectStr).append($(outerHtml));
        $(targetSelectStr+ ' '+ sourceSelectStr).addClass(_this.appendedClassStr);
    }
    $(sourceSelectStr+ ':not(.'+ _this.appendedClassStr+ ')').remove();
};

/**
 * Get element's outter Html like node.outerHTML .
 * @param {string} selectorStr The selector string.
 * @return {string} The html script.
 */
changeableTable.outerHtml = function(selectorStr){
    return $('<div>').append($(selectorStr).clone()).html();
};


/**
 * Get/reflash this.changeable_table$El, "#changeable_table".
 */
changeableTable.getChangeableTable$El = function(){
    var _this = this;
    if( typeof(_this.changeable_table$El) === 'undefined'
        || _this.changeable_table$El.length == 0
        || (! _this.changeable_table$El.is(":visible"))
    ){
        _this.changeable_table$El = $('#changeable_table');
    }
    return _this.changeable_table$El;
}; // EOF changeableTable.getChangeableTable$El()

/**
 * The script on Ready
 */
changeableTable.onReady = function(){
    var _this = this;

    $(window).on('resize', function () {

        /// Patch for the data too long to over layout after loading.
        // _this.script4DTScrollFullWidth();
    });

    // batch_tagging();
    // batch_linking_accounts();

    $('#playersLog').on('change', 'select:has(option[data-load])', function(e){
        _this.onChangeByOption(e);
    });

    /// copied from view_user_information.php
    $(document).on("click",".dup_modal_trigger",function(e) {
        e.preventDefault();
        var username = $(this).data('username');
        get_duplicate_account_detail_by_username_for_modal(username);
    });

};// EOF changeableTable.onReady()

/**
 * Initialize attr. deferr4onChangeByOption for $.load(uri,...) of onChangeByOption().
 * Deferr4onChangeByOption = D4CBO
 *
 */
changeableTable.initD4CBO = function(){
    var _this = this;
    _this.deferr4onChangeByOption = $.Deferred();

    _this.deferr4onChangeByOption.done(function(callback, player_id) {
        if (    callback
            && utils.evil("typeof "+callback+" !== 'undefined'")
            && typeof(eval(callback+'.call')) === 'function' // detect callable
        ) {

            $('#changeable_table .dateInput').each(function () {
                initDateInput($(this));
            });

            var theCode = callback+ '('+ player_id+ ')';
            if(changeableTable.isDebugEnv()){
                console.log('theCode', theCode);
            }
            utils.evil( theCode );
            // eval(callback)(player_id);
        }
    });
    _this.deferr4onChangeByOption.fail(function(){
        // @todo $.load().abort().
    });

    return _this.deferr4onChangeByOption;
}

/**
 * Convert URI to base64
 *
 * @param {string} theUrl The URI String
 */
changeableTable.btoaURL = function(theUrl){
    var _this = this;
    var pureUri = theUrl.split(' ')[0]; // filter selector string , "/player_management/ipHistory/16814 .panel".
    return btoa(pureUri).split('=').join('');
}
/**
 * Replace all keyword into another string
 *
 * Ref. to https://stackoverflow.com/a/1144788
 * @param {string} str The origin string.
 * @param {string} find The keyword will be replaced.
 * @param {string} replace The string for replace to.
 * @return {string} The string has replaced.
 */
changeableTable.replaceAll = function(str, find, replace) {
    return str.replace(new RegExp(find, 'g'), replace);
};
/**
 * htmlencode like function.
 * Ref. to https://codertw.com/%E5%89%8D%E7%AB%AF%E9%96%8B%E7%99%BC/252909/
 * @param {string} s The origin string.
 * @return {string} The string after htmlencode.
 */
changeableTable.htmlencode = function(s){
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(s));
    return div.innerHTML;
}
/**
 * htmldecode like function.
 * Ref. to https://codertw.com/%E5%89%8D%E7%AB%AF%E9%96%8B%E7%99%BC/252909/
 * @param {string} s The origin string.
 * @return {string} The string after htmldecode.
 */
changeableTable.htmldecode = function(s){
    var div = document.createElement('div');
    div.innerHTML = s;
    return div.innerText || div.textContent;
}

/**
 * Handle On Change by Option of [data-load] for Change Player Log UI.
 * @param {object event} e The event object.
 */
changeableTable.onChangeByOption = function(e){
    var _this = this;

    var appendTargetSelectorStr = 'footer';
    var el = $(e.target).find(':selected');
    var url = el.data('load');
    var params = el.data('params'); // json string
    var callback = el.data('callback');
    var dtSelector = el.data('dt-selector');
    var player_id = params['player_id'];


    if(_this.deferr4onChangeByOption.state() === 'resolved'){
        _this.initD4CBO();
    }else{ // not yet complite switch UI and change another UI.
        _this.deferr4onChangeByOption.reject(); // maybe failed or abort
        _this.initD4CBO();
    }

    var isLoaded = false;


    if( $(appendTargetSelectorStr+ ' .'+ _this.btoaURL(url) ).length > 0
        && typeof(dtSelector) !== 'undefined' // playerUpdateHistory and "Linked Account" need always loading
    ){
        isLoaded = true;
    }

    if( ! isLoaded ){
        _this.getChangeableTable$El()
        .html('<center><img src="' + imgloader + '"></center>')
        .load(url, params, function (responseText, textStatus, jqXHR) {

            if(_this.isDebugEnv()){
                console.log('onChangeByOption', 'url:', url, '_this.btoaURL(url):', _this.btoaURL(url), 'responseText:', responseText.length);
            }

            responseText = _this.htmlencode( responseText);// for nested tags,<stript> <style> "<!-- -->"... etc.
            var outerHtml = _this.outerHtml( $('<script type="text/template" class="'+ _this.btoaURL(url)+ '" data-load-uri="'+ url+'" >').html( responseText ) ); // _this.htmlencode('<![CDATA['+ lf+ responseText+ lf+ ']]>')) );

            _this.htmlAppendInto(outerHtml, appendTargetSelectorStr, appendTargetSelectorStr+ ' .'+ _this.btoaURL(url));

            changeableTable.displayLoadFileUnder$El( $("#changeable_table") );

            // will execute _this.deferr4onChangeByOption.done().
            _this.deferr4onChangeByOption.resolve(callback, player_id);

        }); /// EOF _this.changeable_table$El.load
    }else{
        // had loaded

        _this.getChangeableTable$El().empty();

        var pureUri = url.split(' ')[0];
        var theSelector = _this.replaceAll(url, pureUri, '');

        var checkSelectorStr = dtSelector;
        var targetSelectStr = _this.getChangeableTable$El().selector;
        var theHtml = $('.'+ _this.btoaURL(url) ).html();

        theHtml = _this.htmldecode( theHtml); // for nested tags,<stript> <style> "<!-- -->"... etc.
        if(_this.isDebugEnv()){
            // console.log('11onChangeByOption', 'theHtml:', theHtml);
        }
        if(theSelector != ''){ // for url contain selector of $.load(url).
            // Ref. to https://stackoverflow.com/a/3203072
            theHtml = _this.outerHtml($( theHtml ).filter(theSelector));
        }
        if(_this.isDebugEnv()){
            // console.log('onChangeByOption', 'theSelector:', '11'+theSelector+'11', '_this.btoaURL(url):', _this.btoaURL(url), 'theHtml:', theHtml.length);
        }
        _this.htmlAppendInto(theHtml, targetSelectStr, checkSelectorStr);

        // will execute _this.deferr4onChangeByOption.done().
        _this.deferr4onChangeByOption.resolve(callback, player_id);
    }

}; // EOF changeableTable.onChangeByOption

/**
 * merge teo divs with jquery
 * Ref. to https://stackoverflow.com/a/31508099
 * @param {string} selectorStr The selector string for merged.
 */
changeableTable.mergeDivs = function(selectorStr){
    var combinedHTML = "";
    $(selectorStr).each(function () {
        combinedHTML += $(this).html();
    });

    $(selectorStr).not(':first').remove();
    $(selectorStr).html(combinedHTML);
};


/**
 * for Debug, Check which file the ajax UI is from?
 */
changeableTable.displayLoadFileUnder$El = function(the$El){
    var _this = this;

    var fileInfo$El = the$El.find('[data-file-info]');
    if( fileInfo$El.length > 0
        && _this.isDebugEnv()
    ){
        var logs = [];
        logs.push('load '+ fileInfo$El.data('file-info') );
        if(fileInfo$El.data('datatable-selector')){
            logs.push('datatable: '+ fileInfo$El.data('datatable-selector') );
        }
        console.log.apply(null, logs);
    }
}; // EOF changeableTable.displayLoadFileUnder$El

/**
 * Detect debug env.
 * @return {boolean} true means debug Env else other.
 */
changeableTable.isDebugEnv = function(){
    return (    window.location.host.toString().indexOf('staging') > -1
            || window.location.host.toString().indexOf('local') > -1 );
}; // EOF changeableTable.isDebugEnv

// https://stackoverflow.com/a/9614662
changeableTable.visible = function(the$El) {
    return the$El.css('visibility', 'visible');
};

changeableTable.invisible = function(the$El) {
    return the$El.css('visibility', 'hidden');
};

changeableTable.visibilityToggle = function(the$El) {
    return the$El.css('visibility', function(i, visibility) {
        return (visibility == 'visible') ? 'hidden' : 'visible';
    });
};
