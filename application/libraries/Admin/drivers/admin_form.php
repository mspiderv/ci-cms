<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_form extends CI_Driver {

    /* Protected */
    // CI Instance
    protected $CI;
    
    // General
    protected $content = '';
    protected $status = array();
    protected $buffering = FALSE;
    protected $buffer = '';

    // Tabs
    protected $tabs = array();
    protected $tab_id = 0;
    
    // Fields
    protected $field_id = 0;
    protected $field_info = '';
    protected $field_values = array();
    protected $field_return = FALSE;
    protected $field_return_row = FALSE;
    protected $field_class = '';
    protected $field_class_still = FALSE;
    protected $field_params;
    
    // Listing
    protected $listing = array();
    public $listing_level_col = 1;
    public $listing_sortable = TRUE;
    
    // Contextmenu
    protected $contextmenu = array();
    
    // Categorizing
    protected $categorizing = array();
    
    // Widgets
    protected $widgets_id = 0;
    protected $widgets = array();

    // Charts
    protected $charts = array();
    protected $chart_id = 1;

    // Buttons
    protected $buttons = '';
    protected $buttons_generated = FALSE;

    // Messages, warnings, errors
    protected $messages = array();
    protected $warnings = array();
    protected $errors = array();
    
    // Breadcrumbs
    public $generate_breadcrumbs = TRUE;
    public $breadcrumbs = NULL;
    public $middle_breadcrumbs = array();

    // Filters
    protected $filters = array();

    // Admin forms
    protected $admin_forms = array();
    protected $admin_form_cache_file_prefix = 'admin_form_';
    protected $admin_form_caching = FALSE;
    
    // Index button
    public $generate_index_button = TRUE;
    protected $index_button_generated = FALSE;
    
    // Form validation
    protected $validation_rules = array();
    
    // Generating
    protected $generated = FALSE;
    
    /* Public */    
    public $variables = array();
    public $hidden_fields = array();
    public $main_view = 'main';
    public $title = '';
    public $top = TRUE;
    public $menu = TRUE;
    public $field_id_prefix = '';
    
    /* Class methods */
    
    function __construct()
    {
        $this->CI =& get_instance();
        
        $this->CI->load->driver('cache', array('adapter' => 'file'));
        
        $this->CI->load->load_interface('formfield');
        $this->CI->load->load_interface('formfielddynamic');
        $this->CI->load->load_class('formfield');
        
        if(strlen($this->CI->input->get('iframe')) > 0 && $this->CI->input->get('iframe') == cfg('url', 'true'))
        {
            $this->top = FALSE;
            $this->menu = FALSE;
        }
        
        // Field id
        $field_id = (int)$this->CI->input->get_post('field_id');
        if($field_id > 0) $this->field_id = $field_id;
        
        // Hidden fields
        $this->hidden_fields = array(cfg('form', 'sent') => cfg('form', 'true'));
        if(form_sent()) $this->hidden_fields[cfg('form', 'sent')] = cfg('form', 'true');
        
        // Title tag
        if($this->CI->router->fetch_method() != 'index') $this->title = ll($this->CI->router->fetch_directory() . $this->CI->router->fetch_class() . '_title_' . $this->CI->router->fetch_method());
        else $this->title = ll($this->CI->router->fetch_directory() . $this->CI->router->fetch_class() . '_title');
        
        // Tabs
        $this->_init_tabs();
        
        // Admin forms
        $this->_forms();
    }
    
    function __get($var)
    {
        return $this->CI->$var;
    }
    
    /* General */
    
    protected function _add_content($content = '')
    {
        if($this->_get_status('tab'))
        {
            // Add content to tab
            $this->_add_tab_content($content);
        }
        
        else
        {
            // Add content to main content
            $this->_add_main_content($content);
        }
    }
    
    protected function _add_main_content($content = '')
    {
        if($this->buffering)
        {
            $this->buffer .= $content;
        }
        
        $this->content .= $content;
    }
    
    function load_part($part = '', $data = array())
    {
        $this->_add_content($this->load_view('parts/' . $part, $data, TRUE));
    }
    
    protected function _set_status($item, $status)
    {
        $this->status[$item] = (bool)$status;
    }
    
    protected function _get_status($item)
    {
        return (bool) @$this->status[$item];
    }
    
    function buffering_start()
    {
        $this->buffering = TRUE;
    }
    
    function buffering_stop()
    {
        $this->buffering = TRUE;
    }
    
    function get_buffer_content($stop_buffering = TRUE)
    {
        $buffer = $this->buffer;
        $this->buffer = '';
        if($stop_buffering) $this->buffering_stop();
        return $buffer;
    }
    
    function include_view($view = '', $data = array())
    {
        $this->_add_content($this->load_view($view, $data, TRUE));
    }
    
    /* Tabs */
    
    protected function _init_tabs()
    {
        $this->_set_status('tab', FALSE);
        $this->tabs = array();
        $this->tab_id = 0;
    }
    
    function tab($tab_name = '')
    {
        // Try to close opened fields table
        $this->close_fields_table();
        
        // Try to close listing
        $this->close_listing();
        
        // if(strlen($tab_name) == 0) show_error("Metóda <strong>tab</strong> prijala neočakávaný parameter.");
        if(strlen($tab_name) == 0) $tab_name = '-';
            
        $this->_set_status('tab', TRUE);
        
        $this->tab_id++;
        
        $this->tabs[$this->tab_id]['name'] = $tab_name;
        $this->tabs[$this->tab_id]['content'] = '';
    }
    
    function parse_tabs()
    {
        $this->close_wrap();
        
        if($this->_get_status('tab'))
        {
            // Try to close opened fields table
            $this->close_fields_table();
            
            // Try to close listing
            $this->close_listing();

            // Add tabs content to main content
            $this->_add_main_content($this->load_view('general/tabs', array('tabs' => $this->tabs), TRUE));

            // Reinit
            $this->_init_tabs();
        }
    }
    
    protected function _add_tab_content($content = '')
    {
        if($this->buffering)
        {
            $this->buffer .= $content;
        }
        
        $this->tabs[$this->tab_id]['content'] .= $content;
    }
    
    /* Wraps */
    
    protected function _open_wrap()
    {
        if($this->_get_status('wrap')) $this->close_wrap();
        
        $this->_set_status('wrap', TRUE);
        $this->close_fields_table();
        $this->_add_main_content($this->load_view('wrap/open', NULL, TRUE));
    }
    
    function close_wrap()
    {
        if($this->_get_status('wrap'))
        {
            $this->close_fields_table();
            $this->_set_status('wrap', FALSE);
            $this->_add_main_content($this->load_view('wrap/close', NULL, TRUE));
        }
    }
    
    /* Fields */
    
    function add_field()
    {
        $args = func_get_args();
        
        $field_data = $this->_parse_field_type_name($args[0]);
        
        $args[0] = $field_data['field'];
        $this->field_params = $field_data['params'];
        
        $this->open_fields_table();
        $this->load_field_class($args[0]);
        $this->get_new_field_id();
        
        // $field_object = new $field($param_1, $param_2, $param_3, $param_4, $param_5, $param_6, $param_7);
        
        $reflection = new ReflectionClass($args[0] . 'Field');
        $field_object = $reflection->newInstanceArgs(array_slice($args, 1));
        
        if(!$field_object instanceof IFormField) show_error('Trieda poľa <strong>' . $args[0] . '</strong> musí implementovať rozhranie <strong>IFormField</strong>.');
        
        if($this->field_return)
        {
            $this->field_return = FALSE;
            return $field_object->get_field();
        }
        
        else
        {
            $field_data = array();

            $field_data['multilingual'] = $field_object->is_multilingual();
            $field_data['title'] = $field_object->get_title();
            $field_data['field'] = $field_object->get_field();
            $field_data['info'] = $field_object->get_info();
            $field_data['error'] = $field_object->get_error();
            $field_data['field_id'] = $this->get_field_id();
            $field_data['class'] = $this->_get_field_class();
            $field_data['label'] = @$this->validation_rules[@$args[1]]['label'];
            $field_data['rules'] = @$this->validation_rules[@$args[1]]['rules'];
            
            $field_row = $this->load_view('fields/general/' . $field_object->get_field_tpl(), $field_data, TRUE);
            
            if($this->field_return_row)
            {
                $this->field_return_row = FALSE;
                return $field_row;
            }
            else
            {
                $this->_add_content($field_row);
            }
            
        }
    }
    
    function add_dynamic_field($field = '', $name = '', $title = '', $value = '')
    {
        $field_data = $this->_parse_field_type_name($field);
        
        $field = $field_data['field'];
        $this->field_params = $field_data['params'];
        
        $this->open_fields_table();
        $this->load_field_class($field);
        $this->get_new_field_id();
        
        $reflection = new ReflectionClass($field . 'Field');
        $field_object = $reflection->newInstanceArgs();
        $field_object->set($name, $title, $value);
        
        if(!$field_object instanceof IFormField) show_error('Trieda poľa <strong>' . $field . '</strong> musí implementovať rozhranie <strong>IFormField</strong>.');
        
        if($this->field_return)
        {
            $this->field_return = FALSE;
            return $field_object->get_field();
        }
        
        else
        {
            $field_data = array();

            $field_data['multilingual'] = $field_object->is_multilingual();
            $field_data['title'] = $field_object->get_title();
            $field_data['field'] = $field_object->get_field();
            $field_data['info'] = $field_object->get_info();
            $field_data['error'] = $field_object->get_error();
            $field_data['field_id'] = $this->get_field_id();
            $field_data['class'] = $this->_get_field_class();
            $field_data['label'] = @$this->validation_rules[$name]['label'];
            $field_data['rules'] = @$this->validation_rules[$name]['rules'];
            
            $field_row = $this->load_view('fields/general/' . $field_object->get_field_tpl(), $field_data, TRUE);
            
            if($this->field_return_row)
            {
                $this->field_return_row = FALSE;
                return $field_row;
            }
            else
            {
                $this->_add_content($field_row);
            }
            
        }
    }
    
    protected function _parse_field_type_name($field = '')
    {
        $params = array();
        
        $pos = strpos($field, '[');
        
        if($pos > -1)
        {
            $params = explode('|', substr($field, $pos+1, -1));
            $field = substr($field, 0, $pos);
        }
        
        return array('field' => $field, 'params' => $params);
    }
    
    function get_field_params()
    {
        return $this->field_params;
    }
    
    function set_field_class($field_class = '', $still = FALSE)
    {
        if(is_array($field_class)) $field_class = implode(' ', $field_class);
        $this->field_class = $field_class;
        $this->field_class_still = (bool)$still;
    }
    
    function _get_field_class()
    {
        $field_class = $this->field_class;
        if(!$this->field_class_still) $this->field_class = '';
        return $field_class;
    }
    
    function get_field()
    {
        $this->field_return = TRUE;
        return call_user_func_array(array($this, 'add_field'), func_get_args());
    }
    
    function get_field_row()
    {
        $this->field_return_row = TRUE;
        return call_user_func_array(array($this, 'add_field'), func_get_args());
    }
    
    function get_field_id($only_number = FALSE)
    {
        return ($only_number) ? ($this->field_id_prefix . $this->field_id) : ('field_' . $this->field_id_prefix . $this->field_id);
    }
    
    function get_new_field_id($only_number = FALSE)
    {
        $this->field_id++;
        return $this->get_field_id($only_number);
    }
    
    function info($info = '')
    {
        $this->field_info = $info;
    }
    
    function get_info()
    {
        $info = $this->field_info;
        $this->field_info = '';
        return $info;
    }
    
    function get_error($field_name)
    {
        return form_error($field_name, '<p class="error">', '</p>');
    }
    
    function load_field_class($field)
    {
        $field = strtolower($field);
        if(!@include_once './' . APPPATH . 'fields/' . $field . EXT) show_error('Triedu poľa <strong>' . $field . '</strong> sa nepodarilo načítať, pretože neexistuje.');
    }

    function open_fields_table()
    {
        if(!$this->_get_status('fields_table'))
        {
            if(!$this->_get_status('tab')) $this->_open_wrap();
            
            $this->_add_content($this->load_view('fields/general/table_open', array(), TRUE));
            $this->_set_status('fields_table', TRUE);
        }
    }
    
    function close_fields_table()
    {
        if($this->_get_status('fields_table'))
        {
            $this->_add_content($this->load_view('fields/general/table_close', array(), TRUE));
            $this->_set_status('fields_table', FALSE);
        }
    }
    
    function set_field_value($field_name, $field_value)
    {
        $this->field_values[$field_name] = $field_value;
    }
    
    function get_field_value($field_name)
    {
        return @$this->field_values[$field_name];
    }
    
    function is_iframe()
    {
        return ($this->input->get(cfg('url', 'iframe')) == cfg('form', 'true'));
    }
    
    /* Listing */
    
    protected function _open_listing()
    {
        if(!$this->_get_status('listing'))
        {
            $this->_add_content($this->load_view('listing/general/open', array(), TRUE));
            $this->_set_status('listing', TRUE);
        }
    }
    
    function close_listing()
    {
        if($this->_get_status('listing'))
        {
            $this->_add_content($this->load_view('listing/general/close', array(), TRUE));
            $this->_set_status('listing', FALSE);
        }
    }
    
    function clear_listing()
    {
        $this->listing = array();
    }
    
    function col($title, $help = '', $width = '')
    {
        if(is_int($help))
        {
            $width = $help;
            $help = '';
        }
        
        @$this->listing['cols'][] = array(
            'title' => $title,
            'help'  => $help,
            'width' => $width
        );
    }
    
    function cell($content = '', $classes = array())
    {
        @$this->listing['cells'][] = array(
            'content' => $content,
            'classes' => (is_array($classes)) ? implode(' ', $classes) : $classes
        );
    }
    
    function cell_left($content = '', $classes = array())
    {
        $classes[] = 'align_left';
        return $this->cell($content, $classes);
    }
    
    function cell_right($content = '', $classes = array())
    {
        $classes[] = 'align_right';
        return $this->cell($content, $classes);
    }
    
    function row($id = '', $level = 0, $sortgroup = '', $sortgroup_categorizing = FALSE, $contextmenu = array(), $classes = array())
    {
        // Try to add missing cells
        while(count(@$this->listing['cols']) > count(@$this->listing['cells'])) $this->cell('');
        
        foreach($contextmenu as $contextmenu_item)
        {
            $this->_contextmenu($sortgroup . '_' . $id, $contextmenu_item[0], $contextmenu_item[1], $contextmenu_item[2], @$contextmenu_item[3]);
        }
        
        if(@count($contextmenu))
        {
            $classes = (is_array($classes)) ? implode(' ', $classes) : $classes;
            $classes = (strlen($classes)) ? $classes .= ' contextmenu' : $classes = 'contextmenu';
        }
        
        @$this->listing['rows'][] = array(
            'cells' => @$this->listing['cells'],
            'level' => $level,
            'sortgroup' => $sortgroup,
            'sortgroup_categorizing' => $sortgroup_categorizing,
            'id' => $id,
            'contextmenu' => (@count($contextmenu)),
            'classes' => (is_array($classes)) ? implode(' ', $classes) : $classes
        );
        
        @$this->listing['cells'] = array();
    }
    
    function listing($classes = array(), $get = FALSE)
    {
        @$this->listing['cells'] = (array)@$this->listing['cells'];
        @$this->listing['rows'] = (array)@$this->listing['rows'];
        @$this->listing['classes'] = $classes;
        @$this->listing['listing_level_col'] = $this->listing_level_col;
        $this->listing['sortable'] = $this->listing_sortable;
        
        if($get)
        {
            $content = $this->load_view('listing/listing', $this->listing, TRUE);
            $this->clear_listing();
            return $content;
        }
        else
        {
            $this->_add_content($this->load_view('listing/listing', $this->listing, TRUE));
        }
        
        $this->clear_listing();
    }
    
    /* Listing tools */
    
    function cell_button($href, $title, $icon, $confirm = '')
    {
        $data_array = @explode('|', $confirm, 2);

        $data_text = @$data_array[0];
        $data_title = @$data_array[1];
        
        if(strlen($data_title) == 0)
        {
            $data_title = ll('admin_general_warning');
        }
        
        $data = array();
        
        $data['href'] = admin_url($href);
        $data['title'] = $title;
        $data['icon'] = $icon;
        $data['data_text'] = $data_text;
        $data['data_title'] = $data_title;
        
        return $this->load_view('listing/tools/button', $data, TRUE);
    }
    
    function cell_radio($url = '', $title = '', $checked = FALSE, $confirm = '')
    {
        $data_array = @explode('|', $confirm, 2);

        $data_text = @$data_array[0];
        $data_title = @$data_array[1];
        
        if(strlen($data_title) == 0)
        {
            $data_title = ll('admin_general_warning');
        }
        
        $data = array();
        
        $data['url'] = admin_url($url);
        $data['title'] = $title;
        $data['checked'] = $checked;
        $data['data_text'] = $data_text;
        $data['data_title'] = $data_title;
        
        return $this->load_view('listing/tools/radio', $data, TRUE);
    }
    
    function cell_checkbox($url = '', $title = '', $checked = FALSE, $confirm = '')
    {
        $data_array = @explode('|', $confirm, 2);

        $data_text = @$data_array[0];
        $data_title = @$data_array[1];
        
        if(strlen($data_title) == 0)
        {
            $data_title = ll('admin_general_warning');
        }
        
        $data = array();
        
        $data['url'] = admin_url($url);
        $data['title'] = $title;
        $data['checked'] = $checked;
        $data['data_text'] = $data_text;
        $data['data_title'] = $data_title;
        
        return $this->load_view('listing/tools/checkbox', $data, TRUE);
    }
    
    function cell_thumbnail($text = '', $image_url = '', $max_width = 200, $max_height = 200)
    {
        $data = array();
        
        $data['text'] = $text;
        $data['image_url'] = $image_url;
        $data['max_width'] = intval($max_width);
        $data['max_height'] = intval($max_height);
        
        return $this->load_view('listing/tools/thumbnail', $data, TRUE);
    }
    
    function cell_indicator($value = 0, $full = 0)
    {
        $data = array();
        
        $data['percent'] = 100 / ($full / $value);
        $data['title'] = $value . ' / ' . $full . ' = ' . round($data['percent'], 2) . '%';
        
        return $this->load_view('listing/tools/indicator', $data, TRUE);
    }
    
    function cell_image($image_url = '', $href = '', $max_width = '100', $max_height = '28', $attributes = array())
    {
        $data = array();
        
        $data['image_url'] = $image_url;
        $data['max_width'] = $max_width;
        $data['max_height'] = $max_height;
        $data['href'] = $href;
        $data['is_href'] = (strlen($href) > 0);
        $data['attributes'] = _attributes_to_string($attributes);
        
        return $this->load_view('listing/tools/image', $data, TRUE);
    }
    
    /* Contextmenu */
    
    protected function _contextmenu($contextmenu_id, $text = '', $href = '', $icon = '', $confirm = '')
    {
        $data_array = @explode('|', $confirm, 2);

        $data_text = @$data_array[0];
        $data_title = @$data_array[1];

        if(strlen($data_title) == 0)
        {
            $data_title = ll('admin_general_warning');
        }
        
        @$this->contextmenu[$contextmenu_id][] = array(
            'text' => $text,
            'href' => $href,
            'icon' => ADMIN_ASSETS . 'jeegoocontext/images/icons/' . $icon . '.' . cfg('format', 'contextmenu_icon'),
            'data-text' => $data_text,
            'data-title' => $data_title
        );
    }
    
    /* Categorizing */
    
    function categorizing_add_item($id, $name)
    {
        @$this->categorizing['items'][$id] = $name;
    }
    
    function categorizing_add_widget($id, $name)
    {
        @$this->categorizing['widgets'][$id] = $name;
    }
    
    function categorizing_add_item_to_widget($widget_id, $item_id)
    {
        @$this->listing['used_item_ids'][] = $item_id;
        @$this->categorizing['widget_items'][$widget_id][] = $item_id;
    }
    
    function categorizing($options = array())
    {
        $defaults = array(
            'widget_sort_method' => '',
            'item_sort_method' => '',
            'unique' => TRUE,
            'unique_in_widget' => TRUE,
            'trash' => ll('admin_general_trash'),
            'delete_item' => ll('admin_general_delete_item')
        );
        
        foreach($defaults as $key => $value) if(isset($options[$key])) $defaults[$key] = $options[$key];
        
        if(!$this->_get_status('tab')) $this->_open_wrap();
        
        if(strlen($options['widget_sort_method']) > 0 && !strpos($options['widget_sort_method'], '/')) $options['widget_sort_method'] = $this->router->directory . $this->router->class . '/' . $options['widget_sort_method'];
        if(strlen($options['item_sort_method']) > 0 && !strpos($options['item_sort_method'], '/')) $options['item_sort_method'] = $this->router->directory . $this->router->class . '/' . $options['item_sort_method'];

        // Open
        $this->_add_content($this->load_view('categorizing/open', $options, TRUE));
        
        // Items
        $items = (array)@$this->categorizing['items'];
        
        if($options['unique'])
        {
            foreach((array)@$this->listing['used_item_ids'] as $used_item_id)
            {
                unset($items[$used_item_id]);
            }
        }
        
        $this->_add_content($this->load_view('categorizing/items', array('items' => $items), TRUE));
        
        // Widgets
        foreach((array)@$this->categorizing['widgets'] as $widget_id => $widget_name)
        {
            $widget_data = array();
            $widget_data['items'] = array();
            
            $widget_data['id'] = $widget_id;
            $widget_data['name'] = $widget_name;
            
            foreach((array)@$this->categorizing['widget_items'][$widget_id] as $item_id)
            {
                $widget_data['items'][] = array(
                    'id' => $item_id,
                    'name' => @$this->categorizing['items'][$item_id]
                );
            }
            
            $this->_add_content($this->load_view('categorizing/widget', $widget_data, TRUE));
        }
        
        // Close
        $close_data = array();
        
        $close_data['trash'] = $defaults['trash'];
        $close_data['delete_item'] = $defaults['delete_item'];
        
        $this->_add_content($this->load_view('categorizing/close', $close_data, TRUE));
        
        // Reset data
        $this->categorizing = array();
    }
    
    /* Widgets */
    
    function add_widget($id, $width = 10, $heading = '', $content = '', $settings = array())
    {
        $width = intval($width);
        if($width < 1 || $width > 10) $width = 10;
        
        $this->widgets[] = array(
            'id' => $id,
            'width' => $width,
            'heading' => $heading,
            'content' => $content,
            'settings' => $settings
        );
    }
    
    function widgets($id)
    {
        $id = 'widgets_' . $id;
        
        // Open
        $this->_add_content($this->load_view('widgets/open', array('id' => $id), TRUE));
        
        foreach($this->widgets as $widget)
        {
            $widget['id'] = 'widget_' . $id . '_' . $widget['id'];
            $this->_add_content($this->load_view('widgets/widget', $widget, TRUE));
        }
        
        // Clear widgets
        $this->widgets = array();
        
        // Close
        $this->_add_content($this->load_view('widgets/close', array(), TRUE));
    }
    
    function clear_widgets()
    {
        $this->widgets = array();
    }
    
    /* Charts */
    
    function chart($data = '')
    {
        $id = $this->chart_id++;
        
        $this->charts[$id] = $data;
        
        $this->_add_content($this->load_view('general/chart', array('id' => $id), TRUE));
    }
    
    function new_chart($data)
    {
        $id = $this->chart_id++;
        $this->charts[$id] = $data;
        return $id;
    }

    /* Buttons */
    
    function button_link($url, $text, $type = 'transferthick-e-w', $confirm = '')
    {
        $this->_button($url, $text, '', $type, $confirm);
    }
    
    function button_admin_link($url, $text, $type = 'transferthick-e-w', $confirm = '')
    {
        $attributes = array();
        $attributes['class'] = 'jui_button ui-state-default';
        
        $anchor = '<span class="ui-icon ui-icon-' . $type . '"></span>' . htmlspecialchars($text);
        
        $this->buttons .= admin_anchor($url, $anchor, $confirm, $attributes);
    }
    
    function button_submit($text, $param = '', $type = 'disk')
    {
        $this->_button($param, $text, 'form_submit_e', $type);
    }
    
    function button_index($title = '')
    {
        if($this->router->fetch_method() != 'index')
        {
            $this->index_button_generated = TRUE;
            if($title == '') $title = __('title') . ' - ' . ((strlen(__('index')) > 0) ? __('index') : ll('admin_general_index'));
            $this->_button(site_url($this->router->directory . $this->router->fetch_class()), $title, '', 'arrowreturnthick-1-w');
        }
    }
    
    protected function _button($url = '', $text = '', $classes = '', $type = '', $confirm = '')
    {
        $data = array();
        
        $data['url'] = '#';
        $data['text'] = htmlspecialchars($text);
        $data['type'] = 'ui-icon-' . $type;
        $data['data'] = '';
        
        if(strlen($confirm) > 0)
        {
            // Confirmation
            
            $data_array = @explode('|', $confirm, 2);
            
            $data_text = @$data_array[0];
            $data_title = @$data_array[1];
            
            if(strlen($data_title) == 0)
            {
                $data_title = ll('admin_general_warning');
            }
            
            $data_attributes = array();
            
            $data_attributes['data-page'] = 'confirm_link';
            $data_attributes['data-href'] = $url;
            $data_attributes['data-title'] = $data_title;
            $data_attributes['data-text'] = $data_text;
            
            $data['classes'] = $classes . ' confirm_link';
            $data['data'] = _attributes_to_string($data_attributes);
        }
        
        else
        {
            $data['classes'] = $classes;
            $data['url'] = $url;
        }
        
        $this->buttons .= $this->load_view(cfg('folder', 'fields') . '/general/button', $data, TRUE);
    }
    
    function button_helper($text = '', $title = '')
    {
        $data = array();

        if(strlen($title) == 0)
        {
            $title = ll('admin_general_help');
        }
        
        $data['text'] = $text;
        $data['title'] = $title;
        
        $this->buttons .= $this->load_view(cfg('folder', 'fields') . '/general/button_helper', $data, TRUE);
    }
    
    function get_buttons()
    {
        return $this->buttons;
    }
    
    function generate_buttons()
    {
        // Try to generate index button
        if($this->generate_index_button && !$this->index_button_generated) $this->button_index();
        
        $this->buttons_generated = TRUE;
        
        // Add buttons as field
        if($this->_get_status('fields_table'))
        {
            $this->add_field('buttons');
        }
        
        // Add buttons as widget (outside of the tabs)
        else
        {
            $this->_add_main_content($this->load_view('general/buttons', array('content' => $this->buttons), TRUE));
        }
    }
    
    function clear_buttons()
    {
        $this->buttons = '';
    }
    
    /* Ajax areas */
    
    function ajax_area($ajax_method = '', $handlers = array())
    {
        $data = array();
        
        if(!strpos($ajax_method, '/')) $ajax_method = $this->router->directory . $this->router->class . '/' . $ajax_method;
        
        $data['handlers'] = $handlers;
        $data['ajax_method'] = $ajax_method;
        
        if($this->_get_status('fields_table'))
        {
            $this->_add_content($this->load_view('general/ajax_area_field', $data, TRUE));
        }
        else
        {
            $this->_add_content($this->load_view('general/ajax_area', $data, TRUE));
        }
    }
    
    /* Messages, warnings, errors */

    function message($message, $session = FALSE)
    {
        if($session === TRUE)
        {
            $this->CI->session->set_flashdata('message', $message);
        }

        else
        {
            $this->messages[] = $message;
        }
    }
    
    function warning($warning, $session = FALSE)
    {
        if($session === TRUE)
        {
            $this->CI->session->set_flashdata('warning', $warning);
        }

        else
        {
            $this->warnings[] = $warning;
        }
    }
    
    function error($error, $session = FALSE)
    {
        if($session === TRUE)
        {
            $this->CI->session->set_flashdata('error', $error);
        }

        else
        {
            $this->errors[] = $error;
        }
    }

    protected function _parse_messages()
    {
        $messages_content = '';
        foreach($this->messages as $message)
        {
            $messages_content .= $this->load_view('general/message', array('message' => $message), TRUE);
        }

        $flash_message = $this->CI->session->flashdata('message');
        if(strlen($flash_message) > 0) $messages_content .= $this->load_view('general/message', array('message' => $flash_message), TRUE);

        return $messages_content;
    }
    
    protected function _parse_warnings()
    {
        $warnings_content = '';
        foreach($this->warnings as $warning)
        {
            $warnings_content .= $this->load_view('general/warning', array('warning' => $warning), TRUE);
        }

        $flash_warning = $this->CI->session->flashdata('warning');
        if(strlen($flash_warning) > 0) $warnings_content .= $this->load_view('general/warning', array('warning' => $flash_warning), TRUE);

        return $warnings_content;
    }
    
    protected function _parse_errors()
    {
        $errors_content = '';
        foreach($this->errors as $error)
        {
            $errors_content .= $this->load_view('general/error', array('error' => $error), TRUE);
        }

        $flash_error = $this->CI->session->flashdata('error');
        if(strlen($flash_error) > 0) $errors_content .= $this->load_view('general/error', array('error' => $flash_error), TRUE);

        return $errors_content;
    }
    
    /* Breadcrumbs */
    
    function breadcrumbs($links = NULL)
    {
        if(is_array($links)) $this->breadcrumbs = $links;
        $this->_add_content($this->_get_breadcrumbs());
        $this->generate_breadcrumbs = FALSE;
    }
    
    function get_first_breadcrumb()
    {
        $directory = $this->router->directory;
        $controller = $this->router->fetch_class();
        $method = $this->router->fetch_method();

        load_lang(cfg('folder', 'admin') . '/' . $controller);

        $ll_title = ll($directory . $controller . '_title');

        return array(
            'text' => (strlen($ll_title) > 0) ? $ll_title : $this->title,
            'href' => ($method == 'index') ? '#' : site_url($directory . $controller)
        );
    }
    
    function get_last_breadcrumb()
    {
        $directory = $this->router->directory;
        $controller = $this->router->fetch_class();
        $method = $this->router->fetch_method();

        load_lang(cfg('folder', 'admin') . '/' . $controller);

        return array(
            'text' => ($method == 'index') ? ((strlen(__('index')) > 0) ? __('index') : ll('admin_general_index')) : ((strlen($this->title) > 0) ? $this->title : ll($directory . $controller . '_title_' . $method)),
            'href' => '#'
        );
    }
    
    protected function _get_breadcrumbs()
    {
        $this->parse_tabs();
        
        $data = array();
        
        $data['buttons'] = $this->get_buttons();
        
        if(is_array($this->breadcrumbs))
        {
            $data['links'] = $this->breadcrumbs;
        }
        else
        {
            $data['links'] = array();
            $data['links'][] = $this->get_first_breadcrumb();
            
            // Try to add middle breadcrumbs
            if(count($this->middle_breadcrumbs) > 0)
            {
                $data['links'] = array_merge($data['links'], $this->middle_breadcrumbs);
            }
            
            $data['links'][] = $this->get_last_breadcrumb();
        }
        
        $this->breadcrumbs = NULL;
        
        foreach($data['links'] as $key => $link)
        {
            $data['links'][$key]['text'] = htmlspecialchars(@$link['text']);
        }
        
        return $this->load_view('strips/breadcrumbs', $data, TRUE);
    }
    
    function add_breadcrumb($breadcrumb = array())
    {
        $this->middle_breadcrumbs[] = $breadcrumb;
    }
    
    /* Strips */
    
    protected function _get_strips()
    {
        $strips = '';
        if($this->generate_breadcrumbs) $strips .= $this->_get_breadcrumbs();
        return $strips;
    }
    
    /* Filters */

    function filters($filters = array())
    {
        // TODO: Spravit typovu kontrolu premennych (explicitne pretrypovanie na typ array)

        $this->parse_tabs();

        $this->filters = array_merge($this->filters, $filters);

        $this->_add_content($this->load_view('strips/filters', array('filters' => $filters), TRUE));
    }

    protected function _filters_accept()
    {
        // TODO: Redirectovat len v priipade ze v  URL nie su zadane vsetky hofnoty premennych filtrov
        // Ak su zadane vsetky -> ulozit ich do session, resp. nezadane neukladat vobec
        // (Tie ktore su defaultne neukladat a tie ktore nie su defaultne ukladat
        // <=> Zmazat session (len filtre) a potom do nej ulozit tie ffiltre ktore treba ulozit)

        foreach((array)$this->filters as $filter_name => $filter_options)
        {
            // TODO: Zo session zistit ci je filter zadany, ak je jeho hodnotu
            // ziskat zo session, inak bude hodnota defaultne nastavena na prvu moznost

            // Potom treba poskladat cely URL retazec a redirectnut
        }
    }
    
    /* Admin forms */
    
    protected function _forms()
    {
        $this->admin_forms = array();
        
        $this->CI->load->helper('file');
        
        foreach(get_filenames(APPPATH . cfg('folder', 'admin_forms')) as $filename)
        {
            if(get_ext($filename) == 'xml')
            {
                $this->admin_forms[] = cut_ext($filename);
            }
        }
    }
    
    protected function _get_admin_form_path($filename = '')
    {
        return APPPATH . cfg('folder', 'admin_forms') . '/' . $filename . '.xml';
    }

    function admin_form_exists($admin_form = '')
    {
        return (bool)(in_array($admin_form, $this->admin_forms));
    }
    
    function form($admin_form = '')
    {
        if($this->admin_form_exists($admin_form))
        {
            // Try to load cached form
            if($this->admin_form_caching)
            {
                $cache_filename = $this->get_admin_form_cache_filename($admin_form);
                $cached_form = $this->CI->cache->get($cache_filename);
            }
            
            if($this->admin_form_caching && $cached_form)
            {
                // Form has been loaded from cache
                $this->_add_content($cached_form);
            }
            
            else
            {
                if($this->admin_form_caching) $this->buffering_start();
                
                $this->CI->load->library('xml');

                $form = $this->CI->xml->xml_parse($this->_get_admin_form_path($admin_form));

                if(isset($form['field'][0]))
                {
                    // The are several fields
                    foreach(@$form['field'] as $field)
                    {
                        $this->_add_xml_field(@$field['@attributes']['type'], @$field['@attributes']['info'], @$field['param']);
                    }
                }

                else
                {
                    // There are only one field
                    $this->_add_xml_field(@$form['field']['@attributes']['type'], @$form['field']['@attributes']['info'], $form['field']['param']);
                }
                
                if($this->admin_form_caching)
                {
                    // Try to close opened fields table
                    $this->close_fields_table();
                    
                    // Try to close listing
                    $this->close_listing();

                    $form_content = $this->get_buffer_content();

                    $this->CI->cache->save($cache_filename, $form_content);
                }
            }
        }
        
        else
        {
            $this->_add_content("Formulár <strong>" . $admin_form . "</strong> sa nepdarilo načítať, pretože neexistue.");
            //show_error("Formulár <strong>" . $admin_form . "</strong> sa nepdarilo načítať, pretože neexistue.");
        }
    }
    
    function get_admin_form_cache_filename($admin_form = '')
    {
        return $this->admin_form_cache_file_prefix . md5($admin_form);
    }
    
    protected function _add_xml_field($type, $info, $params)
    {
        $params = (array)$params;
        
        foreach($params as $param_key => $param)
        {
            if(strtolower($param) == 'true') $params[$param_key] = TRUE;
            elseif(strtolower($param) == 'false') $params[$param_key] = FALSE;
        }
        
        // Info
        if(strlen($info) > 0) $this->info($info);
        
        $params = array_merge(array($type), $params);
        
        call_user_func_array(array($this, 'add_field'), $params);
    }
    
    /* Form validation */
    
    function set_rules($field, $label = '', $rules = '')
    {
        $this->validation_rules[$field] = array(
            'label' => $label,
            'rules' => $rules
        );
        
        $this->CI->load->library('form_validation');
        $this->CI->form_validation->set_rules($field, $label, $rules);
    }
    
    function validate()
    {
        $this->CI->load->library('form_validation');
        $validate = $this->CI->form_validation->run();
        if(form_sent() && !$validate)
        {
            $this->error(ll('admin_general_form_validation_failed'));
            $this->variables['form_validation_error'] = TRUE;
        }
        return $validate;
    }
    
    /* Generating */
    
    function set_main_view($view = '')
    {
        $this->main_view = $view;
    }
    
    function get_main_view()
    {
        return $this->main_view;
    }
    
    function generate()
    {
        if($this->generated) return TRUE;
        $this->generated = TRUE;
        
        $is_iframe = $this->is_iframe();

        // Try to accept filters
        $this->_filters_accept();

        // Try to generate widgets
        if(count($this->widgets) > 0) $this->widgets(md5($this->CI->router->fetch_directory()  . '_' . $this->CI->router->fetch_class()  . '_' . $this->CI->router->fetch_method()  . '_' . ++$this->widgets_id));
        
        // Try to generate tabs
        $this->parse_tabs();
        
        // Try to close wrap
        $this->close_wrap();
        
        // Try to generate listing
        if(count(@$this->listing['cols'])) $this->listing();
        
        // Try to generate buttons
        if(!$this->buttons_generated && strlen($this->buttons)) $this->generate_buttons();
        
        // Create view data
        $data = array();
        
        // Set form action
        $data['form_action'] = ($is_iframe) ? (uri_string() . '?' . cfg('url', 'iframe') . '=' . cfg('url', 'true')) : uri_string();
        
        // Title
        $data['title'] = (strlen($this->title) > 0) ? $this->title . cfg('url', 'separator') . cfg('general', 'app_name') : cfg('general', 'app_name');
        
        // Top
        $data['top'] = ($this->top) ? $this->load_view('general/top', array(), TRUE) : '';
        
        // Menu
        $data['menu'] = '';
        
        if($this->menu)
        {
            $menu_data = array();
            
            // TODO:
            $limit = 9999999;
            
            // Pages
            $menu_data['pages'] = array();
            $this->CI->cms->model->load_system('pages');
            foreach(array_slice($this->CI->s_pages_model->get_ids(), 0, $limit) as $page_id)
            {
                $menu_data['pages'][$page_id] = $this->CI->s_pages_model->$page_id->name;
            }
            
            // Page types
            $menu_data['page_types'] = array();
            $this->CI->cms->model->load_system('page_types');
            foreach(array_slice($this->CI->s_page_types_model->get_ids(), 0, $limit) as $page_type_id)
            {
                $menu_data['page_types'][$page_type_id] = $this->CI->s_page_types_model->$page_type_id->name;
            }
            
            // Categories
            $menu_data['categories'] = array();
            $this->CI->cms->model->load_system('categories');
            foreach(array_slice($this->CI->s_categories_model->get_ids(), 0, $limit) as $category_id)
            {
                $menu_data['categories'][$category_id] = $this->CI->s_categories_model->$category_id->_name;
            }
            
            // Menus
            $menu_data['menus'] = array();
            $this->CI->cms->model->load_system('menus');
            foreach(array_slice($this->CI->s_menus_model->get_ids(), 0, $limit) as $menu_id)
            {
                $menu_data['menus'][$menu_id] = $this->CI->s_menus_model->$menu_id->name;
            }
            
            // Panels
            $menu_data['panels'] = array();
            $this->CI->cms->model->load_system('panels');
            foreach(array_slice($this->CI->s_panels_model->get_ids(), 0, $limit) as $panel_id)
            {
                $menu_data['panels'][$panel_id] = $this->CI->s_panels_model->$panel_id->name;
            }
            
            // Panel types
            $menu_data['panel_types'] = array();
            $this->CI->cms->model->load_system('panel_types');
            foreach(array_slice($this->CI->s_panel_types_model->get_ids(), 0, $limit) as $panel_type_id)
            {
                $menu_data['panel_types'][$panel_type_id] = $this->CI->s_panel_types_model->$panel_type_id->name;
            }
             
            // Positions
            $menu_data['positions'] = array();
            $this->CI->cms->model->load_system('positions');
            foreach(array_slice($this->CI->s_positions_model->get_ids(), 0, $limit) as $position_id)
            {
                $menu_data['positions'][$position_id] = $this->CI->s_positions_model->$position_id->name;
            }
            
            // Lists
            $menu_data['lists'] = array();
            $this->CI->cms->model->load_system('lists');
            foreach(array_slice($this->CI->s_lists_model->get_ids(), 0, $limit) as $list_id)
            {
                $menu_data['lists'][$list_id] = $this->CI->s_lists_model->$list_id->name;
            }
            
            // List types
            $menu_data['list_types'] = array();
            $this->CI->cms->model->load_system('list_types');
            foreach(array_slice($this->CI->s_list_types_model->get_ids(), 0, $limit) as $list_type_id)
            {
                $menu_data['list_types'][$list_type_id] = $this->CI->s_list_types_model->$list_type_id->name;
            }
            
            // Langs
            $this->CI->cms->model->load_system('langs');
            $menu_data['langs'] = array();
            foreach($this->CI->s_langs_model->get_data_in_col('lang') as $lang_id => $lang)
            {
                if(lang() == $lang) continue;
                $menu_data['langs'][$lang_id] = strtoupper($lang);
            }
            
            $data['menu'] = $this->load_view('general/menu', $menu_data, TRUE);
        }
        
        // Iframe
        if($is_iframe) $data['top'] = $data['menu'] = '';
        
        // Strips
        $data['strips'] = $this->_get_strips();
        
        // Contextmenu
        $data['contextmenu'] = (array)$this->contextmenu;
        
        // Charts
        $data['charts'] = (array)$this->charts;
        
        // Messages
        $data['messages'] = $this->_parse_messages();
        
        // Warnings
        $data['warnings'] = $this->_parse_warnings();
        
        // Errors
        $data['errors'] = $this->_parse_errors();
        
        // Content
        $data['content'] = $this->content;
        
        // Field id
        $this->hidden_fields['field_id'] = $this->field_id;
        
        // Hidden fields
        $data['hidden_fields'] = $this->hidden_fields;
        
        // Variables
        foreach($this->variables as $variable => $value)
        {
            $data[$variable] = $value;
        }
        
        // JS Panels
        $this->CI->cms->model->load_system('panels');
        $data['js_panels'] = $this->CI->s_panels_model->get_data_in_col('name');
        
        // JS Positions
        $this->CI->cms->model->load_system('positions');
        $data['js_positions'] = $this->CI->s_positions_model->get_data_in_col('name');
        
        // JS Pages
        $this->CI->cms->model->load_system('pages');
        $data['js_pages'] = $this->CI->s_pages_model->get_data_in_col('name');
        
        // JS Products
        $this->CI->cms->model->load_eshop('products');
        $data['js_products'] = $this->CI->e_products_model->get_data_in_col('_name');
        
        // JS Categories
        $this->CI->cms->model->load_eshop('categories');
        $data['js_categories'] = $this->CI->e_categories_model->get_data_in_col('_name');
        
        // JS Services
        $this->CI->cms->model->load_system('services');
        $data['js_services'] = $this->CI->s_services_model->get_data_in_col('name');
        
        // JS Langs
        $data['js_langs'] = array();
        $this->CI->cms->model->load_system('langs');
        $this->CI->config->load('valid_languages');
        $lang_names = cfg('valid_languages');
        foreach($this->CI->s_langs_model->get_data_in_col('code') as $lang)
        {
            $data['js_langs'][$lang] = @$lang_names[$lang];
        }
        
        // TODO:
        // CKEditor skin
        $data['ckeditor_skin'] = 'silver';
        
        // Load and return view content
        return $this->load_view($this->main_view, $data);
    }
    
}