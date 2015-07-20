<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class VariantField extends FormField implements IFormField {
    
    protected $data;
    
    function __construct($title = '', $data = array())
    {
        $this->CI =& get_instance();
        
        $this->title = $title;
        $this->data = $data;
        $this->info = $this->CI->admin->form->get_info();
    }
    
    function get_field() 
    {
        $output = '';
        
        foreach($this->data as $variant_id => $variant_data)
        {
            $options = array('' => '') + $variant_data['options'];
            
            // set_value($this->name, $this->_get_value($this->name, $this->selected))
            
            $output .= form_dropdown('variant_ids_selected[]'.rand(1,555), $options, NULL, 'id="' . $this->CI->admin->form->get_new_field_id() . '" data-page="chosen" class="variant_select chosen fv" data-placeholder="' . $variant_data['_name'] . '"');
        }
        
        return $output;
    }
    
}
