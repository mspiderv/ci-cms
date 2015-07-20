<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SelectField extends FormField implements IFormField {
    
    protected $options;
    protected $selected;
    protected $unselectable;
    protected $unselectable_placeholder;
    
    function __construct($name = '', $title = '', $options = array(), $selected = NULL, $unselectable = FALSE, $unselectable_placeholder = '')
    {
        $this->CI =& get_instance();
        
        $this->name = $name;
        $this->options = $options;
        $this->selected = $selected;
        $this->unselectable = $unselectable;
        $this->unselectable_placeholder = $unselectable_placeholder;
        
        $this->_init($name, $title);
    }
    
    function get_field() 
    {
        if($this->unselectable) $this->options = array('' => '') + $this->options;
        return form_dropdown($this->name, $this->options, set_value($this->name, $this->_get_value($this->name, $this->selected)), 'id="' . $this->field_id . '" data-page="chosen" class="a_select chosen fv" data-placeholder="' .  $this->unselectable_placeholder . '"');
    }
    
}
