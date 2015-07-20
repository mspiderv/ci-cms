<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cms extends CI_Driver_Library {
    
    protected $CI;
    protected $valid_drivers = array(
        'cms_model_conditions',
        'cms_model',
        'cms_langs',
        'cms_fields',
        'cms_libraries',
        'cms_templates',
        'cms_panels',
        'cms_pages',
        'cms_lists',
        'cms_export',
        'cms_categories',
        'cms_services',
        'cms_positions',
        'cms_resources',
        'cms_menus',
        'cms_updates'
    );
    protected $fields = NULL;
    protected $referring_fields = array(
        'service',
        'product',
        'product_category',
        'page',
        'page_category',
        'menu',
        'panel',
        'list',
        'select_email_wrap',
        'select_email'
    );
    protected $referring_fields_only_validation = array(
        'page_with_type',
        'panel_with_type',
        'list_with_type'
    );
    
    function  __construct()
    {
        $this->CI =& get_instance();
    }
    
    function set_constants()
    {
        if(!defined('BASE')) define('BASE', base_url());
        if(!defined('ASSETS')) define('ASSETS', base_url() . cfg('folder', 'assets') . '/');
        if(!defined('ADMIN_ASSETS')) define('ADMIN_ASSETS', base_url() . cfg('folder', 'assets') . '/' . cfg('folder', 'admin') . '/');
        if(!defined('FILES')) define('FILES', base_url() . cfg('folder', 'assets') . '/' . cfg('folder', 'files') . '/');
        if(!defined('THEME_ASSETS')) define('THEME_ASSETS', base_url() . cfg('folder', 'assets') . '/' . cfg('folder', 'themes') . '/' . $this->CI->cms->templates->get_theme_folder() . '/');
    }
    
    function load_system_view($view = '', $data = array(), $return = FALSE)
    {
        return $this->CI->load->view(cfg('folder', 'system') . '/' . $view, $data, $return);
    }
    
    function load_action_view($view = '', $data = array(), $return = FALSE)
    {
        return $this->CI->load->view(cfg('folder', 'action') . '/' . $view, $data, $return);
    }
    
    function load_cache()
    {
        $this->CI->load->driver('cache', array('adapter' => cfg('general', 'cache_adapter')));
    }
    
    function get_fields()
    {
        if($this->fields == NULL)
        {
            $this->CI->load->helper('directory');
            $path = APPPATH . cfg('folder', 'fields') . '/';
            $fields = array();

            foreach((array)directory_map($path, 1) as $file)
            {
                if(get_ext($file) == 'php')
                {
                    $fields[] = cut_ext($file);
                }
            }
            
            $this->fields = (array)$fields;
        }
        
        return $this->fields;
    }
    
    function field_exists($field = '')
    {
        return in_array($field, $this->get_fields());
    }
    
    function get_dynamic_fields()
    {
        return array_intersect($this->get_fields(), cfg('fields', 'dynamic'));
    }
    
    function dynamic_field_exists($field = '')
    {
        return in_array($field, $this->get_dynamic_fields());
    }
    
    function get_referring_fields()
    {
        return $this->referring_fields;
    }
    
    function referring_field_exists($field = '')
    {
        $pos = strpos($field, '[');
        if($pos > -1) $field = substr($field, 0, $pos);
        
        return (in_array($field, $this->referring_fields) || in_array($field, $this->referring_fields_only_validation));
    }
    
    function try_redirect()
    {
        $this->CI->cms->model->load_system('redirects');
        
        $url = uri_string() . ((strlen(@$_SERVER['QUERY_STRING']) > 0) ? '?' . @$_SERVER['QUERY_STRING'] : '');
        
        $this->CI->s_redirects_model->where('from', '=', $url);
        $this->CI->s_redirects_model->where('active', '>', '0');
        
        $redirect_id = $this->CI->s_redirects_model->get_first_id();
        
        if($this->CI->s_redirects_model->item_exists($redirect_id))
        {
            redirect($this->CI->s_redirects_model->$redirect_id->to);
        }
    }
    
    function try_block_ip()
    {
        $this->CI->cms->model->load_system('banned_ips');
        
        $this->CI->s_banned_ips_model->where('active', '>', '0');
        
        if(in_array($this->CI->input->ip_address(), $this->CI->s_banned_ips_model->get_data_in_col('ip')))
        {
            show_error('Your IP is banned!', $status_code = 403, $heading = 'Forbidden');
        }
    }
    
    function save_sort($table = '', $items = array(), $sort = array())
    {
        if(($count = count((array)$items)) != count((array)$sort)) return 'error_1';
        $this->CI->cms->model->load_auto($table, 'ajax_sorting');
        if(!$this->CI->ajax_sorting_model->is_ordering()) return 'error_2';

        $this->CI->ajax_sorting_model->reinit = FALSE;
        $order_col = $this->CI->ajax_sorting_model->get_col('order');

        for($i = 0; $i < $count; $i++)
        {
            $id = intval($sort[$i]);
            
            if($this->CI->ajax_sorting_model->item_exists($id))
            {
                $this->CI->ajax_sorting_model->set_item_data(
                    $id,
                    array(
                        $order_col => $this->CI->ajax_sorting_model->get_item_data(
                                intval($items[$i]),
                                $order_col
                            )
                        )
                );
            }
        }
        
        $this->CI->ajax_sorting_model->recache_sortable();
        
        return TRUE;
    }
    
    function send_mail($to = '', $email_id = '', $vars = array(), $from = array())
    {
        $this->CI->cms->model->load_system('emails');
        
        if(!$this->CI->s_emails_model->item_exists($email_id)) return FALSE;
        
        $this->CI->cms->model->load_system('email_wraps');
        
        $email_data = $this->CI->s_emails_model->$email_id;
        $email_wrap_data = (intval($email_data->email_wrap_id) > 0) ? $this->CI->s_email_wraps_model->get_item($email_data->email_wrap_id) : FALSE;
        
        if(!isset($from['name'])) $from['name'] = db_config('email_from_name');
        if(!isset($from['email'])) $from['email'] = db_config('email_from_email');
        
        $this->CI->load->library('email');
        
        $this->CI->email->from($from['name'], $from['email']);
        $this->CI->email->to($to);
        
        // E-mail copies
        $this->CI->cms->model->load_system('email_copies');
        
        foreach($this->CI->s_email_copies_model->get_data() as $copy_email)
        {
            if($copy_email->hidden) $this->CI->email->bcc($copy_email->email);
            else $this->CI->email->cc($copy_email->email);
        }

        $message = replace_vars($email_data->_content, $vars);
        
        if($email_wrap_data != FALSE)
        {
            $message = replace_vars($email_wrap_data->_content, array('content' => $message));
        }
        
        $this->CI->email->subject($email_data->_subject);
        $this->CI->email->message($message);
        
        $this->CI->email->send();
    }
    
}