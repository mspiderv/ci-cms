<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Services extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('services');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add#tab-1', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        $this->admin->form->col(__('col_5'));
        $this->admin->form->col(__('col_6'));
        $this->admin->form->col(__('col_7'));
        $this->admin->form->col(__('col_8'));
        
        foreach($this->cms->services->get_services_structure() as $service)
        {
            $service_id = @$service['id'];
            $service_level = @$service['level'];
            $service = $this->s_services_model->get_item($service_id);
            
            $options_cell = '';
            $options_cell .= '<a href="' . href_service($service->id) . '">' . __('button_13') . '</a>';
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/duplicate/' . $service->id, __('button_9'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/delete/' . $service->id, __('button_2'), __('confirm_1'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/export/' . $service->id, __('button_10'));
            
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $service->id, $service->name));
            $this->admin->form->cell($service->_alias);
            $this->admin->form->cell((strlen($service->class) > 0) ? ($service->class . '->' . $service->method) : '');
            $this->admin->form->cell($service->tpl); // TODO: ak bude hotovy ten editor sablon tak toto by mal byt link ktory tu sablonu zedituje
            $this->admin->form->cell($this->admin->form->cell_checkbox((($service->index) ? '~/unindex_service/' : '~/index_service/') . $service->id, (($service->index) ? __('button_11') : __('button_12')), $service->index));
            $this->admin->form->cell($this->admin->form->cell_checkbox((($service->public) ? '~/unpublish_service/' : '~/publish_service/') . $service->id, (($service->public) ? __('button_7') : __('button_8')), $service->public));
            $this->admin->form->cell($this->admin->form->cell_indicator($service->sitemap_priority, 1));
            $this->admin->form->cell($options_cell);

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $service->id), 'edit');
            $contextmenu[] = array(__('button_13'), href_service($service->id), 'show');
            
            if($service->index) $contextmenu[] = array(__('button_11'), admin_url('~/unindex_service/' . $service->id), 'noindex');
            else $contextmenu[] = array(__('button_12'), admin_url('~/index_service/' . $service->id), 'index');
            
            if($service->public) $contextmenu[] = array(__('button_7'), admin_url('~/unpublish_service/' . $service->id), 'x');
            else $contextmenu[] = array(__('button_8'), admin_url('~/publish_service/' . $service->id), 'check');
            
            $contextmenu[] = array(__('button_9'), admin_url('~/duplicate/' . $service->id), 'copy');
            $contextmenu[] = array(__('button_10'), admin_url('~/export/' . $service->id), 'export');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $service->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($service->id, $service_level, $this->cms->model->system_table('services') . '_' . intval($service->parent_service_id), TRUE, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function unindex_service($service_id = '')
    {
        if($this->s_services_model->item_exists($service_id))
        {
            $this->s_services_model->set_item_data($service_id, array('index' => FALSE));
            $this->admin->form->message(__('message_7'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_7'), TRUE);
        }
        admin_redirect();
    }
    
    function index_service($service_id = '')
    {
        if($this->s_services_model->item_exists($service_id))
        {
            $this->s_services_model->set_item_data($service_id, array('index' => TRUE));
            $this->admin->form->message(__('message_8'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_8'), TRUE);
        }
        admin_redirect();
    }
    
    function unpublish_service($service_id = '')
    {
        if($this->s_services_model->item_exists($service_id))
        {
            $this->s_services_model->set_item_data($service_id, array('public' => FALSE));
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_4'), TRUE);
        }
        admin_redirect();
    }
    
    function publish_service($service_id = '')
    {
        if($this->s_services_model->item_exists($service_id))
        {
            $this->s_services_model->set_item_data($service_id, array('public' => TRUE));
            $this->admin->form->message(__('message_5'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_5'), TRUE);
        }
        admin_redirect();
    }
    
    function add($service_type_id = '')
    {
        $this->_validation('', TRUE);
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['parent_service_id'] = $this->input->post('parent_service_id');
            $data['name'] = $this->input->post('name');
            $data['_alias'] = $this->input->post('_alias');
            $data['tpl'] = $this->input->post('tpl');
            $data['class'] = $this->input->post('class');
            $data['method'] = $this->input->post('method');
            $data['parameters'] = $this->input->post('parameters');
            $data['public'] = is_form_true($this->input->post('public'));
            $data['_meta_title'] = $this->input->post('_meta_title');
            $data['_meta_description'] = $this->input->post('_meta_description');
            $data['_meta_keywords'] = $this->input->post('_meta_keywords');
            $data['changefreq'] = $this->input->post('changefreq');
            $data['index'] = is_form_true($this->input->post('index'));
            $data['sitemap_priority'] = $this->input->post('sitemap_priority');
            
            if(strlen($data['_alias']) == 0) $data['_alias'] = url_title($data['name']);

            $this->s_services_model->add_item($data);
            
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', 'name', __('field_1'));
        $this->admin->form->add_field('input', '_alias', __('field_2'));
        $this->admin->form->add_field('select', 'parent_service_id', __('field_14'), $this->cms->services->get_services_select_data(), '', TRUE);
        $this->admin->form->add_field('select', 'tpl', __('field_3'), $this->cms->templates->get_templates_select_data('services'));
        $this->admin->form->add_field('select', 'class', __('field_4'), $this->cms->libraries->get_libraries_select_data('services'), '', TRUE);
        $this->admin->form->add_field('input', 'method', __('field_5'));
        $this->admin->form->add_field('input', 'parameters', __('field_13'));
        $this->admin->form->add_field('checkbox', 'public', __('field_6'), TRUE);
        
        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('input', '_meta_title', __('field_7'), '', __('placeholder_1'));
        $this->admin->form->add_field('textarea', '_meta_description', __('field_8'), '', __('placeholder_2'));
        $this->admin->form->add_field('textarea', '_meta_keywords', __('field_9'), '', __('placeholder_2'));
        $this->admin->form->add_field('select_changefreq', 'changefreq', __('field_10'), cfg('changefreq', 'default'));
        $this->admin->form->add_field('checkbox', 'index', __('field_11'), TRUE);
        $this->admin->form->add_field('slider', 'sitemap_priority', __('field_12'), cfg('sitemap', 'default_priority'), 0, 1, 0.1);
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($service_id = '')
    {
        if(!$this->s_services_model->item_exists($service_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation($service_id);
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['parent_service_id'] = $this->input->post('parent_service_id');
            $data['name'] = $this->input->post('name');
            $data['_alias'] = $this->input->post('_alias');
            $data['tpl'] = $this->input->post('tpl');
            $data['class'] = $this->input->post('class');
            $data['method'] = $this->input->post('method');
            $data['parameters'] = $this->input->post('parameters');
            $data['public'] = is_form_true($this->input->post('public'));
            $data['_meta_title'] = $this->input->post('_meta_title');
            $data['_meta_description'] = $this->input->post('_meta_description');
            $data['_meta_keywords'] = $this->input->post('_meta_keywords');
            $data['changefreq'] = $this->input->post('changefreq');
            $data['index'] = is_form_true($this->input->post('index'));
            $data['sitemap_priority'] = $this->input->post('sitemap_priority');
            
            if(strlen($data['_alias']) == 0) $data['_alias'] = url_title($data['name']);
            
            $this->s_services_model->set_item_data($service_id, $data);
            
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->s_services_model->$service_id->name);
        $this->admin->form->add_field('input', '_alias', __('field_2'), $this->s_services_model->$service_id->_alias);
        $this->admin->form->add_field('select', 'parent_service_id', __('field_14'), $this->cms->services->get_services_select_data($service_id), $this->s_services_model->$service_id->parent_service_id, TRUE);
        $this->admin->form->add_field('select', 'tpl', __('field_3'), $this->cms->templates->get_templates_select_data('services'), $this->s_services_model->$service_id->tpl);
        $this->admin->form->add_field('select', 'class', __('field_4'), $this->cms->libraries->get_libraries_select_data('services'), $this->s_services_model->$service_id->class, TRUE);
        $this->admin->form->add_field('input', 'method', __('field_5'), $this->s_services_model->$service_id->method);
        $this->admin->form->add_field('input', 'parameters', __('field_13'), $this->s_services_model->$service_id->parameters);
        $this->admin->form->add_field('checkbox', 'public', __('field_6'), $this->s_services_model->$service_id->public);
        
        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('input', '_meta_title', __('field_7'), $this->s_services_model->$service_id->_meta_title, __('placeholder_1'));
        $this->admin->form->add_field('textarea', '_meta_description', __('field_8'), $this->s_services_model->$service_id->_meta_description, __('placeholder_2'));
        $this->admin->form->add_field('textarea', '_meta_keywords', __('field_9'), $this->s_services_model->$service_id->_meta_keywords, __('placeholder_2'));
        $this->admin->form->add_field('select_changefreq', 'changefreq', __('field_10'), $this->s_services_model->$service_id->changefreq);
        $this->admin->form->add_field('checkbox', 'index', __('field_11'), $this->s_services_model->$service_id->index);
        $this->admin->form->add_field('slider', 'sitemap_priority', __('field_12'), cfg('sitemap', 'default_priority'), $this->s_services_model->$service_id->sitemap_priority, 1, 0.1);
        
        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_link(site_url(href_service($service_id)), __('button_13'), 'home');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($service_id = '')
    {
        if($this->s_services_model->item_exists($service_id))
        {
            $this->s_services_model->delete_item($service_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    function duplicate($service_id = '')
    {
        if($this->cms->services->duplicate($service_id))
        {
            $this->admin->form->message(__('message_6'), TRUE);
            admin_redirect();
        }
        else
        {
            $this->admin->form->error(__('error_6'), TRUE);
        }
        admin_redirect();
    }
    
    function export($service_id = '')
    {
        if($this->s_services_model->item_exists($service_id))
        {
            $this->admin->form->warning("Služby zatiaľ nie je možné exportovať.", TRUE);
            //$this->cms->export->service($service_id);
            //$this->cms->export->download();
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation($service_id = '')
    {
        $class_method_required = (strlen($this->input->post('class')) > 0 || strlen($this->input->post('method')) > 0) ? '|required' : '';
        $parent_service_validation = (intval($service_id) > 0) ? 'parent_service_id[' . $service_id . ']' : 'item_exists_system[services]';
        
        $this->admin->form->set_rules('name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('_alias', __('field_2'), 'trim|url_title|max_length[255]|valid_alias');
        $this->admin->form->set_rules('parent_service_id', __('field_14'), 'trim|' . $parent_service_validation);
        $this->admin->form->set_rules('tpl', __('field_3'), 'trim|required|max_length[255]|tpl[services]');
        $this->admin->form->set_rules('class', __('field_4'), 'trim|max_length[255]|library[services]' . $class_method_required);
        $this->admin->form->set_rules('method', __('field_5'), 'trim|max_length[255]|' . $class_method_required);
        $this->admin->form->set_rules('parameters', __('field_13'), 'trim');
        $this->admin->form->set_rules('public', __('field_6'), 'trim|intval');
        $this->admin->form->set_rules('_meta_title', __('field_7'), 'trim|max_length[255]');
        $this->admin->form->set_rules('_meta_description', __('field_8'), 'trim');
        $this->admin->form->set_rules('_meta_keywords', __('field_9'), 'trim');
        $this->admin->form->set_rules('changefreq', __('field_10'), 'trim|required|changefreq|max_length[255]');
        $this->admin->form->set_rules('index', __('field_11'), 'trim|intval');
        $this->admin->form->set_rules('sitemap_priority', __('field_12'), 'trim|required|sitemap_priority');
    }
    
}