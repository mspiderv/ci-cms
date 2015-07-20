<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$CI =& get_instance();

/* Výpis dát */
function my_print($mix) {
    echo '<pre>';
    print_r($mix);
    echo '</pre>';
}

function my_dump($mix) {
    echo '<pre>';
    var_dump($mix);
    echo '</pre>';
}

/* Pomocné funkcie */
function remove_from_array($array, $key)
{
    unset($array[array_search($key, $array)]);
    return $array;
}

/* Prípony súborov */
function get_ext($filename)
{
    return (strpos($filename, '.') > -1) ? (array_pop(explode('.', $filename))) : $filename;
}

function cut_ext($filename)
{
    return (strpos($filename, '.') > -1) ? (implode('.', array_slice(explode('.', $filename), 0, -1))) : $filename;
}

/* Vráti názov súboru z celej cesty k súboru */
function get_filename_from_path($path = '')
{
    return end(explode('/', $path));
}

/* Konfiguračné metódy */
function cfg($item, $key = '')
{
    $value = _cfg($item);
    return ($key != '') ? @$value[$key] : $value;
}

function set_cfg($item = '', $value = '')
{
    $CI =& get_instance();
    $CI->config->set_item($item, $value);
    _cfg($item, TRUE);
}

function _cfg($item = '', $delete_item_cache = FALSE)
{
    static $_config_item = array();
    
    if($delete_item_cache)
    {
        unset($_config_item[$item]);
        return;
    }

    if ( ! isset($_config_item[$item]))
    {
        $config =& get_config();

        if ( ! isset($config[$item]))
        {
            return FALSE;
        }
        $_config_item[$item] = $config[$item];
    }

    return $_config_item[$item];
}

/* Funkcie pracujúce s jazykmi */
function set_lang($language = '', $no_validation = FALSE)
{
    if($no_validation || lang_exists($language))
    {
        set_cfg('language', $language);
    }
    else
    {
        show_error('Jazyk <strong>' . $language . '</strong> sa nepodarilo nastaviť, pretože neexistuje.');
    }
}

function lang_exists($language = '')
{
    $CI =& get_instance();
    $CI->cms->model->load_system('langs');
    return (in_array($language, $CI->s_langs_model->get_data_in_col('lang')));
}

function lang_id_exists($language_id = '')
{
    $CI =& get_instance();
    $CI->cms->model->load_system('langs');
    return ($CI->s_langs_model->item_exists($language_id));
}

function set_lang_id($language_id = '')
{
    $CI =& get_instance();
    $CI->cms->model->load_system('langs');
    
    if($CI->s_langs_model->item_exists($language_id))
    {
        set_lang($CI->s_langs_model->get_item_data($language_id, 'lang'), TRUE);
    }
    else
    {
        show_error('Jazyk s id <strong>' . $language_id . '</strong> sa nepodarilo nastaviť, pretože neexistuje.');
    }
}

function lang()
{
    return cfg('language');
}

function admin_user_lang()
{
    static $admin_user_lang = NULL;

    if($admin_user_lang == NULL)
    {
        $CI =& get_instance();
        $CI->load->driver('admin');
        $admin_user_lang = $CI->admin->get_admin_user_lang();
    }

    return $admin_user_lang;
}

function default_admin_lang()
{
    return cfg('admin_language');
}

function admin_lang_select_data()
{
    $data = array();
    
    foreach(cfg('admin_languages') as $admin_language)
    {
        $data[$admin_language] = cfg('admin_lang', $admin_language);
    }
    
    return $data;
}

function lang_id($lang = '')
{
    if(strlen($lang) && !lang_exists($lang)) show_error('Nepodarilo sa získať ID jazyka <strong>' . $lang . '</strong>, pretože neexistuje.');
    $CI =& get_instance();
    $CI->cms->model->load_system('langs');
    $langs = array_flip($CI->s_langs_model->get_data_in_col('lang'));
    return @$langs[(strlen($lang)) ? $lang : lang()];
}

function default_lang()
{
    static $default_lang = NULL;
    
    if($default_lang == NULL)
    {
        $CI =& get_instance();
        $CI->cms->model->load_system('langs');
        $default_lang = $CI->s_langs_model->get_item_data(default_lang_id(), 'lang');
    }
    
    return $default_lang;
}

function default_lang_id()
{
    // TODO: checkovat ci taky jazyk existuje a je validny inak vypisat chybu (asi pomocou show_error())
    
    return db_config('default_lang_id');
}

