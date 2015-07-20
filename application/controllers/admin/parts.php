<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Parts extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('parts');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        
        foreach($this->s_parts_model->get_data() as $part)
        {
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $part->id, $part->name));
            $this->admin->form->cell_left($part->code);
            $this->admin->form->cell(admin_anchor('~/delete/' . $part->id, __('button_2'), __('confirm_1')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $part->id), 'edit');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $part->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($part->id, 0, $this->cms->model->system_table('parts'), NULL, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function add()
    {
        $this->_validation(TRUE);
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['code'] = $this->input->post('code');
            $data['_content'] = $this->input->post('_content');
            
            if(strlen($data['code']) == 0) $data['code'] = url_title($data['name']);
            
            $this->s_parts_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'));
        $this->admin->form->add_field('input', 'code', __('field_2'));
        $this->admin->form->add_field('ckeditor', '_content', __('field_3'));
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($part_id = '')
    {
        if(!$this->s_parts_model->item_exists($part_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation($this->s_parts_model->$part_id->code != $this->input->post('code'));
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['code'] = $this->input->post('code');
            $data['_content'] = $this->input->post('_content');
            
            if(strlen($data['code']) == 0) $data['code'] = url_title($data['name']);
            
            $this->s_parts_model->set_item_data($part_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->s_parts_model->$part_id->name);
        $this->admin->form->add_field('input', 'code', __('field_2'), $this->s_parts_model->$part_id->code);
        $this->admin->form->add_field('ckeditor', '_content', __('field_3'), $this->s_parts_model->$part_id->_content);

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($part_id = '')
    {
        if($this->s_parts_model->item_exists($part_id))
        {
            $this->s_parts_model->delete_item($part_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation($unique_code = TRUE)
    {
        $unique_code = $unique_code ? '|unique_system[parts.code]' : '';
        
        $this->admin->form->set_rules('name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('code', __('field_2'), 'trim|url_title|max_length[50]' . $unique_code);
        $this->admin->form->set_rules('_content', __('field_3'), 'trim|required');
    }
    
}