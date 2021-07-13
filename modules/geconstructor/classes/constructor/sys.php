<?php defined('SYSPATH') OR die('No direct access allowed.');

	/**
	 * Класс для настройки сайта. Создание системных таблиц.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */

class Constructor_Sys {

	protected static $sys_tables = array();
	
	public static function create_sys_tables(){
		
		self::$sys_tables = self::get_tables(GE::pref('sys'));

		// таблица языков сайта
		self::_create_lang_table();
		// таблицы модулей
		self::_create_module_table();
		// карта сайта
		self::_create_sitemap_table();
		// пользователи панели управления
		self::_create_manage_user_table();
		// сессии
		self::_create_session_table();
		// таблица изображений
		self::_create_image_table();
		// таблица файлов
		self::_create_file_table();
		// таблицы языковых переменных значений модулей
		self::_create_translate_table();
		// таблица для клиентских css для модулей
		self::_create_css_table();
		// таблица для клиентских js для модулей
		self::_create_js_table();

		// таблицы виджетов
		self::_create_widget_table();
		
	}
	
	public static function delete_sys_tables($tables){
		// не будем пока это зверство реализовывать!!!
		// echo Kohana::debug($tables); die;
		/*ALTER TABLE `ge2_db`.`gsys_manage_access` DROP FOREIGN KEY `FK_muser_access`
			, ENGINE = InnoDB;*/
	}
	
	
	/**
	 * Создает таблицу списка языков
	 * 
	 */
	private static function _create_lang_table(){
		
		$lang_table = GE::pref('sys', 'lang');

		if ( ! in_array($lang_table, self::$sys_tables)){
			$sql = 'CREATE TABLE ' . $lang_table . ' ('
	  				. '`id` INT (11) NOT NULL AUTO_INCREMENT,'
	  				. '`title` VARCHAR(50) NOT NULL,'
	  				. '`short` VARCHAR(10) NOT NULL,'
	  				. '`uri` VARCHAR(5) NOT NULL,'
	  				. '`date_format` VARCHAR(100) NOT NULL,'
	  				. 'PRIMARY KEY(`id`),'
	  				. 'UNIQUE `uniq`(`uri`)'
					. ') ENGINE=InnoDB '
	  				. self::_get_db_create_charset();
			DB::query(NULL, $sql)->execute();
			
			$lang = ORM::factory('lang');
			$default = GE::lang();
			$data = $lang->validate_create($default);
			if ($data->check()){
				$lang->values($data);
				$lang->save();
			}
		}
		
	}

	
	/**
	 * Создает таблицу списка модулей
	 * 
	 */
	private static function _create_module_table(){
		$module_table = GE::pref('sys', 'module');
		$module_table_translate = $module_table.'_translate';
		if ( ! in_array($module_table, self::$sys_tables)){
			$sql = 'CREATE TABLE ' . $module_table . ' ('
	  				. '`id` INT (11) NOT NULL AUTO_INCREMENT,'
	  				. '`parent_id` INT (11) NOT NULL,'
	  				. '`name` VARCHAR(30) NOT NULL,'
	  				. '`type` VARCHAR(30) NOT NULL,'
	  				. '`caption_field` VARCHAR(30) NOT NULL,'
	  				. '`controller` VARCHAR(30) NOT NULL,'
	  				. '`outorder` VARCHAR(255) NOT NULL DEFAULT "outorder:ASC",'
	  				. '`model` LONGTEXT NOT NULL,'
	  				. '`maxnum` INT (11) NOT NULL,'
	  				. '`sitemap_show` INT(1) DEFAULT 1 NOT NULL,'
	  				. '`seo_changefreq` ENUM( "always", "hourly", "daily", "weekly", "monthly", "yearly", "never") DEFAULT "weekly" NOT NULL,'
	  				. '`seo_priority` ENUM( "0", "0.1", "0.2", "0.3", "0.4", "0.5", "0.6", "0.7", "0.8", "0.9", "1") DEFAULT "0.7" NOT NULL,'
	  				. '`lastmod` DATETIME NOT NULL,'
	  				. 'PRIMARY KEY(`id`),'
	  				. 'UNIQUE `uniq`(`name`)'
					. ') ENGINE=InnoDB '
	  				. self::_get_db_create_charset();
			DB::query(NULL, $sql)->execute();
		}
		
		if ( ! in_array($module_table_translate, self::$sys_tables)){
			$sql = 'CREATE TABLE ' . $module_table_translate . ' ('
	  				. '`id` INT (11) NOT NULL AUTO_INCREMENT,'
					. '`module_id` INT (11) NOT NULL,'
	  				. '`lang_id` INT (11) NOT NULL,'
	  				. '`title` VARCHAR(255) NOT NULL,'
	  				. 'PRIMARY KEY(`id`),'
	  				. 'UNIQUE `uniq`(`module_id`, `lang_id`)'
	  				. ') ENGINE=InnoDB '
	  				. self::_get_db_create_charset();
			DB::query(NULL, $sql)->execute();
		}
		
		// связь с таблицей переводов
		if ( ! in_array($module_table_translate, self::$sys_tables) OR  ! in_array($module_table, self::$sys_tables)){
			$sql = 'ALTER TABLE ' . $module_table_translate . ' ADD CONSTRAINT `FK_modules` FOREIGN KEY `FK_modules` (`module_id`)'
				.' REFERENCES ' . $module_table . ' (`id`)'
				.' ON DELETE CASCADE'
	    		.' ON UPDATE RESTRICT'
				.', ENGINE = InnoDB ';
			DB::query(NULL, $sql)->execute();
		}
		
		// связь с языком таблицы перевода
		$lang_table = GE::pref('sys', 'lang');
		if ( ! in_array($module_table_translate, self::$sys_tables) OR  ! in_array($lang_table, self::$sys_tables)){
			$sql = 'ALTER TABLE ' . $module_table_translate . ' ADD CONSTRAINT `FK_modules_lang` FOREIGN KEY `FK_modules_lang` (`lang_id`)'
				.' REFERENCES ' . $lang_table . ' (`id`)'
				.' ON DELETE CASCADE'
	    		.' ON UPDATE RESTRICT'
				.', ENGINE = InnoDB ';
			DB::query(NULL, $sql)->execute();
		}
		
	}
	