function __($line = '', $idiom = '')
{
    static $cache = array();
    
    $cache_key = $line . $idiom;
    
    if(!isset($cache[$cache_key]))
    {
        $CI =& get_instance();
        $line = $CI->router->directory . $CI->router->fetch_class() . '_' . $line;
        $cache[$cache_key] = ll($line, _set_idiom($idiom));
    }
    
    return $cache[$cache_key];
}

// Vráti konkétnu hodnotu z jazykového poľa
function ll($line = '', $idiom = '')
{
    $CI =& get_instance();
    return $CI->lang->line($line, _set_idiom($idiom));
}

function _set_idiom($idiom = '')
{
    $CI =& get_instance();
    if(strlen($idiom) > 0) return $idiom;
    else return ($CI->uri->segment(1) == cfg('url', 'admin') || $CI->uri->segment(1) == cfg('url', 'ajax')) ? admin_user_lang() : lang();
}

function admin_lang()
{
    $CI =& get_instance();
    
    $session_admin_lang_id = $CI->session->userdata(cfg('session_keys', 'admin_lang_id'));
    if(lang_id_exists($session_admin_lang_id))
    {
        $CI->cms->model->load_system('langs');
        return $CI->s_langs_model->$session_admin_lang_id->lang;
    }
    else
    {
        return lang();
    }
}

function load_lang($langfile = '', $idiom = '')
{
    $CI =& get_instance();
    return $CI->lang->load($langfile, $idiom);
}

/* Vráti URL adresu k adminu */
function admin_url($url = '')
{
    $CI =& get_instance();
    if(substr($url, 0, 1) == '~')
    {
        $url = $CI->router->directory . $CI->router->fetch_class() . substr($url, 1);
    }
    else
    {
        $url = cfg('url', 'admin') . '/' . $url;
    }
    return site_url($url);
}

/* POST, GET, GET_POST */

function post($key = '')
{
    $CI =& get_instance();
    return $CI->input->post($key);
}

function get($key = '')
{
    $CI =& get_instance();
    return $CI->input->get($key);
}

function get_post($key = '')
{
    $CI =& get_instance();
    return $CI->input->get_post($key);
}

function cookie($key = '')
{
    $CI =& get_instance();
    return $CI->input->cookie($key);
}

/* Presmerovacie funkcie */
function admin_redirect($url = '~')
{
    $CI =& get_instance();
    if(substr($url, 0, 1) == '~')
    {
        $url = $CI->router->directory . $CI->router->fetch_class() . substr($url, 1);
    }
    else
    {
        $url = cfg('url', 'admin') . '/' . $url;
    }
    
    return redirect($url);
}

/* Funkcie generujúce odkazy */
function admin_anchor($uri = '', $title = '', $confirm = '', $attributes = array())
{
    $CI =& get_instance();
    
    $iframe_prefix = cfg('url', 'iframe') . ':';
    $iframe = (substr($uri, 0, strlen($iframe_prefix)) == $iframe_prefix);
    if($iframe) $uri = substr($uri, strlen($iframe_prefix));
    
    $title = (string) $title;

    $site_url = admin_url($uri);
    
    if($title == '')
    {
        $title = $site_url;
    }
    
    $iframe_url_suffix = '?' . cfg('url', 'iframe') . '=' . cfg('form', 'true');
    
    if($iframe && substr($site_url, -(strlen($iframe_url_suffix))) != $iframe_url_suffix) $site_url .= $iframe_url_suffix;
    
    if($confirm != '')
    {
        $data_array = @explode('|', $confirm, 2);

        $data_text = @$data_array[0];
        $data_title = @$data_array[1];

        if(strlen($data_title) == 0)
        {
            $CI =& get_instance();
            $CI->lang->load('admin/general');
            $data_title = ll('admin_general_warning');
        }
        
        @$attributes['class'] .= ' confirm_link';
        @$attributes['data-page'] .= ' confirm_link';
        $attributes['data-href'] = $site_url;
        $attributes['data-title'] = $data_title;
        $attributes['data-text'] = $data_text;
        $href = "#";
    }
    else
    {
        $href = $site_url;
    }
    
    if($iframe && $CI->input->get(cfg('url', 'iframe')) != cfg('form', 'true'))
    {
        @$attributes['class'] .= ' fancybox_iframe';
        @$attributes['data-page'] .= ' fancybox_iframe';
    }
    
    $attributes = _attributes_to_string($attributes);
    
    return '<a href="'.$href.'"'.$attributes.'>'.$title.'</a>';
}

