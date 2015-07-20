<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Panels extends CI_Driver {
    
    protected $CI;
    protected $codes = NULL;
    
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->cms->model->load_system('panels');
        $this->CI->cms->model->load_system('panel_types');
        $this->CI->cms->model->load_system('panel_type_variables');
    }
    
    /* Panel types */
    
    function get_panel_type_variables($panel_type_id = '', $type = '')
    {
        if($type == 'add') $this->CI->s_panel_type_variables_model->where('add', '=', '1');
        elseif($type == 'edit') $this->CI->s_panel_type_variables_model->where('edit', '=', '1');
        $this->CI->s_panel_type_variables_model->where('panel_type_id', '=', $panel_type_id);
        return $this->CI->s_panel_type_variables_model->get_data();
    }
    
    function get_panel_type_variable_names($panel_type_id = '', $type = '')
    {
        if($type == 'add') $this->CI->s_panel_type_variables_model->where('add', '=', '1');
        elseif($type == 'edit') $this->CI->s_panel_type_variables_model->where('edit', '=', '1');
        $this->CI->s_panel_type_variables_model->where('panel_type_id', '=', $panel_type_id);
        return $this->CI->s_panel_type_variables_model->get_data_in_col('name');
    }
    
    function get_panel_type_variable_ids($panel_type_id = '', $type = '')
    {
        if($type == 'add') $this->CI->s_panel_type_variables_model->where('add', '=', '1');
        elseif($type == 'edit') $this->CI->s_panel_type_variables_model->where('edit', '=', '1');
        $this->CI->s_panel_type_variables_model->where('panel_type_id', '=', $panel_type_id);
        return $this->CI->s_panel_type_variables_model->get_ids();
    }
    
    function get_panel_type_variable($panel_type_variable_id = '')
    {
        if($this->CI->s_panel_type_variables_model->item_exists($panel_type_variable_id))
        {
            return $this->CI->s_panel_type_variables_model->$panel_type_variable_id;
        }
        else
        {
            return FALSE;
        }
    }
    
    function get_panel_type_variable_field_row($panel_type_variable_id = '')
    {
        if($this->CI->s_panel_type_variables_model->item_exists($panel_type_variable_id))
        {
            $this->CI->admin->form->info($this->CI->s_panel_type_variables_model->$panel_type_variable_id->info);
            return $this->CI->admin->form->get_field_row($this->CI->s_panel_type_variables_model->$panel_type_variable_id->field_type, $this->CI->s_panel_type_variables_model->$panel_type_variable_id->name, $this->CI->s_panel_type_variables_model->$panel_type_variable_id->title);
        }
        else
        {
            return FALSE;
        }
    }
    
    function add_panel_type_variable_field($panel_type_variable_id = '', $value = '')
    {
        if($this->CI->s_panel_type_variables_model->item_exists($panel_type_variable_id))
        {
            $this->CI->admin->form->info($this->CI->s_panel_type_variables_model->$panel_type_variable_id->info);
            return $this->CI->admin->form->add_dynamic_field($this->CI->s_panel_type_variables_model->$panel_type_variable_id->field_type, $this->CI->s_panel_type_variables_model->$panel_type_variable_id->name, $this->CI->s_panel_type_variables_model->$panel_type_variable_id->title, $value);
        }
        else
        {
            return FALSE;
        }
    }
    
    function load_panel_type_model($panel_type_id = '', $model = 'panel_type_data')
    {
        if($this->CI->s_panel_types_model->item_exists($panel_type_id))
        {
            $this->CI->cms->model->load_user('panel_type_data_' . $panel_type_id, $model);
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    /* Panels */
    
    function load_panel_model($panel_id = '', $model = 'panel_type_data')
    {
        if($this->CI->s_panels_model->item_exists($panel_id))
        {
            return $this->load_panel_type_model($this->CI->s_panels_model->$panel_id->panel_type_id, $model);
        }
        else
        {
            return FALSE;
        }
    }
    
    function get_panel_data($panel_id = '', $variable = '')
    {
        if($this->load_panel_model($panel_id) === FALSE) return FALSE;
        if(!$this->CI->u_panel_type_data_model->item_exists($panel_id)) return FALSE;
        
        $panel_data = (object) array_merge((array)$this->CI->s_panels_model->get_item($panel_id), (array)$this->CI->u_panel_type_data_model->get_item($panel_id));
        
        if(strlen($variable) > 0)
        {
            return $panel_data->$variable;
        }
        else
        {
            return $panel_data;
        }
    }
    
    function set_panel_data($panel_id = '', $data = array())
    {
        if($this->load_panel_model($panel_id) === FALSE) return FALSE;
        if($this->CI->u_panel_type_data_model->item_exists($panel_id))
        {
            $this->CI->u_panel_type_data_model->set_item_data($panel_id, $data);
        }
        else
        {
            $data[$this->CI->u_panel_type_data_model->get_col('id')] = $panel_id;
            $this->CI->u_panel_type_data_model->add_item($data);
        }
    }
    
    function get_code_copy($code = '')
    {
        $codes = $this->CI->s_panels_model->get_data_in_col('code');

        while(in_array($code, $codes))
        {
            $code .= '_copy';
        }
        
        return $code;
    }
    
    function panel_exists($panel_id = '')
    {
        return $this->CI->s_panels_model->item_exists($panel_id);
    }
    
    function is_public($panel_id = '')
    {
        if(!$this->panel_exists($panel_id)) return FALSE;
        
        return (bool)$this->CI->s_panels_model->$panel_id->public;
    }
    
    function get_panel_id_by_code($panel_code = '')
    {
        if($this->codes == NULL) $this->codes = array_flip($this->CI->s_panels_model->get_data_in_col('code'));
        
        return (isset($this->codes[$panel_code])) ? $this->codes[$panel_code] : FALSE;
    }
    
}