	private static function _create_widget_table(){

		$widget_table = GE::pref('sys', 'widget');
		if ( ! in_array($widget_table, self::$sys_tables)){
			$sql = 'CREATE TABLE ' . $widget_table . ' ('
				. '`id` int NOT NULL auto_increment, '
				. '`name` varchar(30) NOT NULL, '
				. '`title` varchar(255) NOT NULL, '
	  			. '`settings` LONGTEXT NOT NULL,'
				. ' PRIMARY KEY  (`id`), '
				. ' UNIQUE KEY `uniq` (`name`) '
				. ') ENGINE = InnoDB '
				. self::_get_db_create_charset();
			DB::query(NULL, $sql)->execute();
		}
		
	}
	
	/**
	 * Создает таблицу карты сайта
	 * 
	 */
	private static function _create_sitemap_table(){
		$module_table = GE::pref('sys', 'module');
		$sitemap_table = GE::pref('sys', 'sitemap');
		$sitemap_table_translate = $sitemap_table.'_translate';
		
		if ( ! in_array($sitemap_table, self::$sys_tables)){
			$sql = 'CREATE TABLE ' . $sitemap_table . ' ('
	  				. '`id` INT (11) NOT NULL AUTO_INCREMENT,'
	  				. '`module_id` INT (11) NOT NULL,'
	  				. '`item_id` INT (11) NOT NULL,'
	  				. '`item_parent_id` INT NOT NULL,'
					. '`short_url` VARCHAR(50) NOT NULL,'
					. '`isactive` INT(1) NOT NULL,'
	  				. '`sitemap_show` INT(1) NOT NULL,'
					. '`seo_changefreq` ENUM( "always", "hourly", "daily", "weekly", "monthly", "yearly", "never") DEFAULT "weekly" NOT NULL,'
	  				. '`seo_priority` ENUM( "0", "0.1", "0.2", "0.3", "0.4", "0.5", "0.6", "0.7", "0.8", "0.9", "1") DEFAULT "0.7" NOT NULL,'
	  				. '`lastmod` DATETIME NOT NULL,'
					. 'PRIMARY KEY(`id`),'
					. 'UNIQUE (`module_id`, `item_id`)'
	  				. ') ENGINE=InnoDB '
	  				. self::_get_db_create_charset();
			DB::query(NULL, $sql)->execute();
			
			$sql = 'ALTER TABLE ' . $sitemap_table . ' ADD CONSTRAINT `FK_sitemap_modules` FOREIGN KEY `FK_sitemap_modules` (`module_id`)'
				.' REFERENCES ' . $module_table . ' (`id`)'
				.' ON DELETE CASCADE'
	    		.' ON UPDATE RESTRICT'
				.', ENGINE = InnoDB ';
			DB::query(NULL, $sql)->execute();
		}

		if ( ! in_array($sitemap_table_translate, self::$sys_tables)){
			$sql = 'CREATE TABLE ' . $sitemap_table_translate . ' ('
	  				. '`id` INT (11) NOT NULL AUTO_INCREMENT,'
					. '`sitemap_id` INT (11) NOT NULL,'
	  				. '`lang_id` INT (11) NOT NULL,'
	  				. '`title` VARCHAR(255) NOT NULL,'
					. '`keywords` VARCHAR(255) NOT NULL,'
					. '`description` VARCHAR(255) NOT NULL,'
	  				. 'PRIMARY KEY(`id`),'
					. 'UNIQUE (`sitemap_id`, `lang_id`)'
	  				. ') ENGINE=InnoDB '
	  				. self::_get_db_create_charset();
			DB::query(NULL, $sql)->execute();
		}
		

		if ( ! in_array($sitemap_table_translate, self::$sys_tables) OR  ! in_array($sitemap_table, self::$sys_tables)){
			$sql = 'ALTER TABLE ' . $sitemap_table_translate . ' ADD CONSTRAINT `FK_sitemap` FOREIGN KEY `FK_sitemap` (`sitemap_id`)'
				.' REFERENCES ' . $sitemap_table . ' (`id`)'
				.' ON DELETE CASCADE'
	    		.' ON UPDATE RESTRICT'
				.', ENGINE = InnoDB ';
			DB::query(NULL, $sql)->execute();
		}

		// связь с языком таблицы перевода
		$lang_table = GE::pref('sys', 'lang');
		if ( ! in_array($sitemap_table_translate, self::$sys_tables) OR  ! in_array($lang_table, self::$sys_tables)){
			$sql = 'ALTER TABLE ' . $sitemap_table_translate . ' ADD CONSTRAINT `FK_sitemap_lang` FOREIGN KEY `FK_sitemap_lang` (`lang_id`)'
				.' REFERENCES ' . $lang_table . ' (`id`)'
				.' ON DELETE CASCADE'
	    		.' ON UPDATE RESTRICT'
				.', ENGINE = InnoDB ';
			DB::query(NULL, $sql)->execute();
		}
		
	}

