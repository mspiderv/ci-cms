<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_users extends CI_Controller {
    
    protected $super_admin_id = 1;
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_admin('users');
    }
    
    function index()
    {
        $this->admin->form->button_admin_link('~/add#tab-1', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        $this->admin->form->col(__('col_5'));
        
        foreach($this->a_users_model->get_data() as $admin_user)
        {
            $options_cell = '';
            $options_cell .= admin_anchor('~/duplicate/' . $admin_user->id, __('button_8'));
            
            if($admin_user->id != $this->super_admin_id)
            {
                $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                $options_cell .= admin_anchor('~/delete/' . $admin_user->id, __('button_2'), __('confirm_1'));
            }
            
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $admin_user->id, $admin_user->name));
            $this->admin->form->cell(date(cfg('time_format', 'normal'), $admin_user->registration_time));
            $this->admin->form->cell(date(cfg('time_format', 'normal'), $admin_user->last_login));
            $this->admin->form->cell($admin_user->lang);
            $this->admin->form->cell($options_cell);
            
            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $admin_user->id . '#tab-1'), 'edit');
            if($admin_user->id != $this->super_admin_id) $contextmenu[] = array(__('button_4'), admin_url('~/edit/' . $admin_user->id . '#tab-2'), 'permissions');
            $contextmenu[] = array(__('button_8'), admin_url('~/duplicate/' . $admin_user->id), 'copy');
            if($admin_user->id != $this->super_admin_id) $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $admin_user->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($admin_user->id, NULL, $this->cms->model->admin_table('users'), NULL, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function duplicate($admin_user_id = '')
    {
        if($this->admin->auth->user_exists($admin_user_id))
        {
            $this->admin->auth->duplicate_user($admin_user_id);
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_5'), TRUE);
        }
        admin_redirect();
    }
    
    function add()
    {
        $this->_validation(TRUE);
        
        if($this->admin->form->validate())
        {
            $permissions = array();
            
            foreach((array)$this->admin->auth->get_all_permissions() as $permission_name => $permission_title)
            {
                if($this->input->post('permission_' . str_replace('*', '__ALL__', $permission_name)) == cfg('form', 'true'))
                {
                    $permissions[] = $permission_name;
                }
            }
            
            $this->admin->auth->create_user($this->input->post('name'), $this->input->post('password'), $this->input->post('lang'), $permissions, ($this->input->post('cookie_login') == cfg('form', 'true')));
            
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', 'name', __('field_1'));
        $this->admin->form->add_field('password', 'password', __('field_2'));
        $this->admin->form->info(__('info_1'));
        $this->admin->form->add_field('password', 'password_2', __('field_3'));
        $this->admin->form->info(__('info_2'));
        $this->admin->form->add_field('checkbox', 'cookie_login', __('field_4'));
        $this->admin->form->add_field('select', 'lang', __('field_5'), admin_lang_select_data(), default_admin_lang(), TRUE);
        
        $this->admin->form->tab(__('tab_2'));
        $this->_get_permissions();
        
        $this->admin->form->parse_tabs();
        
        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_index();
        $this->admin->form->generate_buttons();
        
        $this->admin->form->generate();
    }
    
    function edit($admin_user_id = '')
    {
        if(!$this->a_users_model->item_exists($admin_user_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation(FALSE, $this->admin->auth->get_user_name($admin_user_id));
        
        if($this->admin->form->validate())
        {
            $this->admin->auth->set_user_login_data($admin_user_id, $this->input->post('name'), $this->input->post('password'));
            
            if($admin_user_id != $this->super_admin_id)
            {
                $permissions = array();

                foreach((array)$this->admin->auth->get_all_permissions() as $permission_name => $permission_title)
                {

                    if($this->input->post('permission_' . str_replace('*', '__ALL__', $permission_name)) == cfg('form', 'true'))
                    {
                        $permissions[] = $permission_name;
                    }
                }

                $this->admin->auth->set_user_permissions($admin_user_id, $permissions);
            }
            
            $this->admin->auth->set_user_lang($admin_user_id, $this->input->post('lang'));
            $this->admin->auth->set_user_cookie_login($admin_user_id, ($this->input->post('cookie_login') == cfg('form', 'true')));
            
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        if($admin_user_id == admin_user_id())
        {
            $this->admin->form->error(__('error_2'));
        }
        
        if($admin_user_id != $this->super_admin_id) $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->admin->auth->get_user_name($admin_user_id));
        $this->admin->form->add_field('password', 'password', __('field_2'));
        $this->admin->form->info(__('info_1'));
        $this->admin->form->add_field('password', 'password_2', __('field_3'));
        $this->admin->form->info(__('info_2'));
        $this->admin->form->add_field('checkbox', 'cookie_login', __('field_4'), $this->admin->auth->get_user_cookie_login($admin_user_id));
        $this->admin->form->add_field('select', 'lang', __('field_5'), admin_lang_select_data(), $this->admin->auth->get_user_lang($admin_user_id), TRUE);
        
        if($admin_user_id != $this->super_admin_id)
        {
            $this->admin->form->tab(__('tab_2'));
            $this->_get_permissions($admin_user_id);

            $this->admin->form->parse_tabs();
        }
        
        $this->admin->form->button_submit(__('button_6'));
        $this->admin->form->button_submit(__('button_7'), 'accept', 'check');
        $this->admin->form->button_index();
        $this->admin->form->generate_buttons();
        
        $this->admin->form->generate();
    }
    
    protected function _validation($password = TRUE, $instead_of = '')
    {
        if(strlen($instead_of) == 0) $instead_of = '[' . $instead_of . ']';
        
        $this->admin->form->set_rules('name', __('field_1'), 'trim|required|free_admin_user_name' . $instead_of . '|max_length[255]');
        $this->admin->form->set_rules('password', __('field_2'), 'trim|' . ($password ? 'required|' : '') . 'min_length[4]');
        $this->admin->form->set_rules('password_2', __('field_3'), 'trim|' . ($password ? 'required|' : '') . 'matches[password]');
        $this->admin->form->set_rules('cookie_login', __('field_4'), 'trim|intval');
        $this->admin->form->set_rules('lang', __('field_5'), 'trim|admin_lang');
    }
    
    protected function _get_permissions($user_id = '')
    {
        foreach((array)$this->admin->auth->get_all_permissions() as $permission_name => $permission_title)
        {
            $permission_key = str_replace('*', '__ALL__', $permission_name);
            $this->admin->form->add_field('checkbox', 'permission_' . $permission_key, $permission_title, (bool)$this->admin->auth->user_really_has_permission($user_id, $permission_name));
        }
    }
    
    function delete($admin_user_id = '')
    {
        if($this->admin->auth->user_exists($admin_user_id))
        {
            if($admin_user_id != $this->super_admin_id)
            {
                $this->admin->auth->delete_user($admin_user_id);
                $this->admin->form->message(__('message_3'), TRUE);
            }
            else
            {
                $this->admin->form->error(__('error_3'), TRUE);
            }
        }
        else
        {
            $this->admin->form->error(__('error_4'), TRUE);
        }
        admin_redirect();
    }
    
}