<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ButtonsField extends FormField implements IFormField {
    
    function __construct()
    {
        $this->CI =& get_instance();
        
        $this->content = $this->CI->admin->form->get_buttons();
        $this->title = '';
        $this->info = '';
        $this->error = '';
    }
    
    function is_multilingual()
    {
        return FALSE;
    }
    
    function get_field_tpl()
    {
        return 'buttons_wrap';
    }
    
    function get_field() 
    {
        return $this->content;
    }
    
}
