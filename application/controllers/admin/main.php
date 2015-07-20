<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->library('form_validation');
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        
        $this->load->driver('admin');
        $this->admin->auth->check_access();
    }
    
    function page_missing()
    {
        $this->admin->form->title = __('page_missing_title');
        $this->admin->form->load_part('page_missing');
        $this->admin->form->generate();
    }
    
    function change_lang($lang_id = '')
    {
        if(lang_id_exists($lang_id))
        {
            $this->session->set_userdata(array(cfg('session_keys', 'admin_lang_id') => $lang_id));
        }
        redirect($this->input->get(cfg('url', 'redirect')));
    }
    
    function index()
    {
        $this->admin->form->generate();
    }
}