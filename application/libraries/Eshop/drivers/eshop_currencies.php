<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Eshop_currencies extends CI_Driver {
    
    // Členské premenné
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->CI->cms->model->load_eshop('currencies');
    }
    
    function currency_exists($currency_id = '')
    {
        return $this->CI->e_currencies_model->item_exists($currency_id);
    }
    
    function is_used($currency_id = '')
    {
        if($this->CI->e_currencies_model->item_exists($currency_id))
        {
            $this->CI->cms->model->load_system('langs');
            return (in_array($currency_id, $this->CI->s_langs_model->get_data_in_col('currency_id')));
        }
        else return FALSE;
    }
    
    function get_format($currency_id = '')
    {
        if($this->CI->e_currencies_model->item_exists($currency_id))
        {
            return $this->CI->e_currencies_model->$currency_id->name . ' (' . $this->CI->e_currencies_model->$currency_id->symbol . ')';
        }
        else return '';
    }
    
    function get_select_data()
    {
        $data = array();
        
        foreach($this->CI->e_currencies_model->get_ids() as $currency_id)
        {
            $data[$currency_id] = $this->get_format($currency_id);
        }
        
        return $data;
    }
    
    function get_currency($currency_id = '')
    {
        if(!$this->CI->e_currencies_model->item_exists($currency_id)) return FALSE;
        return $this->CI->e_currencies_model->get_item($currency_id);
    }
    
    function get_current($lang_id = '')
    {
        if($lang_id == '') $lang_id = lang_id();
        $this->CI->cms->model->load_system('langs');
        if(!$this->CI->s_langs_model->item_exists($lang_id)) return FALSE;
        $currency_id = $this->CI->s_langs_model->$lang_id->currency_id;
        return $this->CI->e_currencies_model->get_item($currency_id);
    }
    
}