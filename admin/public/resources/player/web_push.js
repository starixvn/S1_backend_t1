//web push message
define('web_push',['web_socket'], function(web_socket) {

    var init = function() {
        var self = this;
        $(function(){

            utils.safelog('url:' + utils.getAssetUrl('WebSocketMain.swf'));
            window.WEB_SOCKET_SWF_LOCATION = utils.getAssetUrl('WebSocketMain.swf');

            self.conn = new window.WebSocket("ws://" + variables.websocket_server);

            self.conn.onopen = function(e) {
                utils.safelog("Connection established!");
                //login by token
                // conn.send(JSON.stringify({type: "login", token: variables.token}));
                //register player
                if (variables.logged) {
                    self.loginPlayer();
                }
                if (variables.debugLog) {
                    // self.test();
                }
            };

            self.conn.onmessage = function(e) {
                var json = e.data;
                utils.safelog(json);
                if (json) {
                    var data = JSON.parse(json);
                    if (data['success']) {
                        if (data['type'] == 'player:login') {
                            //nothing
                            utils.safelog('login webpush');
                            // utils.buildPushMessage('login success');
                        } else if (data['type'] == 'player:logout') {
                            utils.buildPushMessage('logout success');
                        } else if (data['type'] == 'player:query_job_result') {
                            self.processJobResult(data['result']);
                        } else if (data['type'] == 'player:query_message') {
                            self.processMessage(data['result']);
                        } else if (data['type'] == 'system:push_queue_result') {
                            self.processJobResult(data['result']);
                        }
                    } else {
                        self.failedResult(data['type'], data['msg_id']);
                    }
                }
            };
            //init unread queue results if exists
            self.queryQueueResults();
        });
    };

    var failedResult = function(type, msgId) {
        //load msg id for type
        // utils.safelog("type:" + type + " msgId:" + msgId);
        utils.buildErrorMessage(msgId);
    };

    var processJobResult = function(rlt) {
        utils.safelog(rlt);
        // utils.buildPushMessage(rlt);
        if(rlt && rlt['job_info']){
          var funcName=rlt['job_info']['func_name'];
          this[funcName+'Result'](rlt['job_result'], rlt['job_info']);
        }
    };

    var queryPlayerBalanceResult=function(jobResult, jobInfo){
      //get state for UI
      if(jobResult['success']){
          // jobInfo['state'];
          var balance=jobResult['balance'];
          var systemId=jobInfo['system_id'];
          var state=jobInfo['state'];
          var showBalance='#_async_balance_'+systemId;
          if(state){
            state=JSON.parse(state);
            if(state){
              showBalance=state['show'];
              var afterFunc=state['after'];
              // utils.safelog(afterFunc);
              // utils.safelog(window[afterFunc]);
              if(afterFunc && window[afterFunc]){
                window[afterFunc](jobResult, jobInfo, state);
              }
            }
          }
          //format number
          $(showBalance).html(utils.formatCurrency(jobResult['balance']));
      }else{
        //error
      }
    };

    var processMessage = function(rlt) {
        // utils.safelog(rlt);
        utils.buildPushMessage(rlt);
    };

    var test = function() {
        //send query job token
        this.sendMessage("player:query_job_result", {
            job_token: '78b1b6c7d109dcdd8edab86861c74e20'
        });
        this.sendMessage("player:query_message");
        // this.conn.send(JSON.stringify({type: "player:query_job_result", token: variables.token, job_token: '78b1b6c7d109dcdd8edab86861c74e20'}));
        // this.conn.send(JSON.stringify({type: "player:query_message", token: variables.token}));
    };

    var loginPlayer = function() {
        this.sendMessage("player:login");
    };
    var logoutPlayer = function() {
        this.sendMessage("player:logout");
    };

    var sendMessage = function(type, arr) {
        if(this.conn){
            if (!arr) {
                arr = {};
            }
            arr['type'] = type;
            arr['token'] = variables.token;
            this.conn.send(JSON.stringify(arr));
        }
    };

    var queryQueueResults = function() {
        var self = this;
        if(variables.logged){
            $(function(){
                utils.getJSONP(utils.getApiUrl('query_queue_results/' + variables.role),null, function(data) {
                    utils.safelog(data);
                    //process job result
                    if (data && _.isArray(data)) {
                        for (var i = 0; i < data.length; i++) {
                            var rlt = data[i];
                            self.processJobResult(rlt);
                        }
                    }
                },null);
            });
        }
    };

    return {
        init: init,
        sendMessage: sendMessage,
        loginPlayer: loginPlayer,
        logoutPlayer: logoutPlayer,
        processJobResult: processJobResult,
        queryPlayerBalanceResult: queryPlayerBalanceResult,
        processMessage: processMessage,
        queryQueueResults: queryQueueResults,
        failedResult: failedResult,
        test: test,
        conn: null
    };

});
