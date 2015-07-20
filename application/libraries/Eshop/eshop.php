<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Eshop extends CI_Driver_Library {
    
    protected $CI;
    protected $valid_drivers = array(
        'eshop_categories',
        'eshop_products',
        'eshop_variants',
        'eshop_parameters',
        'eshop_product_types',
        'eshop_transport_payment',
        'eshop_currencies',
        'eshop_orders',
        'eshop_product_galleries'
    );

    function  __construct()
    {
        $this->CI =& get_instance();
        
        $this->CI->load->helper('eshop');
    }
    
}