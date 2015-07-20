<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ImagepickerField extends FormField implements IFormField, IFormFieldDynamic {
    
    protected $value;
    protected $multiple;
    
    function __construct($name = '', $title = '', $value = '', $multiple = FALSE)
    {
        parent::__construct($name, $title, $value);
        
        $this->value = $this->_get_value($name, $value);
        $this->multiple = $multiple;
    }
    
    function get_field() 
    {
        $data = array();
        
        $data['name'] = $this->name;
        $data['value'] = set_value($this->name, $this->value);
        $data['field_id'] = $this->field_id;
        $data['multiple'] = $this->multiple;
        
        return $this->load_view('imagepicker', $data);
    }
    
    function get_field_tpl()
    {
        return 'elfinder_field_wrap';
    }
    
    function set($name = '', $title = '', $value = '')
    {
        $this->error = $this->CI->admin->form->get_error($name);
        
        $this->name = $name;
        $this->title = $title;
        $this->value = $value;
    }
    
}
