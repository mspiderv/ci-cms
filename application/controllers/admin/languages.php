<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Languages extends CI_Controller {

    protected $valid_languages = array();
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        
        $this->config->load('valid_languages');
        $this->valid_languages = cfg('valid_languages');
    }
    
    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'), __('col_1_title'));
        $this->admin->form->col(__('col_2'), __('col_2_title'));
        $this->admin->form->col(__('col_3'), __('col_3_title'));
        if(cfg('general', 'eshop')) $this->admin->form->col(__('col_4'));
        $this->admin->form->col(__('col_5'));
        $this->admin->form->col(__('col_6'));
        
        foreach($this->s_langs_model->get_data() as $lang)
        {
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $lang->id, @$this->valid_languages[$lang->code]));
            $this->admin->form->cell($lang->lang);
            $this->admin->form->cell($lang->code);
            if(cfg('general', 'eshop')) $this->admin->form->cell($this->eshop->currencies->get_format($lang->currency_id));
            $this->admin->form->cell($this->admin->form->cell_radio('~/set_default_lang/' . $lang->id, __('button12'), (db_config('default_lang_id') == $lang->id)));
            
            $contextmenu = array();
            
            $contextmenu[] = array('Upraviť', admin_url('~/edit/' . $lang->id), 'edit');
            
            if(db_config('default_lang_id') == $lang->id)
            {
                $this->admin->form->cell();
            }
            else
            {
                $this->admin->form->cell(admin_anchor('~/delete/' . $lang->id, __('button_3'), __('confirm_1')));
                $contextmenu[] = array(__('button_4'), admin_url('~/set_default_lang/' . $lang->id), 'setup');
                $contextmenu[] = array(__('button_3'), admin_url('~/delete/' . $lang->id), 'delete', __('confirm_1'));
            }
            
            $this->admin->form->row($lang->id, NULL, $this->cms->model->system_table('langs'), NULL, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function add()
    {
        $this->admin->form->set_rules('code', __('field_1'), 'trim|required|available_lang_code|max_length[255]');
        $this->admin->form->set_rules('lang', __('field_2'), 'trim|required|max_length[255]');
        if(cfg('general', 'eshop')) $this->admin->form->set_rules('currency_id', __('field_3'), 'trim|required|item_exists_eshop[currencies]');
        
        if($this->admin->form->validate())
        {
            $lang_data = array();
            
            $lang_data['lang'] = $this->input->post('lang');
            $lang_data['code'] = $this->input->post('code');
            if(cfg('general', 'eshop')) $lang_data['currency_id'] = $this->input->post('currency_id');
            
            $this->s_langs_model->add_item($lang_data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('select', 'code', __('field_1'), $this->cms->langs->get_available_languages());
        $this->admin->form->info(__('info_1'));
        $this->admin->form->add_field('input', 'lang', __('field_2'), '', 'sk, cz, en, de ...');
        if(cfg('general', 'eshop')) $this->admin->form->add_field('select', 'currency_id', __('field_3'), $this->eshop->currencies->get_select_data(), db_config('default_currency_id'));
        
        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_index();
        $this->admin->form->generate_buttons();
        
        $this->admin->form->generate();
    }
    
    function edit($lang_id = '')
    {
        if(!$this->s_langs_model->item_exists($lang_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        // Validation
        $unique_code = ($this->s_langs_model->$lang_id->code != $this->input->post('code')) ? '|unique_system[langs.code]' : '';
        $unique_lang = ($this->s_langs_model->$lang_id->lang != $this->input->post('lang')) ? '|unique_system[langs.lang]' : '';
        
        $this->admin->form->set_rules('code', __('field_1'), 'trim|required|available_lang_code[' . $lang_id . ']|max_length[255]' . $unique_code);
        $this->admin->form->set_rules('lang', __('field_2'), 'trim|required|max_length[255]' . $unique_lang);
        if(cfg('general', 'eshop')) $this->admin->form->set_rules('currency_id', __('field_3'), 'trim|required|item_exists_eshop[currencies]');
        
        if($this->admin->form->validate())
        {
            $lang_data = array();
            
            $lang_data['lang'] = $this->input->post('lang');
            $lang_data['code'] = $this->input->post('code');
            if(cfg('general', 'eshop')) $lang_data['currency_id'] = $this->input->post('currency_id');
            
            $this->s_langs_model->set_item_data($lang_id, $lang_data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('select', 'code', __('field_1'), $this->cms->langs->get_available_languages($lang_id), $this->s_langs_model->$lang_id->code);
        $this->admin->form->info(__('info_1'));
        $this->admin->form->add_field('input', 'lang', __('field_2'), $this->s_langs_model->$lang_id->lang, 'sk, cz, en, de ...');
        if(cfg('general', 'eshop')) $this->admin->form->add_field('select', 'currency_id', __('field_3'), $this->eshop->currencies->get_select_data(), $this->s_langs_model->$lang_id->currency_id);
        
        $this->admin->form->button_submit(__('button_6'));
        $this->admin->form->button_submit(__('button_7'), 'accept', 'check');
        $this->admin->form->button_index();
        $this->admin->form->generate_buttons();
        
        $this->admin->form->generate();
    }
    
    function set_default_lang($lang_id = '')
    {
        if($this->s_langs_model->item_exists($lang_id))
        {
            db_config('default_lang_id', $lang_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    function delete($lang_id = '')
    {
        if($this->s_langs_model->item_exists($lang_id))
        {
            if(db_config('default_lang_id') == $lang_id)
            {
                $this->admin->form->error(__('error_3'), TRUE);
            }
            else
            {
                $this->s_langs_model->delete_item($lang_id);
                $this->admin->form->message(__('message_4'), TRUE);
            }
        }
        else
        {
            $this->admin->form->error(__('error_4'), TRUE);
        }
        admin_redirect();
    }
    
}