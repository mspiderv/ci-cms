<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Lang extends CI_Lang {
    
    protected $lang_content = array();
    protected $loaded_langs = array();
    protected $langfile_suffix = '_lang';
    
    function load($langfile = '', $idiom = '')
    {
        if(strlen($idiom) == 0)
        {
            $config =& get_config();
            $idioms = (array)@$config['languages'];
        }
        else
        {
            $idioms = array($idiom);
        }
        
        foreach($idioms as $cur_idiom)
        {
            if(!$this->is_loaded($langfile, $cur_idiom))
            {
                if($this->is_readable($langfile, $cur_idiom))
                {
                    unset($lang);
                    include($this->get_langfile_path($langfile, $cur_idiom));
                    $this->lang_content[$cur_idiom] = array_merge((array)@$this->lang_content[$cur_idiom], (array)@$lang);
                    $this->loaded_langs[$cur_idiom][] = $langfile;
                }
                /*else
                {
                    show_error('Unable to load the requested language file: ' . $this->get_langfile_path($langfile, $cur_idiom));
                }*/
            }
        }
    }
    
    function line($line = '', $idiom = '')
    {
        $idiom = $this->_set_idiom($idiom);
        return (isset($this->lang_content[$idiom][$line])) ? $this->lang_content[$idiom][$line] : FALSE;
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
        return (in_array($langfile, (array)@$this->loaded_langs[$idiom]));
    }
    
    function get_lang_content()
    {
        return $this->lang_content;
    }
    
    function set_langfile_suffix($langfile_suffix)
    {
        $this->langfile_suffix = $langfile_suffix;
    }
    
    function get_langfile_suffix()
    {
        return $this->langfile_suffix;
    }
    
    function get_langfile_path($langfile = '', $idiom = '')
    {
        $idiom = $this->_set_idiom($idiom);
        if(strlen($idiom)) $idiom .= '/';
        return APPPATH . 'language/' . $idiom . $langfile . $this->get_langfile_suffix() . EXT;
    }
    
    // Protected
    
    protected function _set_idiom($idiom = '')
    {
        $config =& get_config();
        return (strlen($idiom)) ? $idiom : @$config['language'];
    }
    
}