	/**
	 * Создает таблицы для пользователей панели управления
	 * 
	 */
	private static function _create_manage_user_table(){

		$user_table = GE::pref('sys', 'manage_user');
		$group_table = GE::pref('sys', 'manage_group');
		$access_table = GE::pref('sys', 'manage_access');
		
		if ( ! in_array($user_table, self::$sys_tables)){
			$sql = 'CREATE TABLE ' . $user_table . ' ('
				.'`id` INT (11) NOT NULL AUTO_INCREMENT,'
				.'`group_id` INT (11) NOT NULL,'
				.'`login` VARCHAR(30) NOT NULL,'
				.'`password` VARCHAR(50) NOT NULL,'
				.'`name` VARCHAR(50) NOT NULL,'
				.'`last_login` INTEGER NOT NULL,'
				.'`isactive` INT (11) NOT NULL,'
				.'PRIMARY KEY(`id`)'
				.') ENGINE = InnoDB '
				. self::_get_db_create_charset();		
			DB::query(NULL, $sql)->execute();
			
			$sql = 'INSERT INTO ' . $user_table . ' VALUES (1, 1, \'root\', \'ed01nc8d226eb2e5135e9b3addeea1c163d687eaf9\', \'Root\', \'0000-00-00 00:00:00\', 1)';
			DB::query(Database::INSERT, $sql)->execute();
		}

		if ( ! in_array($group_table, self::$sys_tables)){
			$sql = 'CREATE TABLE ' . $group_table . ' ('
	  			.'`id` INT (11) NOT NULL AUTO_INCREMENT,'
	  			.'`name` VARCHAR(50) NOT NULL,'
	  			.'`rang` INT (11) NOT NULL,'
	  			.'PRIMARY KEY(`id`)'
				.') ENGINE = InnoDB '
				. self::_get_db_create_charset();		
			DB::query(NULL, $sql)->execute();
			
			$sql = 'INSERT INTO ' . $group_table . ' VALUES (1, \'Администратор\', 0)';
			DB::query(Database::INSERT, $sql)->execute();
		}

		if ( ! in_array($access_table, self::$sys_tables)){
			$sql = 'CREATE TABLE ' . $access_table . ' ('
				.'`group_id` INT (11) NOT NULL AUTO_INCREMENT,'
				.'`name` VARCHAR(20) NOT NULL,'
				.'`type` VARCHAR(20) NOT NULL,'
				.'`action` INT (11) NOT NULL,'
				.'PRIMARY KEY(`group_id`, `name`, `type`)'
				.') ENGINE = InnoDB '
				. self::_get_db_create_charset();		
			DB::query(NULL, $sql)->execute();
		}
		
		if ( ! in_array($user_table, self::$sys_tables) OR ! in_array($group_table, self::$sys_tables)){
			$sql = 'ALTER TABLE ' . $user_table . ' ADD CONSTRAINT `FK_muser_users` FOREIGN KEY `FK_muser_users` (`group_id`)'
				.' REFERENCES ' . $group_table . ' (`id`)'
				.' ON DELETE CASCADE'
	    		.' ON UPDATE RESTRICT'
				.', ENGINE = InnoDB ';
			DB::query(NULL, $sql)->execute();
		}

		if ( ! in_array($access_table, self::$sys_tables) OR ! in_array($group_table, self::$sys_tables)){
			$sql = 'ALTER TABLE ' . $access_table . ' ADD CONSTRAINT `FK_muser_access` FOREIGN KEY `FK_muser_access` (`group_id`)'
				.' REFERENCES ' . $group_table . ' (`id`)'
				.' ON DELETE CASCADE'
				.' ON UPDATE RESTRICT'
				.', ENGINE = InnoDB ';
			DB::query(NULL, $sql)->execute();
		}
		
	}
	
