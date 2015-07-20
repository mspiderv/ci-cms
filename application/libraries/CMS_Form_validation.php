<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Form_validation extends CI_Form_validation {

    function  __construct($rules = array())
    {
        parent::__construct($rules);
    }
    
    /* CMS */
    
    /**
     * Page with type
     */
    
    function page_with_type($page_id = '', $page_type_id = '')
    {
        if($page_id == '') return TRUE;
        $this->CI->cms->model->load_system('pages');
        return ($this->CI->s_pages_model->item_exists($page_id) && $this->CI->s_pages_model->get_item_data($page_id, 'page_type_id') == $page_type_id);
    }
    
    /**
     * Panel with type
     */
    
    function panel_with_type($panel_id = '', $panel_type_id = '')
    {
        if($panel_id == '') return TRUE;
        $this->CI->cms->model->load_system('panels');
        return ($this->CI->s_panels_model->item_exists($panel_id) && $this->CI->s_panels_model->get_item_data($panel_id, 'panel_type_id') == $panel_type_id);
    }
    
    /**
     * List with type
     */
    
    function list_with_type($list_id = '', $list_type_id = '')
    {
        if($list_id == '') return TRUE;
        $this->CI->cms->model->load_system('lists');
        return ($this->CI->s_lists_model->item_exists($list_id) && $this->CI->s_lists_model->get_item_data($list_id, 'list_type_id') == $list_type_id);
    }
    
    /**
     * Valid alias
     */
    
    function valid_alias($alias = '')
    {
        if($alias == '') return TRUE;
        return (!in_array(array_shift(explode('/', $alias)), cfg('url', 'reserved_aliases')));
    }
    
    /**
     * Daň
     */
    
    function tax($tax = '')
    {
        if($tax == '') return TRUE;
        return ($this->numeric($tax) && $tax > -1000 && $tax < 1000);
    }
    
    /**
     * Nadradený odkaz
     */
    
    function parent_link_id($parent_link_id = '', $link_id = '')
    {
        $this->CI->load->driver('cms');
        return ($this->CI->cms->menus->is_link_in_menu($parent_link_id, $this->CI->s_menu_links_model->$link_id->menu_id) && $this->CI->cms->menus->valid_parent_link_id($parent_link_id, $link_id));
    }
    
    /**
     * Odkaz menu
     */
    
    function menu_link($link_id = '', $menu_id = '')
    {
        if($link_id == '') return TRUE;
        
        $this->CI->load->driver('cms');
        
        if(!$this->CI->cms->menus->link_exists($link_id)) return FALSE;
        
        if(strlen($menu_id) > 0)
        {
            return (bool)$this->CI->cms->menus->is_link_in_menu($link_id, $menu_id);
        }
        else
        {
            return TRUE;
        }
    }
    
    /**
     * Typ závisloti dátového zdroja
     */
    
    function resource_rel_type($value = '')
    {
        if($value == '') return TRUE;
        
        $this->CI->load->driver('cms');
        return $this->CI->cms->resources->rel_type_exists($value);
    }
    
    /**
     * Povinný odkaz
     */
    
    function required_href($value = '')
    {
        if($value == '') return FALSE;
        
        $value = get_href(htmlspecialchars_decode($value));
        
        return in_array(@$value['type'], array('page', 'product', 'category', 'service', 'url'));
    }
    
    /**
     * Povinný odkaz (internal)
     */
    
    function required_internal($value = '')
    {
        if($value == '') return FALSE;
        
        $value = get_href(htmlspecialchars_decode($value));
        
        return in_array(@$value['type'], array('page', 'product', 'category', 'service'));
    }
    
    /**
     * Odkaz
     */
    
    function href($value = '')
    {
        if($value == '') return TRUE;
        
        $first_value = $value;
        
        $value = get_href(htmlspecialchars_decode($value));
        
        if($value === FALSE) return FALSE;
        
        return (@$value['type'] == 'url') ? TRUE : $this->internal($first_value);
    }
    
    function internal($value = '')
    {
        if($value == '') return TRUE;
        
        $value = get_href(htmlspecialchars_decode($value));
        
        if($value === FALSE) return FALSE;
        
        switch(@$value['type'])
        {
            case 'empty':
                return TRUE;
                break;
            
            case 'page':
                $this->CI->cms->model->load_system('pages');
                return $this->CI->s_pages_model->item_exists(@$value['value']);
                break;
            
            case 'product':
                if(!cfg('general', 'eshop')) return FALSE;
                $this->CI->cms->model->load_eshop('products');
                return $this->CI->e_products_model->item_exists(@$value['value']);
                break;
            
            case 'category':
                if(!cfg('general', 'eshop')) return FALSE;
                $this->CI->cms->model->load_eshop('categories');
                return $this->CI->e_categories_model->item_exists(@$value['value']);
                break;
            
            case 'service':
                $this->CI->cms->model->load_system('services');
                return $this->CI->s_services_model->item_exists(@$value['value']);
                break;
            
            default:
                return FALSE;
                break;
        }
    }
    
    /**
     * Knižica
     */
    
    function value($value = '', $values = '')
    {
        return (in_array($value, explode(',', $values)));
    }
    
    /**
     * Knižica
     */
    
    function library($library = '', $type = '')
    {
        $this->CI->load->driver('cms');
        return $this->CI->cms->libraries->library_exists($library, $type);
    }
    
    /**
     * Validná nadradená kategória stránok
     */
    
    function system_parent_category_id($parent_category_id = '', $category_id = '')
    {
        $this->CI->load->driver('cms');
        return $this->CI->cms->categories->valid_parent_category_id($parent_category_id, $category_id);
    }
    
    /**
     * Nadradená služba
     */
    
    function parent_service_id($parent_service_id = '', $service_id = '')
    {
        $this->CI->load->driver('cms');
        return $this->CI->cms->services->valid_parent_service_id($parent_service_id, $service_id);
    }
    
    /**
     * Nadradená stránka
     */
    
    function parent_page_id($parent_page_id = '', $page_id = '')
    {
        $this->CI->load->driver('cms');
        return $this->CI->cms->pages->valid_parent_page_id($parent_page_id, $page_id);
    }
    
    /**
     * Premenná typu panela
     */
    
    function panel_type_variable_id($panel_type_variable_id = '', $panel_type_id = '')
    {
        $this->CI->load->driver('cms');
        return (in_array($panel_type_variable_id, $this->CI->cms->panels->get_panel_type_variable_ids($panel_type_id, 'both')));
    }
    
    /**
     * Premenná typu stránky
     */
    
    function page_type_variable_id($page_type_variable_id = '', $page_type_id = '')
    {
        $this->CI->load->driver('cms');
        return (in_array($page_type_variable_id, $this->CI->cms->pages->get_page_type_variable_ids($page_type_id, 'both')));
    }
    
    /**
     * Premenná typu zoznamu
     */
    
    function list_type_variable_id($list_type_variable_id = '', $list_type_id = '')
    {
        $this->CI->load->driver('cms');
        return (in_array($list_type_variable_id, $this->CI->cms->lists->get_list_type_variable_ids($list_type_id, 'both')));
    }
    
    /**
     * Reserved values
     */
    
    function reserved($value = '', $reserved = '')
    {
        return (!in_array($value, explode(',', $reserved)));
    }
    
    /**
     * Field
     */
    
    function field($field = '')
    {
        if($field == '') return TRUE;
        $this->CI->load->driver('cms');
        return $this->CI->cms->field_exists($field);
    }
    
    function dynamic_field($field = '')
    {
        if($field == '') return TRUE;
        $this->CI->load->driver('cms');
        return $this->CI->cms->dynamic_field_exists($field);
    }
    
    function referring_field($field = '')
    {
        if($field == '') return TRUE;
        $this->CI->load->driver('cms');
        return $this->CI->cms->referring_field_exists($field);
    }
    
    function dynamic_or_referring_field($field = '')
    {
        if($field == '') return TRUE;
        return ($this->dynamic_field($field) || $this->referring_field($field));
    }
    
    /**
     * Dátový typ
     */
    
    function data_type($data_type = '')
    {
        if($data_type == '') return TRUE;
        return in_array(strtolower($data_type), array_map('strtolower', cfg('db', 'data_types')));
    }
    
    /**
     * IČO
     */
    
    function ico($ico = '')
    {
        if($ico == '') return TRUE;
        return ($this->integer($ico) && $this->exact_length($ico, 8));
    }
    
    /**
     * PSČ
     */
    
    function psc($psc = '')
    {
        if($psc == '') return TRUE;
        return ($this->min_length($psc, 3) && $this->max_length($psc, 10));
    }
    
    /**
     * Telefónne číslo
     */
    
    function telephone($number = '')
    {
        if($number == '') return TRUE;
        $number = '0' . substr(str_replace(' ', '', $number), 1);
        $length = strlen($number);
        return ($length >= 9 && $length <= 14 && preg_match('/^[0-9]+$/', $number));
    }
    
    /**
     * Formát ID objednávky
     */
    
    function order_id_format($order_id_format = '')
    {
        if($order_id_format == '') return TRUE;

        $required = cfg('order_id_format', 'required');
        $possible = array_keys(cfg('order_id_format', 'variables'));
        $contains = preg_split('//', strtolower($order_id_format), -1, PREG_SPLIT_NO_EMPTY);
        
        if(count(array_diff($required, $contains)) > 0) return FALSE;
        foreach($possible as $possible_char) $contains = remove_from_array($contains, strtolower($possible_char));
        return (count($contains) == 0);
    }
    
    /**
     * Heslo administrátora
     */
    
    function user_password($password = '', $user_id = '')
    {
        if($password == '') return TRUE;
        if(intval($user_id) == 0) $user_id = admin_user_id();
        
        $this->CI->load->driver('admin');

        return $this->CI->admin->auth->check_user_password($user_id, $password);
    }
    
    /**
     * Kladné / záporné číslo
     */
    
    function plus($number = '')
    {
        if($number == '') return TRUE;
        else return !(doubleval($number) < 0);
    }
    
    function minus($number = '')
    {
        if($number == '') return TRUE;
        return !$this->plus($number);
    }
    
    /**
     * Percento
     */
    
    function percent($percent = '')
    {
        if($this->numeric($percent))
        {
            return ($percent >= 0 && $percent <= 100);
        }
        
        return FALSE;
    }
    
    /**
     * Farba
     */
    
    function color($color = '')
    {
        if(substr($color, 0, 1) == '#')
        {
            $color = substr($color, 1);
            return ((strlen($color) == 3 || strlen($color) == 6) && ctype_xdigit($color));
        }
        
        return FALSE;
    }
    
    /**
     * Doprava a platba
     */
    
    function transport_id($transport_id = '', $lang_id = '')
    {
        $this->CI->load->driver('eshop');
        return $this->CI->eshop->transport_payment->transport_exists($transport_id , $lang_id);
    }
    
    function payment_id($payment_id = '', $lang_id = '')
    {
        $this->CI->load->driver('eshop');
        return $this->CI->eshop->payment_payment->payment_exists($payment_id , $lang_id);
    }
    
    /**
     * Cena
     */
    
    function price($price = '', $decimals = '')
    {
        if($decimals == '') $decimals = cfg('price', 'max_decimal');
        $fulls = cfg('price', 'max_full');
        
        $price = str_replace(',', '.', (string)$price);
        
        if($price == '') return TRUE;
        
        if($this->numeric($price))
        {
            if(strpos($price, '.') > -1)
            {
                list($full, $decimal) = explode('.', $price);
                if((strlen($full) <= $fulls) && (strlen($decimal) <= $decimals)) return doubleval($price);
                else return FALSE;
            }
            else
            {
                if(strlen($price) <= $fulls) return doubleval($price);
                else return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
    }
    
    /**
     * Jazyk administrácie
     */
    
    function admin_lang($lang = '')
    {
        if($lang == '') return TRUE;
        return in_array($lang, cfg('admin_languages'));
    }
    
    /**
     * Hodnota premennej typu produktu
     */
    
    function product_type_variable_value($product_type_variable_value_id = '', $product_type_variable_id = '')
    {
        $this->CI->load->driver('eshop');
        return in_array($product_type_variable_value_id, $this->CI->eshop->product_types->get_variable_value_ids($product_type_variable_id));
    }
    
    /**
     * Šablóny
     */
    
    function tpl($tpl = '', $type = '')
    {
        if($tpl == '') return TRUE;
        $this->CI->load->driver('cms');
        return in_array($tpl, $this->CI->cms->templates->get_templates($type));
    }
    
    /**
     * SEO polia
     */
    
    function sitemap_priority($sitemap_priority = '')
    {
        if($sitemap_priority == '') return TRUE;
        return ($this->numeric($sitemap_priority) && $sitemap_priority >= 0 && $sitemap_priority <= 1);
    }
    
    function changefreq($changefreq = '')
    {
        return in_array($changefreq, cfg('changefreq', 'values'));
    }
    
    /**
     * E-shop - Kategórie
     */
    
    function eshop_parent_category_id($parent_category_id = '', $category_id = '')
    {
        $this->CI->load->driver('eshop');
        return $this->CI->eshop->categories->valid_parent_category_id($parent_category_id, $category_id);
    }
    
    /**
     * Administrátori
     */
    function free_admin_user_name($name = '', $instead_of = '')
    {
        if($name == $instead_of) return TRUE;
        $this->CI->load->driver('admin');
        $usernames = $this->CI->admin->auth->get_usernames();
        return (!in_array($name, $usernames));
    }
    
    /**
     * Zálohy databázy
     */
    
    function database_backup_file($file = '')
    {
        $this->CI->load->library('database_deposit');
        return (bool)in_array($file, $this->CI->database_deposit->get_backups());
    }
    
    function no_database_backup_file($file = '', $instead_od = '')
    {
        if($file == $instead_od) return TRUE;
        $this->CI->load->library('database_deposit');
        return (bool)!in_array($file, $this->CI->database_deposit->get_backups());
    }
    
    /**
     * Jazyky
     */
    
    function lang_code($value)
    {
        $this->CI->load->driver('cms');
        return $this->CI->cms->langs->is_lang_code($value);
    }
    
    function available_lang_code($value, $plus_lang_id = '')
    {
        $this->CI->load->driver('cms');
        return $this->CI->cms->langs->is_available_lang_code($value, $plus_lang_id);
    }

    /**
     * Unique
     */

    function unique($value, $field)
    {
        return $this->_unique($value, $field, '');
    }

    function unique_admin($value, $field)
    {
        return $this->_unique($value, $field, 'admin');
    }

    function unique_eshop($value, $field)
    {
        return $this->_unique($value, $field, 'eshop');
    }

    function unique_system($value, $field)
    {
        return $this->_unique($value, $field, 'system');
    }

    function unique_user($value, $field)
    {
        return $this->_unique($value, $field, 'user');
    }

    protected function _unique($value, $field, $model_type)
    {
        $this->CI->load->driver('cms');
        
        // Pomocná premenná
        $status = TRUE;

        // Získa názov tabuľky a stĺpca
        list($table, $column) = explode('.', $field, 2);

        // Upraví názov stĺpca
        if(substr($column, 0, 1) == '_') $column = lang() . $column;

        // Vytvorí celý názov tabuľky
        switch($model_type)
        {
            default:
                $fable_fullname = $table;
                break;

            case 'admin':
                $fable_fullname = $this->CI->cms->model->admin_table($table);
                break;

            case 'eshop':
                $fable_fullname = $this->CI->cms->model->eshop_table($table);
                break;

            case 'system':
                $fable_fullname = $this->CI->cms->model->system_table($table);
                break;

            case 'user':
                $fable_fullname = $this->CI->cms->model->user_table($table);
                break;
        }

        // Zistí, či zadaná tabuľka má model
        $loaded_models = cms_model::get_loaded_models();
        if(in_array($fable_fullname, $loaded_models))
        {
            // Zadaná tabuľka už má model
            $loaded_model_names = cms_model::get_loaded_model_names();
            $model_name = $loaded_model_names[$fable_fullname] . '_model';

            // Vytvorí kópiu inštancie modela zadanej tabuľky
            $validation_model = $this->CI->$model_name;

            // Zistí, ci zadaný stĺpec existuje
            if(!in_array($column, $validation_model->get_fields()))
            {
                return TRUE;
            }

            // Overí riadky
            foreach($validation_model->get_ids() as $item_id)
            {
                if($validation_model->get_item_data($item_id, $column) == $value)
                {
                    $status = FALSE;
                    break;
                }
            }
        }
        else
        {
            // Zadaná tabuľka nemá model
            $search = $this->CI->db;
            $search = $search->where($column, $value);
            $search = $search->get($fable_fullname);

            $num_rows = $search->num_rows();

            if($num_rows > 0)
            {
                $status = FALSE;
            }
        }

        // Vráti boolean hodnotu
        return $status;
    }

    /**
     * Item exists
     */

    function item_exists_resource_rel($item_id, $type = '')
    {
        if(intval($item_id) == 0) return TRUE;
        
        switch($type)
        {
            case 'page_category':
                return $this->item_exists_system($item_id, 'categories');
                break;
            
            case 'page_type':
                return $this->item_exists_system($item_id, 'page_types');
                break;
            
            case 'page':
                return $this->item_exists_system($item_id, 'pages');
                break;
            
            case 'panel_type':
                return $this->item_exists_system($item_id, 'panel_types');
                break;
            
            case 'panel':
                return $this->item_exists_system($item_id, 'panels');
                break;
            
            case 'product_category':
                return $this->item_exists_eshop($item_id, 'categories');
                break;
            
            case 'product':
                return $this->item_exists_eshop($item_id, 'products');
                break;
            
            case 'service':
                return $this->item_exists_system($item_id, 'services');
                break;
            
            default:
                return TRUE;
                break;
        }
    }

    function item_exists($item_id, $table)
    {
        return $this->_item_exists($item_id, $table, '');
    }

    function item_exists_admin($item_id, $table)
    {
        return $this->_item_exists($item_id, $table, 'admin');
    }

    function item_exists_eshop($item_id, $table)
    {
        return $this->_item_exists($item_id, $table, 'eshop');
    }

    function item_exists_system($item_id, $table)
    {
        return $this->_item_exists($item_id, $table, 'system');
    }

    function item_exists_user($item_id, $table)
    {
        return $this->_item_exists($item_id, $table, 'user');
    }

    protected function _item_exists($item_id, $table, $model_type)
    {
        if(intval($item_id) == 0) return TRUE;
        
        $this->CI->load->driver('cms');
        
        switch($model_type)
        {
            default:
                $this->CI->cms->model->load($table);
                $model_name = $table . '_model';
                break;

            case 'admin':
                $this->CI->cms->model->load_admin($table);
                $model_name = $this->CI->cms->model->admin_table_prefix . $table . '_model';
                break;

            case 'eshop':
                $this->CI->cms->model->load_eshop($table);
                $model_name = $this->CI->cms->model->eshop_table_prefix . $table . '_model';
                break;

            case 'system':
                $this->CI->cms->model->load_system($table);
                $model_name = $this->CI->cms->model->system_table_prefix . $table . '_model';
                break;

            case 'user':
                $this->CI->cms->model->load_user($table);
                $model_name = $this->CI->cms->model->user_table_prefix . $table . '_model';
                
                break;
        }
        
        $item_exists_model =& $this->CI->$model_name;
        return $item_exists_model->item_exists($item_id);
    }

    /**
     * Unmatch columnm
     */

    function unmatch_column($column, $table)
    {
        return $this->_unmatch_column($column, $table, '');
    }

    function unmatch_column_admin($column, $table)
    {
        return $this->_unmatch_column($column, $table, 'admin');
    }

    function unmatch_column_eshop($column, $table)
    {
        return $this->_unmatch_column($column, $table, 'eshop');
    }

    function unmatch_column_system($column, $table)
    {
        return $this->_unmatch_column($column, $table, 'system');
    }

    function unmatch_column_user($column, $table)
    {
        return $this->_unmatch_column($column, $table, 'user');
    }

    protected function _unmatch_column($column, $table, $model_type)
    {
        $this->CI->load->driver('cms');
        
        switch($model_type)
        {
            default:
                $this->CI->cms->model->load($table);
                $model_name = $table . '_model';
                
                break;

            case 'admin':
                $this->CI->cms->model->load_admin($table);
                $model_name = $this->CI->cms->model->admin_table_prefix . $table . '_model';
                break;

            case 'eshop':
                $this->CI->cms->model->load_eshop($table);
                $model_name = $this->CI->cms->model->eshop_table_prefix . $table . '_model';
                break;

            case 'system':
                $this->CI->cms->model->load_system($table);
                $model_name = $this->CI->cms->model->system_table_prefix . $table . '_model';
                break;

            case 'user':
                $this->CI->cms->model->load_user($table);
                $model_name = $this->CI->cms->model->user_table_prefix . $table . '_model';
                break;
        }
        
        $unmatch_column_model =& $this->CI->$model_name;
        return !in_array($column, $unmatch_column_model->get_fields());
    }
    
    /* V nasledujúcich metódach je reťazec '$this->CI->lang->line' nahradený reťazcom 'll' */
    
    /**
     * Executes the Validation routines
     *
     * @access	private
     * @param	array
     * @param	array
     * @param	mixed
     * @param	integer
     * @return	mixed
     */
    protected function _execute($row, $rules, $postdata = NULL, $cycles = 0)
    {
            // If the $_POST data is an array we will run a recursive call
            if (is_array($postdata))
            {
                    foreach ($postdata as $key => $val)
                    {
                            $this->_execute($row, $rules, $val, $cycles);
                            $cycles++;
                    }

                    return;
            }

            // --------------------------------------------------------------------

            // If the field is blank, but NOT required, no further tests are necessary
            $callback = FALSE;
            if ( ! in_array('required', $rules) AND is_null($postdata))
            {
                    // Before we bail out, does the rule contain a callback?
                    if (preg_match("/(callback_\w+(\[.*?\])?)/", implode(' ', $rules), $match))
                    {
                            $callback = TRUE;
                            $rules = (array('1' => $match[1]));
                    }
                    else
                    {
                            return;
                    }
            }

            // --------------------------------------------------------------------

            // Isset Test. Typically this rule will only apply to checkboxes.
            if (is_null($postdata) AND $callback == FALSE)
            {
                    if (in_array('isset', $rules, TRUE) OR in_array('required', $rules))
                    {
                            // Set the message type
                            $type = (in_array('required', $rules)) ? 'required' : 'isset';

                            if ( ! isset($this->_error_messages[$type]))
                            {
                                    if (FALSE === ($line = ll($type)))
                                    {
                                            $line = 'The field was not set';
                                    }
                            }
                            else
                            {
                                    $line = $this->_error_messages[$type];
                            }

                            // Build the error message
                            $message = sprintf($line, $this->_translate_fieldname($row['label']));

                            // Save the error message
                            $this->_field_data[$row['field']]['error'] = $message;

                            if ( ! isset($this->_error_array[$row['field']]))
                            {
                                    $this->_error_array[$row['field']] = $message;
                            }
                    }

                    return;
            }

            // --------------------------------------------------------------------

            // Cycle through each rule and run it
            foreach ($rules As $rule)
            {
                    $_in_array = FALSE;

                    // We set the $postdata variable with the current data in our master array so that
                    // each cycle of the loop is dealing with the processed data from the last cycle
                    if ($row['is_array'] == TRUE AND is_array($this->_field_data[$row['field']]['postdata']))
                    {
                            // We shouldn't need this safety, but just in case there isn't an array index
                            // associated with this cycle we'll bail out
                            if ( ! isset($this->_field_data[$row['field']]['postdata'][$cycles]))
                            {
                                    continue;
                            }

                            $postdata = $this->_field_data[$row['field']]['postdata'][$cycles];
                            $_in_array = TRUE;
                    }
                    else
                    {
                            $postdata = $this->_field_data[$row['field']]['postdata'];
                    }

                    // --------------------------------------------------------------------

                    // Is the rule a callback?
                    $callback = FALSE;
                    if (substr($rule, 0, 9) == 'callback_')
                    {
                            $rule = substr($rule, 9);
                            $callback = TRUE;
                    }

                    // Strip the parameter (if exists) from the rule
                    // Rules can contain a parameter: max_length[5]
                    $param = FALSE;
                    if (preg_match("/(.*?)\[(.*)\]/", $rule, $match))
                    {
                            $rule	= $match[1];
                            $param	= $match[2];
                    }

                    // Call the function that corresponds to the rule
                    if ($callback === TRUE)
                    {
                            if ( ! method_exists($this->CI, $rule))
                            {
                                    continue;
                            }

                            // Run the function and grab the result
                            $result = $this->CI->$rule($postdata, $param);

                            // Re-assign the result to the master data array
                            if ($_in_array == TRUE)
                            {
                                    $this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
                            }
                            else
                            {
                                    $this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
                            }

                            // If the field isn't required and we just processed a callback we'll move on...
                            if ( ! in_array('required', $rules, TRUE) AND $result !== FALSE)
                            {
                                    continue;
                            }
                    }
                    else
                    {
                            if ( ! method_exists($this, $rule))
                            {
                                    // If our own wrapper function doesn't exist we see if a native PHP function does.
                                    // Users can use any native PHP function call that has one param.
                                    if (function_exists($rule))
                                    {
                                            $result = $rule($postdata);

                                            if ($_in_array == TRUE)
                                            {
                                                    $this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
                                            }
                                            else
                                            {
                                                    $this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
                                            }
                                    }
                                    else
                                    {
                                            log_message('debug', "Unable to find validation rule: ".$rule);
                                    }

                                    continue;
                            }

                            $result = $this->$rule($postdata, $param);

                            if ($_in_array == TRUE)
                            {
                                    $this->_field_data[$row['field']]['postdata'][$cycles] = (is_bool($result)) ? $postdata : $result;
                            }
                            else
                            {
                                    $this->_field_data[$row['field']]['postdata'] = (is_bool($result)) ? $postdata : $result;
                            }
                    }

                    // Did the rule test negatively?  If so, grab the error.
                    if ($result === FALSE)
                    {
                            if ( ! isset($this->_error_messages[$rule]))
                            {
                                    if (FALSE === ($line = ll($rule)))
                                    {
                                            $line = 'Unable to access an error message corresponding to your field name.';
                                    }
                            }
                            else
                            {
                                    $line = $this->_error_messages[$rule];
                            }

                            // Is the parameter we are inserting into the error message the name
                            // of another field?  If so we need to grab its "field label"
                            if (isset($this->_field_data[$param]) AND isset($this->_field_data[$param]['label']))
                            {
                                    $param = $this->_translate_fieldname($this->_field_data[$param]['label']);
                            }

                            // Build the error message
                            $message = sprintf($line, $this->_translate_fieldname($row['label']), $param);

                            // Save the error message
                            $this->_field_data[$row['field']]['error'] = $message;

                            if ( ! isset($this->_error_array[$row['field']]))
                            {
                                    $this->_error_array[$row['field']] = $message;
                            }

                            return;
                    }
            }
    }

    // --------------------------------------------------------------------

    /**
     * Translate a field name
     *
     * @access	private
     * @param	string	the field name
     * @return	string
     */
    protected function _translate_fieldname($fieldname)
    {
            // Do we need to translate the field name?
            // We look for the prefix lang: to determine this
            if (substr($fieldname, 0, 5) == 'lang:')
            {
                    // Grab the variable
                    $line = substr($fieldname, 5);

                    // Were we able to translate the field name?  If not we use $line
                    if (FALSE === ($fieldname = ll($line)))
                    {
                            return $line;
                    }
            }

            return $fieldname;
    }
    
}
