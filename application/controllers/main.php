<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        
        $this->load->driver('parse');
        $this->load->helper('page');
        
        $this->output->enable_profiler(cfg('profiler', 'front'));
    }
    
    function unavailable()
    {
        $this->output->set_status_header('503');
        $this->output->append_output('Site is temporarily unavailable. We will be back soon.');
    }
    
    function index()
    {
        // Try to block user by IP address
        $this->cms->try_block_ip();
        
        // Redirect
        $this->cms->try_redirect();
        
        // Parse
        switch($this->parse->url->get_type())
        {
            case 'homepage':
                $this->_parse_href(db_config('homepage'));
                break;

            case 'page':
                $this->_parse_page();
                break;

            case 'product':
                if(cfg('general', 'eshop')) $this->_parse_product();
                else $this->_parse_404();
                break;

            case 'category':
                if(cfg('general', 'eshop')) $this->_parse_category();
                else $this->_parse_404();
                break;

            case 'service':
                $this->_parse_service();
                break;
            
            default:
            case '404':
                $this->_parse_404();
                break;
        }
    }
    
    protected function _parse_href($href = '')
    {
        @$href_data = get_href($href);

        $id = @$href_data['value'];
        $type = @$href_data['type'];
        
        if($this->parse->url->set_content_type($type) === TRUE)
        {
            $this->parse->url->set_id($id);
        }
        
        switch($type)
        {
            case 'page':
                if($this->parse->page->show($id) === FALSE) $this->_parse_404();
                break;
            
            case 'product':
                if($this->parse->product->show($id) === FALSE) $this->_parse_404();
                break;
            
            case 'category':
                if($this->parse->category->show($id) === FALSE) $this->_parse_404();
                break;
            
            case 'service':
                if($this->parse->service->show($id) === FALSE) $this->_parse_404();
                break;
                
            default:
                $this->_parse_404();
                break;
        }
    }
    
    protected function _parse_404()
    {
        $this->parse->show_404();
    }
    
    protected function _parse_page()
    {
        $page_id = $this->parse->url->get_id();
        
        /*$href = get_href(db_config('page_404'));
        if(@$href['type'] == 'page' && @$href['value'] == $page_id) $this->parse->redirect_hp();*/

        $result = $this->parse->page->check_page_segments($page_id);

        if($result === FALSE)
        {
            if(db_config_bool('page_alias_redirect'))
            {
                redirect($this->parse->page->get_page_url($page_id) . ($this->input->server('QUERY_STRING') != '' ? '?' . $this->input->server('QUERY_STRING') : ''));
            }
            else
            {
                $this->_parse_404();
            }
        }
        else
        {
            $this->cms->model->load_system('pages');
            
            if($this->s_pages_model->$page_id->public)
            {
                set_status_header(200);
                $this->parse->url->set_other_segments($result);
                $this->parse->page->show($page_id);
            }
            else
            {
                $this->_parse_href(db_config('unpublish_page'));
            }
        }
    }
    
    protected function _parse_product()
    {
        $product_id = $this->parse->url->get_id();

        /*$href = get_href(db_config('page_404'));
        if(@$href['type'] == 'product' && @$href['value'] == $product_id) $this->parse->redirect_hp();*/
        
        $result = $this->parse->product->check_product_segments($product_id);

        if($result === FALSE)
        {
            if(db_config_bool('product_alias_redirect'))
            {
                redirect($this->parse->product->get_product_url($product_id) . ($this->input->server('QUERY_STRING') != '' ? '?' . $this->input->server('QUERY_STRING') : ''));
            }
            else
            {
                $this->_parse_404();
            }
        }
        else
        {
            $this->cms->model->load_eshop('products');
            
            if($this->e_products_model->$product_id->public)
            {
                if($this->e_products_model->$product_id->quantity > 0)
                {
                    set_status_header(200);
                    $this->parse->url->set_other_segments($result);
                    $this->parse->product->show($product_id);
                }
                else
                {
                    $this->_parse_href(db_config('product_sold'));
                }
            }
            else
            {
                $this->_parse_href(db_config('unpublish_product'));
            }
        }
    }
    
    protected function _parse_category()
    {
        $category_id = $this->parse->url->get_id();

        /*$href = get_href(db_config('page_404'));
        if(@$href['type'] == 'category' && @$href['value'] == $category_id) $this->parse->redirect_hp();*/
        
        $result = $this->parse->category->check_category_segments($category_id);

        if($result === FALSE)
        {
            if(db_config_bool('category_alias_redirect'))
            {
                redirect($this->parse->category->get_category_url($category_id) . ($this->input->server('QUERY_STRING') != '' ? '?' . $this->input->server('QUERY_STRING') : ''));
            }
            else
            {
                $this->_parse_404();
            }
        }
        else
        {
            $this->cms->model->load_eshop('categories');
            
            if($this->e_categories_model->$category_id->public)
            {
                set_status_header(200);
                $this->parse->url->set_other_segments($result);
                $this->parse->category->show($category_id);
            }
            else
            {
                $this->_parse_href(db_config('unpublish_category'));
            }
        }
    }
    
    protected function _parse_service()
    {
        $service_id = $this->parse->url->get_id();

        /*$href = get_href(db_config('page_404'));
        if(@$href['type'] == 'service' && @$href['value'] == $service_id) $this->parse->redirect_hp();*/
        
        $result = $this->parse->service->check_service_segments($service_id);

        if($result === FALSE)
        {
            if(db_config_bool('service_alias_redirect'))
            {
                redirect($this->parse->service->get_service_url($service_id) . ($this->input->server('QUERY_STRING') != '' ? '?' . $this->input->server('QUERY_STRING') : ''));
            }
            else
            {
                $this->_parse_404();
            }
        }
        else
        {
            $this->cms->model->load_system('services');
            
            if($this->s_services_model->$service_id->public)
            {
                set_status_header(200);
                $this->parse->url->set_other_segments($result);
                $this->parse->service->show($service_id);
            }
            else
            {
                $this->_parse_href(db_config('unpublish_service'));
            }
        }
    }
    
    function sitemap()
    {
        if(!db_config_bool('generate_sitemap')) return $this->index();
        
        $this->output->set_content_type('xml');
        
        $items = array();
        
        // Base
        
        if(substr(base_url(), 0, 4) == 'http') $base = '';
        else $base = ((strpos($_SERVER['SERVER_PROTOCOL'], 'https') > -1) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        
        // Pages
        
        if(db_config_bool('sitemap_pages'))
        {
            $this->cms->model->load_system('pages');

            foreach($this->s_pages_model->get_data() as $page)
            {
                if(intval($page->public) < 1) continue;
                if(intval($page->index) < 1) continue;

                $items[] = array(
                    'url' => $base . href_page($page->id, '', TRUE),
                    'lastmod' => $page->lastmod,
                    'changefreq' => $page->changefreq,
                    'priority' => doubleval($page->sitemap_priority)
                );
            }
        }
        
        if(cfg('general', 'eshop'))
        {
            // Products
            
            if(db_config_bool('sitemap_products'))
            {
                $this->cms->model->load_eshop('products');

                foreach($this->e_products_model->get_data() as $product)
                {
                    if(intval($product->public) < 1) continue;
                    if(intval($product->index) < 1) continue;

                    $items[] = array(
                        'url' => $base . href_product($product->id, '', TRUE),
                        'lastmod' => $product->lastmod,
                        'changefreq' => $product->changefreq,
                        'priority' => doubleval($product->sitemap_priority)
                    );
                }
            }

            // Categories

            if(db_config_bool('sitemap_categories'))
            {
                $this->cms->model->load_eshop('categories');

                foreach($this->e_categories_model->get_data() as $category)
                {
                    if(intval($category->public) < 1) continue;
                    if(intval($category->index) < 1) continue;

                    $items[] = array(
                        'url' => $base . href_category($category->id, '', TRUE),
                        'lastmod' => $category->lastmod,
                        'changefreq' => $category->changefreq,
                        'priority' => doubleval($category->sitemap_priority)
                    );
                }
            }
        }
        
        // Services
        
        if(db_config_bool('sitemap_services'))
        {
            $this->cms->model->load_system('services');

            foreach($this->s_services_model->get_data() as $service)
            {
                if(intval($service->public) < 1) continue;
                if(intval($service->index) < 1) continue;

                $items[] = array(
                    'url' => $base . href_service($service->id, '', TRUE),
                    'lastmod' => $service->lastmod,
                    'changefreq' => $service->changefreq,
                    'priority' => doubleval($service->sitemap_priority)
                );
            }
        }
        
        $this->cms->load_system_view('xml/sitemap', array('items' => $items));
    }
    
}