	/**
	 * Создает таблицу для сессий
	 * 
	 */
	private static function _create_session_table(){
		
		$session_table = GE::pref('sys', 'session');
		
		if ( ! in_array($session_table, self::$sys_tables)){
			$sql = 'CREATE TABLE ' . $session_table . ' ('
				.'`session_id` VARCHAR(24) NOT NULL,'
				.'`last_active` INT(10) unsigned NOT NULL,'
				.'`contents` MEDIUMTEXT NOT NULL,'
				.'PRIMARY KEY  (`session_id`),'
				.'KEY `last_active` (`last_active`)'
				.') ENGINE=InnoDB '
				. self::_get_db_create_charset();
			DB::query(NULL, $sql)->execute();
		}
	}

	/**
	 * Создает таблицу для картинок модулей сайта
	 * 
	 */
	private static function _create_image_table(){
		$image_table = GE::pref('sys', 'image');
		if ( ! in_array($image_table, self::$sys_tables)){
			$sql = 'CREATE TABLE ' . $image_table . ' ('
				.'`id` INT (11) NOT NULL AUTO_INCREMENT,'
				.'`content` LONGBLOB NOT NULL,'
				.'`type` VARCHAR(20) NOT NULL,'
				.'PRIMARY KEY(`id`)'
				.') ENGINE = InnoDB '
				. self::_get_db_create_charset();
			DB::query(NULL, $sql)->execute();
		}
	}
	
