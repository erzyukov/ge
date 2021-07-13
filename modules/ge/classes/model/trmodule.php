<?php defined('SYSPATH') OR die('No direct access allowed.');

	/**
	 * Модель переводов заголовков списка модулей сайта.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */


class Model_Trmodule extends ORM
{
	// связь с модулем и языками сайта
	protected $_belongs_to = array(
		'module' => array('foreign_key' => 'module_id'),
		'lang' => array('foreign_key' => 'lang_id'),
	);
	
	// правила для валидации
    protected $_rules = array(

    	'title'			=> array('not_empty' => null, 'max_length' => array(255)),
    
    );

    /**
     * Перекрытие инициализации объекта
     */
	protected function _initialize()
	{
		if ($table_name = GE::pref('sys', 'module'))
		{
			$this->_table_name = $table_name.'_translate';
		}
	 
		parent::_initialize();
	}	
	
	public function get_tree(){
		$result = array();
		$this->clear();
		$r = $this->find_all();
		foreach($r as $item){
			$result[$item->module_id][$item->lang_id]['title'] = $item->title; 
		}
		return $result;
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
					->filter('title', 'trim');
		return $array;
	}
	
	/**
	 * Перекрытие сохранения записи
	 */
	public function save(){
		if (empty($this->lang_id)){
			$this->lang_id = GE::lang('id');
		}
		parent::save();
	}
	
	
}