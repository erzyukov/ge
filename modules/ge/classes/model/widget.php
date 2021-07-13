<?php defined('SYSPATH') OR die('No direct access allowed.');

	/**
	 * Модель списка модулей сайта.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */


class Model_Widget extends ORM {
	
	// правила для валидации
    protected $_rules = array(

    	'name'			=> array('not_empty' => null, 'max_length' => array(30), 'regex' => array('/[A-Za-z0-9]+/')),
    
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
		if ($table_name = GE::pref('sys', 'widget'))
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
		$r = $this->find_all();
		foreach($r as $item){
			$result[] = $item->as_array();
		}
		return $result;
	}
	
	public function get_widget($widget){
		return $this->where('name', '=', $widget)->find();
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
	
	
	
}