<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

admin_form_load_field_class('select');

class Select_ChangefreqField extends SelectField implements IFormField {
    
    function __construct($name = '', $title = '', $selected = NULL, $unselectable = FALSE)
    {
        $this->CI =& get_instance();
        
        load_lang('changefreq');

        $this->options = array('');

        foreach(cfg('changefreq', 'values') as $changefreq)
        {
            $this->options[$changefreq] = ll('changefreq_' . $changefreq);
        }
        
        $this->name = $name;
        $this->selected = $selected;
        $this->unselectable = $unselectable;
        
        $this->_init($name, $title);
    }
    
}
