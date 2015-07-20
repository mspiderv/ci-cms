<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class File_manager extends CI_Controller {

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
        $this->admin->form->title = "Elfinder";
        
        $this->admin->form->top = FALSE;
        $this->admin->form->menu = FALSE;
        
        $this->admin->form->set_main_view('elfinder');
        
        $this->admin->form->generate();
    }
    
    function show()
    {
        $this->admin->form->title = "Elfinder";
        
        $this->admin->form->set_main_view('elfinder');
        
        $this->admin->form->generate();
    }
    
    function connector()
    {
        $options = array(
            'roots' => array(
                array(
                    'driver'        => 'LocalFileSystem', // driver for accessing file system (REQUIRED)
                    'path'          => './' . cfg('folder', 'assets') . '/' . cfg('folder', 'files') . '/', // path to files (REQUIRED)
                    'URL'           => ASSETS . cfg('folder', 'files') . '/' // URL to files (REQUIRED)
                )
            )
        );
        $this->load->library('elfinder/elfinder_input');
        $this->elfinder_input->start($options);
    }
    
}