<?php defined('SYSPATH') OR die('No direct access allowed.');

	/**
	 * Класс для настройки сайта. Управление языками сайта.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */


//SHOW COLUMNS FROM gsys_module LIKE 'seo_changefreq'

class Constructor_Mod {

	/**
	 * Создает новый модуль движка (добавляет запись в таблицу модулей + создает таблицу контента модуля)
	 * 
	 * @param array $data
	 */
	public static function create_module($data){
		
		$module = ORM::factory('module');
		$post = $module->validate_create($data);
		
		if ($post->check()){
			$post['parent_id'] = $data['parent_id'];
			$module->values($post);
			$module->save();
			
			$translate = $module->trmodules;
			$tr_post = $translate->validate_create($data);
			if ($tr_post->check()){
				$translate->module_id = $module->id;
				$translate->values($tr_post);
				$translate->save();
			}

			$sitemap = array(
				'module_id' => $module->id,
				'item_id' => 0,
				'item_parent_id' => -1,
				'isactive' => 1,
				'sitemap_show' => 1,
				'lastmod' => date('Y-m-d H:i:s'),
			);

			// язык не указываем, т.к. по автоматомм проставится язык по умолчанию в модели
			$translates = array(array(
				'title' => $translate->title,
			));
			
			Sitemap::instance()->create_record($sitemap, $translates);

			if ((int) Kohana::config('constructor.module_type.'.$module->type.'.table')){
				self::create_table($module->name, $module->type, $data['parent_id']);
			}
			
		}
		else{
			echo Kohana::debug($post->errors('register')); die;
		}

		return $module->id;

	}
	
	/**
	 * Сохраняет изменения в настройках модуля
	 * 
	 * @param array $data
	 */
	public static function save_module($data){
		
		$id = $data['id'];
		unset($data['id']);
		$data['sitemap_show'] = (isset($data['sitemap_show']))? 1: 0;
		$module = ORM::factory('module')->find($id);

		// если модуль являлся ребенком и перестал им быть, или наоборот
		// - удаляем поле parent_id, или создаем его 
		if ((bool)$module->parent_id != (bool)$data['parent_id'])
			self::alter_table_child($module->name, (bool)$data['parent_id']);
		// Обновляем заголовок в sitemap
		$tr_sitemap = $module->trmodules->where('lang_id', '=', GE::lang('id'))->find();
		$tr_sitemap->title = $data['title'];
		$tr_sitemap->save();
		$module->values($data);
		$module->save();

		$sitemap = array(
			'sitemap_show' => $module->sitemap_show,
			'lastmod' => date('Y-m-d H:i:s'),
			'seo_priority' => $module->seo_priority,
			'seo_changefreq' => $module->seo_changefreq,
		);

		$translates = array(array(
			'lang_id' => $tr_sitemap->lang_id,
			'title' => $tr_sitemap->title,
		));

		Sitemap::instance()->update_record($module->id, 0, $sitemap, $translates);
		
		return $id;
	}
	
	/**
	 * Удаляет текущий модуль
	 * 
	 * @param $data
	 */
	public static function delete_module($data){
		$id = $data['id'];
		
		$module = ORM::factory('module')->find($id);
		$delete_name = $module->name;
		$module->delete($id);
		$module->clear()->where('parent_id', '=', $id)->find();
		if ($module->id){
			self::alter_table_child($module->name, false);
			
			$module->parent_id = 0;
			$module->save();
		}
		
		Sitemap::instance()->delete_record($id, 0);
		
		self::delete_table($delete_name);
	}
	
	/**
	 * Создает новое поле в модуле движка 
	 * (добавляет запись в модель модуля + создает поле в таблице контента модуля)
	 * 
	 * @param array $data
	 */
	public static function create_field($data){
		
		if (trim($data['field']) == '' OR trim($data['title']) == '')
			return $data['id'];
		$data['field'] = trim($data['field']);
		$data['title'] = trim($data['title']);
		
		
		$module = ORM::factory('module')->find($data['id']);
		$model = $module->model;
		$model = ($model != '')? unserialize($model): array();

		if (isset($model[$data['field']]))
			throw new Kohana_Exception('Поле "'.$data['field'].'" уже существует в модели!');
		
		$model[$data['field']] = self::process_field($data);
		$model = self::sort_fields($model);
		
		$module->model = serialize($model);
		$module->save();
		
		$type = Kohana::config('constructor.field_type');
		
		$gemodule = GE::mod($module->name);
		$gemodule->create_field($data['field'], $type[$data['type']]['sql']);
		
		return $data['id'];
	}
	
