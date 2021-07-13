<?php defined('SYSPATH') OR die('No direct access allowed.');

	/**
	 * Модель переводов заголовков списка модулей сайта.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */


class Model_Trsitemap extends ORM
{
	// связь с модулем и языками сайта
	protected $_belongs_to = array(
		'sitemap' => array('foreign_key' => 'sitemap_id'),
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
		if ($table_name = GE::pref('sys', 'sitemap'))
		{
			$this->_table_name = $table_name.'_translate';
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