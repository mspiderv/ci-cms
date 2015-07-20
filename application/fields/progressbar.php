<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ProgressbarField extends FormField implements IFormField {
    
    protected $value;
    
    function __construct($title, $value = 0)
    {
        $this->CI =& get_instance();
        
        $this->value = $value;
        
        $this->_init('', $title);
    }
    
    function get_field() 
    {
        $data = array();
        
        $data['value'] = $this->value;
        
        return $this->load_view('progressbar', $data);
    }
    
}
