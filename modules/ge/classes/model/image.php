<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Модель изображений сайта.
	 * 
	 * @package Gengine
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */
// TODO ! Расписать комментарии ко всем функциям класса
class Model_Image extends ORM{
	
	public static $img_ext = array('jpg', 'gif', 'png', 'ico', 'bmp');
	
    /**
     * Перекрытие инициализации объекта
     */
	protected function _initialize()
	{
		if ($table_name = GE::pref('sys', 'image'))
		{
			$this->_table_name = $table_name;
		}
	 
		parent::_initialize();
	}	
		
}