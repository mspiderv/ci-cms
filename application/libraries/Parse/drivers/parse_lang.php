<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parse_lang extends CI_Driver {
    
    // Členské premenné
    protected $CI;
    protected $language_folder;
    protected $lang_content = array();
    
    function __construct()
    {
        $this->CI = & get_instance();
        $this->language_folder = cfg('folder', 'front');
    }
    
    function get_lang($langfile = '', $idiom = '')
    {
        $idiom = $this->_set_idiom($idiom);
        
        if(!$this->is_loaded($langfile, $idiom))
        {
            if($this->is_readable($langfile, $idiom))
            {
                unset($lang);
                include($this->get_langfile_path($langfile, $idiom));
                $this->lang_content[$idiom][$langfile] = $lang;
            }
            else
            {
                $this->lang_content[$idiom][$langfile] = array();
            }
        }
        
        return $this->lang_content[$idiom][$langfile];
    }
    
    function get_langfile_path($langfile = '', $idiom = '')
    {
        return APPPATH . 'language/' . $idiom . '/' . $this->language_folder . '/' . $this->CI->cms->templates->get_theme_folder() . '/' . $langfile . $this->CI->lang->get_langfile_suffix() . EXT;
        
    }
    
    function is_readable($langfile = '', $idiom = '')
    {
        $idiom = $this->_set_idiom($idiom);
        $path = $this->get_langfile_path($langfile, $idiom);
        return (file_exists($path) && is_readable($path));
    }
    
    function is_loaded($langfile = '', $idiom = '')
    {
        $idiom = $this->_set_idiom($idiom);
        if($langfile == '' || $idiom == '') return FALSE;
        return isset($this->lang_content[$idiom][$langfile]);
    }
    
    // Protected
    
    protected function _set_idiom($idiom = '')
    {
        return ($idiom != '' && lang_exists($idiom)) ? $idiom : lang();
    }
    
}