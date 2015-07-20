<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PasswordField extends FormField implements IFormField, IFormFieldDynamic {
    
    protected $placeholder;
    
    public function __construct($name, $title = '', $value = '', $options = array())
    {
        $this->CI =& get_instance();
        
        if(!isset($options['autocomplete'])) $options['autocomplete'] = 'off';
        
        $this->name = $name;
        $this->options = $options;
        $this->value = $this->_get_value($name, $value);
        $this->_init($name, $title);
    }
    
    function get_field() 
    {
        $attributes = (array)$this->options;
        
        $attributes['type'] = 'password';
        $attributes['name'] = $this->name;
        $attributes['value'] = set_value($this->name, $this->_get_value($this->name, $this->value));
        $attributes['class'] = ' input fv';
        $attributes['id'] = $this->field_id;
        
        $attributes = _attributes_to_string($attributes);
        
        return $this->load_view('password', array('attributes' => $attributes));
    }
    
    function set($name = '', $title = '', $value = '')
    {
        $this->error = $this->CI->admin->form->get_error($name);
        
        $this->name = $name;
        $this->title = $title;
        $this->value = $value;
    }
    
}