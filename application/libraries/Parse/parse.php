<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parse extends CI_Driver_Library {
    
    protected $CI;
    protected $valid_drivers = array(
        'parse_url',
        'parse_page',
        'parse_product',
        'parse_category',
        'parse_service',
        'parse_panel',
        'parse_part',
        'parse_lang'
    );
    protected $fields = NULL;
    protected $resource_ids = array();
    protected $resource_added = array();
    protected $resource_content = NULL;
    
    function  __construct()
    {
        $this->CI =& get_instance();
        
        if(!db_config_bool('multilang'))
        {
            $this->CI->cms->model->load_system('domains');
            $domain = $this->CI->input->get_request_header('Host', TRUE);
            $domains = array_flip($this->CI->s_domains_model->get_data_in_col('domain'));

            if(isset($domains[$domain]))
            {
                $domain_id = $domains[$domain];
                $lang_id = $this->CI->s_domains_model->$domain_id->lang_id;
                $theme_id = $this->CI->s_domains_model->$domain_id->theme_id;

                if($this->CI->s_langs_model->item_exists($lang_id))
                {
                    set_lang_id($lang_id);
                }

                $this->CI->cms->model->load_system('themes');

                if($this->CI->s_themes_model->item_exists($theme_id))
                {
                    $this->CI->cms->templates->set_theme($theme_id);
                }
            }
        }
    }
    
    function load_view($view = '', $data = array(), $return = FALSE, $parse = TRUE)
    {
        $content = $this->CI->load->view(cfg('folder', 'front') . '/' . $this->CI->cms->templates->get_theme_folder() . '/' . $view, $data, TRUE);
        
        if($parse)
        {
            $this->CI->load->driver('parse');
            $content = $this->CI->parse->parse_text($content);
        }
        
        if($return) return $content;
        else $this->CI->output->append_output($content);
    }
    
    function show_404()
    {
        set_status_header(404);
        
        $href = get_href(db_config('page_404'));
        
        $id = @$href['value'];
        
        $this->CI->load->driver('parse');
        
        switch(@$href['type'])
        {
            case 'page':
                if($this->CI->parse->page->show($id) === FALSE) show_404();
                break;
            
            case 'product':
                if($this->CI->parse->product->show($id) === FALSE) show_404();
                break;
            
            case 'category':
                if($this->CI->parse->category->show($id) === FALSE) show_404();
                break;
            
            case 'service':
                if($this->CI->parse->service->show($id) === FALSE) show_404();
                break;
            
            default:
                show_404();
                break;
        }
    }
    
    function redirect_hp()
    {
        if(db_config_bool('multilang') && db_config_bool('hp_lang_segment'))
        {
            redirect(default_lang());
        }
        else
        {
            redirect();
        }
    }
    
    /**
     * Generate metódy
     * Každú metódu, ktorá začína reťazcom '_generate_X' bude možné v šablónach zavolať ako generate('X', [parametre])
     */
    
    function _generate_panel($panel_code = '')
    {
        return $this->_generate_panel_id($this->CI->cms->panels->get_panel_id_by_code($panel_code));
    }
    
    function _generate_panel_id($panel_id = '')
    {
        return $this->CI->parse->panel->generate_panel($panel_id);
    }
    
    function _generate_position($position_code = '')
    {
        return $this->_generate_position_id($this->CI->cms->positions->get_position_id_by_code($position_code));
    }
    
    function _generate_position_id($position_id = '')
    {
        return $this->CI->parse->panel->generate_position($position_id);
    }
    
    function _generate_part($part_code = '', $lang = '')
    {
        return $this->_generate_part_id($this->CI->parse->part->get_part_id_by_code($part_code), $lang);
    }
    
    function _generate_part_id($part_id = '', $lang = '')
    {
        return $this->CI->parse->part->generate_part($part_id, $lang);
    }
    
    function add_resources($type = '', $type_id = '')
    {
        if(!is_array($this->resource_ids)) $this->resource_ids = array();
        if(@in_array($type_id, @$this->resource_added[$type])) return;
        
        @$this->resource_added[$type][] = $type_id;
        $this->resource_ids = array_merge($this->resource_ids, $this->CI->cms->resources->get_resource_ids($type, $type_id));
    }
    
    function _generate_title_tag()
    {
        $title = '';
        
        $id = $this->CI->parse->url->get_id();
        
        switch($this->CI->parse->url->get_content_type())
        {
            case 'page':
                $this->CI->cms->model->load_system('pages');
                if($this->CI->s_pages_model->item_exists($id))
                {
                    $title = $this->CI->s_pages_model->$id->_meta_title;
                    if(strlen($title) == 0) $title = $this->CI->s_pages_model->$id->name;
                }
                break;

            case 'product':
                $this->CI->cms->model->load_eshop('products');
                if($this->CI->e_products_model->item_exists($id))
                {
                    $title = $this->CI->e_products_model->$id->_meta_title;
                    if(strlen($title) == 0) $title = $this->CI->e_products_model->$id->_name;
                }
                break;

            case 'category':
                $this->CI->cms->model->load_eshop('categories');
                if($this->CI->e_categories_model->item_exists($id))
                {
                    $title = $this->CI->e_categories_model->$id->_meta_title;
                    if(strlen($title) == 0) $title = $this->CI->e_categories_model->$id->_name;
                }
                break;

            case 'service':
                $this->CI->cms->model->load_system('services');
                if($this->CI->s_services_model->item_exists($id))
                {
                    $title = $this->CI->s_services_model->$id->_meta_title;
                    if(strlen($title) == 0) $title = $this->CI->s_services_model->$id->name;
                }
                break;
        }
        
        $title = db_config('_global_meta_title_prefix') . $title . db_config('_global_meta_title_suffix');
        
        return '<title>' . $title . '</title><meta property="og:title" content="' . $title . '" />';
    }
    
    function _generate_keywords_tag()
    {
        $keywords = '';
        
        $id = $this->CI->parse->url->get_id();
        
        switch($this->CI->parse->url->get_content_type())
        {
            case 'page':
                $this->CI->cms->model->load_system('pages');
                if($this->CI->s_pages_model->item_exists($id))
                {
                    $keywords = $this->CI->s_pages_model->$id->_meta_keywords;
                }
                break;

            case 'product':
                $this->CI->cms->model->load_eshop('products');
                if($this->CI->e_products_model->item_exists($id))
                {
                    $keywords = $this->CI->e_products_model->$id->_meta_keywords;
                }
                break;

            case 'category':
                $this->CI->cms->model->load_eshop('categories');
                if($this->CI->e_categories_model->item_exists($id))
                {
                    $keywords = $this->CI->e_categories_model->$id->_meta_keywords;
                }
                break;

            case 'service':
                $this->CI->cms->model->load_system('services');
                if($this->CI->s_services_model->item_exists($id))
                {
                    $keywords = $this->CI->s_services_model->$id->_meta_keywords;
                }
                break;
        }
        
        if($keywords == '') $keywords = db_config('_global_meta_keywords');
        
        return ($keywords != '') ? '<meta name="keywords" content="' . $keywords . '" />' : '';
    }
    
    function _generate_description_tag()
    {
        $description = '';
        
        $id = $this->CI->parse->url->get_id();
        
        switch($this->CI->parse->url->get_content_type())
        {
            case 'page':
                $this->CI->cms->model->load_system('pages');
                if($this->CI->s_pages_model->item_exists($id))
                {
                    $description = $this->CI->s_pages_model->$id->_meta_description;
                }
                break;

            case 'product':
                $this->CI->cms->model->load_eshop('products');
                if($this->CI->e_products_model->item_exists($id))
                {
                    $description = $this->CI->e_products_model->$id->_meta_description;
                }
                break;

            case 'category':
                $this->CI->cms->model->load_eshop('categories');
                if($this->CI->e_categories_model->item_exists($id))
                {
                    $description = $this->CI->e_categories_model->$id->_meta_description;
                }
                break;

            case 'service':
                $this->CI->cms->model->load_system('services');
                if($this->CI->s_services_model->item_exists($id))
                {
                    $description = $this->CI->s_services_model->$id->_meta_description;
                }
                break;
        }
        
        if($description == '') $description = db_config('_global_meta_description');
        
        return ($description != '') ? '<meta name="description" content="' . $description . '" /><meta property="og:description" content="' . $description . '" />' : '';
    }
    
    function _generate_favicon()
    {
        $favicon = $this->CI->cms->templates->get_favicon();
        
        if(strlen($favicon) > 0)
        {
            return '<link rel="shortcut icon" href="' . $favicon . '" />';
        }
    }
    
    function _generate_social()
    {
        return '';
		return '<meta property="og:image" content="PATH_TO_IMG_HERE" />';
    }
    
    function _generate_robots()
    {
        return '<meta name="robots" content="index, follow" />';
    }
    
    function _generate_js_vars()
    {
        $data = array();
        
        $data['csrf_token_name'] = $this->CI->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->CI->security->get_csrf_hash();
        
        return $this->CI->cms->load_system_view('js/vars', $data, TRUE);
    }
    
    function _generate_resources()
    {
        if($this->resource_content == NULL)
        {
            $this->CI->load->library('carabiner');

            $path = cfg('folder', 'assets') . '/' . cfg('folder', 'themes') . '/' . $this->CI->cms->templates->get_theme_folder() . '/';

            $config = array(
                'script_dir' => $path,
                'style_dir' => $path
            );

            $this->CI->carabiner->config($config);

            $css = array();
            $js = array();

            if(!is_array($this->resource_ids)) $this->resource_ids = array();
            
            $this->resource_ids = array_merge($this->CI->cms->resources->get_resource_ids('global'), $this->resource_ids);
            
            foreach(array_intersect($this->CI->cms->resources->get_resource_ids('all'), array_unique($this->resource_ids)) as $global_resource_id)
            {
                $url = $this->CI->s_resources_model->get_item_data($global_resource_id, 'url');
                $type = $this->CI->s_resources_model->get_item_data($global_resource_id, 'type');

                if($type == 'css' && !in_array($url, $css))
                {
                    $css[] = array($this->CI->cms->resources->parse_resource($url, 'css'));
                }

                elseif($type == 'js' && !in_array($url, $js))
                {
                    $js[] = array($this->CI->cms->resources->parse_resource($url, 'js'));
                }
            }

            $data = array();

            if(count($css) > 0) $data['css'] = $css;
            if(count($js) > 0) $data['js'] = $js;

            if(count($data) > 0)
            {
                $this->CI->carabiner->group('main_resources', $data);
                $this->resource_content = $this->CI->carabiner->display_string('main_resources');
            }
            else
            {
                $this->resource_content = '';
            }
        }
        
        return $this->resource_content;
    }
    
    function _generate_head()
    {
        $head = '';
        
        $head .= $this->_generate_robots();
        $head .= $this->_generate_title_tag();
        $head .= $this->_generate_keywords_tag();
        $head .= $this->_generate_description_tag();
        $head .= $this->_generate_js_vars();
        $head .= $this->_generate_resources();
        $head .= $this->_generate_favicon();
        $head .= $this->_generate_social();
        
        return $head;
    }
    
    function parse_text($mixed = '')
    {
        if(is_array($mixed))
        {
            foreach($mixed as $mixed_key => $mixed_value)
            {
                $mixed[$mixed_key] = $this->parse_text($mixed_value);
            }
        }
        
        else
        {
            if(cfg('parse', 'emails') == TRUE) $mixed = $this->parse_text_emails($mixed);
            if(cfg('parse', 'panels')  == TRUE) $mixed = $this->parse_text_panels($mixed);
            if(cfg('parse', 'positions')  == TRUE) $mixed = $this->parse_text_positions($mixed);
            if(cfg('parse', 'hrefs')  == TRUE) $mixed = $this->parse_text_hrefs($mixed);
        }

        return $mixed;
    }
    
    function parse_text_emails($string)
    {
        // CONTINUE: toto by bolo vhodne dorobit
        
        $email_pattern = '[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}';

        $string = preg_replace_callback('/<a(.*?)>(' . $email_pattern . ')<\/a>/', function($data) {
            return safe_mailto($data[2]);
        }, $string);
        $string = preg_replace_callback('/' . $email_pattern . '/', function ($data) {
            return safe_mailto($data[0]);
        }, $string);

        return $string;
    }

    function parse_text_panels($string)
    {
        /* {PANEL:1} */
        $string = preg_replace_callback('/(\/){0,1}{' . cfg('parser_val', 'panel') . ':([0-9]*)}/', function ($data) {
            return generate("panel_id", $data[2]);
        }, $string);

        /* {PANEL:panel_code} */
        $string = preg_replace_callback('/(\/){0,1}{' . cfg('parser_val', 'panel') . ':([a-zA-Z0-9-]*)}/', function ($data) {
            return generate("panel", $data[2]);
        }, $string);

        return $string;
    }
    
    function parse_text_positions($string)
    {
        /* {POSITION:1} */
        $string = preg_replace_callback('/(\/){0,1}{' . cfg('parser_val', 'position') . ':([0-9]*)}/', function ($data) {
            return generate("position_id", $data[2]);
        }, $string);

        /* {POSITION:position_code} */
        $string = preg_replace_callback('/(\/){0,1}{' . cfg('parser_val', 'position') . ':([a-zA-Z0-9-]*)}/', function ($data) {
            return generate("position", $data[2]);
        }, $string);

        return $string;
    }

    function parse_text_hrefs($string)
    {
        /* {HREF:page:1:sk}, {HREF:service:2:en} ... */
        $this->CI->cms->model->load_system('langs');
        $langs = implode('|', $this->CI->s_langs_model->get_data_in_col('lang'));
        $string = preg_replace_callback('/(\/){0,1}{' . cfg('parser_val', 'href') . ':(page|product|category|service):([0-9]*):(' . $langs . ')}/', function ($data) {
            return href_by_type($data[2], $data[3], $data[4]);
        }, $string);
        
        /* {HREF:page:1} */
        $string = preg_replace_callback('/(\/){0,1}{' . cfg('parser_val', 'href') . ':(page|product|category|service):([0-9]*)}/', function ($data) {
            return href_by_type($data[2], $data[3]);
        }, $string);
        
        return $string;
    }
    
}