/* Informačné funkcie */
function form_sent()
{
    static $form_sent = NULL;
    
    if($form_sent == NULL)
    {
        $CI =& get_instance();
        $form_sent = ($CI->input->post(cfg('form', 'sent')) == cfg('form', 'true'));
    }
    
    return $form_sent;
}

function form_sent_label($label = '')
{
    static $sent_labels = array();
    
    if(in_array($label, $sent_labels)) return TRUE;
    
    $CI =& get_instance();
    if($CI->input->post($label) == cfg('form', 'true'))
    {
        $sent_labels[] = $label;
        return TRUE;
    }
    else
    {
        return FALSE;
    }
}

/* Funkcie určené na načívavanie tried polí v administrácii (/application/fields/...) */
function admin_form_load_field_class($field = '')
{
    $CI =& get_instance();
    
    if(is_array($field))
    {
        foreach($field as $one_field)
        {
            $CI->admin->form->load_field_class($one_field);
        }
    }
    
    else
    {
        $CI->admin->form->load_field_class($field);
    }
}

/* Konfiguračné DB fuknkcie */
function db_config($key, $value = NULL)
{
    static $config_keys = NULL;
    
    $CI =& get_instance();

    // Získa ID položky

    $CI->cms->model->load_system('config');
    if($config_keys == NULL) $config_keys = $CI->s_config_model->get_data_in_col('key');
    
    $multilang = FALSE;
    $lang = '';
    
    if(substr($key, 0, 1) == '_')
    {
        $multilang = TRUE;
        $lang = '';
    }
    else
    {
        $CI->cms->model->load_system('langs');
        
        foreach($CI->s_langs_model->get_data_in_col('lang') as $lang_temp)
        {
            $lang_prefix = $lang_temp . '_';
            $lang_temp_length = strlen($lang_prefix);
            
            if(substr($key, 0, $lang_temp_length) == $lang_prefix)
            {
                $key = substr($key, $lang_temp_length - 1);
                $lang = $lang_temp;
                $multilang = TRUE;
                break;
            }
        }
    }
    
    $col = ($multilang) ? $lang . '_value' : 'value';
    
    if(in_array($key, $config_keys))
    {
        // Položka existuje
        
        $config_item_id = array_search($key, $config_keys);

        if(isset($value))
        {
            // Nastavenie existujúcej položky
            $CI->s_config_model->set_item_data($config_item_id, array($col => $value));
            return $value;
        }
        else
        {
            // Vrátenie existujúcej položky
            return $CI->s_config_model->get_item_data($config_item_id, $col);
        }
    }
    else
    {
        // Položka neexistuje
        if(isset($value))
        {
            // Vytvorenie položky
            $CI->s_config_model->add_item(array('key' => $key, $col => $value));
            return $value;
        }
        else
        {
            // Vrátenie prázdneho reťazca
            db_config($key, '');
            return '';
        }
    }
}

function db_config_bool($key, $value = NULL)
{
    if(isset($value))
    {
        return db_config($key, (is_bool($value)) ? $value : ((int)($value > 0)));
    }
    else
    {
        return (intval(db_config($key)) > 0);
    }
}

/* Combinations */
function combinations($arr, $keep_keys = FALSE, &$out = array(), $codes = array(), $pos = 0, $big_arr = NULL) {
    if(count($arr))
    {
        if($keep_keys && !is_array($big_arr)) $big_arr= array_keys($arr);
        $first_count = count(current($arr));
        foreach(current($arr) as $item)
        {
            $tmp = $arr;
            $codes[$keep_keys ? $big_arr[$pos] : $pos] = $item;
            $tarr = array_shift($tmp);
            $pos++;
            combinations($tmp, $keep_keys, $out, $codes, $pos, $big_arr);
            $pos--;
        }
    }
    else
    {
        $out[] = $codes;
    }
    return $out;
}

/* Vráti GET premennú z URL */
function url_param()
{
    return get(cfg('url', 'param'));
}

/* Vráti boolean hodnotu podľa toho či sa parameter rovná formulárovému TRUE */
function is_form_true($value = '')
{
    return $value == cfg('form', 'true');
}

