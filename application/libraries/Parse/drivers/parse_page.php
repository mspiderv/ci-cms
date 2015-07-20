<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parse_page extends CI_Driver {
    
    // Členské premenné
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->CI->cms->model->load_system('pages');
    }
    
    function get_page_url($page_id = '', $lang = '')
    {
        if(!$this->CI->cms->pages->page_exists($page_id)) return FALSE;
        
        return $this->get_page_segments($page_id, TRUE, $lang) . '/' . $this->CI->parse->url->create_id_segment('page', $page_id);
    }
    
    function get_page_segments($page_id = '', $string = FALSE, $lang = '')
    {
        if($this->CI->cms->pages->page_exists($page_id))
        {
            $alias_var = $lang . '_alias';
            $segments = array();
            
            // Aliasy nadradených stránok
            if(!db_config_bool('page_only_one_alias'))
            {
                foreach(array_reverse($this->CI->cms->pages->get_page_parents($page_id)) as $parent_page_id)
                {
                    $segments[] = $this->CI->s_pages_model->$parent_page_id->$alias_var;
                }
            }
            
            // Alias konkrétnej stránky
            $alias = $this->CI->s_pages_model->$page_id->$alias_var;
            if(strlen($alias) > 0) $segments[] = $alias;
            
            return ($string) ? implode('/', $segments) : $segments;
        }
        else
        {
            return ($string) ? '' : array();
        }
    }
    
    function check_page_segments($page_id = '', $segments = NULL)
    {
        if(!$this->CI->cms->pages->page_exists($page_id)) return FALSE;
        
        if($segments == NULL) $segments = $this->CI->uri->segment_array();
        
        $real_segments = $this->get_page_segments($page_id);
        $real_segments_count = count($real_segments);
        
        if(implode('/', $real_segments) == implode('/', array_slice($segments, 0, $real_segments_count)))
        {
            return array_slice($segments, $real_segments_count + 1);
        }
        else
        {
            return FALSE;
        }
    }
    
    function get_page_tpl($page_id = '')
    {
        if(!$this->CI->cms->pages->page_exists($page_id)) return '';
        
        $page_tpl = $this->CI->cms->pages->get_page_data($page_id, 'tpl');
        if(strlen($page_tpl) > 0) return $page_tpl;
        
        $page_type_id = $this->CI->cms->pages->get_page_data($page_id, 'page_type_id');
        
        $this->CI->cms->model->load_system('page_types');
        
        return $this->CI->s_page_types_model->$page_type_id->tpl;
    }
    
    function show($page_id = '')
    {
        if(!$this->CI->cms->pages->page_exists($page_id)) return FALSE;
        
        $this->CI->cms->model->load_system('page_types');
        
        $page_data = (array)$this->CI->cms->pages->get_page_data($page_id);
        $page_type_data = $this->CI->s_page_types_model->get_item($page_data['page_type_id']);
        
        // Add resources
        $this->CI->parse->add_resources('page', $page_id);
        $this->CI->parse->add_resources('page_type', $page_data['page_type_id']);
        
        $page_tpl = cfg('folder', 'pages') . '/' . $this->get_page_tpl($page_id);
        $page_data[cfg('variable', 'lang')] = $this->CI->parse->lang->get_lang($page_tpl);
        
        foreach($this->CI->cms->pages->get_page_categories($page_id) as $page_category_id)
        {
            $this->CI->parse->add_resources('page_category', $page_category_id);
        }
        
        if(strlen($page_type_data->class) > 0 && strlen($page_type_data->method) > 0)
        {
            if($this->CI->cms->libraries->library_exists($page_type_data->class, 'page_types'))
            {
                if($this->CI->cms->libraries->load_library($page_type_data->class, 'page_types', 'page_type_library'))
                {
                    if(method_exists($this->CI->page_type_library, $page_type_data->method))
                    {
                        $arguments = array((array)$page_data);
                        if(strlen($page_type_data->parameters) > 0) $arguments = array_merge($arguments, explode('|', $page_type_data->parameters));
                        $page_type_library_result = (array)call_user_func_array(array(&$this->CI->page_type_library, $page_type_data->method), $arguments);
                        $page_data = array_merge($page_data, $page_type_library_result);
                    }
                    else
                    {
                        show_error('Typ stránky s ID <strong>' . $page_data['page_type_id'] . '</strong> má určenú neexistujúcu metódu.');
                    }
                }
                else
                {
                    show_error('Knižnicu typu stránky sa nepodarilo načítať.');
                }
            }
            else
            {
                show_error('Typ stránky s ID <strong>' . $page_data['page_type_id'] . '</strong> má určenú neexistujúcu knižnicu.');
            }
        }
        
        $this->load_view($page_tpl, $page_data);
    }
    
}