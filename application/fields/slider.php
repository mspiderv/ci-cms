<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SliderField extends FormField implements IFormField {
    
    protected $value;
    protected $min;
    protected $max;
    protected $step;
    
    function __construct($name, $title, $value = '', $min = 0, $max = 10, $step = 1)
    {
        parent::__construct($name, $title, $value);
        
        $this->value = $value;
        $this->min = $min;
        $this->max = $max;
        $this->step = $step;
    }
    
    function get_field() 
    {
        $data = array();
        
        $data['name'] = $this->name;
        $data['value'] = set_value($this->name, $this->value);
        $data['field_id'] = $this->field_id;
        $data['min'] = $this->min;
        $data['max'] = $this->max;
        $data['step'] = $this->step;
        
        return $this->load_view('slider', $data);
    }
    
}
