var CommonCategory = {
	msgSubmitConfirmation : '',
	msgDeleteConfirmation : '',
	addModalTitle : '',
	editModalTitle : '',
	msgActiveConfirmation : '',
	msgInactiveConfirmation : '',
	exportPermission : '',
	langExportExcel : '',
	export_excel_on_queue : '',
	submitEntry : function() {
		var self = this;
		if (confirm(self.msgSubmitConfirmation)) {
            $("#form_category").submit();
        }
	},

	modal : function(title) {
		var self = this;
		 $('#mainModalLabel').html(title);
        $('#mainModal').modal('show');
	},

	addNewEntry : function() {
		var self = this;
		self.clearInputFieldsModal();
        self.modal(self.addModalTitle);
	},

	clearInputFieldsModal : function() {
		var self = this;
		$(".clear-fields").val("");
	},

	loadCategoryInfoById : function(id) {
		var self = this;
		self.clearInputFieldsModal();
        $.post('/system_management/getCategoryById/', {'id' : id} ,function(data){
            if(Object.keys(data).length > 0){
                $("#order_by").val(data.order_by);
                $("#category_id").val(data.id);
                $("#category_type").val(data.category_type);
                var category_name = data.category_name.substring(6);
                var parsed = JSON.parse(category_name);

                $("#category_name_english").val(parsed[1]);
                $("#category_name_chinese").val(parsed[2]);
                $("#category_name_indonesian").val(parsed[3]);
                $("#category_name_vietnamese").val(parsed[4]);
                $("#category_name_korean").val(parsed[5]);
                self.modal(self.editModalTitle);
            }
        });
	},

	deleteCategoryById : function(id) {
		var self = this;
		self.clearInputFieldsModal();
		if (confirm(self.msgDeleteConfirmation)) {
            $.post('/system_management/updateStatusCategoryById/', {'id' : id , 'status' : 2} ,function(data){
	            if(Object.keys(data).length > 0){
	                alert(data.msg);
	                location.reload();
	            }
	        });
        }
	},

	activeCategoryById : function(id) {
		var self = this;
		self.clearInputFieldsModal();
		if (confirm(self.msgActiveConfirmation)) {
            $.post('/system_management/updateStatusCategoryById/', {'id' : id , 'status' : 1} ,function(data){
	            if(Object.keys(data).length > 0){
	                alert(data.msg);
	                location.reload();
	            }
	        });
        }
	},

	inactiveCategoryById : function(id) {
		var self = this;
		self.clearInputFieldsModal();
		if (confirm(self.msgInactiveConfirmation)) {
           	$.post('/system_management/updateStatusCategoryById/', {'id' : id , 'status' : 0} ,function(data){
	            if(Object.keys(data).length > 0){
	                alert(data.msg);
	                location.reload();
	            }
	        });
        }
	}
}

$(document).ready(function(){
    var dataTable = $('#myTable').DataTable({

        autoWidth: false,
        searching: false,
        responsive: false,
       // dom: "<'panel-body'<'pull-right'B>l>t<'text-center'r><'panel-body'<'pull-right'p>i>",
        dom: "<'panel-body' <'pull-right'B><'pull-right progress-container'>l><'dt-information-summary1 text-info pull-left' i>t<'panel-body'<'pull-right'p><'dt-information-summary2 text-info pull-left' i>>",
        buttons: [
            {
                extend: 'colvis',
                postfixButtons: [ 'colvisRestore' ],
				className: 'btn-linkwater',
            }
        ],
        order: [[1, 'asc']],

        // SERVER-SIDE PROCESSING
        processing: true,
        serverSide: true,
        ajax: function (data, callback, settings) {
            data.extra_search = $('#search-form').serializeArray();
            $.post(base_url + "api/getCommonCategory", data, function(data) {
                callback(data);
            }, 'json');
        },

    });

});