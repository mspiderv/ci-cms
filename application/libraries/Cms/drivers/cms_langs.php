<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Langs extends CI_Driver {
    
    protected $CI;
    protected $valid_languages;
    
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->config->load('valid_languages');
        $this->valid_languages = cfg('valid_languages');
    }
    
    function get_lang_name($lang_code_or_id = '')
    {
        if($this->CI->s_langs_model->item_exists($lang_code_or_id)) $lang_code_or_id = $this->CI->s_langs_model->$lang_code_or_id->code;
        return cfg('valid_languages', $lang_code_or_id);
    }
    
    function get_languages()
    {
        return $this->valid_languages;
    }
    
    function get_language_codes()
    {
        return array_keys($this->get_languages());
    }
    
    function get_language_names()
    {
        return array_values($this->get_languages());
    }
    
    function get_available_languages($plus_lang_id = '')
    {
        $available_languages = $this->get_languages();
        
        foreach($this->CI->s_langs_model->get_data_in_col('code') as $lang_id => $lang_code)
        {
            if($lang_id != $plus_lang_id)
            {
                unset($available_languages[$lang_code]);
            }
        }
        
        return $available_languages;
    }
    
    function get_available_language_codes($plus_lang_id = '')
    {
        return array_keys($this->get_available_languages($plus_lang_id));
    }
    
    function get_available_language_names()
    {
        return array_values($this->get_available_languages());
    }
    
    function is_lang_code($lang_code = '')
    {
        return in_array($lang_code, $this->get_language_codes());
    }
    
    function lang_id_code($lang_code = '')
    {
        $langs = array_flip($this->CI->s_langs_model->get_data_in_col('lang'));
        return (isset($langs[$lang_code])) ? $langs[$lang_code] : FALSE;
    }
    
    function is_available_lang_code($lang_code = '', $plus_lang_id = '')
    {
        return $this->is_lang_code($lang_code) && in_array($lang_code, $this->get_available_language_codes($plus_lang_id));
    }
    
}