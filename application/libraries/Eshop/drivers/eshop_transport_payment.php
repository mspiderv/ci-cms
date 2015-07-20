<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Eshop_transport_payment extends CI_Driver {

    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->CI->cms->model->load_eshop('transports');
        $this->CI->cms->model->load_eshop('payments');
    }
    
    function get_transports_select_data($lang_id = '')
    {
        $this->CI->e_transports_model->where('lang_id', '=', ($lang_id == '') ? lang_id() : $lang_id);
        return $this->CI->e_transports_model->get_data_in_col('name');
    }
    
    function transport_exists($transport_id = '', $lang_id = '')
    {
        $this->CI->e_transports_model->where('id', '=', $transport_id);
        $this->CI->e_transports_model->where('lang_id', '=', ($lang_id == '') ? lang_id() : $lang_id);
        return ((int)$this->CI->e_transports_model->get_rows() > 0);
    }
    
    function payment_exists($payment_id = '', $lang_id = '')
    {
        if($this->CI->e_payments_model->item_exists($payment_id))
        {
            return $this->transport_exists($this->CI->e_payments_model->$payment_id->transport_id, $lang_id);
        }
        else
        {
            return FALSE;
        }
    }
    
}