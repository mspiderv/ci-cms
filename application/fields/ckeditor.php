<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CkeditorField extends FormField implements IFormField, IFormFieldDynamic {
    
    function get_field() 
    {
        return form_textarea($this->name, set_value($this->name, $this->_get_value($this->name, $this->value)), 'id="' . $this->field_id . '" class="ckeditor fv"');
    }
    
    function set($name = '', $title = '', $value = '')
    {
        $this->error = $this->CI->admin->form->get_error($name);
        
        $this->name = $name;
        $this->title = $title;
        $this->value = $value;
    }
    
}