	/**
	 * Сохраняет изменения в настройках модели модуля
	 * 
	 * @param array $data
	 */
	public static function save_field($data){

		$module = ORM::factory('module')->find($data['id']);
		
		$model = array();
		foreach ($data['data_id'] as $id){
			if (isset($data['delete'][$id]))
				continue;
			$field = array(
				'field' => $data['field'][$id], 
				'title' => $data['title'][$id], 
				'required' => (isset($data['required'][$id])?'on':NULL), 
				'type' => $data['type'][$id], 
				'order' => $data['order'][$id], 
				'size' => (isset($data['size'][$id]))? $data['size'][$id]: NULL,
				'reference_module' => (isset($data['reference_module'][$id]))? $data['reference_module'][$id]: NULL,
				'reference_type' => (isset($data['reference_type'][$id]))? $data['reference_type'][$id]: NULL,
				'reference_pk_field' => (isset($data['reference_pk_field'][$id]))? $data['reference_pk_field'][$id]: NULL,
				'reference_value_field' => (isset($data['reference_value_field'][$id]))? $data['reference_value_field'][$id]: NULL,
				'select_type' => (isset($data['select_type'][$id]))? $data['select_type'][$id]: NULL,
				'select_values' => (isset($data['select_values'][$id]))? $data['select_values'][$id]: NULL,
			);

			$field = self::process_field($field);
			
			$model[$data['field'][$id]] = $field;
		}

		$module->model = serialize(self::sort_fields($model));
		$module->save();

		if (isset($data['delete'])){
			$gemodule = GE::mod($module->name);
			foreach ($data['delete'] as $id => $v){
				$gemodule->delete_field($data['field'][$id]);
			}
		}
		
		return $data['id'];
	}

	
	/**
	 * Создает поля для модуля по сериализованному массиву
	 * (прежние поля удаляет)
	 * 
	 * @param $data
	 */
	public static function import_field($data){
		$model = unserialize($data['model']);
		if (!is_array($model))
			return $data['id'];
		
		$module = ORM::factory('module')->find($data['id']);
		$old_model = unserialize($module->model);
		$module->model = $data['model'];
		$module->save();
		
		if (is_array($old_model)){
			foreach ($old_model as $field => $param){
				$gemodule->delete_field($field);
			}
		}
		
		$gemodule = GE::mod($module->name);
		$type = Kohana::config('constructor.field_type');
		foreach ($model as $field => $param){
			$gemodule->create_field($field, $type[$param['type']]['sql']);
		}
		
		return $data['id'];
	}
	
	
	/**
	 * Обрабатывает значения настроек поля модели,
	 * возвращяет массив готовый для сериализации
	 * 
	 * @param array $data
	 */
	protected static function process_field($data){
		$field = array(
			'field' => $data['field'], 
			'title' => $data['title'], 
			'required' => (isset($data['required'])?1:0), 
			'type' => $data['type'], 
			'order' => $data['order'], 
		);
		
		switch ($data['type']){
			case 'image':
				$size = explode(';', $data['size']);
				foreach($size as $k => $v){
					$size[$k] = explode('.', $v);
				}
				$field['size'] = $size;
				break;
			case 'reference':
				$field['module'] = $data['reference_module'];
				$field['ref_type'] = $data['reference_type'];
				$field['pk_field'] = $data['reference_pk_field'];
				$field['value_field'] = $data['reference_value_field'];
				break;
			case 'select':
				$field['sel_type'] = $data['select_type'];
				$field['values'] = explode(',', $data['select_values']);
				break;
		}
		
		return $field;
	}
	
	/**
	 * Сортирует полученный массив модели модуля по полю $model['order']
	 * 
	 * @param array $model
	 */
	protected static function sort_fields($model){
		$sort = array();
		foreach ($model as $k => $v){
			$sort[$k] = $v['order'];
		}
		asort($sort);
		$result = array();
		foreach ($sort as $k => $order){
			$result[$k] = $model[$k];
		}
		return $result;
	}
	
	
	protected static function create_table($module, $type, $have_parent){
		
		$sql = 'CREATE TABLE ' . GE::pref('mod', $module) . ' ( '
			. '`id` INT (11) NOT NULL AUTO_INCREMENT, '
	 		. (($type == 'tree') ? '`self_parent_id` INT NOT NULL, ' : '')
	 		. (($have_parent) ? '`parent_id` INT NOT NULL, ' : '')
	 		. '`isactive` INT(1) NOT NULL, '
	 		. '`outorder` INT NOT NULL, '
	 		. '`lastmod` DATETIME NOT NULL, '
	 		. '`user_id` INT NOT NULL, '
	 		. 'PRIMARY KEY(`id`) '
	 		. ') ENGINE=INNODB '
  			. 'CHARACTER SET utf8 COLLATE utf8_general_ci';
		
  		DB::query(NULL, $sql)->execute();
	 		
	}
	
	protected static function delete_table($module){
		$sql = 'DROP TABLE `' . GE::pref('mod', $module) . '`';
		DB::query(NULL, $sql)->execute();
	}
	
	protected static function alter_table_child($module, $have_parent){
		try{
			if ($have_parent)
				$sql = 'ALTER TABLE `'. GE::pref('mod', $module) .'` 
					ADD COLUMN `parent_id` INT NOT NULL	, ENGINE = InnoDB';
			else 
				$sql = 'ALTER TABLE `'. GE::pref('mod', $module) .'` 
					DROP COLUMN `parent_id` , ENGINE = InnoDB';
			DB::query(NULL, $sql)->execute();
		}
		catch (Exception $e){
			throw new Kohana_Exception('Не удалось изменить таблицу для модуля: '.$module.' ('.$sql.')');
		}
	}
	
	/**
	 * 
	 * Возвращает массив таблиц модулей сайта, 
	 *   можно задать префикс по которому будут выбераться таблицы
	 * 
	 * @param $pref префикс таблиц
	 * 
	 * @return array()
	 */
	public static function get_tables($pref = NULL){
		$pref = ($pref)? $pref.'%': NULL;
		return Database::instance()->list_tables($pref);
	}
	
	
}