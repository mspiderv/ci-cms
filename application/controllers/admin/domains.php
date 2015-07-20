<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Domains extends CI_Controller {
    
    protected $super_admin_id = 1;
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('domains');
        $this->cms->model->load_system('langs');
        $this->cms->model->load_system('themes');
    }
    
    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        
        foreach($this->s_domains_model->get_data() as $domain)
        {
            if($this->s_langs_model->item_exists($domain->lang_id))
            {
                $lang = admin_anchor('languages/edit/' . $domain->lang_id, $this->cms->langs->get_lang_name($domain->lang_id));
            }
            else
            {
                $default_lang_id = default_lang_id();
                $lang = admin_anchor('languages/edit/' . $default_lang_id, __('default_lang') . ' (' . $this->cms->langs->get_lang_name($default_lang_id) . ')');
            }
            
            if($this->s_themes_model->item_exists($domain->theme_id))
            {
                $theme = admin_anchor('themes/edit/' . $domain->theme_id, $this->s_themes_model->get_item_data($domain->theme_id, 'name'));
            }
            else
            {
                $active_theme_id = db_config('active_theme_id');
                $theme = admin_anchor('themes/edit/' . $active_theme_id, __('active_theme') . ' (' . $this->s_themes_model->get_item_data($active_theme_id, 'name') . ')');
            }
            
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $domain->id, $domain->domain));
            $this->admin->form->cell($lang);
            $this->admin->form->cell($theme);
            $this->admin->form->cell(admin_anchor('~/delete/' . $domain->id, __('button_2'), __('confirm_1')));
            
            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $domain->id), 'edit');
            if($domain->id != $this->super_admin_id) $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $domain->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($domain->id, NULL, $this->cms->model->system_table('domains'), NULL, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function add()
    {
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['domain'] = $this->input->post('domain');
            $data['lang_id'] = $this->input->post('lang_id');
            $data['theme_id'] = $this->input->post('theme_id');
            
            $this->s_domains_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'domain', __('field_1'));
        $this->admin->form->add_field('select', 'lang_id', __('field_2'), $this->s_langs_model->get_data_in_col('lang'), '', TRUE, __('default_lang'));
        $this->admin->form->add_field('select', 'theme_id', __('field_3'), $this->s_themes_model->get_data_in_col('name'), '', TRUE, __('active_theme'));
        
        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_index();
        $this->admin->form->generate_buttons();
        
        $this->admin->form->generate();
    }
    
    function edit($domain_id = '')
    {
        if(!$this->s_domains_model->item_exists($domain_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['domain'] = $this->input->post('domain');
            $data['lang_id'] = $this->input->post('lang_id');
            $data['theme_id'] = $this->input->post('theme_id');
            
            $this->s_domains_model->set_item_data($domain_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'domain', __('field_1'), $this->s_domains_model->$domain_id->domain);
        $this->admin->form->add_field('select', 'lang_id', __('field_2'), $this->s_langs_model->get_data_in_col('lang'), $this->s_domains_model->$domain_id->lang_id, TRUE, __('default_lang'));
        $this->admin->form->add_field('select', 'theme_id', __('field_3'), $this->s_themes_model->get_data_in_col('name'), $this->s_domains_model->$domain_id->theme_id, TRUE, __('active_theme'));
        
        $this->admin->form->button_submit(__('button_6'));
        $this->admin->form->button_submit(__('button_7'), 'accept', 'check');
        $this->admin->form->button_index();
        $this->admin->form->generate_buttons();
        
        $this->admin->form->generate();
    }
    
    protected function _validation()
    {
        $this->admin->form->set_rules('domain', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('lang_id', __('field_2'), 'trim|item_exists_system[langs]');
        $this->admin->form->set_rules('theme_id', __('field_3'), 'trim|item_exists_system[themes]');
    }
    
    function delete($domain_id = '')
    {
        if($this->s_domains_model->item_exists($domain_id))
        {
            $this->s_domains_model->delete_item($domain_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
}