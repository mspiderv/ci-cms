<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parse_url extends CI_Driver {
    
    // Členské premenné
    protected $CI;
    
    protected $id_type = array();
    protected $type_id = array();
    protected $type;
    protected $content_type;
    protected $id;
    protected $other_segments = array();
    protected $content_types = array('page', 'product', 'category', 'service');
    
    function __construct()
    {
        $this->CI = & get_instance();
        
        $this->id_type = cfg('url', 'id_types');
        $this->type_id = array_flip($this->id_type);
    }
    
    /* Initialize */
    
    protected function _get_type()
    {
        $segments = $this->CI->uri->segment_array();

        if(count($segments) == 0)
        {
            if(db_config_bool('multilang') && db_config_bool('hp_lang_segment'))
            {
                redirect(default_lang());
            }
            else
            {
                return 'homepage';
            }
        }
        
        if(db_config_bool('multilang'))
        {
            $lang_segment = array_shift($segments);

            if(lang_exists($lang_segment))
            {
                if(count($segments) == 0 && default_lang() == $lang_segment)
                {
                    if(db_config_bool('hp_lang_segment'))
                    {
                        set_lang($lang_segment);
                        return 'homepage';
                    }
                    else
                    {
                        redirect('');
                    }
                }
                else
                {
                    set_lang($lang_segment);
                }
            }
            else
            {
                return '404';
            }
        }
        
        return $this->_get_type_from_segments($segments);
    }
    
    protected function _get_type_from_segments($segments = array())
    {
        if(count($segments) > 0)
        {
            $reversed_segments = array_reverse($segments);
            
            foreach($reversed_segments as $segment)
            {
                $type = $this->_parse_id($segment);
                if($type !== FALSE) break;
            }
            
            if($type === FALSE)
            {
                return '404';
            }
            else
            {
                // URL obsahuje viackrát ID segment -> 404
                $segment_counts = array_count_values($segments);
                if($segment_counts[$this->get_id_from_type($type) . cfg('url', 'id_delimiter') . $this->id] > 1) return '404';
                
                return $type;
            }
        }
        else
        {
            return 'homepage';
        }
    }
    
    protected function _parse_id($id = '')
    {
        $id_pieces = explode(cfg('url', 'id_delimiter'), $id);
        
        if(count($id_pieces) == 2 && ((string)$id_pieces[1] == (string)(int)$id_pieces[1]))
        {
            $this->id = $id_pieces[1];
            return $this->get_type_from_id($id_pieces[0]);
        }
        else
        {
            return FALSE;
        }
    }
    
    protected function _get_content_type()
    {
        $type = $this->get_type();
        
        if($this->is_content_type($type)) return $type;
        
        elseif($type == 'homepage')
        {
            $homepage = get_href(db_config('homepage'));
            $content_type = @$homepage['type'];
            $this->id = intval(@$homepage['value']);
            return ($this->is_content_type($content_type)) ? $content_type : FALSE;
        }
        
        elseif($type == '404')
        {
            $page_404 = get_href(db_config('page_404'));
            $content_type = @$page_404['type'];
            $this->id = intval(@$page_404['value']);
            return ($this->is_content_type($content_type)) ? $content_type : FALSE;
        }
        
        else return FALSE;
    }
    
    /* Public */
    
    function set_other_segments($other_segments = array())
    {
        $this->other_segments = $other_segments;
    }
    
    function get_other_segments($string = FALSE)
    {
        return ($string) ? implode('/', $this->other_segments) : $this->other_segments;
    }
    
    function create_id_segment($type = '', $id = '')
    {
        return $this->get_id_from_type($type) . cfg('url', 'id_delimiter') . $id;
    }
    
    function get_id_from_type($type = '')
    {
        return (isset($this->type_id[$type]) ? $this->type_id[$type] : FALSE);
    }
    
    function get_type_from_id($id = '')
    {
        return (isset($this->id_type[$id]) ? $this->id_type[$id] : FALSE);
    }
    
    function set_type($type = '')
    {
        $this->type = $type;
    }
    
    function set_content_type($content_type = '')
    {
        if($this->is_content_type($content_type))
        {
            $this->content_type = $content_type;
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    function set_id($id = '')
    {
        $this->id = $id;
    }
    
    function get_type()
    {
        if($this->type == NULL)
        {
            $this->type = $this->_get_type();
        }
        
        return $this->type;
    }
    
    function get_content_type()
    {
        if($this->content_type == NULL)
        {
            $this->content_type = $this->_get_content_type();
        }
        
        return $this->content_type;
    }
    
    function is_content_type($content_type = '')
    {
        return in_array($content_type, $this->content_types);
    }
    
    function get_id()
    {
        if($this->content_type == NULL)
        {
            $this->content_type = $this->_get_content_type();
        }
        
        return $this->id;
    }
    
    function get_identification()
    {
        return $this->get_type() . cfg('url', 'id_delimiter') . $this->get_id();
    }
    
    function get_plain_url($return_as_array = FALSE)
    {
        $count = intval(count($this->get_other_segments())-1);
        $segments = array_slice($this->CI->uri->segment_array(), (db_config_bool('multilang') ? 1 : 0), (($count > 0) ? -$count : NULL));
        return ($return_as_array) ? $segments : site_url(implode('/', $segments));
    }
    
}