<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Libraries extends CI_Driver {
    
    protected $CI;
    protected $libraries = array();
    
    function __construct()
    {
        $this->CI = & get_instance();
    }
    
    protected function _get_path($type = '')
    {
        switch($type)
        {
            case 'panel_types':
                return cfg('folder', 'panel_type_libraries');
                break;
            
            case 'page_types':
                return cfg('folder', 'page_type_libraries');
                break;
            
            case 'services':
                return cfg('folder', 'service_libraries');
                break;
            
            default:
                return array();
                break;
        }
    }
    
    function get_libraries($type = '')
    {
        if(isset($this->libraries[$type]))
        {
            return (array)$this->libraries[$type];
        }
        
        $this->CI->load->helper('directory');
        
        $path = APPPATH . 'libraries/' . $this->_get_path($type);
        
        $libraries = array();
        
        foreach((array)directory_map($path, 1) as $file)
        {
            if(get_ext($file) == 'php')
            {
                $libraries[] = cut_ext($file);
            }
        }
        
        $this->libraries[$type] = $libraries;
        
        return $libraries;
    }
    
    function library_exists($library = '', $type = '')
    {
        return (in_array($library, $this->get_libraries($type)));
    }
    
    function load_library($library = '', $type = '', $load_as = '')
    {
        if(strlen($load_as) > 0 && $this->library_exists($library, $type))
        {
            unset($this->CI->$load_as);
            $this->CI->load->library($this->_get_path($type) . '/' . $library, NULL, $load_as);
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    function get_libraries_select_data($type = '')
    {
        $data = (array)$this->get_libraries($type);
        if(count($data) == 0) return array();
        $data = array_combine($data, $data);
        return $data;
    }
    
}