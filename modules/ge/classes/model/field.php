<?php defined('SYSPATH') OR die('No direct access allowed.');

	/**
	 * Модель переводов переменных текстовых редакторов.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */


abstract class Model_Field extends ORM
{
	
	// TODO нид комментарии к функциям

	public function get_value($id, $lang_id, $default = NULL){
		$r = $this->where('id', '=', $id)->where('lang_id', '=', $lang_id)->find();
		
		if ($r->value == NULL AND $default !== NULL){
			return $default;
		}
		else if ($r->value == NULL && $lang_id != GE::lang('id')){
			$r = $this->where('id', '=', $id)->where('lang_id', '=', GE::lang('id'))->find();
		}
		
		return $r->value;
	}
	
	public function set_value($value, $lang_id, $id = NULL){
// TODO эта функция требует рефакторинка (очень сильно причем =))		
		if ($id){
			$this->where('id', '=', $id)->where('lang_id', '=', $lang_id)->find();
			if ($this->id){
				DB::update($this->_table_name)->set(array('value' => $value))
					->where('id', '=', $id)->where('lang_id', '=', $lang_id)->execute();
				$insert_id = $id;
			}
			else{
				$this->values(array('id' => $id, 'lang_id' => $lang_id, 'value' => $value));
				$this->save();
				$insert_id = $this->id;
			}
		}
		else{
			$this->values(array('lang_id' => $lang_id, 'value' => $value));
			$this->save();
			$insert_id = $this->id;
		}
		$this->clear();

		if ($id === NULL && $lang_id != GE::lang('id')){
			$this->values(array('id' => $insert_id, 'lang_id' => GE::lang('id'), 'value' => $value));
			$this->save();
			$this->clear();
		}
		
		return $insert_id;
	}
	
	public function delete_value($id){
		$this->where('id', '=', $id)->find()->delete();
	}
	
	
}