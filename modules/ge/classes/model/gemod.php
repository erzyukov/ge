<?php defined('SYSPATH') OR die('No direct access allowed.');

	/**
	 * Модель списка модулей сайта.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */


class Model_Gemod extends ORM
{
	public static $module_table = NULL;

	protected function _initialize() {
		if (self::$module_table === NULL)
			throw new Kohana_Exception('Undefined Gemod table!!!');
		
		self::$_column_cache[$this->_object_name] = NULL;
		$this->_table_name = self::$module_table;

		parent::_initialize();
	}	

	
	/**
	 * Возвращяет массив активных записей модуля
	 * если указан родительский идентификатор - возвращаются дети этого родителя
	 *  
	 * @param int $parent_id
	 * @return array
	 */
	public function get_item($id){
		
		$item = $this->active()->find($id);

		return $item->as_array();
	}
	
	/**
	 * Возвращяет массив [активных/всех] записей модуля
	 * если указан родительский идентификатор - возвращаются дети этого родителя
	 *  
	 * @param boolean $active
	 * @param int $parent_id
	 * @return array
	 */
	public function get_items($active = TRUE, $sort = '', $self_parent_id = NULL, $module_parent_id = NULL, $limit = NULL){
		$result = array();
		if ($self_parent_id !== NULL)
			$this->where('self_parent_id', '=', $self_parent_id);
		if ($module_parent_id !== NULL)
			$this->where('parent_id', '=', $module_parent_id);
		if ($limit !== NULL)
			$this->limit($limit);
		$this->add_sort($sort);
		$items = ($active)? $this->active()->find_all(): $this->find_all();
		foreach ($items as $item){
			$result[] = $item->as_array();
		}
		return $result;
	}
	
	/**
	 * Добавление сортировки для выборки
	 * 
	 * @param string $param
	 */
	public function add_sort($param) {
		if ($param != ''){
			$order_list = explode('|', $param);
			foreach ($order_list as $order){
				$order_param = explode(':', $order);
				$this->order_by($order_param[0], $order_param[1]);
			}
		}
		return $this;
	}
	
	/**
	 * Проверка на активность записи
	 */
	public function active(){
		return $this->where('isactive', '=', '1');
	}
	
	/**
	 * Валидация данных на добавление
	 * 
	 * @param array $array
	 * @param string $caption_field
	 */
	public function validate_add($array, $caption_field = 'caption') 
	{
		$max_lenth = 255;
		$title = array(
			'not_empty'		=> NULL,
			'min_length'	=> array(1),
			'max_length'	=> array(( int )$max_lenth),
		);

		// Валидация данных на создание элемента		
		$array = Validate::factory($array)
			->rules($caption_field, $title)
			->filter($caption_field, 'trim');
 
		return $array;
	}
	
	/**
	 * Создает поле в таблице модуля движка
	 * 
	 * @param string $field
	 * @param string $sql
	 */
	public function create_field($field, $sql){
		try{
			DB::query(NULL, 
				'ALTER TABLE `'.$this->_table_name.'` 
				ADD COLUMN `'.$field.'` '.$sql.' NOT NULL
				, ENGINE = InnoDB'
			)->execute();
		}
		catch (Exception $e){
			throw new Kohana_Exception('Не удалось создать поле: '.$field.' ('.$sql.')');
		}
	}
	
	/**
	 * Удаляет поле в таблице модуля движка
	 * 
	 * @param string $field
	 */
	public function delete_field($field){
		try {
			DB::query(NULL, 
				'ALTER TABLE `'.$this->_table_name.'` 
				DROP COLUMN `'.$field.'`
				, ENGINE = InnoDB'
			)->execute();
		}
		catch (Exception $e){
			throw new Kohana_Exception('Не удалось удалить поле: '.$field);
		}
	}

	/**
	 * Возвращает максимальный порядок элемента + 1
	 * 
	 * @param unknown_type $parent_id
	 */
	public function get_outorder($self_parent_id = NULL, $parent_id = NULL){
		
		$parent_where = (isset($this->_table_columns['parent_id']))? ' parent_id='.(int)$parent_id: '';
		
		if ($self_parent_id !== NULL AND isset($this->_table_columns['self_parent_id'])){
			$parent_where = ($parent_where) ? ' AND '.$parent_where : '';
			$r = $this->_db->query(Database::SELECT, 'SELECT MAX(outorder)+1 as value FROM '.$this->_table_name.' WHERE self_parent_id='.(int)$self_parent_id.$parent_where, NULL)
					->current();
		}
		else{
			$parent_where = ($parent_where) ? ' WHERE '.$parent_where : '';
			$r = $this->_db->query(Database::SELECT, 'SELECT MAX(outorder)+1 as value FROM '.$this->_table_name.$parent_where, NULL)
					->current();
		}
		return $r['value'];
	}
	
	
	
	
	
}