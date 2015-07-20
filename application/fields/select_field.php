<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

admin_form_load_field_class('select');

class Select_FieldField extends SelectField implements IFormField {
    
    function __construct($name = '', $title = '', $selected = NULL, $unselectable = FALSE)
    {
        $this->CI =& get_instance();
        
        $this->name = $name;
        $this->options = array_combine($this->CI->cms->get_fields(), $this->CI->cms->get_fields());
        $this->selected = $selected;
        $this->unselectable = $unselectable;
        
        $this->_init($name, $title);
    }
    
}
