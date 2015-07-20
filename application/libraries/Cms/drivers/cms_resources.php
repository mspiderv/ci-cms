<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Resources extends CI_Driver {
    
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->cms->model->load_system('resources');
        $this->CI->cms->model->load_system('resource_rels');
    }
    
    function resource_exists($resource_id = '')
    {
        return ($this->CI->s_resources_model->item_exists($resource_id));
    }
    
    function get_rel_name($resource_rel_id = '')
    {
        if(!$this->CI->s_resource_rels_model->item_exists($resource_rel_id)) return '';
        
        $resource_rel = $this->CI->s_resource_rels_model->get_item($resource_rel_id);
        
        switch($resource_rel->type)
        {
            case 'page_category':
                $this->CI->cms->model->load_system('categories');
                return $this->CI->s_categories_model->get_item_data($resource_rel->page_category_id, '_name');
                break;
            
            case 'page_type':
                $this->CI->cms->model->load_system('page_types');
                return $this->CI->s_page_types_model->get_item_data($resource_rel->page_type_id, 'name');
                break;
            
            case 'page':
                $this->CI->cms->model->load_system('pages');
                return $this->CI->s_pages_model->get_item_data($resource_rel->page_id, 'name');
                break;
            
            case 'panel_type':
                $this->CI->cms->model->load_system('panel_types');
                return $this->CI->s_panel_types_model->get_item_data($resource_rel->panel_type_id, 'name');
                break;
            
            case 'panel':
                $this->CI->cms->model->load_system('panels');
                return $this->CI->s_panels_model->get_item_data($resource_rel->panel_id, 'name');
                break;
            
            case 'product_category':
                $this->CI->cms->model->load_eshop('categories');
                return $this->CI->e_categories_model->get_item_data($resource_rel->product_category_id, '_name');
                break;
            
            case 'product':
                $this->CI->cms->model->load_eshop('products');
                return $this->CI->e_products_model->get_item_data($resource_rel->product_id, '_name');
                break;
            
            case 'service':
                $this->CI->cms->model->load_system('services');
                return $this->CI->s_services_model->get_item_data($resource_rel->service_id, 'name');
                break;
            
            default:
                return '';
                break;
        }
    }
    
    function rel_type_exists($rel_type = '')
    {
        $rel_types = array(
            'page_category',
            'page_type',
            'page',
            'panel_type',
            'panel',
            'service'
        );
        
        if(cfg('general', 'eshop'))
        {
            $rel_types[] = 'product_category';
            $rel_types[] = 'product';
        }
        
        return in_array($rel_type, $rel_types);
    }
    
    function get_rel_type_name($rel_type = '')
    {
        switch($rel_type)
        {
            case 'page_category':
                return ll('field_select_resource_rel_1'); break;
            
            case 'page_type':
                return ll('field_select_resource_rel_2'); break;
            
            case 'page':
                return ll('field_select_resource_rel_3'); break;
            
            case 'panel_type':
                return ll('field_select_resource_rel_4'); break;
            
            case 'panel':
                return ll('field_select_resource_rel_5'); break;
            
            case 'product_category':
                return ll('field_select_resource_rel_6'); break;
            
            case 'product':
                return ll('field_select_resource_rel_7'); break;
            
            case 'service':
                return ll('field_select_resource_rel_8'); break;
            
            default:
                return ''; break;
        }
    }
    
    protected function _load_rel_type_table($type = '', $model = 'resource_rels_help')
    {
        switch($type)
        {
            case 'page_category':
                $this->CI->cms->model->load_system('categories', $model, FALSE);
                break;
            
            case 'page_type':
                $this->CI->cms->model->load_system('page_types', $model, FALSE);
                break;
            
            case 'page':
                $this->CI->cms->model->load_system('pages', $model, FALSE);
                break;
            
            case 'panel_type':
                $this->CI->cms->model->load_system('panel_types', $model, FALSE);
                break;
            
            case 'panel':
                $this->CI->cms->model->load_system('panels', $model, FALSE);
                break;
            
            case 'product_category':
                $this->CI->cms->model->load_eshop('categories', $model, FALSE);
                break;
            
            case 'product':
                $this->CI->cms->model->load_eshop('products', $model, FALSE);
                break;
            
            case 'service':
                $this->CI->cms->model->load_system('services', $model, FALSE);
                break;
            
            default:
                return FALSE;
                break;
        }
        
        return TRUE;
    }
    
    function get_used_ids($resource_id = '', $type = '')
    {
        if(!$this->resource_exists($resource_id)) return array();
        if(!$this->rel_type_exists($type)) return array();
        
        $type = $type . '_id';
        
        $this->CI->s_resource_rels_model->where('resource_id', '=', $resource_id);
        $this->CI->s_resource_rels_model->where($type, '>', 0);
        return $this->CI->s_resource_rels_model->get_data_in_col($type);
    }
    
    function get_available_ids($resource_id = '', $type = '')
    {
        if(!$this->resource_exists($resource_id)) return array();
        if(!$this->rel_type_exists($type)) return array();
        if($this->_load_rel_type_table($type) === FALSE) return array();
        return $this->CI->resource_rels_help_model->get_ids_filter($this->get_used_ids($resource_id, $type));
    }
    
    function get_select_data($resource_id = '', $type = '', $plus_id = '')
    {
        if(!$this->resource_exists($resource_id)) return array();
        if(!$this->rel_type_exists($type)) return array();
        
        $select_data = array();
        
        switch($type)
        {
            case 'page_category':
                $this->CI->cms->model->load_system('categories');
                $select_data = $this->CI->s_categories_model->get_data_in_col('_name');
                break;
            
            case 'page_type':
                $this->CI->cms->model->load_system('page_types');
                $select_data = $this->CI->s_page_types_model->get_data_in_col('name');
                break;
            
            case 'page':
                $this->CI->cms->model->load_system('pages');
                $select_data = $this->CI->s_pages_model->get_data_in_col('name');
                break;
            
            case 'panel_type':
                $this->CI->cms->model->load_system('panel_types');
                $select_data = $this->CI->s_panel_types_model->get_data_in_col('name');
                break;
            
            case 'panel':
                $this->CI->cms->model->load_system('panels');
                $select_data = $this->CI->s_panels_model->get_data_in_col('name');
                break;
            
            case 'product_category':
                $this->CI->cms->model->load_eshop('categories');
                $select_data = $this->CI->e_categories_model->get_data_in_col('_name');
                break;
            
            case 'product':
                $this->CI->cms->model->load_eshop('products');
                $select_data = $this->CI->e_products_model->get_data_in_col('_name');
                break;
            
            case 'service':
                $this->CI->cms->model->load_system('services');
                $select_data = $this->CI->s_services_model->get_data_in_col('name');
                break;
            
            default:
                return FALSE;
                break;
        }
        
        foreach($this->get_used_ids($resource_id, $type) as $used_id)
        {
            if($plus_id == $used_id) continue;
            unset($select_data[$used_id]);
        }
        
        return $select_data;
    }
    
    function resource_has_rel($resource_id = '', $type = '', $value_id = '')
    {
        if(!$this->resource_exists($resource_id)) return FALSE;
        if(!$this->rel_type_exists($type)) return FALSE;
        
        $this->CI->s_resource_rels_model->where('resource_id', '=', $resource_id);
        $this->CI->s_resource_rels_model->where('type', '=', $type);
        $this->CI->s_resource_rels_model->where($type . '_id', '=', $value_id);
        return ($this->CI->s_resource_rels_model->get_rows() > 0);
    }
    
    function get_resource_ids($type = '', $type_id = '')
    {
        $theme_id = $this->CI->cms->templates->get_theme();
        
        if($type == 'all')
        {
            $this->CI->s_resources_model->where('theme_id', '=', $theme_id);
            $this->CI->s_resources_model->where('public', '>', '0');
            return $this->CI->s_resources_model->get_ids();
        }
        
        if($type == 'global')
        {
            $this->CI->s_resources_model->where('theme_id', '=', $theme_id);
            $this->CI->s_resources_model->where('public', '>', '0');
            $this->CI->s_resources_model->where('global', '>', '0');
            return $this->CI->s_resources_model->get_ids();
        }
        
        $this->CI->s_resources_model->where('theme_id', '=', $theme_id);
        $this->CI->s_resources_model->where('public', '>', '0');
        $this->CI->s_resources_model->where('global', '<', '1');
        $possible_ids = $this->CI->s_resources_model->get_ids();
        
        $resource_ids = array();
        
        switch($type)
        {
            default:
                return array();
                break;
                
            case 'page_category':
                $this->CI->s_resource_rels_model->where('public', '>', '0');
                $this->CI->s_resource_rels_model->where('type', '=', 'page_category');
                $this->CI->s_resource_rels_model->where('page_category_id', '=', $type_id);
                $resource_ids = $this->CI->s_resource_rels_model->get_data_in_col('resource_id');
                break;
                
            case 'page_type':
                $this->CI->s_resource_rels_model->where('public', '>', '0');
                $this->CI->s_resource_rels_model->where('type', '=', 'page_type');
                $this->CI->s_resource_rels_model->where('page_type_id', '=', $type_id);
                $resource_ids = $this->CI->s_resource_rels_model->get_data_in_col('resource_id');
                break;
                
            case 'page':
                $this->CI->s_resource_rels_model->where('public', '>', '0');
                $this->CI->s_resource_rels_model->where('type', '=', 'page');
                $this->CI->s_resource_rels_model->where('page_id', '=', $type_id);
                $resource_ids = $this->CI->s_resource_rels_model->get_data_in_col('resource_id');
                break;
                
            case 'panel_type':
                $this->CI->s_resource_rels_model->where('public', '>', '0');
                $this->CI->s_resource_rels_model->where('type', '=', 'panel_type');
                $this->CI->s_resource_rels_model->where('panel_type_id', '=', $type_id);
                $resource_ids = $this->CI->s_resource_rels_model->get_data_in_col('resource_id');
                break;
                
            case 'panel':
                $this->CI->s_resource_rels_model->where('public', '>', '0');
                $this->CI->s_resource_rels_model->where('type', '=', 'panel');
                $this->CI->s_resource_rels_model->where('panel_id', '=', $type_id);
                $resource_ids = $this->CI->s_resource_rels_model->get_data_in_col('resource_id');
                break;
                
            case 'product_category':
                $this->CI->s_resource_rels_model->where('public', '>', '0');
                $this->CI->s_resource_rels_model->where('type', '=', 'product_category');
                $this->CI->s_resource_rels_model->where('product_category_id', '=', $type_id);
                $resource_ids = $this->CI->s_resource_rels_model->get_data_in_col('resource_id');
                break;
                
            case 'product':
                $this->CI->s_resource_rels_model->where('public', '>', '0');
                $this->CI->s_resource_rels_model->where('type', '=', 'product');
                $this->CI->s_resource_rels_model->where('product_id', '=', $type_id);
                $resource_ids = $this->CI->s_resource_rels_model->get_data_in_col('resource_id');
                break;
                
            case 'service':
                $this->CI->s_resource_rels_model->where('public', '>', '0');
                $this->CI->s_resource_rels_model->where('type', '=', 'service');
                $this->CI->s_resource_rels_model->where('service_id', '=', $type_id);
                $resource_ids = $this->CI->s_resource_rels_model->get_data_in_col('resource_id');
                break;
        }
        
        return array_intersect($possible_ids, $resource_ids);
    }
    
    function parse_resource($resource = '', $type = '')
    {
        if(is_url($resource)) return $resource;
        
        $resource = $resource . '.' . $type;
        
        return $resource;
    }
    
}
