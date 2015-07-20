<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

admin_form_load_field_class('select');

class Product_GalleryField extends SelectField implements IFormField {
    
    function __construct($name = '', $title = '', $selected = NULL, $unselectable = FALSE)
    {
        $this->CI =& get_instance();
        
        $this->CI->cms->model->load_eshop('product_galleries');
        
        $this->options = $this->CI->e_product_galleries_model->get_data_in_col('_name');
        
        $this->name = $name;
        $this->selected = $selected;
        $this->unselectable = $unselectable;
        
        $this->_init($name, $title);
    }
    
    function get_field() 
    {
        if($this->unselectable) $this->options = array('' => '') + $this->options;
        
        //$options = ($this->unselectable) ? array_merge(array(''), $this->options) : $this->options;
        return $this->load_view('product_gallery', array('dropdown' => form_dropdown($this->name, $this->options, set_value($this->name, $this->_get_value($this->name, $this->selected)), 'id="' . $this->field_id . '" data-page="chosen" class="a_select field_product_gallery chosen fv"'), 'field_id' => $this->field_id));
        
    }
    
}
