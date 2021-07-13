<?php defined('SYSPATH') OR die('No direct access allowed.');

	/**
	 * Модель карты сайта.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */


class Model_Sitemap extends ORM {

	// связь с переводам заголовков модулей
	protected $_has_many = array (
		'translates' => array('model' => 'trsitemap')
	);

	protected $_belongs_to = array (
		'module' => array('model' => 'module', 'foreign_key' => 'module_id')
	);
	
	// правила для валидации
    protected $_rules = array(

    	'short_url'		=> array('max_length' => array(50)),
    
    );

//	protected $_callbacks = array
//	(
//		'name'	=> array('name_unique'),
//	);
    
    /**
     * Перекрытие инициализации объекта
     */
	protected function _initialize()
	{
		if ($table_name = GE::pref('sys', 'sitemap'))
		{
			$this->_table_name = $table_name;
		}
	 
		parent::_initialize();
	}	
	
	
}
