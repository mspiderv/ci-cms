<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
    }
    
    function index()
    {
        $this->admin->form->set_rules('multilang', __('field_7_3'), 'trim|intval');
        $this->admin->form->set_rules('hp_lang_segment', __('field_7_4'), 'trim|intval');
        $this->admin->form->set_rules('homepage', __('field_7_1'), 'trim|internal|required_internal');
        $this->admin->form->set_rules('page_404', __('field_7_2'), 'trim|internal|required_internal');
        
        if(cfg('general', 'eshop'))
        {
            $this->admin->form->set_rules('order_id_format', __('field_1_1'), 'trim|required|order_id_format|strtoupper');

            $this->admin->form->set_rules('default_product_tpl', __('field_2_1'), 'trim|required|tpl[products]');
            $this->admin->form->set_rules('product_sold', __('field_2_8'), 'trim|internal|required_internal');
            $this->admin->form->set_rules('unpublish_product', __('field_2_9'), 'trim|internal|required_internal');
            $this->admin->form->set_rules('remember_product_parameters', __('field_2_2'), 'trim|intval');
            $this->admin->form->set_rules('remember_product_variables', __('field_2_3'), 'trim|intval');
            $this->admin->form->set_rules('remember_product_variants', __('field_2_4'), 'trim|intval');
            $this->admin->form->set_rules('remember_product_variant_parameters', __('field_2_5'), 'trim|intval');
            $this->admin->form->set_rules('product_only_one_alias', __('field_2_6'), 'trim|intval');
            $this->admin->form->set_rules('product_alias_redirect', __('field_2_7'), 'trim|intval');
        }
        
        $this->admin->form->set_rules('_global_meta_title_prefix', __('field_3_1'), 'max_length[255]');
        $this->admin->form->set_rules('_global_meta_title_suffix', __('field_3_4'), 'max_length[255]');
        $this->admin->form->set_rules('_global_meta_description', __('field_3_2'), 'trim');
        $this->admin->form->set_rules('_global_meta_keywords', __('field_3_3'), 'trim');
        $this->admin->form->set_rules('robots', __('field_3_5'), 'trim');
        $this->admin->form->set_rules('generate_sitemap', __('field_3_6'), 'trim|intval');
        $this->admin->form->set_rules('sitemap_pages', __('field_3_7'), 'trim|intval');
        
        if(cfg('general', 'eshop'))
        {
            $this->admin->form->set_rules('sitemap_products', __('field_3_8'), 'trim|intval');
            $this->admin->form->set_rules('sitemap_categories', __('field_3_9'), 'trim|intval');
        }
        
        $this->admin->form->set_rules('sitemap_services', __('field_3_10'), 'trim|intval');
        
        $this->admin->form->set_rules('page_only_one_alias', __('field_4_1'), 'trim|intval');
        $this->admin->form->set_rules('page_alias_redirect', __('field_4_2'), 'trim|intval');
        $this->admin->form->set_rules('unpublish_page', __('field_4_3'), 'trim|internal|required_internal');
        
        if(cfg('general', 'eshop'))
        {
            $this->admin->form->set_rules('default_category_tpl', __('field_5_1'), 'trim|required|tpl[categories]');
            $this->admin->form->set_rules('category_only_one_alias', __('field_5_2'), 'trim|intval');
            $this->admin->form->set_rules('category_alias_redirect', __('field_5_3'), 'trim|intval');
            $this->admin->form->set_rules('unpublish_category', __('field_5_4'), 'trim|internal|required_internal');
        }
        
        $this->admin->form->set_rules('service_alias_redirect', __('field_6_1'), 'trim|intval');
        $this->admin->form->set_rules('unpublish_service', __('field_6_2'), 'trim|internal|required_internal');
        
        $this->admin->form->set_rules('email_from_name', __('field_8_1'), 'trim|required');
        $this->admin->form->set_rules('email_from_email', __('field_8_2'), 'trim|required|valid_email');
        
        if($this->admin->form->validate())
        {
            db_config_bool('multilang', is_form_true($this->input->post('multilang')));
            db_config_bool('hp_lang_segment', is_form_true($this->input->post('hp_lang_segment')));
            db_config('homepage', $this->input->post('homepage'));
            db_config('page_404', $this->input->post('page_404'));
            
            if(cfg('general', 'eshop'))
            {
                db_config('order_id_format', $this->input->post('order_id_format'));

                db_config('default_product_tpl', $this->input->post('default_product_tpl'));
                db_config('product_sold', $this->input->post('product_sold'));
                db_config('unpublish_product', $this->input->post('unpublish_product'));
                db_config_bool('remember_product_parameters', is_form_true($this->input->post('remember_product_parameters')));
                db_config_bool('remember_product_variables', is_form_true($this->input->post('remember_product_variables')));
                db_config_bool('remember_product_variants', is_form_true($this->input->post('remember_product_variants')));
                db_config_bool('remember_product_variant_parameters', is_form_true($this->input->post('remember_product_variant_parameters')));
                db_config_bool('product_only_one_alias', is_form_true($this->input->post('product_only_one_alias')));
                db_config_bool('product_alias_redirect', is_form_true($this->input->post('product_alias_redirect')));
            }
            
            db_config('_global_meta_title_prefix', $this->input->post('_global_meta_title_prefix'));
            db_config('_global_meta_title_suffix', $this->input->post('_global_meta_title_suffix'));
            db_config('_global_meta_description', $this->input->post('_global_meta_description'));
            db_config('_global_meta_keywords', $this->input->post('_global_meta_keywords'));
            $this->_set_robots($this->input->post('robots'));
            db_config_bool('generate_sitemap', is_form_true($this->input->post('generate_sitemap')));
            db_config_bool('sitemap_pages', is_form_true($this->input->post('sitemap_pages')));
            db_config_bool('sitemap_products', is_form_true($this->input->post('sitemap_products')));
            db_config_bool('sitemap_categories', is_form_true($this->input->post('sitemap_categories')));
            db_config_bool('sitemap_services', is_form_true($this->input->post('sitemap_services')));
            
            db_config_bool('page_only_one_alias', is_form_true($this->input->post('page_only_one_alias')));
            db_config_bool('page_alias_redirect', is_form_true($this->input->post('page_alias_redirect')));
            db_config('unpublish_page', $this->input->post('unpublish_page'));
            
            if(cfg('general', 'eshop'))
            {
                db_config('default_category_tpl', $this->input->post('default_category_tpl'));
                db_config_bool('category_only_one_alias', is_form_true($this->input->post('category_only_one_alias')));
                db_config_bool('category_alias_redirect', is_form_true($this->input->post('category_alias_redirect')));
                db_config('unpublish_category', $this->input->post('unpublish_category'));
            }
            
            db_config_bool('service_alias_redirect', is_form_true($this->input->post('service_alias_redirect')));
            db_config('unpublish_service', $this->input->post('unpublish_service'));
            
            db_config('email_from_name', $this->input->post('email_from_name'));
            db_config('email_from_email', $this->input->post('email_from_email'));
            
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_7'));
        $this->admin->form->add_field('checkbox', 'multilang', __('field_7_3'), db_config_bool('multilang'));
        $this->admin->form->info(__('info_2'));
        $this->admin->form->add_field('checkbox', 'hp_lang_segment', __('field_7_4'), db_config_bool('hp_lang_segment'));
        $this->admin->form->add_field('internal', 'homepage', __('field_7_1'), db_config('homepage'));
        $this->admin->form->add_field('internal', 'page_404', __('field_7_2'), db_config('page_404'));
        
        if(cfg('general', 'eshop'))
        {
            $this->admin->form->tab(__('tab_1'));
            $this->admin->form->info(__('info_1'));
            $this->admin->form->add_field('input', 'order_id_format', __('field_1_1'), db_config('order_id_format'));

            $this->admin->form->tab(__('tab_2'));
            $this->admin->form->add_field('select', 'default_product_tpl', __('field_2_1'), $this->cms->templates->get_templates_select_data('products'), db_config('default_product_tpl'));
            $this->admin->form->add_field('internal', 'product_sold', __('field_2_8'), db_config('product_sold'));
            $this->admin->form->add_field('internal', 'unpublish_product', __('field_2_9'), db_config('unpublish_product'));
            $this->admin->form->add_field('checkbox', 'remember_product_parameters', __('field_2_2'), db_config_bool('remember_product_parameters'));
            $this->admin->form->add_field('checkbox', 'remember_product_variables', __('field_2_3'), db_config_bool('remember_product_variables'));
            $this->admin->form->add_field('checkbox', 'remember_product_variants', __('field_2_4'), db_config_bool('remember_product_variants'));
            $this->admin->form->add_field('checkbox', 'remember_product_variant_parameters', __('field_2_5'), db_config_bool('remember_product_variant_parameters'));
            $this->admin->form->add_field('checkbox', 'product_only_one_alias', __('field_2_6'), db_config_bool('product_only_one_alias'));
            $this->admin->form->add_field('checkbox', 'product_alias_redirect', __('field_2_7'), db_config_bool('product_alias_redirect'));
        }
        
        $this->admin->form->tab(__('tab_3'));
        $this->admin->form->add_field('input', '_global_meta_title_prefix', __('field_3_1'), db_config('_global_meta_title_prefix'));
        $this->admin->form->add_field('input', '_global_meta_title_suffix', __('field_3_4'), db_config('_global_meta_title_suffix'));
        $this->admin->form->add_field('textarea', '_global_meta_description', __('field_3_2'), db_config('_global_meta_description'));
        $this->admin->form->add_field('textarea', '_global_meta_keywords', __('field_3_3'), db_config('_global_meta_keywords'));
        $this->admin->form->add_field('textarea', 'robots', __('field_3_5'), $this->_get_robots());
        $this->admin->form->add_field('checkbox', 'generate_sitemap', __('field_3_6'), db_config_bool('generate_sitemap'));
        $this->admin->form->add_field('checkbox', 'sitemap_pages', __('field_3_7'), db_config_bool('sitemap_pages'));
        
        if(cfg('general', 'eshop'))
        {
            $this->admin->form->add_field('checkbox', 'sitemap_products', __('field_3_8'), db_config_bool('sitemap_products'));
            $this->admin->form->add_field('checkbox', 'sitemap_categories', __('field_3_9'), db_config_bool('sitemap_categories'));
        }
        
        $this->admin->form->add_field('checkbox', 'sitemap_services', __('field_3_10'), db_config_bool('sitemap_services'));
        
        $this->admin->form->tab(__('tab_4'));
        $this->admin->form->add_field('checkbox', 'page_only_one_alias', __('field_4_1'), db_config_bool('page_only_one_alias'));
        $this->admin->form->add_field('checkbox', 'page_alias_redirect', __('field_4_2'), db_config_bool('page_alias_redirect'));
        $this->admin->form->add_field('internal', 'unpublish_page', __('field_4_3'), db_config('unpublish_page'));
        
        if(cfg('general', 'eshop'))
        {
            $this->admin->form->tab(__('tab_5'));
            $this->admin->form->add_field('select', 'default_category_tpl', __('field_5_1'), $this->cms->templates->get_templates_select_data('categories'), db_config('default_category_tpl'));
            $this->admin->form->add_field('checkbox', 'category_only_one_alias', __('field_5_2'), db_config_bool('category_only_one_alias'));
            $this->admin->form->add_field('checkbox', 'category_alias_redirect', __('field_5_3'), db_config_bool('category_alias_redirect'));
            $this->admin->form->add_field('internal', 'unpublish_category', __('field_5_4'), db_config('unpublish_category'));
        }
        
        $this->admin->form->tab(__('tab_6'));
        $this->admin->form->add_field('checkbox', 'service_alias_redirect', __('field_6_1'), db_config_bool('service_alias_redirect'));
        $this->admin->form->add_field('internal', 'unpublish_service', __('field_6_2'), db_config('unpublish_service'));
        
        $this->admin->form->tab(__('tab_8'));
        $this->admin->form->add_field('input', 'email_from_name', __('field_8_1'), db_config('email_from_name'));
        $this->admin->form->add_field('input', 'email_from_email', __('field_8_2'), db_config('email_from_email'));
        
        $this->admin->form->button_submit(__('button_1'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    protected function _get_robots()
    {
        $this->load->helper('file');
        return read_file('./robots.txt');
    }
    
    protected function _set_robots($content = '')
    {
        $this->load->helper('file');
        return write_file('./robots.txt', $content);
    }
    
}