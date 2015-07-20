<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

admin_form_load_field_class('select');

class Select_Dynamic_FieldField extends SelectField implements IFormField {
    
    function __construct($name = '', $title = '', $selected = NULL, $unselectable = FALSE)
    {
        $this->CI =& get_instance();
        
        $this->name = $name;
        $this->selected = $selected;
        $this->unselectable = $unselectable;
        
        $this->_init($name, $title);
        
        // Dynamic fields
        $dynamic_fields = array();
        
        foreach($this->CI->cms->get_dynamic_fields() as $dynamic_field)
        {
            $title = ll('dynamic_field_' . $dynamic_field);
            $dynamic_fields[$dynamic_field] = ($title == '') ? $dynamic_field : $title;
        }
        
        $dynamic_fields_title = ll('dynamic_fields');
        if($dynamic_fields_title == '') $dynamic_fields_title = 'dynamic_fields';
        
        // Referring fields
        $referring_fields = array();
        
        foreach($this->CI->cms->get_referring_fields() as $referring_field)
        {
            $title = ll('referring_field_' . $referring_field);
            $referring_fields[$referring_field] = ($title == '') ? $referring_field : $title;
        }
        
        $referring_fields_title = ll('referring_fields');
        if($referring_fields_title == '') $referring_fields_title = 'referring_fields';
        
        // Page with type
        $page_with_type_title = ll('page_with_type');
        if($page_with_type_title == '') $page_with_type_title = 'page_with_type';
        
        $this->CI->cms->model->load_system('page_types');
        
        $pages_with_type = array();
        
        foreach($this->CI->s_page_types_model->get_data_in_col('name') as $page_type_id => $page_type_name)
        {
            $pages_with_type['page_with_type[' . $page_type_id . ']'] = $page_type_name;
        }
        
        // Panel with type
        $panel_with_type_title = ll('panel_with_type');
        if($panel_with_type_title == '') $panel_with_type_title = 'panel_with_type';
        
        $this->CI->cms->model->load_system('panel_types');
        
        $panels_with_type = array();
        
        foreach($this->CI->s_panel_types_model->get_data_in_col('name') as $panel_type_id => $panel_type_name)
        {
            $panels_with_type['panel_with_type[' . $panel_type_id . ']'] = $panel_type_name;
        }
        
        // List with type
        $list_with_type_title = ll('list_with_type');
        if($list_with_type_title == '') $list_with_type_title = 'list_with_type';
        
        $this->CI->cms->model->load_system('list_types');
        
        $lists_with_type = array();
        
        foreach($this->CI->s_list_types_model->get_data_in_col('name') as $list_type_id => $list_type_name)
        {
            $lists_with_type['list_with_type[' . $list_type_id . ']'] = $list_type_name;
        }
        
        // Options
        $this->options = array(
            $dynamic_fields_title => $dynamic_fields,
            $referring_fields_title => $referring_fields
        );
        
        if(count($pages_with_type) > 0) $this->options[$page_with_type_title] = $pages_with_type;
        if(count($panels_with_type) > 0) $this->options[$panel_with_type_title] = $panels_with_type;
        if(count($lists_with_type) > 0) $this->options[$list_with_type_title] = $lists_with_type;
    }
    
}
