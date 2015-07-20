<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DateField extends FormField implements IFormField, IFormFieldDynamic {
    
    function get_field() 
    {
        $attributes = (array)$this->options;
        
        $attributes['name'] = $this->name;
        $attributes['value'] = set_value($this->name, $this->_get_value($this->name, $this->value));
        $attributes['class'] = @$attributes['class'] . ' input datepicker fv';
        $attributes['id'] = $this->field_id;
        $attributes['data-page'] = 'datepicker';
        
        $attributes = _attributes_to_string($attributes);
        
        return $this->load_view('date', array('attributes' => $attributes));
    }
    
    function set($name = '', $title = '', $value = '')
    {
        $this->error = $this->CI->admin->form->get_error($name);
        
        $this->name = $name;
        $this->title = $title;
        $this->value = $value;
    }
    
}