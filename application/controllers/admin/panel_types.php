<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Panel_types extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('panel_types');
        $this->cms->model->load_system('panel_type_variables');
    }
    
    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        
        foreach($this->s_panel_types_model->get_data() as $panel_type)
        {
            $options_cell = '';
            $options_cell .= admin_anchor('~/add_variable/' . $panel_type->id . '#tab-1', __('button_5'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/delete/' . $panel_type->id, __('button_2'), __('confirm_1'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/export/' . $panel_type->id, __('button_8'));
            
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $panel_type->id . '#tab-1', $panel_type->name));
            $this->admin->form->cell($panel_type->tpl);
            $this->admin->form->cell(($panel_type->class != '') ? $panel_type->class . ' / ' . $panel_type->method : '');
            $this->admin->form->cell($options_cell);

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_4'), admin_url('~/edit/' . $panel_type->id . '#tab-1'), 'edit');
            $contextmenu[] = array(__('button_5'), admin_url('~/add_variable/' . $panel_type->id . '#tab-1'), 'add');
            $contextmenu[] = array(__('button_8'), admin_url('~/export/' . $panel_type->id), 'export');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $panel_type->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($panel_type->id, 0, $this->cms->model->system_table('panel_types'), NULL, $contextmenu);
            
            // Panel type variables
            $this->s_panel_type_variables_model->where('panel_type_id', '=', $panel_type->id);
            foreach($this->s_panel_type_variables_model->get_data() as $panel_type_variable)
            {
                $this->admin->form->cell_left(admin_anchor('~/edit_variable/' . $panel_type_variable->id, $panel_type_variable->title));
                $this->admin->form->cell($panel_type_variable->name);
                $this->admin->form->cell();
                $this->admin->form->cell(admin_anchor('~/delete_variable/' . $panel_type_variable->id, __('button_2'), __('confirm_2')));

                $contextmenu = array();

                $contextmenu[] = array(__('button_4'), admin_url('~/edit_variable/' . $panel_type_variable->id), 'edit');
                $contextmenu[] = array(__('button_2'), admin_url('~/delete_variable/' . $panel_type_variable->id), 'delete', __('confirm_2'));

                $this->admin->form->row($panel_type_variable->id, 1, $this->cms->model->system_table('panel_type_variables_' . $panel_type->id), TRUE, $contextmenu);
            }
        }
        
        $this->admin->form->button_helper(__('helper_1'));
        
        $this->admin->form->generate();
    }
    
    // Typy panelov
    
    function add()
    {
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['tpl'] = $this->input->post('tpl');
            $data['class'] = $this->input->post('class');
            $data['method'] = $this->input->post('method');
            $data['parameters'] = $this->input->post('parameters');
            
            $this->s_panel_types_model->add_item($data);
            
            /* Vytvorenie novej tabuľky (user_panel_type_data_X) */
            
            // Načíta dbforge
            $this->load->dbforge();
            
            // Názov novej tabuľky
            $new_table_name = $this->cms->model->user_table('panel_type_data_' . $this->s_panel_types_model->insert_id());
            
            // Názov tabuľky panelov
            $panels_table_name = $this->db->dbprefix . $this->cms->model->system_table('panels');
            
            // Polia novej tabuľky
            $new_table_fields = array(
                cfg('table_cols', 'id') => array(
                    'type' => 'SMALLINT',
                    'constraint' => 5, 
                    'unsigned' => TRUE
                )
            );
            
            // Pridanie polí novej tabuľke
            $this->dbforge->add_field($new_table_fields);
            
            // Stĺpcu ID nastavíme primárky kľúč 
            $this->dbforge->add_key(cfg('table_cols', 'id'), TRUE);
            
            // Vytvorenie tabuľky
            $this->dbforge->create_table($new_table_name);
            
            // Typ uloženia zmeníme na INNODB
            $this->db->query('ALTER TABLE `' . $this->db->dbprefix . $new_table_name. '` ENGINE = INNODB');
            
            // Stĺpec ID previažeme so stĺpcom ID v tabuľke 'system_panels' -> keď zmažem nejaký panel aby sa mi zmazal aj príslušný riadok v tejto tabuľke
            $this->db->query('ALTER TABLE `' . $this->db->dbprefix . $new_table_name . '` ADD FOREIGN KEY (`' . cfg('table_cols', 'id') . '`) REFERENCES  `' . $panels_table_name . '` (`' . cfg('table_cols', 'id') . '`) ON DELETE CASCADE ON UPDATE CASCADE');
            
            // Načíta cache driver
            $this->load->driver('cache', array('adapter' => 'file'));
            
            // Vymaže zoznam tabuliek z cache
            $this->cache->delete('list_tables');
            
            $this->admin->form->message(__('message_1'), TRUE);

            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'));
        $this->admin->form->add_field('select', 'tpl', __('field_2'), $this->cms->templates->get_templates_select_data('panels'), NULL, TRUE);
        $this->admin->form->add_field('select', 'class', __('field_3'), $this->cms->libraries->get_libraries_select_data('panel_types'), NULL, TRUE);
        $this->admin->form->add_field('input', 'method', __('field_4'));
        $this->admin->form->add_field('input', 'parameters', __('field_16'));
        
        $this->admin->form->button_submit(__('button_3'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($panel_type_id = '')
    {
        if(!$this->s_panel_types_model->item_exists($panel_type_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['tpl'] = $this->input->post('tpl');
            $data['class'] = $this->input->post('class');
            $data['method'] = $this->input->post('method');
            $data['parameters'] = $this->input->post('parameters');
            
            $this->s_panel_types_model->set_item_data($panel_type_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_4'));
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->s_panel_types_model->$panel_type_id->name);
        $this->admin->form->add_field('select', 'tpl', __('field_2'), $this->cms->templates->get_templates_select_data('panels'), $this->s_panel_types_model->$panel_type_id->tpl, TRUE);
        $this->admin->form->add_field('select', 'class', __('field_3'), $this->cms->libraries->get_libraries_select_data('panel_types'), $this->s_panel_types_model->$panel_type_id->class, TRUE);
        $this->admin->form->add_field('input', 'method', __('field_4'), $this->s_panel_types_model->$panel_type_id->method);
        $this->admin->form->add_field('input', 'parameters', __('field_16'), $this->s_panel_types_model->$panel_type_id->parameters);
        
        $this->admin->form->tab(__('tab_5'));
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_5'));
        $this->admin->form->col(__('col_4'));
        
        $this->s_panel_type_variables_model->where('panel_type_id', '=', $panel_type_id);
        foreach($this->s_panel_type_variables_model->get_data() as $panel_type_variable)
        {
            $this->admin->form->cell_left(admin_anchor('~/edit_variable/' . $panel_type_variable->id, $panel_type_variable->title));
            $this->admin->form->cell($panel_type_variable->name);
            $this->admin->form->cell(admin_anchor('~/delete_variable/' . $panel_type_variable->id, __('button_2'), __('confirm_2')));

            $contextmenu = array();

            $contextmenu[] = array(__('button_4'), admin_url('~/edit_variable/' . $panel_type_variable->id), 'edit');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete_variable/' . $panel_type_variable->id), 'delete', __('confirm_2'));

            $this->admin->form->row($panel_type_variable->id, 0, $this->cms->model->system_table('panel_type_variables_' . $panel_type_id), TRUE, $contextmenu);
        }
        
        $this->admin->form->listing();
        
        $this->admin->form->button_submit(__('button_3'));
        $this->admin->form->button_submit(__('button_7'), 'accept', 'check');
        $this->admin->form->button_admin_link('~/add_variable/' . $panel_type_id . '#tab-1', __('button_5'), 'plus');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($panel_type_id = '')
    {
        if($this->s_panel_types_model->item_exists($panel_type_id))
        {
            $this->cms->model->load_user('panel_type_data_' . $panel_type_id, 'panel_type_data_X');
            $this->u_panel_type_data_X_model->drop();
            
            $this->s_panel_types_model->delete_item($panel_type_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    function export($panel_type_id = '')
    {
        if($this->s_panel_types_model->item_exists($panel_type_id))
        {
            $this->cms->export->panel_type($panel_type_id);
            $this->cms->export->download();
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation()
    {
        $required = (strlen($this->input->post('class')) > 0 || strlen($this->input->post('method')) > 0) ? '|required' : '';
        
        $this->admin->form->set_rules('name', __('field_1'), 'trim|required|max_length[255]');
        $this->admin->form->set_rules('tpl', __('field_2'), 'trim|required|max_length[255]|tpl[panels]');
        $this->admin->form->set_rules('class', __('field_3'), 'trim|library[panel_types]|max_length[255]' . $required);
        $this->admin->form->set_rules('method', __('field_4'), 'trim|max_length[255]' . $required);
        $this->admin->form->set_rules('parameters', __('field_16'), 'trim');
    }
    
    // Premenné typy panelov
    
    function add_variable($panel_type_id = '')
    {
        if(!$this->s_panel_types_model->item_exists($panel_type_id))
        {
            $this->admin->form->error(__('error_3'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_breadcrumb(array(
            'text' => $this->s_panel_types_model->$panel_type_id->name,
            'href' => admin_url('~/edit/' . $panel_type_id)
        ));
        
        $this->_validation_variable(TRUE, $panel_type_id);
        
        if($this->admin->form->validate())
        {
            /* Prijatie dát */
            
            $data = array();
            $data['panel_type_id'] = $panel_type_id;
            $data['title'] = $this->input->post('title');
            $data['name'] = $this->input->post('name');
            $data['info'] = $this->input->post('info');
            $data['add'] = $this->input->post('add');
            $data['edit'] = $this->input->post('edit');
            $data['field_type'] = $this->input->post('field_type');
            $data['rules'] = $this->input->post('rules');
            
            /* Vytvorenie stĺpca v tabuľke user_panel_type_data_X */
            
            // Získanie názu tabuľky user_panel_type_data_X
            $this->cms->model->load_user('panel_type_data_' . $panel_type_id, 'panel_type_data_X');

            $column_data = array();
            
            $column_data['type'] = $this->input->post('data_type');
            
            if(strlen($this->input->post('constraint')) > 0)
            $column_data['constraint'] = $this->input->post('constraint');
            
            if(is_form_true($this->input->post('unsigned')))
            $column_data['unsigned'] = TRUE;
            
            if(is_form_true($this->input->post('null')))
            $column_data['null'] = TRUE;
            
            $this->u_panel_type_data_X_model->add_column($data['name'], $column_data);

            /* Pridanie riadku do tabuľky system_panel_type_variables */
            $this->s_panel_type_variables_model->add_item($data);
            $this->admin->form->message(__('message_4'), TRUE);
            admin_redirect('~/edit/' . $panel_type_id . '#tab-2');
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', 'title', __('field_5'));
        $this->admin->form->add_field('input', 'name', __('field_6'));
        $this->admin->form->add_field('input', 'rules', __('field_15'));
        $this->admin->form->add_field('input', 'info', __('field_7'));
        
        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('select_dynamic_field', 'field_type', __('field_14'));
        $this->admin->form->add_field('checkbox', 'add', __('field_12'), TRUE);
        $this->admin->form->add_field('checkbox', 'edit', __('field_13'), TRUE);
        
        $this->admin->form->tab(__('tab_3'));
        $this->admin->form->add_field('select_data_type', 'data_type', __('field_8'), NULL, TRUE);
        $this->admin->form->info(__('info_1'));
        $this->admin->form->add_field('input', 'constraint', __('field_9'));
        $this->admin->form->add_field('checkbox', 'unsigned', __('field_10'));
        $this->admin->form->add_field('checkbox', 'null', __('field_11'), TRUE);
        
        $this->admin->form->button_submit(__('button_6'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit_variable($panel_type_variable_id = '')
    {
        if(!$this->s_panel_type_variables_model->item_exists($panel_type_variable_id))
        {
            $this->admin->form->error(__('error_4'), TRUE);
            admin_redirect();
        }
        
        $panel_type_id = $this->s_panel_type_variables_model->$panel_type_variable_id->panel_type_id;
        
        $this->admin->form->add_breadcrumb(array(
            'text' => $this->s_panel_types_model->$panel_type_id->name,
            'href' => admin_url('~/edit/' . $panel_type_id)
        ));
        
        $this->_validation_variable(FALSE, $this->s_panel_type_variables_model->$panel_type_variable_id->panel_type_id);
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['title'] = $this->input->post('title');
            //$data['name'] = $this->input->post('name');
            $data['info'] = $this->input->post('info');
            $data['add'] = $this->input->post('add');
            $data['edit'] = $this->input->post('edit');
            $data['field_type'] = $this->input->post('field_type');
            $data['rules'] = $this->input->post('rules');
            
            $this->s_panel_type_variables_model->set_item_data($panel_type_variable_id, $data);
            $this->admin->form->message(__('message_5'), url_param() != 'accept');
            
            if(url_param() != 'accept') admin_redirect('~/edit/' . $panel_type_id . '#tab-2');
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', 'title', __('field_5'), $this->s_panel_type_variables_model->$panel_type_variable_id->title);
        //$this->admin->form->add_field('input', 'name', __('field_6'), $this->s_panel_type_variables_model->$panel_type_variable_id->name);
        $this->admin->form->add_field('input', 'rules', __('field_15'), $this->s_panel_type_variables_model->$panel_type_variable_id->rules);
        $this->admin->form->add_field('input', 'info', __('field_7'), $this->s_panel_type_variables_model->$panel_type_variable_id->info);
        
        // TODO: dorobit editovanie datoveho typu premennej typu panela
        /*$this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('select_data_type', 'data_type', __('field_8'), NULL, TRUE);
        $this->admin->form->info(__('info_1'));
        $this->admin->form->add_field('input', 'constraint', __('field_9'));
        $this->admin->form->add_field('checkbox', 'unsigned', __('field_10'));
        $this->admin->form->add_field('checkbox', 'null', __('field_11'));*/
        
        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('select_dynamic_field', 'field_type', __('field_14'), $this->s_panel_type_variables_model->$panel_type_variable_id->field_type);
        $this->admin->form->add_field('checkbox', 'add', __('field_12'), $this->s_panel_type_variables_model->$panel_type_variable_id->add);
        $this->admin->form->add_field('checkbox', 'edit', __('field_13'), $this->s_panel_type_variables_model->$panel_type_variable_id->edit);
        
        $this->admin->form->button_submit(__('button_6'));
        $this->admin->form->button_submit(__('button_7'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete_variable($panel_type_variable_id = '')
    {
        $redirect = '';
        
        if($this->s_panel_type_variables_model->item_exists($panel_type_variable_id))
        {
            $redirect = '~/edit/' . $this->s_panel_type_variables_model->$panel_type_variable_id->panel_type_id . '#tab-2';
            
            // Odstránenie stĺpca z tabuľky user_panel_type_data_X
            $this->cms->model->load_user('panel_type_data_' . $this->s_panel_type_variables_model->$panel_type_variable_id->panel_type_id, 'panel_type_data_X');
            $this->u_panel_type_data_X_model->show_errors = FALSE;
            $this->u_panel_type_data_X_model->drop_column($this->s_panel_type_variables_model->$panel_type_variable_id->name);
            $this->u_panel_type_data_X_model->show_errors = TRUE;
            
            // Odstránenie riadku z tabuľky system_panel_variables
            $this->s_panel_type_variables_model->delete_item($panel_type_variable_id);
            $this->admin->form->message(__('message_6'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_5'), TRUE);
        }
        admin_redirect($redirect);
    }
    
    protected function _validation_variable($validate_data_type = FALSE, $panel_type_id = '')
    {
        $this->admin->form->set_rules('title', __('field_5'), 'trim|required|max_length[255]');
        if($validate_data_type) $this->admin->form->set_rules('name', __('field_5'), 'trim|required|max_length[50]|unmatch_column_user[panel_type_data_' . $panel_type_id . ']');
        $this->admin->form->set_rules('info', __('field_7'), 'trim|max_length[255]');
        if($validate_data_type) $this->admin->form->set_rules('data_type', __('field_8'), 'trim|required|data_type');
        if($validate_data_type) $this->admin->form->set_rules('constraint', __('field_9'), 'trim');
        if($validate_data_type) $this->admin->form->set_rules('unsigned', __('field_10'), 'trim');
        if($validate_data_type) $this->admin->form->set_rules('null', __('field_11'), 'trim');
        $this->admin->form->set_rules('add', __('field_12'), 'trim');
        $this->admin->form->set_rules('edit', __('field_13'), 'trim');
        $this->admin->form->set_rules('field_type', __('field_14'), 'trim|required|dynamic_or_referring_field');
        $this->admin->form->set_rules('rules', __('field_15'), 'trim');
    }
    
}