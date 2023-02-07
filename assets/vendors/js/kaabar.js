// roundOff function
function roundOff(n, d) {
	num    = n * Math.pow(10, d);
	num    = Math.round(num);
	strnum = String(num);
	len    = strnum.length;
	if (d === 0)
		return strnum.substring(0, len-d);
	else
		return strnum.substring(0, len-d) + '.' + strnum.substring(len-d, len);
}

function combineSearch() {

	var txtData = [];
	var txtFilterData = [];
	$('.AdvancedSearch').each(function() {
		if ($(this).val())
			txtData.push($(this).attr('name')+':'+$(this).val());
	});
	$('.FilterSearch').each(function() {
		if ($(this).val())
			txtFilterData.push($(this).attr('name')+':'+$(this).val());
	});

	$('input#advanceForm').val(txtData.join(' '));
	$('input#advanceFilterForm').val(txtFilterData.join(' '));

	$('#SearchButton').click();
}

function clearSearch() {
	$('input#advanceForm').val('');
	$('input#advanceFilterForm').val('');
	$('input#Search').val('');
	
	$('#SearchButton').click();
}

function filter(search) {
	var v = $('input#Search').val();
	$('input#Search').val(v+search);
	$('#SearchButton').click();
}

function filterRemove(search) {
	var v = $('input#Search').val();
	var newString = v.replace(search,'');
	$('input#Search').val(newString);
	$('#SearchButton').click();
}

function split(val) {
	return val.split(/ \s*/);
}

function extractLast(term) {
	return split(term).pop();
}


function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function isNumberDot(evt) {
    var theEvent = evt || window.event;
	var key = theEvent.keyCode || theEvent.which;
	key = String.fromCharCode( key );
	var regex = /^[0-9.]+$/;
	if( !regex.test(key) ) {
	theEvent.returnValue = false;
	if(theEvent.preventDefault) theEvent.preventDefault();
	}
}

function resizeFixedHeader(top_margin, fixedToolbar, fixedHeader, table) {
	if ($(fixedHeader).width() != $(table).width()) {
		$(table).find('thead tr').children().each(function(i, e) {
			$($(fixedHeader).find('thead tr').children()[i]).width($(e).width());
		});
		$(fixedHeader).width($(table).width());
	}
	
	$(fixedToolbar).css({
		left: ($(table).offset().left - $(window).scrollLeft()) + 'px'
	});
	if ($(this).scrollTop() > top_margin) {
		$(fixedToolbar).addClass('fixedTop show');
		$(fixedHeader).removeClass('hide');
	}
	else {
		$(fixedToolbar).removeClass('fixedTop show');
		$(fixedHeader).addClass('hide');
	}
}

function fixedHeader(top_margin, fixedHeader, table) {
	if ($(fixedHeader).width() != $(table).width()) {
		$(table).find('thead tr').children().each(function(i, e) {
			$($(fixedHeader).find('thead tr').children()[i]).width($(e).width());
		});
		$(fixedHeader).width($(table).width());
	}

	// $(fixedHeader).css({
	// 	left: ($(table).offset().left - $(window).scrollLeft()) + 'px'
	// });
	if ($(this).scrollTop() > top_margin) {
		$(fixedHeader).addClass('fixedTop').removeClass('hide');
	}
	else {
		$(fixedHeader).removeClass('fixedTop').addClass('hide');
	}
}

function ISO6346Check(cntrNum) {
	var num = 0;
	var charCode = "0123456789A?BCDEFGHIJK?LMNOPQRSTU?VWXYZ";
	if (!cntrNum || cntrNum.length != 11) {
		return false;
	}
	cntrNum = cntrNum.toUpperCase();
	for (var i = 0; i < 10; i++) {
		var chr = cntrNum.substring(i, i + 1);
		var idx = chr == '?' ? -1 : charCode.indexOf(chr);
		if (idx < 0) {
			return false;
		}
		idx = idx * Math.pow(2, i);
		num += idx;
	}
	num = (num % 11) % 10;
	return parseInt(cntrNum.substring(10, 11)) == num;
}

function checkGstn(gst) {
	var factor = 2, sum = 0, checkCodePoint = 0, i, j, digit, mod, codePoint, cpChars, inputChars;
	cpChars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	inputChars = gst.trim().toUpperCase();

	mod = cpChars.length;
	for (i = inputChars.length - 1; i >= 0; i = i - 1) {
		codePoint = -1;
		for (j = 0; j < cpChars.length; j = j + 1) {
			if (cpChars[j] === inputChars[i]) {
				codePoint = j;
			}
		}

		digit = factor * codePoint;
		factor = (factor === 2) ? 1 : 2;
		digit = (digit / mod) + (digit % mod);
		sum += Math.floor(digit);
	}
	checkCodePoint = ((mod - (sum % mod)) % mod);

	return gst + cpChars[checkCodePoint];
};

