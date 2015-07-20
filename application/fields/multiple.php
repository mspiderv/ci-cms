<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MultipleField extends FormField implements IFormField {
    
    protected $options;
    protected $selected;
    
    function __construct($name = '', $title = '', $options = array(), $selected = array())
    {
        $this->CI =& get_instance();
        
        $this->name = $name;
        $this->options = $options;
        $this->selected = $selected;
        
        $this->_init($name, $title);
    }
    
    protected function _get_value($name, $selected = '')
    {
        return (count((array)$selected) > 0) ? $selected : $this->CI->admin->form->get_field_value($name);
    }
    
    function get_field()
    {
        return form_dropdown(((substr($this->name, -2) != '[]') ? $this->name . '[]' : $this->name), $this->options, set_multiple($this->name, $this->_get_value($this->name, $this->selected)), 'id="' . $this->field_id . '" data-page="chosen" class="a_select chosen fv" multiple');
    }
    
}
