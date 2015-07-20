<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

admin_form_load_field_class('select');

class Select_Email_WrapField extends SelectField implements IFormField {
    
    function __construct($name = '', $title = '', $selected = NULL, $unselectable = FALSE)
    {
        $this->CI =& get_instance();
        
        $this->CI->cms->model->load_system('email_wraps');
        
        $this->options = $this->CI->s_email_wraps_model->get_data_in_col('name');
        
        $this->name = $name;
        $this->selected = $selected;
        $this->unselectable = $unselectable;
        
        $this->_init($name, $title);
    }
    
}