$(document).ready(function() {
	(function ( $ ) {

		if (jQuery.isFunction(jQuery.fn.selectize)) {
			// Autocomplete via Selectize
			$.fn.kaabar_selectize = function(options) {
				var settings = $.extend({
					valueField: 'id',
					labelField: 'name',
					searchField: 'name',
					create: false,
					handlebarID: null
				}, options);

				$(this).selectize({
					valueField:  settings.valueField,
					labelField:  settings.labelField,
					searchField: settings.searchField,
					options:  settings.options,
					create:  settings.create,
					render: {
						option: function(item, escape) {
							if (settings.handlebarID) {
								var source   = $(settings.handlebarID).html();
								var template = Handlebars.compile(source);
								var html     = template(item);
								return '<div>' + html + '</div>';
							}
							else {
								return '<div>' + escape(item.name) + '</div>';
							}
						}
					},
					load: function(query, callback) {
						if (!query.length) return callback();
						$.ajax({
							url: settings.source+'/'+encodeURIComponent(query),
							type: 'GET',
							dataType: 'json',
							error: function() {
								callback();
							},
							success: function(res) {
								callback(res);
							}
						});
					}
				});
			};
		}

		if (jQuery.isFunction(jQuery.fn.typeahead)) {
			// Typeahead
			$.fn.kaabar_typeahead_old = function(options) {
				var settings = $.extend({
					hint: true,
					highlight: true,
					minLength: 1,
					name: '',
					displayKey: 'value',
					url: '',
					type: 'post',
					extra_data: {},
					empty: '<div class="tt-no-result">Unable to find any results that match the current query</div>',
					suggestion: '<p>{{name}}</p>'
				}, options);

				$(this).typeahead(null, {
					name: settings.name,
					hint: settings.hint,
					highlight: settings.highlight,
					minLength: settings.minLength,
					display: settings.displayKey,
					source: function(query, process) {
						$.ajax({ 
							url: settings.url,
							type: settings.type,
							dataType: 'json',
							data: $.extend({ term: query }, settings.extra_data),
							success: function (result) {
								return process(result);
							}
						});
					},
					templates: {
						empty: [settings.empty],
						suggestion: Handlebars.compile(settings.suggestion)
					}
				});
			};

			$.fn.kaabar_typeahead = function(options) {
				var settings = $.extend({
					hint: false,
					highlight: true,
					minLength: 1,
					name: '',
					displayKey: 'value',
					url: '',
					type: 'post',
					extra_data: {},
					empty: '<div class="tt-no-result">Unable to find any results that match the current query</div>',
					suggestion: '<p>{{name}}</p>'
				}, options);

				$(this).typeahead({
					hint: settings.hint,
					highlight: settings.highlight,
					minLength: settings.minLength
				}, {
					name: settings.name,
					displayKey: settings.displayKey,
					source: function(query, syncResults, asyncResults) {
						return $.ajax({ 
							url: settings.url,
							type: settings.type,
							dataType: 'json',
							data: $.extend({ term: query }, settings.extra_data),
							success: function (result) {
								asyncResults(result);
							}
						});
					},
					templates: {
						empty: [settings.empty],
						suggestion: Handlebars.compile(settings.suggestion)
					}
				});
			};


			$.fn.kaabar_typeahead_complex = function(options) {
				var settings = $.extend({
					hint: false,
					highlight: true,
					minLength: 1,
					name: '',
					displayKey: 'name',
					url: '',
					type: 'post',
					extra_data: {},
					empty: '<div class="tt-no-result">Unable to find any results that match the current query</div>',
					keep_value: false,
					suggestion: '<p>{{name}}</p>',
					fields: [{id: '#ID', field: 'id', type: 'input'}]
				}, options);

				$(this).typeahead({
					hint: settings.hint,
					highlight: settings.highlight,
					minLength: settings.minLength
				}, {
					name: settings.name,
					displayKey: settings.displayKey,
					source: function(query, process) {
						return $.ajax({ 
							url: settings.url,
							type: settings.type,
							dataType: 'json',
							data: $.extend({ term: query }, settings.extra_data),
							success: function (result) {
								process(result);
							}
						});
					},
					templates: {
						empty: function(context) {
							if (! settings.keep_value) {
								$.each(settings.fields, function(k, v) {
									$(v.id).val((v.type == 'Numeric' ? 0 : ''));
								});
							}
							return settings.empty;
						},
						suggestion: Handlebars.compile(settings.suggestion)
					}
				}).on('typeahead:selected', function(obj, datum) {
					$.each(settings.fields, function(k, v) {
						if (v.type == 'typeahead')
							$(v.id).typeahead('val', datum[v.field]);
						else if (v.type == 'text')
							$(v.id).text(datum[v.field]);
						else
							$(v.id).val(datum[v.field]);
					});
				});
			}
		}

		if (jQuery.isFunction(jQuery.fn.autocomplete)) {
			// Autocomplete Template
			$.fn.kaabar_autocomplete = function(options) {
				var settings = $.extend({
					appendTo: null,
					source: "",
					minLength: 1,
					handlebarID: null,
					otherValue: null,
					alertText: 'Result'
				}, options);

				$(this).autocomplete({
					appendTo: settings.appendTo,
					source: settings.source,
					minLength: settings.minLength,
					focus: function(event, ui) {
						$(this).val(ui.item.name);
						return false;
					},
					select: function(event, ui) {
						$(this).val(ui.item.name);
						
						var attr_id = $(this).nextAll('input[title="id"]');
						var attr_category = $(this).nextAll('input[title="category"]');
						
						// For some browsers, `attr` is undefined; for others,
						// `attr` is false.  Check for both.
						if (typeof attr_id !== 'undefined' && attr_id !== false)
							$(this).nextAll('input[title="id"]').val(ui.item.id);
						
						if (typeof attr_category !== 'undefined' && attr_category !== false)
							$(this).nextAll('input[title="category"]').val(ui.item.category);
						
						if(settings.otherValue && ui.item.id == 1){
							$(settings.otherValue+' option[value="No"]').removeAttr("selected");
							$(settings.otherValue+' option[value="Yes"]').attr("selected", "selected");
						}
						else{
							$(settings.otherValue+' option[value="Yes"]').removeAttr("selected");
							$(settings.otherValue+' option[value="No"]').attr("selected", "selected");
						}
						return false;
					},
					change: function(event, ui) {
						if ($(this).val() === '')
							$(this).nextAll('input').val(0);
						return false;
					},
					response: function(event, ui) {
						if (ui.content.length === 0) {
							$(this).nextAll('input').val(0);
							$(this).val('');

							new PNotify({
								text: '<i class=\"fas fa-info-circle\"></i> No '+settings.alertText+' Found',
								type: 'error',
								delay: 2000,
								nonblock: {
									nonblock: true,
									nonblock_opacity: .2
								}
							});

						}
					}
				}).each(function() {
					$(this).data('ui-autocomplete')._renderItem = function(ul, item) {
						if (settings.handlebarID) {
							var source   = $(settings.handlebarID).html();
							var template = Handlebars.compile(source);
							var html     = template(item);
							return $(html).appendTo(ul);
						}
						else {
							return $('<li></li>')
								.data('item.autocomplete', item)
								.append('<a>' + item.name + '</a>')
								.appendTo(ul);
						}
					};
				});
			};

			$.fn.cskaabar_autocomplete = function(options) {
				var settings = $.extend({
					appendTo: null,
					source: "",
					minLength: 1,
					handlebarID: null,
					otherValue: null
				}, options);

				$(this).autocomplete({
					appendTo: settings.appendTo,
					source: settings.source,
					minLength: settings.minLength,
					focus: function(event, ui) {
						$(this).val(ui.item.name);
						return false;
					},
					select: function(event, ui) {
						$(this).val(ui.item.name);
						$(this).nextAll('input').val(ui.item.id);
						if(settings.otherValue && ui.item.id == 1){
							$(settings.otherValue+' option[value="No"]').removeAttr("selected");
							$(settings.otherValue+' option[value="Yes"]').attr("selected", "selected");
						}
						else{
							$(settings.otherValue+' option[value="Yes"]').removeAttr("selected");
							$(settings.otherValue+' option[value="No"]').attr("selected", "selected");
						}
						return false;
					},
					change: function(event, ui) {
						if ($(this).val() === '')
							$(this).nextAll('input').val(0);
						return false;
					},
					response: function(event, ui) {
						if (ui.content.length === 0) {
							$(this).nextAll('input').val(0);
							$(this).val('');
						}
					}
				}).each(function() {
					$(this).data('ui-autocomplete')._renderItem = function(ul, item) {
						if (settings.handlebarID) {
							var source   = $(settings.handlebarID).html();
							var template = Handlebars.compile(source);
							var html     = template(item);
							return $(html).appendTo(ul);
						}
						else {
							return $('<li></li>')
								.data('item.autocomplete', item)
								.append('<a>' + item.name + '</a>')
								.appendTo(ul);
						}
					};
				});
			};

			///////////////for not delete if not respond
			$.fn.kaabar_autocomplete_full = function(options) {
				var settings = $.extend({
					appendTo: null,
					source: "",
					minLength: 1,
					handlebarID: null
				}, options);

				$(this).autocomplete({
					appendTo: settings.appendTo,
					source: settings.source,
					minLength: settings.minLength,
					focus: function(event, ui) {
						$(this).val(ui.item.name);
						return false;
					},
					select: function(event, ui) {
						$(this).prevAll('input').val(ui.item.id);
						$(this).val(ui.item.name);
						return false;
					}
				}).each(function() {
					$(this).data('ui-autocomplete')._renderItem = function(ul, item) {
						if (settings.handlebarID) {
							var source   = $(settings.handlebarID).html();
							var template = Handlebars.compile(source);
							var html     = template(item);
							return $(html).appendTo(ul);
						}
						else {
							return $('<li></li>')
								.data('item.autocomplete', item)
								.append('<a>' + item.name + '</a>')
								.appendTo(ul);
						}
					};
				});
			};

			// Autocomplete Multicolumn
			$.fn.kaabar_multicomplete = function(options) {
				var settings = $.extend({
					appendTo: null,
					source: "",
					minLength: 1,
					handlebarID: null
				}, options);

				var methods = {
					data: function(items) {
						$.each(items, function(index, val) {
							$('tr.TemplateRow').find('input[data-value="'+index+'"]').val(val);
						});
					}
				};

				$(this).autocomplete({
					appendTo: settings.appendTo,
					source: settings.source,
					minLength: settings.minLength,
					focus: function(event, ui) {
						$(this).prevAll('input').val(ui.item.id);
						$(this).val(ui.item.name);
						methods.data(ui.item);
						return false;
					},
					select: function(event, ui) {
						$(this).prevAll('input').val(ui.item.id);
						$(this).val(ui.item.name);
						methods.data(ui.item);
						return false;
					},
					change: function(event, ui) {
						if ($(this).val() === '')
							$(this).prevAll('input').val(0);
						return false;
					},
					response: function(event, ui) {
						if (ui.content.length === 0) {
							$(this).prevAll('input').val(0);
							$(this).val('');
						}
					}
				}).each(function() {
					$(this).data('ui-autocomplete')._renderItem = function(ul, item) {
						if (settings.handlebarID) {
							var source   = $(settings.handlebarID).html();
							var template = Handlebars.compile(source);
							var html     = template(item);
							return $(html).appendTo(ul);
						}
						else {
							return $('<li></li>')
								.data('item.autocomplete', item)
								.append('<a>' + item.name + '</a>')
								.appendTo(ul);
						}
					};
				});
			};
		}

		// Popupwindow
		$.fn.popupWindow = function(instanceSettings){
			return this.each(function(){
				$(this).click(function(){
					$.fn.popupWindow.defaultSettings = {
						centerBrowser:0, // center window over browser window? {1 (YES) or 0 (NO)}. overrides top and left
						centerScreen:0,  // center window over entire screen? {1 (YES) or 0 (NO)}. overrides top and left
						height:500,      // sets the height in pixels of the window.
						left:0,          // left position when the window appears.
						location:0,      // determines whether the address bar is displayed {1 (YES) or 0 (NO)}.
						menubar:0,       // determines whether the menu bar is displayed {1 (YES) or 0 (NO)}.
						resizable:0,     // whether the window can be resized {1 (YES) or 0 (NO)}. Can also be overloaded using resizable.
						scrollbars:0,    // determines whether scrollbars appear on the window {1 (YES) or 0 (NO)}.
						status:0,        // whether a status line appears at the bottom of the window {1 (YES) or 0 (NO)}.
						width:500,       // sets the width in pixels of the window.
						windowName:null, // name of window set from the name attribute of the element that invokes the click
						windowURL:null,  // url used for the popup
						top:0,           // top position when the window appears.
						toolbar:0        // determines whether a toolbar (includes the forward and back buttons) is displayed {1 (YES) or 0 (NO)}.
					};
					
					settings = $.extend({}, $.fn.popupWindow.defaultSettings, instanceSettings || {});
					
					var windowFeatures = 'height=' + settings.height +
										 ',width=' + settings.width +
										 ',toolbar=' + settings.toolbar +
										 ',scrollbars=' + settings.scrollbars +
										 ',status=' + settings.status + 
										 ',resizable=' + settings.resizable +
										 ',location=' + settings.location +
										 ',menuBar=' + settings.menubar;

					settings.windowName = this.name || settings.windowName;
					settings.windowURL = this.href || settings.windowURL;
					var centeredY,centeredX;
				
					if (settings.centerBrowser) {
						if ($.browser.msie) { //hacked together for IE browsers
							centeredY = (window.screenTop - 120) + ((((document.documentElement.clientHeight + 120)/2) - (settings.height/2)));
							centeredX = window.screenLeft + ((((document.body.offsetWidth + 20)/2) - (settings.width/2)));
						}
						else {
							centeredY = window.screenY + (((window.outerHeight/2) - (settings.height/2)));
							centeredX = window.screenX + (((window.outerWidth/2) - (settings.width/2)));
						}
						window.open(settings.windowURL, settings.windowName, windowFeatures+',left=' + centeredX +',top=' + centeredY).focus();
					}
					else if (settings.centerScreen) {
						centeredY = (screen.height - settings.height)/2;
						centeredX = (screen.width - settings.width)/2;
						window.open(settings.windowURL, settings.windowName, windowFeatures+',left=' + centeredX +',top=' + centeredY).focus();
					}
					else {
						window.open(settings.windowURL, settings.windowName, windowFeatures+',left=' + settings.left +',top=' + settings.top).focus();	
					}
					return false;
				});
			});	
		};
	}( jQuery ));

	$('.Popup').popupWindow({
		menubar: 1,
		scrollbars: 1,
		height: 768,
		width: 1024
	});

	
	Date.format = 'dd-mm-yyyy';

	$('.disabled').on('click', function(e) {
		e.preventDefault();
	});
	
	$('#Update:not(.onEventAttached)').addClass('onEventAttached').on('click', function() {
		$('form#MainForm').submit();
	});

	$('table.table-rowselect').on('click', 'tbody tr td', function() {
		if ($(this).children('a').size() == 1) {
			window.location = $(this).children('a').attr('href');
		}
		else if ($(this).children('a').size() === 0 &&
			$(this).parents('tr').children('td').children('a:first').size() === 1) {
			window.location = $(this).parents('tr').children('td').children('a:first').attr('href');
		}
	});

	// $('td.ignoreClicks').off('click');

	setTimeout("$('.Focus:first').focus().select();", 0);

	$('.SearchTags').on('click', function(){
		$('#Search').focus();
		if ($('#Search').val().length > 0)
			$('#Search').val($('#Search').val() + ' ' + $(this).data('field'));
		else
			$('#Search').val($(this).data('field'));
	});

	if (window.keypress) {
		listener = new window.keypress.Listener();

		listener.simple_combo('alt a', function() {
			url = $('#AddNew').attr('href');
			if (url) window.location = url;
		});

		listener.simple_combo('alt u', function() {
			$('#Update').click();
		});

		// List Pagination
		listener.simple_combo('ctrl up', function() {
			url = $('#FirstPage').attr('href');
			if (url) window.location = url;
		});
		listener.simple_combo('ctrl left', function() {
			url = $('#PrevPage').attr('href');
			if (url) window.location = url;
		});
		listener.simple_combo('ctrl right', function() {
			url = $('#NextPage').attr('href');
			if (url) window.location = url;
		});
		listener.simple_combo('ctrl down', function() {
			url = $('#LastPage').attr('href');
			if (url) window.location = url;
		});
	};

	if (jQuery.isFunction(jQuery.fn.tooltip)) {
		$('body').tooltip({selector: '[rel=tooltip]'});
	}

	if (jQuery.isFunction(jQuery.fn.selectize)) {
		$('.Selectize').selectize({
			plugins: ['remove_button']
		});

		$('.SelectizeKaabar').selectize({
			
		});
	}

	// if (jQuery.isFunction(jQuery.fn.select2)) {
	// 	$.fn.select2.defaults.set( "theme", "bootstrap" );

	// 	$('.Select2').select2({
			
	// 	});
	// }
		
	var parseInputDate = function(inputDate) {
		var day, month, m;
		if (inputDate.length < 10) {
			var s = inputDate.split('-');
			if (s.length == 1) {
				if (isNaN(parseInt(s[0], 10)) || parseInt(s[0], 10) === 0)
					day = 1;
				else
					day = Math.abs(parseInt(s[0], 10));
				m = moment().date(day);
			}
			else if (s.length === 2) {
				if (isNaN(parseInt(s[0], 10)) || parseInt(s[0], 10) === 0)
					day = 1;
				else
					day = Math.abs(parseInt(s[0], 10)) ;

				if (isNaN(parseInt(s[1], 10)) || parseInt(s[1], 10) === 0)
					month = 0;
				else
					month = Math.abs(parseInt(s[1], 10)) - 1;

				m = moment().date(day).month(month);
			}
			else
				m = moment();
		}
		else {
			m = moment(inputDate, 'DD-MM-YYYY');
		}
		return m;
	}

	if (jQuery.isFunction(jQuery.fn.datetimepicker)) {
		// bootstrap-datetimepicker
		$('.DatePicker').datetimepicker({
			format: 'DD-MM-YYYY',
			useCurrent: false,
			calendarWeeks: true,
			showTodayButton: true,
			allowInputToggle: true,
			showOnFocus: false,
			showClose: true,
			parseInputDate: parseInputDate,
		});

		$('.DateTimePicker').datetimepicker({
			format: 'DD-MM-YYYY HH:mm:ss',
			useCurrent: false,
			calendarWeeks: true,
			showTodayButton: true,
			allowInputToggle: true,
			showOnFocus: false,
			showClose: true,
		});
	}

	if (jQuery.isFunction(jQuery.fn.clockpicker)) {
		$('.ClockPicker').clockpicker({
			autoclose: true
		});
	}

	
	// Checkall
	$('table').on('click', 'a.CheckAll', function() {
		var className = $(this).attr('checkbox-class');
		if (! className) {
			className = 'DeleteCheckbox';
		}

		var checked = $(this).parents('table').find('input:visible.'+className).first().prop('checked');
		if(checked)
			$(this).parents('table').find('input:visible.'+className).prop('checked', '').change();
		else
			$(this).parents('table').find('input:visible.'+className).prop("checked", "checked").change();
	});
	

	// Data Entry +/-
	var del_button = '<button type="button" class="btn btn-danger btn-sm DelButton"><i class="icon-minus fa fa-minus"></i></button>';

	function cloneRow(template_row) {

		$new_row = $(template_row).clone();
		$new_row.children('td:last').html(del_button);
		$new_row.removeClass('TemplateRow');
		$new_row.removeClass('d-none');
		$new_row.addClass('lastRowElement');
		$new_row.insertBefore($(template_row));

		$(template_row).find('textarea').each(function(index, el) {
			$new_row.find('textarea').eq(index).val($(this).val());
		});
		
		$(template_row).find('select').each(function(index, el) {
			$new_row.find('select').eq(index).val($(this).val());
		});
		$(template_row).find(':input').not('.Unchanged').each(function() {
			if ($(this).hasClass('tt-input'))
				$(this).typeahead('val', '');
			else
				$(this).val('');
			
		});

		$(template_row).find('input[type="checkbox"]').not('.Unchanged').prop('checked', '');

		$(template_row).find('td.ClearText').each(function() {
			$(this).text('');
		});
		$(template_row).find('.Increment').each(function() {
			if ($(this).hasClass('tt-input'))
				$(this).typeahead('val', (parseInt($(this).val(), 10)+1));
			else
				$(this).val(parseInt($(this).val(), 10)+1);
		});
		$(template_row).find('.DataDefault').each(function() {
			$(this).val($(this).attr('data-default'));
		});



		$(template_row).find(':input.Focus').focus();
	}

	$('table.DataEntry').unbind().on('click', 'button.AddButton', function(e) {

		var submit_row = $(this).hasClass('RowSubmit');
		var clone_row  = true;
		$template_row  = $(this).parents('tr.TemplateRow');

		$template_row.find('.Validate').each(function(index, el) {
			if ($(this).val() === '') {
				//console.log($(this).attr('class'));
				clone_row = false;
				$(this).addClass('is-invalid');
			}
			else {
				$(this).removeClass('is-invalid');
			}
		});

		if (clone_row) {


			cloneRow($template_row);
			if (submit_row === false) {
				e.preventDefault();
			}
		}
		else {
			e.preventDefault();
		}
	});

	$('table.DataEntry').on('click', 'button.DelButton', function() {
		const row = $(this).parents('tr');
		const that = this // here a is change
        var output = true;
        Swal.fire({
        	title: "Are you sure?",
			text: "You won't be able to revert this!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Yes, Delete it...!",
			cancelButtonText: "Cancel",
			buttonsStyling: true
        }).then(function(result) {

        	if (result.dismiss === Swal.DismissReason.cancel) {
        		output = false;
                return false;
        	}
        	else if (result.value) {
				row.remove();		
        	}
        });
	});

	

	if (jQuery.isFunction(jQuery.fn.sortable)) {
		$('.DataEntry.Sortable tbody').sortable({
			cursor: 'move',
			handle: '.SortHandle',
			cancel: '.ui-state-disabled',
			axis: 'y',
			helper: function(e, tr) {
				var $original = tr.children();
				var $helper   = tr.clone();
				$helper.children().each(function(index) {
					$(this).width($original.eq(index).width());
				});
				return $helper;
			},
			stop: function(e, ui) {
				$('td.SortHandle', ui.item.parent()).each(function(i) {
					$(this).parent('tr').find('input:eq(0)').val(i + 1);
					$(this).parent('tr').find('.sr_no').val(i + 1);
				});
			}
		});
	}

	if (typeof Sortable != 'undefined') {
		$('table.Sortable tbody').each(function() {
			Sortable.create(
				$(this)[0],
				{
					animation: 100,
					scroll: true,
					handle: 'td.SortHandle',
					onEnd: function (e) {
						$('td.SortHandle', e.item.parentElement).each(function(i) {
							$(this).parent('tr').find('input:eq(0)').val(i + 1);
							$(this).parent('tr').find('.sr_no').val(i + 1);
						});
					}
				}
			);
		});
	}
	
	$('.DataEntry.Sortable').on('click.sortable mousedown.sortable', 'input', function(ev) {
		ev.target.focus();
	});
});

