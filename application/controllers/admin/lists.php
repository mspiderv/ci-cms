<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lists extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('lists');
        $this->cms->model->load_system('list_types');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        
        // Zoznamy
        foreach($this->s_lists_model->get_data() as $list)
        {
            $list_type_id = $list->list_type_id;
            
            $options_cell = '';
            $options_cell .= admin_anchor('~/add_item/' . $list->id, __('button_11'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/duplicate/' . $list->id, __('button_9'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/delete/' . $list->id, __('button_2'), __('confirm_1'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/export/' . $list->id, __('button_10'));
            
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $list->id . '#tab-1', $list->name));
            $this->admin->form->cell(admin_anchor('list_types/edit/' . $list_type_id, $this->s_list_types_model->$list_type_id->name));
            $this->admin->form->cell($this->admin->form->cell_checkbox((($list->public) ? '~/unpublish_list/' : '~/publish_list/') . $list->id, (($list->public) ? __('button_7') : __('button_8')), $list->public));
            $this->admin->form->cell($options_cell);

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $list->id . '#tab-1'), 'edit');
            $contextmenu[] = array(__('button_11'), admin_url('~/add_item/' . $list->id), 'add');
            if($list->public) $contextmenu[] = array(__('button_7'), admin_url('~/unpublish_list/' . $list->id), 'x');
            else $contextmenu[] = array(__('button_8'), admin_url('~/publish_list/' . $list->id), 'check');
            $contextmenu[] = array(__('button_9'), admin_url('~/duplicate/' . $list->id), 'copy');
            $contextmenu[] = array(__('button_10'), admin_url('~/export/' . $list->id), 'export');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $list->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($list->id, 0, $this->cms->model->system_table('lists'), NULL, $contextmenu);
            
            // Položky zoznamu
            foreach($this->cms->lists->get_list_data($list->id) as $item)
            {
                $options_cell = '';
                $options_cell .= admin_anchor('~/duplicate_item/' . $list->id . '/' . $item->id, __('button_9'));
                $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                $options_cell .= admin_anchor('~/delete_item/' . $list->id . '/' . $item->id, __('button_2'), __('confirm_2'));

                $this->admin->form->cell_left(admin_anchor('~/edit_item/' . $list->id . '/' . $item->id, $this->cms->lists->get_item_name($list->id, $item->id)));
                $this->admin->form->cell();
                $this->admin->form->cell($this->admin->form->cell_checkbox((($item->public) ? '~/unpublish_item/' : '~/publish_item/') . $list->id . '/' . $item->id, (($item->public) ? __('button_12') : __('button_13')), $item->public));
                $this->admin->form->cell($options_cell);

                $contextmenu = array();

                $contextmenu[] = array(__('button_3'), admin_url('~/edit_item/' . $list->id . '/' . $item->id), 'edit');
                if($item->public) $contextmenu[] = array(__('button_12'), admin_url('~/unpublish_item/' . $list->id . '/' . $item->id), 'x');
                else $contextmenu[] = array(__('button_13'), admin_url('~/publish_item/' . $list->id . '/' . $item->id), 'check');
                $contextmenu[] = array(__('button_9'), admin_url('~/duplicate_item/' . $list->id . '/' . $item->id), 'copy');
                $contextmenu[] = array(__('button_2'), admin_url('~/delete_item/' . $list->id . '/' . $item->id), 'delete', __('confirm_2'));
                
                $this->admin->form->row($item->id . '_' . $list->id, 1, $this->cms->model->user_table('list_type_data_' . $list_type_id), NULL, $contextmenu);
            }
        }
        
        $this->admin->form->generate();
    }
    
    /* Zoznamy */
    
    function unpublish_list($list_id = '')
    {
        if($this->s_lists_model->item_exists($list_id))
        {
            $this->s_lists_model->set_item_data($list_id, array('public' => FALSE));
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_4'), TRUE);
        }
        admin_redirect();
    }
    
    function publish_list($list_id = '')
    {
        if($this->s_lists_model->item_exists($list_id))
        {
            $this->s_lists_model->set_item_data($list_id, array('public' => TRUE));
            $this->admin->form->message(__('message_5'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_5'), TRUE);
        }
        admin_redirect();
    }
    
    function add($list_type_id = '')
    {
        $this->_validation(TRUE);
        
        if($this->admin->form->validate())
        {
            // Add list data
            $data = array();
            
            $data['list_type_id'] = $this->input->post('list_type_id');
            $data['name'] = $this->input->post('name');

            $this->s_lists_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'));
        $this->admin->form->add_field('select', 'list_type_id', __('field_2'), $this->s_list_types_model->get_data_in_col('name'), $list_type_id, TRUE);
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($list_id = '')
    {
        if(!$this->s_lists_model->item_exists($list_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation(FALSE);
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            
            $this->s_lists_model->set_item_data($list_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $list_type_id = $this->s_lists_model->$list_id->list_type_id;
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->s_lists_model->$list_id->name);
        $this->admin->form->add_field('adminbutton', 'list_types/edit/' . $list_type_id, __('field_2'), $this->s_list_types_model->$list_type_id->name, 'pencil');
        
        $this->admin->form->tab(__('tab_2'));
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_5'));
        $this->admin->form->col(__('col_4'));
        
        foreach($this->cms->lists->get_list_data($list_id) as $item)
        {
            $options_cell = '';
            $options_cell .= admin_anchor('~/duplicate_item/' . $list_id . '/' . $item->id, __('button_9'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/delete_item/' . $list_id . '/' . $item->id, __('button_2'), __('confirm_2'));

            $this->admin->form->cell_left(admin_anchor('~/edit_item/' . $list_id . '/' . $item->id, $this->cms->lists->get_item_name($list_id, $item->id)));
            $this->admin->form->cell($this->admin->form->cell_checkbox((($item->public) ? '~/unpublish_item/' : '~/publish_item/') . $list_id . '/' . $item->id, (($item->public) ? __('button_12') : __('button_13')), $item->public));
            $this->admin->form->cell($options_cell);

            $contextmenu = array();

            $contextmenu[] = array(__('button_3'), admin_url('~/edit_item/' . $list_id . '/' . $item->id), 'edit');
            if($item->public) $contextmenu[] = array(__('button_12'), admin_url('~/unpublish_item/' . $list_id . '/' . $item->id), 'x');
            else $contextmenu[] = array(__('button_13'), admin_url('~/publish_item/' . $list_id . '/' . $item->id), 'check');
            $contextmenu[] = array(__('button_9'), admin_url('~/duplicate_item/' . $list_id . '/' . $item->id), 'copy');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete_item/' . $list_id . '/' . $item->id), 'delete', __('confirm_2'));

            $this->admin->form->row($item->id . '_' . $list_id, 0, $this->cms->model->user_table('list_type_data_' . $this->s_lists_model->$list_id->list_type_id), NULL, $contextmenu);
        }
        
        $this->admin->form->listing();

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_admin_link('~/add_item/' . $list_id, __('button_14'), 'plus');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function duplicate($list_id = '')
    {
        if($this->cms->lists->duplicate_list($list_id))
        {
            $this->admin->form->message(__('message_6'), TRUE);
            admin_redirect();
        }
        else
        {
            $this->admin->form->error(__('error_6'), TRUE);
        }
        admin_redirect();
    }
    
    function export($list_id = '')
    {
        if($this->s_lists_model->item_exists($list_id))
        {
            $this->admin->form->warning("Zoznamy zatiaľ nie je možné exportovať.", TRUE);
            //$this->cms->export->list($list_id);
            //$this->cms->export->download();
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    function delete($list_id = '')
    {
        if($this->s_lists_model->item_exists($list_id))
        {
            $this->s_lists_model->delete_item($list_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation($validate_list_type_id = TRUE)
    {
        $this->admin->form->set_rules('name', __('field_1'), 'trim|required|max_length[255]');
        if($validate_list_type_id) $this->admin->form->set_rules('list_type_id', __('field_2'), 'trim|required|item_exists_system[list_types]');
    }
    
    /* Položky zoznamov */
    
    function unpublish_item($list_id = '', $item_id = '')
    {
        if($this->cms->lists->list_has_item($list_id, $item_id))
        {
            $this->u_list_type_data_model->set_item_data($item_id, array('public' => FALSE));
            $this->admin->form->message(__('message_9'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_9'), TRUE);
        }
        admin_redirect();
    }
    
    function publish_item($list_id = '', $item_id = '')
    {
        if($this->cms->lists->list_has_item($list_id, $item_id))
        {
            $this->u_list_type_data_model->set_item_data($item_id, array('public' => TRUE));
            $this->admin->form->message(__('message_10'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_10'), TRUE);
        }
        admin_redirect();
    }
    
    function add_item($list_id = '')
    {
        $this->_validation_item(TRUE);
        
        $list_type_id = 0;
        
        if(form_sent())
        {
            $list_type_id = $this->s_lists_model->get_item_data($this->input->post('list_id'), 'list_type_id');
            $this->_list_type_variables_validation($list_type_id, 'add');
        }
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            if(!$this->cms->lists->has_list_primary_variable($this->input->post('list_id'))) $data['name'] = $this->input->post('name');
            
            foreach($this->cms->lists->get_list_type_variable_names($list_type_id, 'add') as $variable_name)
            {
                $data[$variable_name] = $this->input->post($variable_name);
            }
            
            $this->cms->lists->add_list_item($this->input->post('list_id'), $data);
            
            $this->admin->form->message(__('message_7'), TRUE);
            admin_redirect();
        }
        
        //$this->admin->form->add_field('input', 'name', __('field_3'));
        $this->admin->form->add_field('select', 'list_id', __('field_4'), $this->s_lists_model->get_data_in_col('name'), $list_id, TRUE);
        $this->admin->form->ajax_area('ajax_list_variables', array('list_id'));
        
        $this->admin->form->button_submit(__('button_15'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function ajax_list_variables()
    {
        if(!$this->input->is_ajax_request()) show_404();

        $data = array();
        
        $list_id = $this->input->post('list_id');
        
        if(strlen($list_id) == 0)
        {
            $data['result'] = TRUE;
            $data['content'] = '';
        }
        else
        {
            $data['result'] = $this->s_lists_model->item_exists($list_id);
            $data['content'] = '';
            $data['error'] = __('error_7');

            if($data['result'])
            {
                $list_type_id = $this->s_lists_model->$list_id->list_type_id;
                
                if(!$this->cms->lists->has_list_primary_variable($list_id)) $data['content'] .= $this->admin->form->get_field_row('input', 'name', __('field_3'));
                
                $this->_list_type_variables_validation($list_type_id, 'add');
                if(form_sent()) $this->admin->form->validate();
                
                foreach($this->cms->lists->get_list_type_variable_ids($list_type_id, 'add') as $list_type_variable_id)
                {
                    $data['content'] .= $this->cms->lists->get_list_type_variable_field_row($list_type_variable_id);
                }
            }
        }
        
        $data['field_id'] = $this->admin->form->get_field_id(TRUE);
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data));
    }
    
    function edit_item($list_id = '', $item_id = '')
    {
        if(!$this->s_lists_model->item_exists($list_id))
        {
            $this->admin->form->error(__('error_8'), TRUE);
            admin_redirect();
        }
        
        if(!$this->cms->lists->list_has_item($list_id, $item_id))
        {
            $this->admin->form->error(__('error_8'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_breadcrumb(array(
            'text' => $this->s_lists_model->$list_id->name,
            'href' => admin_url('~/edit/' . $list_id)
        ));
        
        $list_type_id = $this->s_lists_model->$list_id->list_type_id;
        
        $this->_validation_item(FALSE);
        $this->_list_type_variables_validation($list_type_id, 'edit');
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            
            foreach($this->cms->lists->get_list_type_variable_names($list_type_id, 'add') as $variable_name)
            {
                $data[$variable_name] = $this->input->post($variable_name);
            }
            
            $this->cms->lists->set_item_data($list_id, $item_id, $data);
            
            $this->admin->form->message(__('message_8'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $item_data = $this->cms->lists->get_list_data($list_id, $item_id);
        
        if(!$this->cms->lists->has_list_primary_variable($list_id))
        $this->admin->form->add_field('input', 'name', __('field_3'), $item_data->name);
        
        foreach($this->cms->lists->get_list_type_variables($list_type_id, 'edit') as $list_type_variable)
        {
            $variable_name = $list_type_variable->name;
            $this->cms->lists->add_list_type_variable_field($list_type_variable->id, @$item_data->$variable_name);
        }

        $this->admin->form->button_submit(__('button_16'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_admin_link('~/edit/' . $list_id . '#tab-2', $this->s_lists_model->$list_id->name, 'arrowreturnthick-1-w');
        
        $this->admin->form->generate_index_button = FALSE;
        $this->admin->form->generate();
    }
    
    function duplicate_item($list_id = '', $item_id = '')
    {
        if($this->cms->lists->duplicate_item($list_id, $item_id))
        {
            $this->admin->form->message(__('message_12'), TRUE);
            admin_redirect();
        }
        else
        {
            $this->admin->form->error(__('error_12'), TRUE);
        }
        admin_redirect();
    }
    
    function delete_item($list_id = '', $item_id = '')
    {
        if($this->cms->lists->list_has_item($list_id, $item_id))
        {
            $this->u_list_type_data_model->delete_item($item_id);
            $this->admin->form->message(__('message_11'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_11'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation_item($validate_list_id = TRUE)
    {
        $this->admin->form->set_rules('name', __('field_3'), 'trim|max_length[255]');
        if($validate_list_id) $this->admin->form->set_rules('list_id', __('field_4'), 'trim|required|item_exists_system[lists]');
    }
    
    protected function _list_type_variables_validation($list_type_id = '', $type = '')
    {
        foreach($this->cms->lists->get_list_type_variable_ids($list_type_id, $type) as $list_type_variable_id)
        {
            $list_type_variable = $this->cms->lists->get_list_type_variable($list_type_variable_id);
            $this->admin->form->set_rules($list_type_variable->name, $list_type_variable->title, $list_type_variable->rules);
        }
    }
    
}