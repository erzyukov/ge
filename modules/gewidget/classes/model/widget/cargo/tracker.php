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
class Model_Widget_Cargo_Tracker extends ORM {

	// связь с переводам заголовков модулей
//	protected $_has_many = array (
//		'elements'	=> array('model' => 'widget_menu_element', 'foreign_key' => 'menu_id'),
//	);
	
	// правила для валидации
    protected $_rules = array(

    	'user_id'		=> array(),
    	'number'		=> array('not_empty' => null, 'max_length' => array(50)),
    	'date'			=> array(),
    	'action'		=> array('not_empty' => null, 'max_length' => array(255)),
    	'receiver'		=> array('not_empty' => null, 'max_length' => array(100)),
    	'from'		=> array('not_empty' => null, 'max_length' => array(255)),
    	'to'		=> array('not_empty' => null, 'max_length' => array(255)),
    
    );
	
	/**
     * Перекрытие инициализации объекта
     */
	protected function _initialize()
	{
		if ($table_name = GE::pref('wid', 'cargo_tracker'))
		{
			$this->_table_name = $table_name;
		}
	 
		parent::_initialize();
	}	

	
	public function add_track($data){
		$values = $data;
		
		try{$date = new DateTime($data['date']);}catch (Kohana_Exception $e){$date = new DateTime();}
		try{$time = new DateTime($data['time']);}catch (Kohana_Exception $e){$time = new DateTime();}
		if ((int)$date->format('H') == 0){
			$date->modify('+ '.$time->format('H').' hour '.$time->format('i').' min');
		}
		$values['date'] = $date->format('U');
		
		$values['action'] = $values['caction'];
		
		$this->clear()->values($values);
		$this->save();
		return $this->id;
	}
	
	public function change_track($data){
		$values = $data;
		unset($values['id']);
		try{$date = new DateTime($data['date']);}catch (Kohana_Exception $e){$date = new DateTime();}
		try{$time = new DateTime($data['time']);}catch (Kohana_Exception $e){$time = new DateTime();}
		if ((int)$date->format('H') == 0){
			$date->modify('+ '.$time->format('H').' hour '.$time->format('i').' min');
		}
		$values['date'] = $date->format('U');
		
		$values['action'] = $values['caction'];
		
		$this->values($values);
		$this->save();
	}
	
	
	
	
	
	
	
	
	
	
	
	
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
	
	
	public function delete_menu($id){
		$this->delete($id);
	}
	

	
	
}