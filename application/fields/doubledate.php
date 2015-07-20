<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DoubledateField extends FormField implements IFormField {
    
    protected $name_1;
    protected $name_2;
    protected $value_1;
    protected $value_2;
    
    function __construct($name_1, $name_2, $title = '', $value_1 = '', $value_2 = '')
    {
        $this->CI =& get_instance();
        
        $this->name_1 = $name_1;
        $this->name_2 = $name_2;
        $this->value_1 = $value_1;
        $this->value_2 = $value_2;
        
        $this->_init('', $title);
    }
    
    function get_field()
    {
        $data = array();
        
        $data['field_id_1'] = $this->field_id;
        $data['field_id_2'] = $this->CI->admin->form->get_new_field_id();
        $data['name_1'] = $this->name_1;
        $data['name_2'] = $this->name_2;
        $data['value_1'] = set_value($this->name_1, $this->_get_value($this->name_1, $this->value_1));
        $data['value_2'] = set_value($this->name_2, $this->_get_value($this->name_2, $this->value_2));
        
        return $this->load_view('doubledate', $data);
    }
    
}