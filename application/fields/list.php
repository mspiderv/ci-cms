<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

admin_form_load_field_class('select');

class ListField extends SelectField implements IFormField, IFormFieldDynamic {
    
    function __construct($name = '', $title = '', $selected = NULL)
    {
        $this->CI =& get_instance();
        
        $this->CI->cms->model->load_system('lists');
        
        $this->options = $this->CI->s_lists_model->get_data_in_col('name');
        
        $this->name = $name;
        $this->selected = $selected;
        $this->unselectable = TRUE;
        
        $this->_init($name, $title);
    }
    
    function set($name = '', $title = '', $selected = '')
    {
        $this->error = $this->CI->admin->form->get_error($name);
        
        $this->name = $name;
        $this->title = $title;
        $this->selected = $selected;
    }
    
}
