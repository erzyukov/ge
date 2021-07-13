<?php defined('SYSPATH') OR die('No direct access allowed.');

	/**
	 * Класс для настройки сайта. Управление виджетами сайта.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */

class Constructor_Wid {

	/**
	 * Устанавливает запрошенные виджеты
	 * 
	 * @param array $data
	 */
	public static function install_widget($data){
		
		foreach($data['widget'] as $name){
			self::install($name);
		}
	}
	
	protected static function install($name){
		$tables = Kohana::config($name.'.tables');
		$links = Kohana::config($name.'.links');
		$title = Kohana::config($name.'.title');

		// создаем таблицы виджетов
		if (count($tables)){
			foreach ($tables as $table_name => $table){
				self::create_widget_table(GE::pref('wid', $table_name), $table['fields']);
			}
		}

		// создаем связи для таблиц (если есть)
		if (count($links)){
			foreach ($links as $p){
				$parent_table_pref = isset($p['parent_table_pref'])? $p['parent_table_pref']: 'wid';
				self::create_table_link(GE::pref($parent_table_pref, $p['parent_table']), GE::pref('wid', $p['child_table']), $p['parent_key'], $p['child_key'], $p['ondelete'], $p['onupdate']);
			}
		}
		
		// создаем запись в таблице установленных виджетов
		$widget = ORM::factory('widget');
		$new = array();
		$new['name'] = $name;
		$new['title'] = $title;
		$widget->values($new)->save();
	}

	protected static function create_widget_table($table, $fields){
		
		$result_field = array();
		$primary = array();
		$unique = array();
		
		$sql = 'CREATE TABLE ' . $table . ' (';
		foreach ($fields as $field){
			$field_sql = '';

			$field_sql .= ' `'.$field['name'].'` ';
			$field_sql .= $field['sql_type'];
			$field_sql .= (isset($field['sql_length'])) ? '('.$field['sql_length'].')' : '';
			$field_sql .= ' NOT NULL ';
			$field_sql .= (isset($field['autoinc'])) ? ' AUTO_INCREMENT ' : '';
			
			$result_field[] = $field_sql;

			if (isset($field['primary']))
				$primary[] = '`'.$field['name'].'`';
			else if (isset($field['unique']))
				$unique[] = '`'.$field['name'].'`';
		}
		
		if (count($primary))
			$result_field[] = ' PRIMARY KEY('.implode(',', $primary).') ';
		if (count($unique))
			$result_field[] = ' UNIQUE KEY('.implode(',', $unique).') ';
			
		$sql .= implode(',', $result_field);
		
  		$sql .= ') ENGINE=INNODB '
  				. self::_get_db_create_charset();
		DB::query(NULL, $sql)->execute();
	}

	protected static function create_table_link($parent_table, $child_table, $parent_key = 'id', $child_key = 'parent_id', $ondelete = 'CASCADE', $onupdate = 'RESTRICT'){
		// связь с таблицей переводов
		$sql = 'ALTER TABLE ' . $child_table . ' ADD CONSTRAINT `FK_'.$parent_table.'s'.$child_table.
			'` FOREIGN KEY `FK_'.$parent_table.'` (`'.$child_key.'`)'
			.' REFERENCES ' . $parent_table . ' (`'.$parent_key.'`)'
			.' ON DELETE '.$ondelete
    		.' ON UPDATE '.$onupdate
			.', ENGINE = InnoDB ';
		DB::query(NULL, $sql)->execute();
		
	}
		
	public static function delete_widget($data){
		
		foreach($data['widget'] as $name){
			self::delete($name);
		}
	}
	
	protected static function delete($name){
		$tables = array_keys(Kohana::config($name.'.tables'));
		$links = Kohana::config($name.'.links');
		
		// сначала удаляем ключи если они есть
		if (count($links)){
			foreach ($links as $p){
				self::delete_table_link(GE::pref('wid', $p['parent_table']), GE::pref('wid', $p['child_table']));
			}
		}
		
		// затем удаляем все таблицы
		if (count($tables)){
			foreach ($tables as $table_name){
				self::delete_widget_table(GE::pref('wid', $table_name));
			}
		}
		
		
		// удаляем запись в таблице установленных виджетов
		$widget = ORM::factory('widget', array('name' => $name));
		$widget->delete();
		
	}
	
	protected static function delete_widget_table($table){

		DB::query(NULL, 'DROP TABLE `'.$table.'`')->execute();
		
	}
	
	protected static function delete_table_link($parent_table, $child_table){
		
		// связь с таблицей переводов
		$sql =  'ALTER TABLE `'.$child_table.'` DROP FOREIGN KEY `FK_'.$parent_table.'s'.$child_table.'`'
			. ', ENGINE = InnoDB';
		DB::query(NULL, $sql)->execute();
		
	}
	
	
	/**
	 * Генерирует sql фрагмент
	 * кодировка в зависимости от конфигурации (Kohana::config('database.default.charset'))
	 */
	private static function _get_db_create_charset(){
		$result = '';
		$charset = Kohana::config('database.default.charset');
		switch ($charset){
			case 'utf8':
				$result = 'CHARACTER SET utf8 COLLATE utf8_general_ci';
				break;
			case 'cp1251':
				$result = 'CHARACTER SET cp1251 COLLATE cp1251_general_ci';
				break;
		}
		return $result;
	}
	
}