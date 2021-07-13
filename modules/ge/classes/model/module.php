<?php defined('SYSPATH') OR die('No direct access allowed.');

	/**
	 * Модель списка модулей сайта.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */


class Model_Module extends ORM {
	
	// связь с переводам заголовков модулей
	protected $_has_many = array (
		'sitemaps'	=> array('model' => 'sitemap', 'foreign_key' => 'module_id'),
		'trmodules' => array(),
	);
	
	// правила для валидации
    protected $_rules = array(

    	'name'			=> array('not_empty' => null, 'max_length' => array(30), 'regex' => array('/[A-Za-z0-9]+/')),
    	'type'			=> array('not_empty' => null, 'max_length' => array(30)),
    	'caption_field'	=> array('not_empty' => null, 'max_length' => array(30), 'regex' => array('/[A-Za-z0-9]+/')),
    	'controller'	=> array('max_length' => array(30), 'regex' => array('/[A-Za-z0-9]+/')),
    
    );
	
	protected $_callbacks = array
	(
		'name'	=> array('name_unique'),
	);
    
    /**
     * Перекрытие инициализации объекта
     */
	protected function _initialize()
	{
		if ($table_name = GE::pref('sys', 'module'))
		{
			$this->_table_name = $table_name;
		}
	 
		parent::_initialize();
	}	

	/**
	 * Возвращает список установленных модулей
	 */
	public function get_list(){
		$result = array();
		$translates = $this->trmodules->get_tree();
		$r = $this->find_all();
		foreach($r as $item){
			$item_data = $item->as_array();
			$item_data['child'] = $item->child_exists();
			$item_data['translate'] = (isset($translates[$item_data['id']]))? $translates[$item_data['id']]: array();
			$result[$item_data['id']] = $item_data;
		}
		return $result;
	}
	
	public function get_module($module){
		return $this->where('name', '=', $module)->find();
	}
	
	/**
	 * Возвращает возможные значения поля 'seo_priority'
	 */
	public function get_priority_options(){
		return $this->_table_columns['seo_priority']['options'];
	}
	
	/**
	 * Возвращает возможные значения поля 'seo_changefreq'
	 */
	public function get_changefreq_options(){
		return $this->_table_columns['seo_changefreq']['options'];
	}
	
	
	
	
	/**
	 * Валидация данных на создание записи
	 * 
	 * @param array $array
	 */
	public function validate_create($array) 
	{
		$array = Validate::factory($array)
					->rules('name', $this->_rules['name'])
					->rules('type', $this->_rules['type'])
					->filter('name', 'trim')
					->filter('type', 'trim');
 
		foreach ($this->_callbacks as $field => $callbacks){
			foreach ($callbacks as $callback){
				$array->callback($field, array($this, $callback));
			}
		}
		return $array;
	}
	
	/**
	 * Проверяет поле переданных данных на уникальность
	 * 
	 * @param Validate $array
	 * @param string $field
	 */
	public function name_unique(Validate $array, $field)
	{
		if ($this->unique_key_exists($array[$field])) {
			$array->error($field, 'name_unique', array($array[$field]));
		}
	}
	
	/**
	 * Проверяет существует ли запись с уникальным ключем
	 * 
	 * @param string $value
	 */
	public function unique_key_exists($value)
	{
		return (bool) DB::select(array('COUNT("*")', 'total_count'))
						->from($this->_table_name)
						->where('name', '=', $value)
						->execute($this->_db)
						->get('total_count');
	}
	
	public function child_exists() {
		return (bool) DB::select(array('COUNT("*")', 'total_count'))
						->from($this->_table_name)
						->where('parent_id', '=', $this->id)
						->execute($this->_db)
						->get('total_count');
	}
	
	
}