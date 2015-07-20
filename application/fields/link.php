<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LinkField extends FormField implements IFormField {
    
    protected $url;
    protected $text;
    
    function __construct($url, $title, $text)
    {
        $this->CI =& get_instance();
        
        $this->url = $url;
        $this->title = $title;
        $this->text = $text;
        
        $this->_init('', $title);
    }
    
    function get_field() 
    {
        $attributes = array();
        
        $attributes['field_id'] = $this->field_id;
        
        return anchor($this->url, (strlen($this->text) > 0) ? $this->text : $this->title, $attributes);
    }
    
}
