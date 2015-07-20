<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Positions extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('positions');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        
        foreach($this->s_positions_model->get_data() as $position)
        {
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $position->id, $position->name));
            $this->admin->form->cell_left($position->code);
            $this->admin->form->cell($this->admin->form->cell_checkbox((($position->public) ? '~/unpublish_position/' : '~/publish_position/') . $position->id, (($position->public) ? __('button_7') : __('button_8')), $position->public));
            $this->admin->form->cell(admin_anchor('~/delete/' . $position->id, __('button_2'), __('confirm_1')));

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $position->id), 'edit');
            if($position->public) $contextmenu[] = array(__('button_7'), admin_url('~/unpublish_position/' . $position->id), 'x');
            else $contextmenu[] = array(__('button_8'), admin_url('~/publish_position/' . $position->id), 'check');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $position->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($position->id, 0, $this->cms->model->system_table('positions'), NULL, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function unpublish_position($position_id = '')
    {
        if($this->s_positions_model->item_exists($position_id))
        {
            $this->s_positions_model->set_item_data($position_id, array('public' => FALSE));
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    function publish_position($position_id = '')
    {
        if($this->s_positions_model->item_exists($position_id))
        {
            $this->s_positions_model->set_item_data($position_id, array('public' => TRUE));
            $this->admin->form->message(__('message_5'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_4'), TRUE);
        }
        admin_redirect();
    }
    
    function add()
    {
        $this->_validation(TRUE);
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['code'] = $this->input->post('code');
            $data['public'] = is_form_true($this->input->post('public'));
            
            if(strlen($data['code']) == 0) $data['code'] = url_title($data['name']);
            
            $this->s_positions_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'));
        $this->admin->form->add_field('input', 'code', __('field_3'));
        $this->admin->form->add_field('checkbox', 'public', __('field_2'), TRUE);
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($position_id = '')
    {
        if(!$this->s_positions_model->item_exists($position_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation($this->s_positions_model->$position_id->code != $this->input->post('code'));
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['code'] = $this->input->post('code');
            $data['public'] = is_form_true($this->input->post('public'));
            
            if(strlen($data['code']) == 0) $data['code'] = url_title($data['name']);
            
            $this->s_positions_model->set_item_data($position_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->s_positions_model->$position_id->name);
        $this->admin->form->add_field('input', 'code', __('field_3'), $this->s_positions_model->$position_id->code);
        $this->admin->form->add_field('checkbox', 'public', __('field_2'), $this->s_positions_model->$position_id->public);

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($position_id = '')
    {
        if($this->s_positions_model->item_exists($position_id))
        {
            $this->s_positions_model->delete_item($position_id);
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
        $unique_code = $unique_code ? '|unique_system[positions.code]' : '';
        
        $this->admin->form->set_rules('name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('code', __('field_3'), 'trim|url_title|max_length[50]' . $unique_code);
        $this->admin->form->set_rules('public', __('field_2'), 'intval');
    }
    
}