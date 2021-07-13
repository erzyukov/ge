<?php defined('SYSPATH') OR die('No direct access allowed.');

	/**
	 * Модель языков сайта.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */


class Model_Lang extends ORM
{

	protected $_has_many = array(
		'trmodules' => array()
	);
	
	// правила для валидации
    protected $_rules = array(
        'title'			=> array('not_empty' => null, 'max_length' => array(50)),
        'short'			=> array('not_empty' => null, 'max_length' => array(10)),
    	'uri'			=> array('not_empty' => null, 'max_length' => array(5), 'alpha' => null, 'min_length' => array(2)),
        'date_format'	=> array('max_length' => array(100)),
    );
	
	protected $_callbacks = array
	(
		'uri'	=> array('uri_unique'),
	);
    
    /**
     * Перекрытие инициализации объекта
     */
	protected function _initialize()
	{
		if ($table_name = GE::pref('sys', 'lang'))
		{
			$this->_table_name = $table_name;
		}
	 
		parent::_initialize();
	}	

	
	/**
	 * Валидация данных на создание записи
	 * 
	 * @param array $array
	 */
	public function validate_create($array) 
	{
		$array = Validate::factory($array)
					->rules('title', $this->_rules['title'])
					->rules('short', $this->_rules['short'])
					->rules('uri', $this->_rules['uri'])
					->rules('date_format', $this->_rules['date_format'])
					->filter('title', 'trim')
					->filter('short', 'trim')
					->filter('uri', 'trim')
					->filter('date_format', 'trim');
 
		foreach ($this->_callbacks as $field => $callbacks){
			foreach ($callbacks as $callback){
				$array->callback($field, array($this, $callback));
			}
		}
					
		return $array;
	}
	
	/**
	 * Валидация данных на редактирование записи
	 * 
	 * @param array $array
	 */
	public function validate_edit($array) 
	{
		$array = Validate::factory($array)
					->rules('title', $this->_rules['title'])
					->rules('short', $this->_rules['short'])
					->rules('date_format', $this->_rules['date_format'])
					->filter('title', 'trim')
					->filter('short', 'trim')
					->filter('date_format', 'trim');
 
		return $array;
	}
 	
	/**
	 * Проверяет поле переданных данных на уникальность
	 * 
	 * @param Validate $array
	 * @param string $field
	 */
	public function uri_unique(Validate $array, $field)
	{
		if ($this->unique_key_exists($array[$field])) {
			$array->error($field, 'uri_unique', array($array[$field]));
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
						->where('uri', '=', $value)
						->execute($this->_db)
						->get('total_count');
	}
	
	
}