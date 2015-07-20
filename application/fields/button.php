<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ButtonField extends FormField implements IFormField {
    
    protected $url;
    protected $text;
    protected $type;
    
    function __construct($url, $title, $text, $type = '')
    {
        $this->CI =& get_instance();
        
        $this->url = $url;
        $this->text = $text;
        $this->type = $type;
        
        $this->_init('', $title);
    }
    
    function get_field() 
    {
        $data = array();
        
        $data['href'] = site_url($this->url);
        $data['type'] = $this->type;
        $data['text'] = $this->text;
        $data['field_id'] = $this->field_id;
        
        return $this->load_view('button', $data);
    }
    
}
