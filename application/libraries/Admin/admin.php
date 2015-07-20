<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Driver_Library {
    
    protected $CI;
    protected $valid_drivers = array(
        'admin_auth',
        'admin_form'
    );

    function  __construct()
    {
        $this->CI =& get_instance();
        
        $this->CI->load->helper('admin');
        
        // Set admin lang
        $session_admin_lang_id = $this->CI->session->userdata(cfg('session_keys', 'admin_lang_id'));
        if(lang_id_exists($session_admin_lang_id)) set_lang_id($session_admin_lang_id);
        
        $this->set_carabiner_options();
        
        load_lang(cfg('folder', 'admin') . '/general');
        load_lang($this->CI->router->directory . $this->CI->router->fetch_class());
        
        if($this->CI->uri->segment(1) == cfg('url', 'admin'))
        $this->CI->output->enable_profiler(cfg('profiler', 'admin'));
    }
    
    function get_admin_user_lang()
    {
        return $this->CI->admin->auth->get_user_lang($this->CI->admin->auth->get_user_id());
    }
    
    function set_carabiner_options()
    {
        $path = cfg('folder', 'assets') . '/' . cfg('folder', 'admin') . '/';
        
        $config = array(
            'script_dir' => $path,
            'style_dir' => $path
        );
        
        $this->CI->carabiner->config($config);
    }
    
    function load_view($view = '', $data = array(), $return = FALSE)
    {
        return $this->CI->load->view(cfg('folder', 'admin') . '/' . $view, $data, $return);
    }
    
}