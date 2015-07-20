<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parse_part extends CI_Driver {
    
    // Členské premenné
    protected $CI;
    protected $codes = NULL;
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->CI->cms->model->load_system('parts');
    }
    
    function part_exists($part_id = '')
    {
        return $this->CI->s_parts_model->item_exists($part_id);
    }
    
    function get_part_id_by_code($part_code = '')
    {
        if($this->codes == NULL) $this->codes = array_flip($this->CI->s_parts_model->get_data_in_col('code'));
        
        return (isset($this->codes[$part_code])) ? $this->codes[$part_code] : FALSE;
    }
    
    function generate_part($part_id = '', $lang = '')
    {
        if(!$this->part_exists($part_id)) return '';
        if($lang != '' && !lang_exists($lang)) return '';
        
        return $this->CI->s_parts_model->get_item_data($part_id, $lang . '_content');
    }
    
}