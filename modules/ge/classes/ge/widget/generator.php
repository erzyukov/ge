<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Базовый класс генераторов данных для виджетов.
	 * Описание функций интерфейса находятся непосредственно в описании интерфейса
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */
	
	class GE_Widget_Generator implements GE_Interface_Widget_Generator {
		
		protected $model;
		protected $name;
		
		protected $lang = NULL;
		protected $param = array();
		protected $current_template = 'index';
		
		public function __construct($name){
			$this->name = $name;
			// выставляем язык по умолчанию
			$this->lang = GE::get_lang(GE::lang('id'));
		}
		
		public function data() {}
		
		/**
		 * Создает и возвращает объект генератор данных по названию виджета
		 * 
		 * @param $widget_name название виджета
		 * @return Generator
		 */
		public static function factory($widget_name) {

			// если генератор найден в папке application запускаем его
			if (Kohana::find_file('classes', 'widget/'.$widget_name, 'php')){
				$class = 'Widget_'.ucfirst($widget_name);
				return new $class($widget_name);
			}
			else {
				throw new Kohana_Exception('Site widget "'.$widget_name.'" not found!');
			}
			
		}

		/**
		 * Возвращает шаблон который определил генератор
		 * 
		 * @return sting
		 */
		public function get_template(){
			return $this->current_template;
		}
		
		/**
		 * Устанавливает язык страницы
		 * @param string $lang
		 */
		public function set_lang($lang){
			$this->lang = GE::get_lang($lang);
		}
		
		/**
		 * Устанавливает значение параметра
		 * @param 
		 */
		public function set_param($num, $value){
			$this->param[(int)$num] = $value;
		}
		
		
		
		
		
		
	}



