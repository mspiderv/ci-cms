<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Templates extends CI_Driver {
    
    // TODO: Mozno dorobit cachovanie (neviem ci multirequestove alebo len instancne)
    
    protected $CI;
    protected $theme_id;
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->CI->cms->model->load_system('themes');
        
        $this->theme_id = db_config('active_theme_id');
    }
    
    function set_theme($theme_id = '')
    {
        if($this->CI->s_themes_model->item_exists($theme_id)) $this->theme_id = $theme_id;
    }
    
    function get_theme()
    {
        return $this->theme_id;
    }
    
    function get_theme_folder($theme_id = '')
    {
        if($theme_id == '') $theme_id = $this->theme_id;
        
        if(!$this->CI->s_themes_model->item_exists($theme_id)) return '';
        else return $this->CI->s_themes_model->$theme_id->folder;
    }
    
    function get_favicon($theme_id = '')
    {
        if($theme_id == '') $theme_id = $this->theme_id;
        
        if(!$this->CI->s_themes_model->item_exists($theme_id)) return '';
        else return $this->CI->s_themes_model->$theme_id->favicon;
    }
    
    function get_templates($type = '')
    {
        $theme_folder = $this->get_theme_folder();
        if($theme_folder == '') return array();
        
        $this->CI->load->helper('directory');
        
        $path = APPPATH . 'views/' . cfg('folder', 'front') . '/' . $theme_folder . '/';
        
        switch($type)
        {
            case 'categories':
                $path .= cfg('folder', 'categories');
                break;
            
            case 'products':
                $path .= cfg('folder', 'products');
                break;
            
            case 'panels':
                $path .= cfg('folder', 'panels');
                break;
            
            case 'pages':
                $path .= cfg('folder', 'pages');
                break;
            
            case 'services':
                $path .= cfg('folder', 'services');
                break;
            
            default:
                return array();
                break;
        }
        
        $templates = array();
        
        foreach((array)directory_map($path, 1) as $file)
        {
            if(get_ext($file) == 'php')
            {
                $templates[] = cut_ext($file);
            }
        }
        
        return $templates;
    }
    
    function get_templates_select_data($type = '', $default_tpl = FALSE)
    {
        $data = (array)$this->get_templates($type);
        if(count($data) == 0) return ($default_tpl) ? array('' => ll('admin_general_default_template')) : array();
        $data = array_combine($data, $data);
        
        if($default_tpl)
        {
            $data = array_merge(array('' => ll('admin_general_default_template')), $data);
        }
        
        return (count($data) > 0) ? $data : array('');
    }
    
}