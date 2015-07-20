<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CodemirrorField extends FormField implements IFormField {
    
    protected $type;
    
    public function __construct($name, $type = '', $title = '', $value = '')
    {
        $this->CI =& get_instance();
        
        $this->name = $name;
        $this->value = $this->_get_value($name, $value);
        $this->type = $type;
        $this->_init($name, $title);
    }
    
    function get_field() 
    {
        return form_textarea($this->name, set_value($this->name, $this->_get_value($this->name, $this->value)), 'id="' . $this->field_id . '" class="codemirror fv" data-page="codemirror" data-type="' . $this->type . '"');
    }
    
}