<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Carabiner 1.45 configuration file.
* CodeIgniter-library for Asset Management
*/

/*
|--------------------------------------------------------------------------
| Script Directory
|--------------------------------------------------------------------------
|
| Path to the script directory.  Relative to the CI front controller.
|
*/

$config['script_dir'] = '';


/*
|--------------------------------------------------------------------------
| Style Directory
|--------------------------------------------------------------------------
|
| Path to the style directory.  Relative to the CI front controller
|
*/

$config['style_dir'] = '';

/*
|--------------------------------------------------------------------------
| Cache Directory
|--------------------------------------------------------------------------
|
| Path to the cache directory. Must be writable. Relative to the CI 
| front controller.
|
*/

$config['cache_dir'] = 'assets/cache/';




/*
* The following config values are not required.  See Libraries/Carabiner.php
* for more information.
*/



/*
|--------------------------------------------------------------------------
| Base URI
|--------------------------------------------------------------------------
|
|  Base uri of the site, like http://www.example.com/ Defaults to the CI 
|  config value for base_url.
|
*/

$config['base_uri'] = '';


/*
|--------------------------------------------------------------------------
| Development Flag
|--------------------------------------------------------------------------
|
|  Flags whether your in a development environment or not. Defaults to FALSE.
|
*/

$config['dev'] = TRUE; //is_localhost();


/*
|--------------------------------------------------------------------------
| Combine
|--------------------------------------------------------------------------
|
| Flags whether files should be combined. Defaults to TRUE.
|
*/

$config['combine'] = TRUE;


/*
|--------------------------------------------------------------------------
| Minify Javascript
|--------------------------------------------------------------------------
|
| Global flag for whether JS should be minified. Defaults to TRUE.
|
*/

$config['minify_js'] = TRUE;


/*
|--------------------------------------------------------------------------
| Minify CSS
|--------------------------------------------------------------------------
|
| Global flag for whether CSS should be minified. Defaults to TRUE.
|
*/

$config['minify_css'] = TRUE;

/*
|--------------------------------------------------------------------------
| Force cURL
|--------------------------------------------------------------------------
|
| Global flag for whether to force the use of cURL instead of file_get_contents()
| Defaults to FALSE.
|
*/

$config['force_curl'] = FALSE;


/*
|--------------------------------------------------------------------------
| Predifined Asset Groups
|--------------------------------------------------------------------------
|
| Any groups defined here will automatically be included.  Of course, they
| won't be displayed unless you explicity display them ( like this: $this->carabiner->display('jquery') )
| See docs for more.
|
*/

$config['groups']['admin_css'] = array(
	
	'css' => array(
            array('admin/css/style.css'),
            array('chosen/css/chosen.css'),
            array('checkbox/css/ezmark.css'),
            
            array('jquery-ui/css/custom-theme/jquery-ui-1.8.18.custom.css'), // old
            
            //array('jquery-ui-new/css/ui-lightness/jquery-ui-1.9.0.custom.css'), // new
            
            array('colorpicker/css/colorpicker.css'),
            array('elfinder/css/elfinder.min.css'),
            array('datatables/media/css/style.css'),
            array('jeegoocontext/style.css'),
            array('codemirror/lib/codemirror.css'),
            array('fancybox/jquery.fancybox.css'),
            array('fancybox/helpers/jquery.fancybox-buttons.css'),
            array('tiptip/tipTip.css')
	)
);

$config['groups']['admin_jq'] = array(
	
	'js' => array(
            array('admin/js/jquery-1.7.1.min.js')
	)
);

$config['groups']['admin_js'] = array(
	
	'js' => array(
            array('admin/js/initialize.js'),
            array('langs/' . lang() . '.js'),
            array('admin/js/modernizr.js'),
            array('admin/js/shortcut.js'),
            
            array('jquery-ui/js/jquery-ui-1.8.18.custom.min.js'), // old
            
            //array('jquery-ui-new/js/jquery-ui-1.9.0.custom.min.js'), // new
            
            array('admin/js/jquery.easing.1.3.js'),
            array('admin/js/jquery.cookie.min.js'),
            array('admin/js/jquery.equalHeights.js'),
            array('admin/js/jquery.disableSelection.js'),
            array('admin/js/jquery.autogrow-textarea.js'),
            array('chosen/js/chosen.jquery.min.js'),
            array('checkbox/js/jquery.ezmark.min.js'),
            array('ckeditor/ckeditor.js'),
            array('colorpicker/js/colorpicker.js'),
            array('colorpicker/js/eye.js'),
            array('colorpicker/js/utils.js'),
            array('colorpicker/js/layout.js'),
            array('elfinder/js/elfinder.min.js'),
            array('elfinder/js/i18n/elfinder.cs.js'),
            array('charts/js/highcharts.js'),
            array('charts/js/themes/custom.js'),
            array('datatables/media/js/jquery.dataTables.min.js'),
            array('jeegoocontext/jquery.jeegoocontext.min.js'),
            array('codemirror/lib/codemirror.js'),
            array('codemirror/mode/xml/xml.js'),
            array('codemirror/mode/javascript/javascript.js'),
            array('codemirror/mode/css/css.js'),
            array('codemirror/mode/clike/clike.js'),
            array('codemirror/mode/php/php.js'),
            array('codemirror/mode/mysql/mysql.js'),
            array('codemirror/mode/xml/xml.js'),
            array('fancybox/jquery.fancybox.pack.js'),
            array('fancybox/helpers/jquery.fancybox-buttons.js'),
            array('fancybox/helpers/jquery.fancybox-media.js'),
            array('tiptip/jquery.tipTip.minified.js'),
            array('admin/js/page.js')
	)
);