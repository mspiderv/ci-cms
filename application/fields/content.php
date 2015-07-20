<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ContentField extends FormField implements IFormField {
    
    function __construct($content = '', $title = '')
    {
        $this->CI =& get_instance();
        
        $this->content = $content;
        $this->title = $title;
    }
    
    function is_multilingual()
    {
        return FALSE;
    }
    
    function get_field() 
    {
        return $this->content;
    }
    
}