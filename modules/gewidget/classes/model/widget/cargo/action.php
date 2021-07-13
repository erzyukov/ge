<?php
defined('SYSPATH') or die('No direct script access.');

	/**
	 * 
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */
class Model_Widget_Cargo_Action extends ORM {

	// правила для валидации
    protected $_rules = array(

    	'value'		=> array('not_empty' => null, 'max_length' => array(255)),
    
    );
	
	/**
     * Перекрытие инициализации объекта
     */
	protected function _initialize()
	{
		if ($table_name = GE::pref('wid', 'cargo_action'))
		{
			$this->_table_name = $table_name;
		}
	 
		parent::_initialize();
	}	

}