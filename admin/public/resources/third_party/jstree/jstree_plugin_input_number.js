(function ($, undefined) {
    "use strict";
    var inp = document.createElement('INPUT');
    inp.setAttribute('type','text');
    inp.className = "jstree-inp";
    // $(inp).change(function(){
    //     $(this);
    // });
    // inp.style.width = '40px';

    $.jstree.defaults.input_number = {
        form_sel: null
    },


    $.jstree.plugins.input_number = function (options, parent) {

        this.numberMap={};

        this.get_number_map = function () {
            return this.numberMap;
        };

        this.generate_number_fields = function () {
            // utils.safelog(this.settings);
            var form_obj=$(this.settings.input_number.form_sel);
            $.each(this.numberMap, function(key, value){
                var number_fld=form_obj.find('[name='+key+']');
                if(number_fld.length>0){
                    //update
                    number_fld.val(value);
                }else{
                    form_obj.append('<input type="hidden" name="'+key+'" value="'+value+'">');
                }
            });

            return this.numberMap;
        };

        this.bind = function () {
            parent.bind.call(this);

            this.element
                .on('model.jstree', $.proxy(function (e, data) {
                    var m = this._model.data,
                        p = m[data.parent],
                        dpc = data.nodes,
                        i, j;
                    for(i = 0, j = dpc.length; i < j; i++) {
                        var id=m[dpc[i]]['id'];
                        var number=m[dpc[i]]['original']['number'];
                        if(number){
                            this.numberMap['per_'+id]=number;
                        }
                        // utils.safelog(m[dpc[i]]);
                        // m[dpc[i]].state.checked = m[dpc[i]].state.checked || (m[dpc[i]].original && m[dpc[i]].original.state && m[dpc[i]].original.state.checked);
                        // if(m[dpc[i]].state.checked) {
                        //     this._data.checkbox.selected.push(dpc[i]);
                        // }
                    }
                }, this));

            this.element
                .on("change.jstree", ".jstree-inp", $.proxy(function (e) {
                        // do something with $(e.target).val()
                        // console.log($(e.target).val());
                    // utils.safelog(e);
                    }, this));
        };
        this.teardown = function () {
            if(this.settings.questionmark) {
                this.element.find(".jstree-inp").remove();
            }
            parent.teardown.call(this);
        };
        this.redraw_node = function(obj, deep, callback) {
            obj = parent.redraw_node.call(this, obj, deep, callback);
            if(obj) {
              var node_id=obj.id;
              var node=this.get_node(obj.id);
              if(node.original['set_number']){

                var self=this;
                var tmp = inp.cloneNode(true);

                // utils.safelog(node);

                // $(tmp).attr('name',"per_"+obj.id)
                $(tmp).attr('id',"per_"+obj.id).val(node.original['number']).keyup(function(){
                    var id=$(this).attr('id');
                    var number=$(this).val();

                    //set data back
                    node.original['number']=number;
                    self.numberMap[id]=number;

                    // utils.safelog(self.numberMap[id]);
                    // utils.safelog(node);

                });

                // console.log(obj);
                // console.log(node.original);
                // console.log(deep);
                // obj.insertBefore(tmp, obj.childNodes[1].nextSibling);
                $(obj).append(tmp);
                if(node.original['percentage']){
                  $(obj).append('%');
                }
              }

            }
            return obj;
        };
    };
})(jQuery);