/* Odkazy */
function href_is_hp($type = '', $value = '')
{
    static $href_hp = NULL;
    
    if($href_hp == NULL)
    {
        $href_hp = get_href(db_config('homepage'));
    }
    
    return (@$href_hp['type'] == $type && @$href_hp['value'] == $value);
}

function is_href_set($href= '')
{
    return ($href != '{"type":"empty","value":""}' && $href != '');
}

function href_page($page_id = '', $lang = '')
{
    if(href_is_hp('page', $page_id)) return site_url($lang);
    $CI =& get_instance();
    $CI->load->driver('parse');
    return site_url($CI->parse->page->get_page_url($page_id, $lang));
}

function redirect_page($page_id = '', $lang = '')
{
    redirect(href_page($page_id, $lang));
}

function href_product($product_id = '', $lang = '')
{
    if(href_is_hp('product', $product_id)) return site_url($lang);
    $CI =& get_instance();
    $CI->load->driver('parse');
    return site_url($CI->parse->product->get_product_url($product_id, $lang));
}

function redirect_product($product_id = '', $lang = '')
{
    redirect(href_product($product_id, $lang));
}

function href_category($category_id = '', $lang = '')
{
    if(href_is_hp('category', $category_id)) return site_url($lang);
    $CI =& get_instance();
    $CI->load->driver('parse');
    return site_url($CI->parse->category->get_category_url($category_id, $lang));
}

function redirect_category($category_id = '', $lang = '')
{
    redirect(href_category($category_id, $lang));
}

function href_service($service_id = '', $lang = '')
{
    if(href_is_hp('service', $service_id)) return site_url($lang);
    $CI =& get_instance();
    $CI->load->driver('parse');
    return site_url($CI->parse->service->get_service_url($service_id, $lang));
}

function redirect_service($service_id = '', $lang = '')
{
    redirect(href_service($service_id, $lang));
}

function href_label($label_code = '')
{
    $CI =& get_instance();
    $CI->cms->model->load_system('labels');
    $CI->s_labels_model->where('code', '=', $label_code);
    $id = $CI->s_labels_model->get_first_id();
    if(!$CI->s_labels_model->item_exists($id)) return '';
    return href($CI->s_labels_model->get_item_data($id, 'href'));
}

function redirect_label($label_code = '', $extra = '')
{
    redirect(href_label($label_code) . (($extra == '') ? '' : $extra));
}

/* Vráti hodnotu href fieldu ako pole */
function get_href($field = '', $default = '')
{
    if(substr($field, 0, 1) == '{') $result = (array)json_decode($field);
    else $result = (array)@json_decode(set_value($field, $default));
    return (json_last_error() == 0) ? $result : FALSE;
}

/* Vráti boolean hodnotu podľa toho či je zadaný odkaz aktívny */
function active_href($href = '')
{
    return (href($href) == site_url(uri_string_without_os()));
}

function active_label($label = '')
{
    return (href_label($label) == site_url(uri_string_without_os()));
}

function active_page($page_id = '')
{
    return (href_page($page_id) == site_url(uri_string_without_os()));
}

// TODO: active_product() atd

function uri_string_without_os()
{
    $CI =& get_instance();
    $CI->load->driver('parse');
    
    $other_segments = $CI->parse->url->get_other_segments(TRUE);
    $other_segments_length = strlen($other_segments);
    
    return ($other_segments_length == 0) ? uri_string() : substr(uri_string(), 0, -($other_segments_length+1));
}

/* Vráti odkaz */
function href($href = '', $lang = '')
{
    $href = get_href($href);
    
    return href_by_type(@$href['type'], @$href['value'], $lang);
}

function href_by_type($type = '', $value = '', $lang = '')
{
    switch($type)
    {
        case 'page':
            return href_page($value, $lang);
            break;
        
        case 'product':
            return href_product($value, $lang);
            break;
        
        case 'category':
            return href_category($value, $lang);
            break;
        
        case 'service':
            return href_service($value, $lang);
            break;
        
        case 'url':
            return prep_url($value);
            break;
        
        default:
            return '#';
            break;
    }
}

function redirect_by_type($type = '', $value = '', $lang = '')
{
    switch($type)
    {
        case 'page':
            return redirect_page($value, $lang);
            break;
        
        case 'product':
            return redirect_product($value, $lang);
            break;
        
        case 'category':
            return redirect_category($value, $lang);
            break;
        
        case 'service':
            return redirect_service($value, $lang);
            break;
        
        case 'url':
            return prep_url($value);
            break;
        
        default:
            return '#';
            break;
    }
}

