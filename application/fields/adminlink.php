<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

admin_form_load_field_class('link');

class AdminlinkField extends LinkField implements IFormField {
    
    function get_field() 
    {
        $attributes = array();
        
        $attributes['field_id'] = $this->field_id;
        
        return admin_anchor($this->url, (strlen($this->text) > 0) ? $this->text : $this->title, '', $attributes);
    }
    
}
