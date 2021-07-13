<?php defined('SYSPATH') OR die('No direct access allowed.');

	/**
	 * Модель переводов текстовых переменных.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */


class Model_Field_Text extends Model_Field
{

//	protected $_belongs_to = array(
//		'lang' => array()
//	);
	
	// правила для валидации
//    protected $_rules = array(
//        'title'			=> array('not_empty' => null, 'max_length' => array(50)),
//        'short'			=> array('not_empty' => null, 'max_length' => array(10)),
//    	'uri'			=> array('not_empty' => null, 'max_length' => array(5), 'alpha' => null, 'min_length' => array(2)),
//        'date_format'	=> array('max_length' => array(100)),
//    );
	
    /**
     * Перекрытие инициализации объекта
     */
	protected function _initialize()
	{
		if ($table_name = GE::pref('sys', 'text'))
		{
			$this->_table_name = $table_name;
		}
	 
		parent::_initialize();
	}	

	
	
	
}