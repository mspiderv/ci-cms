<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class List_types extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
        
        $this->load->driver('cms');
        $this->cms->set_constants();
        $this->cms->model->autoload();
        $this->load->driver('admin');
        $this->admin->auth->check_access();
        $this->cms->model->load_system('list_types');
        $this->cms->model->load_system('list_type_variables');
    }
    
    function index()
    {
        $this->admin->form->button_admin_link('~/add', __('button_1'), 'plus');
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_2'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        
        foreach($this->s_list_types_model->get_data() as $list_type)
        {
            $options_cell = '';
            $options_cell .= admin_anchor('~/add_variable/' . $list_type->id . '#tab-1', __('button_5'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/delete/' . $list_type->id, __('button_2'), __('confirm_1'));
            $options_cell .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            $options_cell .= admin_anchor('~/export/' . $list_type->id, __('button_8'));
            
            $this->admin->form->cell_left(admin_anchor('~/edit/' . $list_type->id . '#tab-1', $list_type->name));
            $this->admin->form->cell();
            $this->admin->form->cell();
            $this->admin->form->cell($options_cell);

            $contextmenu = array();
            
            $contextmenu[] = array(__('button_4'), admin_url('~/edit/' . $list_type->id . '#tab-1'), 'edit');
            $contextmenu[] = array(__('button_5'), admin_url('~/add_variable/' . $list_type->id . '#tab-1'), 'add');
            $contextmenu[] = array(__('button_8'), admin_url('~/export/' . $list_type->id), 'export');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete/' . $list_type->id), 'delete', __('confirm_1'));
            
            $this->admin->form->row($list_type->id, 0, $this->cms->model->system_table('list_types'), NULL, $contextmenu);
            
            // List type variables
            $this->s_list_type_variables_model->where('list_type_id', '=', $list_type->id);
            foreach($this->s_list_type_variables_model->get_data() as $list_type_variable)
            {
                $is_primary_variable = ($list_type->primary_variable_id == $list_type_variable->id);
                
                $this->admin->form->cell_left(admin_anchor('~/edit_variable/' . $list_type_variable->id, $list_type_variable->title));
                $this->admin->form->cell($list_type_variable->name);
                $this->admin->form->cell($this->admin->form->cell_radio(($is_primary_variable) ? '~/unset_primary_variable/' . $list_type->id : '~/set_primary_variable_id/' . $list_type_variable->id, ($is_primary_variable) ? __('button_10') : __('button_9'), ($is_primary_variable)));
                $this->admin->form->cell(admin_anchor('~/delete_variable/' . $list_type_variable->id, __('button_2'), __('confirm_2')));

                $contextmenu = array();

                $contextmenu[] = array(__('button_4'), admin_url('~/edit_variable/' . $list_type_variable->id), 'edit');
                $contextmenu[] = array(__('button_2'), admin_url('~/delete_variable/' . $list_type_variable->id), 'delete', __('confirm_2'));

                $this->admin->form->row($list_type_variable->id, 1, $this->cms->model->system_table('list_type_variables_' . $list_type->id), TRUE, $contextmenu);
            }
        }
        
        $this->admin->form->button_helper(__('helper_1'));
        
        $this->admin->form->generate();
    }
    
    function set_primary_variable_id($list_type_variable_id = '')
    {
        $redirect = '';
        
        if($this->s_list_type_variables_model->item_exists($list_type_variable_id))
        {
            $list_type_id = $this->s_list_type_variables_model->$list_type_variable_id->list_type_id;
            $redirect = '~/edit/' . $list_type_id . '#tab-2';
            $this->s_list_types_model->set_item_data($list_type_id, array('primary_variable_id' => $list_type_variable_id));
            $this->admin->form->message(__('message_7'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_6'), TRUE);
        }
        admin_redirect($redirect);
    }
    
    function unset_primary_variable($list_type_id = '')
    {
        $redirect = '';
        
        if($this->s_list_types_model->item_exists($list_type_id))
        {
            $redirect = '~/edit/' . $list_type_id . '#tab-2';
            $this->s_list_types_model->set_item_data($list_type_id, array('primary_variable_id' => NULL));
            $this->admin->form->message(__('message_8'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_7'), TRUE);
        }
        admin_redirect($redirect);
    }
    
    // Typy zoznamov
    
    function add()
    {
        $this->_validation();
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            
            $this->s_list_types_model->add_item($data);
            
            /* Vytvorenie novej tabuľky (user_list_type_data_X) */
            
            // Načíta dbforge
            $this->load->dbforge();
            
            // Názov novej tabuľky
            $new_table_name = $this->cms->model->user_table('list_type_data_' . $this->s_list_types_model->insert_id());
            
            // Názov tabuľky zoznamov
            $lists_table_name = $this->db->dbprefix . $this->cms->model->system_table('lists');
            
            // Polia novej tabuľky
            $new_table_fields = array(
                cfg('table_cols', 'id') => array(
                    'type' => 'SMALLINT',
                    'constraint' => 5, 
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ),
                'order' => array(
                    'type' => 'SMALLINT',
                    'constraint' => 5, 
                    'unsigned' => TRUE
                ),
                'list_id' => array(
                    'type' => 'SMALLINT',
                    'constraint' => 5, 
                    'unsigned' => TRUE
                ),
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255, 
                    'null' => TRUE
                ),
                'public' => array(
                    'type' => 'BOOLEAN',
                    'default' => 1
                )
            );
            
            // Pridanie polí novej tabuľke
            $this->dbforge->add_field($new_table_fields);
            
            // Stĺpcu ID nastavíme primárky kľúč 
            $this->dbforge->add_key(cfg('table_cols', 'id'), TRUE);
            
            // Stĺpcu list_id nastavíme kľúč 
            $this->dbforge->add_key('list_id');
            
            // Vytvorenie tabuľky
            $this->dbforge->create_table($new_table_name);
            
            // Typ uloženia zmeníme na INNODB
            $this->db->query('ALTER TABLE `' . $this->db->dbprefix . $new_table_name. '` ENGINE = INNODB');
            
            // Stĺpec list_id previažeme so stĺpcom ID v tabuľke 'system_lists' -> keď zmažem nejaký zoznam aby sa mi zmazal aj príslušný riadok v tejto tabuľke
            $this->db->query('ALTER TABLE `' . $this->db->dbprefix . $new_table_name . '` ADD FOREIGN KEY (`list_id`) REFERENCES  `' . $lists_table_name . '` (`' . cfg('table_cols', 'id') . '`) ON DELETE CASCADE ON UPDATE CASCADE');
            
            // Načíta cache driver
            $this->load->driver('cache', array('adapter' => 'file'));
            
            // Vymaže zoznam tabuliek z cache
            $this->cache->delete('list_tables');
            
            $this->admin->form->message(__('message_1'), TRUE);

            admin_redirect();
        }
        
        $this->admin->form->add_field('input', 'name', __('field_1'));
        
        $this->admin->form->button_submit(__('button_3'));
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function edit($list_type_id = '')
    {
        if(!$this->s_list_types_model->item_exists($list_type_id))
        {
            $this->admin->form->error(__('error_1'), TRUE);
            admin_redirect();
        }
        
        $this->_validation($list_type_id);
        
        if($this->admin->form->validate())
        {
            $data = array();
            
            $data['name'] = $this->input->post('name');
            $data['primary_variable_id'] = $this->input->post('primary_variable_id');
            
            $this->s_list_types_model->set_item_data($list_type_id, $data);
            $this->admin->form->message(__('message_2'), url_param() != 'accept');
            
            if(url_param() != 'accept') admin_redirect();
        }
        
        $this->admin->form->tab(__('tab_4'));
        $this->admin->form->add_field('input', 'name', __('field_1'), $this->s_list_types_model->$list_type_id->name);
        $this->admin->form->add_field('select', 'primary_variable_id', __('field_2'), $this->cms->lists->get_list_type_variable_names($list_type_id, 'both'), $this->s_list_types_model->$list_type_id->primary_variable_id, TRUE);
        
        $this->admin->form->tab(__('tab_5'));
        
        $this->admin->form->col(__('col_1'));
        $this->admin->form->col(__('col_5'));
        $this->admin->form->col(__('col_3'));
        $this->admin->form->col(__('col_4'));
        
        $this->s_list_type_variables_model->where('list_type_id', '=', $list_type_id);
        foreach($this->s_list_type_variables_model->get_data() as $list_type_variable)
        {
            $is_primary_variable = ($this->s_list_types_model->$list_type_id->primary_variable_id == $list_type_variable->id);
            
            $this->admin->form->cell_left(admin_anchor('~/edit_variable/' . $list_type_variable->id, $list_type_variable->title));
            $this->admin->form->cell($list_type_variable->name);
            $this->admin->form->cell($this->admin->form->cell_radio(($is_primary_variable) ? '~/unset_primary_variable/' . $list_type_id : '~/set_primary_variable_id/' . $list_type_variable->id, ($is_primary_variable) ? __('button_10') : __('button_9'), ($is_primary_variable)));
            $this->admin->form->cell(admin_anchor('~/delete_variable/' . $list_type_variable->id, __('button_2'), __('confirm_2')));

            $contextmenu = array();

            $contextmenu[] = array(__('button_4'), admin_url('~/edit_variable/' . $list_type_variable->id), 'edit');
            $contextmenu[] = array(__('button_2'), admin_url('~/delete_variable/' . $list_type_variable->id), 'delete', __('confirm_2'));

            $this->admin->form->row($list_type_variable->id, 0, $this->cms->model->system_table('list_type_variables_' . $list_type_id), TRUE, $contextmenu);
        }
        
        $this->admin->form->listing();
        
        $this->admin->form->button_submit(__('button_3'));
        $this->admin->form->button_submit(__('button_7'), 'accept', 'check');
        $this->admin->form->button_admin_link('~/add_variable/' . $list_type_id . '#tab-1', __('button_5'), 'plus');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete($list_type_id = '')
    {
        if($this->s_list_types_model->item_exists($list_type_id))
        {
            $this->cms->model->load_user('list_type_data_' . $list_type_id, 'list_type_data_X');
            $this->u_list_type_data_X_model->drop();
            
            $this->s_list_types_model->delete_item($list_type_id);
            $this->admin->form->message(__('message_3'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_2'), TRUE);
        }
        admin_redirect();
    }
    
    function export($list_type_id = '')
    {
        $this->admin->form->warning('Typy zoznamov zatiaľ nie je možné exportovať.', TRUE);
        admin_redirect();
        
        if($this->s_list_types_model->item_exists($list_type_id))
        {
            $this->cms->export->list_type($list_type_id);
            $this->cms->export->download();
        }
        else
        {
            $this->admin->form->error(__('error_3'), TRUE);
        }
        admin_redirect();
    }
    
    protected function _validation($list_type_id = '')
    {
        $this->admin->form->set_rules('name', __('field_1'), 'trim|required|max_length[255]');
        if(intval($list_type_id) > 0) $this->admin->form->set_rules('primary_variable_id', __('field_2'), 'trim|list_type_variable_id[' . $list_type_id . ']');
    }
    
    // Premenné typy zoznamov
    
    function add_variable($list_type_id = '')
    {
        if(!$this->s_list_types_model->item_exists($list_type_id))
        {
            $this->admin->form->error(__('error_3'), TRUE);
            admin_redirect();
        }
        
        $this->admin->form->add_breadcrumb(array(
            'text' => $this->s_list_types_model->$list_type_id->name,
            'href' => admin_url('~/edit/' . $list_type_id)
        ));
        
        $this->_validation_variable(TRUE, $list_type_id);
        
        if($this->admin->form->validate())
        {
            /* Prijatie dát */
            
            $data = array();
            $data['list_type_id'] = $list_type_id;
            $data['title'] = $this->input->post('title');
            $data['name'] = $this->input->post('name');
            $data['info'] = $this->input->post('info');
            $data['add'] = $this->input->post('add');
            $data['edit'] = $this->input->post('edit');
            $data['field_type'] = $this->input->post('field_type');
            $data['rules'] = $this->input->post('rules');
            
            /* Vytvorenie stĺpca v tabuľke user_list_type_data_X */
            
            // Získanie názu tabuľky user_list_type_data_X
            $this->cms->model->load_user('list_type_data_' . $list_type_id, 'list_type_data_X');

            $column_data = array();
            
            $column_data['type'] = $this->input->post('data_type');
            
            if(strlen($this->input->post('constraint')) > 0)
            $column_data['constraint'] = $this->input->post('constraint');
            
            if(is_form_true($this->input->post('unsigned')))
            $column_data['unsigned'] = TRUE;
            
            if(is_form_true($this->input->post('null')))
            $column_data['null'] = TRUE;
            
            $this->u_list_type_data_X_model->add_column($data['name'], $column_data);

            /* Pridanie riadku do tabuľky system_list_type_variables */
            $this->s_list_type_variables_model->add_item($data);
            $this->admin->form->message(__('message_4'), TRUE);
            admin_redirect('~/edit/' . $list_type_id . '#tab-2');
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
    
    function edit_variable($list_type_variable_id = '')
    {
        if(!$this->s_list_type_variables_model->item_exists($list_type_variable_id))
        {
            $this->admin->form->error(__('error_4'), TRUE);
            admin_redirect();
        }
        
        $list_type_id = $this->s_list_type_variables_model->$list_type_variable_id->list_type_id;
        
        $this->admin->form->add_breadcrumb(array(
            'text' => $this->s_list_types_model->$list_type_id->name,
            'href' => admin_url('~/edit/' . $list_type_id)
        ));
        
        $this->_validation_variable(FALSE, $this->s_list_type_variables_model->$list_type_variable_id->list_type_id);
        
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
            
            $this->s_list_type_variables_model->set_item_data($list_type_variable_id, $data);
            $this->admin->form->message(__('message_5'), url_param() != 'accept');
            
            if(url_param() != 'accept') admin_redirect('~/edit/' . $list_type_id . '#tab-2');
        }
        
        $this->admin->form->tab(__('tab_1'));
        $this->admin->form->add_field('input', 'title', __('field_5'), $this->s_list_type_variables_model->$list_type_variable_id->title);
        //$this->admin->form->add_field('input', 'name', __('field_6'), $this->s_list_type_variables_model->$list_type_variable_id->name);
        $this->admin->form->add_field('input', 'rules', __('field_15'), $this->s_list_type_variables_model->$list_type_variable_id->rules);
        $this->admin->form->add_field('input', 'info', __('field_7'), $this->s_list_type_variables_model->$list_type_variable_id->info);
        
        // TODO: dorobit editovanie datoveho typu premennej typu zoznamu
        /*$this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('select_data_type', 'data_type', __('field_8'), NULL, TRUE);
        $this->admin->form->info(__('info_1'));
        $this->admin->form->add_field('input', 'constraint', __('field_9'));
        $this->admin->form->add_field('checkbox', 'unsigned', __('field_10'));
        $this->admin->form->add_field('checkbox', 'null', __('field_11'));*/
        
        $this->admin->form->tab(__('tab_2'));
        $this->admin->form->add_field('select_dynamic_field', 'field_type', __('field_14'), $this->s_list_type_variables_model->$list_type_variable_id->field_type);
        $this->admin->form->add_field('checkbox', 'add', __('field_12'), $this->s_list_type_variables_model->$list_type_variable_id->add);
        $this->admin->form->add_field('checkbox', 'edit', __('field_13'), $this->s_list_type_variables_model->$list_type_variable_id->edit);
        
        $this->admin->form->button_submit(__('button_6'));
        $this->admin->form->button_submit(__('button_7'), 'accept', 'check');
        $this->admin->form->button_index();
        
        $this->admin->form->generate();
    }
    
    function delete_variable($list_type_variable_id = '')
    {
        $redirect = '';
        
        if($this->s_list_type_variables_model->item_exists($list_type_variable_id))
        {   
            $redirect = '~/edit/' . $this->s_list_type_variables_model->$list_type_variable_id->list_type_id . '#tab-2';
            
            // Odstránenie stĺpca z tabuľky user_list_type_data_X
            $this->cms->model->load_user('list_type_data_' . $this->s_list_type_variables_model->$list_type_variable_id->list_type_id, 'list_type_data_X');
            $this->u_list_type_data_X_model->show_errors = FALSE;
            $this->u_list_type_data_X_model->drop_column($this->s_list_type_variables_model->$list_type_variable_id->name);
            $this->u_list_type_data_X_model->show_errors = TRUE;
            
            // Odstránenie riadku z tabuľky system_list_variables
            $this->s_list_type_variables_model->delete_item($list_type_variable_id);
            $this->admin->form->message(__('message_6'), TRUE);
        }
        else
        {
            $this->admin->form->error(__('error_5'), TRUE);
        }
        admin_redirect($redirect);
    }
    
    protected function _validation_variable($validate_data_type = FALSE, $list_type_id = '')
    {
        $this->admin->form->set_rules('title', __('field_5'), 'trim|required|max_length[255]');
        if($validate_data_type) $this->admin->form->set_rules('name', __('field_5'), 'trim|required|max_length[50]|reserved[name,list_id]|unmatch_column_user[list_type_data_' . $list_type_id . ']');
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