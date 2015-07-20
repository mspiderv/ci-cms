<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Eshop_orders extends CI_Driver {
    
    // Členské premenné
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->CI->cms->model->load_eshop('orders');
        $this->CI->cms->model->load_eshop('order_states');
        $this->CI->cms->model->load_eshop('order_data');
    }
    
    function get_new_order_id()
    {
        // Vráti celé ID objednávky vrátane mesiaca a dna a pod.
        $format = db_config('order_id_format');
    }
    
    function get_new_order_id_db()
    {
        // Vráti len ID objednávky
        $format = preg_split('//', strtolower(db_config('order_id_format')), -1, PREG_SPLIT_NO_EMPTY);
        $variables = cfg('order_id_format', 'variables');
        $start = 0;
        
        foreach($format as $variable)
        {
            if($variable == cfg('order_id_format', 'id')) break;
            $start += intval(@$variables[$variable]);
        }
        
        $order_ids = array();
        $id_variable_length = intval(@$variables[cfg('order_id_format', 'id')]);
        
        foreach($this->CI->e_orders_model->get_ids() as $order_id)
        {
            $order_ids[] = intval(substr($order_id, $start, $id_variable_length));
        }
        
        $new_id = intval(max($order_ids) + 1);
        while($this->CI->e_orders_model->item_exists($new_id)) $new_id++;
        return $new_id;
    }
    
    /* Obsah objednávky */
    
    function get_order_price($order_id = '', $with_tax = FALSE, $coupon = TRUE, $payment_transport = TRUE, $parse = FALSE, $with_symbol = TRUE)
    {
        if(!$this->CI->e_orders_model->item_exists($order_id)) return FALSE;
        
        $price = 0;
        
        $this->CI->e_order_data_model->where('order_id', '=', $order_id);
        
        // Položky v objednávke
        foreach($this->CI->e_order_data_model->get_data() as $order_data)
        {
            $item_price = $order_data->price * $order_data->quantity;
            if($with_tax) $item_price *= ($order_data->tax + 100) / 100;
            $price += $item_price;
        }
        
        // Kupón
        if($coupon)
        {
            $price *= (100 - $this->CI->e_orders_model->$order_id->coupon_discount) / 100;
        }
        
        // Doprava a platba
        if($payment_transport)
        {
            $price += $this->CI->e_orders_model->$order_id->transport_price;
            $price += $this->CI->e_orders_model->$order_id->payment_price;
        }
        
        // Parsovanie
        if($parse)
        {
            $price = parse_price($price, $this->CI->e_orders_model->$order_id->currency_decimals, $this->CI->e_orders_model->$order_id->currency_round, $with_symbol, $this->CI->e_orders_model->$order_id->currency_symbol);
        }
        
        return $price;
    }
    
    /*
     * NEDOROBENE
     * 
     * function get_order_data($order_id = '')
    {
        if(!$this->CI->e_order_data_model->item_exists($order_id)) return FALSE;
        
        $this->CI->e_order_data_model->where('order_id', '=', $order_id);
        return $this->CI->e_order_data_model->get_data();
    }
    
    function delete_all_order_data($order_id = '')
    {
        if(!$this->CI->e_order_data_model->item_exists($order_id)) return FALSE;
        
        $this->CI->e_order_data_model->where('order_id', '=', $order_id);
        $this->CI->e_order_data_model->delete();
        return TRUE;
    }
    
    function delete_order_data($order_id = '', $order_data_id = '')
    {
        if(!$this->CI->e_order_data_model->item_exists($order_id)) return FALSE;
        
        if($this->CI->e_order_data_model->item_exists($order_data_id))
        {
            $this->CI->e_order_data_model->delete_item($order_data_id);
        }
        
        return TRUE;
    }
    
    function add_order_data($order_id = '', $order_data = array())
    {
        if(!$this->CI->e_orders_model->item_exists($order_id)) return FALSE;
        
        $order_data['order_id'] = $order_id;
        $this->CI->e_order_data_model->add_item($order_data);
    }
    
    function set_order_data($order_data_id = '', $order_data = array())
    {
        if(!$this->CI->e_order_data_model->item_exists($order_data_id)) return FALSE;
        
        $this->CI->e_order_data_model->set_item_data();
    }*/
    
}