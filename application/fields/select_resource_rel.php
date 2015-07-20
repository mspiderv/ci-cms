<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

admin_form_load_field_class('select');

class Select_Resource_RelField extends SelectField implements IFormField {
    
    function __construct($name = '', $title = '', $selected = NULL, $unselectable = FALSE)
    {
        $this->CI =& get_instance();
        
        load_lang('changefreq');

        $this->options = array();
        
        $this->options['page_category'] = ll('field_select_resource_rel_1');
        $this->options['page_type'] = ll('field_select_resource_rel_2');
        $this->options['page'] = ll('field_select_resource_rel_3');
        $this->options['panel_type'] = ll('field_select_resource_rel_4');
        $this->options['panel'] = ll('field_select_resource_rel_5');
        
        if(cfg('general', 'eshop'))
        {
            $this->options['product_category'] = ll('field_select_resource_rel_6');
            $this->options['product'] = ll('field_select_resource_rel_7');
        }
        
        $this->options['service'] = ll('field_select_resource_rel_8');
        
        $this->name = $name;
        $this->selected = $selected;
        $this->unselectable = $unselectable;
        
        $this->_init($name, $title);
    }
    
}