function dateRangePicker(options) {
	var month = moment().month();

	var settings = $.extend({
		years: [
		(month > 2 ? moment().year() : moment().year()-1),
		(month > 2 ? moment().year()+1 : moment().year())
		],
		from_date: (month > 2 ? moment().date(1).month(3).format('YYYY-MM-DD') : moment().date(1).month(3).year(moment().year()-1).format('YYYY-MM-DD')),
		to_date:   (month > 2 ? moment().date(31).month(2).year(moment().year()+1).format('YYYY-MM-DD') : moment().date(31).month(2).format('YYYY-MM-DD')),
		ranges: { }
	}, options);
	
	if (Object.keys(settings.ranges).length == 0) {
		settings.ranges = {
			'Today':                 [moment(), moment()],
			'Yesterday':             [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			//'Last 7 Days':         [moment().subtract(6, 'days'), moment()],
			//'Last 30 Days':        [moment().subtract(29, 'days'), moment()],
			'This Week':             [moment().startOf('week'), moment().endOf('week')],
			'This Month':            [moment().startOf('month'), moment().endOf('month')],
			'Last Month':            [moment().subtract(1, 'months').startOf('month'), moment().subtract(1, 'months').endOf('month')],
			// 'Last 2 Months':      [moment().subtract(2, 'months').startOf('month'), moment().subtract(1, 'months').endOf('month')],
			'This Year':             [moment(settings.years[0] + '-04-01', 'YYYY-MM-DD'), moment()],
			'Prev. Year':            [moment((settings.years[0] - 1) + '-04-01', 'YYYY-MM-DD'), moment(settings.years[0] + '-03-31', 'YYYY-MM-DD')],
			'Prev. Year till Today': [moment((settings.years[0] - 1) + '-04-01', 'YYYY-MM-DD'), moment()],
			'Q1 (Apr - Jun)':        [moment(settings.years[0] + '-04-01', 'YYYY-MM-DD'), moment(settings.years[0] + '-06-30', 'YYYY-MM-DD')],
			'Q2 (Jul - Sep)':        [moment(settings.years[0] + '-07-01', 'YYYY-MM-DD'), moment(settings.years[0] + '-09-30', 'YYYY-MM-DD')],
			'Q3 (Oct - Dec)':        [moment(settings.years[0] + '-10-01', 'YYYY-MM-DD'), moment(settings.years[0] + '-12-31', 'YYYY-MM-DD')],
			'Q4 (Jan - Mar)':        [moment(settings.years[1] + '-01-01', 'YYYY-MM-DD'), moment(settings.years[1] + '-03-31', 'YYYY-MM-DD')],
			// 'April':              [moment(settings.years[0] + '-04-01', 'YYYY-MM-DD'), moment(settings.years[0] + '-04-30', 'YYYY-MM-DD')],
			// 'May':                [moment(settings.years[0] + '-05-01', 'YYYY-MM-DD'), moment(settings.years[0] + '-05-31', 'YYYY-MM-DD')],
			// 'June':               [moment(settings.years[0] + '-06-01', 'YYYY-MM-DD'), moment(settings.years[0] + '-06-30', 'YYYY-MM-DD')],
			// 'July':               [moment(settings.years[0] + '-07-01', 'YYYY-MM-DD'), moment(settings.years[0] + '-07-31', 'YYYY-MM-DD')],
			// 'August':             [moment(settings.years[0] + '-08-01', 'YYYY-MM-DD'), moment(settings.years[0] + '-08-31', 'YYYY-MM-DD')],
			// 'September':          [moment(settings.years[0] + '-09-01', 'YYYY-MM-DD'), moment(settings.years[0] + '-09-30', 'YYYY-MM-DD')],
			// 'October':            [moment(settings.years[0] + '-10-01', 'YYYY-MM-DD'), moment(settings.years[0] + '-10-30', 'YYYY-MM-DD')],
			// 'November':           [moment(settings.years[0] + '-11-01', 'YYYY-MM-DD'), moment(settings.years[0] + '-11-30', 'YYYY-MM-DD')],
			// 'December':           [moment(settings.years[0] + '-12-01', 'YYYY-MM-DD'), moment(settings.years[0] + '-12-30', 'YYYY-MM-DD')],
			// 'January':            [moment(settings.years[1] + '-01-01', 'YYYY-MM-DD'), moment(settings.years[1] + '-01-31', 'YYYY-MM-DD')],
			// 'February':           [moment(settings.years[1] + '-02-01', 'YYYY-MM-DD'), moment(settings.years[1] + '-02-28', 'YYYY-MM-DD')],
			// 'March':              [moment(settings.years[1] + '-03-01', 'YYYY-MM-DD'), moment(settings.years[1] + '-03-31', 'YYYY-MM-DD')]
		};
	}
	
	if (jQuery.isFunction(jQuery.fn.daterangepicker)) {
		$('#ReportRange').daterangepicker({
			ranges: settings.ranges,
			opens: 'right',
			format: 'DD-MM-YYYY',
			showDropdowns: true,
			separator: ' to ',
			startDate: moment().date(1),
			endDate: moment(),
			minDate: moment((settings.years[0]-1) + '-04-01', 'YYYY-MM-DD'),
			maxDate: moment(settings.years[1] + '-03-31', 'YYYY-MM-DD'),
			locale: {
				format: 'DD-MM-YYYY',
				applyLabel: 'Select',
				fromLabel: 'From',
				toLabel: 'To',
				customRangeLabel: 'Custom Range',
				daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
				monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
				firstDay: 1
			},
			showWeekNumbers: true
		},
		function(start, end) {
			if(start === null) {
				$('#ReportRange span').html(moment(settings.from_date).date(1).format('MMM D, YYYY') + ' - ' + moment().format('MMM D, YYYY'));
				$('#FromDate').val(settings.from_date);
				$('#ToDate').val(settings.to_date);
			}
			else {
				$('#ReportRange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
				$('#FromDate').val(start.format('DD-MM-YYYY'));
				$('#ToDate').val(end.format('DD-MM-YYYY'));
			}
		});

		//Set the initial state of the picker label
		$('#ReportRange span').html(moment(settings.from_date).format('MMM D, YYYY') + ' - ' + moment(settings.to_date).format('MMM D, YYYY'));


		$('#DateRangePicker').daterangepicker({
			ranges: settings.ranges,
			opens: 'right',
			format: 'DD-MM-YYYY',
			showDropdowns: true,
			separator: ' to ',
			startDate: moment().date(1),
			endDate: moment(),
			minDate: moment((settings.years[0]-1) + '-04-01', 'YYYY-MM-DD'),
			maxDate: moment(settings.years[1] + '-03-31', 'YYYY-MM-DD'),
			locale: {
				format: 'DD-MM-YYYY',
				applyLabel: 'Select',
				fromLabel: 'From',
				toLabel: 'To',
				customRangeLabel: 'Custom Range',
				daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
				monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
				firstDay: 1
			},
			showWeekNumbers: true
		},
		function(start, end) {
			if(start === null) {
				$('#DateRangePicker .daterange-custom-display').html(moment(settings.from_date).date(1).format('<i>D</i> <b><i>MMM</i> <i>YYYY</i></b>') + '<em> &#8211; </em>' + moment().format('<i>D</i> <b><i>MMM</i> <i>YYYY</i></b>'));
				$('#FromDate').val(settings.from_date);
				$('#ToDate').val(settings.to_date);
			}
			else {
				$('#DateRangePicker .daterange-custom-display').html(start.format('<i>D</i> <b><i>MMM</i> <i>YYYY</i></b>') + '<em> &#8211; </em>' + end.format('<i>D</i> <b><i>MMM</i> <i>YYYY</i></b>'));
				$('#FromDate').val(start.format('YYYY-MM-DD'));
				$('#ToDate').val(end.format('YYYY-MM-DD'));
			}
		});

		//Set the initial state of the picker label
		$('#DateRangePicker .daterange-custom-display').html(moment(settings.from_date).format('<i>D</i> <b><i>MMM</i> <i>YYYY</i></b>') + '<em> &#8211; </em>' + moment(settings.to_date).format('<i>D</i> <b><i>MMM</i> <i>YYYY</i></b>'));
	}
}

function datetimeRangePicker(options) {
	var month = moment().month();

	var settings = $.extend({
		years: [
		(month > 2 ? moment().year() : moment().year()-1),
		(month > 2 ? moment().year()+1 : moment().year())
		],
		from_date: (month > 2 ? moment().date(1).month(3).format('YYYY-MM-DD HH:mm:ss') : moment().date(1).month(3).year(moment().year()-1).format('YYYY-MM-DD HH:mm:ss')),
		to_date:   (month > 2 ? moment().date(31).month(2).year(moment().year()+1).format('YYYY-MM-DD HH:mm:ss') : moment().date(31).month(2).format('YYYY-MM-DD HH:mm:ss')),
		ranges: { }
	}, options);
	
	if (Object.keys(settings.ranges).length == 0) {
		settings.ranges = {
			'Today':                 [moment(), moment()],
			'Yesterday':             [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'This Week':             [moment().startOf('week'), moment().endOf('week')],
			'This Month':            [moment().startOf('month'), moment().endOf('month')],
			'Last Month':            [moment().subtract(1, 'months').startOf('month'), moment().subtract(1, 'months').endOf('month')],
			'This Year':             [moment(settings.years[0] + '-04-01 00:00:00', 'YYYY-MM-DD hh:mm:ss'), moment()],
			'Prev. Year':            [moment((settings.years[0]-1) + '-04-01 00:00:00', 'YYYY-MM-DD hh:mm:ss'), moment(settings.years[0] + '-03-31 23:59:59', 'YYYY-MM-DD hh:mm:ss')],
			'Prev. Year till Today': [moment((settings.years[0]-1) + '-04-01 00:00:00', 'YYYY-MM-DD hh:mm:ss'), moment()],
			'Q1 (Apr - Jun)':        [moment(settings.years[0] + '-04-01 00:00:00', 'YYYY-MM-DD hh:mm:ss'), moment(settings.years[0] + '-06-30 23:59:59', 'YYYY-MM-DD hh:mm:ss')],
			'Q2 (Jul - Sep)':        [moment(settings.years[0] + '-07-01 00:00:00', 'YYYY-MM-DD hh:mm:ss'), moment(settings.years[0] + '-09-30 23:59:59', 'YYYY-MM-DD hh:mm:ss')],
			'Q3 (Oct - Dec)':        [moment(settings.years[0] + '-10-01 00:00:00', 'YYYY-MM-DD hh:mm:ss'), moment(settings.years[0] + '-12-31 23:59:59', 'YYYY-MM-DD hh:mm:ss')],
			'Q4 (Jan - Mar)':        [moment(settings.years[1] + '-01-01 00:00:00', 'YYYY-MM-DD hh:mm:ss'), moment(settings.years[1] + '-03-31 23:59:59', 'YYYY-MM-DD hh:mm:ss')],
		};
	}
	
	if (jQuery.isFunction(jQuery.fn.daterangepicker)) {
		$('#ReportRange').daterangepicker({
			ranges: settings.ranges,
			opens: 'right',
			format: 'DD-MM-YYYY HH:mm:ss',
			timePicker: true,
			timePicker24Hour: true,
			timePickerSeconds: true,
			timePickerIncrement: 1,
			showDropdowns: true,
			separator: ' - ',
			startDate: moment(settings.from_date, 'YYYY-MM-DD HH:mm:ss'),
			endDate: moment(settings.to_date, 'YYYY-MM-DD HH:mm:ss'),
			minDate: moment((settings.years[0]-1) + '-04-01 0:00:00', 'YYYY-MM-DD HH:mm:ss'),
			maxDate: moment(settings.years[1] + '-03-31 23:59:59', 'YYYY-MM-DD HH:mm:ss'),
			locale: {
				format: 'DD-MM-YYYY',
				applyLabel: 'Select',
				fromLabel: 'From',
				toLabel: 'To',
				customRangeLabel: 'Custom Range',
				daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
				monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
				firstDay: 1
			},
			showWeekNumbers: true
		},
		function(start, end) {
			if(start === null) {
				$('#ReportRange span').html(moment(settings.from_date, 'YYYY-MM-DD HH:mm:ss').subtract(29, 'days').format('MMM D, YYYY HH:mm') + ' - ' + moment().format('MMM D, YYYY HH:mm'));
				$('#FromDate').val(moment(from_date, 'YYYY-MM-DD HH:mm:ss').format('DD-MM-YYYY HH:mm:ss'));
				$('#ToDate').val(moment(to_date, 'YYYY-MM-DD HH:mm:ss').format('DD-MM-YYYY HH:mm:ss'));
			}
			else {
				$('#ReportRange span').html(start.format('MMM D, YYYY HH:mm') + ' - ' + end.format('MMM D, YYYY HH:mm'));
				$('#FromDate').val(start.format('DD-MM-YYYY HH:mm:ss'));
				$('#ToDate').val(end.format('DD-MM-YYYY HH:mm:ss'));
			}
		});

		//Set the initial state of the picker label
		$('#ReportRange span').html(moment(settings.from_date, 'YYYY-MM-DD HH:mm:ss').format('MMM D, YYYY HH:mm') + ' - ' + moment(settings.to_date, 'YYYY-MM-DD HH:mm:ss').format('MMM D, YYYY HH:mm'));


		$('#DateRangePicker').daterangepicker({
			ranges: settings.ranges,
			opens: 'right',
			format: 'DD-MM-YYYY HH:mm:ss',
			showDropdowns: true,
			timePicker: true,
			timePicker24Hour: true,
			timePickerSeconds: true,
			timePickerIncrement: 1,
			separator: ' to ',
			startDate: moment().date(1),
			endDate: moment(),
			minDate: moment((settings.years[0]-1) + '-04-01', 'YYYY-MM-DD HH:mm:ss'),
			maxDate: moment(settings.years[1] + '-03-31', 'YYYY-MM-DD HH:mm:ss'),
			locale: {
				format: 'DD-MM-YYYY',
				applyLabel: 'Select',
				fromLabel: 'From',
				toLabel: 'To',
				customRangeLabel: 'Custom Range',
				daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
				monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
				firstDay: 1
			},
			showWeekNumbers: true
		},
		function(start, end) {
			if(start === null) {
				$('#DateRangePicker .daterange-custom-display').html(moment(settings.from_date).date(1).format('<i>D</i> <b><i>MMM</i> <i>YYYY</i></b> <b><i>HH</i> <i>mm</i></b>') + '<em> &#8211; </em>' + moment().format('<i>D</i> <b><i>MMM</i> <i>YYYY</i></b> <b><i>HH</i> <i>mm</i></b>'));
				$('#FromDate').val(settings.from_date, 'YYYY-MM-DD HH:mm:ss');
				$('#ToDate').val(settings.to_date, 'YYYY-MM-DD HH:mm:ss');
			}
			else {
				$('#DateRangePicker .daterange-custom-display').html(start.format('<i>D</i> <b><i>MMM</i> <i>YYYY</i></b> <b><i>HH</i> <i>mm</i></b>') + '<em> &#8211; </em>' + end.format('<i>D</i> <b><i>MMM</i> <i>YYYY</i></b> <b><i>HH</i> <i>mm</i></b>'));
				$('#FromDate').val(start.format('YYYY-MM-DD HH:mm:ss'));
				$('#ToDate').val(end.format('YYYY-MM-DD HH:mm:ss'));
			}
		});

		//Set the initial state of the picker label
		$('#DateRangePicker .daterange-custom-display').html(moment(settings.from_date).format('<i>D</i> <b><i>MMM</i> <i>YYYY</i></b> <b><i>HH</i> <i>mm</i></b>') + '<em> &#8211; </em>' + moment(settings.to_date).format('<i>D</i> <b><i>MMM</i> <i>YYYY</i></b> <b><i>HH</i> <i>mm</i></b>'));
	}
}