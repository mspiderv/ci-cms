<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class RadiosField extends FormField implements IFormField {
    
    function __construct($name, $title, $radios = array(), $selected = '')
    {
        $this->CI =& get_instance();
        
        $this->content = '';
        
        foreach((array)$radios as $radio_value => $radio_text)
        {
            $this->content .= $this->CI->admin->form->get_field('radio', $name, '', $radio_value, ($radio_value == $selected), $radio_text);
        }
        
        $this->_init($name, $title);
    }
    
    function is_multilingual()
    {
        return FALSE;
    }
    
    function get_field_tpl()
    {
        return 'radios_wrap';
    }
    
    function get_field() 
    {
        return $this->content;
    }
    
}