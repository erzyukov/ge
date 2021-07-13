<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Класс обработки полей модулей.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */

// типы полей (string|text|editor|number|cost|image|file|reference|select|date|datetime|time)
class GE_Fields {
	
	protected $fields = array();
	protected $lang;
	
	const EMPTY_VIEW_DIR = 'fields/empty/';
	const PROCESS_VIEW_DIR = 'fields/process/';
	const EDIT_VIEW_DIR = 'admin/fields';
	protected $image_cache_dir = '';
	protected $file_cache_dir = '';
	
	protected $fstring;
	protected $ftext;
	protected $feditor;
	
	
	/**
	 * constructor
	 * 
	 * @param array $fields
	 */
	public function __construct($fields, $lang){
		$this->image_cache_dir = $_SERVER['DOCUMENT_ROOT'].'/rs/module/images/';
		$this->file_cache_dir = $_SERVER['DOCUMENT_ROOT'].'/rs/module/files/';
		$this->fields = $fields;
		$this->lang = $lang;
		
		$this->fstring	= ORM::factory('field_string');
		$this->ftext	= ORM::factory('field_text');
		$this->feditor	= ORM::factory('field_editor');
		
	}

	public function process($data, $type = 'user', $default = array()){
		switch ($type){
			case 'user':
				return $this->user_process($data);
			case 'edit':
				return $this->edit_process($data);
			case 'save':
				return $this->save_process($data, $default);
			case 'delete':
				$this->delete_process($data);
				return TRUE;
		}
	}

	protected function delete_process($data){

		foreach($this->fields as $field_name => $config){
			$this->delete_process_value($field_name, $data);
		}
		
	}

	protected function delete_process_value($field_name, $data){

		if (!isset($data[$field_name]))
			return NULL;
		$value = $data[$field_name];

		$set = $this->fields[$field_name];
		switch ($this->fields[$field_name]['type']) {
			case 'string':
				$this->fstring->delete_value($value);
			break;
			case 'text':
				$this->ftext->delete_value($value);
			break;
			case 'editor':
				$this->feditor->delete_value($value);
			break;
			case 'image':
				// TODO Удалить
//				if (isset($data[$field_name.'_delete']))
//					$result = $this->delete_image($field_name, $value);
			break;
			case 'flash':
				// TODO Удалить
			break;
			case 'file':
				// TODO Удалить
			break;
		}
			
	}
	
	protected function save_process($data, $default){

		$result = array();

		foreach($this->fields as $field_name => $config){
			$r = $this->save_process_value($field_name, $data, $default);
			if ($r !== NULL)
				$result[$field_name] = $r;
		}
		
		return $result;
	}
	
	protected function save_process_value($field_name, $data, $default){
		if (!isset($data[$field_name]))
			return NULL;
		$result = $value = $data[$field_name];
		$default_value = (isset($default[$field_name])) ? $default[$field_name] : NULL;

		$set = $this->fields[$field_name];
		switch ($this->fields[$field_name]['type']) {
			case 'string':
				$result = $this->fstring->set_value($value, $this->lang, $default_value);
			break;
			case 'text':
				$result = $this->ftext->set_value($value, $this->lang, $default_value);
			break;
			case 'editor':
				$result = $this->feditor->set_value($value, $this->lang, $default_value);
			break;
			case 'number':
				$result = ( int ) $value;
			break;
			case 'cost':
		// TODO как-то надо обработать
				$result = $value;
			break;
			case 'image':
				if (isset($data[$field_name.'_delete']))
					$result = $this->delete_image($field_name, $value);
				else
					$result = $this->save_image($field_name, $value);
			break;
			case 'flash':
		// TODO как-то надо обработать
				$result = $value;
			break;
			case 'file':
				if (isset($data[$field_name.'_delete']))
					$result = $this->delete_file($field_name, $value);
				else
					$result = $this->save_file($field_name, $value);
			break;
			case 'reference':
				$result = $value;
			break;
			case 'select':
				$result = $value;
			break;
			case 'date':
				$date = new DateTime($value);
				$result = $date->format('Y-m-d');
			break;
			case 'datetime':
		// TODO как-то надо обработать
				$result = $value;
			break;
			case 'time':
		// TODO как-то надо обработать
				$result = $value;
			break;
			case 'checkbox':
				$result = ($value === 'on') ? 1 : 0;
			break;
		}
			
			
		return $result;
	}
	
	protected function save_image($field_name, $image_id){

		$set = $this->fields[$field_name];
		
		if (isset($_FILES[$field_name])){
			if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] == UPLOAD_ERR_NO_FILE){
				// TODO Добавить систему сообщений
				return NULL;
			}
			if ($_FILES[$field_name]['error']) {
				// TODO Добавить систему сообщений
				return NULL;
			}
			if ((isset($set['image']['size'])) AND (filesize($_FILES[$field_name]['tmp_name']) > $set['image']['size'])){
				// TODO Добавить систему сообщений
				return NULL;
			}
			list($name, $ext) = explode('.', $_FILES[$field_name]['name']);
			if ( ! in_array($ext, Model_Image::$img_ext)) {
				// TODO Добавить систему сообщений
				return NULL;
			}

