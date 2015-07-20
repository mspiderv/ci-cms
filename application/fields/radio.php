<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class RadioField extends FormField implements IFormField {
    
    protected $description;
    protected $selected;
    
    function __construct($name, $title, $value = '', $selected = FALSE, $description = '')
    {
        parent::__construct($name, $title, $value);
        
        $this->description = $description;
        $this->selected = $selected;
        $this->title = $title;
    }
    
    function get_field()
    {
        $data = array();
        
        $data['name'] = $this->name;
        $data['value'] = $this->_get_value($this->name, $this->value);
        $data['selected'] = set_radio($this->name, $this->value, $this->selected);
        $data['description'] = $this->description;
        $data['field_id'] = $this->field_id;
        $data['title'] = $this->title;
        
        return $this->load_view('radio', $data);
    }
    
    protected function _check_field_name($field_name)
    {
        //
    }
    
}