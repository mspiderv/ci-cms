<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parse_panel extends CI_Driver {
    
    // Členské premenné
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->CI->cms->model->load_system('panels');
    }
    
    function get_panel_tpl($panel_id = '')
    {
        if(!$this->CI->cms->panels->panel_exists($panel_id)) return '';
        
        $panel_type_id = $this->CI->cms->panels->get_panel_data($panel_id, 'panel_type_id');
        $this->CI->cms->model->load_system('panel_types');
        return $this->CI->s_panel_types_model->$panel_type_id->tpl;
    }
    
    function generate_panel($panel_id = '')
    {
        if(!$this->CI->cms->panels->panel_exists($panel_id)) return '';
        if(!$this->CI->cms->panels->is_public($panel_id)) return '';
        
        $this->CI->cms->model->load_system('panel_types');
        
        $panel_data = (array)$this->CI->cms->panels->get_panel_data($panel_id);
        $panel_type_data = $this->CI->s_panel_types_model->get_item($panel_data['panel_type_id']);
        
        $panel_tpl = cfg('folder', 'panels') . '/' . $this->get_panel_tpl($panel_id);
        $panel_data[cfg('variable', 'lang')] = $this->CI->parse->lang->get_lang($panel_tpl);
        
        // Add resources
        $this->CI->parse->add_resources('panel', $panel_id);
        $this->CI->parse->add_resources('panel_type', $panel_data['panel_type_id']);
        
        if(strlen($panel_type_data->class) > 0 && strlen($panel_type_data->method) > 0)
        {
            if($this->CI->cms->libraries->library_exists($panel_type_data->class, 'panel_types'))
            {
                if($this->CI->cms->libraries->load_library($panel_type_data->class, 'panel_types', 'panel_type_library'))
                {
                    if(method_exists($this->CI->panel_type_library, $panel_type_data->method))
                    {
                        $arguments = array((array)$panel_data);
                        if(strlen($panel_type_data->parameters) > 0) $arguments = array_merge($arguments, explode('|', $panel_type_data->parameters));
                        $panel_type_library_result = (array)call_user_func_array(array(&$this->CI->panel_type_library, $panel_type_data->method), $arguments);
                        $panel_data = array_merge($panel_data, $panel_type_library_result);
                    }
                    else
                    {
                        show_error('Typ panela s ID <strong>' . $panel_data['panel_type_id'] . '</strong> má určenú neexistujúcu metódu.');
                    }
                }
                else
                {
                    show_error('Knižnicu typu panela sa nepodarilo načítať.');
                }
            }
            else
            {
                show_error('Typ panela s ID <strong>' . $panel_data['panel_type_id'] . '</strong> má určenú neexistujúcu knižnicu.');
            }
        }
        
        return $this->load_view($panel_tpl, $panel_data, TRUE);
    }
    
    function generate_position($position_id = '')
    {
        if(!$this->CI->cms->positions->position_exists($position_id)) return '';
        if(!$this->CI->cms->positions->is_public($position_id)) return '';
        
        $result = '';
        
        foreach($this->CI->cms->positions->get_panel_ids($position_id) as $panel_id)
        {
            $result = $result . $this->generate_panel($panel_id);
        }
        
        return $result;
    }
    
}