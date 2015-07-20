<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ColorpickerField extends FormField implements IFormField, IFormFieldDynamic {
    
    function get_field() 
    {
        $data = array();
        
        $data['name'] = $this->name;
        $data['value'] = set_value($this->name, $this->_get_value($this->name, $this->value));
        $data['field_id'] = $this->field_id;
        
        return $this->load_view('colorpicker', $data);
    }
    
    function set($name = '', $title = '', $value = '')
    {
        $this->error = $this->CI->admin->form->get_error($name);
        
        $this->name = $name;
        $this->title = $title;
        $this->value = $value;
    }
    
}