<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Services extends CI_Driver {
    
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->cms->model->load_system('services');
    }
    
    function service_exists($service_id = '')
    {
        return $this->CI->s_services_model->item_exists($service_id);
    }
    
    function duplicate($service_id = '')
    {
        if(!$this->service_exists($service_id)) return FALSE;
        
        $service_data = (array)$this->CI->s_services_model->get_item($service_id);
        unset($service_data[$this->CI->s_services_model->get_col('id')]);
        $this->CI->s_services_model->add_item($service_data);
        
        return TRUE;
    }
    
    function get_service_data($service_id = '', $variable = '')
    {
        if(!$this->service_exists($service_id)) return FALSE;
        
        if(strlen($variable) > 0)
        {
            return $this->CI->s_services_model->$service_id->$variable;
        }
        else
        {
            return $this->CI->s_services_model->get_item($service_id);
        }
    }
    
    // Parents
    
    function get_services_select_data($service_id = '', $unselectable = TRUE)
    {
        $this->CI->load->helper('string');
        $select_data = ($unselectable) ? array('') : array();
        
        foreach($this->get_services_structure() as $service)
        {
            if(is_array($service) && $this->CI->s_services_model->item_exists(@$service['id']) && (!$this->CI->s_services_model->item_exists($service_id) || $this->valid_parent_service_id($service['id'], $service_id)))
            {
                $select_data[$service['id']] = repeater('-&nbsp;', @$service['level']) . $this->CI->s_services_model->$service['id']->name;
            }
        }
        
        return $select_data;
    }
    
    function valid_parent_service_id($parent_service_id = '', $service_id = '')
    {
        if(!$this->CI->s_services_model->item_exists($service_id) || !$this->CI->s_services_model->item_exists($parent_service_id)) return FALSE;
        if($service_id == $parent_service_id) return FALSE;
        if(in_array($service_id, $this->get_service_parents($parent_service_id))) return FALSE;
        return TRUE;
    }
    
    function get_services_structure($parent_id = NULL, $level = 0)
    {
        $services_structure = array();
        
        if((int)$parent_id > 0 && !$this->CI->s_services_model->item_exists($parent_id)) return array();
        
        $order_cat_col = $this->CI->s_services_model->get_col('order_cat');
        
        if((int)$parent_id > 0)
        {
            $this->CI->s_services_model->where($order_cat_col, '=', $parent_id);
        }
        else
        {
            $this->CI->s_services_model->where($order_cat_col, '=', '');
        }
        
        foreach($this->CI->s_services_model->get_ids() as $service_id)
        {
            $services_structure[] = array(
                'id' => $service_id,
                'level' => $level
            );
            
            $subservices = (array)$this->get_services_structure($service_id, $level + 1);
            if(count($subservices) > 0) $services_structure = array_merge($services_structure, $subservices);
        }
        
        return $services_structure;
    }
    
    function get_service_levels($service_id = '')
    {
        return count($this->get_service_parents($service_id));
    }
    
    function get_service_parents($service_id = '')
    {
        $parents = array();
        
        while($this->service_has_parent($service_id))
        {
            $service_id = $this->get_service_parent($service_id);;
            $parents[] = $service_id;
        }
        
        return $parents;
    }
    
    function service_has_parent($service_id = '')
    {
        return (intval($this->get_service_parent($service_id)) > 0);
    }
    
    function get_service_parent($service_id = '')
    {
        if($this->service_exists($service_id))
        {
            return $this->CI->s_services_model->get_item_data($service_id, $this->CI->s_services_model->get_col('order_cat'));
        }
        else
        {
            return NULL;
        }
    }
    
}