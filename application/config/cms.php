<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['general']['app_name'] = '{{SITE}}';
$config['general']['eshop'] = FALSE;
$config['general']['cache_adapter'] = 'apc'; // apc | memcached | file

$config['profiler']['front'] = FALSE;
$config['profiler']['admin'] = FALSE;

$config['time_format']['normal'] = 'j.n.Y - H:i';
$config['time_format']['normal_date'] = 'j.n.Y';

$config['url']['admin'] = 'admin';
$config['url']['ajax'] = 'ajax';
$config['url']['separator'] = ' | ';
$config['url']['true'] = '1';
$config['url']['iframe'] = 'iframe';
$config['url']['redirect'] = 'redirect';
$config['url']['param'] = 'param';
$config['url']['id_delimiter'] = '-';
$config['url']['action'] = 'action';

$config['url']['id_types']['1'] = 'page';
$config['url']['id_types']['2'] = 'product';
$config['url']['id_types']['3'] = 'category';
$config['url']['id_types']['4'] = 'service';

$config['url']['reserved_aliases'] = array(
    'action',
    'admin',
    'ajax'
);

$config['folder']['panel_type_libraries'] = 'panel_type_libraries';
$config['folder']['page_type_libraries'] = 'page_type_libraries';
$config['folder']['service_libraries'] = 'service_libraries';
$config['folder']['admin'] = 'admin';
$config['folder']['assets'] = 'assets';
$config['folder']['files'] = 'files';
$config['folder']['fields'] = 'fields';
$config['folder']['admin_forms'] = 'admin_forms';
$config['folder']['front'] = 'front';
$config['folder']['categories'] = 'categories';
$config['folder']['services'] = 'services';
$config['folder']['products'] = 'products';
$config['folder']['panels'] = 'panels';
$config['folder']['pages'] = 'pages';
$config['folder']['themes'] = 'themes';
$config['folder']['system'] = 'system';
$config['folder']['action'] = 'action';
$config['folder']['uploads'] = 'uploads';

$config['file']['config_system'] = APPPATH . 'config/system' . EXT;

$config['path']['update_packages'] = APPPATH . 'updates/';

$config['form']['sent'] = 'form_sent';
$config['form']['true'] = '1';

$config['parse']['emails'] = FALSE; // Toto nie je doladene -> nezapinat
$config['parse']['panels'] = TRUE;
$config['parse']['positions'] = TRUE;
$config['parse']['hrefs'] = TRUE;

$config['parser_val']['href'] = 'HREF';
$config['parser_val']['panel'] = 'PANEL';
$config['parser_val']['position'] = 'POSITION';

// $lang['lang_key'] in views
$config['variable']['lang'] = 'lang';

$config['admin']['languages']['sk'] = 'Slovenčina';
$config['admin']['languages']['cz'] = 'Čeština';
$config['admin']['languages']['en'] = 'English';

$config['database_backup']['folder'] = 'db_backups';
$config['database_backup']['backup_filename'] = 'Y-m-d-H-i-s';
$config['database_backup']['backup_filename_format'] = 'sql';

$config['format']['contextmenu_icon'] = 'png';

$config['session_keys']['admin_user_id'] = 'admin_user_id';
$config['session_keys']['admin_lang_id'] = 'admin_lang_id';
$config['session_keys']['admin_temp_url'] = 'admin_temp_url';

$config['cookie_keys']['admin_login_name'] = 'admin_login_name';
$config['cookie_keys']['admin_login_password'] = 'admin_login_password';

$config['expire']['admin_remember_login'] = '1209600'; // 2 týždne

$config['changefreq']['values'] = array('always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never');
$config['changefreq']['default'] = 'monthly';

$config['sitemap']['default_priority'] = 0.5;

$config['price']['max_full'] = 11;
$config['price']['max_decimal'] = 5;
$config['price']['default_decimals'] = 2;
$config['price']['default_round'] = 2;

$config['update_package']['format'] = 'zip';
$config['update_package']['info_file'] = 'info.json';
$config['update_package']['dir_replace'] = 'replace';
$config['update_package']['dir_sql'] = 'sql';
$config['update_package']['dir_scripts'] = 'scripts';

// "m" -> Mesiac; 2 -> 01 - 12
$config['order_id_format']['variables'] = array(
    'm' => 2,
    'd' => 2,
    'o' => 5
);
$config['order_id_format']['required'] = array('o');
$config['order_id_format']['id'] = 'o';

$config['fields']['dynamic'] = array(
    'checkbox',
    'ckeditor',
    'colorpicker',
    'date',
    'filepicker',
    'href',
    'imagepicker',
    'input',
    'internal',
    'password',
    'textarea'
);

/* Jazyky */

$config['languages'] = array('sk', 'cz');
$config['admin_languages'] = array('sk', 'cz');

$config['admin_lang']['sk'] = 'Slovenčina';
$config['admin_lang']['cz'] = 'Čeština';

$config['language'] = 'sk';
$config['admin_language'] = 'sk';

/* PHPass */

$config['phpass']['iteration_count_log2'] = 8;
$config['phpass']['portable_hashes'] = FALSE;
$config['phpass']['salt'] = '0-I08=!8~<c8725001(~^/O ^%v+7x';

/* Dátové typy */

$config['db']['data_types'] = array(
    'INT', 'VARCHAR', 'TEXT', 'DATE',
    'TINYINT', 'SMALLINT', 'MEDIUMINT', 'BIGINT',
    'DECIMAL', 'FLOAT', 'DOUBLE', 'REAL',
    'BIT', 'BOOLEAN', 'SERIAL',
    'DATETIME', 'TIMESTAMP', 'TIME', 'YEAR',
    'CHAR',
    'TINYTEXT', 'MEDIUMTEXT', 'LONGTEXT',
    'BINARY', 'VARBINARY',
    'TINYBLOB', 'MEDIUMBLOB', 'BLOB', 'LONGBLOB',
    'ENUM', 'SET',
    'GEOMETRY', 'POINT', 'LINESTRING', 'POLYGON', 'MULTIPOINT', 'MULTILINESTRING', 'MULTIPOLYGON', 'GEOMETRYCOLLECTION'
);