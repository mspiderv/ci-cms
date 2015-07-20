<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TextareaField extends FormField implements IFormField, IFormFieldDynamic {
    
    protected $placeholder;
    
    public function __construct($name = '', $title = '', $value = '', $placeholder = '')
    {
        $this->CI =& get_instance();
        
        $this->name = $name;
        $this->value = $this->_get_value($name, $value);
        $this->placeholder = $placeholder;
        $this->_init($name, $title);
    }
    
    function get_field() 
    {
        $attributes = (array)$this->options;
        
        $attributes['id'] = $this->field_id;
        $attributes['class'] = 'textarea fv';
        $attributes['data-page'] = 'textarea';
        
        if(strlen($this->placeholder) > 0)
        {
            $attributes['data-placeholder'] = $this->placeholder;
            $attributes['class'] .= ' field_placeholder';
            $attributes['data-page'] .= ' field_placeholder';
        }
        
        $attributes = _attributes_to_string($attributes);
        
        return form_textarea($this->name, set_value($this->name, $this->_get_value($this->name, $this->value)), $attributes);
    }
    
    function set($name = '', $title = '', $value = '')
    {
        $this->error = $this->CI->admin->form->get_error($name);
        
        $this->name = $name;
        $this->title = $title;
        $this->value = $value;
    }
    
}