<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CheckboxField extends FormField implements IFormField, IFormFieldDynamic {
    
    protected $checked;
    protected $description;
    
    function __construct($name = '', $title = '', $checked = FALSE, $description = '')
    {
        parent::__construct($name, $title, '', array());
        
        $this->checked = $this->_get_value($name, (bool)$checked);
        $this->description = $description;
    }
    
    function get_field() 
    {
        $data = array();
        
        $data['name'] = $this->name;
        $data['description'] = $this->description;
        $data['checked'] = $this->checked;
        $data['field_id'] = $this->field_id;
        
        return $this->load_view('checkbox', $data);
    }
    
    function set($name = '', $title = '', $value = '')
    {
        $this->error = $this->CI->admin->form->get_error($name);
        
        $this->name = $name;
        $this->title = $title;
        $this->checked = (bool)$value;
    }
    
}