<?php
defined('SYSPATH') or die('No direct script access.');

	/**
	 * Модель меню сайта.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */
class Model_Widget_Banner_Target extends ORM {

	// связь с переводам заголовков модулей
	protected $_belongs_to = array (
		'list' => array('model' => 'widget_banner_list', 'foreign_key' => 'banner_id'),
	);
	
	// правила для валидации
//    protected $_rules = array(
//
//    	'title'			=> array('not_empty' => null, 'max_length' => array(255)),
//    	'code'			=> array('not_empty' => null, 'max_length' => array(20), 'regex' => array('/[A-Za-z]+/')),
//
//    );
	
	/**
     * Перекрытие инициализации объекта
     */
	protected function _initialize()
	{
		$this->_table_name = GE::pref('wid', 'banner_target');
		
		parent::_initialize();
	}	

	
	
	
	
//	public function get_first_id(){
//		
//		$r = $this->find();
//		if ($r->id)
//			return $r->id;
//		return false;
//
//	}
//	
//	public function get_element_tree($menu_id){
//		$result = array();
//		$r = $this->find($menu_id)->elements->order_by('outorder')->find_all()->as_array();
//		foreach ($r as $element){
//			$result[$element->parent_id][$element->id] = $element->as_array();
//		}
//		return $result;
//	}
//	
//	public function get_menu_list(){
//		$result = array();
//		
//		$r = $this->find_all()->as_array();
//		foreach ($r as $item){
//			$result[$item->code] = $item->as_array();
//		}
//		return $result;
//	}
//	
//	
//	
//	
//	
//	
//	
//	
//	
//	
//	public function validate_create($array){
//
//		$array = Validate::factory($array)
//					->rules('title', $this->_rules['title'])
//					->rules('code', $this->_rules['code'])
//					->filter('title', 'trim')
//					->filter('code', 'trim');
// 
//		return $array;
//	}
//	
//	public function add_menu($data){
//		$this->clear()->values($data);
//		$this->save();
//		return $this->id;
//	}
//	
//	public function delete_menu($id){
//		$this->delete($id);
//	}
	

	
}