function href_action($action = '')
{
    return site_url(cfg('url', 'action') . '/' . $action);
}

/* Zistí, či je prijatý reťazec validná URL */
function is_url($string)
{
    $pattern = '@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@';
    return preg_match($pattern, $string);
}

/* Pridá skryté pole do formulára */
function add_form_label($label = '')
{
    return form_hidden($label, cfg('form', 'true'));
}

/* Zistí, či sme v administrácií */
function is_admin()
{
    static $is_admin = NULL;
    
    if($is_admin == NULL)
    {
        $CI =& get_instance();
        $is_admin = ($CI->uri->segment(1) == cfg('url', 'admin'));
    }
    
    return $is_admin;
}

/* Zistí, či je nejaký administrátor prihlásený */
function admin_is_logged()
{
    static $admin_is_logged = NULL;
    
    if($admin_is_logged == NULL)
    {
        $CI =& get_instance();
        $CI->load->driver('admin');
        $admin_is_logged = $CI->admin->auth->is_logged();
    }
    
    return (bool)$admin_is_logged;
}

/* Z reťazca spraví validný názov súboru */
function sanitize_file_name($filename)
{
    $special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}");
    $filename = str_replace($special_chars, '', $filename);
    $filename = trim($filename, '.-_');
    return $filename;
}

/* Skopíruje jeden adresár do druhého */
function dir_copy($path, $dest)
{
    if(is_dir($path))
    {
        @mkdir($dest);
        @chmod($dest, 0777);
        $objects = scandir($path);
        $status = TRUE;
        if(sizeof($objects) > 0)
        {
            foreach($objects as $file)
            {
                if($file == "." || $file == "..") continue;
                @chmod($dest.'/'.$file, 0777);
                if(is_dir($path.'/'.$file))
                {
                    if(!dir_copy($path.'/'.$file, $dest.'/'.$file)) $status = FALSE;
                }
                else
                {
                    if(!copy($path.'/'.$file, $dest.'/'.$file)) $status = FALSE;
                }
            }
        }
        return $status;
    }
    elseif(is_file($path))
    {
        return copy($path, $dest);
    }
    else
    {
        return false;
    }
}
    
function dir_is_really_writable($path)
{
    if(is_dir($path))
    {
        $objects = scandir($path);
        $status = TRUE;
        if(sizeof($objects) > 0)
        {
            foreach($objects as $file)
            {
                if($file == "." || $file == "..") continue;
                if(!dir_is_really_writable($path.'/'.$file)) $status = FALSE;
            }
        }
        return $status;
    }
    else
    {
        return is_really_writable($path);
    }
}

function delete_dir($path)
{
    return is_file($path) ? @unlink($path) : array_map(__FUNCTION__, glob($path.'/*')) == @rmdir($path);
}

function explode_sql($query = '')
{
    $query = trim($query);
    if(substr($query, -1) != ';') $query .= ';';
    preg_match_all("/(?>[^;']|(''|(?>'([^']|\\')*[^\\\]')))+;/ixU", $query, $matches, PREG_SET_ORDER);
    $result = '';
    foreach($matches as $match) $result[] = substr($match[0], 0, -1);
    return $result;
}

function replace_vars($text = '', $vars = array())
{
    foreach($vars as $var => $value)
    {
        $text = str_replace('{' . $var . '}', $value, $text);
    }
    
    return $text;
}

/* Obrázky */
function get_image_max($width, $height, $url)
{
    return _get_image($width, $height, $url, FALSE);
}

function get_image_min($width, $height, $url)
{
    return _get_image($width, $height, $url, TRUE);
}

function _get_image($width, $height, $url, $minimum = FALSE)
{
    // Odreže / na začiatku URL
    if(substr($url, 0, 1) == '/')
    {
        $url = substr($url, 1);
    }
    
    if(substr($url, 0, strlen(base_url())) == base_url())
    {
        $url = substr($url, strlen(base_url()));
    }
    
    if(substr($url, 0, 1) == '/') $url = substr($url, 1);
    
    //return site_url('get_image.php?img=' . $url . '&sirka=' . (int)$width . '&vyska=' . (int)$height . '&minimum=' . (int)$minimum);
    return site_url('image-' . (($minimum) ? 'min' : 'max') . '/' . $width . '/' . $height . '/' . $url); // pomocou htaccess
}