	/**
	 * Создает таблицу для файлов модулей сайта
	 * 
	 */
	private static function _create_file_table(){
		$file_table = GE::pref('sys', 'file');
		if ( ! in_array($file_table, self::$sys_tables)){
			$sql = 'CREATE TABLE ' . $file_table . ' ('
				.'`id` INT (11) NOT NULL AUTO_INCREMENT,'
				.'`content` LONGBLOB NOT NULL,'
				.'`real_name` VARCHAR(255) NOT NULL,'
				.'`type` VARCHAR(20) NOT NULL,'
				.'PRIMARY KEY(`id`)'
				.') ENGINE = InnoDB '
				. self::_get_db_create_charset();
			DB::query(NULL, $sql)->execute();
		}
	}

	/**
	 * Создает таблицы языковых переменных значений модулей
	 * 
	 */
	private static function _create_translate_table(){
		$lang_table = GE::pref('sys', 'lang');
		
		$string_table = GE::pref('sys', 'string');
		if ( ! in_array($string_table, self::$sys_tables)){
			$sql = 'CREATE TABLE ' . $string_table . ' ('
				.'`id` INT (11) NOT NULL AUTO_INCREMENT,'
				.'`lang_id` INT (11) NOT NULL,'
				.'`value` VARCHAR(255) NOT NULL,'
				.'PRIMARY KEY(`id`, `lang_id`)'
				.') ENGINE = InnoDB '
				. self::_get_db_create_charset();
			DB::query(NULL, $sql)->execute();

			// связь с языком таблицы перевода
			$sql = 'ALTER TABLE ' . $string_table . ' ADD CONSTRAINT `FK_string_lang` FOREIGN KEY `FK_string_lang` (`lang_id`)'
				.' REFERENCES ' . $lang_table . ' (`id`)'
				.' ON DELETE CASCADE'
	    		.' ON UPDATE RESTRICT'
				.', ENGINE = InnoDB ';
			DB::query(NULL, $sql)->execute();
		}
		
		$text_table = GE::pref('sys', 'text');
		if ( ! in_array($text_table, self::$sys_tables)){
			$sql = 'CREATE TABLE ' . $text_table . ' ('
				.'`id` INT (11) NOT NULL AUTO_INCREMENT,'
				.'`lang_id` INT (11) NOT NULL,'
				.'`value` TEXT NOT NULL,'
				.'PRIMARY KEY(`id`, `lang_id`)'
				.') ENGINE = InnoDB '
				. self::_get_db_create_charset();
			DB::query(NULL, $sql)->execute();

			// связь с языком таблицы перевода
			$sql = 'ALTER TABLE ' . $text_table . ' ADD CONSTRAINT `FK_text_lang` FOREIGN KEY `FK_text_lang` (`lang_id`)'
				.' REFERENCES ' . $lang_table . ' (`id`)'
				.' ON DELETE CASCADE'
	    		.' ON UPDATE RESTRICT'
				.', ENGINE = InnoDB ';
			DB::query(NULL, $sql)->execute();
		}
		
		$editor_table = GE::pref('sys', 'editor');
		if ( ! in_array($editor_table, self::$sys_tables)){
			$sql = 'CREATE TABLE ' . $editor_table . ' ('
				.'`id` INT (11) NOT NULL AUTO_INCREMENT,'
				.'`lang_id` INT (11) NOT NULL,'
				.'`value` MEDIUMTEXT NOT NULL,'
				.'PRIMARY KEY(`id`, `lang_id`)'
				.') ENGINE = InnoDB '
				. self::_get_db_create_charset();
			DB::query(NULL, $sql)->execute();

			// связь с языком таблицы перевода
			$sql = 'ALTER TABLE ' . $editor_table . ' ADD CONSTRAINT `FK_editor_lang` FOREIGN KEY `FK_editor_lang` (`lang_id`)'
				.' REFERENCES ' . $lang_table . ' (`id`)'
				.' ON DELETE CASCADE'
	    		.' ON UPDATE RESTRICT'
				.', ENGINE = InnoDB ';
			DB::query(NULL, $sql)->execute();
		}
		
	}

