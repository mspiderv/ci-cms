/**
 * Languages
 */
function __(key)
{
    try
    {
        return languages[cms.admin_lang][key];
    }
    catch(e)
    {
        console.log("Language error:", e);
        return key;
    }
}
/**
 * Global objects
 */
var page = {};
var global = {};
/**
 * Initialization
 */
$(function(){
        // Ajax initialize
        $.ajaxSetup({
            cache: false,
            async: false,
            type: 'POST'
        });
        // Load components
        global.$form = $('#form');
	var page_data = new Array('jqueryui_init', 'form_validation');
	$("*[data-page]").each(function(){
            page_data = page_data.concat($(this).data("page").split(" "));
	});
	var page_data_used = new Array();
	$.each(page_data, function(index, element){
        if(element != '' && $.inArray(element, page_data_used) == -1){
            page_data_used.push(element);
            if(typeof page[element] == "function") page[element]();
            else console.log("Pokus o volanie neexistujúcej funkcie '" + element + "', na základe atribútu 'data-page'.");
        }
	});
	// Remove non JS element
	$('#no-js').remove();
	//jQuery UI Initialization
	global.functions.initJUI();
	// Form Submiting
	$('.form_submit_e').on('click', function(e){
            var href = $(this).attr('href');
            $(this).removeAttr('href');
            if(typeof href != 'undefined' && href.length > 0)
            global.$form.attr('action', global.$form.attr('action') + '?' + cms.url_param + '=' + href);
            global.$form.submit();
	});
        // Protect form from multisubmiting
        global.$form.on('submit', function(e){
            if($(this).hasClass('submited'))
            {
                e.preventDefault();
                return false;
            }
            else
            {
                $(this).addClass('submited');
            }
        });
        // Ctrl + S
        shortcut.add("Ctrl+S",function(){
            global.$form.submit();
        });
        // Key events
        $(window).on('keyup', function(e){
            switch(e.keyCode)
            {
                case $.ui.keyCode.BACKSPACE:
                    if(!$("input, textarea").is(":focus")) javascript:history.back(1);
                    break;
                /*case $.ui.keyCode.ENTER:
                    if($("input.input").is(":focus")) global.$form.submit();
                    break;*/
                case $.ui.keyCode.HOME:
                    window.location = cms.admin;
                    break;
                case $.ui.keyCode.END:
                    window.location = cms.logout;
                    break;
            }
        });
        // Update textarea on CKEditor blur
        CKEDITOR.on('instanceReady', function(){
            for(i in CKEDITOR.instances)
            {
                CKEDITOR.instances[i].ui.editor.on('blur', function(){
                    $(CKEDITOR.instances[i].element.$).val(CKEDITOR.instances[i].getData()).trigger('blur');
                });
            }
        });
	// Protect # hrefs to scroll to the top
	$('a:not(.form_submit_e, .confirm_link)').on('click', function(){
            if($(this).attr('href') == '#') return false;
	});
        // Board initialization
        board.init();
});
/**
 * jQuery UI Initialization
 */
page.jqueryui_init = function()
{
    $('.buttons_wrap .jui_button:first-child').addClass('ui-corner-left');
    $('.buttons_wrap .jui_button:last-child').addClass('ui-corner-right');
    $.extend($.ui.dialog.prototype.options, {
        modal: true,
        show: 'fade',
        hide: 'explode'
    });
}
/**
 * Menu
 */
page.menu = function(){
    $(function(){
        // Sortable main menu items + save order to cookie
        var main_menu = $('#main-menu');
        var main_menu_order = $.cookie("main_menu_order");    
        if(main_menu_order)
        {
            $.each(main_menu_order.split(','), function(i,id){
                $("#" + id).appendTo(main_menu);
            });
        }
        main_menu.sortable({
            items: '> li',
            handle: '.link',
            axis: 'x',
            cursor: 'move',
            update:function(e,ui){
                var main_menu_order = $(this).sortable("toArray").join();
                $.cookie("main_menu_order", main_menu_order);
            }
        });
        // Submenu width
        var submenu_width = 190;
        var submenu_margin = 10;
        $('#main-menu > li .hover').each(function(){
            $(this).width(((submenu_width + submenu_margin) * $(this).children('.col').length) - submenu_margin);
            
        });
    });
};
/**
 * Chosen
 */
page.chosen = function(){
    $(function(){
        var chosen_options = {};
        chosen_options.allow_single_deselect = true;
        $(".chosen").chosen(chosen_options);
    });
}
/**
 * CKEditor
 */
page.ckeditor = function()
{
    $('textarea.ckeditor').each(function(){
        var id = $(this).attr('id');
        if(typeof CKEDITOR.instances[id] == 'undefined') CKEDITOR.replace(id);
    });
}
/**
 * Tabs
 */
global.codemirror_first_tab_show = true;
page.tabs = function(){
    $(function(){
        $('.jui_tabs').tabs({
            cookie: {expires: 30},
            show: function(event, ui){
                if(global.codemirror_first_tab_show)
                {
                    global.codemirror_first_tab_show = false;
                }
                else
                {
                    if(global.codemirror_objects.length > 0)
                    {
                        for(var index in global.codemirror_objects)
                        {
                            global.codemirror_objects[index].refresh();
                        }
                    }
                }
            }
        });
    });
}
/**
 * Sliders
 */
page.slider = function(){
    $(function(){
        $('.jui_slider').each(function(){
            $(this).slider({
                min: $(this).data('min'),
                max: $(this).data('max'),
                step: (typeof $(this).data('step') == 'undefined') ? 1 : $(this).data('step'),
                value: $(':input[name="' + $(this).data('name') + '"]').val(),
                slide: function(event, ui){
                    $(':input.jui_slider_field[name="' + $(this).data('name') + '"]').val(ui.value);
                },
                stop: function(event, ui){
                    $(':input.jui_slider_field[name="' + $(this).data('name') + '"]').trigger('change');
                }
            });
        });
        $('.jui_slider_field').on('keyup', function(){
            $('.jui_slider[data-name="' + $(this).attr("name") + '"]').slider({
                value: $(this).val()
            });
        });
    });
}
/**
 * Checkbox
 */
page.ezmark = function(){
    $(function(){
        $(':checkbox.checkbox:not(.ezMarked), :radio.radio:not(.ezMarked)').addClass('ezMarked').ezMark();
    });
}
/**
 * Colorpicker
 */