			$content = file_get_contents($_FILES[$field_name]['tmp_name']);
			$image = ORM::factory('image')->find($image_id);
			if ($image){
				$this->clear_image_cache($image_id, $this->fields[$field_name]['size'], $image->type);
			}
			$image->content = $content;
			$image->type = $ext;
			$image->save();
			return $image->id;
		}
		return NULL;
	}
	
	protected function delete_image($field_name, $value){
		if ( ! $value) return 0;
		
		$image = ORM::factory('image')->find($value);
		$this->clear_image_cache($value, $this->fields[$field_name]['size'], $image->type);
		$image->delete($value);
		return 0;
	}
	
	protected function save_file($field_name, $file_id){
		$set = $this->fields[$field_name];
		
		if (isset($_FILES[$field_name])){
			if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] == UPLOAD_ERR_NO_FILE){
				// TODO Добавить систему сообщений
				return NULL;
			}
			if ($_FILES[$field_name]['error']) {
				// TODO Добавить систему сообщений
				return NULL;
			}
			if ((isset($set['file']['size'])) AND (filesize($_FILES[$field_name]['tmp_name']) > $set['file']['size'])){
				// TODO Добавить систему сообщений
				return NULL;
			}
			list($name, $ext) = explode('.', $_FILES[$field_name]['name']);
			$ext = strtolower($ext);

			$content = file_get_contents($_FILES[$field_name]['tmp_name']);
			$file = ORM::factory('file')->find($file_id);
			if ($file){
				$this->clear_file_cache($file_id, $file->type);
			}
			$file->content = $content;
			$file->real_name = $_FILES[$field_name]['name'];
			$file->type = $ext;
			$file->save();
			return $file->id;
		}
		return NULL;
	}
	
	protected function delete_file($field_name, $value){
		if ( ! $value) return 0;
		
		$file = ORM::factory('file')->find($value);
		$this->clear_file_cache($value, $file->type);
		$file->delete($value);
		return 0;
	}
	
	protected function clear_image_cache($id, $measurement, $ext){
		$m = $this->normalize_image_measurment($measurement);
		foreach ($m as $param){
			$file = $this->image_cache_dir.'i'.$id.'_'.$param[0].'_'.$param[1].'.'.$ext;
			if (is_file($file)){
				unlink($file);
			}
		}
	}
	
	protected function clear_file_cache($id, $ext){
		$file = $this->file_cache_dir.'i'.$id.'_'.'.'.$ext;
		if (is_file($file)){
		    unlink($file);
		}
	}
	
	protected function edit_process($data){

		$result = array();

		foreach($data as $field_name => $value){
			if ($r = $this->edit_process_value($field_name, $value))
				$result[$field_name] = $r;
		}
		
		return $result;
	}
	
	protected function edit_process_value($field_name, $value){
		
		$result = $value;

		if (isset($this->fields[$field_name])){
			
			$set = $this->fields[$field_name];
			switch ($this->fields[$field_name]['type']) {
				case 'string':
					$result = $this->fstring->get_value($value, $this->lang, '');
				break;
				case 'text':
					$result = $this->ftext->get_value($value, $this->lang, '');
				break;
				case 'editor':
					$result = $this->feditor->get_value($value, $this->lang, '');
				break;
					case 'number':
					$result = $value;
				break;
				case 'cost':
					$result = $value;
				break;
				case 'image':
					$result = $this->prepare_edit_image($value, $set);
				break;
				case 'flash':
					$result = $value;
				break;
				case 'file':
					$result = $this->prepare_edit_file($value);
				break;
				case 'reference':
					$model = GE::mod($set['module']);
					$result = $this->prepare_edit_reference($field_name, $model->get_items(), $value);
				break;
				case 'select':
					$result = $value;
				break;
				case 'date':
					$date = new DateTime(($value==='0000-00-00'?date('Y-m-d'):$value));
					$result = $date->format('d.m.Y');
				break;
				case 'datetime':
					$result = $value;
				break;
				case 'time':
					$result = $value;
				break;
				case 'checkbox':
					$result = ($value == 1) ? 'checked="true"' : '';
				break;
				
			}

			return array('value' => $result, 'default' => $value);
		}
		
		return NULL;
	}
	
	protected function prepare_edit_reference($name, $data, $default){
		$result = '<select name="'.$name.'">';
		foreach($data as $item){
			$item_array = $item;//$item->as_array();
			$value = $item_array[$this->fields[$name]['pk_field']];
			$content = $this->fstring->get_value($item_array[$this->fields[$name]['value_field']], $this->lang, '');
			$selected = ($value == $default) ? 'selected="true"' : '' ;
			$result .= '<option value="'.$value.'" '.$selected.'>'.$content.'</option>';
		}
		$result .= '</select>';
		return $result;
	}
	
	protected function prepare_edit_image($value, $set){
		if ( ! $value) return '';
		$result = array();
		$m = $this->normalize_image_measurment($set['size']);
		foreach ($m as $id => $param){
			$file_name = $this->module_image_prepare($value, $param);
			$result[] = '<a href="/rs/module/images/'.$file_name.'" class="highslide" onclick="return hs.expand(this)" title="">Просмотр: '.$param[0].' x '.$param[1].'</a>'; 
		}
		return implode('<br />', $result);
	}
	
	protected function prepare_edit_file($value){
		if ( ! $value) return '';
		$result = array();
		list($file_name, $real_name) = $this->module_file_prepare($value);
		$result[] = 'Файл: <a href="/rs/module/files/'.$file_name.'" title="">'.$real_name.'</a>';
		return implode('<br />', $result);
	}
	
	protected function normalize_image_measurment($measurment){
		$result = array();
		if (isset($measurment['width'])){
			$result[] = $measurment;
		}
		else{
			$result = $measurment;
		}
		return $result;
	}
	
	protected function module_image_prepare($id, $param){
// TODO по хорошему надо сделать, чтобы грузилось не с файла, а с контента картинки
		$file_name = 'default.jpg';
		$image_data = ORM::factory('image')->find($id);
		if ( ! $image_data->content) return $file_name;
		
		$file_name = 'i'.$id.'_'.$param[0].'_'.$param[1].'.'.$image_data->type;
		$file_path = $this->image_cache_dir.$file_name;
		if ( ! is_file($file_path))
		{
			GE::test_dir(APPPATH.'cache/media/');
			$tmpfname = tempnam(APPPATH.'cache/media/', 'img');
			$fp = fopen($tmpfname, 'w');
			fwrite($fp, $image_data->content);
			fclose($fp);
			
			$image = Image::factory($tmpfname);
			if ($param[0] && $param[1]){
				$image->resize($param[0], $param[1]);
			}
			GE::test_file($file_path);
			$image->save($file_path);
		
			if (is_file($tmpfname))
				unlink($tmpfname);
		}

		return $file_name;
	}
	
	protected function module_file_prepare($id){
		$file_data = ORM::factory('file')->find($id);
		if ( ! $file_data->content) return NULL;
		
		$file_name = 'i'.$id.'_'.'.'.$file_data->type;
		$file_path = $this->file_cache_dir.$file_name;
		if ( ! is_file($file_path))
		{
			$tmpfname = tempnam(APPPATH.'cache/media/', 'file');
			$fp = fopen($tmpfname, 'w');
			fwrite($fp, $file_data->content);
			fclose($fp);
			
			$file = File::factory($tmpfname);
			$file->save($file_path);
			if (is_file($tmpfname))
				unlink($tmpfname);
		}
		return array($file_name, $file_data->real_name);
	}
	
	protected function user_process($data){
		foreach($data as $field_name => $value){
			$data['_'.$field_name] = $value;
			$data[$field_name] = $this->user_process_value($field_name, $value);
		}
		return $data;
	}
	
	protected function user_process_value($field_name, $value){
		$result = $value;
		if (isset($this->fields[$field_name])){
			$set = $this->fields[$field_name];
			switch ($this->fields[$field_name]['type']) {
				case 'string':
					$result = $this->fstring->get_value($value, $this->lang);
				break;
				case 'text':
					$result = $this->ftext->get_value($value, $this->lang);
				break;
				case 'editor':
					$result = $this->feditor->get_value($value, $this->lang);
				break;
				case 'number':
					$result = $value;
				break;
				case 'cost':
					$result = $value;
				break;
				case 'image':
					$img = array();
					foreach ($set['size'] as $key => $param){
						$img[$key]['src'] = '/rs/module/images/'.$this->module_image_prepare($value, $param);
						$img[$key]['width'] = $param[0];//width
						$img[$key]['height'] = $param[1];//height
					}
					$result = $img;
				break;
				case 'flash':
					$result = $value;
				break;
				case 'file':
					$file = array();
					$file['default'] = $value;
					list($file['src'], $file['real_name']) = $this->module_file_prepare($value);
					$file['src'] = '/rs/module/files/'.$file['src'];
					$result = $file;
				break;
				case 'reference':
					if ($value){
						$model = GE::mod($set['module']);
						$data = $model->get_item($value);
						$result = $this->fstring->get_value($data[$set['value_field']], $this->lang, '');
					}
					else{
						$result = '';
					}
				break;
				case 'select':
					$result = $value;
				break;
				case 'date':
					$d = array();
					$date = new DateTime($value);
					$d['value'] = $date->format('d.m.Y');
					$d['d'] = $date->format('d');
					$d['m'] = $date->format('m');
					$d['y'] = $date->format('y');
					$result = $d;
				break;
				case 'datetime':
					$result = $value;
				break;
				case 'time':
					$result = $value;
				break;
				case 'hidden':
					$result = $value;
				break;
				
			}

		}

		return $result;
	}

//	public function content($data, $type = 'user'){
//		switch ($type){
//			case 'user':
//				return $this->user_content($data);
//		}
//	}
//	
//	protected function user_content($data){
//		$result = '';
//		foreach($this->fields as $field_name => $set){
//			$value['field'] = $set['name'];
//			$value['value'] = $data[$field_name];
//			$result .= View::factory(self::EMPTY_VIEW_DIR.$set['type'], $value);
//		}
//		return $result;
//	}
	


}// End Gengine_Fields