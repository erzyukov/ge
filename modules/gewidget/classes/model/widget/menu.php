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
class Model_Widget_Menu extends ORM {

	// связь с переводам заголовков модулей
	protected $_has_many = array (
		'elements'	=> array('model' => 'widget_menu_element', 'foreign_key' => 'menu_id'),
	);
	
	// правила для валидации
    protected $_rules = array(

    	'title'			=> array('not_empty' => null, 'max_length' => array(255)),
    	'code'			=> array('not_empty' => null, 'max_length' => array(20), 'regex' => array('/[A-Za-z]+/')),

    );
	
	/**
     * Перекрытие инициализации объекта
     */
	protected function _initialize()
	{
		if ($table_name = GE::pref('wid', 'menu'))
		{
			$this->_table_name = $table_name;
		}
	 
		parent::_initialize();
	}	

	
	
	
	
	public function get_first_id(){
		
		$r = $this->find();
		if ($r->id)
			return $r->id;
		return false;

	}
	
	public function get_element_tree($menu_id){
		$result = array();
		$r = $this->find($menu_id)->elements->order_by('outorder')->find_all()->as_array();
		foreach ($r as $element){
			$result[$element->parent_id][$element->id] = $element->as_array();
		}
		return $result;
	}
	
	public function get_menu_list(){
		$result = array();
		
		$r = $this->find_all()->as_array();
		foreach ($r as $item){
			$result[$item->code] = $item->as_array();
		}
		return $result;
	}
	
	
	
	
	
	
	
	
	
	
	public function validate_create($array){

		$array = Validate::factory($array)
					->rules('title', $this->_rules['title'])
					->rules('code', $this->_rules['code'])
					->filter('title', 'trim')
					->filter('code', 'trim');
 
		return $array;
	}
	
	public function add_menu($data){
		$this->clear()->values($data);
		$this->save();
		return $this->id;
	}
	
	public function delete_menu($id){
		$this->delete($id);
	}
	

	
	
/*	
	protected $table = 'menu';
	protected $table_element = 'menu_element';
	
	public function __construct(){
		parent::__construct();

		$this->table = Gengine::wpref().$this->table;
		$this->table_element = Gengine::wpref().$this->table_element;
	}
	
	public function get_menu($code = ''){
		return DB::select('e.*')
			->from(array($this->table, 'm'))
			->join(array($this->table_element, 'e'), 'INNER')
			->on('m.id', '=', 'e.menu_id')
			->where('code', '=', $code)
			->order_by('e.outorder')
			->execute()
			->as_array();
	}
	
	
	public function get_current_menu_id($code, $module_id, $item_id){
		$r = DB::select('e.id', 'e.parent_id')
			->from(array($this->table, 'm'))
			->join(array($this->table_element, 'e'), 'INNER')
			->on('m.id', '=', 'e.menu_id')
			->where('code', '=', $code)
			->where('module_id', '=', $module_id)
			->where('item_id', '=', $item_id)
			->order_by('e.outorder')
			->execute()
			->as_array();
		if (count($r)){
			$r = $r[0];
		
			if ($r['parent_id'] == 0)
				return $r['id'];
			else
				return $r['parent_id'];
		}
		$map = Gengine::get_sitemap('module_id');

		// если ничего не нашли, пробуем искать у родителей
		$module_config = Gengine::module_config($map[$module_id][$item_id]['name']);
		if (isset($module_config['parent_module'])){
			// если мы в подмодуле, ищем в родительском модуле
			$map_name = Gengine::get_sitemap('module');
			if (isset($map_name[$module_config['parent_module']][0]['module_id']) AND isset($map[$module_id][$item_id]['item_parent_id'])){
				$m_id = $map_name[$module_config['parent_module']][0]['module_id'];
				$i_id = $map[$module_id][$item_id]['item_parent_id'];
				return $this->search_parent_menu_id($map, $code, $m_id, $i_id);
			}
		}else{
			return $this->search_parent_menu_id($map, $code, $module_id, $item_id);
		}
	}
	
	protected function search_parent_menu_id($map, $code, $module_id, $item_id, $deep = 0){

		if (isset($map[$module_id][$item_id]['item_parent_id'])){
			$parent_id = $map[$module_id][$item_id]['item_parent_id'];

			$r = DB::select('e.id', 'e.parent_id')
				->from(array($this->table, 'm'))
				->join(array($this->table_element, 'e'), 'INNER')
				->on('m.id', '=', 'e.menu_id')
				->where('code', '=', $code)
				->where('module_id', '=', $module_id)
				->where('item_id', '=', $parent_id)
				->order_by('e.outorder')
				->execute()
				->as_array();
	
			if (count($r)){
				$r = $r[0];
			
				if ($r['parent_id'] == 0)
					return $r['id'];
				else
					return $r['parent_id'];
			}
			else{
				return $this->search_parent_menu_id($map, $code, $module_id, $parent_id, $deep+1);
			}
		}
		return false;
	}
	
	
	
*/
}