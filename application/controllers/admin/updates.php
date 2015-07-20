<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Updates extends CI_Controller {

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
        if($this->cms->updates->is_updated())
        {
            $this->admin->form->message(__('message_1'));
        }
        else
        {
            $this->admin->form->warning(__('warning_1'));
        }
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        
        $i = 0;
        
        foreach($this->cms->updates->get_updates() as $version)
        {
            $this->admin->form->cell($version);
            $this->admin->form->cell(admin_anchor('~/update/' . $version, __('button_1'), __('confirm_1')));
            
            $contextmenu = array();
            
            $this->admin->form->row(++$i, 0, NULL, NULL, $contextmenu);
        }
        
        $this->admin->form->button_helper(__('helper_1'), __('helper_title_1'));
        
        $this->admin->form->generate();
    }
    
    function update($version = '')
    {
        if(!$this->cms->updates->check_version($version))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        if($this->cms->updates->update($version))
        {
            $this->admin->form->message(sprintf(__('message_2'), $version), TRUE);
            admin_redirect();
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
            admin_redirect();
        }
    }
    
}