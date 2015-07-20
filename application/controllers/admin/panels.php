<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Panels extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('panels');
        $this->cms->model->load_system('panel_types');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        $this->admin->form->col(__('col_5'));
        
        foreach($this->s_panels_model->get_data() as $panel)
        {
            $panel_type_id = $panel->panel_type_id;
            
            $options_cell = '';
            $options_cell .= admin_anchor('~/duplicate/' . $panel->id, __('button_9'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/delete/' . $panel->id, __('button_2'), __('confirm_1'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/export/' . $panel->id, __('button_10'));
            
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $panel->id, $panel->name));
            $this->admin->form->cell(admin_anchor('panel_types/edit/' . $panel_type_id, $this->s_panel_types_model->$panel_type_id->name));
            $this->admin->form->cell($panel->code);
            $this->admin->form->cell($this->admin->form->cell_checkbox((($panel->public) ? '~/unpublish_panel/' : '~/publish_panel/') . $panel->id, (($panel->public) ? __('button_7') : __('button_8')), $panel->public));
            $this->admin->form->cell($options_cell);

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $panel->id), 'edit');
            if($panel->public) $contextmenu[] = array(__('button_7'), admin_url('~/unpublish_panel/' . $panel->id), 'x');
            else $contextmenu[] = array(__('button_8'), admin_url('~/publish_panel/' . $panel->id), 'check');
            $contextmenu[] = array(__('button_9'), admin_url('~/duplicate/' . $panel->id), 'copy');
            $contextmenu[] = array(__('button_10'), admin_url('~/export/' . $panel->id), 'export');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $panel->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($panel->id, 0, $this->cms->model->system_table('panels'), NULL, $contextmenu);
        }
        
        $this->admin->form->generate();
    }
    
    function unpublish_panel($panel_id = '')
    {
        if($this->s_panels_model->item_exists($panel_id))
        {
            $this->s_panels_model->set_item_data($panel_id, array('public' => FALSE));
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_4'), TRUE);
        }
        admin_redirect();
    }
    
    function publish_panel($panel_id = '')
    {
        if($this->s_panels_model->item_exists($panel_id))
        {
            $this->s_panels_model->set_item_data($panel_id, array('public' => TRUE));
            $this->admin->form->message(__('message_5'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_5'), TRUE);
        }
        admin_redirect();
    }
    
    function add($panel_type_id = '')
    {
        $this->_validation(TRUE, TRUE);
        
        if(form_sent())
        {
            $this->_panel_type_variables_validation($this->input->post('panel_type_id'), 'add');
        }
        
        if($this->admin->form->validate())
        {
            // Add panel data
            $data = array();
            
            $data['panel_type_id'] = $this->input->post('panel_type_id');
            $data['name'] = $this->input->post('name');
            $data['code'] = $this->input->post('code');
            
            if(strlen($data['code']) == 0) $data['code'] = url_title($data['name']);

            $this->s_panels_model->add_item($data);
            
            // Add panel variables data
            $variables_data = array();
            
            foreach($this->cms->panels->get_panel_type_variable_names($this->input->post('panel_type_id'), 'add') as $variable_name)
            {
                $variables_data[$variable_name] = $this->input->post($variable_name);
            }
            
            $this->cms->panels->set_panel_data($this->s_panels_model->insert_id(), $variables_data);
            
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'));
        $this->admin->form->add_field('input', 'code', __('field_3'));
        $this->admin->form->add_field('select', 'panel_type_id', __('field_2'), $this->s_panel_types_model->get_data_in_col('name'), $panel_type_id, TRUE);
        $this->admin->form->ajax_area('ajax_panel_type_variables', array('panel_type_id'));
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function ajax_panel_type_variables()
    {
        if(!$this->input->is_ajax_request()) show_404();

        $data = array();
        
        $panel_type_id = $this->input->post('panel_type_id');
        
        if(strlen($panel_type_id) == 0)
        {
            $data['result'] = TRUE;
            $data['content'] = '';
        }
        else
        {
            $data['result'] = $this->s_panel_types_model->item_exists($panel_type_id);
            $data['content'] = '';
            $data['error'] = __('error_7');

            if($data['result'])
            {
                $this->_panel_type_variables_validation($panel_type_id, 'add');
                if(form_sent()) $this->admin->form->validate();
                
                foreach($this->cms->panels->get_panel_type_variable_ids($panel_type_id, 'add') as $panel_type_variable_id)
                {
                    $data['content'] .= $this->cms->panels->get_panel_type_variable_field_row($panel_type_variable_id);
                }
            }
        }
        
        $data['field_id'] = $this->admin->form->get_field_id(TRUE);
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data));
    }
    
    function edit($panel_id = '')
    {
        if(!$this->s_panels_model->item_exists($panel_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $panel_type_id = $this->s_panels_model->$panel_id->panel_type_id;
        
        $this->_validation(FALSE, ($this->s_panels_model->$panel_id->code != $this->input->post('code')));
        $this->_panel_type_variables_validation($panel_type_id, 'edit');
        
        if($this->admin->form->validate())
        {
            // Edit panel data
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['code'] = $this->input->post('code');
            
            if(strlen($data['code']) == 0) $data['code'] = url_title($data['name']);
            
            $this->s_panels_model->set_item_data($panel_id, $data);
            
            // Edit panel variables data
            $variables_data = array();
            
            foreach($this->cms->panels->get_panel_type_variable_names($panel_type_id, 'edit') as $variable_name)
            {
                $variables_data[$variable_name] = $this->input->post($variable_name);
            }
            
            $this->cms->panels->set_panel_data($panel_id, $variables_data);
            
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->s_panels_model->$panel_id->name);
        $this->admin->form->add_field('input', 'code', __('field_3'), $this->s_panels_model->$panel_id->code);
        $this->admin->form->add_field('adminbutton', 'panel_types/edit/' . $panel_type_id, __('field_2'), $this->s_panel_types_model->$panel_type_id->name, 'pencil');
        
        $panel_data = $this->cms->panels->get_panel_data($panel_id);
                
        foreach($this->cms->panels->get_panel_type_variables($panel_type_id, 'edit') as $panel_type_variable)
        {
            $variable_name = $panel_type_variable->name;
            $this->cms->panels->add_panel_type_variable_field($panel_type_variable->id, @$panel_data->$variable_name);
        }

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($panel_id = '')
    {
        if($this->s_panels_model->item_exists($panel_id))
        {
            $this->s_panels_model->delete_item($panel_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    function duplicate($panel_id = '')
    {
        if($this->s_panels_model->item_exists($panel_id))
        {
            // Get panel data
            $panel_data = $this->cms->panels->get_panel_data($panel_id);
            
            // Add panel data
            $data = array();
            
            $data['panel_type_id'] = $panel_data->panel_type_id;
            $data['name'] = $panel_data->name;
            $data['public'] = $panel_data->public;
            $data['code'] = $this->cms->panels->get_code_copy($panel_data->code);
            
            $this->s_panels_model->add_item($data);
            
            // Add panel variables data
            $variables_data = array();
            
            foreach($this->cms->panels->get_panel_type_variable_names($panel_data->panel_type_id, 'both') as $variable_name)
            {
                $variables_data[$variable_name] = $panel_data->$variable_name;
            }
            
            $this->cms->panels->set_panel_data($this->s_panels_model->insert_id(), $variables_data);
            
            $this->admin->form->message(__('message_6'), TRUE);
            admin_redirect();
        }
        else
        {
            $this->admin->form->error(__('error_6'), TRUE);
        }
        admin_redirect();
    }
    
    function export($panel_id = '')
    {
        if($this->s_panels_model->item_exists($panel_id))
        {
            $this->admin->form->warning("Panely zatiaľ nie je možné exportovať.", TRUE);
            //$this->cms->export->panel($panel_id);
            //$this->cms->export->download();
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation($panel_type_id_required = FALSE, $unique_code = TRUE)
    {
        $unique_code = $unique_code ? '|unique_system[panels.code]' : '';
        
        $this->admin->form->set_rules('name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('code', __('field_3'), 'trim|url_title|max_length[50]' . $unique_code);
        if($panel_type_id_required)
        $this->admin->form->set_rules('panel_type_id', __('field_2'), 'trim|required|item_exists_system[panel_types]');
    }
    
    protected function _panel_type_variables_validation($panel_type_id = '', $type = '')
    {
        foreach($this->cms->panels->get_panel_type_variable_ids($panel_type_id, $type) as $panel_type_variable_id)
        {
            $panel_type_variable = $this->cms->panels->get_panel_type_variable($panel_type_variable_id);
            $this->admin->form->set_rules($panel_type_variable->name, $panel_type_variable->title, $panel_type_variable->rules);
        }
    }
    
}