	/**
	 * Создает таблицу для клиентских css для модулей
	 * 
	 */
	private static function _create_css_table(){
		
		$css_table = GE::pref('sys', 'css');

		if ( ! in_array($css_table, self::$sys_tables)){
			$sql = 'CREATE TABLE ' . $css_table . ' ('
	  				. '`id` INT (11) NOT NULL AUTO_INCREMENT,'
					. '`module_id` INT (11) NOT NULL,'
	  				. '`item_id` INT (11) NOT NULL,'
	  				. '`value` TEXT NOT NULL,'
	  				. 'PRIMARY KEY(`id`),'
					. 'UNIQUE (`module_id`, `item_id`)'
	  				. ') ENGINE=InnoDB '
	  				. self::_get_db_create_charset();
			DB::query(NULL, $sql)->execute();
			

			$module_table = GE::pref('sys', 'module');
			// связь 
			$sql = 'ALTER TABLE ' . $css_table . ' ADD CONSTRAINT `FK_module_css` FOREIGN KEY `FK_module_css` (`module_id`)'
				.' REFERENCES ' . $module_table . ' (`id`)'
				.' ON DELETE CASCADE'
	    		.' ON UPDATE RESTRICT'
				.', ENGINE = InnoDB ';
			DB::query(NULL, $sql)->execute();
		}
	}
	
	/**
	 * Создает таблицу для клиентских js для модулей
	 * 
	 */
	private static function _create_js_table(){
		
		$js_table = GE::pref('sys', 'js');

		if ( ! in_array($js_table, self::$sys_tables)){
			$sql = 'CREATE TABLE ' . $js_table . ' ('
	  				. '`id` INT (11) NOT NULL AUTO_INCREMENT,'
					. '`module_id` INT (11) NOT NULL,'
	  				. '`item_id` INT (11) NOT NULL,'
	  				. '`value` TEXT NOT NULL,'
	  				. 'PRIMARY KEY(`id`),'
					. 'UNIQUE (`module_id`, `item_id`)'
	  				. ') ENGINE=InnoDB '
	  				. self::_get_db_create_charset();
			DB::query(NULL, $sql)->execute();
			
			$module_table = GE::pref('sys', 'module');
			// связь 
			$sql = 'ALTER TABLE ' . $js_table . ' ADD CONSTRAINT `FK_module_js` FOREIGN KEY `FK_module_js` (`module_id`)'
				.' REFERENCES ' . $module_table . ' (`id`)'
				.' ON DELETE CASCADE'
	    		.' ON UPDATE RESTRICT'
				.', ENGINE = InnoDB ';
			DB::query(NULL, $sql)->execute();
		}
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
	
	/**
	 * 
	 * Возвращает массив таблиц сайта, 
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