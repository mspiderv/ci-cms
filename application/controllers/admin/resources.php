<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Resources extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('resources');
        $this->cms->model->load_system('resource_rels');
    }

    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        $this->admin->form->col(__('col_5'));
        
        $this->s_resources_model->where('theme_id', '=', db_config('active_theme_id'));
        foreach($this->s_resources_model->get_data() as $resource)
        {
            $options_cell = '';
            
            if(!$resource->global)
            {
                $options_cell .= admin_anchor('~/add_rel/' . $resource->id, __('button_11'));
                $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            
            $options_cell .= admin_anchor('~/delete/' . $resource->id, __('button_2'), __('confirm_1'));
            
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $resource->id, $resource->url));
            $this->admin->form->cell($resource->type);
            $this->admin->form->cell($this->admin->form->cell_checkbox((($resource->global) ? '~/unglobal_resource/' : '~/global_resource/') . $resource->id, (($resource->global) ? __('button_9') : __('button_10')), $resource->global));
            $this->admin->form->cell($this->admin->form->cell_checkbox((($resource->public) ? '~/unpublish_resource/' : '~/publish_resource/') . $resource->id, (($resource->public) ? __('button_7') : __('button_8')), $resource->public));
            $this->admin->form->cell($options_cell);

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $resource->id), 'edit');
            if(!$resource->global) $contextmenu[] = array(__('button_11'), admin_url('~/add_rel/' . $resource->id), 'add');
            if($resource->public) $contextmenu[] = array(__('button_7'), admin_url('~/unpublish_resource/' . $resource->id), 'x');
            else $contextmenu[] = array(__('button_8'), admin_url('~/publish_resource/' . $resource->id), 'check');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $resource->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($resource->id, 0, $this->cms->model->system_table('resources'), NULL, $contextmenu);
            
            $this->s_resource_rels_model->where('resource_id', '=' , $resource->id);
            foreach($this->s_resource_rels_model->get_data() as $resource_rel)
            {
                if(!cfg('general', 'eshop') && ($resource_rel->type == 'product_category' || $resource_rel->type == 'product')) continue;
                
                $this->admin->form->cell_left(admin_anchor('~/edit_rel/' . $resource_rel->id, $this->cms->resources->get_rel_name($resource_rel->id)));
                $this->admin->form->cell($this->cms->resources->get_rel_type_name($resource_rel->type));
                $this->admin->form->cell();
                $this->admin->form->cell($this->admin->form->cell_checkbox((($resource_rel->public) ? '~/unpublish_resource_rel/' : '~/publish_resource_rel/') . $resource_rel->id, (($resource_rel->public) ? __('button_13') : __('button_14')), $resource_rel->public));
                $this->admin->form->cell(admin_anchor('~/delete_rel/' . $resource_rel->id, __('button_2'), __('confirm_2')));

                $contextmenu = array();

                $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $resource_rel->id), 'edit');
                if($resource_rel->public) $contextmenu[] = array(__('button_7'), admin_url('~/unpublish_resource/' . $resource_rel->id), 'x');
                else $contextmenu[] = array(__('button_8'), admin_url('~/publish_resource/' . $resource_rel->id), 'check');
                $contextmenu[] = array(__('button_2'), admin_url('~/delete_rel/' . $resource_rel->id), 'delete', __('confirm_2'));

                $this->admin->form->row($resource_rel->id, 1, $this->cms->model->system_table('resource_rels'), NULL, $contextmenu);
            }
        }
        
        $this->admin->form->button_helper(__('helper_1'));
        $this->admin->form->generate();
    }
    
    function unpublish_resource($resource_id = '')
    {
        if($this->s_resources_model->item_exists($resource_id))
        {
            $this->s_resources_model->set_item_data($resource_id, array('public' => FALSE));
            $this->admin->form->message(__('message_4'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    function publish_resource($resource_id = '')
    {
        if($this->s_resources_model->item_exists($resource_id))
        {
            $this->s_resources_model->set_item_data($resource_id, array('public' => TRUE));
            $this->admin->form->message(__('message_5'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_4'), TRUE);
        }
        admin_redirect();
    }
    
    function unpublish_resource_rel($resource_rel_id = '')
    {
        if($this->s_resource_rels_model->item_exists($resource_rel_id))
        {
            $this->s_resource_rels_model->set_item_data($resource_rel_id, array('public' => FALSE));
            $this->admin->form->message(__('message_9'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_8'), TRUE);
        }
        admin_redirect();
    }
    
    function publish_resource_rel($resource_rel_id = '')
    {
        if($this->s_resource_rels_model->item_exists($resource_rel_id))
        {
            $this->s_resource_rels_model->set_item_data($resource_rel_id, array('public' => TRUE));
            $this->admin->form->message(__('message_10'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_9'), TRUE);
        }
        admin_redirect();
    }
    
    function unglobal_resource($resource_id = '')
    {
        if($this->s_resources_model->item_exists($resource_id))
        {
            $this->s_resources_model->set_item_data($resource_id, array('global' => FALSE));
            $this->admin->form->message(__('message_6'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    function global_resource($resource_id = '')
    {
        if($this->s_resources_model->item_exists($resource_id))
        {
            $this->s_resources_model->set_item_data($resource_id, array('global' => TRUE));
            $this->admin->form->message(__('message_7'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_4'), TRUE);
        }
        admin_redirect();
    }
    
    function add()
    {
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['theme_id'] = db_config('active_theme_id');
            $data['type'] = $this->input->post('type');
            $data['url'] = $this->input->post('url');
            $data['global'] = $this->input->post('global');
            $data['public'] = is_form_true($this->input->post('public'));
            
            $this->s_resources_model->add_item($data);
            $this->admin->form->message(__('message_1'), TRUE);
            admin_redirect();
        }
        
        $radios = array();
        $radios['css'] = __('radio_1');
        $radios['js'] = __('radio_2');
        
        $this->admin->form->add_field('radios', 'type', __('field_1'), $radios);
        $this->admin->form->add_field('input', 'url', __('field_2'));
        $this->admin->form->add_field('checkbox', 'global', __('field_3'), TRUE);
        $this->admin->form->add_field('checkbox', 'public', __('field_4'), TRUE);
        
        $this->admin->form->button_submit(__('button_4'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function add_rel($resource_id = '')
    {
        if(!$this->s_resources_model->item_exists($resource_id))
        {
            $this->admin->form->error(__('error_5'), TRUE);
            admin_redirect();
        }
        
        if($this->s_resources_model->$resource_id->global)
        {
            $this->admin->form->error(__('error_6'), TRUE);
            admin_redirect();
        }
        
        if(form_sent())
        {
            $this->_validation_rel_type($this->input->post('type'));
        }
        
        $this->_validation_rel();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['resource_id'] = $resource_id;
            $data['public'] = is_form_true($this->input->post('public'));
            $data['type'] = $this->input->post('type');
            $data[$data['type'] . '_id'] = $this->input->post('value');
            
            if($this->cms->resources->resource_has_rel($data['resource_id'], $data['type'], $data[$data['type'] . '_id']))
            {
                $this->admin->form->warning(__('warning_1'), TRUE);
            }
            else
            {
                $this->s_resource_rels_model->add_item($data);
                $this->admin->form->message(__('message_8'), TRUE);
            }
            
            admin_redirect();
        }
        
        $this->admin->form->hidden_fields['resource_id'] = $resource_id;
        
        $this->admin->form->add_field('checkbox', 'public', __('field_6'), TRUE);
        $this->admin->form->add_field('select_resource_rel', 'type', __('field_5'), '', TRUE);
        $this->admin->form->ajax_area('ajax_resources', array('type'));
        
        $this->admin->form->button_submit(__('button_12'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit_rel($resource_rel_id = '')
    {
        if(!$this->s_resource_rels_model->item_exists($resource_rel_id))
        {
            $this->admin->form->error(__('error_11'), TRUE);
            admin_redirect();
        }
        
        $resource_id = $this->s_resource_rels_model->$resource_rel_id->resource_id;
        $selected = $this->s_resource_rels_model->get_item_data($resource_rel_id, $this->s_resource_rels_model->get_item_data($resource_rel_id, 'type') . '_id');
        
        if(form_sent())
        {
            $this->_validation_rel_type($this->input->post('type'));
        }
        
        $this->_validation_rel();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['page_type_id'] = NULL;
            $data['page_id'] = NULL;
            $data['panel_type_id'] = NULL;
            $data['panel_id'] = NULL;
            $data['product_category_id'] = NULL;
            $data['product_id'] = NULL;
            $data['page_category_id'] = NULL;
            $data['service_id'] = NULL;
            
            $data['public'] = is_form_true($this->input->post('public'));
            $data['type'] = $this->input->post('type');
            $data[$data['type'] . '_id'] = $this->input->post('value');
            
            if($this->cms->resources->resource_has_rel($resource_id, $data['type'], $data[$data['type'] . '_id']) && $selected != $data[$data['type'] . '_id'])
            {
                $this->admin->form->error(__('error_12'));
            }
            else
            {
                $this->s_resource_rels_model->set_item_data($resource_rel_id, $data);
                $this->admin->form->message(__('message_12'), url_param() != 'accept');
                if(url_param() != 'accept') admin_redirect();
            }
        }
        
        $this->admin->form->hidden_fields['resource_id'] = $resource_id;
        $this->admin->form->hidden_fields['selected'] = $selected;
        
        $this->admin->form->add_field('checkbox', 'public', __('field_6'), $this->s_resource_rels_model->$resource_rel_id->public);
        $this->admin->form->add_field('select_resource_rel', 'type', __('field_5'), $this->s_resource_rels_model->$resource_rel_id->type, TRUE);
        $this->admin->form->ajax_area('ajax_resources', array('type'));
        
        $this->admin->form->button_submit(__('button_15'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function ajax_resources()
    {
        if(!$this->input->is_ajax_request()) show_404();

        $data = array();
        
        $resource_id = $this->input->post('resource_id');
        $type = $this->input->post('type');
        $selected = $this->input->post('selected');
        
        if(strlen($type) == 0)
        {
            $data['result'] = TRUE;
            $data['content'] = '';
        }
        else
        {
            $data['result'] = ($this->cms->resources->resource_exists($resource_id) && $this->cms->resources->rel_type_exists($type));
            $data['content'] = '';
            $data['error'] = __('error_7');

            if($data['result'])
            {
                $this->_validation_rel_type($type);
                if(form_sent()) $this->admin->form->validate();
                
                $data['content'] = $this->admin->form->get_field_row('select', 'value', $this->cms->resources->get_rel_type_name($type), $this->cms->resources->get_select_data($resource_id, $type, $selected), $selected);
            }
        }
        
        $data['field_id'] = $this->admin->form->get_field_id(TRUE);
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($data));
    }
    
    function edit($resource_id = '')
    {
        if(!$this->s_resources_model->item_exists($resource_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['type'] = $this->input->post('type');
            $data['url'] = $this->input->post('url');
            $data['global'] = $this->input->post('global');
            $data['public'] = is_form_true($this->input->post('public'));
            
            $this->s_resources_model->set_item_data($resource_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_1'));
        
        $radios = array();
        $radios['css'] = __('radio_1');
        $radios['js'] = __('radio_2');
        
        $this->admin->form->add_field('radios', 'type', __('field_1'), $radios, $this->s_resources_model->$resource_id->type);
        $this->admin->form->add_field('input', 'url', __('field_2'), $this->s_resources_model->$resource_id->url);
        $this->admin->form->add_field('checkbox', 'global', __('field_3'), $this->s_resources_model->$resource_id->global);
        $this->admin->form->add_field('checkbox', 'public', __('field_4'), $this->s_resources_model->$resource_id->public);

        $this->admin->form->tab(__('tab_2'));
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_4'));
        $this->admin->form->col(__('col_5'));
        
        $this->s_resource_rels_model->where('resource_id', '=' , $resource_id);
        foreach($this->s_resource_rels_model->get_data() as $resource_rel)
        {
            if(!cfg('general', 'eshop') && ($resource_rel->type == 'product_category' || $resource_rel->type == 'product')) continue;
            
            $this->admin->form->cell_left(admin_anchor('~/edit_rel/' . $resource_rel->id, $this->cms->resources->get_rel_name($resource_rel->id)));
            $this->admin->form->cell($this->cms->resources->get_rel_type_name($resource_rel->type));
            $this->admin->form->cell($this->admin->form->cell_checkbox((($resource_rel->public) ? '~/unpublish_resource_rel/' : '~/publish_resource_rel/') . $resource_rel->id, (($resource_rel->public) ? __('button_13') : __('button_14')), $resource_rel->public));
            $this->admin->form->cell(admin_anchor('~/delete_rel/' . $resource_rel->id, __('button_2'), __('confirm_2')));

            $contextmenu = array();

            $contextmenu[] = array(__('button_3'), admin_url('~/edit/' . $resource_rel->id), 'edit');
            if($resource_rel->public) $contextmenu[] = array(__('button_7'), admin_url('~/unpublish_resource/' . $resource_rel->id), 'x');
            else $contextmenu[] = array(__('button_8'), admin_url('~/publish_resource/' . $resource_rel->id), 'check');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $resource_rel->id), 'delete', __('confirm_2'));

            $this->admin->form->row($resource_rel->id, 0, $this->cms->model->system_table('resource_rels'), NULL, $contextmenu);
        }

        $this->admin->form->listing();

        $this->admin->form->button_submit(__('button_5'));
        $this->admin->form->button_submit(__('button_6'), 'accept', 'check');
        
        if(!$this->s_resources_model->$resource_id->global)
        {
            $this->admin->form->button_admin_link('~/add_rel/' . $resource_id, __('button_11'), 'plus');
        }
        
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($resource_id = '')
    {
        if($this->s_resources_model->item_exists($resource_id))
        {
            $this->s_resources_model->delete_item($resource_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    function delete_rel($resource_rel_id = '')
    {
        if($this->s_resource_rels_model->item_exists($resource_rel_id))
        {
            $this->s_resource_rels_model->delete_item($resource_rel_id);
            $this->admin->form->message(__('message_11'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_10'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation()
    {
        $this->admin->form->set_rules('type', __('field_1'), 'trim|required|value[css,js]');
        $this->admin->form->set_rules('url', __('field_2'), 'trim|required');
        $this->admin->form->set_rules('global', __('field_3'), 'intval');
        $this->admin->form->set_rules('public', __('field_4'), 'intval');
    }
    
    protected function _validation_rel()
    {
        $this->admin->form->set_rules('type', __('field_5'), 'trim|required|resource_rel_type');
        $this->admin->form->set_rules('public', __('field_6'), 'intval');
    }
    
    protected function _validation_rel_type($type = '')
    {
        if(!$this->cms->resources->rel_type_exists($type)) return FALSE;
        $this->admin->form->set_rules('value', $this->cms->resources->get_rel_type_name($type), 'trim|required|item_exists_resource_rel[' . $type . ']');
    }
    
}