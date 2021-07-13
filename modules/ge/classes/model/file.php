<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Model file.
	 * 
	 * @package Gengine
	 * @author Atber (aka Khramkov Ivan)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */
class Model_File extends ORM{
	
    /**
     * Перекрытие инициализации объекта
     */
	protected function _initialize()
	{
		if ($table_name = GE::pref('sys', 'file'))
		{
			$this->_table_name = $table_name;
		}
	 
		parent::_initialize();
	}	
	
		
}