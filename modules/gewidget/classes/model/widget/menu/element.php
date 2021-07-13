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
class Model_Widget_Menu_Element extends ORM {


	protected $_belongs_to = array (
		'menu' => array('model' => 'widget_menu', 'foreign_key' => 'menu_id')
	);

	
	// правила для валидации
    protected $_rules = array(

    	'title'			=> array('not_empty' => null, 'max_length' => array(255)),
    	'direct_link'	=> array('max_length' => array(255)),

    );
	
    /**
     * Перекрытие инициализации объекта
     */
	protected function _initialize()
	{
		if ($table_name = GE::pref('wid', 'menu_element'))
		{
			$this->_table_name = $table_name;
		}
	 
		parent::_initialize();
	}	

	
	public function validate_create($array){
		
		$array = Validate::factory($array)
					->rules('title', $this->_rules['title'])
					->rules('direct_link', $this->_rules['direct_link'])
					->filter('title', 'trim')
					->filter('direct_link', 'trim');
					
		if ($array['module_id'] === 0 AND $array['direct_link'] == '')
			$array->error('module_id', 'empty.field');
					
		return $array;
		
	}
	
	public function add_element($data){
		$this->clear()->values($data);
		$this->save();
		return $this->id;
	}

	public function save_elements($data){
		foreach($data['id'] as $id){
			if (isset($data['delete'][$id])){
				$this->delete($id);
				continue;
			}
			$this->find($id);
			$this->outorder = $data['outorder'][$id];
			if ($data['title'][$id])
				$this->title = $data['title'][$id];
			if (isset($data['module_id'][$id])){
				$this->module_id = (int)$data['module_id'][$id];
				$this->direct_link = '';
			}
			if (isset($data['item_id'][$id])){
				$this->item_id = (int)$data['item_id'][$id];
				$this->direct_link = '';
			}
			if ($data['direct_link'][$id]){
				$this->direct_link = $data['direct_link'][$id];
				$this->module_id = 0;
				$this->item_id = 0;
			}
			$this->save();
			$this->clear();
		}
		
	}
	
	
	
}