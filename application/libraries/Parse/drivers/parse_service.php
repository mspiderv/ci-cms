<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parse_service extends CI_Driver {
    
    // Členské premenné
    protected $CI;
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->CI->cms->model->load_system('services');
    }
    
    function get_service_url($service_id = '', $lang = '')
    {
        if(!$this->CI->cms->services->service_exists($service_id)) return FALSE;
        
        return $this->get_service_segments($service_id, TRUE, $lang) . '/' . $this->CI->parse->url->create_id_segment('service', $service_id);
    }
    
    function get_service_segments($service_id = '', $string = FALSE, $lang = '')
    {
        if($this->CI->cms->services->service_exists($service_id))
        {
            $alias_var = $lang . '_alias';
            $segments = array();
            
            // Alias konkrétnej služby
            $alias = $this->CI->s_services_model->$service_id->$alias_var;
            if(strlen($alias) > 0) $segments[] = $alias;
            
            return ($string) ? implode('/', $segments) : $segments;
        }
        else
        {
            return ($string) ? '' : array();
        }
    }
    
    function check_service_segments($service_id = '', $segments = NULL)
    {
        if(!$this->CI->cms->services->service_exists($service_id)) return FALSE;
        
        if($segments == NULL) $segments = $this->CI->uri->segment_array();
        
        $real_segments = $this->get_service_segments($service_id);
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
    
    function get_service_tpl($service_id = '')
    {
        if(!$this->CI->cms->services->service_exists($service_id)) return '';
        
        return $this->CI->cms->services->get_service_data($service_id, 'tpl');
    }
    
    function show($service_id = '')
    {
        if(!$this->CI->cms->services->service_exists($service_id)) return FALSE;
        
        // Add resources
        $this->CI->parse->add_resources('service', $service_id);
        
        $service_data = $this->CI->cms->services->get_service_data($service_id);
        
        $service_tpl = cfg('folder', 'services') . '/' . $this->get_service_tpl($service_id);
        
        if(is_object($service_data))
        {
            $lang_var_name = cfg('variable', 'lang');
            $service_data->$lang_var_name = $this->CI->parse->lang->get_lang($service_tpl);
        }
        else
        {
            $service_data[cfg('variable', 'lang')] = $this->CI->parse->lang->get_lang($service_tpl);
        }
        
        if(strlen($service_data->class) > 0 && strlen($service_data->method) > 0)
        {
            if($this->CI->cms->libraries->library_exists($service_data->class, 'services'))
            {
                if($this->CI->cms->libraries->load_library($service_data->class, 'services', 'service_library'))
                {
                    if(method_exists($this->CI->service_library, $service_data->method))
                    {
                        $arguments = array_merge(array((array)$service_data), $this->CI->parse->url->get_other_segments());
                        if(strlen($service_data->parameters) > 0) $arguments = array_merge($arguments, explode('|', $service_data->parameters));
                        $service_library_result = (array)call_user_func_array(array(&$this->CI->service_library, $service_data->method), $arguments);
                        $service_data = array_merge((array)$service_data, (array)$service_library_result);
                    }
                    else
                    {
                        show_error('Služba s ID <strong>' . $service_id . '</strong> má určenú neexistujúcu metódu.');
                    }
                }
                else
                {
                    show_error('Knižnicu služby sa nepodarilo načítať.');
                }
            }
            else
            {
                show_error('Služba s ID <strong>' . $service_id . '</strong> má určenú neexistujúcu knižnicu.');
            }
        }
        
        $this->load_view($service_tpl, $service_data);
    }
    
}