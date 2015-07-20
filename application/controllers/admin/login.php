<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->library('form_validation');
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        
        $this->load->driver('admin');
    }
    
    function index()
    {
        $this->admin->form->variables['login_error'] = FALSE;
        
        if(form_sent())
        {
            $user_id = $this->admin->auth->check_login_data($this->input->post('login_name'), $this->input->post('login_password'));
            
            if($this->admin->auth->user_exists($user_id))
            {
                $this->admin->auth->unset_session_user_id();

                /*if($this->input->post('remember') == cfg('form', 'true')) $this->admin->auth->set_cookie_data($this->input->post('login_name'), $this->input->post('login_password'));
                else $this->admin->auth->delete_cookie_data();*/

                $this->admin->auth->set_session_user_id($user_id);
                $this->admin->auth->update_login_time();
                $this->admin->form->message(sprintf(__('success'), $this->input->post('login_name')), TRUE);
                $temp_url = $this->admin->auth->get_temp_url();
                $this->admin->auth->unset_temp_url();
                if(strlen($temp_url) > 0) redirect($temp_url);
                else admin_redirect('/');
            }
            else
            {
                $this->admin->auth->delete_cookie_data();
                $this->admin->form->variables['login_error'] = TRUE;
            }
        }
        
        $this->admin->form->variables['default_name'] = $this->admin->auth->get_cookie_name();
        $this->admin->form->variables['default_password'] = $this->admin->auth->get_cookie_password();
        
        $this->admin->form->set_main_view('login');
        
        // Generating
        $this->admin->form->generate();
    }
    
    function logout()
    {
        $this->admin->auth->unset_session_user_id();
        admin_redirect('login');
    }
    
}