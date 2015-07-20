<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order_states extends CI_Controller {

    // TODO: BUG: ajaxovy error fieldu pri fielde colorpicker blbne
    // TODO: v edite a v pridavani (~/edit a ~/add) by som mal nejakym vhodnym
    // sposobom generovat vsetky mozne premenne ktore mozu byt pouzite v CKEditore
    // + navod ako ich tam davat (napr. ze to musi byt v zlozenych zatvorkach a pod.)
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->load->driver('eshop');
        $this->cms->model->load_eshop('order_states');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        
        foreach($this->e_order_states_model->get_data() as $order_state)
        {
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $order_state->id, $order_state->name));
            $this->admin->form->cell('<span style="font-weight: bold; color: ' . $order_state->color . ';">' . $order_state->color . '</span>');
            $this->admin->form->cell($this->admin->form->cell_radio('~/set_default_order_state/' . $order_state->id, __('button_7'), (db_config('default_order_state_id') == $order_state->id)));
            $this->admin->form->cell((db_config('default_order_state_id') != $order_state->id) ? admin_anchor('~/delete/' . $order_state->id, __('button_2'), __('confirm_1')) : '');

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $order_state->id), 'edit');
            $contextmenu[] = array(__('button_7'), admin_url('~/set_default_order_state/' . $order_state->id), 'check');
            if(db_config('default_order_state_id') != $order_state->id) $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $order_state->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($order_state->id, 0, $this->cms->model->eshop_table('order_states'), NULL, $contextmenu);
        }
        
        $this->admin->form->button_helper(__('helper_1'));
        $this->admin->form->generate();
    }
    
    function set_default_order_state($order_state_id = '')
    {
        if($this->e_order_states_model->item_exists($order_state_id))
        {
            db_config('default_order_state_id', $order_state_id);
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    function add()
    {
        $this->_validation(is_form_true($this->input->post('_email_send')));
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['color'] = $this->input->post('color');
            $data['locked'] = is_form_true($this->input->post('locked'));
            $data['_email_send'] = is_form_true($this->input->post('_email_send'));
            $data['_email_subject'] = $this->input->post('_email_subject');
            $data['_email_content'] = $this->input->post('_email_content');
            
            $this->e_order_states_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', 'name', __('field_1'));
        $this->admin->form->add_field('colorpicker', 'color', __('field_2'), '#555555');
        $this->admin->form->add_field('checkbox', 'locked', __('field_3'));
        
        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('checkbox', '_email_send', __('field_4'));
        $this->admin->form->add_field('input', '_email_subject', __('field_5'));
        $this->admin->form->add_field('ckeditor', '_email_content', __('field_6'));
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($order_state_id = '')
    {
        if(!$this->e_order_states_model->item_exists($order_state_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['color'] = $this->input->post('color');
            $data['locked'] = is_form_true($this->input->post('locked'));
            $data['_email_send'] = is_form_true($this->input->post('_email_send'));
            $data['_email_subject'] = $this->input->post('_email_subject');
            $data['_email_content'] = $this->input->post('_email_content');
            
            $this->e_order_states_model->set_item_data($order_state_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->e_order_states_model->$order_state_id->name);
        $this->admin->form->add_field('colorpicker', 'color', __('field_2'), $this->e_order_states_model->$order_state_id->color);
        $this->admin->form->add_field('checkbox', 'locked', __('field_3'), $this->e_order_states_model->$order_state_id->locked);
        
        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('checkbox', '_email_send', __('field_4'), $this->e_order_states_model->$order_state_id->_email_send);
        $this->admin->form->add_field('input', '_email_subject', __('field_5'), $this->e_order_states_model->$order_state_id->_email_subject);
        $this->admin->form->add_field('ckeditor', '_email_content', __('field_6'), $this->e_order_states_model->$order_state_id->_email_content);

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($order_state_id = '')
    {
        if($this->e_order_states_model->item_exists($order_state_id))
        {
            if(db_config('default_order_state_id') == $order_state_id)
            {
                $this->admin->form->error(__('error_4'), TRUE);
            }
            else
            {
                $this->e_order_states_model->delete_item($order_state_id);
                $this->admin->form->message(__('message_3'), TRUE);
            }
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation($email_required = FALSE)
    {
        $email_required = ($email_required) ? '|required' : '';
        
        $this->admin->form->set_rules('name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('color', __('field_2'), 'trim|required|color');
        $this->admin->form->set_rules('locked', __('field_3'), 'trim|intval');
        $this->admin->form->set_rules('email_send', __('field_4'), 'trim|intval');
        $this->admin->form->set_rules('email_subject', __('field_5'), 'trim|max_length[255]' . $email_required);
        $this->admin->form->set_rules('email_content', __('field_6'), 'trim' . $email_required);
    }
    
}