<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class System extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        
        $this->load->driver('admin');
        
        if(!$this->input->is_ajax_request()) show_404();
    }
    
    function save_sort()
    {
        $data = array();
        
        $table = $this->input->post('table');
        $items = json_decode($this->input->post('items'));
        $sort = json_decode($this->input->post('sort'));
        
        $result = $this->cms->save_sort($table, $items, $sort);
        
        if($result === TRUE)
        {
            $data['result'] = TRUE;
            $data['error'] = '';
        }
        elseif($result == 'error_1')
        {
            $data['result'] = FALSE;
            $data['error'] = __('error_2');
        }
        elseif($result == 'error_2')
        {
            $data['result'] = FALSE;
                $data['error'] = sprintf(__('error_1'), $table);
        }
        else
        {
            $data['result'] = FALSE;
            $data['error'] = '';
        }
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data));
    }
    
    function get_field_error()
    {
        $data = array();
        
        $this->load->library('form_validation');
        
        $name = $this->input->get('name');
        $label = $this->input->get('label');
        $rules = $this->input->get('rules');
        $value = $this->input->get('value');
        
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules($name, $label, $rules);
        $this->form_validation->run();
        
        $data['error'] = form_error($name);
        $data['value'] = $this->input->post($name);
        
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data));
    }
    
}