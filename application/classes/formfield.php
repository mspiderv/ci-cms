<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class FormField implements IFormField {
    
    protected $CI;
    protected $name;
    protected $title;
    protected $value;
    protected $options;
    protected $field_id;
    protected $info;
    protected $error;
    
    protected static $field_names = array();
    
    public function __construct($name = '', $title = '', $value = '', $options = array())
    {
        $this->CI =& get_instance();
        
        $this->name = $name;
        $this->value = $this->_get_value($name, $value);
        $this->options = $options;
        $this->_init($name, $title);
    }
    
    protected function _init($name, $title)
    {
        $this->_check_field_name($name);
        
        $this->title = $title;
        $this->field_id = $this->CI->admin->form->get_field_id();
        $this->info = $this->CI->admin->form->get_info();
        $this->error = $this->CI->admin->form->get_error($name);
    }
    
    protected function _get_value($name, $value = '')
    {
        return (@strlen($value) > 0) ? $value : $this->CI->admin->form->get_field_value($name);
    }

    function is_multilingual()
    {
        return (bool) (substr($this->name, 0, 1) == '_');
    }
    
    function get_field_tpl()
    {
        return 'field_wrap';
    }
    
    function get_title()
    {
        return $this->title;
    }
    
    function get_field()
    {
        return '';
    }
    
    function get_info()
    {
        return $this->info;
    }
    
    function get_error()
    {
        return $this->error;
    }
    
    protected function load_view($view = '', $data = array())
    {
        return $this->CI->admin->load_view(cfg('folder', 'fields') . '/' . $view, $data, TRUE);
    }
    
    protected function _check_field_name($field_name)
    {
        if(strlen($field_name) > 0)
        {
            if(in_array($field_name, self::$field_names))
            {
                show_error("Pole s názvom <strong>" . $field_name . "</strong> sa nedá vytvoriť, pretože už existuje pole s rovnakým názvom.");
            }
            
            self::$field_names[] = $field_name;
        }
    }
    
}