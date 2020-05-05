/**
 * This pagination plug-in provides a `dt-tag select` menu with the list of the page
 * numbers that are available for viewing.
 *
 *  @name Select list
 *  @summary Show a `dt-tag select` list of pages the user can pick from.
 *  @author _jneilliii_
 *
 *  @example
 *    $(document).ready(function() {
 *        $('#example').dataTable( {
 *            "sPaginationType": "listbox"
 *        } );
 *    } );
 */
(function () {
	var span_total_page_class_name = 'paginate_total_page';
	var select_class_name = 'paginate_select';

	$.fn.dataTableExt.oPagination.listbox = {
		/*
		* Function: oPagination.listbox.fnInit
		* Purpose:  Initalise dom elements required for pagination with listbox input
		* Returns:  -
		* Inputs:   object:oSettings - dataTables settings object
		*             node:nPaging - the DIV which contains this pagination control
		*             function:fnCallbackDraw - draw function which must be called on update
		*/
		"fnInit": function (oSettings, nPaging, fnCallbackDraw) {
			var oLang = oSettings.oLanguage;
			var nInput = document.createElement('select');

			var template = oLang.oPaginate.select.template
				.replace(/_TOTAL_/g, '<span class="' + span_total_page_class_name + '"></span>')
				.replace(/_SELECT_/g, '<select class="' + select_class_name + '"></select>')
			;

			$(nPaging).empty().html(template);

			var nInput = $(nPaging).find('.paginate_select');

			if (oSettings.sTableId !== '') {
				$(nPaging).attr('id', oSettings.sTableId + '_paginate');
			}

			nInput.css('display', "inline");

			// nPaging.appendChild(nPage);
			// nPaging.appendChild(nInput);
			// nPaging.appendChild(nOf);

			nInput.on('change', function (e) { // Set DataTables page property and redraw the grid on listbox change event.
				window.scroll(0,0); //scroll to top of page
				if (this.value === "" || this.value.match(/[^0-9]/)) { /* Nothing entered or non-numeric character */
					return;
				}
				var iNewStart = oSettings._iDisplayLength * (this.value - 1);
				if (iNewStart > oSettings.fnRecordsDisplay()) { /* Display overrun */
					oSettings._iDisplayStart = (Math.ceil((oSettings.fnRecordsDisplay() - 1) / oSettings._iDisplayLength) - 1) * oSettings._iDisplayLength;
					fnCallbackDraw(oSettings);
					return;
				}
				oSettings._iDisplayStart = iNewStart;
				fnCallbackDraw(oSettings);
			}); /* Take the brutal approach to cancelling text selection */

			$('span', nPaging).bind('mousedown', function () {
				return false;
			});
			$('span', nPaging).bind('selectstart', function () {
				return false;
			});
		},

		/*
		* Function: oPagination.listbox.fnUpdate
		* Purpose:  Update the listbox element
		* Returns:  -
		* Inputs:   object:oSettings - dataTables settings object
		*             function:fnCallbackDraw - draw function which must be called on update
		*/
		"fnUpdate": function (oSettings, fnCallbackDraw) {
			if (!oSettings.aanFeatures.p) {
				return;
			}
			oSettings._iDisplayLength = parseInt(oSettings._iDisplayLength);
			var iPages = Math.ceil((oSettings.fnRecordsDisplay()) / oSettings._iDisplayLength);
			var iCurrentPage = Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength) + 1; /* Loop over each instance of the pager */
			var nPaging = oSettings.aanFeatures.p;
			for (var i = 0, iLen = nPaging.length; i < iLen; i++) {
				var inputs = nPaging[i].getElementsByTagName('select');
				var elSel = inputs[0];
				if(elSel.options.length != iPages) {
					elSel.options.length = 0; //clear the listbox contents
					for (var j = 0; j < iPages; j++) { //add the pages
						var oOption = document.createElement('option');
						oOption.text = j + 1;
						oOption.value = j + 1;
						try {
							elSel.add(oOption, null); // standards compliant; doesn't work in IE
						} catch (ex) {
							elSel.add(oOption); // IE only
						}
					}
					$('.' + span_total_page_class_name, nPaging).text(iPages);
				}
				elSel.value = iCurrentPage;
			}
		}
	};
})();