<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ChartField extends FormField implements IFormField {
    
    protected $id;
    
    function __construct($data, $title)
    {
        $this->CI =& get_instance();
        
        $this->id = $this->CI->admin->form->new_chart($data);
        
        $this->_init('', $title);
    }
    
    function get_field() 
    {
        $data = array();
        
        $data['id'] = $this->id;
        
        return $this->load_view('chart', $data);
    }
    
}
