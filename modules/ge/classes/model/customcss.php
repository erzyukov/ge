<?php defined('SYSPATH') OR die('No direct access allowed.');

	/**
	 * Модель переводов заголовков списка модулей сайта.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */


class Model_Customcss extends ORM
{
	// связь с модулем и языками сайта
	protected $_belongs_to = array(
		'module' => array('foreign_key' => 'module_id'),
	);
	

    /**
     * Перекрытие инициализации объекта
     */
	protected function _initialize()
	{
		if ($table_name = GE::pref('sys', 'css'))
		{
			$this->_table_name = $table_name;
		}
	 
		parent::_initialize();
	}	
	

	public function get_item($module_id, $item_id){
		return  $this->where('module_id', '=', $module_id)->where('item_id', '=', $item_id)->find()->value;
	}
	
	
}