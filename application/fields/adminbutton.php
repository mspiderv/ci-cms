<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

admin_form_load_field_class('button');

class AdminButtonField extends ButtonField implements IFormField {
    
    function get_field()
    {
        $data = array();
        
        $data['href'] = admin_url($this->url);
        $data['type'] = $this->type;
        $data['text'] = $this->text;
        $data['field_id'] = $this->field_id;
        
        return $this->load_view('button', $data);
    }
    
}
