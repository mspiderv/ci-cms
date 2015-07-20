<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Default_panel_type_library {
    
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
    }
    
    function menu($panel_data)
    {
        $this->CI->cms->model->load_system('menu_links');
        
        $data = array();
        $data['links'] = array();
        
        $data['settings'] = array();
        
        $data['settings']['menu_open'] = @$panel_data['menu_open'];
        $data['settings']['menu_close'] = @$panel_data['menu_close'];
        $data['settings']['link'] = @$panel_data['link'];
        $data['settings']['link_open'] = @$panel_data['link_open'];
        $data['settings']['link_close'] = @$panel_data['link_close'];
        $data['settings']['link_separator'] = @$panel_data['link_separator'];
        
        $data['links'] = $this->_menu_get_links($panel_data['menu_id'], NULL, @$panel_data['levels']);
        
        return $data;
    }
    
    protected function _menu_get_links($menu_id = '', $parent_link_id = '', $levels = 0, $level = 0)
    {
        if($levels > 0 && $level >= $levels) return array();
        
        $links = array();
        
        $this->CI->cms->model->load_system('menu_links');
        
        $this->CI->s_menu_links_model->where('public', '>', '0');
        $this->CI->s_menu_links_model->where('menu_id', '=', $menu_id);
        $this->CI->s_menu_links_model->where('parent_link_id', '=', intval($parent_link_id));
        
        foreach($this->CI->s_menu_links_model->get_data() as $link)
        {
            $links[] = array(
                'link' => $link,
                'subs' => $this->_menu_get_links($menu_id, $link->id, $levels, $level + 1)
            );
        }
        
        return $links;
    }
    
    function list_data($panel_data, $var_list_id = 'list_id', $var_data = 'data')
    {
        $this->CI->load->model('lists_model');
        return array($var_data => $this->CI->lists_model->get_list_data($panel_data[$var_list_id]));
    }
    
}