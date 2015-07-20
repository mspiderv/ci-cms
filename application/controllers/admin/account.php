<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Controller {
    
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
        $admin_user_id = admin_user_id();
        
        if(!$this->a_users_model->item_exists($admin_user_id))
        {
            admin_redirect('login/logout');
        }
        
        $pass_required = (strlen($this->input->post('password_cur')) > 0 || strlen($this->input->post('password')) > 0 || strlen($this->input->post('password_2')) > 0) ? '|required' : '';
        
        $this->admin->form->set_rules('name', __('field_1'), 'trim|required|free_admin_user_name[' . $this->admin->auth->get_user_name($admin_user_id) . ']|max_length[255]');
        $this->admin->form->set_rules('password_cur', __('field_2'), 'trim|user_password' . $pass_required);
        $this->admin->form->set_rules('password', __('field_3'), 'trim|min_length[4]' . $pass_required);
        $this->admin->form->set_rules('password_2', __('field_4'), 'trim|matches[password]' . $pass_required);
        $this->admin->form->set_rules('cookie_login', __('field_5'), 'trim|intval');
        $this->admin->form->set_rules('lang', __('field_6'), 'trim|admin_lang');
        
        if($this->admin->form->validate())
        {
            $this->admin->auth->set_user_login_data($admin_user_id, $this->input->post('name'), $this->input->post('password'));
            $this->admin->auth->set_user_lang($admin_user_id, $this->input->post('lang'));
            $this->admin->auth->set_user_cookie_login($admin_user_id, ($this->input->post('cookie_login') == cfg('form', 'true')));
            
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->error(__('error_1'));
        
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->admin->auth->get_user_name($admin_user_id));
        $this->admin->form->info(__('info_1'));
        $this->admin->form->add_field('password', 'password_cur', __('field_2'));
        $this->admin->form->add_field('password', 'password', __('field_3'));
        $this->admin->form->info(__('info_2'));
        $this->admin->form->add_field('password', 'password_2', __('field_4'));
        $this->admin->form->info(__('info_3'));
        $this->admin->form->add_field('checkbox', 'cookie_login', __('field_5'), $this->admin->auth->get_user_cookie_login($admin_user_id));
        $this->admin->form->add_field('select', 'lang', __('field_6'), admin_lang_select_data(), $this->admin->auth->get_user_lang($admin_user_id), TRUE);
        
        $this->admin->form->button_submit(__('button_1'));
        $this->admin->form->button_index();
        $this->admin->form->generate_buttons();
        
        $this->admin->form->generate();
    }
    
}