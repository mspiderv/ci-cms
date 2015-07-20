<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class HrefField extends FormField implements IFormField, IFormFieldDynamic {
    
    public function __construct($name = '', $title = '', $value = '')
    {
        $this->CI =& get_instance();
        
        if(is_array($value)) $value = json_encode($value);
        
        $this->name = $name;
        $this->value = $this->_get_value($name, $value);
        $this->_init($name, $title);
    }
    
    function get_field()
    {
        $data = array();
        
        $data['name'] = $this->name;
        
        $value = get_href($this->name, set_value($this->name, $this->_get_value($this->name, $this->value)));

        $data['value_type'] = @$value['type'];
        $data['value_value'] = @$value['value'];
        
        $data['attrs'] = (array)@$value['attrs'];
        
        $data['extra'] = 'id="' . $this->field_id . '" class="a_select chosen" data-page="chosen" data-placeholder="' . ll('field_href_6') . '"';
        
        // Services
        $this->CI->cms->model->load_system('href_attributes');
        $data['attributes'] = $this->CI->s_href_attributes_model->get_data_in_col('name');
        
        // Pages
        $data['pages'] = $this->CI->cms->pages->get_pages_select_data('', FALSE);
        
        // Products
        $this->CI->cms->model->load_eshop('products');
        $data['products'] = $this->CI->e_products_model->get_data_in_col('_name');
        
        // Categories
        $this->CI->load->driver('eshop');
        $data['categories'] = $this->CI->eshop->categories->get_categories_select_data('', FALSE);
        
        // Services
        $this->CI->cms->model->load_system('services');
        $data['services'] = $this->CI->s_services_model->get_data_in_col('name');
        
        $data['selector_options'] = array();
        $data['selector_options']['empty'] = '';
        if(count($data['pages']) > 0) $data['selector_options']['page'] = ll('field_href_1');
        
        if(cfg('general', 'eshop'))
        {
            if(count($data['products']) > 0) $data['selector_options']['product'] = ll('field_href_2');
            if(count($data['categories']) > 0) $data['selector_options']['category'] = ll('field_href_3');
        }
        
        if(count($data['services']) > 0) $data['selector_options']['service'] = ll('field_href_4');
        $data['selector_options']['url'] = ll('field_href_5');
        
        return $this->load_view('href', $data);
    }
    
    function set($name = '', $title = '', $value = '')
    {
        $this->error = $this->CI->admin->form->get_error($name);
        
        $this->name = $name;
        $this->title = $title;
        $this->value = $value;
    }
    
}