page.colorpicker = function(){
    $(function(){
		$('.color_picker').each(function(){
			var colorpicker = this;
			$('#' + $(this).attr('id')).ColorPicker({
				onShow: function (colpkr){
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr){
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function (hsb, hex, rgb){
					$(':hidden[name="' + $(colorpicker).data('name') + '"]').val('#' + hex);
					$(colorpicker).css('background-color', '#' + hex);
				},
                                color: $(colorpicker).data('value').substr(1)
			});
		});
	});
}
/**
 * Progressbar
 */
page.progressbar = function(){
    $(function(){
		$(".progressbar").each(function(){
			var $progressbar = $(this);
			$progressbar.progressbar({
				value: $progressbar.data('value')
			});
		});
	});
}
/**
 * Datepicker
 */
page.datepicker = function(){
    $(function(){
		$.datepicker.regional['sk'] = {
                    closeText: 'Zavrieť',
                    prevText: '&#x3c;Predchádzajúci',
                    nextText: 'Nasledujúci&#x3e;',
                    currentText: 'Dnes',
                    monthNames: ['Január','Február','Marec','Apríl','Máj','Jún','Júl','August','September','Október','November','December'],
                    monthNamesShort: ['Jan','Feb','Mar','Apr','Máj','Jún','Júl','Aug','Sep','Okt','Nov','Dec'],
                    dayNames: ['Nedeľa','Pondelok','Utorok','Streda','Štvrtok','Piatok','Sobota'],
                    dayNamesShort: ['Ned','Pon','Uto','Str','Štv','Pia','Sob'],
                    dayNamesMin: ['Ne','Po','Ut','St','Št','Pia','So'],
                    weekHeader: 'Ty',
                    dateFormat: '@', // d.m.yy
                    firstDay: 1,
                    isRTL: false,
                    showMonthAfterYear: false,
                    showButtonPanel: true,
                    showOtherMonths: true,
                    selectOtherMonths: true,
                    
                    //altField: "#alternate",
                    //altFormat: "DD, d MM, yy",
                    
                    yearSuffix: ''
                };
		$.datepicker.setDefaults($.datepicker.regional[cms.admin_lang]);
                
                $('.datepicker').each(function(){
                    var $self = $(this);
                    $self.datepicker({
                        showAnim: 'drop',
                        //altField: '#' + $self.attr('id'),
                        //altFormat: 'd.m.yy' // DD, d.m.yy
                    });
                });
                
		/*$('.datepicker').datepicker({
                    showAnim: 'drop'
                    //dateFormat: 'd.m.yy'
                    //dateFormat: '@'
                });*/
                /*global.$form.on('submit', function(){
                    $('.datepicker').each(function(){
                        var unix_time = '';
                        try
                        {
                            unix_time = $(this).datepicker('getDate').getTime() / 1000;
                        }
                        catch (e){}
                        unix_time = String(unix_time);
                        if(unix_time.length > 0) $(this).val(unix_time);
                    });
                });*/
	});
}
/**
 * Fancybox
 */
page.fancybox = function(){
    $(function(){
        $('.fancybox').fancybox();
    });
}
/**
 * Overlay
 */
page.overlay = function(){
    $(function(){
        page.overlay.el = $('#overlay');
        page.overlay.el.hide();
        $('#overlay .overlay_close').on('click', page.overlay.hide);
        page.overlay.el.on('click', page.overlay.hide);
    });
}
page.overlay.hide = function(){
    $(function(){
        page.overlay.el.hide().trigger('hide');
        $('body').removeClass('ovelayed');
        $(window).scrollTop(global.elfinder.scrollTop);
    });
}
page.overlay.show = function(){
    $(function(){
        global.elfinder.scrollTop = $(window).scrollTop();
        $(window).scrollTop(0);
        page.overlay.el.show().trigger('show');
        $('body').addClass('ovelayed');
    });
}
/**
 * Elfinder
 */
global.elfinder = {};
global.elfinder.connector_url = cms.base_url + 'admin/file_manager/connector';
page.elfinder = function(){
    $(function(){
        $('.elfinder_picker :input.input:not(.' + global.field_placeholder.focus_class + ')').on('change keydown keyup', function(){
            if($(this).val().length > 0){
                $(this).parents('.elfinder_picker').addClass('elfinder_picker_selected');
                $(this).parents('.elfinder_picker').find('.link_to_target a').attr('href', $(this).val());
            }
            else
            {
                $(this).parents('.elfinder_picker').removeClass('elfinder_picker_selected');
                $(this).parents('.elfinder_picker').find('.link_to_target a').attr('href', '#');
            }
        }).trigger('keyup');
    });
};
page.elfinder_pick = function (field_name, onlyMimes, multiple){
    page.overlay.show();
    page.overlay.el.on('hide', function(){
        $('#elfinder').html('').hide();
    });
    $('<div class="over_overlay disable-selection" />').appendTo('#elfinder');
    $('#elfinder > div').elfinder({
        url : global.elfinder.connector_url,
        lang: 'cs',
        resizable: false,
        commandsOptions: {
            getfile: {
                onlyURL: false,
                multiple: multiple
            }
        },
        getFileCallback: function(url){
            url = cms.assets_relative + url.path;
            $('.elfinder_picker :input.input[name="' + field_name + '"]').val(url).trigger('change');
            page.overlay.hide();
        },
        onlyMimes: [onlyMimes]
    });
    $('#elfinder').show();
}
page.elfinder_autoload = function (){
    var element = $('.elfinder_autoload');
    element.elfinder({
        url : global.elfinder.connector_url,
        lang: 'cs',
        resizable: false,
        height: $('#wrap').height() - $('#vrch').outerHeight() - $('#main-menu-wrap').outerHeight() - 2,
        getFileCallback: function(url){
            window.opener.CKEDITOR.tools.callFunction(element.data('ckeditorfuncnum') ,url);
            window.close();
        },
        onlyMimes: [element.data('onlyMimes')]
    });
}
/**
 * Table forms
 */
global.table_form = {};
page.table_form = function(){
    $(function(){
        global.table_form.object = $('.table_form');
        global.table_form.custom_rows();
        global.table_form.equal_widths();
        global.table_form.object.on('change', function(){
            global.table_form.custom_rows();
            page.form_validation();
            //global.table_form.equal_widths(); // TODO: BUG: toto mi rozhodi sirku neajaxovych fieldov
            // TODO: BUG: ciara posledneho ajaxoveho fieldu tam nema byt
        });
    });
};
global.table_form.custom_rows = function()
{
    $('.table_form tr').removeClass("e2");
    $('.table_form tr:nth-child(2n)').addClass("e2");
}
global.table_form.equal_widths = function()
{
    global.table_form.object.each(function(){
        var $table_form = $(this);
        var max_width = 0;
        $table_form.find('tr th').each(function(){
            var this_width = $(this).find('span label').width();
            if(this_width > max_width) max_width = this_width;
        }).width(max_width + 30);
        
    });
}
/**
 * Datatables
 */
page.datatable = function(){
    $(function(){
        
        global.datatable = {};
        
        global.datatable.sortgroupped_class = 'ui-sortable-grouped';
        global.datatable.sortgroupped_class_main = 'ui-sortable-grouped-main';
        global.datatable.unactive_class = 'ui-sortable-unactive';
        global.datatable.sorting_class = 'ui-sortable-sorting';
	
        $('.datatable tbody tr[data-sortgroup]')
            .unbind('mouseover mouseout')
            .on('mouseover', function(){
                $('.datatable tbody tr[data-sortgroup]').removeClass(global.datatable.sortgroupped_class);
                $(this).addClass(global.datatable.sortgroupped_class + ' ' + global.datatable.sortgroupped_class_main);
                find_tree($(this)).addClass(global.datatable.sortgroupped_class);
                if($('tr[data-sortgroup="' + $(this).data('sortgroup') + '"]').length > 1)
                {
                    $(this).parent('tbody').find('tr td .handle').on('mousedown', function(){
                        global.datatable.length_dropdown.val(global.datatable.length_dropdown.find('option').last().val())
                        stop_sorting();
                    });
                    var sortable_group = $(this).data('sortgroup');
                    var sortable_items_selector = 'tr[data-sortgroup="' + sortable_group + '"]:visible';
                    var sortable_items_object = $(sortable_items_selector);
                    $(this).parent('tbody').sortable({
                        helper: fixHelper,
                        items: sortable_items_selector,
                        axis: 'y',
                        handle: '.handle',
                        start: function(event, ui){
                            $(this).addClass(global.datatable.sorting_class);
                            $(this).find('tr').removeClass(global.datatable.unactive_class).each(function(){
                                if($(this).data('sortgroup') != ui.helper.data('sortgroup')) $(this).addClass(global.datatable.unactive_class);
                            });
                            global.datatable.main_row = $(this).find('tr[data-sortgroup].' + global.datatable.sortgroupped_class + '.' + global.datatable.sortgroupped_class_main);
                            global.datatable.sub_rows = $(this).find('tr[data-sortgroup].' + global.datatable.sortgroupped_class + ':not(.' + global.datatable.sortgroupped_class_main + ')');
                            global.datatable.this_prev = $($(ui.item).prevAll('tr[data-sortgroup="' + $(ui.item).data('sortgroup') + '"]')[0]);
                            global.datatable.this_prev_childs = find_tree(global.datatable.this_prev);
                            global.datatable.this_next = $($(ui.item).nextAll('tr[data-sortgroup="' + $(ui.item).data('sortgroup') + '"]')[0]);
                            global.datatable.this_next_childs = find_tree(global.datatable.this_next);
                        },
                        update: function(event, ui){
                            global.datatable.main_row.after(global.datatable.sub_rows);
                            global.datatable.this_prev.after(global.datatable.this_prev_childs);
                            global.datatable.this_next.after(global.datatable.this_next_childs);
                            var result = $(this).sortable("toArray");
                            var sortable_items_ids = new Array();
                            sortable_items_object.each(function(){
                                sortable_items_ids.push($(this).attr('id'));
                            });
                            global.functions.saveSort(ui.item.data('table'), sortable_items_ids, result);
                            var aoData = global.datatable.object.fnSettings().aoData;
                            var aoData_sort = [];
                            sortable_items_object.each(function(){
                                for(var aoData_index in global.datatable.object.fnSettings().aoData)
                                {
                                    if(typeof aoData[aoData_index] == "object" && aoData[aoData_index].nTr == this) aoData_sort.push(aoData_index);
                                }
                            });
                            function getNode(id)
                            {
                                for(var node in aoData)
                                {
                                    if(typeof aoData[node] == "object" && aoData[node].nTr.id == id && $(aoData[node].nTr).data('sortgroup') == sortable_group) return aoData[node];
                                }
                            }
                            // TODO: Clone array
                            //var new_aoData = global.datatable.object.fnSettings().aoData;
                            var new_aoData = [];
                            aoData.forEach(function(item){
                                new_aoData.push(item);
                            });
                            for(var index in aoData_sort)
                            {
                                var new_object = getNode(result[index]);
                                if(typeof new_object == "object") new_aoData[aoData_sort[index]] = new_object;
                            }
                            global.datatable.object.fnSettings().aoData = new_aoData;
                            sorting_changing = false;
                        },
                        stop: function(event, ui){
                            stop_sorting();
                            $(this).find('tr').show().css('display', 'table-row');
                            sorting_changing = false;
                        }
                    });
                }
            })
            .on('mouseout', function(){
                $('.datatable tbody tr[data-sortgroup]').removeClass(global.datatable.sortgroupped_class + ' ' + global.datatable.sortgroupped_class_main);
            });
            
        function stop_sorting()
        {
            $('.datatable tbody').removeClass(global.datatable.sorting_class);
            $('.datatable tbody tr').removeClass(global.datatable.unactive_class);
        }
            
        function find_tree(element)
        {
            var cur_level = element.find('[data-level]').data('level');
            if(typeof cur_level == 'undefined') cur_level = 0;
            var go_next = true;
            var next = element;
            while(go_next)
            {
                next = next.next();
                var next_level = next.find('[data-level]').data('level');
                if(typeof next_level == 'undefined') next_level = 0;
                if(next_level > cur_level) next.addClass('temporary_class');
                else go_next = false;
            }
            var result = $('.temporary_class');
            result.removeClass('temporary_class');
            return result;
        }
		
        $('.datatable tbody tr td').each(function(){
            var level = $(this).data('level');
            if(parseInt(level) > 0)
            {
                var pre = '';
                for(level; level > 0; level--) pre += '<span class="pre ui-icon ui-icon ui-icon-carat-1-sw"></span>';
                var handle_html = $(this).find('.handle')[0].outerHTML;
                $(this).find('.handle').remove();
                $(this).html(handle_html + pre + $(this).html());
            }
        });
        
        global.datatable.languages = new Array();
        
        global.datatable.languages['sk'] = {
            "sProcessing":   "Pracujem...",
            "sLengthMenu":   "Zobraz _MENU_ záznamov",
            "sZeroRecords":  "Neboli nájdené žiadne záznamy",
            "sInfo":         "Záznamy _START_ až _END_ z celkovo _TOTAL_",
            "sInfoEmpty":    "Záznamy 0 až 0 z celkovo 0",
            "sInfoFiltered": "(filtrované z celkovo _MAX_ záznamov)",
            "sInfoPostFix":  "",
            "sSearch":       "Hľadaj:",
            "sUrl":          "",
            "oPaginate": {
                "sFirst":    "Prvá",
                "sPrevious": "Predchádzajúca",
                "sNext":     "Ďalšia",
                "sLast":     "Posledná"
            }
        };
        
        // TODO: Zlangovat datatables
        
        global.datatable.languages['en'] = {
            "sEmptyTable":     "No data available in table",
            "sInfo":           "Showing _START_ to _END_ of _TOTAL_ entries",
            "sInfoEmpty":      "Showing 0 to 0 of 0 entries",
            "sInfoFiltered":   "(filtered from _MAX_ total entries)",
            "sInfoPostFix":    "",
            "sInfoThousands":  ",",
            "sLengthMenu":     "Show _MENU_ entries",
            "sLoadingRecords": "Loading...",
            "sProcessing":     "Processing...",
            "sSearch":         "Search:",
            "sZeroRecords":    "No matching records found",
            "oPaginate": {
                "sFirst":    "First",
                "sLast":     "Last",
                "sNext":     "Next",
                "sPrevious": "Previous"
            },
            "oAria": {
                "sSortAscending":  ": activate to sort column ascending",
                "sSortDescending": ": activate to sort column descending"
            }
        };
        
        global.datatable.languages['cs'] =
        global.datatable.languages['cz'] = {
            "sProcessing":   "Provádím...",
            "sLengthMenu":   "Zobraz záznamů _MENU_",
            "sZeroRecords":  "Žádné záznamy nebyly nalezeny",
            "sInfo":         "Zobrazuji _START_ až _END_ z celkem _TOTAL_ záznamů",
            "sInfoEmpty":    "Zobrazuji 0 až 0 z 0 záznamů",
            "sInfoFiltered": "(filtrováno z celkem _MAX_ záznamů)",
            "sInfoPostFix":  "",
            "sSearch":       "Hledat:",
            "sUrl":          "",
            "oPaginate": {
               "sFirst":    "První",
               "sPrevious": "Předchozí",
               "sNext":     "Další",
               "sLast":     "Poslední"
            }
        };
        
        $('.datatable').each(function(){
            var $this = $(this);
            if($this.find('tr[data-sortgroup]').length > 0) $this.removeClass('datatable-sorting');

            global.datatable.object = $(this).dataTable({
                "bJQueryUI": true,
                "bSort": $this.hasClass('datatable-sorting'),
                "iDisplayLength": -1,
                "aLengthMenu": [
                     [10, 20, 30, 60, 100, 200, -1],
                     [10, 20, 30, 60, 100, 200, "všetky"]
                 ],
                "oLanguage": global.datatable.languages[cms.admin_lang],
                "fnDrawCallback": page.ezmark
            });
            
            global.datatable.length_dropdown = $(this).parent('.dataTables_wrapper').find('.dataTables_length select');
            global.datatable.filter_input = $(this).parent('.dataTables_wrapper').find('.dataTables_filter input');
            
        });
        
        $(window).on('keydown', function(e){
            if(!$('*:focus').is('textarea, input, select') && $('.datatable').is(':visible')){
                if(e.keyCode == $.ui.keyCode.LEFT) global.datatable.object.fnPageChange('previous');
                else if(e.keyCode == $.ui.keyCode.UP){
                    var $DataTables_length = $('#DataTables_Table_0_length');
                    var $prev = $DataTables_length.find(':selected').prev();
                    if($prev.is('option')){
                        $DataTables_length.find('option').removeAttr('selected');
                        $prev.attr('selected', 'selected');
                    }
                    $DataTables_length.find('select').trigger('change');
                }
                else if(e.keyCode == $.ui.keyCode.DOWN){
                    var $DataTables_length = $('#DataTables_Table_0_length');
                    var $next = $DataTables_length.find(':selected').next();
                    if($next.is('option')){
                        $DataTables_length.find('option').removeAttr('selected');
                        $next.attr('selected', 'selected');
                    }
                    $DataTables_length.find('select').trigger('change');
                }
                else if(e.keyCode == $.ui.keyCode.RIGHT) global.datatable.object.fnPageChange('next');
                else if(e.keyCode == $.ui.keyCode.ENTER){
                    var $first_row = $('.datatable').find('a').first();
                    if(typeof $first_row != 'undefined') $first_row.trigger('click');
                }
                else if(!e.ctrlKey && e.keyCode >= 32) $('.dataTables_filter :input').focus();
            }
        });
    });
};
/**
 * Charts
 */
page.chart = function (){
	$(function(){
                // Radialize the colors
		Highcharts.getOptions().colors = $.map(Highcharts.getOptions().colors, function(color) {
		    return {
		        radialGradient: {cx: 0.5, cy: 0.3, r: 0.7},
		        stops: [
		            [0, color],
		            [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
		        ]
		    };
		});
		var charts = new Array();
		$('.chart_container').each(function(){
			var chart = $(this).data('chart');
			var chart_options = charts_data[chart];
			var chart_container = $(this).attr('id');
			if(typeof chart_container == 'undefined' || chart_container.length == 0) chart_container = "chart_container_" + chart;
			chart_options.chart.renderTo = chart_container;
			charts[chart] = new Highcharts.Chart(chart_options);
		});
	});
};
/**
 * Contextmenu
 */
page.contextmenu = function (){
    $('#hide').append('<div id="contextmenu_confirm_link"></div>');
    var $contextmenu_confirm_link = $('#contextmenu_confirm_link');
    $('.jeegoocontext li[data-icon]').addClass('icon').each(function(){
        $(this).prepend('<span class="icon" style="background-image: url(\'' + $(this).data('icon') + '\');"></span>');
    });
    $('.contextmenu').each(function(){
        $(this).jeegoocontext('contextmenu_' + $(this).data('contextmenu'), {
            widthOverflowOffset: 0,
            heightOverflowOffset: 3,
            submenuLeftOffset: -1,
            submenuTopOffset: -2,
            onSelect: function(a, b)
            {
                var data = $(a.currentTarget).data();
                if(typeof data.text != 'undefined' && data.text.length > 0)
                {
                    $contextmenu_confirm_link.text(data.text);
                    var dialog_options = {
                        title: data.title,
                        buttons: {
                            "Potvrdiť": function(){
                                window.location = data.href;
                            },
                            "Zrušiť": function(){
                                $(this).dialog("close");
                            }
                        }
                    };
                    $contextmenu_confirm_link.dialog(dialog_options);
                }
                else
                {
                    window.location = data.href;
                }
            }
        });
    });
};
/**
 * Equal heights
 */
page.equalheights = function (){
    $(function(){
        $('.equalheights').equalHeights();
    });
};
/**
 * Equal widths
 */
page.equalwidths = function (){
    $(function(){
        $('.equalwidths').equalwidths();
    });
};
/**
 * Disable selection
 */
page.disableselection = function (){
    $(function(){
        $('.disableselection').disableSelection();
    });
};
/**
 * Textarea
 */
page.textarea = function (){
    $(function(){
        $('.textarea').autogrow();
    });
};
/**
 * Categorizing
 */
page.categorizing = function (){
    $(function(){
        $('.categorizing_wrap').each(function(index){
            var categorizing_wrap_id = "categorizing_wrap_" + (index + 1);
            $(this).attr('id', categorizing_wrap_id);
            var categorizing_wrap_selector = '#' + categorizing_wrap_id;
            
            initDraggable();
            
            var data = $(this).data();
            var widget_sort_method = data.categorizingWidgetSortMethod;
            var item_sort_method = data.categorizingItemSortMethod
            var unique = data.categorizingUnique;
            var unique_in_widget = data.categorizingUniqueInWidget;
            
            if(widget_sort_method.length > 0)
            {
                $(categorizing_wrap_selector).sortable({
                    cursor: 'pointer',
                    opacity: 0.8,
                    items: '.category:not(.trash)',
                    handle: '.ui-widget-header',
                    forceHelperSize: true,
                    update: function()
                    {
                        var items = [];
                        $(this).find('.category[id]').each(function(){
                            items.push($(this).attr('id'));
                        });
                        $.ajax({
                            url: cms.base_url + widget_sort_method,
                            dataType: 'json',
                            data: global.functions.get_token() + 'sort=' + JSON.stringify(items),
                            success: function(data){
                                if(data.result)
                                {
                                    board.message(__('sort_saved'));
                                }
                                else
                                {
                                    console.log(__('sort_unsaved'));
                                    console.log(data.error);
                                    board.error(__('sort_unsaved'), __('details_in_console'));
                                }
                            },
                            error: function(e){
                                console.log(__('sort_unsaved'));
                                console.log(e);
                                board.error(__('sort_unsaved'), __('details_in_console'));
                            }
                        });
                    }
                });
            }
            $(categorizing_wrap_selector + ' .list.droppable').droppable({
                drop: function(event, ui)
                {
                    if($(event.srcElement).is('.item') && $(event.srcElement).parents('.categorizing_wrap').is(categorizing_wrap_selector))
                    {
                        if(unique_in_widget)
                        {
                            var drop_denied = false;
                            var item_id = $(event.srcElement).data('id');
                            $(this).children('li').each(function(){
                                if($(this).attr('id') == item_id) drop_denied = true;
                            });
                            if(drop_denied) return false;
                        }
                        var item_id = $(event.srcElement).data('id');
                        $(this).append('<li class="item-categorized ui-state-default" id="' + item_id + '">' + $(event.srcElement).text() + '</li>');
                        saveSort($(this).parents(categorizing_wrap_selector).data('categorizingItemSortMethod'), $(this).parents('.category').attr('id'), $(this).sortable('toArray'));
                        if($(this).parents(categorizing_wrap_selector + '[data-categorizing-unique=1]').length) $(ui.draggable).remove();
                    }
                }
            });
            $(categorizing_wrap_selector + ' .sortable').sortable({
                cursor: 'pointer',
                opacity: 0.8,
                connectWith: categorizing_wrap_selector + ' .sortable',
                forceHelperSize: true,
                //containment: categorizing_wrap_selector,
                update: function(event, ui)
                {
                    try{if(unique_in_widget)
                    {
                        var drop_denied = false;
                        var item_id = $(event.srcElement).attr('id');
                        $(ui.item).siblings('li').each(function(){
                            if($(this).attr('id') == item_id) drop_denied = true;
                        });
                        if(drop_denied) return false;
                    }}
                    catch(e){}
                    saveSort($(this).parents(categorizing_wrap_selector).data('categorizingItemSortMethod'), $(this).parents('.category').attr('id'), $(this).sortable('toArray'));
                }
            });
            
            // TODO: Ta hlaska "Widget data saving started" by sa mala zorbazit uz pri zacani ajaxoveho requestu nie az po jeho dokonceni
            
            function saveSort(save_target, category_id, sort)
            {
                checkEmptyLists();
                //board.warning('Widget data saving started.');
                $.ajax({
                    url: cms.base_url + save_target,
                    dataType: 'json',
                    data: global.functions.get_token() + 'category_id=' + category_id + '&sort=' + JSON.stringify(sort),
                    success: function(data){
                        if(data.result)
                        {
                            board.message(__('sort_saved'));
                        }
                        else
                        {
                            console.log(__('sort_unsaved'));
                            console.log(data.error);
                            board.error(__('sort_unsaved'), __('details_in_console'));
                        }
                    },
                    error: function(e){
                        console.log(__('sort_unsaved'));
                        console.log(e);
                        board.error(__('sort_unsaved'), __('details_in_console'));
                    }
                });
            }
            function checkEmptyLists()
            {
                $(categorizing_wrap_selector + ' .list').each(function(){
                    if($(this).find('li').length == 0) $(this).addClass('empty');
                    else $(this).removeClass('empty');
                });
            }
            $(categorizing_wrap_selector + ' .trash.droppable, ' + categorizing_wrap_selector + ' .items').droppable({
                drop: function(event, ui)
                {
                    $(this).removeClass('over');
                    if(!$(ui.draggable).is('.item-categorized') || !$(ui.draggable).parents('.categorizing_wrap').is(categorizing_wrap_selector)) return;
                    if($(this).parents(categorizing_wrap_selector + '[data-categorizing-unique=1]').length)
                    {
                        $(this).parents(categorizing_wrap_selector).find('.items').append('<p class="item jui_button ui-state-default ui-corner-all draggable" data-id="' + $(ui.draggable).attr('id') + '">' + $(ui.draggable).text() + '</p>');
                        global.functions.initJUI();
                        initDraggable();
                    }
                    $(event.srcElement).remove();
                },
                over: function(event, ui){
                    if(!$(ui.draggable).is('.item-categorized') || !$(ui.draggable).parents('.categorizing_wrap').is(categorizing_wrap_selector)) return;
                    $(this).addClass('over');
                },
                out: function(event, ui){
                    if(!$(ui.draggable).is('.item-categorized') || !$(ui.draggable).parents('.categorizing_wrap').is(categorizing_wrap_selector)) return;
                    $(this).removeClass('over');
                }
            });
            function initDraggable()
            {
                var options = {
                    //containment: categorizing_wrap_selector,
                    revert: true
                };
                $(categorizing_wrap_selector + ' .draggable').each(function(){
                    if(!$(this).parents(categorizing_wrap_selector + '[data-categorizing-unique=1]').length)
                    {
                        options.helper = 'clone';
                    }
                    $(this).draggable(options);
                });
            }
            checkEmptyLists();
        });
    });
};
/**
 * Confirm link
 */
page.confirm_link = function (){
    $(function(){
        $('#hide').append('<div id="confirm_link"></div>');
        var $confirm_link = $('#confirm_link');
        $('.confirm_link').on('click', function(e){
            var $a = $(this);
            var data = $(this).data();
            $confirm_link.text(data.text);
            var dialog_options = {
                title: data.title,
                buttons: {
                    "Potvrdiť": function(){
                        //alert("a");
                        //$(this).dialog("close");
                        //$a.removeClass('confirm_link').attr('href', data.href).trigger('click');
                        //$(this).dialog("close");
                        window.location = data.href;
                    },
                    "Zrušiť": function(){
                        $(this).dialog("close");
                    }
                }
            };
            $confirm_link.dialog(dialog_options);
            return false;
        })
    });
};
/**
 * Code Mirror
 */
global.codemirror_objects = [];
page.codemirror = function (){
    $(function(){
        $("textarea.codemirror").autogrow().each(function(){
                var editor = CodeMirror.fromTextArea($(this)[0], {
                        lineNumbers: true,
                        matchBrackets: true,
                        mode: $(this).data('type'),
                        indentUnit: 4,
                        indentWithTabs: true,
                        lineWrapping: true
                });
        });
    });
};
/**
 * Field placeholder
 */
global.field_placeholder = {}
global.field_placeholder.focus_class = 'placeholder_focus';
page.field_placeholder = function (){
    $(function(){
        $('.field_placeholder')
        .on('focus', function(){
            if($(this).val() == $(this).data('placeholder')) $(this).val('').removeClass(global.field_placeholder.focus_class);
        })
        .on('blur change', function(){
            if($(this).val() == '') $(this).val($(this).data('placeholder')).addClass(global.field_placeholder.focus_class);
        })
        .on('change', function(){
            if($(this).val() != $(this).data('placeholder')) $(this).removeClass(global.field_placeholder.focus_class);
        })
        .each(function(){
            if($(this).val() == '') $(this).val($(this).data('placeholder')).addClass(global.field_placeholder.focus_class);
        });
        global.$form.on('submit', function(){
            $('.field_placeholder').each(function(){
                if($(this).val() == $(this).data('placeholder')) $(this).val('').removeClass(global.field_placeholder.focus_class);
            });
        });
    });
};
/**
 * Field product gallery
 */
global.field_product_gallery = {};
global.field_product_gallery.refresh = function()
{
    if(typeof global.field_product_gallery.elements == 'undefined') page.field_product_gallery();
    global.field_product_gallery.elements.trigger('change');
}
page.field_product_gallery = function (){
    $(function(){
        global.field_product_gallery.elements = $('.field_product_gallery');
        $('.field_product_gallery:not(.field_product_gallery_bound)').addClass('field_product_gallery_bound').each(function(){
            var $self = $(this);
            var id = $self.attr('id');
            var $chosen = $('#' + id + '_chzn');
            var $button = $('#' + id + '_button');
            var href = $button.data('href');
            var iframe = '?' + cms.url_iframe + '=' + cms.url_true;
            $self.on('change', function(){
                var val = parseInt($(this).val());
                if(val > 0)
                {
                    $chosen.width(320);
                    $button.show(0);
                    $button.attr('href', href + val + iframe);
                }
                else
                {
                    $chosen.width(350);
                    $button.hide(0);
                }
            });
        });
        global.field_product_gallery.refresh();
    });
};
/**
 * Global functions
 */
global.functions = {};
global.functions.initJUI = function(){
    //jQuery UI Initialization
    $('.jui_button, .jui_icon').hover(
        function(){$(this).addClass('ui-state-hover');}, 
        function(){$(this).removeClass('ui-state-hover');}
    );
}
global.functions.get_form_field_value = function(field){
    var $field = $('*[name="' + field + '"]');
    var field_val = $field.val();
    return ($field.hasClass('field_placeholder') && $field.data('placeholder') == field_val) ? '' : field_val;
}
global.functions.set_form_field_value = function(field, value){
    $('*[name="' + field + '"]').val(value);
}
global.functions.form_sent = function()
{
    return global.$form.hasClass('form_sent');
}
global.functions.form_error = function()
{
    return global.$form.hasClass('validation_error');
}
global.functions.get_form_data = function(serialize, ignore_fields){
    if(typeof serialize == 'undefined') serialize = true;
    if(!Array.isArray(ignore_fields)) ignore_fields = [];
    var form_data = {};
    global.$form.find('input, textarea, select').each(function(){
        var name = $(this).attr('name');
        if(typeof name != 'undefined' && ignore_fields.indexOf(name) == -1)
        {
            var value = global.functions.get_form_field_value(name);
            if(name.substr(-2) == '[]')
            {
                if(value == null) value = [];
                else name = name.substr(0, name.length - 2);
            }
            form_data[name] = value;
        }
    });
    if(global.functions.form_sent()) form_data = $.extend(cms.$_REQUEST, form_data);
    if(serialize) form_data = $.param(form_data);
    return form_data;
}
global.sorting_changing = false;
global.functions.saveSort = function(table, items, sort)
{
    if(global.sorting_changing) board.error(__('sort_saving_progress'));
    board.warning(__('sort_started'));
    global.sorting_changing = true;
    $.ajax({
        url: cms.ajax + 'system/save_sort',
        dataType: 'json',
        type: 'POST',
        data: $.param($.extend(global.functions.get_form_data(false), {table: table, items: JSON.stringify(items), sort: JSON.stringify(sort)})),
        success: function(data){
            if(data.result)
            {
                board.message(__('sort_saved'));
            }
            else
            {
                console.log(__('sort_unsaved'));
                console.log(data.error);
                board.error(__('sort_unsaved'), __('details_in_console'));
            }
            global.sorting_changing = false;
        },
        error: function(e){
            console.log(__('sort_unsaved'));
            console.log(e);
            board.error(__('sort_unsaved'), __('details_in_console'));
            global.sorting_changing = false;
        }
    });
}
global.functions.ajax_refresh = function()
{
    page.chosen();
    page.form_validation();
    page.slider();
    page.label_childs();
    page.ezmark();
    page.required_fields();
    page.elfinder();
    page.fancybox();
    page.ckeditor();
    page.tiptip();
    
    page.field_placeholder();
    page.field_href();
    page.field_product_gallery();
    page.href_field_attributes();
}
global.functions.set_field_error = function(field_name, error)
{
    var $field_wrap = $('*[name="' + field_name + '"]').parents('.field_wrap');
    if($field_wrap.siblings('.error').length == 0) $('<p class="error">' + error + '</p>').insertAfter($field_wrap);
    else $field_wrap.siblings('.error').html(error);
}
global.functions.get_token = function()
{
    return cms.csrf_token_name + '=' + global.functions.get_form_field_value(cms.csrf_token_name) + '&';
}
// Return a helper with preserved width of cells
var fixHelper = function(e, ui){
    ui.children().each(function(){
        $(this).width($(this).width());
    });
    return ui;
};
/**
 * Board
 */
var board = {
    maximum: 10,
    effect_delay: 500,
    timeout_delay: 3000,
    timeout_running: false,
    initialized: false,
    init: function()
    {
        if(!board.initialized)
        {
            global.board_object = $('#board');
            board.refresh();
            global.board_object.hover(board.timeout_stop, board.timeout_start);
            global.board_object.on('click', 'li', function(){
                board.hide($(this));
            });
            board.initialized = true;
        }
    },
    refresh: function()
    {
        if(global.board_object.find('li').length > 0)
        {
            board.timeout_start();
            global.board_object.show();
        }
        else
        {
            board.timeout_stop();
            global.board_object.hide();
        }
    },
    message: function(text, description)
    {
        return board.add(text, description, 'message');
    },
    error: function(text, description)
    {
        return board.add(text, description, 'error');
    },
    warning: function(text, description)
    {
        return board.add(text, description, 'warning');
    },
    add: function(text, description, status)
    {
        board.init();
        global.board_object.show();
        var heading_tag = '<p class="heading">' + text + '</p>';
        var description_tag = (typeof description != 'undefined' && description.length > 0) ? '<p class="description">' + description + '</p>' : '';
        global.board_object.prepend('<li class="' + status + ' new">' + heading_tag + description_tag + '</li>');
        if(global.board_object.find('li').length > board.maximum) global.board_object.find('li').slice(board.maximum).each(function(){
            $(this).slideUp(board.effect_delay, function(){
                $(this).remove();
                board.refresh();
            });
        });
        global.board_object.find('li').first().hide().slideDown(board.effect_delay, function(){
            global.board_object.find('li.new').removeClass('new', 2000);
            board.refresh();
        });
    },
    hide: function(element)
    {
        if(global.board_object.find('li').length > 0)
        {
            if(typeof element == 'undefined')
            {
                if(global.board_object.find('li').length > board.maximum) element = global.board_object.find('li').slice(board.maximum);
                else element = global.board_object.find('li').last();
            }
            else board.timeout_stop();
            element.stop(true, true).slideUp(board.effect_delay, function(){
                $(this).remove();
                board.timeout_running = false;
                board.refresh();
            });
        }
        else
        {
            board.timeout_running = false;
        }
    },
    timeout_start: function()
    {
        if(!board.timeout_running)
        {
            board.timeout_running = true;
            board.timeout = setTimeout(board.hide, board.timeout_delay);
        }
    },
    timeout_stop: function()
    {
        if(board.timeout_running)
        {
            board.timeout_running = false;
            clearTimeout(board.timeout);
        }
    }
};
/**
 * Fancybox iframe
 */
page.fancybox_iframe = function(){
    $('.fancybox_iframe').fancybox({
        type    : 'iframe',
        width   : '100%',
        height  : '100%',
        padding : 5,
        margin  : 20
    });
}
/**
 * Reports
 */
global.reports = {};
global.reports.speed = 300;
global.reports.wait = 0;
page.reports = function(){
    if(global.reports.wait > 0)
    {
        setTimeout(function(){
            $('.reports').slideUp(global.reports.speed * $('.reports .report:visible').length);
        }, global.reports.wait);
    }
    $('.reports .hidable.message, .reports .hidable.warning, .reports .hidable.error').bind('click', function(){
        if($('.reports .report:visible').length == 1) $('.reports').slideUp(global.reports.speed);
        else $(this).css('-webkit-box-shadow', 'none').slideUp(global.reports.speed)
    });
}
/**
 * Label childs
 */
page.label_childs = function(){
    $('.label_childs').each(function(){
        var children = $(this).children(':visible');
        children.removeClass('first-child last-child');
        children.first().addClass('first-child');
        children.last().addClass('last-child');
    });
}
/**
 * Helper buttons
 */
page.button_helper = function(){
    $('#hide').append('<div id="button_helper"></div>');
    var $button_helper = $('#button_helper');
    $('.button_helper').on('click', function(){
        var data = $(this).data();
        $button_helper.text(data.text);
        $button_helper.dialog({
            title: data.title,
            buttons: {
                "Rozumiem": function(){ // TODO: Zlangovat
                    $(this).dialog("close");
                }
            }
        });
    });
}
/**
 * Cell thumbnail
 */
page.cell_thumbnail = function(){
    $(".cell_thumbnail").hover(function(){
        $(this).find('.image').stop(true, true).fadeOut(0).fadeIn(200);
    }, function(){
        $(this).find('.image').stop(true, true).fadeIn(0).fadeOut(200);
    });
}
/**
 * TipTip
 */
page.tiptip = function(){
    $(".tiptip").each(function(){
        if($(this).attr('title') != '')
        {
            var defaultPosition = (typeof $(this).data('tip') == 'undefined') ? 'top' : $(this).data('tip');
            $(this).tipTip({
                defaultPosition: defaultPosition
            });
        }
    });
}
/**
 * Ajax area
 */
global.ajax_area = {};
page.ajax_area = function(){
    $(".ajax_area").each(function(){
        var handlers = $(this).data('handlers').split(',');
        var ajax_method = $(this).data('ajax_method');
        for(var handler in handlers)
        {
            $('*[name="' + handlers[handler] + '"]').on('change', {ajax_method: ajax_method, ajax_area: $(this)}, global.ajax_area.load).trigger('change', 'no_validation');
        }
    });
}
global.ajax_area.load = function(event)
{
    //console.log( global.functions.get_form_data(false, (global.functions.form_sent() ? null : [config.form_sent])) );
    
    var event_data = event.data;
    
    var $field_wrap = $(this).parents('.field_wrap');
    $field_wrap.addClass('loading');
    
    $.ajax({
        url: cms.base_url + event_data.ajax_method,
        dataType: 'json',
        data: global.functions.get_form_data(true, (global.functions.form_sent() ? null : [config.form_sent])),
        async: true,
        success: function(data){
            //global.functions.set_form_field_value('field_id', data.field_id);
            $('.table_form').trigger('change');
            $field_wrap.removeClass('loading');
            $(':hidden [name=field_id]').val(data.field_id);
            if(data.result)
            {
                event_data.ajax_area.html(data.content);
            }
            else
            {
                console.log(__('error'));
                console.log(data.error);
                board.error(__('error'), __('details_in_console'));
                event_data.ajax_area.html('');
            }
            global.functions.ajax_refresh();
        },
        error: function(e){
            $('.table_form').trigger('change');
            $field_wrap.removeClass('loading');
            console.log(__('error'));
            console.log(e);
            board.error(__('error'), __('details_in_console'));
            event_data.ajax_area.html('');
        }
    });
}
/**
 * Autofocus
 */
page.autofocus = function()
{
    $(function(){
        $('.autofocus').focus();
    });
}
/**
 * Field href
 */
page.field_href = function()
{
    $(function(){
        $('.field_href').each(function(){
            var self = $(this);
            var has_attrs = (self.find('.href_field_attributes').length > 0);
            var $href_type_value = self.find('.href_type .href_type_value');
            var $type_wraps = self.find('.href_type_types .field_type_wrap');
            var $types = self.find('.href_type_types .field_type_wrap > *');
            var $selector = self.find('.href_type > select');
            var $selected;
            var $selected_wrap;
            var value_type = (String(self.data('value-type')).length > 0) ? self.data('value-type') : '';
            var value_value = (String(self.data('value-value')).length > 0) ? self.data('value-value') : '';
            $selector.val(value_type).on('change', function(e, param){
                $selected = self.find('.href_type_types .field_type_wrap .href_type_' + $selector.val());
                $selected_wrap = $selected.parent('.field_type_wrap');
                if(typeof $selected != 'undefined' && $selected.length > 0)
                {
                    $type_wraps.hide(0);
                    $selected_wrap.show(0);
                }
                
                var data = {};
                data.type = $selector.val();
                data.value = $selected.val();
                if(has_attrs)
                {
                    var are_attrs = false;
                    var attrs = {};
                    self.find('.href_field_attributes .attribute_field .a_field').each(function(){
                        var name = $(this).data('name');
                        var val = $(this).val();
                        if(typeof name != 'undefined' && name.length > 0 && typeof val != 'undefined' && val.length > 0)
                        {
                            are_attrs = true;
                            attrs[name] = val;
                        }
                    });
                    if(are_attrs) data.attrs = attrs;
                }
                $href_type_value.val(JSON.stringify(data)).trigger('change', param);
            });
            $selector.trigger('change', 'no_validation');
            $selected.val(value_value);
            $types.on('change', function(){
                $selector.trigger('change');
            });
            global.$form.on('submit', function(){
                $selector.trigger('change');
            });
        });
    });
}
/**
 * Field href attributes
 */
page.href_field_attributes = function()
{
    $(function(){
        $('.href_field_attributes').each(function(){
            var speed = 300;
            var self = $(this);
            var visible = self.hasAttr('data-visible');
            var $handle = self.find('.handle');
            var text_show = $handle.data('show');
            var text_hide = $handle.data('hide');
            var $content = self.find('.content');
            
            if(!visible) $content.hide(0);
            $handle.text((visible) ? text_hide : text_show);
            
            $handle.off('click').on('click', function(){
                visible = !visible;
                $content.stop(true, true).slideToggle(speed);
                $handle.text((visible) ? text_hide : text_show);
            });
        });
    });
}
/**
 * Variant areas
 */
page.variant_areas = function()
{
    $(function(){
        $('.variant_field').hide();
        $('.variant_select').on('change', function(){
            var selected_variant_class = '';
            var data = global.$form.serializeArray();
            for(var k in data)
            {
                if(data[k].name.substr(0, 21) == 'variant_ids_selected_')
                {
                    selected_variant_class += '_' + data[k].name.substr(21) + '-' + data[k].value;
                }
            }
            selected_variant_class = selected_variant_class.substr(1);
            $('.variant_field').hide();
            var fields = $('.variant_field.variant_field_' + selected_variant_class);
            fields.show(0);
            fields.last().show(0, global.field_product_gallery.refresh);
            
        }).trigger('change');
    });
}
/**
 * Form validation
 */
page.form_validation = function()
{
    $('.field_wrap input.fv:not(.bound-form_validation), .field_wrap textarea.fv:not(.bound-form_validation), .field_wrap radio.fv:not(.bound-form_validation), .field_wrap select.fv:not(.bound-form_validation)').addClass('bound-form_validation').on('change blur', function(e, param){
        if(param == 'no_validation') return;
        var field = $(this);
        var field_name = $(this).attr('name');
        var $field_wrap = $(this).parents('.field_wrap');
        var get_data = {
            name: field_name,
            label: $field_wrap.data('label'),
            rules: $field_wrap.data('rules'),
            value: $(this).val()
        };
        if(typeof get_data.rules != 'undefined' && get_data.rules.length > 0)
        {
            global.functions.set_field_error(field_name, '');
            $field_wrap.addClass('loading');
            $.ajax({
                url: cms.ajax + 'system/get_field_error?' + $.param(get_data),
                dataType: 'json',
                data: global.functions.get_form_data(),
                async: true,
                success: function(data){
                    if(typeof data.error != 'undefined')
                    {
                        if(data.error.length == 0) global.functions.set_field_error(field_name, '');
                        else global.functions.set_field_error(field_name, data.error);
                    }
                    $field_wrap.removeClass('loading');
                    if(field.hasClass('field_placeholder') && data.value.length == 0) return;
                    if(data.value !== false) field.val(data.value);
                },
                error: function(e)
                {
                    console.log("Form validation error:");
                    console.log(e);
                    $field_wrap.removeClass('loading');
                }
            });
        }
    });
}
/**
 * Required fields
 */
page.required_fields = function()
{
    $('.tr_field').each(function(){
        var rules = $(this).find('.field_wrap').data('rules');
        if(typeof rules != 'undefined')
        {
            rules = rules.split('|');
            if($.inArray('required', rules) > -1 || $.inArray('required_href', rules) > -1 || $.inArray('required_internal', rules) > -1) $(this).addClass('required');
        }
    });
}
/**
 * Widgets
 */
global.widgets_closed = {}
page.widgets = function()
{
    global.widgets_closed.init();
    
    $('.widget_heading .button_collapse').on('click', function(){
        var el = $(this).parents('.widget_wrap').find('.widget_content');
        $.cookie($(this).parents('.widget').attr('id') + '_collapsed', el.is(":visible"));
        el.slideToggle();
        
    });
    $('.widget_heading .button_close').on('click', function(){
        var $self = $(this).parents('.widget');
        var id = $self.attr('id');
        $self.hide();
        global.widgets_closed.add($self.find('.widget_heading').text(), id);
        $.cookie($self.attr('id') + '_closed', true);
    });
    
    // Zoradenie widgetov
    $('.widgets').each(function(){
        var $self = $(this);
        var widgets_id = $self.attr('id');
        var widgets_order = $.cookie('widgets_order_' + widgets_id);
        if(widgets_order)
        {
            $.each(widgets_order.split(','), function(i,id){
                $("#" + id).appendTo($self);
            });
        }
        $self.sortable({
            item: '> .widget',
            handle: '.widget_heading',
            cursor: 'move',
            forceHelperSize: true,
            update:function(e,ui){
                var widgets_order = $(this).sortable('toArray').join();
                $.cookie('widgets_order_' + widgets_id, widgets_order);
            }
        });
        $('.widget').each(function(){
            var $self = $(this);
            var $content = $self.find('.widget_content');
            var widget_id = $self.attr('id');
            if($self.data('collapsed')) $content.slideUp(0);
            var cookie_collapsed = $.cookie(widget_id + '_collapsed');
            if(cookie_collapsed == null)
            {
                if($self.data('collapsed')) $content.slideUp(0);
            }
            else
            {
                if(cookie_collapsed == "true") $content.slideUp(0);
                else $content.slideDown(0);
            }
            if($.cookie(widget_id + '_closed') == 'true') $self.find('.button_close').trigger('click');
        });
    });
}
global.widgets_closed.speed = 300;
global.widgets_closed.init = function()
{
    global.widgets_closed.$ = $('#widgets-closed');
    
    global.widgets_closed.$.on('click', 'li.item', function(){
        var id = $(this).data('id');
        $('#' + id).show();
        $.cookie(id + '_closed', null);
        $(this).remove();
        global.widgets_closed.refresh();
    });
    
    global.widgets_closed.refresh(0);
}
global.widgets_closed.refresh = function(speed)
{
    (global.widgets_closed.$.children('li.item').length > 0) ? global.widgets_closed.show(speed) : global.widgets_closed.hide(speed);
}
global.widgets_closed.hide = function(speed)
{
    global.widgets_closed.$.slideUp((typeof speed == 'undefined') ? global.widgets_closed.speed : speed);
}
global.widgets_closed.show = function(speed)
{
    global.widgets_closed.$.slideDown((typeof speed == 'undefined') ? global.widgets_closed.speed : speed);
}
global.widgets_closed.add = function(text, id)
{
    global.widgets_closed.show();
    global.widgets_closed.$.append('<li class="item" data-id="' + id + '">' + text + '</li>');
}
/**
 * jQuery extension
 */
$.fn.hasAttr = function(name) {  
   return this.attr(name) !== undefined;
};