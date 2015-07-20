<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Model_Conditions extends CI_Driver {
    
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
    }
    
    function get_condition_types()
    {
        $all_methods = get_class_methods(__CLASS__);
        $method_blacklist = array('__construct', 'get_condition_types', 'decorate', '__call', '__get', '__set');
        
        return array_diff($all_methods, $method_blacklist);
    }
    
    /**
     *
     * The pattern of comparative method
     * 
     * @param mix $value1
     * @param mix $value2
     * @return boolean 
     */
    
}