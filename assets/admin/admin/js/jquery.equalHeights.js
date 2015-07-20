/*-------------------------------------------------------------------- 
 * JQuery Plugin: "EqualHeights" & "EqualWidths"
 * by:	Scott Jehl, Todd Parker, Maggie Costello Wachs (http://www.filamentgroup.com)
 *
 * Copyright (c) 2007 Filament Group
 * Licensed under GPL (http://www.opensource.org/licenses/gpl-license.php)
 *
 * Description: Compares the heights or widths of the top-level children of a provided element 
   and sets their min-height to the tallest height (or width to widest width). Sets in em units
   by default if pxToEm() method is available.
 * Dependencies: jQuery library, pxToEm method	(article: http://www.filamentgroup.com/lab/retaining_scalable_interfaces_with_pixel_to_em_conversion/)							  
 * Usage Example: $(element).equalHeights();
   Optional: to set min-height in px, pass a true argument: $(element).equalHeights(true);
 * Version: 2.0, 07.24.2008
 * Changelog:
 *  08.02.2007 initial Version 1.0
 *  07.24.2008 v 2.0 - added support for widths
--------------------------------------------------------------------*/

$.fn.equalHeights = function() {
    $(this).each(function(){
        var currentTallest = 0;
        $(this).children().each(function(){
            $(this).css('height', '');
            if ($(this).height() > currentTallest) {
                currentTallest = $(this).height();
            }
        });
        $(this).children().height(currentTallest);
    });
    return this;
};

// just in case you need it...
$.fn.equalWidths = function() {
    $(this).each(function(){
        var currentWidest = 0;
        $(this).children().each(function(){
            $(this).css('width', '');
            if($(this).width() > currentWidest) {
                currentWidest = $(this).width();
            }
        });
        $(this).children().height(currentWidest);
    });
    return this;
};