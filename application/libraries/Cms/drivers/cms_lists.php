<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Lists extends CI_Driver {
    
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->cms->model->load_system('lists');
        $this->CI->cms->model->load_system('list_types');
        $this->CI->cms->model->load_system('list_type_variables');
    }
    
    /* List types */
    
    function get_list_type_variables($list_type_id = '', $type = '')
    {
        if($type == 'add') $this->CI->s_list_type_variables_model->where('add', '=', '1');
        elseif($type == 'edit') $this->CI->s_list_type_variables_model->where('edit', '=', '1');
        $this->CI->s_list_type_variables_model->where('list_type_id', '=', $list_type_id);
        return $this->CI->s_list_type_variables_model->get_data();
    }
    
    function get_list_type_variable_names($list_type_id = '', $type = '')
    {
        if($type == 'add') $this->CI->s_list_type_variables_model->where('add', '=', '1');
        elseif($type == 'edit') $this->CI->s_list_type_variables_model->where('edit', '=', '1');
        $this->CI->s_list_type_variables_model->where('list_type_id', '=', $list_type_id);
        return $this->CI->s_list_type_variables_model->get_data_in_col('name');
    }
    
    function get_list_type_variable_ids($list_type_id = '', $type = '')
    {
        if($type == 'add') $this->CI->s_list_type_variables_model->where('add', '=', '1');
        elseif($type == 'edit') $this->CI->s_list_type_variables_model->where('edit', '=', '1');
        $this->CI->s_list_type_variables_model->where('list_type_id', '=', $list_type_id);
        return $this->CI->s_list_type_variables_model->get_ids();
    }
    
    function get_list_type_variable($list_type_variable_id = '')
    {
        if($this->CI->s_list_type_variables_model->item_exists($list_type_variable_id))
        {
            return $this->CI->s_list_type_variables_model->$list_type_variable_id;
        }
        else
        {
            return FALSE;
        }
    }
    
    function get_list_type_variable_field_row($list_type_variable_id = '')
    {
        if($this->CI->s_list_type_variables_model->item_exists($list_type_variable_id))
        {
            $this->CI->admin->form->info($this->CI->s_list_type_variables_model->$list_type_variable_id->info);
            return $this->CI->admin->form->get_field_row($this->CI->s_list_type_variables_model->$list_type_variable_id->field_type, $this->CI->s_list_type_variables_model->$list_type_variable_id->name, $this->CI->s_list_type_variables_model->$list_type_variable_id->title);
        }
        else
        {
            return FALSE;
        }
    }
    
    function add_list_type_variable_field($list_type_variable_id = '', $value = '')
    {
        if($this->CI->s_list_type_variables_model->item_exists($list_type_variable_id))
        {
            $this->CI->admin->form->info($this->CI->s_list_type_variables_model->$list_type_variable_id->info);
            return $this->CI->admin->form->add_dynamic_field($this->CI->s_list_type_variables_model->$list_type_variable_id->field_type, $this->CI->s_list_type_variables_model->$list_type_variable_id->name, $this->CI->s_list_type_variables_model->$list_type_variable_id->title, $value);
        }
        else
        {
            return FALSE;
        }
    }
    
    function load_list_type_model($list_type_id = '', $model = 'list_type_data')
    {
        if($this->CI->s_list_types_model->item_exists($list_type_id))
        {
            $this->CI->cms->model->load_user('list_type_data_' . $list_type_id, $model);
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    /* Lists */
    
    function load_list_model($list_id = '', $model = 'list_type_data')
    {
        if($this->CI->s_lists_model->item_exists($list_id))
        {
            return $this->load_list_type_model($this->CI->s_lists_model->$list_id->list_type_id, $model);
        }
        else
        {
            return FALSE;
        }
    }
    
    function get_list_data($list_id = '', $item_id = '')
    {
        if($this->load_list_model($list_id) === FALSE) return FALSE;
        
        if(intval($item_id) > 0)
        {
            $this->load_list_model($list_id);
            
            if($this->CI->u_list_type_data_model->item_exists($item_id))
            {
                return $this->CI->u_list_type_data_model->get_item($item_id);
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            $this->CI->u_list_type_data_model->where('list_id', '=', $list_id);
            return $this->CI->u_list_type_data_model->get_data();
        }
    }
    
    function add_list_item($list_id = '', $item_data = array())
    {
        if($this->load_list_model($list_id) === FALSE) return FALSE;
        $item_data['list_id'] = $list_id;
        $this->CI->u_list_type_data_model->add_item($item_data);
        return TRUE;
    }
    
    function list_has_item($list_id = '', $item_id = '')
    {
        if($this->load_list_model($list_id) === FALSE) return FALSE;
        return $this->CI->u_list_type_data_model->item_exists($item_id);
    }
    
    function set_item_data($list_id = '', $item_id = '', $data = array())
    {
        if(!$this->list_has_item($list_id, $item_id)) return FALSE;
        $this->CI->u_list_type_data_model->set_item_data($item_id, $data);
        return TRUE;
    }
    
    function duplicate_item($list_id = '', $item_id = '')
    {
        if(!$this->list_has_item($list_id, $item_id)) return FALSE;
        $item_data = (array)$this->CI->u_list_type_data_model->get_item($item_id);
        unset($item_data[$this->CI->u_list_type_data_model->get_col('id')]);
        return $this->CI->u_list_type_data_model->add_item($item_data);
    }
    
    function duplicate_list($list_id = '')
    {
        if(!$this->CI->s_lists_model->item_exists($list_id)) return FALSE;
        
        $status = TRUE;
        
        $list_data = (array)$this->CI->s_lists_model->get_item($list_id);
        unset($list_data[$this->CI->s_lists_model->get_col('id')]);
        $this->CI->s_lists_model->add_item($list_data);
        
        $new_list_id = $this->CI->s_lists_model->insert_id();
        
        foreach($this->get_list_data($list_id, $item_id) as $item)
        {
            $item = (array)$item;
            unset($item[$this->CI->u_list_type_data_model->get_col('id')]);
            $item['list_id'] = $new_list_id;
            
            if(!$this->CI->u_list_type_data_model->add_item($item)) $status = FALSE;
        }
        
        return $status;
    }
    
    function get_item_name($list_id = '', $item_id = '')
    {
        if(!$this->list_has_item($list_id, $item_id)) return '';
        
        $list_type_id = $this->CI->s_lists_model->$list_id->list_type_id;
        $primary_variable_id = $this->CI->s_list_types_model->$list_type_id->primary_variable_id;
        $variable_name = (intval($primary_variable_id) > 0) ? $this->CI->s_list_type_variables_model->$primary_variable_id->name : 'name';
        $item_name = $this->CI->u_list_type_data_model->$item_id->$variable_name;
        return (strlen($item_name) > 0) ? $item_name : $item_id;
    }
    
    function has_list_primary_variable($list_id = '')
    {
        if(!$this->CI->s_lists_model->item_exists($list_id)) return FALSE;
        $list_type_id = $this->CI->s_lists_model->$list_id->list_type_id;
        return (intval($this->CI->s_list_types_model->$list_type_id->primary_variable_id) > 0);
    }
    
}