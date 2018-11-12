<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Uni_model extends CI_Model {

    // TODO: Zlangovat texty
    
    // Modelové premenné
    public    $config;
    protected $col;
    protected $fields;
    protected $fields_filtered;
    protected $table;
    protected $lang_table = FALSE;
    protected $db_object;
    protected $lang_table_suffix = '_lang';
    protected $order_type = 'ASC';

    // Dátové premenné
    protected $data;
    protected $lang_data;
    protected $data_in_col;
    protected $insert_id;
    protected $ids;
    protected $rows;
    protected $lang_rows;
    protected $db_result;
    protected $db_lang_result;
    protected $unrequired_categorizing = FALSE;
    protected $conditions = array();
    protected $conditions_stack = array();
    protected $cond_logic = 'and';
    protected $use_null_value_while_ordering = FALSE;
    
    // Premenné cachu
    protected $cache_time_to_live = 0;
    protected $cache_key_prefix = 'con_';
    protected $cache_table_fields;
    protected $cache_table_fields_prefix = 'fields_';
    protected $cache_table_filtered_fields_prefix = 'filtered_fields_';
    protected $cache_data_in_col_prefix = 'data_in_col_';
    protected $cache_data_in_col;
    
    // Statické premenné
    protected static $user_condition_types_loaded = FALSE;
    protected static $valid_condition_types = array('=', '!', '==', '!=', '===', '!==', '<', '>', '<=', '=<', '>=', '=>');

    // Verejné premenné
    public $reinit = TRUE;
    public $reinit_add = TRUE;
    public $reinit_order = TRUE;
    public $show_errors = TRUE;
    
    // Konštanty
    const THIS_TABLE = '__TABLE__';
    const THIS_LANG_TABLE = '__LANG_TABLE__';

    /* Konštruktor */

    function  __construct()
    {
        parent::__construct();
        
        // Cache
        if($this->db->cache_on)
        {
            $this->load->driver('cms');
            $this->cms->load_cache();
        }
        
        $this->cache_table_fields = $this->cache_data_in_col = (bool)$this->db->cache_on;
        
        // Metódy pracujúce s podmienkami
        $this->init_user_conditions();
        
        // Modelové metóody
        $this->_init_db_object();
        $this->_init_table_cols();
        
        // Check table
        $this->_check_table();

        // Dátové metóody
        $this->_initialize();
    }

    /* Modelové metódy */

    protected function _init_db_object()
    {
        $db_object = $this->cms->model->get_db_object();
        if($db_object instanceof CI_DB_result) $this->db_object =& $db_object;
    }

    protected function is_db_object()
    {
        return ($this->db_object instanceof CI_DB_result);
    }

    protected function _init_table_cols()
    {
        $explicit_config = $this->cms->model->get_config();

        $this->col = cfg('table_cols');

        if(isset($explicit_config['id']))           $this->col['id']        = $explicit_config['id'];
        if(isset($explicit_config['order']))        $this->col['order']     = $explicit_config['order'];
        if(isset($explicit_config['order_cat']))    $this->col['order_cat'] = $explicit_config['order_cat'];
        if(isset($explicit_config['lang_id']))      $this->col['lang_id']   = $explicit_config['lang_id'];
        if(isset($explicit_config['lastmod']))      $this->col['lastmod']   = $explicit_config['lastmod'];
        
        if(isset($explicit_config['reinit']))       $this->reinit           = $explicit_config['reinit'];
        if(isset($explicit_config['reinit_add']))   $this->reinit_add       = $explicit_config['reinit_add'];
        if(isset($explicit_config['reinit_order'])) $this->reinit_order     = $explicit_config['reinit_order'];
        
        $this->table = $this->cms->model->get_table();
        
        if(!$this->table_exists($this->table)) return $this->show_error('Model tabuľky <strong>' . $this->table . '</strong> sa nepodarilo vytvoriť, pretože tabuľka neexistuje.');
        
        // Inicializácia DB objektu normálnej tabuľky
        if($this->is_db_object() === TRUE)
        {
            $db =& $this->db_object;
        }
        else
        {
            $db =& $this->db;
            $db = $db->select('*');
            
            if($this->is_ordering())
            {
                if($this->is_cat_ordering())
                {
                    $db = $db->order_by($this->col['order_cat'], $this->order_type);
                }
                
                $db = $db->order_by($this->col['order'], $this->order_type);
            }
            
            $db = $db->get($this->table);
        }

        // Inicializácia DB objektu jazykovej tabuľky
        $this->rows = $db->num_rows();
        $this->db_result = $db->result();
        
        $lang_table = $this->table . $this->lang_table_suffix;
        if($this->table_exists($lang_table))
        {
            $this->lang_table = $lang_table;
            
            $db =& $this->db;
            $db = $db->select('*');
            $db = $db->get($this->lang_table);
            $this->lang_rows = $db->num_rows();
            $this->db_lang_result = $db->result();
        }
        
        // Inicializácia stĺpcov
        if($this->lang_rows > 0)
        {
            if(strlen($this->lang_table) && $this->table != $this->cms->model->system_table('langs'))
            {
                $lang_fields = array_keys(get_object_vars($this->db_lang_result[0]));
                
                foreach($lang_fields as $lang_field_index => $lang_field)
                {
                    if($lang_field == 'id' || $lang_field == $this->col['lang_id'])
                    {
                        unset($lang_fields[$lang_field_index]);
                    }
                    else
                    {
                        foreach($this->s_langs_model->get_data_in_col('lang') as $lang)
                        {
                            $lang_fields[] = $lang . '_' . $lang_field;
                        }
                        
                        $lang_fields[$lang_field_index] = '_' . $lang_field;
                    }
                }
                
                $this->fields = array_merge(array_keys(get_object_vars($this->db_result[0])), $lang_fields);
            }
            else
            {
                $this->fields = array_keys(get_object_vars($this->db_result[0]));
            }
        }

        else
        {
            $this->_init_fields();
        }
        
        if($this->table != $this->cms->model->system_table('langs'))
        {
            $this->cms->model->load_system('langs');
            
            foreach($this->fields as $field_key => $field)
            {
                foreach($this->s_langs_model->get_data_in_col('lang') as $lang)
                {
                    $lang = $lang . '_';

                    if(substr($field, 0, strlen($lang)) == $lang)
                    {
                        $new_field = '_' . substr($field, strlen($lang));

                        if(!in_array($new_field, $this->fields, TRUE))
                        {
                            $this->fields[] = $new_field;
                        }
                    }
                }
            }
        }
    }
    
    function get_col($col)
    {
        return (strlen($col)) ? @$this->col[$col] : $this->col;
    }
    
    function get_table()
    {
        return $this->table;
    }
    
    protected function _check_table()
    {
        // TODO: Kontroovat ci ma ID stlpec auto_ncrement
        if(!$this->_id_column_has_primary_key()) return $this->show_error('Stĺpec <strong>' . $this->col['id'] . '</strong> musí mať priradený primárny kľúč.');
    }

    /* Dátové metódy */

    // Inicializácia

    protected function _initialize()
    {
        $this->data = array();
        $this->ids = array();
        $this->lang_data = array();
        
        // Multijazyčné polia
        if(strlen($this->lang_table))
        {
            foreach($this->db_lang_result as $item)
            {
                $item_id = $item->id;
                $lang_id = $item->lang_id;
                
                unset($item->id);
                unset($item->lang_id);
                
                $this->lang_data[$lang_id][$item_id] = $item;
            }
        }
        
        // Normálne polia
        foreach($this->db_result as $item)
        {
            $id = $this->col['id'];
            $this->data[$item->$id] = $item;
            $this->_add_id($item->$id);
        }
    }

    protected function _init_fields()
    {
        $this->fields = array();
        
        if($this->cache_table_fields)
        {
            $key = $this->cache_table_fields_prefix . $this->table;
            $cached_fields = $this->cache->get($key);

            if($cached_fields)
            {
                $this->fields = $cached_fields;
            }

            else
            {
                $this->_get_db_fields();
                $this->cache->save($key, $this->fields);
            }
        }
        
        else
        {
            $this->_get_db_fields();
        }
    }
    
    protected function _get_db_fields()
    {
        $this->fields = ($this->is_db_object()) ? $this->db_object->list_fields() : $this->db->list_fields($this->table);
        
        if(strlen($this->lang_table))
        {
            $lang_fields = $this->db->list_fields($this->lang_table);
            foreach($lang_fields as $lang_field_index => $lang_field)
            {
                if($lang_field == 'id' || $lang_field == $this->col['lang_id'])
                {
                    unset($lang_fields[$lang_field_index]);
                }
                else
                {
                    $this->cms->model->load_system('langs');
                    
                    foreach($this->s_langs_model->get_data_in_col('lang') as $lang)
                    {
                        $this->fields[] = $lang . '_' . $lang_field;
                    }
                    
                    $this->fields[] = '_' . $lang_field;
                }
            }
        }
    }

    // Metódy pracujúce s podmienkami
    
    function init_user_conditions()
    {
        if(!self::$user_condition_types_loaded)
        {
            self::$valid_condition_types = array_merge(self::$valid_condition_types, $this->cms->model_conditions->get_condition_types());
            self::$user_condition_types_loaded = TRUE;
        }
    }
    
    function where($field, $type = '', $value = '')
    {
        if(strlen($value) == 0 && strlen($type) == 0 && strlen($field) > 0 && substr_count($field, ' ') >= 2) return $this->_short_where($field);
        
        if(!$this->field_exists($field)) return $this->show_error('Podmienka nemohla byť vytvorená, pretože stĺpec <strong>' . $field . '</strong> neexistuje.');
        
        if(!in_array($type, self::$valid_condition_types, TRUE)) return $this->show_error('Podmienka nemohla byť vytvorená, pretože podmienka typu <strong>' . $type . '</strong> nie je validná.');
        
        $this->conditions[] = array(
            'field' => $field,
            'type'  => $type,
            'value' => $value,
            'logic' => $this->_get_cond_logic()
        );
    }
    
    function cond_or()
    {
        $this->cond_logic = 'or';
    }
    
    protected function _get_cond_logic()
    {
        $cond_logic = $this->cond_logic;
        $this->cond_logic = 'and';
        if(count($this->conditions) == 0) $cond_logic = 'and';
        return $cond_logic;
    }
    
    protected function _short_where($condition)
    {
        list($field, $type, $value) = explode(' ', $condition, 3);
        return $this->where($field, $type, $value);
    }
    
    function delete_conditions()
    {
        $this->cond_logic = 'and';
        $this->conditions = array();
    }
    
    function delete_stacked_conditions($key = '')
    {
        if(isset($this->conditions_stack[$key]))
        {
            unset($this->conditions_stack[$key]);
        }
        else
        {
            $this->conditions_stack = array();
        }
    }
    
    function conditions_exists()
    {
        return (count((array)$this->conditions) > 0);
    }
    
    function condition_type_exists($condition_type)
    {
        return (in_array($condition_type, self::$valid_condition_types));
    }
    
    function get_condition_types()
    {
        return (array)self::$valid_condition_types;
    }
    
    function get_conditions()
    {
        return $this->conditions;
    }
    
    function save_conditions($key)
    {
        $key = (string)$key;
        if(strlen($key) == 0) return $this->show_error('Podminky sa nepodarilo uložiť pretože metóda <strong>save_conditions</strong> prijala neočakávaný parameter.');
        $this->conditions_stack[$key] = $this->get_conditions();
    }
    
    function load_conditions($key, $autodelete = FALSE)
    {
        $key = (string)$key;
        if(!isset($this->conditions_stack[$key])) return $this->show_error('Podminky sa nepodarilo načítať pretože metóda <strong>load_conditions</strong> prijala neočakávaný parameter.');
        $this->conditions = $this->conditions_stack[$key];
        if($autodelete) $this->delete_stacked_conditions($key);
    }
    
    protected function _run_condition($value_1, $type, $value_2)
    {
        if(!$this->condition_type_exists($type)) return FALSE;
        
        switch($type)
        {
            case '=':
                return ($value_1 == $value_2);
                break;
            
            case '!';
                return ($value_1 != $value_2);
                break;
            
            case '==':
                return ($value_1 == $value_2);
                break;
            
            case '!=';
                return ($value_1 != $value_2);
                break;
            
            case '===';
                return ($value_1 === $value_2);
                break;
            
            case '!==';
                return ($value_1 !== $value_2);
                break;
            
            case '<';
                return ($value_1 < $value_2);
                break;
            
            case '>';
                return ($value_1 > $value_2);
                break;
            
            case '<=';
                return ($value_1 <= $value_2);
                break;
                
            case '=<';
                return ($value_1 <= $value_2);
                break;
            
            case '>=';
                return ($value_1 >= $value_2);
                break;
            
            case '=>';
                return ($value_1 >= $value_2);
                break;
            
            default:
                return $this->cms->model_conditions->$type($value_1, $value_2);
                break;
        }
    }
    
    // Metódy vracajúce id
    
    function get_ids()
    {
        $ids = array();
        
        if($this->conditions_exists())
        {
            $cached_ids = FALSE;
            
            if($this->db->cache_on)
            {
                $condition_cache_key = $this->get_condition_cache_key();
                $cached_ids = $this->cache->get($condition_cache_key);
            }
            
            if($cached_ids)
            {
                $this->delete_conditions();
                return $cached_ids;
            }
            
            else
            {
                foreach($this->get_all_ids() as $id)
                {
                    $passed = array();

                    foreach($this->conditions as $condition)
                    {
                        // Ak je hodnota validné pole, je tranfsformovaná na danú hodnotu pola (col1 == col2)
                        if($this->field_exists($condition['value'])) $condition['value'] = $this->get_item_data($id, $condition['value']);
                        
                        $result = (int) $this->_run_condition($this->get_item_data($id, $condition['field']), $condition['type'], $condition['value']);

                        // Vytvorenie novej skupiny
                        if(count($passed) == 0 || $condition['logic'] == 'or')
                        {
                            $passed[] = array();
                        }

                        $group = (array)array_pop($passed);
                        $group[] = $result;

                        $passed[] = $group;
                    }

                    $add = FALSE;

                    foreach($passed as $group)
                    {
                        if(!in_array(0, $group))
                        {
                            $add = TRUE;
                            continue;
                        }
                    }

                    if($add) $ids[] = $id;
                }

                $this->delete_conditions();
                
                if($this->db->cache_on)
                {
                    $this->cache->save($condition_cache_key, $ids, $this->cache_time_to_live);
                }

                return $ids;
            }
        }
        
        else
        {
            return $this->get_all_ids();
        }
    }
    
    function get_first()
    {
        return $this->get_item($this->get_first_id());
    }
    
    function get_first_id()
    {
        $ids = $this->get_ids();
        return (isset($ids[0])) ? $ids[0] : FALSE;
    }
    
    function get_all_ids()
    {
        return (array) $this->ids;
    }

    function get_ids_filter($filter = array())
    {
        return array_diff($this->get_ids(), (array)$filter);
    }

    function get_condition_cache_key()
    {
        return $this->cache_key_prefix . $this->table . '_' . md5(serialize($this->conditions));
    }
    
    protected function _add_id($id = '')
    {
        if((int)$id == (string)$id)
        {
            $this->ids[] = (int)$id;
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    // Metódy vracajúce počet riadkov

    function get_rows()
    {
        return (int) count($this->get_ids());
    }

    // Metódy vracajúce dáta

    function get_fields()
    {
        if($this->cache_table_fields)
        {
            $key = $this->cache_table_filtered_fields_prefix . $this->table;
            $cached_filtered_fields = $this->cache->get($key);
            
            if(is_array($cached_filtered_fields))
            {
                return $cached_filtered_fields;
            }
            
            else
            {
                $filtered_fields = $this->_filter_fields($this->fields);
                $this->cache->save($key, $filtered_fields);
                return $filtered_fields;
            }
        }
        
        elseif(!is_array($this->fields_filtered))
        {
            $this->fields_filtered = $this->_filter_fields($this->fields);
        }

        return $this->fields_filtered;
    }
    
    function get_normal_fields()
    {
        $fields = array();
        
        foreach($this->get_fields() as $field)
        {
            if(substr($field, 0, 1) != '_')
            {
                $fields[] = $field;
            }
        }
        
        return $fields;
    }
    
    function get_lang_fields($with_underscores = TRUE)
    {
        $fields = array();
        
        foreach($this->get_fields() as $field)
        {
            if(substr($field, 0, 1) == '_')
            {
                $fields[] = ($with_underscores) ? $field : substr($field, 1);
            }
        }
        
        return $fields;
    }

    function get_col_name($col = '')
    {
        return (strlen($col) > 0) ? @$this->col[$col] : $this->col;
    }

    function field_exists($field)
    {
        return (bool) in_array($field, $this->fields, TRUE);
    }

    function item_exists($item_id)
    {
        return (bool) (((string)(int)$item_id == ((string)$item_id)) && in_array((int)$item_id, $this->ids, TRUE));
    }
    
    function get_data()
    {
        $data = array();
        
        foreach($this->get_ids() as $id)
        {
            $data[$id] = $this->get_item($id);
        }
        
        $this->delete_conditions();
        
        return $data;
    }
    
    function get_all_data()
    {
        return $this->data;
    }

    function get_data_in_col($field)
    {
        if($this->field_exists($field) === TRUE)
        {
            if($this->cache_data_in_col)
            {
                $key = $this->cache_data_in_col_prefix . $this->table . '_' . ((substr($field, 0, 1) == '_') ? lang() . $field : $field) . $this->encrypt->sha1(serialize($this->get_conditions()));
                
                $cached_data_in_col = $this->cache->get($key);
                
                if(is_array($cached_data_in_col))
                {
                    return $cached_data_in_col;
                }
                else
                {
                    $data_in_col = $this->_get_data_in_col($field);
                    $this->cache->save($key, $data_in_col);
                    return $data_in_col;
                }
            }
            else
            {
                return $this->_get_data_in_col($field);
            }
        }
        else
        {
            return $this->show_error('Nepodarilo sa získať údaje zo stĺpca <strong>' . $field . '</strong>, pretože neexistuje.');
        }
    }

    protected function _get_data_in_col($field)
    {
        $cache_key = $field . $this->encrypt->sha1(serialize($this->get_conditions()));
        
        if(!isset($this->data_in_col[$cache_key]))
        {
            if($this->table != $this->cms->model->system_table('langs') && $this->_is_multilang_field($field))
            {
                $field = substr($this->_get_clean_field($field), 1);
                $data =& $this->lang_data[lang_id($this->_get_field_lang($field))];
            }
            else
            {
                $data =& $this->data;
            }

            if(@count($data) == 0)
            {
                $data_in_col = array();
            }
            else
            {
                $data_in_col = array();
                
                foreach($this->get_ids() as $item_id)
                {
                    $data_in_col[$item_id] = @$data[$item_id]->$field;
                }
                
            }
            
            $this->data_in_col[$cache_key] = $data_in_col;
        }

        return $this->data_in_col[$cache_key];
    }
    
    protected function _unset_data_in_col()
    {
        $this->data_in_col = array();
    }
    
    function __get($item_id)
    {
        if(is_numeric($item_id) && $this->item_exists($item_id))
        {
            return $this->get_item($item_id);
        }
        else
        {
            return parent::__get($item_id);
        }
    }
    
    function __set($item_id, $data)
    {
        if($this->item_exists($item_id))
        {
            return $this->set_item_data($item_id, $data);
        }

        else
        {
            return parent::__get($item_id);
        }
    }

    function get_item_data($item_id, $field = '')
    {
        if($this->item_exists($item_id) === TRUE)
        {
            $is_multilingual_field = FALSE;
            
            if($this->table != $this->cms->model->system_table('langs'))
            {
                if(substr($field, 0, 1) == '_')
                {
                    $is_multilingual_field = TRUE;
                    $field = substr($field, 1);
                    $field_exists = $this->field_exists('_' . $field);
                    $data =& $this->lang_data[lang_id()];
                    $lang = lang();
                }
                else
                {
                    foreach($this->s_langs_model->get_data_in_col('lang') as $lang)
                    {
                        $lang = $lang . '_';
                        if(substr($field, 0, strlen($lang)) == $lang)
                        {
                            $is_multilingual_field = TRUE;
                            $field = substr($field, strlen($lang));
                            $field_exists = $this->field_exists('_' . $field);
                            $data =& $this->lang_data[lang_id(substr($lang, 0, -1))];
                            break;
                        }
                    }

                    $lang = substr($lang, 0, -1);
                }
            }
            
            if(!$is_multilingual_field)
            {
                $field_exists = $this->field_exists($field);
                
                if(substr($field, 0, 1) == '_')
                {
                    $field = substr($field, 1);
                    $data =& $this->lang_data[lang_id()];
                }
                else
                {
                    $data =& $this->data;
                }
            }
            
            if($field_exists === TRUE)
            {
                if(isset($data[$item_id]->$field))
                {
                    return $data[$item_id]->$field;
                }
                else
                {
                    if($this->table != $this->cms->model->system_table('langs'))
                    {
                        $default_lang = default_lang();

                        if($is_multilingual_field && $lang != $default_lang)
                        {
                            $default_lang_value = $this->get_item_data($item_id, $default_lang . '_' . $field);
                            $this->set_item_data($item_id, array($lang . '_' . $field => $default_lang_value), TRUE);
                            return $default_lang_value;
                        }
                    }
                    
                    return NULL;
                }
            }
            else
            {
                return $this->show_error('Nepodarilo sa získať údaje z položky <strong>' . $item_id . '</strong>, pretože stĺpec <strong>' . $field . '</strong> neexistuje.');
            }
        }
        else
        {
            return $this->show_error('Nepodarilo sa získať údaje (<strong>' . $field . '</strong>) z položky <strong>' . $item_id . '</strong>, pretože neexistuje.');
        }
    }
    
    function add_lang_values($item_id = '')
    {
        if(!$this->item_exists($item_id)) return FALSE;
        if(strlen($this->lang_table) == 0 || $this->table == $this->cms->model->system_table('langs')) return FALSE;
        
        $default_lang_id = default_lang_id();
        
        if(!isset($this->lang_data[$default_lang_id][$item_id])) $this->lang_data[$default_lang_id][$item_id] = array();
        
        $default_lang_data = @array_combine($this->get_lang_fields(FALSE), (array)$this->lang_data[$default_lang_id][$item_id]);
        
        foreach($this->s_langs_model->get_data_in_col('lang') as $lang_id => $lang)
        {
            if($lang_id == $default_lang_id) continue;
            
            if(!isset($this->lang_data[$lang_id][$item_id]))
            {
                $data = $default_lang_data;
                
                $data[$this->col['id']] = $item_id;
                $data[$this->col['lang_id']] = $lang_id;
                $this->db->insert($this->lang_table, $data);
                $this->lang_data[$lang_id][$item_id] = (object)$default_lang_data;
            }
        }
        
        $this->_delete_cache();
        $this->_delete_data_in_col_cache();
        $this->_delete_conditions_cache();

        $this->_unset_data_in_col();
        
        return TRUE;
    }
    
    function get_item($item_id)
    {
        if($this->item_exists($item_id) === TRUE)
        {
            $data = $this->data[$item_id];
            
            if(strlen($this->lang_table))
            {
                $this->add_lang_values($item_id);
                
                $langs = $this->s_langs_model->get_data_in_col('lang');
                
                foreach($this->get_lang_fields() as $lang_field)
                {
                    $data->$lang_field = @$this->lang_data[lang_id()][$item_id]->{substr($lang_field, 1)};
                    
                    foreach($langs as $lang_id => $lang)
                    {
                        $data->{$lang . $lang_field} = @$this->lang_data[$lang_id][$item_id]->{substr($lang_field, 1)};
                    }
                }
            }
            
            return $data;
        }
        else
        {
            return $this->show_error('Nepodarilo sa získať údaje (<strong>všetky</strong>) z položky <strong>' . $item_id . '</strong>, pretože neexistuje.');
        }
    }

    function item_movable_up($item_id)
    {
        if($this->is_ordering())
        {
            if($this->item_exists($item_id) === TRUE)
            {
                if($this->is_cat_ordering())
                {
                    $this->save_conditions('item_movable_up');
                    $this->delete_conditions();
                    
                    $this->where($this->col['order_cat'], '=', $this->get_item_data($item_id, $this->col['order_cat']));
                    
                    $list = $this->get_ids();
                    
                    $this->load_conditions('item_movable_up', TRUE);
                }
                
                else
                {
                    $list = $this->get_all_ids();
                }
                
                $item_index = array_search($item_id, $list);

                if(isset($list[$item_index - 1]))
                {
                    return $list[$item_index - 1];
                }

                else
                {
                    return FALSE;
                }
            }
            else
            {
                return $this->show_error('Nepodarilo sa zistiť, či sa dá položka <strong>' . $item_id . '</strong>, presunúť hore, pretože neexistuje.');
            }
        }
        else
        {
            return $this->show_error('Tabuľka <strong>' . $this->table . '</strong> neobsahuje stĺpec <strong>' . $this->col['order'] . '</strong>, takže nemôžete volať metódu <strong>item_movable_up()</strong>.');
        }
    }

    function item_movable_down($item_id)
    {
        if($this->is_ordering())
        {
            if($this->item_exists($item_id) === TRUE)
            {
                if($this->is_cat_ordering())
                {
                    $this->save_conditions('item_movable_up');
                    $this->delete_conditions();
                    
                    $this->where($this->col['order_cat'], '=', $this->get_item_data($item_id, $this->col['order_cat']));
                    
                    $list = $this->get_ids();
                    
                    $this->load_conditions('item_movable_up', TRUE);
                }
                
                else
                {
                    $list = $this->get_all_ids();
                }
                
                $item_index = array_search($item_id, $list);

                if(isset($list[$item_index + 1]))
                {
                    return $list[$item_index + 1];
                }

                else
                {
                    return FALSE;
                }
            }
            else
            {
                return $this->show_error('Nepodarilo sa zistiť, či sa dá položka <strong>' . $item_id . '</strong>, presunúť dole, pretože neexistuje.');
            }
        }
        else
        {
            return $this->show_error('Tabuľka <strong>' . $this->table . '</strong> neobsahuje stĺpec <strong>' . $this->col['order'] . '</strong>, takže nemôžete volať metódu <strong>item_movable_up()</strong>.');
        }
    }
    
    function is_ordering()
    {
        return (strlen(@$this->col['order']) > 0);
        //return (bool)$this->field_exists($this->cat['order']);
    }
    
    function is_cat_ordering()
    {
        return (strlen(@$this->col['order_cat']) > 0);
        //return (bool)$this->field_exists($this->cat['order_cat']);
    }
    
    function get_tables()
    {
        if($this->cache_table_fields)
        {
            $cached_list_tables = $this->cache->get('list_tables');
            if(is_array($cached_list_tables))
            {
                return $cached_list_tables;
            }
            else
            {
                $list_tables = $this->db->list_tables();
                $this->cache->save('list_tables', $list_tables);
                return $list_tables;
            }
        }
        else
        {
            return $this->db->list_tables();
        }
    }
    
    function table_exists($table = '')
    {
        $table = $this->db->dbprefix . $table;
        return in_array($table, $this->get_tables());
    }

    // Metódy pridávajúce dáta

    function add_item($data = array())
    {
        $is_lang_table = (strlen($this->lang_table) > 0);

        if(is_array($data))
        {
            if($this->field_exists($this->col['lastmod']) && !isset($data[$this->col['lastmod']]))
            {
                $data[$this->col['lastmod']] = time();
            }
            
            // TODO: toto (zakomentovany kod o riadok dole) by teoreticky mohlo byt viazane na tabulku (pri niektorej je to potrebne pri idnej nie)
            // if($this->is_cat_ordering() && !isset($data[$this->col['order_cat']])) return $this->show_error('Položku sa nepodarilo pridať, pretože nová položka neobsahuje stĺpec <strong>' . $this->col['order_cat'] . '</strong>.');
            
            $data = $this->_prepare_data($data);
            $lang_data = array();
            
            foreach($data as $field => $value)
            {
                if($this->field_exists($field) !== TRUE) return $this->show_error('Nová položka obsahuje stĺpec <strong>' . $field . '</strong>, ktorý nie je definovaný v tabuľke <strong>' . $this->table . '</strong>.');

                if(substr($field, 0, 1) == '_')
                {
                    unset($data[$field]);
                    $field = lang() . $field;
                    $data[$field] = $value;
                }

                if($is_lang_table)
                {
                    foreach($this->s_langs_model->get_data_in_col('lang') as $lang)
                    {
                        $lang = $lang . '_';
                        if(substr($field, 0, strlen($lang)) == $lang)
                        {
                            unset($data[$field]);
                            $lang_id = intval(lang_id(substr($field, 0, strlen(substr($lang, 0, -1)))));
                            $field = substr($field, strlen($lang));
                            $lang_data[$lang_id][$field] = $value;
                        }
                    }
                }
            }
            
            if(!isset($data[$this->col['id']]))
            {
                // Try to use minimal ID
                if($this->use_min_id())
                {
                    $data[$this->col['id']] = $this->get_min_id();
                }
                else
                {
                    $data[$this->col['id']] = NULL;
                }
            }
            
            $this->db->insert($this->table, $data);
            
            $col_id = $this->get_col('id');
            $this->insert_id = (isset($data[$col_id])) ? $data[$col_id] : $this->db->insert_id();
            
            if($is_lang_table)
            {
                $item_id = $this->insert_id;
                
                foreach($this->s_langs_model->get_ids() as $lang_id)
                {
                    if(isset($lang_data[$lang_id]))
                    {
                        $data = $lang_data[$lang_id];
                    }
                    else
                    {
                        $data = array();
                    }
                    
                    if(count($data) == 0) continue;
                    
                    $update_or_add =& $this->db;

                    $data = $this->_prepare_lang_data($data, TRUE);
                    
                    $data[$this->col['id']] = $item_id;
                    $data[$this->col['lang_id']] = $lang_id;
                    $this->db->insert($this->lang_table, $data);
                    
                    // Pridanie dát
                    if($this->reinit_add)
                    {
                        foreach($data as $field => $value)
                        {
                            $this->lang_data[$lang_id][$item_id]->$field = $value;
                        }
                    }
                }
            }
            
            if($this->is_ordering())
            {
                $update =& $this->db;
                $update = $update->where($this->col['id'], $this->insert_id);
                $update = $update->update($this->table, array($this->col['order'] => $this->insert_id));
            }
            
            // Pridanie ID novej položky do polí IDs
            if($this->reinit)
            {
                $this->_add_id($this->insert_id);
            }
            
            // Pridanie dát
            if($this->reinit_add)
            {
                $reinit_add =& $this->db;
                $cache_on = $this->db->cache_on;
                $reinit_add->cache_on = FALSE;
                $reinit_add = $reinit_add->where($this->col['id'], $this->insert_id);
                $reinit_add = $reinit_add->get($this->table);
                $result = $reinit_add->result();

                $this->data[$this->insert_id] = @$result[0];

                $this->db->cache_on = $cache_on;
            }
            
            $this->_delete_cache();
            $this->_delete_data_in_col_cache();
            $this->_delete_conditions_cache();

            $this->_unset_data_in_col();
            
            return TRUE;
        }
        else
        {
            return $this->show_error('Novú položku sa nepodarilo uložiť, pretože zadané dáta nie sú zapísané v poli.');
        }
    }

    // Metódy upravujúce dáta

    function set_item_data($item_id, $data, $force_update = FALSE)
    {
        if($this->item_exists($item_id) === TRUE)
        {
            if(is_array($data) === TRUE)
            {
                $is_lang_table = (strlen($this->lang_table) > 0);
                $data = $this->_prepare_data($data);
                $lang_data = array();
                
                foreach($data as $field => $value)
                {
                    if($this->field_exists($field) !== TRUE) return $this->show_error('Upravovaná položka obsahuje stĺpec <strong>' . $field . '</strong>, ktorý nie je definovaný v tabuľke <strong>' . $this->table . '</strong>.');
                    
                    if(substr($field, 0, 1) == '_')
                    {
                        unset($data[$field]);
                        $field = lang() . $field;
                        $data[$field] = $value;
                    }
                    
                    if($is_lang_table)
                    {
                        foreach($this->s_langs_model->get_data_in_col('lang') as $lang)
                        {
                            $lang = $lang . '_';
                            if(substr($field, 0, strlen($lang)) == $lang)
                            {
                                unset($data[$field]);
                                $lang_id = intval(lang_id(substr($field, 0, strlen(substr($lang, 0, -1)))));
                                $field = substr($field, strlen($lang));
                                $lang_data[$lang_id][$field] = $value;
                            }
                        }
                    }
                }
                
                if(!$force_update)
                {
                    foreach($data as $field => $value)
                    {
                        if($this->get_item_data($item_id, $field) === $value)
                        {
                            unset($data[$field]);
                        }
                    }
                }
                
                // Upravovanie normálnej tabuľky
                if(count($data) > 0)
                {
                    if($this->field_exists($this->col['lastmod']) && !isset($data[$this->col['lastmod']]))
                    {
                        $data[$this->col['lastmod']] = time();
                    }
                    
                    $update =& $this->db;
                    $update = $update->where($this->col['id'], $item_id);
                    $update = $update->update($this->table, $data);

                    if($update !== TRUE)
                    {
                        return $this->show_error('Položku <strong>' . $item_id . '</strong> sa nepodarilo upraviť. Prosím skúste to neskôr.');
                    }
                    else
                    {
                        if($this->reinit)
                        {
                            foreach($data as $field => $value)
                            {
                                $this->data[$item_id]->$field = $value;
                            }
                        }
                    }
                }
                
                // Upravovanie jazykovej tabuľky
                if($is_lang_table && count($lang_data) > 0)
                {
                    foreach($lang_data as $lang_id => $data)
                    {
                        $update_or_add =& $this->db;
                        
                        if(isset($this->lang_data[$lang_id][$item_id]))
                        {
                            $data = $this->_prepare_lang_data($data);
                            
                            $update_or_add = $update_or_add->where($this->col['id'], $item_id);
                            $update_or_add = $update_or_add->where($this->col['lang_id'], $lang_id);
                            $update_or_add = $update_or_add->update($this->lang_table, $data);
                        }
                        elseif(count($data) > 0)
                        {
                            $data = $this->_prepare_lang_data($data, TRUE);
                            
                            $data[$this->col['id']] = $item_id;
                            $data[$this->col['lang_id']] = $lang_id;
                            $update_or_add = $update_or_add->insert($this->lang_table, $data);
                        }

                        if($update_or_add !== TRUE)
                        {
                            return $this->show_error('Položku <strong>' . $item_id . '</strong> sa nepodarilo upraviť. Prosím skúste to neskôr.');
                        }
                        else
                        {
                            if($this->reinit)
                            {
                                foreach($data as $field => $value)
                                {
                                    @$this->lang_data[$lang_id][$item_id]->$field = $value;
                                }
                            }
                        }
                    }
                }

                $this->_delete_cache();
                $this->_delete_data_in_col_cache();
                $this->_delete_conditions_cache();

                $this->_unset_data_in_col();
                
                return TRUE;
            }
            else
            {
                return $this->show_error('Položka <strong>' . $item_id . '</strong>, nemohla byť upravená, pretože upravované dáta musia byť zapísané v poli.');
            }
        }
        else
        {
            return $this->show_error('Položka <strong>' . $item_id . '</strong>, nemohla byť upravená, pretože neexistuje.');
        }
    }

    function item_move_up($item_id)
    {
        if($this->is_ordering())
        {
            if($this->item_exists($item_id) === TRUE)
            {
                $movable = $this->item_movable_up($item_id);
                if($movable !== FALSE)
                {
                    return $this->_change_items_ordering($item_id, $movable);
                }
                else
                {
                    return $this->show_error('Položka <strong>' . $item_id . '</strong> sa nedá presunúť hore, pretože je úplne hore.');
                }
            }
            else
            {
                return $this->show_error('Položka <strong>' . $item_id . '</strong> sa nedá presunúť hore, pretože neexistuje.');
            }
        }
        else
        {
            return $this->show_error('Tabuľka <strong>' . $this->table . '</strong> neobsahuje stĺpec <strong>' . $this->col['order'] . '</strong>, takže nemôžete volať metódu <strong>item_move_up()</strong>.');
        }
    }

    function item_move_down($item_id)
    {
        if($this->is_ordering())
        {
            if($this->item_exists($item_id) === TRUE)
            {
                $movable = $this->item_movable_down($item_id);
                if($movable !== FALSE)
                {
                    return $this->_change_items_ordering($item_id, $movable);
                }
                else
                {
                    return $this->show_error('Položka <strong>' . $item_id . '</strong> sa nedá presunúť dole, pretože je úplne dole.');
                }
            }
            else
            {
                return $this->show_error('Položka <strong>' . $item_id . '</strong> sa nedá presunúť dole, pretože neexistuje.');
            }
        }
        else
        {
            return $this->show_error('Tabuľka <strong>' . $this->table . '</strong> neobsahuje stĺpec <strong>' . $this->col['order'] . '</strong>, takže nemôžete volať metódu <strong>item_move_down()</strong>.');
        }
    }

    // Metódy odstraňujúce dáta

    function delete_item($item_id)
    {
        if($this->item_exists($item_id) === TRUE)
        {
            $delete =& $this->db;
            $delete = $delete->where($this->col['id'], $item_id);
            $delete = $delete->delete($this->table);

            if(!$delete)
            {
                return $this->show_error('Položku <strong>' . $item_id . '</strong> sa nepodarilo odstrániť. Prosím skúste to neskôr.');
            }
            else
            {
                if($this->reinit)
                {
                    $this->ids = remove_from_array($this->ids, $item_id);
                    $this->rows = count($this->ids);
                }

                $this->_delete_cache();
                $this->_delete_data_in_col_cache();
                $this->_delete_conditions_cache();

                $this->_unset_data_in_col();

                return TRUE;
            }
        }
        else
        {
            return $this->show_error('Položka <strong>' . $item_id . '</strong> sa nedá odstrániť, pretože neexistuje.');
        }
    }
    
    function delete()
    {
        if(!$this->conditions_exists())
        {
            return $this->show_error('Metóda <strong>delete</strong> nemohla byť vykonaná, pretože neboli zadané žiadne podmienky.');
        }
        
        else
        {
            $ids = $this->get_ids();
            
            if(count((array)$ids) > 0)
            {
                $delete =& $this->db;
                $delete = $delete->where_in($this->col['id'], $ids);
                $delete = $delete->delete($this->table);

                if($this->reinit)
                {
                    $this->ids = array_values(array_diff($this->ids, $ids));
                    $this->rows = count($this->ids);
                }
                
                $this->delete_conditions();
                $this->_delete_cache();
                $this->_delete_data_in_col_cache();
                $this->_delete_conditions_cache();

                $this->_unset_data_in_col();
            }
        }
    }

    function truncate()
    {
        if($this->db->truncate($this->table) !== TRUE)
        {
            return $this->show_error('Tabuľku <strong>' . $this->table . '</strong> sa nepodarilo vyprázdniť. Prosím skúste to neskôr.');
        }
        else
        {
            $this->_delete_cache();
            $this->_delete_data_in_col_cache();
            $this->_delete_conditions_cache();

            $this->_unset_data_in_col();

            return TRUE;
        }
    }

    // Informačné metódy

    function insert_id()
    {
        return $this->insert_id;
    }

    // Pomocné metódy

    function fields_data($table = '')
    {
        $field_data = array();
        foreach((array)$this->db->field_data((strlen($table) > 0) ? $table : $this->table) as $field) $field_data[$field->name] = $field;
        return $field_data;
    }

    function field_data($field = '', $table = '')
    {
        $fields_data = $this->fields_data($table);
        return (isset($fields_data[$field])) ? $fields_data[$field] : FALSE;
    }
    
    function use_min_id()
    {
        return $this->cms->model->use_min_id($this->table);
    }
    
    function get_min_id()
    {
        $min_id = 1;
        while($this->item_exists($min_id)) $min_id++;
        return $min_id;
    }
    
    protected function _prepare_lang_data($data, $add_fields = FALSE, $fields = NULL)
    {
        if($fields == NULL)
        {
            $fields = $this->get_lang_fields();
        }
        
        foreach($fields as $field)
        {
            $field = substr($field, 1);
            if($add_fields)
            {
                if(strlen(@$data[$field]) == 0) $data[$field] = '';
            }
            else
            {
                if(isset($data[$field]) && strlen($data[$field]) == 0) $data[$field] = '';
            }
        }
        
        //if(!$add_fields && implode('', $data) == '') $data = array();
        
        return $data;
    }
    
    protected function _prepare_data($data)
    {
        foreach($data as $field => $value)
        {
            if(is_bool($value))
            {
                $data[$field] = (int)$value;
            }
            
            if(is_string($value) && strlen($value) == 0)
            {
                $data[$field] = NULL;
            }
        }

        return $data;
    }

    protected function _change_items_ordering($item_id_1, $item_id_2)
    {
        if($this->is_ordering())
        {
            if($this->item_exists($item_id_1) === TRUE)
            {
                if($this->item_exists($item_id_2) === TRUE)
                {
                    $order_1 = $this->get_item_data($item_id_1, $this->col['order']);
                    $order_2 = $this->get_item_data($item_id_2, $this->col['order']);

                    $status = TRUE;

                    if($this->use_null_value_while_ordering)
                    {
                        if(!$this->set_item_data($item_id_1, array($this->col['order'] => NULL))) $status = FALSE;
                        if(!$this->set_item_data($item_id_2, array($this->col['order'] => NULL))) $status = FALSE;
                    }

                    if(!$this->set_item_data($item_id_1, array($this->col['order'] => $order_2))) $status = FALSE;
                    if(!$this->set_item_data($item_id_2, array($this->col['order'] => $order_1))) $status = FALSE;

                    if($status === TRUE)
                    {
                        if($this->reinit_order)
                        {
                            $item_id_1_pos = array_search($item_id_1, $this->ids);
                            $item_id_2_pos = array_search($item_id_2, $this->ids);
                            
                            $this->ids[$item_id_1_pos] = $item_id_2;
                            $this->ids[$item_id_2_pos] = $item_id_1;
                        }
                        
                        $this->_delete_cache();
                        $this->_delete_data_in_col_cache();
                        $this->_delete_conditions_cache();

                        $this->_unset_data_in_col();
                        
                        return TRUE;
                    }
                    else
                    {
                        return $this->show_error('Položky <strong>' . $item_id_1 . '</strong> a <strong>' . $item_id_2 . '</strong> sa nepodarilo preradiť. Prosím skúste to neskôr.');
                    }
                }
                else
                {
                    return $this->show_error('Položky <strong>' . $item_id_1 . '</strong> a <strong>' . $item_id_2 . '</strong>, nemohli byť preradené, preťože položka <strong>' . $item_id_2 . '</strong> neexistuje.');
                }
            }
            else
            {
                return $this->show_error('Položky <strong>' . $item_id_1 . '</strong> a <strong>' . $item_id_2 . '</strong>, nemohli byť preradené, preťože položka <strong>' . $item_id_1 . '</strong> neexistuje.');
            }
        }
        else
        {
            return $this->show_error('Tabuľka <strong>' . $this->table . '</strong> neobsahuje stĺpec <strong>' . $this->col['order'] . '</strong>, takže nemôžete preraďovať položky.');
        }
    }
    
    protected function _filter_fields($fields = array())
    {
        foreach((array)$fields as $field_index => $field)
        {
            foreach($this->s_langs_model->get_data_in_col('lang') as $lang)
            {
                $lang = $lang . '_';
                if(substr($field, 0, strlen($lang)) == $lang)
                {
                    unset($fields[$field_index]);
                }
            }
        }
        
        return $fields;
    }
    
    protected function _get_clean_field($field = '')
    {
        foreach($this->s_langs_model->get_data_in_col('lang') as $lang)
        {
            $lang = $lang . '_';
            if(substr($field, 0, strlen($lang)) == $lang)
            {
                $field = substr($field, strlen($lang) - 1);
            }
        }
        
        return $field;
    }
    
    protected function _is_multilang_field($field = '')
    {
        return (bool)(substr($this->_get_clean_field($field), 0, 1) == '_');
    }
    
    protected function _get_field_lang($field = '')
    {
        return substr($field, 0, strlen($field) - strlen($this->_get_clean_field($field)));
    }

    protected function show_error($message, $status_code = 500)
    {
        if(!$this->show_errors) return FALSE;
        $heading  = 'Model tabuľky <strong>' . $this->table . '</strong> hlási túto chybu.';
        show_error($message, $status_code, $heading);
        return FALSE;
    }

    protected function _delete_cache($sql = '')
    {
        if(!$this->db->cache_on) return TRUE;

        // Zmazanie cache SQL dopytov
        if(strlen($sql) > 0)
        {
            $this->db->delete_sql_or_table_cache(str_replace(array(self::THIS_TABLE, self::THIS_LANG_TABLE), array($this->table, $this->lang_table), $sql));
        }
        else
        {
            $order_columns = array();

            if($this->is_ordering())
            {
                if($this->is_cat_ordering())
                {
                    $order_columns[$this->col['order_cat']] = $this->order_type;
                }
                $order_columns[$this->col['order']] = $this->order_type;
            }

            $this->db->delete_sql_or_table_cache($this->table, $order_columns);
            $this->db->delete_sql_or_table_cache($this->lang_table, $order_columns);
        }
    }
    
    protected function _delete_conditions_cache($table = '')
    {
        if(!$this->db->cache_on) return TRUE;
        
        // Zmazanie cache podmienkových výsledkov
        $table = (strlen($table) > 0) ? $table : $this->table;
        $this->load->helper('file');
        $cache_path = $this->cache->file->get_cache_path();
        
        foreach((array)get_filenames($cache_path) as $filename)
        {
            $prefix = $this->cache_key_prefix . $table;
            if(substr($filename, 0, strlen($prefix)) == $prefix)
            {
                $this->cache->delete($filename);
            }
        }
    }
    
    protected function _delete_data_in_col_cache($table = '')
    {
        if(!$this->cache_data_in_col) return TRUE;

        $table = (strlen($table) > 0) ? $table : $this->table;
        $this->load->helper('file');
        $cache_path = $this->cache->file->get_cache_path();
        
        foreach((array)get_filenames($cache_path) as $filename)
        {
            $prefix = $this->cache_data_in_col_prefix . $this->table . '_';
            if(substr($filename, 0, strlen($prefix)) == $prefix)
            {
                $this->cache->delete($filename);
            }
        }
    }
    
    protected function _delete_table_list_cache()
    {
        if(!$this->cache_table_fields) return TRUE;

        $this->cache->delete('list_tables');
    }

    protected function _delete_fields_cache($table = '')
    {
        if(!$this->cache_table_fields) return TRUE;

        $this->_delete_cache('SHOW COLUMNS FROM `__TABLE__`');
        $this->_delete_cache('SHOW COLUMNS FROM `__LANG_TABLE__`');

        $table = (strlen($table) > 0) ? $table : $this->table;
        $this->cache->delete($this->cache_table_fields_prefix . $table);
        $this->cache->delete($this->cache_table_fields_prefix . $table . $this->lang_table_suffix);
        $this->cache->delete($this->cache_table_filtered_fields_prefix . $this->table);
    }

    function recache()
    {
        $this->_delete_cache();
        $this->_delete_data_in_col_cache();
        $this->_delete_conditions_cache();
        $this->_delete_fields_cache();
        $this->_delete_table_list_cache();

        $this->_unset_data_in_col();
    }

    function recache_sortable()
    {
        $this->_delete_cache();
        $this->_delete_data_in_col_cache();
        $this->_delete_conditions_cache();

        $this->_unset_data_in_col();
    }

    /* Metódy pracujúce s DBForge */

    function add_column($column_name = '', $column_data = array())
    {
        if($this->is_protected_column($column_name))
        {
            return $this->show_error("Stĺpec s názvom <strong>" . $column_name . "</strong> nie je povolené pridať.");
        }
        
        // Load dbforge
        $this->load->dbforge();

        // Load language model
        $this->cms->model->load_system('langs');

        $status = TRUE;
        $is_lang_table = (strlen($this->lang_table) > 0);
        
        // New column must be NULL
        // $column_data['null'] = TRUE;

        if($this->field_exists($column_name))
        {
            return $this->show_error("Stĺpec s názvom <strong>" . $column_name . '</strong> sa nepodarilo pridať, pretože už existuje stĺpec s rovnakým názvom.');
        }

        if(substr($column_name, 0, 1) == '_')
        {
            if(!$is_lang_table) $this->create_lang_table();
            if(!$this->dbforge->add_column($this->lang_table, array(substr($column_name, 1) => $column_data))) $status = FALSE;
        }
        else
        {
            if(!$this->dbforge->add_column($this->table, array($column_name => $column_data))) $status = FALSE;
        }
        
        $this->_delete_fields_cache();
        $this->_delete_conditions_cache();
        
        $this->_init_fields();

        return $status;
    }

    function drop_column($column)
    {
        if(!$this->field_exists($column))
        {
            return $this->show_error("Stĺpec <strong>" . $column . "</strong> sa nepodarilo odstrániť, pretože neexistuje.");
        }
        
        if($this->is_protected_column($column))
        {
            return $this->show_error("Stĺpec <strong>" . $column . "</strong> nie je povolené odstrániť.");
        }
        
        // Load dbforge
        $this->load->dbforge();
        
        $status = TRUE;
        
        if(substr($column, 0, 1) == '_')
        {
            if(!$this->dbforge->drop_column($this->lang_table, substr($column, 1))) $status = FALSE;
        }
        else
        {
            if(!$this->dbforge->drop_column($this->table, $column)) $status = FALSE;
        }
        
        $this->_delete_fields_cache();
        $this->_delete_conditions_cache();
        
        $this->_init_fields();
        
        return $status;
    }
    
    function is_protected_column($column)
    {
        return (in_array($column, $this->col));
    }

    function drop()
    {
        $status = TRUE;
        
        $this->load->dbforge();
        
        $lang_table = $this->lang_table;
        if($this->table_exists($lang_table) && $this->dbforge->drop_table($lang_table) !== TRUE)
        {
            return $this->show_error('Tabuľku <strong>' . $lang_table . '</strong> sa nepodarilo odstrániť. Prosím skúste to neskôr.');
            $status = FALSE;
        }
        elseif($this->dbforge->drop_table($this->table) !== TRUE)
        {
            return $this->show_error('Tabuľku <strong>' . $this->table . '</strong> sa nepodarilo odstrániť. Prosím skúste to neskôr.');
            $status = FALSE;
        }
        
        $this->_delete_cache();
        $this->_delete_data_in_col_cache();
        $this->_delete_conditions_cache();

        $this->_unset_data_in_col();

        return $status;
    }
    
    protected function _id_column_has_primary_key($table = '')
    {
        $cache_on = $this->db->cache_on;
        $this->db->cache_on = FALSE;
        $has_primary_key = FALSE;

        foreach($this->db->field_data((strlen($table) > 0) ? $table : $this->table) as $field_data)
        {
            if($field_data->name == $this->col['id'])
            {
                $has_primary_key = $field_data->primary_key;
                break;
            }
        }

        $this->db->cache_on = $cache_on;
        
        return $has_primary_key;
    }
    
    function create_lang_table($table = '')
    {
        if(strlen($table) == 0) $table = $this->table;
        $lang_table = $table . $this->lang_table_suffix;
        
        if(!$this->table_exists($lang_table))
        {
            if($this->_id_column_has_primary_key())
            {
                // Load dbforge
                $this->load->dbforge();

                $fields = array(
                    $this->col['id'] => array(
                        'type' => 'SMALLINT',
                        'constraint' => 5, 
                        'unsigned' => TRUE
                        ),
                    $this->col['lang_id'] => array(
                        'type' => 'SMALLINT',
                        'constraint' => 5, 
                        'unsigned' => TRUE
                        )
                );

                $this->dbforge->add_field($fields);
                
                $this->dbforge->add_key($this->col['id']);
                $this->dbforge->add_key($this->col['lang_id']);
                $this->dbforge->create_table($lang_table);
                
                $this->lang_table = $lang_table;
                
                $this->recache();
                
                $this->cms->model->load_system('langs');

                $this->db->query('ALTER TABLE `' . $this->db->dbprefix . $table . '` ENGINE = INNODB');
                $this->db->query('ALTER TABLE `' . $this->db->dbprefix . $lang_table . '` ENGINE = INNODB');
                
                // TODO: tento riadok sposobuje chybu v pripade ze sa datovy typ hlavnej tabulky nerovna datovemu typu langovej tabulky, cize SMAIILINT 5 UNSIGNED
                //       dalo by sa to riesit tak ze pri vytvarani langovej tabulky sa nepouzival datovy typ ID stlpca staticky ale skopiroval by sa z hlavnej tabulky
                $this->db->query('ALTER TABLE `' . $this->db->dbprefix . $lang_table . '` ADD FOREIGN KEY (`' . $this->col['id'] . '`) REFERENCES  `' . $this->db->dbprefix . $table . '` (`' . $this->col['id'] . '`) ON DELETE CASCADE ON UPDATE CASCADE');
                
                // TODO: tento riadok moze teoreticky sposobovat chybu v pripade ze datovy typ ID stlpca s_langs tabulky nie je SMALLINT 5 UNSIGNED
                //       vhodne riesenie by mohlo byt podobne ako to co je popisane vyssie
                $this->db->query('ALTER TABLE `' . $this->db->dbprefix . $lang_table . '` ADD FOREIGN KEY (`' . $this->col['lang_id'] . '`) REFERENCES  `' . $this->db->dbprefix . $this->cms->model->system_table('langs') . '` (`' . $this->s_langs_model->get_col('id') . '`) ON DELETE CASCADE ON UPDATE CASCADE');
            }
            else
            {
                $this->recache();
                return $this->show_error('Jazykovú tabuľku <strong>' . $lang_table . '</strong> k tabuľke <strong>' . $this->table . '</strong> sa nepodarilo vytvoriť, pretože tabuľka <strong>' . $this->table . '</strong> neobsahuje stĺpec <strong>' . $this->col['id'] . '</strong> ktorý by mal priradený primárny kľúč.');
            }
        }
    }
    
    function create_table($table = '', $if_not_exists = FALSE)
    {
        $this->dbforge->create_table($table, $if_not_exists);
        $this->recache();
    }
    
}